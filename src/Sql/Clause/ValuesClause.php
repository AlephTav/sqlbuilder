<?php

declare(strict_types=1);

namespace AlephTools\SqlBuilder\Sql\Clause;

use AlephTools\SqlBuilder\Sql\Expression\ValueListExpression;

trait ValuesClause
{
    protected ?ValueListExpression $values = null;

    public function values(mixed $values): static
    {
        $this->values = $this->values ?? $this->createValuesExpression();
        $this->values->append($values);
        $this->built = false;
        return $this;
    }

    protected function createValuesExpression(): ValueListExpression
    {
        return new ValueListExpression();
    }

    protected function cloneValues(mixed $copy): void
    {
        $copy->values = $this->values ? clone $this->values : null;
    }

    public function cleanValues(): void
    {
        $this->values = null;
    }

    protected function buildValues(): void
    {
        $this->sql .= 'VALUES';
        if ($this->values) {
            $this->sql .= " $this->values";
            $this->addParams($this->values->getParams());
        }
    }
}
