<?php

namespace AlephTools\SqlBuilder\Sql\Expression;

class FromExpression extends ListExpression
{
    public function __construct($table = null, $alias = null)
    {
        parent::__construct($table, $alias);
    }

    /**
     * @param mixed $table
     * @param mixed $alias
     * @return static
     */
    public function append($table, $alias = null)
    {
        return parent::append($table, $alias);
    }
}