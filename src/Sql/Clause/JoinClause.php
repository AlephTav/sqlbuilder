<?php

declare(strict_types=1);

namespace AlephTools\SqlBuilder\Sql\Clause;

use AlephTools\SqlBuilder\Sql\Expression\JoinExpression;

trait JoinClause
{
    /**
     * @var JoinExpression|null
     */
    protected $join;

    /**
     * @param mixed $table
     * @param mixed $aliasOrCondition
     * @param mixed $condition
     * @return static
     */
    public function join($table, $aliasOrCondition = null, $condition = null)
    {
        return $this->typeJoin('JOIN', $table, $aliasOrCondition, $condition);
    }

    /**
     * @param mixed $table
     * @param mixed $aliasOrCondition
     * @param mixed $condition
     * @return static
     */
    public function crossJoin($table, $aliasOrCondition = null, $condition = null)
    {
        return $this->typeJoin('CROSS JOIN', $table, $aliasOrCondition, $condition);
    }

    /**
     * @param mixed $table
     * @param mixed $aliasOrCondition
     * @param mixed $condition
     * @return static
     */
    public function innerJoin($table, $aliasOrCondition = null, $condition = null)
    {
        return $this->typeJoin('INNER JOIN', $table, $aliasOrCondition, $condition);
    }

    /**
     * @param mixed $table
     * @param mixed $aliasOrCondition
     * @param mixed $condition
     * @return static
     */
    public function leftJoin($table, $aliasOrCondition = null, $condition = null)
    {
        return $this->typeJoin('LEFT JOIN', $table, $aliasOrCondition, $condition);
    }

    /**
     * @param mixed $table
     * @param mixed $aliasOrCondition
     * @param mixed $condition
     * @return static
     */
    public function rightJoin($table, $aliasOrCondition = null, $condition = null)
    {
        return $this->typeJoin('RIGHT JOIN', $table, $aliasOrCondition, $condition);
    }

    /**
     * @param mixed $table
     * @param mixed $aliasOrCondition
     * @param mixed $condition
     * @return static
     */
    public function naturalInnerJoin($table, $aliasOrCondition = null, $condition = null)
    {
        return $this->typeJoin('NATURAL INNER JOIN', $table, $aliasOrCondition, $condition);
    }

    /**
     * @param mixed $table
     * @param mixed $aliasOrCondition
     * @param mixed $condition
     * @return static
     */
    public function naturalLeftJoin($table, $aliasOrCondition = null, $condition = null)
    {
        return $this->typeJoin('NATURAL LEFT JOIN', $table, $aliasOrCondition, $condition);
    }

    /**
     * @param mixed $table
     * @param mixed $aliasOrCondition
     * @param mixed $condition
     * @return static
     */
    public function naturalRightJoin($table, $aliasOrCondition = null, $condition = null)
    {
        return $this->typeJoin('NATURAL RIGHT JOIN', $table, $aliasOrCondition, $condition);
    }

    /**
     * @param mixed $table
     * @param mixed $aliasOrCondition
     * @param mixed $condition
     * @return static
     */
    public function leftOuterJoin($table, $aliasOrCondition = null, $condition = null)
    {
        return $this->typeJoin('LEFT OUTER JOIN', $table, $aliasOrCondition, $condition);
    }

    /**
     * @param mixed $table
     * @param mixed $aliasOrCondition
     * @param mixed $condition
     * @return static
     */
    public function rightOuterJoin($table, $aliasOrCondition = null, $condition = null)
    {
        return $this->typeJoin('RIGHT OUTER JOIN', $table, $aliasOrCondition, $condition);
    }

    /**
     * @param mixed $table
     * @param mixed $aliasOrCondition
     * @param mixed $condition
     * @return static
     */
    public function naturalLeftOuterJoin($table, $aliasOrCondition = null, $condition = null)
    {
        return $this->typeJoin('NATURAL LEFT OUTER JOIN', $table, $aliasOrCondition, $condition);
    }

    /**
     * @param mixed $table
     * @param mixed $aliasOrCondition
     * @param mixed $condition
     * @return static
     */
    public function naturalRightOuterJoin($table, $aliasOrCondition = null, $condition = null)
    {
        return $this->typeJoin('NATURAL RIGHT OUTER JOIN', $table, $aliasOrCondition, $condition);
    }

    /**
     * @param mixed $table
     * @param mixed $aliasOrCondition
     * @param mixed $condition
     * @return static
     */
    protected function typeJoin(string $type, $table, $aliasOrCondition = null, $condition = null)
    {
        if ($condition !== null) {
            $alias = $aliasOrCondition;
        } else {
            $alias = null;
            $condition = $aliasOrCondition;
        }
        $this->join = $this->join ?? $this->createJoinExpression();
        $this->join->append($type, $table, $alias, $condition);
        $this->built = false;
        return $this;
    }

    /**
     * @return JoinExpression
     */
    protected function createJoinExpression()
    {
        return new JoinExpression();
    }

    protected function buildJoin(): void
    {
        if ($this->join !== null) {
            $this->sql .= " $this->join";
            $this->addParams($this->join->getParams());
        }
    }
}
