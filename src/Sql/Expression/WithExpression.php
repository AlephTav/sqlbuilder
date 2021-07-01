<?php

namespace AlephTools\SqlBuilder\Sql\Expression;

use AlephTools\SqlBuilder\Query;

class WithExpression extends AbstractExpression
{
    public function __construct($query = null, $alias = null, bool $recursive = false)
    {
        if ($query !== null) {
            $this->append($query, $alias, $recursive);
        }
    }

    /**
     * @param mixed $query
     * @param mixed $alias
     * @param bool $recursive
     * @return static
     */
    public function append($query, $alias = null, bool $recursive = false)
    {
        if (strlen($this->sql) > 0) {
            $this->sql .= ', ';
        }
        if ($alias === null) {
            $expression = $query;
        } else if (is_scalar($alias)) {
            $expression = [$alias => $query];
        } else {
            $expression = [[$alias, $query]];
        }
        $this->sql .= ($recursive ? 'RECURSIVE ' : '') . $this->convertNameToString($expression);
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
                if (is_array($query) && \count($query) === 2) {
                    list($alias, $query) = $query;
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
