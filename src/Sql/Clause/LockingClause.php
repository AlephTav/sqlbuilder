<?php

namespace AlephTools\SqlBuilder\Sql\Clause;

use AlephTools\SqlBuilder\Sql\Expression\FromExpression;

trait LockingClause
{
    protected string $lockStrength = '';
    protected string $lockOption = '';
    protected ?FromExpression $lockOf = null;

    /**
     * @return static
     */
    public function forUpdate(mixed $table = null, string $option = '')
    {
        return $this->forLock('UPDATE', $table, $option);
    }

    /**
     * @return static
     */
    public function forShare(mixed $table = null, string $option = '')
    {
        return $this->forLock('SHARE', $table, $option);
    }

    /**
     * @return static
     */
    protected function forLock(string $lockStrength, mixed $table, string $option)
    {
        if ($this->lockStrength !== $lockStrength) {
            $this->lockOf = null;
        }
        $this->lockStrength = $lockStrength;
        $this->lockOption = $option;
        if ($table !== null) {
            $this->lockOf = $this->lockOf ?? $this->createOfExpression();
            $this->lockOf->append($table);
        }
        $this->built = false;
        return $this;
    }

    protected function createOfExpression(): FromExpression
    {
        return new FromExpression();
    }

    protected function cloneLock(mixed $copy): void
    {
        $copy->lockStrength = $this->lockStrength;
        $copy->lockOption = $this->lockOption;
        $copy->lockOf = $this->lockOf ? clone $this->lockOf : null;
    }

    protected function cleanLock(): void
    {
        $this->lockStrength = '';
        $this->lockOption = '';
        $this->lockOf = null;
    }

    protected function buildLock(): void
    {
        if (!$this->lockStrength) {
            return;
        }
        $this->sql .= " FOR $this->lockStrength";
        if ($this->lockOf) {
            $this->sql .= " OF $this->lockOf";
            $this->addParams($this->lockOf->getParams());
        }
        if ($this->lockOption) {
            $this->sql .= " $this->lockOption";
        }
    }
}