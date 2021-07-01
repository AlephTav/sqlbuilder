<?php

namespace AlephTools\SqlBuilder\PostgreSql\Clause;

use AlephTools\SqlBuilder\Query;
use AlephTools\SqlBuilder\Sql\Clause\UnionClause as BaseUnionClause;

trait UnionClause
{
    use BaseUnionClause;

    /**
     * @param Query $query
     * @return static
     */
    public function unionIntersect(Query $query)
    {
        return $this->typeUnion('INTERSECT', $query);
    }

    /**
     * @param Query $query
     * @return static
     */
    public function unionIntersectAll(Query $query)
    {
        return $this->typeUnion('INTERSECT ALL', $query);
    }

    /**
     * @param Query $query
     * @return static
     */
    public function unionExcept(Query $query)
    {
        return $this->typeUnion('EXCEPT', $query);
    }

    /**
     * @param Query $query
     * @return static
     */
    public function unionExceptAll(Query $query)
    {
        return $this->typeUnion('EXCEPT ALL', $query);
    }
}