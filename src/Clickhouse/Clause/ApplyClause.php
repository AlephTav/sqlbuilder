<?php

declare(strict_types=1);

namespace AlephTools\SqlBuilder\Clickhouse\Clause;

use AlephTools\SqlBuilder\Sql\Expression\ValueListExpression;

trait ApplyClause
{
    protected array $applies = [];

    public function apply(mixed $value): static
    {
        $this->applies[] = (new ValueListExpression())->append($value);
        $this->built = false;
        return $this;
    }

    protected function cloneApply(mixed $copy): void
    {
        foreach ($this->applies as $apply) {
            $copy->apply(clone $apply);
        }
    }

    public function cleanApply(): void
    {
        $this->applies = [];
    }

    protected function buildApply(): void
    {
        foreach ($this->applies as $apply) {
            $this->sql .= " APPLY($apply)";
        }
    }
}
