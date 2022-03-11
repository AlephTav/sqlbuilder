<?php

declare(strict_types=1);

namespace AlephTools\SqlBuilder\Sql\Clause;

use AlephTools\SqlBuilder\Query;

trait QueryClause
{
    protected ?Query $query = null;

    public function select(Query $query): static
    {
        $this->query = $query;
        $this->built = false;
        return $this;
    }

    protected function buildQuery(): void
    {
        if ($this->query) {
            $this->sql .= " $this->query";
            $this->addParams($this->query->getParams());
        }
    }

    protected function cloneQuery(mixed $copy): void
    {
        $copy->query = $this->query?->copy();
    }

    protected function cleanQuery(): void
    {
        $this->query = null;
    }
}
