<?php

declare(strict_types=1);

namespace AlephTools\SqlBuilder\Sql\Clause;

use AlephTools\SqlBuilder\Sql\Expression\FromExpression;

trait UpdateClause
{
    protected ?FromExpression $table = null;

    /**
     * @return static
     */
    public function table(mixed $table, mixed $alias = null)
    {
        $this->table = $this->table ?? $this->createTableExpression();
        $this->table->append($table, $alias);
        $this->built = false;
        return $this;
    }

    protected function createTableExpression(): FromExpression
    {
        return new FromExpression();
    }

    protected function cloneUpdate(mixed $copy): void
    {
        $copy->table = $this->table ? clone $this->table : null;
    }

    protected function cleanUpdate(): void
    {
        $this->table = null;
    }
}
