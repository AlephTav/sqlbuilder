<?php

namespace AlephTools\SqlBuilder\Sql\Clause;

use AlephTools\SqlBuilder\Sql\Expression\ValueListExpression;

trait ValueListClause
{
    /**
     * @var ValueListExpression
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
            } else if ($this->isAssociativeArray($values)) {
                $this->columns(array_keys($values));
            }
        }

        $this->values = $this->values ?? $this->createValueListExpression();
        $this->values->append($values);
        $this->built = false;
        return $this;
    }

    private function isAssociativeArray($items): bool
    {
        return is_array($items) && is_string(key($items));
    }

    protected function createValueListExpression()
    {
        return new ValueListExpression();
    }
}