<?php

declare(strict_types=1);

namespace AlephTools\SqlBuilder\Sql\Execution;

use AlephTools\SqlBuilder\StatementExecutor;

trait StatementExecution
{
    /**
     * Executes this command.
     */
    public function exec(StatementExecutor $db): int
    {
        return $db->execute($this->toSql(), $this->getParams());
    }
}
