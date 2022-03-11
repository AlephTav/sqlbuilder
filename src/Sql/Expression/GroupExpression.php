<?php

declare(strict_types=1);

namespace AlephTools\SqlBuilder\Sql\Expression;

class GroupExpression extends AbstractListExpression
{
    public function __construct(mixed $column = null, mixed $order = null)
    {
        parent::__construct(true);
        if ($column !== null) {
            $this->append($column, $order);
        }
    }

    public function append(mixed $column, mixed $order = null): static
    {
        return $this->appendName($order, $column);
    }
}
