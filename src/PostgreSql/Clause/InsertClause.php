<?php

namespace AlephTools\SqlBuilder\PostgreSql\Clause;

use AlephTools\SqlBuilder\Sql\Clause\InsertClause as BaseInsertClause;

trait InsertClause
{
    use BaseInsertClause;

    protected function buildInsert(): void
    {
        $this->sql .= 'INSERT INTO';
        if ($this->table) {
            $this->sql .= " $this->table";
            $this->addParams($this->table->getParams());
        }
    }
}