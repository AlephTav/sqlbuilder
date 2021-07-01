<?php

namespace AlephTools\SqlBuilder\Sql\Clause;

use AlephTools\SqlBuilder\Sql\Expression\HavingExpression;

trait HavingClause
{
    /**
     * @var HavingExpression
     */
    protected $having;

    /**
     * @param mixed $column
     * @param mixed $operator
     * @param mixed $value
     * @return static
     */
    public function andHaving($column, $operator = null, $value = null)
    {
        return $this->having($column, $operator, $value);
    }

    /**
     * @param mixed $column
     * @param mixed $operator
     * @param mixed $value
     * @return static
     */
    public function orHaving($column, $operator = null, $value = null)
    {
        return $this->having($column, $operator, $value, 'OR');
    }

    /**
     * @param mixed $column
     * @param mixed $operator
     * @param mixed $value
     * @param string $connector
     * @return static
     */
    public function having($column, $operator = null, $value = null, string $connector = 'AND')
    {
        $this->having = $this->having ?? $this->createHavingExpression();
        $this->having->with($column, $operator, $value, $connector);
        $this->built = false;
        return $this;
    }

    protected function createHavingExpression()
    {
        return new HavingExpression();
    }

    protected function buildHaving(): void
    {
        if ($this->having) {
            $this->sql .= " HAVING $this->having";
            $this->addParams($this->having->getParams());
        }
    }
}