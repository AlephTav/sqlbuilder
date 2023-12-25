<?php

declare(strict_types=1);

namespace AlephTools\SqlBuilder\Sql\Clause;

use AlephTools\SqlBuilder\Sql\Expression\FromExpression;

trait InsertClause
{
    protected ?FromExpression $table = null;

    /**
     * @return static
     */
    public function into(mixed $table, mixed $alias = null)
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

    protected function cloneInsert(mixed $copy): void
    {
        $copy->table = $this->table ? clone $this->table : null;
    }

    public function cleanInsert(): void
    {
        $this->table = null;
    }
}
