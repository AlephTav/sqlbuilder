<?php

declare(strict_types=1);

namespace AlephTools\SqlBuilder\Sql\Execution;

trait StatementExecution
{
    /**
     * Executes this command.
     */
    public function exec(): int
    {
        return $this->db()->execute($this->toSql(), $this->getParams());
    }
}
