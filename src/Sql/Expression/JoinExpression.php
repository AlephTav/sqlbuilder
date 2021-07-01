<?php

namespace AlephTools\SqlBuilder\Sql\Expression;

use Closure;

class JoinExpression extends AbstractExpression
{
    public function __construct(string $type = '', $table = null, $alias = null, $condition = null)
    {
        if ($table !== null) {
            $this->append($type, $table, $alias, $condition);
        }
    }

    /**
     * @param string $type
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

    protected function convertTableToString($table, $alias): string
    {
        $tb = new ListExpression($table, $alias);
        $this->addParams($tb->getParams());
        if (is_array($table) && \count($table) > 1) {
            return "($tb)";
        }
        return $tb->toSql();
    }

    protected function addCondition($condition): void
    {
        if ($condition === null) {
            return;
        }
        if ($this->isConditionalExpression($condition)) {
            $conditions = new ConditionalExpression($condition);
            $this->sql .= " ON $conditions";
        } else {
            $conditions = new ListExpression($condition);
            $this->sql .= " USING ($conditions)";
        }
        $this->addParams($conditions->getParams());
    }

    private function isConditionalExpression($expression): bool
    {
        return is_string($expression) ||
            $expression instanceof RawExpression ||
            $expression instanceof ConditionalExpression ||
            $expression instanceof Closure;
    }
}
