<?php

declare(strict_types=1);

namespace AlephTools\SqlBuilder\PostgreSql\Clause;

use AlephTools\SqlBuilder\Sql\Clause\DeleteClause as BaseDeleteClause;

trait DeleteClause
{
    use BaseDeleteClause;

    protected bool $only = false;

    /**
     * @param mixed $table
     * @param mixed $alias
     * @return static
     */
    public function fromOnly($table, $alias = null)
    {
        $this->only = true;
        return $this->from($table, $alias);
    }

    protected function buildDelete(): void
    {
        $this->sql .= 'DELETE FROM';
        if ($this->from) {
            $this->sql .= ($this->only ? ' ONLY ' : ' ') . $this->from->toSql();
            $this->addParams($this->from->getParams());
        }
    }
}
