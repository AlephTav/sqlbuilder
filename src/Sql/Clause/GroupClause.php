<?php

declare(strict_types=1);

namespace AlephTools\SqlBuilder\Sql\Clause;

use AlephTools\SqlBuilder\Sql\Expression\GroupExpression;

trait GroupClause
{
    protected ?GroupExpression $group = null;

    public function groupBy(mixed $column, mixed $order = null): static
    {
        $this->group = $this->group ?? $this->createGroupExpression();
        $this->group->append($column, $order);
        $this->built = false;
        return $this;
    }

    protected function createGroupExpression(): GroupExpression
    {
        return new GroupExpression();
    }

    protected function cloneGroupBy(mixed $copy): void
    {
        $copy->group = $this->group ? clone $this->group : null;
    }

    protected function cleanGroupBy(): void
    {
        $this->group = null;
    }

    protected function buildGroupBy(): void
    {
        if ($this->group) {
            $this->sql .= " GROUP BY $this->group";
            $this->addParams($this->group->getParams());
        }
    }
}
