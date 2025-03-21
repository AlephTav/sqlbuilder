<?php

declare(strict_types=1);

namespace AlephTools\SqlBuilder\Clickhouse\Clause;

use AlephTools\SqlBuilder\Sql\Expression\WhereExpression;

trait PreWereClause
{
    protected ?WhereExpression $preWhere = null;

    public function andPreWhere(mixed $column, mixed $operator = null, mixed $value = null): static
    {
        return $this->preWhere($column, $operator, $value);
    }

    public function orPreWhere(mixed $column, mixed $operator = null, mixed $value = null): static
    {
        return $this->preWhere($column, $operator, $value, 'OR');
    }

    public function preWhere(
        mixed $column,
        mixed $operator = null,
        mixed $value = null,
        string $connector = 'AND'
    ): static {
        $this->preWhere ??= $this->createPreWhereExpression();
        $this->preWhere->where($column, $operator, $value, $connector);
        $this->built = false;
        return $this;
    }

    protected function createPreWhereExpression(): WhereExpression
    {
        return new WhereExpression();
    }

    protected function clonePreWhere(mixed $copy): void
    {
        $copy->preWhere = $this->preWhere ? clone $this->preWhere : null;
    }

    public function cleanPreWhere(): void
    {
        $this->preWhere = null;
    }

    protected function buildPreWhere(): void
    {
        if ($this->preWhere) {
            $this->sql .= " PREWHERE $this->preWhere";
            $this->addParams($this->preWhere->getParams());
        }
    }
}
