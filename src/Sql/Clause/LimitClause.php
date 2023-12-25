<?php

declare(strict_types=1);

namespace AlephTools\SqlBuilder\Sql\Clause;

trait LimitClause
{
    protected ?int $limit = null;

    /**
     * @return static
     */
    public function limit(?int $limit)
    {
        $this->limit = $limit;
        $this->built = false;
        return $this;
    }

    protected function cloneLimit(mixed $copy): void
    {
        $copy->limit = $this->limit;
    }

    public function cleanLimit(): void
    {
        $this->limit = null;
    }

    protected function buildLimit(): void
    {
        if ($this->limit !== null) {
            $this->sql .= " LIMIT $this->limit";
        }
    }
}
