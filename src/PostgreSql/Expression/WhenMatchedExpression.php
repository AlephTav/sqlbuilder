<?php

namespace AlephTools\SqlBuilder\PostgreSql\Expression;

use AlephTools\SqlBuilder\Sql\Expression\AbstractExpression;
use AlephTools\SqlBuilder\Sql\Expression\ColumnListExpression;
use AlephTools\SqlBuilder\Sql\Expression\ConditionalExpression;
use AlephTools\SqlBuilder\Sql\Expression\RawExpression;
use Closure;

class WhenMatchedExpression extends AbstractExpression
{
    public function append(mixed $matched = null, mixed $condition = null, mixed $assignment = null): static
    {
        $this->addMatched($matched);
        $this->addCondition($condition);
        $this->addAssignment($assignment);

        return $this;
    }

    protected function addMatched(mixed $matched): void
    {
        if (!is_bool($matched)) {
            return;
        }
        $this->sql .= ' WHEN' . ($matched ? ' NOT' : '') . ' MATCHED';
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

        $this->sql .= " AND $conditions";
        $this->addParams($conditions->getParams());
    }

    private function isConditionalExpression(mixed $expression): bool
    {
        return is_string($expression) ||
            $expression instanceof RawExpression ||
            $expression instanceof ConditionalExpression ||
            $expression instanceof Closure;
    }

    protected function addAssignment(mixed $assigment = null): void
    {
        if ($assigment === null) {
            return;
        }

        if (is_string($assigment)) {
            $assigment = new RawExpression($assigment);
        }

        $this->addParams($assigment->getParams());
        $this->sql .= " THEN $assigment";
    }
}