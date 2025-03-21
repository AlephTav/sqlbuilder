<?php

declare(strict_types=1);

namespace AlephTools\SqlBuilder\Clickhouse\Clause;

use AlephTools\SqlBuilder\Sql\Expression\SelectExpression;

trait ExceptClause
{
    protected SelectExpression|null $except = null;

    public function except(mixed $except): static
    {
        $this->except ??= $this->createSelectExpression();
        $this->except->append($except);
        $this->built = false;
        return $this;
    }

    protected function createExceptSelectExpression(): SelectExpression
    {
        return new SelectExpression();
    }

    protected function cloneExcept(mixed $copy): void
    {
        $copy->except = $this->except ? clone $this->except : null;
    }

    public function cleanExcept(): void
    {
        $this->except = null;
    }

    protected function buildExcept(): void
    {
        if ($this->except !== null) {
            $this->sql .= " EXCEPT ($this->except)";
            $this->addParams($this->except->getParams());
        }
    }
}
