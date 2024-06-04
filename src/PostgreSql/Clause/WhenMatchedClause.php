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
        $this->when(false);
        return $this;
    }

    public function whenMatchedAnd(mixed $column, mixed $operator = null, mixed $value = null): static
    {
        $condition = new ConditionalExpression($column, $operator, $value);
        $this->when(
            negative: false,
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

    protected function when(mixed $negative = null, mixed $condition = null, mixed $assigment = null): static
    {
        $this->when = $this->when ?? $this->createWhenExpression();
        $this->when->append($negative, $condition, $assigment);
        $this->built = false;
        return $this;
    }

    protected function createWhenExpression(): WhenMatchedExpression
    {
        return new WhenMatchedExpression();
    }

    public function thenDoNothing(): static
    {
        $assigment = 'DO NOTHING';
        $this->when(
            assigment: $assigment
        );

        return $this;
    }

    public function thenDelete(): static
    {
        $assigment = 'DELETE';
        $this->when(
            assigment: $assigment
        );

        return $this;
    }

    public function thenUpdate(mixed $column, mixed $value = null): static
    {
        $assigment = new AssignmentExpression($column, $value);
        $this->addParams($assigment->getParams());
        $this->when(
            assigment: 'UPDATE SET ' . $assigment->toSql(),
        );

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

        $this->when(
            assigment: 'INSERT (' . $columns->toSql() . ')' . ($values ? ' VALUES (' . $values->toSql() . ')' : ''),
        );

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