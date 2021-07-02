<?php

declare(strict_types=1);

namespace AlephTools\SqlBuilder\Sql\Clause;

use AlephTools\SqlBuilder\Sql\Expression\WithExpression;

trait WithClause
{
    /**
     * @var WithExpression
     */
    protected $with;

    /**
     * @param mixed $query
     * @param mixed $alias
     * @return $this
     */
    public function with($query, $alias = null)
    {
        $this->with = $this->with ?? $this->createWithExpression();
        $this->with->append($query, $alias);
        $this->built = false;
        return $this;
    }

    /**
     * @param mixed $query
     * @param mixed $alias
     * @return $this
     */
    public function withRecursive($query, $alias = null)
    {
        $this->with = $this->with ?? $this->createWithExpression();
        $this->with->append($query, $alias, true);
        $this->built = false;
        return $this;
    }

    protected function createWithExpression()
    {
        return new WithExpression();
    }

    protected function buildWith(): void
    {
        if ($this->with) {
            $this->sql .= "WITH $this->with ";
            $this->addParams($this->with->getParams());
        }
    }
}
