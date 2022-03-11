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

    public function copy(): static
    {
        $copy = new static($this->db);
        $this->cloneWith($copy);
        $this->cloneDelete($copy);
        $this->cloneUsing($copy);
        $this->cloneWhere($copy);
        $this->cloneReturning($copy);
        return $copy;
    }

    public function clean(): static
    {
        $this->cleanWith();
        $this->cleanDelete();
        $this->cleanUsing();
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
        $this->buildDelete();
        $this->buildUsing();
        $this->buildWhere();
        $this->buildReturning();
        $this->built = true;
        return $this;
    }
}
