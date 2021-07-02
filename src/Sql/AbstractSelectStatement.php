<?php

declare(strict_types=1);

namespace AlephTools\SqlBuilder\Sql;

use AlephTools\SqlBuilder\Query;
use AlephTools\SqlBuilder\Sql\Clause\FromClause;
use AlephTools\SqlBuilder\Sql\Clause\GroupClause;
use AlephTools\SqlBuilder\Sql\Clause\HavingClause;
use AlephTools\SqlBuilder\Sql\Clause\JoinClause;
use AlephTools\SqlBuilder\Sql\Clause\LimitClause;
use AlephTools\SqlBuilder\Sql\Clause\OffsetClause;
use AlephTools\SqlBuilder\Sql\Clause\OrderClause;
use AlephTools\SqlBuilder\Sql\Clause\SelectClause;
use AlephTools\SqlBuilder\Sql\Clause\UnionClause;
use AlephTools\SqlBuilder\Sql\Clause\WhereClause;
use AlephTools\SqlBuilder\Sql\Clause\WithClause;
use AlephTools\SqlBuilder\Sql\Execution\DataFetching;
use AlephTools\SqlBuilder\Sql\Expression\FromExpression;
use AlephTools\SqlBuilder\Sql\Expression\GroupExpression;
use AlephTools\SqlBuilder\Sql\Expression\HavingExpression;
use AlephTools\SqlBuilder\Sql\Expression\JoinExpression;
use AlephTools\SqlBuilder\Sql\Expression\OrderExpression;
use AlephTools\SqlBuilder\Sql\Expression\SelectExpression;
use AlephTools\SqlBuilder\Sql\Expression\WhereExpression;
use AlephTools\SqlBuilder\Sql\Expression\WithExpression;
use AlephTools\SqlBuilder\StatementExecutor;
use Generator;

abstract class AbstractSelectStatement extends AbstractStatement implements Query
{
    use UnionClause,
        WithClause,
        FromClause,
        SelectClause,
        JoinClause,
        WhereClause,
        GroupClause,
        HavingClause,
        OrderClause,
        LimitClause,
        OffsetClause,
        DataFetching {
            DataFetching::column as parentColumn;
            DataFetching::scalar as parentScalar;
        }

    public function __construct(
        StatementExecutor $db = null,
        WithExpression $with = null,
        FromExpression $from = null,
        SelectExpression $select = null,
        JoinExpression $join = null,
        WhereExpression $where = null,
        GroupExpression $group = null,
        HavingExpression $having = null,
        OrderExpression $order = null,
        int $limit = null,
        int $offset = null
    ) {
        parent::__construct($db);
        $this->with = $with;
        $this->from = $from;
        $this->select = $select;
        $this->join = $join;
        $this->where = $where;
        $this->group = $group;
        $this->having = $having;
        $this->order = $order;
        $this->limit = $limit;
        $this->offset = $offset;
    }

    /**
     * @return static
     */
    public function copy()
    {
        return new static(
            $this->db,
            $this->with ? clone $this->with : null,
            $this->from ? clone $this->from : null,
            $this->select ? clone $this->select : null,
            $this->join ? clone $this->join : null,
            $this->where ? clone $this->where : null,
            $this->group ? clone $this->group : null,
            $this->having ? clone $this->having : null,
            $this->order ? clone $this->order : null,
            $this->limit,
            $this->offset
        );
    }

    public function clean(): void
    {
        $this->with = null;
        $this->from = null;
        $this->select = null;
        $this->join = null;
        $this->where = null;
        $this->group = null;
        $this->having = null;
        $this->order = null;
        $this->limit = null;
        $this->offset = null;
    }

    /**
     * @return static
     */
    public function paginate(int $page, int $size)
    {
        $this->offset = $size * $page;
        $this->limit = $size;
        $this->built = false;
        return $this;
    }

    /**
     * @return static
     */
    public function build()
    {
        if ($this->built) {
            return $this;
        }
        $this->sql = '';
        $this->params = [];
        if ($this->union) {
            $this->buildUnion();
        } else {
            $this->buildWith();
            $this->buildSelect();
            $this->buildFrom();
            $this->buildJoin();
            $this->buildWhere();
            $this->buildGroupBy();
            $this->buildHaving();
        }
        $this->buildOrderBy();
        $this->buildLimit();
        $this->buildOffset();
        $this->built = true;
        return $this;
    }

    //region Data Fetching

    public function column($column = null): array
    {
        if ($column !== null && $column !== '') {
            $prevSelect = $this->select;
            $this->select = $this->createSelectExpression();
            $this->select->append($column);
            $this->built = false;
            $result = $this->parentColumn();
            $this->select = $prevSelect;
            $this->built = false;
            return $result;
        }
        return $this->parentColumn();
    }

    public function scalar($column = null)
    {
        if ($column !== null && $column !== '') {
            $prevSelect = $this->select;
            $this->select = $this->createSelectExpression();
            $this->select->append($column);
            $this->built = false;
            $result = $this->parentScalar();
            $this->select = $prevSelect;
            $this->built = false;
            return $result;
        }
        return $this->parentScalar();
    }

    public function countWithNonConditionalClauses(string $column = '*'): int
    {
        return $this->count($column, false);
    }

    public function count(string $column = '*', bool $clearNonConditionalClauses = true): int
    {
        $this->built = false;
        if ($clearNonConditionalClauses) {
            $prevLimit = $this->limit;
            $prevOffset = $this->offset;
            $prevOrder = $this->order;
            $prevGroup = $this->group;
            $this->limit = $this->offset = $this->order = $this->group = null;
            $total = $this->scalar("COUNT($column)");
            $this->order = $prevOrder;
            $this->limit = $prevLimit;
            $this->offset = $prevOffset;
            $this->group = $prevGroup;
        } else {
            $total = $this->scalar("COUNT($column)");
        }
        $this->built = false;
        return (int)$total;
    }

    /**
     */
    public function pages(int $size = 1000, int $page = 0): Generator
    {
        if ($size <= 0) {
            return;
        }
        while (true) {
            $rows = $this
                ->paginate($page, $size)
                ->rows();

            $count = count($rows);
            if ($count > 0) {
                yield from $rows;
            }
            if ($count < $size) {
                break;
            }

            ++$page;
        }
    }

    /**
     */
    public function batches(int $size = 1000, int $page = 0): Generator
    {
        if ($size <= 0) {
            return;
        }
        while (true) {
            $rows = $this
                ->paginate($page, $size)
                ->rows();

            $count = count($rows);
            if ($count > 0) {
                yield $rows;
            }
            if ($count < $size) {
                break;
            }

            ++$page;
        }
    }

    //endregion
}
