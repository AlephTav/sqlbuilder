<?php

declare(strict_types=1);

namespace AlephTools\SqlBuilder\Sql\Expression;

use Closure;
use AlephTools\SqlBuilder\Query;
use function array_merge;

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
        return 'NULL';
    }

    protected function rawExpressionToString(RawExpression $expression): string
    {
        $this->addParams($expression->getParams());
        return $expression->toSql();
    }

    protected function conditionToString(ConditionalExpression $expression): string
    {
        $this->addParams($expression->getParams());
        return "($expression)";
    }

    protected function closureToString(Closure $expression): string
    {
        $conditions = new ConditionalExpression();
        $expression($conditions);
        return $this->conditionToString($conditions);
    }

    protected function queryToString(Query $expression): string
    {
        $this->addParams($expression->getParams());
        return "($expression)";
    }
}
