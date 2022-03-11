<?php

declare(strict_types=1);

namespace AlephTools\SqlBuilder\PostgreSql\Clause;

use AlephTools\SqlBuilder\Sql\Clause\DeleteClause as BaseDeleteClause;

trait DeleteClause
{
    use BaseDeleteClause {
        BaseDeleteClause::cloneDelete as parentCloneDelete;
        BaseDeleteClause::cleanDelete as parentCleanDelete;
    }

    protected bool $only = false;

    public function fromOnly(mixed $table, mixed $alias = null): static
    {
        $this->only = true;
        return $this->from($table, $alias);
    }

    protected function cloneDelete(mixed $copy): void
    {
        $this->parentCloneDelete($copy);
        $copy->only = $this->only;
    }

    protected function cleanDelete(): void
    {
        $this->parentCleanDelete();
        $this->only = false;
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
