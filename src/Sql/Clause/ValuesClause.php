<?php

declare(strict_types=1);

namespace AlephTools\SqlBuilder\Sql\Clause;

use AlephTools\SqlBuilder\Sql\Expression\ValueListExpression;

trait ValuesClause
{
    protected ?ValueListExpression $values = null;

    /**
     * @param mixed $values
     * @return static
     */
    public function values($values)
    {
        $this->values = $this->values ?? $this->createValuesExpression();
        $this->values->append($values);
        $this->built = false;
        return $this;
    }

    protected function createValuesExpression()
    {
        return new ValueListExpression();
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
