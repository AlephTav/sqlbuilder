<?php

declare(strict_types=1);

namespace AlephTools\SqlBuilder\PostgreSql\Clause;

use AlephTools\SqlBuilder\Sql\Clause\UpdateClause as BaseUpdateClause;

trait UpdateClause
{
    use BaseUpdateClause {
        BaseUpdateClause::cloneUpdate as parentCloneUpdate;
        BaseUpdateClause::cleanUpdate as parentCleanUpdate;
    }

    protected bool $only = false;

    public function onlyTable(mixed $table, mixed $alias = null): static
    {
        $this->only = true;
        return $this->table($table, $alias);
    }

    protected function cloneUpdate(mixed $copy): void
    {
        $this->parentCloneUpdate($copy);
        $copy->only = $this->only;
    }

    public function cleanUpdate(): void
    {
        $this->parentCleanUpdate();
        $this->only = false;
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
