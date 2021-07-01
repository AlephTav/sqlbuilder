<?php

declare(strict_types=1);

namespace AlephTools\SqlBuilder\MySql\Clause;

use AlephTools\SqlBuilder\Sql\Expression\ColumnListExpression;

trait PartitionClause
{
    /**
     * @var ColumnListExpression
     */
    protected $partition;

    /**
     * @param mixed $partition
     * @return static
     */
    public function partition($partition)
    {
        $this->partition = $this->partition ?? new ColumnListExpression();
        $this->partition->append($partition);
        $this->built = false;
        return $this;
    }

    protected function buildPartition(): void
    {
        if ($this->partition) {
            $this->sql .= " PARTITION ($this->partition)";
            $this->addParams($this->partition->getParams());
        }
    }
}
