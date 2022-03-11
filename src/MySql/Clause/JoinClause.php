<?php

declare(strict_types=1);

namespace AlephTools\SqlBuilder\MySql\Clause;

use AlephTools\SqlBuilder\Sql\Clause\JoinClause as BaseJoinClause;

trait JoinClause
{
    use BaseJoinClause;

    public function straightJoin(mixed $table, mixed $aliasOrCondition = null, mixed $condition = null): static
    {
        return $this->typeJoin('STRAIGHT_JOIN', $table, $aliasOrCondition, $condition);
    }
}
