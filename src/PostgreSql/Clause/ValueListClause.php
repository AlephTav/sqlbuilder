<?php

declare(strict_types=1);

namespace AlephTools\SqlBuilder\PostgreSql\Clause;

use AlephTools\SqlBuilder\Sql\Clause\ValueListClause as BaseValueListClause;

trait ValueListClause
{
    use BaseValueListClause;

    protected function buildValues(): void
    {
        if (isset($this->query)) {
            return;
        }
        if ($this->values) {
            $this->sql .= " VALUES $this->values";
            $this->addParams($this->values->getParams());
        } else {
            $this->sql .= ' DEFAULT VALUES';
        }
    }
}
