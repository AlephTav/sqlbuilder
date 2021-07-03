<?php

declare(strict_types=1);

namespace AlephTools\SqlBuilder\Sql\Expression;

class SelectExpression extends AbstractListExpression
{
    /**
     * @param mixed $column
     * @param mixed $alias
     */
    public function __construct($column = null, $alias = null)
    {
        parent::__construct(false);
        if ($column !== null || $alias !== null) {
            $this->append($column, $alias);
        }
    }

    /**
     * @param mixed $column
     * @param mixed $alias
     * @return static
     */
    public function append($column, $alias = null)
    {
        return $this->appendName($column, $alias);
    }
}
