<?php

declare(strict_types=1);

namespace AlephTools\SqlBuilder\PostgreSql;

use AlephTools\SqlBuilder\PostgreSql\Clause\UpdateClause;
use AlephTools\SqlBuilder\Sql\AbstractUpdateStatement;
use AlephTools\SqlBuilder\Sql\Clause\FromClause;
use AlephTools\SqlBuilder\Sql\Clause\ReturningClause;
use AlephTools\SqlBuilder\Sql\Execution\DataFetching;

class UpdateStatement extends AbstractUpdateStatement
{
    use UpdateClause;
    use FromClause;
    use ReturningClause;
    use DataFetching;

    /**
     * @return static
     */
    public function copy()
    {
        $copy = new static($this->db);
        $copy->with = $this->with ? clone $this->with : null;
        $copy->table = $this->table ? clone $this->table : null;
        $copy->only = $this->only;
        $copy->assignment = $this->assignment ? clone $this->assignment : null;
        $copy->from = $this->from ? clone $this->from : null;
        $copy->where = $this->where ? clone $this->where : null;
        $copy->returning = $this->returning ? clone $this->returning : null;
        return $copy;
    }

    public function clean(): void
    {
        $this->with = null;
        $this->table = null;
        $this->only = false;
        $this->assignment = null;
        $this->from = null;
        $this->where = null;
        $this->returning = null;
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
        $this->buildWith();
        $this->buildUpdate();
        $this->buildAssignment();
        $this->buildFrom();
        $this->buildWhere();
        $this->buildReturning();
        $this->built = true;
        return $this;
    }
}
