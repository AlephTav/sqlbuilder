<?php

declare(strict_types=1);

namespace AlephTools\SqlBuilder\Sql\Expression;

use Closure;
use function count;
use function is_array;
use function is_string;

class JoinExpression extends AbstractExpression
{
    public function __construct(string $type = '', mixed $table = null, mixed $alias = null, mixed $condition = null)
    {
        if ($table !== null) {
            $this->append($type, $table, $alias, $condition);
        }
    }

    public function append(string $type, mixed $table, mixed $alias = null, mixed $condition = null): static
    {
        if ($this->sql !== '') {
            $this->sql .= ' ';
        }
        $this->sql .= "$type " . $this->convertTableToString($table, $alias);
        $this->addCondition($condition);
        return $this;
    }

    protected function convertTableToString(mixed $table, mixed $alias): string
    {
        $tb = new ColumnListExpression($table, $alias);
        $this->addParams($tb->getParams());
        if (is_array($table) && count($table) > 1) {
            return "($tb)";
        }
        return $tb->toSql();
    }

    protected function addCondition(mixed $condition): void
    {
        if ($condition === null) {
            return;
        }
        if ($this->isConditionalExpression($condition)) {
            $conditions = new ConditionalExpression($condition);
            $this->sql .= " ON $conditions";
        } else {
            $conditions = new ColumnListExpression($condition);
            $this->sql .= " USING ($conditions)";
        }
        $this->addParams($conditions->getParams());
    }

    private function isConditionalExpression(mixed $expression): bool
    {
        return is_string($expression) ||
            $expression instanceof RawExpression ||
            $expression instanceof ConditionalExpression ||
            $expression instanceof Closure;
    }
}
