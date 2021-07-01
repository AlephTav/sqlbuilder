<?php

declare(strict_types=1);

namespace AlephTools\SqlBuilder\Sql\Execution;

use RuntimeException;

trait StatementExecution
{
    /**
     * Executes this delete statement.
     *
     */
    public function exec(): int
    {
        if ($this->db === null) {
            throw new RuntimeException('The statement executor must not be null.');
        }
        return $this->db->execute($this->toSql(), $this->getParams());
    }
}
