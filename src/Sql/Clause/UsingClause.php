<?php

declare(strict_types=1);

namespace AlephTools\SqlBuilder\Sql\Clause;

use AlephTools\SqlBuilder\Sql\Expression\FromExpression;

trait UsingClause
{
    protected ?FromExpression $using = null;

    public function using(mixed $table, mixed $alias = null): static
    {
        $this->using = $this->using ?? $this->createUsingExpression();
        $this->using->append($table, $alias);
        $this->built = false;
        return $this;
    }

    protected function createUsingExpression(): FromExpression
    {
        return new FromExpression();
    }

    protected function cloneUsing(mixed $copy): void
    {
        $copy->using = $this->using ? clone $this->using : null;
    }

    protected function cleanUsing(): void
    {
        $this->using = null;
    }

    protected function buildUsing(): void
    {
        if ($this->using) {
            $this->sql .= " USING $this->using";
            $this->addParams($this->using->getParams());
        }
    }
}
