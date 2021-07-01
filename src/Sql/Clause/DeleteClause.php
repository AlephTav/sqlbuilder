<?php

declare(strict_types=1);

namespace AlephTools\SqlBuilder\Sql\Clause;

use AlephTools\SqlBuilder\Sql\Expression\FromExpression;

trait DeleteClause
{
    protected ?FromExpression $from = null;

    /**
     * @param mixed $table
     * @param mixed $alias
     * @return static
     */
    public function from($table, $alias = null)
    {
        $this->from = $this->from ?? $this->createFromExpression();
        $this->from->append($table, $alias);
        $this->built = false;
        return $this;
    }

    protected function createFromExpression()
    {
        return new FromExpression();
    }
}
