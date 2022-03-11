<?php

declare(strict_types=1);

namespace AlephTools\SqlBuilder\Sql\Expression;

use AlephTools\SqlBuilder\Query;
use function count;
use function implode;
use function is_array;
use function is_numeric;
use function is_scalar;

class WithExpression extends AbstractExpression
{
    public function __construct(mixed $query = null, mixed $alias = null, bool $recursive = false)
    {
        if ($query !== null) {
            $this->append($query, $alias, $recursive);
        }
    }

    public function append(mixed $query, mixed $alias = null, bool $recursive = false): static
    {
        if ($this->sql !== '') {
            $this->sql .= ', ';
        }
        if ($alias === null) {
            $expression = $query;
        } elseif (is_scalar($alias)) {
            $expression = [(string)$alias => $query];
        } else {
            $expression = [[$alias, $query]];
        }
        $this->sql .= ($recursive ? 'RECURSIVE ' : '') . $this->convertNameToString($expression);
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
        foreach ($expression as $alias => $query) {
            if (is_numeric($alias)) {
                if (is_array($query) && count($query) === 2) {
                    [$alias, $query] = $query;
                } else {
                    $alias = null;
                }
            }
            $alias = $alias === null ? '' : $this->convertNameToString($alias);
            $list[] = ($alias === '' ? '' : $alias . ' AS ') . $this->convertNameToString($query);
        }
        return implode(', ', $list);
    }
}
