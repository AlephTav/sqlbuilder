<?php

namespace AlephTools\SqlBuilder\Sql\Expression;

use AlephTools\SqlBuilder\Query;

abstract class AbstractExpression
{
    protected string $sql = '';
    protected array $params = [];

    /**
     * Used to form a parameter name.
     */
    private static int $parameterIndex = 0;

    public function __toString(): string
    {
        return $this->toSql();
    }

    public function toString(): string
    {
        return $this->toSql();
    }

    public function toSql(): string
    {
        return $this->sql;
    }

    public function getParams(): array
    {
        return $this->params;
    }

    protected function addParams(array $params): void
    {
        $this->params = array_merge($this->params, $params);
    }

    /**
     * Generates the next parameter name of a query.
     *
     * @return string
     */
    protected static function nextParameterName(): string
    {
        return 'p' . (++self::$parameterIndex);
    }

    public static function resetParameterIndex(): void
    {
        self::$parameterIndex = 0;
    }

    protected function nullToString(): string
    {
        return "NULL";
    }

    protected function rawExpressionToString(RawExpression $expression): string
    {
        $this->addParams($expression->getParams());
        return $expression->toSql();
    }

    protected function queryToString(Query $expression): string
    {
        $this->addParams($expression->getParams());
        return "($expression)";
    }
}
