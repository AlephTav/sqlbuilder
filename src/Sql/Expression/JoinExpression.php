<?php

declare(strict_types=1);

namespace AlephTools\SqlBuilder\Sql\Expression;

use Closure;

class JoinExpression extends AbstractExpression
{
    /**
     * @param mixed $table
     * @param mixed $alias
     * @param mixed $condition
     */
    public function __construct(string $type = '', $table = null, $alias = null, $condition = null)
    {
        if ($table !== null) {
            $this->append($type, $table, $alias, $condition);
        }
    }

    /**
     * @param mixed $table
     * @param mixed $alias
     * @param mixed $condition
     * @return static
     */
    public function append(string $type, $table, $alias = null, $condition = null)
    {
        if ($this->sql !== '') {
            $this->sql .= ' ';
        }
        $this->sql .= "$type " . $this->convertTableToString($table, $alias);
        $this->addCondition($condition);
        return $this;
    }

    /**
     * @param mixed $table
     * @param mixed $alias
     */
    protected function convertTableToString($table, $alias): string
    {
        $tb = new ColumnListExpression($table, $alias);
        $this->addParams($tb->getParams());
        if (is_array($table) && \count($table) > 1) {
            return "($tb)";
        }
        return $tb->toSql();
    }

    /**
     * @param mixed $condition
     */
    protected function addCondition($condition): void
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

    /**
     * @param mixed $expression
     */
    private function isConditionalExpression($expression): bool
    {
        return is_string($expression) ||
            $expression instanceof RawExpression ||
            $expression instanceof ConditionalExpression ||
            $expression instanceof Closure;
    }
}
