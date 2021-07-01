<?php

namespace AlephTools\SqlBuilder\Sql\Expression;

use AlephTools\SqlBuilder\Query;

class ValueListExpression extends AbstractExpression
{
    public function __construct($values = null)
    {
        if ($values !== null) {
            $this->append($values);
        }
    }

    /**
     * @param mixed $values
     * @return static
     */
    public function append($values)
    {
        if ($this->sql !== '') {
            $this->sql .= ', ';
        }
        $this->sql .= $this->convertValueListToString($values);
        return $this;
    }

    protected function convertValueListToString($expression): string
    {
        if ($expression instanceof RawExpression) {
            return $this->rawExpressionToString($expression);
        }
        if ($expression instanceof Query) {
            return '(' . $this->convertValueToString($expression) . ')';
        }
        if (is_array($expression)) {
            return $this->convertListOfValueListsToString($expression);
        }
        return (string)$expression;
    }

    protected function convertListOfValueListsToString(array $expression): string
    {
        $values = [];
        if (is_array(reset($expression))) {
            foreach ($expression as $value) {
                $values[] = $this->convertValueListToString($value);
            }
            return implode(', ', $values);
        }
        foreach ($expression as $value) {
            $values[] = $this->convertValueToString($value);
        }
        return '(' . implode(', ', $values) . ')';
    }

    protected function convertValueToString($expression): string
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
        if (is_array($expression)) {
            return $this->arrayToString($expression);
        }
        $param = self::nextParameterName();
        $this->params[$param] = $expression;
        return ":$param";
    }

    protected function arrayToString(array $expression): string
    {
        $values = [];
        foreach ($expression as $value) {
            $values[] = $this->convertValueToString($value);
        }
        return implode(', ', $values);
    }
}
