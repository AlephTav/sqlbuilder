<?php

namespace AlephTools\SqlBuilder\PostgreSql\Clause;

use AlephTools\SqlBuilder\PostgreSql\Expression\WhenMatchedExpression;
use AlephTools\SqlBuilder\PostgreSql\InsertStatement;
use AlephTools\SqlBuilder\PostgreSql\UpdateStatement;
use AlephTools\SqlBuilder\Sql\Expression\ConditionalExpression;

trait MatchClause
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
            matched: false,
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
            matched: true,
            condition: $condition,
        );
        return $this;
    }

    protected function when(mixed $matched = null, mixed $condition = null, mixed $assigment = null): static
    {
        $this->when ??= $this->createWhenExpression();
        $this->when->append($matched, $condition, $assigment);
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

    public function then(InsertStatement|UpdateStatement $statement): static
    {
        $this->when(
            assigment: $statement,
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