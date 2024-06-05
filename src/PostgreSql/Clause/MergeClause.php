<?php

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
        $this->sql .= 'MERGE INTO';
        if ($this->table) {
            $this->sql .= " $this->table";
            $this->addParams($this->table->getParams());
        }
    }
}