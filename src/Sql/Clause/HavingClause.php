<?php

declare(strict_types=1);

namespace AlephTools\SqlBuilder\Sql\Clause;

use AlephTools\SqlBuilder\Sql\Expression\HavingExpression;

trait HavingClause
{
    protected ?HavingExpression $having = null;

    public function andHaving(mixed $column, mixed $operator = null, mixed $value = null): static
    {
        return $this->having($column, $operator, $value);
    }

    public function orHaving(mixed $column, mixed $operator = null, mixed $value = null): static
    {
        return $this->having($column, $operator, $value, 'OR');
    }

    public function having(mixed $column, mixed $operator = null, mixed $value = null, string $connector = 'AND'): static
    {
        $this->having = $this->having ?? $this->createHavingExpression();
        $this->having->where($column, $operator, $value, $connector);
        $this->built = false;
        return $this;
    }

    protected function createHavingExpression(): HavingExpression
    {
        return new HavingExpression();
    }

    protected function cloneHaving(mixed $copy): void
    {
        $copy->having = $this->having ? clone $this->having : null;
    }

    public function cleanHaving(): void
    {
        $this->having = null;
    }

    protected function buildHaving(): void
    {
        if ($this->having) {
            $this->sql .= " HAVING $this->having";
            $this->addParams($this->having->getParams());
        }
    }
}
