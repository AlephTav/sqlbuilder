<?php

declare(strict_types=1);

namespace AlephTools\SqlBuilder\Sql\Clause;

trait OffsetClause
{
    protected ?int $offset = null;

    public function offset(?int $offset): static
    {
        $this->offset = $offset;
        $this->built = false;
        return $this;
    }

    protected function cloneOffset(mixed $copy): void
    {
        $copy->offset = $this->offset;
    }

    protected function cleanOffset(): void
    {
        $this->offset = null;
    }

    protected function buildOffset(): void
    {
        if ($this->offset !== null) {
            $this->sql .= " OFFSET $this->offset";
        }
    }
}
