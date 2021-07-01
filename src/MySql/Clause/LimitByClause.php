<?php

declare(strict_types=1);

namespace AlephTools\SqlBuilder\MySql\Clause;

use AlephTools\SqlBuilder\Sql\Clause\LimitClause;

trait LimitByClause
{
    use LimitClause;

    protected function buildLimit(): void
    {
        if ($this->limit !== null) {
            $this->sql .= " LIMIT BY $this->limit";
        }
    }
}
