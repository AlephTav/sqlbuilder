<?php

declare(strict_types=1);

namespace AlephTools\SqlBuilder\Sql\Expression;

use AlephTools\SqlBuilder\Query;

class FromExpression extends AbstractExpression
{
    public function __construct($table = null, $alias = null)
    {
        if ($table !== null || $alias !== null) {
            $this->append($table, $alias);
        }
    }

    /**
     * @param mixed $table
     * @param mixed $alias
     * @return static
     */
    public function append($table, $alias = null)
    {
        if ($this->sql !== '') {
            $this->sql .= ', ';
        }
        $this->sql .= $this->convertNameToString($this->mapToExpression($table, $alias));
        return $this;
    }

    protected function mapToExpression($table, $alias)
    {
        if ($alias === null) {
            return $table;
        }
        if (is_scalar($alias)) {
            $expression = [$alias => $table];
        } else {
            $expression = [[$alias, $table]];
        }
        return $expression;
    }

    protected function convertNameToString($expression): string
    {
        if ($expression === null) {
            return $this->nullToString();
        }
        if ($expression instanceof RawExpression) {
            return $this->rawExpressionToString($expression);
        }
        if ($expression instanceof Query) {
            return $this->queryToString($expression);
        }
        if ($expression instanceof ValueListExpression) {
            return $this->valueListExpressionToString($expression);
        }
        if (is_array($expression)) {
            return $this->arrayToString($expression);
        }
        return (string)$expression;
    }

    protected function valueListExpressionToString(ValueListExpression $expression): string
    {
        $this->addParams($expression->getParams());
        return "(VALUES $expression)";
    }

    protected function arrayToString(array $expression): string
    {
        $list = [];
        foreach ($expression as $alias => $column) {
            if (is_numeric($alias)) {
                if (is_array($column) && \count($column) === 2) {
                    [$alias, $column] = $column;
                } else {
                    $alias = null;
                }
            }
            $alias = $alias === null ? '' : $this->convertNameToString($alias);
            $list[] = $this->convertNameToString($column) . ($alias === '' ? '' : " $alias");
        }
        return implode(', ', $list);
    }
}
