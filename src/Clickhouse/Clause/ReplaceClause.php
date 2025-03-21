<?php

declare(strict_types=1);

namespace AlephTools\SqlBuilder\Clickhouse\Clause;

use AlephTools\SqlBuilder\Sql\Expression\SelectExpression;

trait ReplaceClause
{
    protected ?SelectExpression $replace = null;

    public function replace(mixed $column, mixed $alias = null): static
    {
        $this->replace ??= new SelectExpression();
        $this->replace->append($column, $alias);
        $this->built = false;
        return $this;
    }

    protected function cloneReplace(mixed $copy): void
    {
        $copy->replace = $this->replace ? clone $this->replace : null;
    }

    public function cleanReplace(): void
    {
        $this->replace = null;
    }

    protected function buildReplace(): void
    {
        if ($this->replace !== null) {
            $this->sql .= " REPLACE($this->replace)";
        }
    }
}
