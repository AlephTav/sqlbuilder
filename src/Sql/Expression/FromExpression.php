<?php

declare(strict_types=1);

namespace AlephTools\SqlBuilder\Sql\Expression;

class FromExpression extends AbstractListExpression
{
    public function __construct($table = null, $alias = null)
    {
        parent::__construct(false);
        if ($table !== null) {
            $this->append($table, $alias);
        }
    }

    /**
     * @param mixed $table
     * @param mixed $alias
     * @return static
     */
    public function append($table, $alias = null)
    {
        return $this->appendName($table, $alias);
    }
}
