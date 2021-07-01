<?php

declare(strict_types=1);

namespace AlephTools\SqlBuilder\Sql\Clause;

use AlephTools\SqlBuilder\Sql\Expression\FromExpression;

trait UpdateClause
{
    protected ?FromExpression $table = null;

    /**
     * @param mixed $table
     * @param mixed $alias
     * @return static
     */
    public function table($table, $alias = null)
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
