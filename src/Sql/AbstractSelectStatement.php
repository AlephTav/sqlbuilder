<?php

declare(strict_types=1);

namespace AlephTools\SqlBuilder\Sql;

use AlephTools\SqlBuilder\Query;
use AlephTools\SqlBuilder\Sql\Clause\FromClause;
use AlephTools\SqlBuilder\Sql\Clause\GroupClause;
use AlephTools\SqlBuilder\Sql\Clause\HavingClause;
use AlephTools\SqlBuilder\Sql\Clause\JoinClause;
use AlephTools\SqlBuilder\Sql\Clause\LimitClause;
use AlephTools\SqlBuilder\Sql\Clause\LockingClause;
use AlephTools\SqlBuilder\Sql\Clause\OffsetClause;
use AlephTools\SqlBuilder\Sql\Clause\OrderClause;
use AlephTools\SqlBuilder\Sql\Clause\SelectClause;
use AlephTools\SqlBuilder\Sql\Clause\UnionClause;
use AlephTools\SqlBuilder\Sql\Clause\WhereClause;
use AlephTools\SqlBuilder\Sql\Clause\WithClause;
use AlephTools\SqlBuilder\Sql\Execution\DataFetching;
use Generator;
use function count;

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
        LockingClause,
        DataFetching {
            DataFetching::column as parentColumn;
            DataFetching::scalar as parentScalar;
        }

    public function copy(): static
    {
        $copy = new static($this->db);
        $this->cloneWith($copy);
        $this->cloneFrom($copy);
        $this->cloneSelect($copy);
        $this->cloneJoin($copy);
        $this->cloneWhere($copy);
        $this->cloneGroupBy($copy);
        $this->cloneHaving($copy);
        $this->cloneOrderBy($copy);
        $this->cloneLimit($copy);
        $this->cloneOffset($copy);
        $this->cloneLock($copy);
        return $copy;
    }

    public function clean(): static
    {
        $this->cleanWith();
        $this->cleanFrom();
        $this->cleanSelect();
        $this->cleanJoin();
        $this->cleanWhere();
        $this->cleanGroupBy();
        $this->cleanHaving();
        $this->cleanOrderBy();
        $this->cleanLimit();
        $this->cleanOffset();
        $this->cleanLock();
        return $this;
    }

    public function paginate(int $page, int $size): static
    {
        $this->offset = $size * $page;
        $this->limit = $size;
        $this->built = false;
        return $this;
    }

    public function build(): static
    {
        if ($this->built) {
            return $this;
        }
        $this->sql = '';
        $this->params = [];
        if ($this->union) {
            $this->buildUnion();
            $this->buildOrderBy();
            $this->buildLimit();
            $this->buildOffset();
        } else {
            $this->buildWith();
            $this->buildSelect();
            $this->buildFrom();
            $this->buildJoin();
            $this->buildWhere();
            $this->buildGroupBy();
            $this->buildHaving();
            $this->buildOrderBy();
            $this->buildLimit();
            $this->buildOffset();
            $this->buildLock();
        }
        $this->built = true;
        return $this;
    }

    //region Data Fetching

    public function column(mixed $column = null): array
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

    public function scalar(mixed $column = null): mixed
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
