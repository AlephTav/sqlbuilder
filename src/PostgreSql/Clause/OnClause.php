<?php

declare(strict_types=1);

namespace AlephTools\SqlBuilder\PostgreSql\Clause;

use AlephTools\SqlBuilder\Sql\Expression\ConditionalExpression;

trait OnClause
{
    protected ?ConditionalExpression $on = null;

    public function andOn(mixed $column, mixed $operator = null, mixed $value = null): static
    {
        return $this->on($column, $operator, $value, 'AND');
    }

    public function orOn(mixed $column, mixed $operator = null, mixed $value = null): static
    {
        return $this->on($column, $operator, $value, 'OR');
    }

    public function on(mixed $column, mixed $operator = null, mixed $value = null, string $connector = 'AND'): static
    {
        $this->on ??= $this->createOnConditionalExpression();
        $this->on->where($column, $operator, $value, $connector);
        return $this;
    }

    protected function createOnConditionalExpression(): ConditionalExpression
    {
        return new ConditionalExpression();
    }

    protected function cloneOn(mixed $copy): void
    {
        $copy->on = $this->on ? clone $this->on : null;
    }

    public function cleanOn(): void
    {
        $this->on = null;
    }

    protected function buildOn(): void
    {
        if ($this->on !== null) {
            $this->sql .= " ON $this->on";
            $this->addParams($this->on->getParams());
        }
    }
}