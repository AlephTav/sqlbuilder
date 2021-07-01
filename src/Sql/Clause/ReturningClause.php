<?php

declare(strict_types=1);

namespace AlephTools\SqlBuilder\Sql\Clause;

use AlephTools\SqlBuilder\Sql\Expression\ReturningExpression;

trait ReturningClause
{
    /**
     * @var ReturningExpression
     */
    protected $returning;

    /**
     * @param mixed $column
     * @param mixed $alias
     * @return static
     */
    public function returning($column = null, $alias = null)
    {
        $this->returning = $this->returning ?? $this->createReturningExpression();
        $this->returning->append($column ?? '*', $alias);
        $this->built = false;
        return $this;
    }

    protected function createReturningExpression()
    {
        return new ReturningExpression();
    }

    protected function buildReturning(): void
    {
        if ($this->returning) {
            $this->sql .= " RETURNING $this->returning";
            $this->addParams($this->returning->getParams());
        }
    }
}
