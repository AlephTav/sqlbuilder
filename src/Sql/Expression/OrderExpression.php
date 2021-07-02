<?php

declare(strict_types=1);

namespace AlephTools\SqlBuilder\Sql\Expression;

class OrderExpression extends AbstractListExpression
{
    public function __construct($column = null, $order = null)
    {
        parent::__construct(true);
        if ($column !== null) {
            $this->append($column, $order);
        }
    }

    /**
     * @param mixed $column
     * @param mixed $order
     * @return static
     */
    public function append($column, $order = null)
    {
        return $this->appendName($order, $column);
    }
}
