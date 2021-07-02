<?php

declare(strict_types=1);

namespace AlephTools\SqlBuilder\MySql\Clause;

use AlephTools\SqlBuilder\Sql\Expression\ColumnListExpression;

trait RowAliasClause
{
    protected ?string $rowAlias = null;

    /**
     * @var ColumnListExpression|null
     */
    protected $columnAliases;

    /**
     * @param mixed $columnAliases
     * @return static
     */
    public function as(string $rowAlias, $columnAliases = null)
    {
        $this->rowAlias = $rowAlias;
        if ($columnAliases !== null) {
            $this->columnAliases = $this->columnAliases ?? new ColumnListExpression();
            $this->columnAliases->append($columnAliases);
        }
        $this->built = false;
        return $this;
    }

    protected function buildRowAndColumnAliases(): void
    {
        if (strlen((string)$this->rowAlias)) {
            $this->sql .= "AS $this->rowAlias";
            if ($this->columnAliases) {
                $this->sql .= " ($this->columnAliases)";
                $this->addParams($this->columnAliases->getParams());
            }
        }
    }
}
