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

    public function copy(): static
    {
        $copy = new static($this->db);
        $this->cloneWith($copy);
        $this->cloneUpdate($copy);
        $this->cloneAssignment($copy);
        $this->cloneFrom($copy);
        $this->cloneWhere($copy);
        $this->cloneReturning($copy);
        return $copy;
    }

    public function clean(): static
    {
        $this->cleanWith();
        $this->cleanUpdate();
        $this->cleanAssignment();
        $this->cleanFrom();
        $this->cleanWhere();
        $this->cleanReturning();
        return $this;
    }

    public function build(): static
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
