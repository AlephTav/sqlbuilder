<?php

declare(strict_types=1);

namespace AlephTools\SqlBuilder\Sql\Clause;

use AlephTools\SqlBuilder\Sql\Expression\OrderExpression;

trait OrderClause
{
    /**
     * @var OrderExpression|null
     */
    protected $order;

    /**
     * @param mixed $column
     * @param mixed $order
     * @return static
     */
    public function orderBy($column, $order = null)
    {
        $this->order = $this->order ?? $this->createOrderExpression();
        $this->order->append($column, $order);
        $this->built = false;
        return $this;
    }

    /**
     * @return OrderExpression
     */
    protected function createOrderExpression()
    {
        return new OrderExpression();
    }

    protected function buildOrderBy(): void
    {
        if ($this->order) {
            $this->sql .= " ORDER BY $this->order";
            $this->addParams($this->order->getParams());
        }
    }
}
