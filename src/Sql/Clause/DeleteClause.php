<?php

declare(strict_types=1);

namespace AlephTools\SqlBuilder\Sql\Clause;

use AlephTools\SqlBuilder\Sql\Expression\FromExpression;

trait DeleteClause
{
    protected ?FromExpression $from = null;

    /**
     * @return static
     */
    public function from(mixed $table, mixed $alias = null)
    {
        $this->from = $this->from ?? $this->createFromExpression();
        $this->from->append($table, $alias);
        $this->built = false;
        return $this;
    }

    protected function createFromExpression(): FromExpression
    {
        return new FromExpression();
    }

    protected function cloneDelete(mixed $copy): void
    {
        $copy->from = $this->from ? clone $this->from : null;
    }

    public function cleanDelete(): void
    {
        $this->from = null;
    }
}
