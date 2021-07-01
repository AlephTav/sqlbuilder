<?php

namespace AlephTools\SqlBuilder\Sql\Execution;

trait StatementExecution
{
    /**
     * Executes this delete statement.
     *
     * @return int
     */
    public function exec(): int
    {
        $this->validateAndBuild();
        return $this->db->execute($this->toSql(), $this->getParams());
    }
}