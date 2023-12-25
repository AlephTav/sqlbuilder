<?php

declare(strict_types=1);

namespace AlephTools\SqlBuilder\MySql\Clause;

use AlephTools\SqlBuilder\Sql\Clause\DeleteClause as BaseDeleteClause;

trait DeleteClause
{
    use BaseDeleteClause {
        BaseDeleteClause::cloneDelete as parentCloneDelete;
        BaseDeleteClause::cleanDelete as parentCleanDelete;
    }

    protected string $modifiers = '';

    public function lowPriority(): static
    {
        return $this->modifier('LOW_PRIORITY');
    }

    public function quick(): static
    {
        return $this->modifier('QUICK');
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

    protected function cloneDelete(mixed $copy): void
    {
        $this->parentCloneDelete($copy);
        $copy->modifiers = $this->modifiers;
    }

    public function cleanDelete(): void
    {
        $this->parentCleanDelete();
        $this->modifiers = '';
    }

    protected function buildDelete(): void
    {
        $this->sql .= 'DELETE';
        if ($this->modifiers !== '') {
            $this->sql .= " $this->modifiers";
        }
        if ($this->from) {
            $this->sql .= " FROM $this->from";
            $this->addParams($this->from->getParams());
        }
    }
}
