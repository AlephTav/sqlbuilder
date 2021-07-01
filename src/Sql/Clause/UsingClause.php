<?php

namespace AlephTools\SqlBuilder\Sql\Clause;

use AlephTools\SqlBuilder\Sql\Expression\FromExpression;

trait UsingClause
{
    /**
     * @var FromExpression
     */
    protected $using;

    /**
     * @param mixed $table
     * @param mixed $alias
     * @return static
     */
    public function using($table, $alias = null)
    {
        $this->using = $this->using ?? $this->createUsingExpression();
        $this->using->append($table, $alias);
        $this->built = false;
        return $this;
    }

    protected function createUsingExpression()
    {
        return new FromExpression();
    }

    protected function buildUsing(): void
    {
        if ($this->using) {
            $this->sql .= " USING $this->using";
            $this->addParams($this->using->getParams());
        }
    }
}