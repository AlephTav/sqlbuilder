<?php

declare(strict_types=1);

namespace AlephTools\SqlBuilder\Sql\Expression;

use AlephTools\SqlBuilder\Query;

class OrderExpression extends AbstractExpression
{
    public function __construct($column = null, $order = null)
    {
        if ($column !== null || $order !== null) {
            $this->append($column, $order);
        }
    }

    /**
     * @param mixed $column
     * @param mixed $order
     * @return static
     */
    public function append($column, $order = null)
    {
        if ($this->sql !== '') {
            $this->sql .= ', ';
        }
        $this->sql .= $this->convertNameToString($this->mapToExpression($column, $order));
        return $this;
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

    protected function mapToExpression($column, $order)
    {
        if ($order === null) {
            return $column;
        }
        if (is_scalar($column)) {
            $expression = [$column => $order];
        } else {
            $expression = [[$column, $order]];
        }
        return $expression;
    }

    protected function arrayToString(array $expression): string
    {
        $list = [];
        foreach ($expression as $column => $order) {
            if (is_numeric($column)) {
                if (is_array($order) && \count($order) === 2) {
                    [$column, $order] = $order;
                } else {
                    $column = $order;
                    $order = null;
                }
            }
            $order = $order === null ? '' : $this->convertNameToString($order);
            $list[] = $this->convertNameToString($column) . ($order === '' ? '' : " $order");
        }
        return implode(', ', $list);
    }
}
