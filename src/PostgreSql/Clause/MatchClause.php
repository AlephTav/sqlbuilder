<?php

namespace AlephTools\SqlBuilder\PostgreSql\Clause;

use AlephTools\SqlBuilder\PostgreSql\InsertStatement;
use AlephTools\SqlBuilder\PostgreSql\UpdateStatement;
use AlephTools\SqlBuilder\Sql\Expression\ColumnListExpression;
use AlephTools\SqlBuilder\Sql\Expression\ConditionalExpression;
use AlephTools\SqlBuilder\Sql\Expression\RawExpression;
use Closure;

trait MatchClause
{
    protected string $matchSql = '';
    protected array $matchParams = [];

    public function whenMatched(): static
    {
        $this->append(true);
        return $this;
    }

    public function whenMatchedAnd(mixed $column, mixed $operator = null, mixed $value = null): static
    {
        $condition = new ConditionalExpression($column, $operator, $value);
        $this->append(
            matched: true,
            condition: $condition,
        );

        return $this;
    }

    public function whenNotMatched(): static
    {
        $this->append(false);
        return $this;
    }

    public function whenNotMatchedAnd(mixed $column, mixed $operator = null, mixed $value = null): static
    {
        $condition = new ConditionalExpression($column, $operator, $value);
        $this->append(
            matched: false,
            condition: $condition,
        );
        return $this;
    }

    public function thenDoNothing(): static
    {
        $assigment = 'DO NOTHING';
        $this->addAssignment($assigment);

        return $this;
    }

    public function thenDelete(): static
    {
        $assigment = 'DELETE';
        $this->addAssignment($assigment);

        return $this;
    }

    public function then(InsertStatement|UpdateStatement $statement): static
    {
        $this->addAssignment($statement);

        return $this;
    }

    protected function append(bool $matched, mixed $condition = null, mixed $assignment = null): static
    {
        $this->matchSql .= ' WHEN' . ($matched ? '' : ' NOT') . ' MATCHED';

        $this->addCondition($condition);
        $this->addAssignment($assignment);

        return $this;
    }

    protected function addCondition(mixed $condition): void
    {
        if ($condition === null) {
            return;
        }
        if ($this->isConditionalExpression($condition)) {
            $conditions = new ConditionalExpression($condition);
        } else {
            $conditions = new ColumnListExpression($condition);
        }

        $this->matchSql .= " AND $conditions";
        $this->matchParams = array_merge($this->matchParams, $conditions->getParams());
    }

    private function isConditionalExpression(mixed $expression): bool
    {
        return is_string($expression) ||
            $expression instanceof RawExpression ||
            $expression instanceof ConditionalExpression ||
            $expression instanceof Closure;
    }

    protected function addAssignment(mixed $assigment): void
    {
        if ($assigment === null) {
            return;
        }

        if (is_string($assigment)) {
            $assigment = new RawExpression($assigment);
        }

        $this->matchParams = array_merge($this->matchParams, $assigment->getParams());
        $this->matchSql .= " THEN $assigment";
    }

    protected function cloneWhenMatched(mixed $copy): void
    {
        $copy->matchSql = $this->matchSql;
        $copy->matchParams = $this->matchParams;
    }

    public function cleanWhenMatched(): void
    {
        $this->matchSql = '';
        $this->matchParams = [];
    }

    protected function buildWhenMatched(): void
    {
        $this->sql .= $this->matchSql;
        $this->addParams($this->matchParams);
    }
}