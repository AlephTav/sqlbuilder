<?php

declare(strict_types=1);

namespace AlephTools\SqlBuilder\PostgreSql\Clause;

use AlephTools\SqlBuilder\Query;
use AlephTools\SqlBuilder\Sql\Clause\UnionClause as BaseUnionClause;

trait UnionClause
{
    use BaseUnionClause;

    public function unionIntersect(Query $query): static
    {
        return $this->typeUnion('INTERSECT', $query);
    }

    public function unionIntersectAll(Query $query): static
    {
        return $this->typeUnion('INTERSECT ALL', $query);
    }

    public function unionExcept(Query $query): static
    {
        return $this->typeUnion('EXCEPT', $query);
    }

    public function unionExceptAll(Query $query): static
    {
        return $this->typeUnion('EXCEPT ALL', $query);
    }
}
