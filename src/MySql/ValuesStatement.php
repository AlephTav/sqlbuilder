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

    /**
     * @return static
     */
    public function copy()
    {
        $copy = new static($this->db);
        $copy->values = $this->values ? clone $this->values : null;
        $copy->order = $this->order ? clone $this->order : null;
        $copy->limit = $this->limit;
        return $copy;
    }

    public function clean(): void
    {
        $this->values = null;
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
