<?php

declare(strict_types=1);

namespace AlephTools\SqlBuilder\Clickhouse\Clause;

use AlephTools\SqlBuilder\Clickhouse\SelectStatement;

trait IntersectClause
{
    protected SelectStatement|null $intersect = null;
    protected bool $intersectDistinct = false;

    public function intersect(SelectStatement $statement, bool $distinct = false): static
    {
        $this->intersect = $statement;
        $this->intersectDistinct = $distinct;
        $this->built = false;
        return $this;
    }

    protected function cloneIntersect(mixed $copy): void
    {
        $copy->intersect = $this->intersect ? clone $this->intersect : null;
        $copy->intersectDistinct = $this->intersectDistinct;
    }

    public function cleanIntersect(): void
    {
        $this->intersect = null;
    }

    protected function buildIntersect(): void
    {
        if ($this->intersect !== null) {
            $this->sql .= ' INTERSECT';
            if ($this->intersectDistinct) {
                $this->sql .= ' DISTINCT';
            }
            $this->sql .= " $this->intersect";
            $this->addParams($this->intersect->getParams());
        }
    }
}
