<?php

declare(strict_types=1);

namespace AlephTools\SqlBuilder\Sql\Clause;

use AlephTools\SqlBuilder\Sql\Expression\JoinExpression;

trait JoinClause
{
    protected ?JoinExpression $join = null;

    /**
     * @return static
     */
    public function join(mixed $table, mixed $aliasOrCondition = null, mixed $condition = null)
    {
        return $this->typeJoin('JOIN', $table, $aliasOrCondition, $condition);
    }

    /**
     * @return static
     */
    public function crossJoin(mixed $table, mixed $aliasOrCondition = null, mixed $condition = null)
    {
        return $this->typeJoin('CROSS JOIN', $table, $aliasOrCondition, $condition);
    }

    /**
     * @return static
     */
    public function innerJoin(mixed $table, mixed $aliasOrCondition = null, mixed $condition = null)
    {
        return $this->typeJoin('INNER JOIN', $table, $aliasOrCondition, $condition);
    }

    /**
     * @return static
     */
    public function leftJoin(mixed $table, mixed $aliasOrCondition = null, mixed $condition = null)
    {
        return $this->typeJoin('LEFT JOIN', $table, $aliasOrCondition, $condition);
    }

    /**
     * @return static
     */
    public function rightJoin(mixed $table, mixed $aliasOrCondition = null, mixed $condition = null)
    {
        return $this->typeJoin('RIGHT JOIN', $table, $aliasOrCondition, $condition);
    }

    /**
     * @return static
     */
    public function naturalInnerJoin(mixed $table, mixed $aliasOrCondition = null, mixed $condition = null)
    {
        return $this->typeJoin('NATURAL INNER JOIN', $table, $aliasOrCondition, $condition);
    }

    /**
     * @return static
     */
    public function naturalLeftJoin(mixed $table, mixed $aliasOrCondition = null, mixed $condition = null)
    {
        return $this->typeJoin('NATURAL LEFT JOIN', $table, $aliasOrCondition, $condition);
    }

    /**
     * @return static
     */
    public function naturalRightJoin(mixed $table, mixed $aliasOrCondition = null, mixed $condition = null)
    {
        return $this->typeJoin('NATURAL RIGHT JOIN', $table, $aliasOrCondition, $condition);
    }

    /**
     * @return static
     */
    public function leftOuterJoin(mixed $table, mixed $aliasOrCondition = null, mixed $condition = null)
    {
        return $this->typeJoin('LEFT OUTER JOIN', $table, $aliasOrCondition, $condition);
    }

    /**
     * @return static
     */
    public function rightOuterJoin(mixed $table, mixed $aliasOrCondition = null, mixed $condition = null)
    {
        return $this->typeJoin('RIGHT OUTER JOIN', $table, $aliasOrCondition, $condition);
    }

    /**
     * @return static
     */
    public function naturalLeftOuterJoin(mixed $table, mixed $aliasOrCondition = null, mixed $condition = null)
    {
        return $this->typeJoin('NATURAL LEFT OUTER JOIN', $table, $aliasOrCondition, $condition);
    }

    /**
     * @return static
     */
    public function naturalRightOuterJoin(mixed $table, mixed $aliasOrCondition = null, mixed $condition = null)
    {
        return $this->typeJoin('NATURAL RIGHT OUTER JOIN', $table, $aliasOrCondition, $condition);
    }

    /**
     * @return static
     */
    protected function typeJoin(
        string $type,
        mixed $table,
        mixed $aliasOrCondition = null,
        mixed $condition = null
    ) {
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

    protected function createJoinExpression(): JoinExpression
    {
        return new JoinExpression();
    }

    protected function cloneJoin(mixed $copy): void
    {
        $copy->join = $this->join ? clone $this->join : null;
    }

    protected function cleanJoin(): void
    {
        $this->join = null;
    }

    protected function buildJoin(): void
    {
        if ($this->join !== null) {
            $this->sql .= " $this->join";
            $this->addParams($this->join->getParams());
        }
    }
}
