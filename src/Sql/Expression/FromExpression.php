<?php

declare(strict_types=1);

namespace AlephTools\SqlBuilder\Sql\Expression;

class FromExpression extends AbstractListExpression
{
    public function __construct(mixed $table = null, mixed $alias = null)
    {
        parent::__construct(false);
        if ($table !== null) {
            $this->append($table, $alias);
        }
    }

    public function append(mixed $table, mixed $alias = null): static
    {
        return $this->appendName($table, $alias);
    }
}
