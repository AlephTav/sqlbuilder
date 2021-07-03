<?php

declare(strict_types=1);

namespace AlephTools\SqlBuilder\PostgreSql;

use AlephTools\SqlBuilder\PostgreSql\Clause\DeleteClause;
use AlephTools\SqlBuilder\Sql\AbstractDeleteStatement;
use AlephTools\SqlBuilder\Sql\Clause\ReturningClause;
use AlephTools\SqlBuilder\Sql\Execution\DataFetching;

class DeleteStatement extends AbstractDeleteStatement
{
    use DeleteClause;
    use ReturningClause;
    use DataFetching;

    /**
     * @return static
     */
    public function copy()
    {
        $copy = new static($this->db);
        $copy->with = $this->with ? clone $this->with : null;
        $copy->from = $this->from ? clone $this->from : null;
        $copy->only = $this->only;
        $copy->using = $this->using ? clone $this->using : null;
        $copy->where = $this->where ? clone $this->where : null;
        $copy->returning = $this->returning ? clone $this->returning : null;
        return $copy;
    }

    public function clean(): void
    {
        $this->with = null;
        $this->from = null;
        $this->only = false;
        $this->using = null;
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
        $this->buildDelete();
        $this->buildUsing();
        $this->buildWhere();
        $this->buildReturning();
        $this->built = true;
        return $this;
    }
}
