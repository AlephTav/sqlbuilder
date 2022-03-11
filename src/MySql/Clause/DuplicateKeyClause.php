<?php

declare(strict_types=1);

namespace AlephTools\SqlBuilder\MySql\Clause;

use AlephTools\SqlBuilder\Sql\Expression\AssignmentExpression;

trait DuplicateKeyClause
{
    protected ?AssignmentExpression $assignmentOnUpdate = null;

    public function onDuplicateKeyUpdate(mixed $column, mixed $value = null): static
    {
        $this->assignmentOnUpdate = $this->assignmentOnUpdate ?? new AssignmentExpression();
        $this->assignmentOnUpdate->append($column, $value);
        $this->built = false;
        return $this;
    }

    protected function cloneDuplicateKey(mixed $copy): void
    {
        $copy->assignmentOnUpdate = $this->assignmentOnUpdate ?
            clone $this->assignmentOnUpdate : $this->assignmentOnUpdate;
    }

    protected function cleanDuplicateKey(): void
    {
        $this->assignmentOnUpdate = null;
    }

    protected function buildOnDuplicateKeyUpdate(): void
    {
        if ($this->assignmentOnUpdate) {
            $this->sql .= " ON DUPLICATE KEY UPDATE $this->assignmentOnUpdate";
            $this->addParams($this->assignmentOnUpdate->getParams());
        }
    }
}
