<?php

declare(strict_types=1);

namespace AlephTools\SqlBuilder\Clickhouse\Clause;

trait JoinClause
{
    use \AlephTools\SqlBuilder\Sql\Clause\JoinClause;

    public function fullJoin(mixed $table, mixed $aliasOrCondition = null, mixed $condition = null): static
    {
        return $this->typeJoin('FULL JOIN', $table, $aliasOrCondition, $condition);
    }

    public function fullOuterJoin(mixed $table, mixed $aliasOrCondition = null, mixed $condition = null): static
    {
        return $this->typeJoin('FULL OUTER JOIN', $table, $aliasOrCondition, $condition);
    }

    public function leftSemiJoin(mixed $table, mixed $aliasOrCondition = null, mixed $condition = null): static
    {
        return $this->typeJoin('LEFT SEMI JOIN', $table, $aliasOrCondition, $condition);
    }

    public function rightSemiJoin(mixed $table, mixed $aliasOrCondition = null, mixed $condition = null): static
    {
        return $this->typeJoin('RIGHT SEMI JOIN', $table, $aliasOrCondition, $condition);
    }

    public function leftAntiJoin(mixed $table, mixed $aliasOrCondition = null, mixed $condition = null): static
    {
        return $this->typeJoin('LEFT ANTI JOIN', $table, $aliasOrCondition, $condition);
    }

    public function leftRightJoin(mixed $table, mixed $aliasOrCondition = null, mixed $condition = null): static
    {
        return $this->typeJoin('LEFT RIGHT JOIN', $table, $aliasOrCondition, $condition);
    }

    public function leftAnyJoin(mixed $table, mixed $aliasOrCondition = null, mixed $condition = null): static
    {
        return $this->typeJoin('LEFT ANY JOIN', $table, $aliasOrCondition, $condition);
    }

    public function rightAnyJoin(mixed $table, mixed $aliasOrCondition = null, mixed $condition = null): static
    {
        return $this->typeJoin('RIGHT ANY JOIN', $table, $aliasOrCondition, $condition);
    }

    public function innerAnyJoin(mixed $table, mixed $aliasOrCondition = null, mixed $condition = null): static
    {
        return $this->typeJoin('INNER ANY JOIN', $table, $aliasOrCondition, $condition);
    }

    public function asofJoin(mixed $table, mixed $aliasOrCondition = null, mixed $condition = null): static
    {
        return $this->typeJoin('ASOF JOIN', $table, $aliasOrCondition, $condition);
    }

    public function leftAsofJoin(mixed $table, mixed $aliasOrCondition = null, mixed $condition = null): static
    {
        return $this->typeJoin('LEFT ASOF JOIN', $table, $aliasOrCondition, $condition);
    }

    public function pastJoin(mixed $table, mixed $aliasOrCondition = null, mixed $condition = null): static
    {
        return $this->typeJoin('PAST JOIN', $table, $aliasOrCondition, $condition);
    }
}
