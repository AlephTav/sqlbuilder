<?php

namespace AlephTools\SqlBuilder\MySql\Clause;

use AlephTools\SqlBuilder\Query;
use AlephTools\SqlBuilder\Sql\Clause\UnionClause as BaseUnionClause;

trait UnionClause
{
    use BaseUnionClause;

    /**
     * @param Query $query
     * @return static
     */
    public function unionDistinct(Query $query)
    {
        return $this->typeUnion('UNION DISTINCT', $query);
    }
}