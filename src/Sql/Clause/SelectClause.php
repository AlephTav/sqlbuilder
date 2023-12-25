<?php

declare(strict_types=1);

namespace AlephTools\SqlBuilder\Sql\Clause;

use AlephTools\SqlBuilder\Sql\Expression\SelectExpression;

trait SelectClause
{
    protected ?SelectExpression $select = null;

    public function select(mixed $column, mixed $alias = null): static
    {
        $this->select = $this->select ?? $this->createSelectExpression();
        $this->select->append($column, $alias);
        $this->built = false;
        return $this;
    }

    protected function createSelectExpression(): SelectExpression
    {
        return new SelectExpression();
    }

    protected function cloneSelect(mixed $copy): void
    {
        $copy->select = $this->select ? clone $this->select : null;
    }

    public function cleanSelect(): void
    {
        $this->select = null;
    }

    protected function buildSelect(): void
    {
        if ($this->select !== null) {
            $this->sql .= "SELECT $this->select";
            $this->addParams($this->select->getParams());
        } else {
            $this->sql .= 'SELECT *';
        }
    }
}
