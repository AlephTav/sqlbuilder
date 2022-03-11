<?php

declare(strict_types=1);

namespace AlephTools\SqlBuilder\MySql\Clause;

use AlephTools\SqlBuilder\Query;
use AlephTools\SqlBuilder\Sql\Clause\UnionClause as BaseUnionClause;

trait UnionClause
{
    use BaseUnionClause;

    public function unionDistinct(Query $query): static
    {
        return $this->typeUnion('UNION DISTINCT', $query);
    }
}
