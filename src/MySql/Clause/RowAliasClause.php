<?php

declare(strict_types=1);

namespace AlephTools\SqlBuilder\MySql\Clause;

use AlephTools\SqlBuilder\Sql\Expression\ColumnListExpression;

trait RowAliasClause
{
    protected ?string $rowAlias = null;

    protected ?ColumnListExpression $columnAliases = null;

    public function as(string $rowAlias, mixed $columnAliases = null): static
    {
        $this->rowAlias = $rowAlias;
        if ($columnAliases !== null) {
            $this->columnAliases = $this->columnAliases ?? new ColumnListExpression();
            $this->columnAliases->append($columnAliases);
        }
        $this->built = false;
        return $this;
    }

    protected function cloneRowAlias(mixed $copy): void
    {
        $copy->columnAliases = $this->columnAliases ? clone $this->columnAliases : null;
        $copy->rowAlias = $this->rowAlias;
    }

    public function cleanRowAlias(): void
    {
        $this->columnAliases = null;
        $this->rowAlias = null;
    }

    protected function buildRowAndColumnAliases(): void
    {
        if ($this->rowAlias !== null && $this->rowAlias !== '') {
            $this->sql .= "AS $this->rowAlias";
            if ($this->columnAliases) {
                $this->sql .= " ($this->columnAliases)";
                $this->addParams($this->columnAliases->getParams());
            }
        }
    }
}
