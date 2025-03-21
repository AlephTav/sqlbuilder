<?php

declare(strict_types=1);

namespace AlephTools\SqlBuilder\Clickhouse\Clause;

trait WindowClause
{
    protected string|null $windowAlias = null;
    protected string|null $windowFunction = null;

    public function window(string $alias, string $function): static
    {
        $this->windowAlias = $alias;
        $this->windowFunction = $function;
        $this->built = false;
        return $this;
    }

    protected function cloneWindow(mixed $copy): void
    {
        $copy->windowAlias = $this->windowAlias;
        $copy->windowFunction = $this->windowFunction;
    }

    protected function cleanWindow(): void
    {
        $this->windowAlias = null;
        $this->windowFunction = null;
    }

    protected function buildWindow(): void
    {
        if ($this->windowAlias !== null && $this->windowFunction !== null) {
            $this->sql .= " WINDOW $this->windowAlias AS ($this->windowFunction)";
        }
    }
}
