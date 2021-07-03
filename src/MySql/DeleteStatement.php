<?php

declare(strict_types=1);

namespace AlephTools\SqlBuilder\MySql;

use AlephTools\SqlBuilder\MySql\Clause\DeleteClause;
use AlephTools\SqlBuilder\MySql\Clause\PartitionClause;
use AlephTools\SqlBuilder\Sql\AbstractDeleteStatement;
use AlephTools\SqlBuilder\Sql\Clause\LimitClause;
use AlephTools\SqlBuilder\Sql\Clause\OrderClause;

class DeleteStatement extends AbstractDeleteStatement
{
    use DeleteClause;
    use PartitionClause;
    use OrderClause;
    use LimitClause;

    /**
     * @return static
     */
    public function copy()
    {
        $copy = new static($this->db);
        $copy->with = $this->with ? clone $this->with : null;
        $copy->modifiers = $this->modifiers;
        $copy->from = $this->from ? clone $this->from : null;
        $copy->partition = $this->partition ? clone $this->partition : null;
        $copy->using = $this->using ? clone $this->using : null;
        $copy->where = $this->where ? clone $this->where : null;
        $copy->order = $this->order ? clone $this->order : null;
        $copy->limit = $this->limit;
        return $copy;
    }

    public function clean(): void
    {
        $this->with = null;
        $this->modifiers = '';
        $this->from = null;
        $this->partition = null;
        $this->using = null;
        $this->where = null;
        $this->order = null;
        $this->limit = null;
    }

    public function build()
    {
        if ($this->built) {
            return $this;
        }
        $this->sql = '';
        $this->params = [];
        $this->buildWith();
        $this->buildDelete();
        $this->buildPartition();
        $this->buildUsing();
        $this->buildWhere();
        $this->buildOrderBy();
        $this->buildLimit();
        $this->built = true;
        return $this;
    }
}
