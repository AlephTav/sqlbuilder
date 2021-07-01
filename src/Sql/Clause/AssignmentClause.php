<?php

declare(strict_types=1);

namespace AlephTools\SqlBuilder\Sql\Clause;

use AlephTools\SqlBuilder\Sql\Expression\AssignmentExpression;

trait AssignmentClause
{
    protected ?AssignmentExpression $assignment = null;

    /**
     * @param mixed $column
     * @param mixed $value
     * @return static
     */
    public function assign($column, $value = null)
    {
        $this->assignment = $this->assignment ?? $this->createAssignmentExpression();
        $this->assignment->append($column, $value);
        $this->built = false;
        return $this;
    }

    protected function createAssignmentExpression()
    {
        return new AssignmentExpression();
    }

    protected function buildAssignment(): void
    {
        if ($this->assignment) {
            $this->sql .= " SET $this->assignment";
            $this->addParams($this->assignment->getParams());
        }
    }
}
