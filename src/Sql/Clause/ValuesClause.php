<?php

declare(strict_types=1);

namespace AlephTools\SqlBuilder\Sql\Clause;

use AlephTools\SqlBuilder\Sql\Expression\ValueListExpression;

trait ValuesClause
{
    /**
     * @var ValueListExpression|null
     */
    protected $values;

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

    /**
     * @return ValueListExpression
     */
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
