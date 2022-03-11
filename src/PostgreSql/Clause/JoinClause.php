<?php

declare(strict_types=1);

namespace AlephTools\SqlBuilder\PostgreSql\Clause;

use AlephTools\SqlBuilder\Sql\Clause\JoinClause as BaseJoinClause;

trait JoinClause
{
    use BaseJoinClause;

    public function fullJoin(mixed $table, mixed $aliasOrCondition = null, mixed $condition = null): static
    {
        return $this->typeJoin('FULL JOIN', $table, $aliasOrCondition, $condition);
    }

    public function fullOuterJoin(mixed $table, mixed $aliasOrCondition = null, mixed $condition = null): static
    {
        return $this->typeJoin('FULL OUTER JOIN', $table, $aliasOrCondition, $condition);
    }

    public function naturalFullJoin(mixed $table, mixed $aliasOrCondition = null, mixed $condition = null): static
    {
        return $this->typeJoin('NATURAL FULL JOIN', $table, $aliasOrCondition, $condition);
    }

    public function naturalFullOuterJoin(mixed $table, mixed $aliasOrCondition = null, mixed $condition = null): static
    {
        return $this->typeJoin('NATURAL FULL OUTER JOIN', $table, $aliasOrCondition, $condition);
    }
}
