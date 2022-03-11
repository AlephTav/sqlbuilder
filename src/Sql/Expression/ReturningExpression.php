<?php

declare(strict_types=1);

namespace AlephTools\SqlBuilder\Sql\Expression;

class ReturningExpression extends AbstractListExpression
{
    public function __construct(mixed $column = null, mixed $alias = null)
    {
        parent::__construct(false);
        if ($column !== null || $alias !== null) {
            $this->append($column, $alias);
        }
    }

    public function append(mixed $column, mixed $alias = null): static
    {
        return $this->appendName($column, $alias);
    }
}
