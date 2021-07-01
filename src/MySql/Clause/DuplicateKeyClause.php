<?php

declare(strict_types=1);

namespace AlephTools\SqlBuilder\MySql\Clause;

use AlephTools\SqlBuilder\Sql\Expression\AssignmentExpression;

trait DuplicateKeyClause
{
    /**
     * @var AssignmentExpression
     */
    protected $assignmentOnUpdate;

    /**
     * @param mixed $column
     * @param mixed $value
     * @return static
     */
    public function onDuplicateKeyUpdate($column, $value = null)
    {
        $this->assignmentOnUpdate = $this->assignmentOnUpdate ?? new AssignmentExpression();
        $this->assignmentOnUpdate->append($column, $value);
        $this->built = false;
        return $this;
    }

    protected function buildOnDuplicateKeyUpdate(): void
    {
        if ($this->assignmentOnUpdate) {
            $this->sql .= " ON DUPLICATE KEY UPDATE $this->assignmentOnUpdate";
            $this->addParams($this->assignmentOnUpdate->getParams());
        }
    }
}
