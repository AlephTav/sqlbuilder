<?php

namespace AlephTools\SqlBuilder\PostgreSql\Clause;

use AlephTools\SqlBuilder\Sql\Expression\ConditionalExpression;

trait OnClause
{
    protected ?ConditionalExpression $condition = null;

    public function on(mixed $column, mixed $operator = null, mixed $value = null, string $connector = 'AND'): static
    {
        $this->condition = $this->condition ?? $this->createConditionalExpression();
        $this->condition->where($column, $operator, $value, $connector);
        return $this;
    }

    protected function createConditionalExpression(): ConditionalExpression
    {
        return new ConditionalExpression();
    }

    protected function cloneOn(mixed $copy): void
    {
        $copy->conditional = $this->condition ? clone $this->condition : null;
    }

    public function cleanOn(): void
    {
        $this->condition = null;
    }

    protected function buildOn(): void
    {
        if ($this->condition !== null) {
            $this->sql .= " ON $this->condition";
            $this->addParams($this->condition->getParams());
        }
    }
}