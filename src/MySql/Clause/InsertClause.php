<?php

declare(strict_types=1);

namespace AlephTools\SqlBuilder\MySql\Clause;

use AlephTools\SqlBuilder\Sql\Clause\InsertClause as BaseInsertClause;

trait InsertClause
{
    use BaseInsertClause {
        BaseInsertClause::cloneInsert as parentCloneInsert;
        BaseInsertClause::cleanInsert as parentCleanInsert;
    }

    protected string $modifiers = '';

    public function lowPriority(): static
    {
        return $this->modifier('LOW_PRIORITY');
    }

    public function highPriority(): static
    {
        return $this->modifier('HIGH_PRIORITY');
    }

    public function delayed(): static
    {
        return $this->modifier('DELAYED');
    }

    public function ignore(): static
    {
        return $this->modifier('IGNORE');
    }

    public function lowPriorityIgnore(): static
    {
        return $this->modifier('LOW_PRIORITY IGNORE');
    }

    public function highPriorityIgnore(): static
    {
        return $this->modifier('HIGH_PRIORITY IGNORE');
    }

    public function delayedIgnore(): static
    {
        return $this->modifier('DELAYED IGNORE');
    }

    public function modifier(string $modifier): static
    {
        $this->modifiers .= " $modifier";
        $this->built = true;
        return $this;
    }

    protected function cloneInsert(mixed $copy): void
    {
        $this->parentCloneInsert($copy);
        $copy->modifiers = $this->modifiers;
    }

    public function cleanInsert(): void
    {
        $this->parentCleanInsert();
        $this->modifiers = '';
    }

    protected function buildInsert(): void
    {
        $this->sql .= 'INSERT';
        if ($this->modifiers !== '') {
            $this->sql .= " $this->modifiers";
        }
        if ($this->table) {
            $this->sql .= " INTO $this->table";
            $this->addParams($this->table->getParams());
        }
    }
}
