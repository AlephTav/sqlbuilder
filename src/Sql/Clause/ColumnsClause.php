<?php

declare(strict_types=1);

namespace AlephTools\SqlBuilder\Sql\Clause;

use AlephTools\SqlBuilder\Sql\Expression\ColumnListExpression;

trait ColumnsClause
{
    protected ?ColumnListExpression $columns = null;

    public function columns(mixed $columns): static
    {
        $this->columns = $this->columns ?? $this->createColumnsExpression();
        $this->columns->append($columns);
        $this->built = false;
        return $this;
    }

    protected function createColumnsExpression(): ColumnListExpression
    {
        return new ColumnListExpression();
    }

    protected function cloneColumns(mixed $copy): void
    {
        $copy->columns = $this->columns ? clone $this->columns : null;
    }

    public function cleanColumns(): void
    {
        $this->columns = null;
    }

    protected function buildColumns(): void
    {
        if ($this->columns) {
            $this->sql .= " ($this->columns)";
            $this->addParams($this->columns->getParams());
        }
    }
}
