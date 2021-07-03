<?php

declare(strict_types=1);

namespace AlephTools\SqlBuilder\Sql\Clause;

use AlephTools\SqlBuilder\Sql\Expression\ValueListExpression;

trait ValueListClause
{
    /**
     * @var ValueListExpression|null
     */
    protected $values;

    /**
     * @param mixed $values
     * @param mixed $columns
     * @return static
     */
    public function values($values, $columns = null)
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

    /**
     * @param mixed $items
     */
    private function isAssociativeArray($items): bool
    {
        return is_array($items) && is_string(key($items));
    }

    /**
     * @return ValueListExpression
     */
    protected function createValueListExpression()
    {
        return new ValueListExpression();
    }
}
