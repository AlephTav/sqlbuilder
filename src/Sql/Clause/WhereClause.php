<?php

declare(strict_types=1);

namespace AlephTools\SqlBuilder\Sql\Clause;

use AlephTools\SqlBuilder\Sql\Expression\WhereExpression;

trait WhereClause
{
    protected ?WhereExpression $where = null;

    public function andWhere(mixed $column, mixed $operator = null, mixed $value = null): static
    {
        return $this->where($column, $operator, $value);
    }

    public function orWhere(mixed $column, mixed $operator = null, mixed $value = null): static
    {
        return $this->where($column, $operator, $value, 'OR');
    }

    public function where(mixed $column, mixed $operator = null, mixed $value = null, string $connector = 'AND'): static
    {
        $this->where = $this->where ?? $this->createWhereExpression();
        $this->where->where($column, $operator, $value, $connector);
        $this->built = false;
        return $this;
    }

    protected function createWhereExpression(): WhereExpression
    {
        return new WhereExpression();
    }

    protected function cloneWhere(mixed $copy): void
    {
        $copy->where = $this->where ? clone $this->where : null;
    }

    protected function cleanWhere(): void
    {
        $this->where = null;
    }

    protected function buildWhere(): void
    {
        if ($this->where) {
            $this->sql .= " WHERE $this->where";
            $this->addParams($this->where->getParams());
        }
    }
}
