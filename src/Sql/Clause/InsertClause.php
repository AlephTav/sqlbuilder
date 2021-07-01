<?php

namespace AlephTools\SqlBuilder\Sql\Clause;

use AlephTools\SqlBuilder\Sql\Expression\FromExpression;

trait InsertClause
{
    /**
     * @var FromExpression
     */
    protected $table;

    /**
     * @param mixed $table
     * @param mixed $alias
     * @return static
     */
    public function into($table, $alias = null)
    {
        $this->table = $this->table ?? $this->createTableExpression();
        $this->table->append($table, $alias);
        $this->built = false;
        return $this;
    }

    protected function createTableExpression()
    {
        return new FromExpression();
    }
}