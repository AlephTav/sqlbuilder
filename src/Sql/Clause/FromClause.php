<?php

declare(strict_types=1);

namespace AlephTools\SqlBuilder\Sql\Clause;

use AlephTools\SqlBuilder\Sql\Expression\FromExpression;

trait FromClause
{
    /**
     * @var FromExpression
     */
    protected $from;

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

    protected function buildFrom(): void
    {
        if ($this->from) {
            $this->sql .= " FROM $this->from";
            $this->addParams($this->from->getParams());
        }
    }
}
