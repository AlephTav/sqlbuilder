<?php

declare(strict_types=1);

namespace AlephTools\SqlBuilder\Sql\Clause;

use AlephTools\SqlBuilder\Sql\Expression\OrderExpression;

trait OrderClause
{
    protected ?OrderExpression $order = null;

    public function orderBy(mixed $column, mixed $order = null): static
    {
        $this->order = $this->order ?? $this->createOrderExpression();
        $this->order->append($column, $order);
        $this->built = false;
        return $this;
    }

    protected function createOrderExpression(): OrderExpression
    {
        return new OrderExpression();
    }

    protected function cloneOrderBy(mixed $copy): void
    {
        $copy->order = $this->order ? clone $this->order : null;
    }

    protected function cleanOrderBy(): void
    {
        $this->order = null;
    }

    protected function buildOrderBy(): void
    {
        if ($this->order) {
            $this->sql .= " ORDER BY $this->order";
            $this->addParams($this->order->getParams());
        }
    }
}
