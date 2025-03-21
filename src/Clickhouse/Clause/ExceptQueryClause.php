<?php

declare(strict_types=1);

namespace AlephTools\SqlBuilder\Clickhouse\Clause;

use AlephTools\SqlBuilder\Sql\Expression\SelectExpression;

trait ExceptQueryClause
{
    protected SelectExpression|null $exceptQuery = null;
    protected bool $exceptQueryDistinct = false;

    public function exceptQuery(mixed $except, bool $distinct = false): static
    {
        $this->exceptQuery ??= $this->createSelectExpression();
        $this->exceptQuery->append($except);
        $this->exceptQueryDistinct = $distinct;
        $this->built = false;
        return $this;
    }

    protected function createExceptQuerySelectExpression(): SelectExpression
    {
        return new SelectExpression();
    }

    protected function cloneExceptQuery(mixed $copy): void
    {
        $copy->exceptQuery = $this->exceptQuery ? clone $this->exceptQuery : null;
        $copy->exceptQueryDistinct = $this->exceptQueryDistinct;
    }

    public function cleanExceptQuery(): void
    {
        $this->exceptQuery = null;
        $this->exceptQueryDistinct = false;
    }

    protected function buildExceptQuery(): void
    {
        if ($this->exceptQuery !== null) {
            $this->sql .= ' EXCEPT';
            if ($this->exceptQueryDistinct) {
                $this->sql .= ' DISTINCT';
            }
            $this->sql .= " $this->exceptQuery";
            $this->addParams($this->exceptQuery->getParams());
        }
    }
}
