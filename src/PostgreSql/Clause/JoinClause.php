<?php

namespace AlephTools\SqlBuilder\PostgreSql\Clause;

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
    public function fullJoin($table, $aliasOrCondition = null, $condition = null)
    {
        return $this->typeJoin('FULL JOIN', $table, $aliasOrCondition, $condition);
    }

    /**
     * @param mixed $table
     * @param mixed $aliasOrCondition
     * @param mixed $condition
     * @return static
     */
    public function fullOuterJoin($table, $aliasOrCondition = null, $condition = null)
    {
        return $this->typeJoin('FULL OUTER JOIN', $table, $aliasOrCondition, $condition);
    }

    /**
     * @param mixed $table
     * @param mixed $aliasOrCondition
     * @param mixed $condition
     * @return static
     */
    public function naturalFullJoin($table, $aliasOrCondition = null, $condition = null)
    {
        return $this->typeJoin('NATURAL FULL JOIN', $table, $aliasOrCondition, $condition);
    }

    /**
     * @param mixed $table
     * @param mixed $aliasOrCondition
     * @param mixed $condition
     * @return static
     */
    public function naturalFullOuterJoin($table, $aliasOrCondition = null, $condition = null)
    {
        return $this->typeJoin('NATURAL FULL OUTER JOIN', $table, $aliasOrCondition, $condition);
    }
}