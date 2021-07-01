<?php

declare(strict_types=1);

namespace AlephTools\SqlBuilder\Sql;

use AlephTools\SqlBuilder\Statement;

abstract class AbstractStatement implements Statement
{
    protected string $sql = '';
    protected array $params = [];

    /**
     * Contains TRUE if the query has built.
     */
    protected bool $built = false;

    final public function __construct()
    {
    }

    /**
     * @return static
     */
    abstract public function copy();

    abstract public function clean(): void;

    /**
     * @return static
     */
    abstract public function build();

    public function getParams(): array
    {
        $this->build();
        return $this->params;
    }

    protected function addParams(array $params): void
    {
        $this->params = array_merge($this->params, $params);
    }

    public function toSql(): string
    {
        $this->build();
        return $this->sql;
    }

    public function toString(): string
    {
        return $this->toSql();
    }

    public function __toString(): string
    {
        return $this->toSql();
    }
}
