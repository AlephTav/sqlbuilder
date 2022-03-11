<?php

declare(strict_types=1);

namespace AlephTools\SqlBuilder\Sql\Clause;

use AlephTools\SqlBuilder\Sql\Expression\ReturningExpression;

trait ReturningClause
{
    protected ?ReturningExpression $returning = null;

    public function returning(mixed $column = null, mixed $alias = null): static
    {
        $this->returning = $this->returning ?? $this->createReturningExpression();
        $this->returning->append($column ?? '*', $alias);
        $this->built = false;
        return $this;
    }

    protected function createReturningExpression(): ReturningExpression
    {
        return new ReturningExpression();
    }

    protected function cloneReturning(mixed $copy): void
    {
        $copy->returning = $this->returning ? clone $this->returning : null;
    }

    protected function cleanReturning(): void
    {
        $this->returning = null;
    }

    protected function buildReturning(): void
    {
        if ($this->returning) {
            $this->sql .= " RETURNING $this->returning";
            $this->addParams($this->returning->getParams());
        }
    }
}
