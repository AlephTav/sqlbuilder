<?php

declare(strict_types=1);

namespace AlephTools\SqlBuilder\MySql;

use AlephTools\SqlBuilder\MySql\Clause\UpdateClause;
use AlephTools\SqlBuilder\Sql\AbstractUpdateStatement;
use AlephTools\SqlBuilder\Sql\Clause\LimitClause;
use AlephTools\SqlBuilder\Sql\Clause\OrderClause;

class UpdateStatement extends AbstractUpdateStatement
{
    use UpdateClause;
    use OrderClause;
    use LimitClause;

    public function copy(): static
    {
        $copy = new static($this->db);
        $this->cloneWith($copy);
        $this->cloneUpdate($copy);
        $this->cloneAssignment($copy);
        $this->cloneWhere($copy);
        $this->cloneOrderBy($copy);
        $this->cloneLimit($copy);
        return $copy;
    }

    public function clean(): static
    {
        $this->cleanWith();
        $this->cleanUpdate();
        $this->cleanAssignment();
        $this->cleanWhere();
        $this->cleanOrderBy();
        $this->cleanLimit();
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
        $this->buildWith();
        $this->buildUpdate();
        $this->buildAssignment();
        $this->buildWhere();
        $this->buildOrderBy();
        $this->buildLimit();
        $this->built = true;
        return $this;
    }
}
