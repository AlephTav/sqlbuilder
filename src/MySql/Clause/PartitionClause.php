<?php

declare(strict_types=1);

namespace AlephTools\SqlBuilder\MySql\Clause;

use AlephTools\SqlBuilder\Sql\Expression\ColumnListExpression;

trait PartitionClause
{
    protected ?ColumnListExpression $partition = null;

    public function partition(mixed $partition): static
    {
        $this->partition = $this->partition ?? new ColumnListExpression();
        $this->partition->append($partition);
        $this->built = false;
        return $this;
    }

    protected function clonePartition(mixed $copy): void
    {
        $copy->partition = $this->partition ? clone $this->partition : null;
    }

    protected function cleanPartition(): void
    {
        $this->partition = null;
    }

    protected function buildPartition(): void
    {
        if ($this->partition) {
            $this->sql .= " PARTITION ($this->partition)";
            $this->addParams($this->partition->getParams());
        }
    }
}
