<?php

declare(strict_types=1);

namespace AlephTools\SqlBuilder\Sql\Clause;

use AlephTools\SqlBuilder\Sql\Expression\GroupExpression;

trait GroupClause
{
    /**
     * @var GroupExpression
     */
    protected $group;

    /**
     * @param mixed $column
     * @param mixed $order
     * @return static
     */
    public function groupBy($column, $order = null)
    {
        $this->group = $this->group ?? $this->createGroupExpression();
        $this->group->append($column, $order);
        $this->built = false;
        return $this;
    }

    protected function createGroupExpression()
    {
        return new GroupExpression();
    }

    protected function buildGroupBy(): void
    {
        if ($this->group) {
            $this->sql .= " GROUP BY $this->group";
            $this->addParams($this->group->getParams());
        }
    }
}
