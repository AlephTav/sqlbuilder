<?php

declare(strict_types=1);

namespace AlephTools\SqlBuilder\Sql\Expression;

use AlephTools\SqlBuilder\Query;
use Closure;
use function implode;
use function is_array;
use function is_numeric;
use function is_string;
use function strtolower;

class ConditionalExpression extends AbstractExpression
{
    public function __construct(mixed $column = null, mixed $operator = null, mixed $value = null)
    {
        if ($column !== null) {
            $this->and($column, $operator, $value);
        }
    }

    public function andWhere(mixed $column, mixed $operator = null, mixed $value = null): static
    {
        return $this->with($column, $operator, $value, 'AND');
    }

    public function orWhere(mixed $column, mixed $operator = null, mixed $value = null): static
    {
        return $this->with($column, $operator, $value, 'OR');
    }

    public function where(mixed $column, mixed $operator = null, mixed $value = null, string $connector = 'AND'): static
    {
        return $this->with($column, $operator, $value, $connector);
    }

    public function and(mixed $column, mixed $operator = null, mixed $value = null): static
    {
        return $this->with($column, $operator, $value, 'AND');
    }

    /**
     * @param mixed $column
     * @param mixed $operator
     * @param mixed $value
     * @return static
     */
    public function or(mixed $column, mixed $operator = null, mixed $value = null): static
    {
        return $this->with($column, $operator, $value, 'OR');
    }

    protected function with(mixed $column, mixed $operator, mixed $value, string $connector): static
    {
        if ($this->sql !== '') {
            $this->sql .= " $connector ";
        }
        if ($operator !== null) {
            if (is_string($operator)) {
                $this->sql .= $this->convertOperandToString($column) . " $operator " .
                    $this->convertValueToString($value, $operator);
            } else {
                $this->sql .= "$column " . $this->convertValueToString($operator, $column);
            }
        } else {
            $this->sql .= $this->convertOperandToString($column);
        }
        return $this;
    }

    protected function convertOperandToString(mixed $expression): string
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
        if ($expression instanceof ConditionalExpression) {
            return $this->conditionToString($expression);
        }
        if (is_array($expression)) {
            return $this->arrayToString($expression);
        }
        if ($expression instanceof Closure) {
            return $this->closureToString($expression);
        }
        return (string)$expression;
    }

    private function convertValueToString(mixed $expression, string $operator): string
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
            return $this->arrayValueToString($expression, $operator);
        }
        $param = self::nextParameterName();
        $this->params[$param] = $expression;
        return ":$param";
    }

    protected function arrayToString(array $expression): string
    {
        $list = [];
        foreach ($expression as $key => $value) {
            if (is_numeric($key)) {
                $list[] = $this->convertOperandToString($value);
            } else {
                $list[] = $this->convertOperandToString($key) .
                    ' = ' . $this->convertValueToString($value, '=');
            }
        }
        return implode(' AND ', $list);
    }

    protected function arrayValueToString(array $expression, string $operator): string
    {
        $isBetween = $this->isBetween($operator);
        $list = [];
        foreach ($expression as $value) {
            $list[] = $this->convertValueToString($value, $operator);
        }
        $sql = implode($isBetween ? ' AND ' : ', ', $list);
        if (!$isBetween) {
            $sql = "($sql)";
        }
        return $sql;
    }

    protected function isBetween(string $operator): bool
    {
        $op = strtolower($operator);
        return $op === 'between' || $op === 'not between';
    }
}
