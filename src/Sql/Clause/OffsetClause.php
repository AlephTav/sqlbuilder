<?php

declare(strict_types=1);

namespace AlephTools\SqlBuilder\Sql\Clause;

trait OffsetClause
{
    protected ?int $offset = null;

    /**
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
