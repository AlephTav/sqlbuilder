<?php

namespace AlephTools\SqlBuilder\MySql\Clause;

use AlephTools\SqlBuilder\Sql\Expression\ListExpression;

trait PartitionClause
{
    /**
     * @var ListExpression
     */
    protected $partition;

    /**
     * @param mixed $partition
     * @return static
     */
    public function partition($partition)
    {
        $this->partition = $this->partition ?? new ListExpression();
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