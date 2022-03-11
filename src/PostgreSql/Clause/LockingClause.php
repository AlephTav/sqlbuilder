<?php

namespace AlephTools\SqlBuilder\PostgreSql\Clause;

use AlephTools\SqlBuilder\Sql\Clause\LockingClause as BaseLockingClause;

trait LockingClause
{
    use BaseLockingClause;

    public function forNoKeyUpdate(mixed $table = null, string $option = ''): static
    {
        return $this->forLock('NO KEY UPDATE', $table, $option);
    }

    public function forKeyShare(mixed $table = null, string $option = ''): static
    {
        return $this->forLock('KEY SHARE', $table, $option);
    }
}