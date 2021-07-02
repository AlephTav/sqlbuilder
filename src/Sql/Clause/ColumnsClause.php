<?php

declare(strict_types=1);

namespace AlephTools\SqlBuilder\Sql\Clause;

use AlephTools\SqlBuilder\Sql\Expression\ColumnListExpression;

trait ColumnsClause
{
    /**
     * @var ColumnListExpression
     */
    protected $columns;

    /**
     * @param mixed $columns
     * @return static
     */
    public function columns($columns)
    {
        $this->columns = $this->columns ?? $this->createColumnsExpression();
        $this->columns->append($columns);
        $this->built = false;
        return $this;
    }

    protected function createColumnsExpression()
    {
        return new ColumnListExpression();
    }

    protected function buildColumns(): void
    {
        if ($this->columns) {
            $this->sql .= " ($this->columns)";
            $this->addParams($this->columns->getParams());
        }
    }
}
