<?php

declare(strict_types=1);

namespace AlephTools\SqlBuilder\Sql\Clause;

use AlephTools\SqlBuilder\Sql\Expression\ValueListExpression;

trait ValueListClause
{
    protected ?ValueListExpression $values = null;

    /**
     * @return static
     */
    public function values(mixed $values, mixed $columns = null)
    {
        if ($columns !== null) {
            $this->columns($columns);
        }

        if (is_array($values)) {
            $first = reset($values);
            if ($this->isAssociativeArray($first)) {
                $this->columns(array_keys($first));
            } elseif ($this->isAssociativeArray($values)) {
                $this->columns(array_keys($values));
            }
        }

        $this->values = $this->values ?? $this->createValueListExpression();
        $this->values->append($values);
        $this->built = false;
        return $this;
    }

    private function isAssociativeArray(mixed $items): bool
    {
        return is_array($items) && is_string(key($items));
    }

    protected function createValueListExpression(): ValueListExpression
    {
        return new ValueListExpression();
    }

    protected function cloneValueList(mixed $copy): void
    {
        $copy->values = $this->values ? clone $this->values : null;
    }

    public function cleanValueList(): void
    {
        $this->values = null;
    }
}
