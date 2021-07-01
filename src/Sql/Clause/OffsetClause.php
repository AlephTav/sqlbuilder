<?php

namespace AlephTools\SqlBuilder\Sql\Clause;

trait OffsetClause
{
    protected ?int $offset = null;

    /**
     * @param int|null $offset
     * @return static
     */
    public function offset(?int $offset)
    {
        $this->offset = $offset;
        $this->built = false;
        return $this;
    }

    protected function buildOffset(): void
    {
        if ($this->offset !== null) {
            $this->sql .= " OFFSET $this->offset";
        }
    }
}