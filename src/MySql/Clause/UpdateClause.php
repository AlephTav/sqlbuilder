<?php

declare(strict_types=1);

namespace AlephTools\SqlBuilder\MySql\Clause;

use AlephTools\SqlBuilder\Sql\Clause\UpdateClause as BaseUpdateClause;

trait UpdateClause
{
    use BaseUpdateClause {
        BaseUpdateClause::cloneUpdate as parentCloneUpdate;
        BaseUpdateClause::cleanUpdate as parentCleanUpdate;
    }

    protected string $modifiers = '';

    public function lowPriority(): static
    {
        return $this->modifier('LOW_PRIORITY');
    }

    public function ignore(): static
    {
        return $this->modifier('IGNORE');
    }

    public function modifier(string $modifier): static
    {
        $this->modifiers .= " $modifier";
        $this->built = true;
        return $this;
    }

    protected function cloneUpdate(mixed $copy): void
    {
        $this->parentCloneUpdate($copy);
        $copy->modifiers = $this->modifiers;
    }

    public function cleanUpdate(): void
    {
        $this->parentCleanUpdate();
        $this->modifiers = '';
    }

    protected function buildUpdate(): void
    {
        $this->sql .= 'UPDATE';
        if ($this->modifiers !== '') {
            $this->sql .= " $this->modifiers";
        }
        if ($this->table) {
            $this->sql .= " $this->table";
            $this->addParams($this->table->getParams());
        }
    }
}
