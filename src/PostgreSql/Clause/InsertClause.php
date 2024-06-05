<?php

declare(strict_types=1);

namespace AlephTools\SqlBuilder\PostgreSql\Clause;

use AlephTools\SqlBuilder\Sql\Clause\InsertClause as BaseInsertClause;

trait InsertClause
{
    use BaseInsertClause;

    protected function buildInsert(): void
    {
        $this->sql .= 'INSERT';
        if ($this->table) {
            $this->sql .= " INTO $this->table";
            $this->addParams($this->table->getParams());
        }
    }
}
