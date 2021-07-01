<?php

declare(strict_types=1);

namespace AlephTools\SqlBuilder\Sql\Execution;

trait StatementExecution
{
    /**
     * Executes this delete statement.
     *
     */
    public function exec(): int
    {
        $this->validateAndBuild();
        return $this->db->execute($this->toSql(), $this->getParams());
    }
}
