<?php

namespace AlephTools\SqlBuilder\Sql\Clause;

use AlephTools\SqlBuilder\Sql\Expression\SelectExpression;

trait SelectClause
{
    /**
     * @var SelectExpression
     */
    protected $select;

    /**
     * @param mixed $column
     * @param mixed $alias
     * @return static
     */
    public function select($column, $alias = null)
    {
        $this->select = $this->select ?? $this->createSelectExpression();
        $this->select->append($column, $alias);
        $this->built = false;
        return $this;
    }

    protected function createSelectExpression()
    {
        return new SelectExpression();
    }

    protected function buildSelect(): void
    {
        if ($this->select !== null) {
            $this->sql .= "SELECT $this->select";
            $this->addParams($this->select->getParams());
        } else {
            $this->sql .= 'SELECT *';
        }
    }
}