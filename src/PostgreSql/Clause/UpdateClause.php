<?php

declare(strict_types=1);

namespace AlephTools\SqlBuilder\PostgreSql\Clause;

use AlephTools\SqlBuilder\Sql\Clause\UpdateClause as BaseUpdateClause;

trait UpdateClause
{
    use BaseUpdateClause;

    protected bool $only = false;

    /**
     * @param mixed $table
     * @param mixed $alias
     * @return static
     */
    public function onlyTable($table, $alias = null)
    {
        $this->only = true;
        return $this->table($table, $alias);
    }

    protected function buildUpdate(): void
    {
        $this->sql .= 'UPDATE';
        if ($this->table) {
            $this->sql .= ($this->only ? ' ONLY ' : ' ') . $this->table->toSql();
            $this->addParams($this->table->getParams());
        }
    }
}
