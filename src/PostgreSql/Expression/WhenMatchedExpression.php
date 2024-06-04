<?php

namespace AlephTools\SqlBuilder\PostgreSql\Expression;

use AlephTools\SqlBuilder\Sql\Expression\AbstractExpression;
use AlephTools\SqlBuilder\Sql\Expression\ColumnListExpression;
use AlephTools\SqlBuilder\Sql\Expression\ConditionalExpression;
use AlephTools\SqlBuilder\Sql\Expression\RawExpression;
use Closure;

class WhenMatchedExpression extends AbstractExpression
{
    public function append(bool $negative = false, mixed $condition = null, mixed $assignment = null): static
    {
        $this->sql .= ' WHEN' . ($negative ? ' NOT' : '') . ' MATCHED';
        $this->addCondition($condition);
        $this->addAssignment($assignment);

        return $this;
    }

    public function addCondition(mixed $condition): void
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

    public function addAssignment(mixed $assigment = null): void
    {
        if ($assigment === null) {
            return;
        }

        $this->sql .= " THEN $assigment";
    }
}