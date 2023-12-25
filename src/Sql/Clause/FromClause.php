<?php

declare(strict_types=1);

namespace AlephTools\SqlBuilder\Sql\Clause;

use AlephTools\SqlBuilder\Sql\Expression\FromExpression;

trait FromClause
{
    protected ?FromExpression $from = null;

    public function from(mixed $table, mixed $alias = null): static
    {
        $this->from = $this->from ?? $this->createFromExpression();
        $this->from->append($table, $alias);
        $this->built = false;
        return $this;
    }

    protected function createFromExpression(): FromExpression
    {
        return new FromExpression();
    }

    protected function cloneFrom(mixed $copy): void
    {
        $copy->from = $this->from ? clone $this->from : null;
    }

    public function cleanFrom(): void
    {
        $this->from = null;
    }

    protected function buildFrom(): void
    {
        if ($this->from) {
            $this->sql .= " FROM $this->from";
            $this->addParams($this->from->getParams());
        }
    }
}
