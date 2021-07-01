<?php

declare(strict_types=1);

namespace AlephTools\SqlBuilder\PostgreSql\Clause;

use AlephTools\SqlBuilder\Query;
use AlephTools\SqlBuilder\Sql\Clause\UnionClause as BaseUnionClause;

trait UnionClause
{
    use BaseUnionClause;

    /**
     * @return static
     */
    public function unionIntersect(Query $query)
    {
        return $this->typeUnion('INTERSECT', $query);
    }

    /**
     * @return static
     */
    public function unionIntersectAll(Query $query)
    {
        return $this->typeUnion('INTERSECT ALL', $query);
    }

    /**
     * @return static
     */
    public function unionExcept(Query $query)
    {
        return $this->typeUnion('EXCEPT', $query);
    }

    /**
     * @return static
     */
    public function unionExceptAll(Query $query)
    {
        return $this->typeUnion('EXCEPT ALL', $query);
    }
}
