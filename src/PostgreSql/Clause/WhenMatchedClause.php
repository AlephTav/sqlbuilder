<?php

namespace AlephTools\SqlBuilder\PostgreSql\Clause;

use AlephTools\SqlBuilder\PostgreSql\Expression\WhenMatchedExpression;
use AlephTools\SqlBuilder\Sql\Expression\AssignmentExpression;
use AlephTools\SqlBuilder\Sql\Expression\ColumnListExpression;
use AlephTools\SqlBuilder\Sql\Expression\ConditionalExpression;
use AlephTools\SqlBuilder\Sql\Expression\ValueListExpression;

trait WhenMatchedClause
{
    protected ?WhenMatchedExpression $when = null;

    public function whenMatched(): static
    {
        $this->when();
        return $this;
    }

    public function whenMatchedAnd(mixed $column, mixed $operator = null, mixed $value = null): static
    {
        $condition = new ConditionalExpression($column, $operator, $value);
        $this->when(
            condition: $condition,
        );

        return $this;
    }

    public function whenNotMatched(): static
    {
        $this->when(true);
        return $this;
    }

    public function whenNotMatchedAnd(mixed $column, mixed $operator = null, mixed $value = null): static
    {
        $condition = new ConditionalExpression($column, $operator, $value);
        $this->when(
            negative: true,
            condition: $condition,
        );
        return $this;
    }

    protected function when(bool $negative = false, mixed $condition = null, mixed $thenPart = null): static
    {
        $this->when = $this->when ?? $this->createWhenExpression();
        $this->when->append($negative, $condition, $thenPart);
        $this->built = false;
        return $this;
    }

    protected function createWhenExpression(): WhenMatchedExpression
    {
        return new WhenMatchedExpression();
    }

    public function thenDoNothing(): static
    {
        $this->when->addAssignment('DO NOTHING');

        return $this;
    }

    public function thenDelete(): static
    {
        $this->when->addAssignment('DELETE');

        return $this;
    }

    public function thenUpdate(mixed $column, mixed $value = null): static
    {
        $assigment = new AssignmentExpression($column, $value);
        $this->when->addAssignment('UPDATE SET ' . $assigment->toSql());
        $this->addParams($assigment->getParams());
        return $this;
    }

    public function thenInsert(mixed $columns, mixed $values = null): static
    {
        $columns = new ColumnListExpression($columns);
        $this->addParams($columns->getParams());
        if ($values !== null) {
            $values = new ValueListExpression($values);
            $this->addParams($values->getParams());
        }

        $this->when->addAssignment('INSERT (' . $columns->toSql() . ')' . ($values ? ' VALUES (' . $values->toSql() . ')' : ''));
        return $this;
    }

    protected function cloneWhenMatched(mixed $copy): void
    {
        $copy->conditional = $this->condition ? clone $this->condition : null;
    }

    public function cleanWhenMatched(): void
    {
        $this->condition = null;
    }

    protected function buildWhenMatched(): void
    {
        if ($this->when) {
            $this->sql .= $this->when->toSql();
            $this->addParams($this->when->getParams());
        }
    }
}