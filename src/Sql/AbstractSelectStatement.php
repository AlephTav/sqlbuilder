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

    /**
     * @return static
     */
    public function copy()
    {
        $copy = new static($this->db);
        $copy->with = $this->with ? clone $this->with : null;
        $copy->from = $this->from ? clone $this->from : null;
        $copy->select = $this->select ? clone $this->select : null;
        $copy->join = $this->join ? clone $this->join : null;
        $copy->where = $this->where ? clone $this->where : null;
        $copy->group = $this->group ? clone $this->group : null;
        $copy->having = $this->having ? clone $this->having : null;
        $copy->order = $this->order ? clone $this->order : null;
        $copy->limit = $this->limit;
        $copy->offset = $this->offset;
        return $copy;
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

    /**
     * @param mixed $column
     */
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

    /**
     * @param mixed $column
     * @return mixed
     */
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
            $total = (int)$this->scalar("COUNT($column)");
            $this->order = $prevOrder;
            $this->limit = $prevLimit;
            $this->offset = $prevOffset;
            $this->group = $prevGroup;
        } else {
            $total = (int)$this->scalar("COUNT($column)");
        }
        $this->built = false;
        return $total;
    }

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
