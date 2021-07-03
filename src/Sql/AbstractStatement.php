<?php

declare(strict_types=1);

namespace AlephTools\SqlBuilder\Sql;

use AlephTools\SqlBuilder\Statement;
use AlephTools\SqlBuilder\StatementExecutor;
use RuntimeException;

abstract class AbstractStatement implements Statement
{
    protected ?StatementExecutor $db;
    protected string $sql = '';
    protected array $params = [];

    /**
     * Contains TRUE if the query has built.
     */
    protected bool $built = false;

    final public function __construct(StatementExecutor $db = null)
    {
        $this->db = $db;
    }

    /**
     * @return static
     */
    abstract public function copy();

    abstract public function clean(): void;

    public function getStatementExecutor(): ?StatementExecutor
    {
        return $this->db;
    }

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

    protected function db(): StatementExecutor
    {
        $this->validateAndBuild();
        /** @var StatementExecutor */
        return $this->db;
    }

    protected function validateAndBuild(): void
    {
        if ($this->db === null) {
            throw new RuntimeException('The statement executor must not be null.');
        }
        $this->build();
    }
}
