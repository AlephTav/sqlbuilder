<?php

declare(strict_types=1);

namespace AlephTools\SqlBuilder\Sql\Expression;

use AlephTools\SqlBuilder\Query;
use function implode;
use function is_array;
use function is_numeric;
use function is_scalar;

class AssignmentExpression extends AbstractExpression
{
    public function __construct(mixed $column = null, mixed $value = null)
    {
        if ($column !== null) {
            $this->append($column, $value);
        }
    }

    public function append(mixed $column, mixed $value = null): static
    {
        if ($this->sql !== '') {
            $this->sql .= ', ';
        }
        if ($value === null) {
            $this->sql .= $this->convertNameToString($column);
        } else {
            if (!is_scalar($column)) {
                $column = $this->convertNameToString($column);
            }
            $this->sql .= $this->convertNameToString([(string)$column => $value]);
        }
        return $this;
    }

    protected function convertNameToString(mixed $expression): string
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
        return (string)$expression;
    }

    protected function arrayToString(array $expression): string
    {
        $list = [];
        foreach ($expression as $column => $value) {
            if (is_numeric($column)) {
                $list[] = $this->convertNameToString($value);
            } else {
                $value = $this->convertValueToString($value);
                $list[] = $this->convertNameToString($column) . ' = ' . ($value === '' ? 'DEFAULT' : $value);
            }
        }
        return implode(', ', $list);
    }

    private function convertValueToString(mixed $expression): string
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
        $param = self::nextParameterName();
        $this->params[$param] = $expression;
        return ':' . $param;
    }
}
