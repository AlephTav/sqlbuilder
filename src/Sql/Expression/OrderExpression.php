<?php

namespace AlephTools\SqlBuilder\Sql\Expression;

class OrderExpression extends ListExpression
{
    public function __construct($column = null, $order = null)
    {
        parent::__construct($column, $order);
    }

    /**
     * @param mixed $column
     * @param mixed $order
     * @return static
     */
    public function append($column, $order = null)
    {
        return parent::append($order, $column);
    }

    protected function mapToExpression($order, $column)
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
                    list($column, $order) = $order;
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