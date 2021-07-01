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

    /**
     * @return static
     */
    public function copy()
    {
        $copy = new static();
        $copy->with = $this->with ? clone $this->with : null;
        $copy->modifiers = $this->modifiers;
        $copy->table = $this->table ? clone $this->table : null;
        $copy->assignment = $this->assignment ? clone $this->assignment : null;
        $copy->where = $this->where ? clone $this->where : null;
        $copy->order = $this->order ? clone $this->order : null;
        $copy->limit = $this->limit;
        return $copy;
    }

    public function clean(): void
    {
        $this->with = null;
        $this->modifiers = '';
        $this->table = null;
        $this->assignment = null;
        $this->where = null;
        $this->order = null;
        $this->limit = null;
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
