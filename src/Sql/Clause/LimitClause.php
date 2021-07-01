<?php

namespace AlephTools\SqlBuilder\Sql\Clause;

trait LimitClause
{
    protected ?int $limit = null;

    /**
     * @param int|null $limit
     * @return static
     */
    public function limit(?int $limit)
    {
        $this->limit = $limit;
        $this->built = false;
        return $this;
    }

    protected function buildLimit(): void
    {
        if ($this->limit !== null) {
            $this->sql .= " LIMIT $this->limit";
        }
    }
}
