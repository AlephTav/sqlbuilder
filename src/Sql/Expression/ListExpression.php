<?php

declare(strict_types=1);

namespace AlephTools\SqlBuilder\Sql\Expression;

use AlephTools\SqlBuilder\Query;

class ListExpression extends AbstractExpression
{
    public function __construct($column = null, $alias = null)
    {
        if ($column !== null || $alias !== null) {
            $this->append($column, $alias);
        }
    }

    /**
     * @param mixed $column
     * @param mixed $alias
     * @return static
     */
    public function append($column, $alias = null)
    {
        if ($this->sql !== '') {
            $this->sql .= ', ';
        }
        $this->sql .= $this->convertNameToString($this->mapToExpression($column, $alias));
        return $this;
    }

    protected function mapToExpression($column, $alias)
    {
        if ($alias === null) {
            return $column;
        }
        if (is_scalar($alias)) {
            $expression = [$alias => $column];
        } else {
            $expression = [[$alias, $column]];
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
