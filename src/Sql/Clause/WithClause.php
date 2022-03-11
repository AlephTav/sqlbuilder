<?php

declare(strict_types=1);

namespace AlephTools\SqlBuilder\Sql\Clause;

use AlephTools\SqlBuilder\Sql\Expression\WithExpression;

trait WithClause
{
    protected ?WithExpression $with = null;

    public function with(mixed $query, mixed $alias = null): static
    {
        $this->with = $this->with ?? $this->createWithExpression();
        $this->with->append($query, $alias);
        $this->built = false;
        return $this;
    }

    public function withRecursive(mixed $query, mixed $alias = null): static
    {
        $this->with = $this->with ?? $this->createWithExpression();
        $this->with->append($query, $alias, true);
        $this->built = false;
        return $this;
    }

    protected function createWithExpression(): WithExpression
    {
        return new WithExpression();
    }

    protected function cloneWith(mixed $copy): void
    {
        $copy->with = $this->with ? clone $this->with : null;
    }

    protected function cleanWith(): void
    {
        $this->with = null;
    }

    protected function buildWith(): void
    {
        if ($this->with) {
            $this->sql .= "WITH $this->with ";
            $this->addParams($this->with->getParams());
        }
    }
}
