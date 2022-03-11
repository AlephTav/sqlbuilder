<?php

declare(strict_types=1);

namespace AlephTools\SqlBuilder\MySql;

use AlephTools\SqlBuilder\MySql\Clause\LimitByClause;
use AlephTools\SqlBuilder\MySql\Clause\RowListClause;
use AlephTools\SqlBuilder\Sql\AbstractValuesStatement;

class ValuesStatement extends AbstractValuesStatement
{
    use RowListClause;
    use LimitByClause;

    public function copy(): static
    {
        $copy = new static($this->db);
        $this->cloneValues($copy);
        $this->cloneOrderBy($copy);
        $this->cloneLimit($copy);
        return $copy;
    }

    public function clean(): static
    {
        $this->cleanValues();
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
        if ($this->union) {
            $this->buildUnion();
        } else {
            $this->buildValues();
        }
        $this->buildOrderBy();
        $this->buildLimit();
        $this->built = true;
        return $this;
    }
}
