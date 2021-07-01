<?php

declare(strict_types=1);

namespace AlephTools\SqlBuilder\Sql\Expression;

use AlephTools\SqlBuilder\Query;

abstract class AbstractListExpression extends AbstractExpression
{
    private bool $reverseOrder;

    public function __construct(bool $reverseOrder)
    {
        $this->reverseOrder = $reverseOrder;
    }

    /**
     * @param mixed $name
     * @param mixed $alias
     * @return static
     */
    protected function appendName($name, $alias)
    {
        if ($this->sql !== '') {
            $this->sql .= ', ';
        }
        $this->sql .= $this->convertNameToString($this->mapToExpression($name, $alias));
        return $this;
    }

    protected function mapToExpression($name, $alias)
    {
        if ($alias === null && !$this->reverseOrder) {
            return $name;
        }
        if ($name === null && $this->reverseOrder) {
            return $alias;
        }
        if (is_scalar($alias)) {
            return [(string)$alias => $name];
        }
        return [[$alias, $name]];
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
        foreach ($expression as $alias => $name) {
            if (is_numeric($alias)) {
                if (is_array($name) && \count($name) === 2) {
                    [$alias, $name] = $name;
                } elseif ($this->reverseOrder) {
                    $alias = $name;
                    $name = null;
                } else {
                    $alias = null;
                }
            }
            if ($this->reverseOrder) {
                $tmp = $name;
                $name = $alias;
                $alias = $tmp;
            }
            $alias = $alias === null ? '' : $this->convertNameToString($alias);
            $list[] = $this->convertNameToString($name) . ($alias === '' ? '' : " $alias");
        }
        return implode(', ', $list);
    }
}
