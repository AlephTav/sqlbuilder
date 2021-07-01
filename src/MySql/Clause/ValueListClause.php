<?php

declare(strict_types=1);

namespace AlephTools\SqlBuilder\MySql\Clause;

use AlephTools\SqlBuilder\Sql\Clause\ValueListClause as BaseValueListClause;

trait ValueListClause
{
    use BaseValueListClause;

    protected function buildValues(): void
    {
        if ($this->values) {
            $this->sql .= " VALUES $this->values";
            $this->addParams($this->values->getParams());
        }
    }
}
