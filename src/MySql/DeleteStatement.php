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

    public function copy(): static
    {
        $copy = new static($this->db);
        $this->cloneWith($copy);
        $this->cloneDelete($copy);
        $this->clonePartition($copy);
        $this->cloneUsing($copy);
        $this->cloneWhere($copy);
        $this->cloneOrderBy($copy);
        $this->cloneLimit($copy);
        return $copy;
    }

    public function clean(): static
    {
        $this->cleanWith();
        $this->cleanDelete();
        $this->cleanPartition();
        $this->cleanUsing();
        $this->cleanWhere();
        $this->cleanOrderBy();
        $this->cleanLimit();
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
        $this->buildPartition();
        $this->buildUsing();
        $this->buildWhere();
        $this->buildOrderBy();
        $this->buildLimit();
        $this->built = true;
        return $this;
    }
}
