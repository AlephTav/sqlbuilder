<?php

namespace AlephTools\SqlBuilder\MySql\Clause;

use AlephTools\SqlBuilder\Sql\Clause\LockingClause as BaseLockingClause;

trait LockingClause
{
    use BaseLockingClause {
        BaseLockingClause::buildLock as parentBuildLock;
        BaseLockingClause::forLock as parentForLock;
        BaseLockingClause::cloneLock as parentCloneLock;
        BaseLockingClause::cleanLock as parentCleanLock;
    }

    protected bool $lockInShareMode = false;

    protected function forLock(string $lockStrength, mixed $table, string $option): static
    {
        $this->lockInShareMode = false;
        return $this->parentForLock($lockStrength, $table, $option);
    }

    public function lockInShareMode(): static
    {
        $this->lockInShareMode = true;
        $this->built = false;
        return $this;
    }

    protected function cloneLock(mixed $copy): void
    {
        $this->parentCloneLock($copy);
        $copy->lockInShareMode = $this->lockInShareMode;
    }

    public function cleanLock(): void
    {
        $this->parentCleanLock();
        $this->lockInShareMode = false;
    }

    protected function buildLock(): void
    {
        if ($this->lockInShareMode) {
            $this->sql .= ' LOCK IN SHARE MODE';
        } else {
            $this->parentBuildLock();
        }
    }
}