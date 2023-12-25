<?php

declare(strict_types=1);

namespace AlephTools\SqlBuilder\Sql\Clause;

use AlephTools\SqlBuilder\Sql\Expression\AssignmentExpression;

trait AssignmentClause
{
    protected ?AssignmentExpression $assignment = null;

    public function assign(mixed $column, mixed $value = null): static
    {
        $this->assignment = $this->assignment ?? $this->createAssignmentExpression();
        $this->assignment->append($column, $value);
        $this->built = false;
        return $this;
    }

    protected function createAssignmentExpression(): AssignmentExpression
    {
        return new AssignmentExpression();
    }

    protected function cloneAssignment(mixed $copy): void
    {
        $copy->assignment = $this->assignment ? clone $this->assignment : null;
    }

    public function cleanAssignment(): void
    {
        $this->assignment = null;
    }

    protected function buildAssignment(): void
    {
        if ($this->assignment) {
            $this->sql .= " SET $this->assignment";
            $this->addParams($this->assignment->getParams());
        }
    }
}
