<?php

namespace AlephTools\SqlBuilder\MySql\Clause;

use AlephTools\SqlBuilder\Sql\Clause\JoinClause as BaseJoinClause;

trait JoinClause
{
    use BaseJoinClause;

    /**
     * @param mixed $table
     * @param mixed $aliasOrCondition
     * @param mixed $condition
     * @return static
     */
    public function straightJoin($table, $aliasOrCondition = null, $condition = null)
    {
        return $this->typeJoin('STRAIGHT_JOIN', $table, $aliasOrCondition, $condition);
    }
}