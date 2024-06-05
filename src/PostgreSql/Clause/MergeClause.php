<?php

declare(strict_types=1);

namespace AlephTools\SqlBuilder\PostgreSql\Clause;

trait MergeClause
{
    use InsertClause {
        InsertClause::buildInsert as buildMerge;
        InsertClause::cloneInsert as cloneMerge;
        InsertClause::cleanInsert as cleanMerge;
    }

    protected function buildMerge(): void
    {
        $this->sql .= 'MERGE';
        if ($this->table) {
            $this->sql .= " INTO $this->table";
            $this->addParams($this->table->getParams());
        }
    }
}