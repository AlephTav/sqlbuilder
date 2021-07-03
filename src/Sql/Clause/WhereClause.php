<?php

declare(strict_types=1);

namespace AlephTools\SqlBuilder\Sql\Clause;

use AlephTools\SqlBuilder\Sql\Expression\WhereExpression;

trait WhereClause
{
    /**
     * @var WhereExpression|null
     */
    protected $where;

    /**
     * @param mixed $column
     * @param mixed $operator
     * @param mixed $value
     * @return static
     */
    public function andWhere($column, $operator = null, $value = null)
    {
        return $this->where($column, $operator, $value);
    }

    /**
     * @param mixed $column
     * @param mixed $operator
     * @param mixed $value
     * @return static
     */
    public function orWhere($column, $operator = null, $value = null)
    {
        return $this->where($column, $operator, $value, 'OR');
    }

    /**
     * @param mixed $column
     * @param mixed $operator
     * @param mixed $value
     * @return static
     */
    public function where($column, $operator = null, $value = null, string $connector = 'AND')
    {
        $this->where = $this->where ?? $this->createWhereExpression();
        $this->where->with($column, $operator, $value, $connector);
        $this->built = false;
        return $this;
    }

    /**
     * @return WhereExpression
     */
    protected function createWhereExpression()
    {
        return new WhereExpression();
    }

    protected function buildWhere(): void
    {
        if ($this->where) {
            $this->sql .= " WHERE $this->where";
            $this->addParams($this->where->getParams());
        }
    }
}
