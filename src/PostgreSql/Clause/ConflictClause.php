<?php

declare(strict_types=1);

namespace AlephTools\SqlBuilder\PostgreSql\Clause;

use AlephTools\SqlBuilder\Sql\Expression\AssignmentExpression;
use AlephTools\SqlBuilder\Sql\Expression\ColumnListExpression;
use AlephTools\SqlBuilder\Sql\Expression\ConditionalExpression;

trait ConflictClause
{
    /**
     * @var ColumnListExpression
     */
    protected $indexColumn;

    /**
     * @var ConditionalExpression
     */
    protected $indexPredicate;

    /**
     * @var AssignmentExpression
     */
    protected $assignment;

    /**
     * @var ConditionalExpression
     */
    protected $assignmentPredicate;

    protected ?string $indexConstraint = null;

    private bool $whereBelongsToConflict = false;

    /**
     * @param mixed $indexColumn
     * @return static
     */
    public function onConflictDoNothing($indexColumn = '')
    {
        return $this->onConflict($indexColumn)
            ->doNothing();
    }

    /**
     * @param mixed $indexColumn
     * @param mixed $column
     * @param mixed $value
     * @return static
     */
    public function onConflictDoUpdate($indexColumn, $column, $value = null)
    {
        return $this->onConflict($indexColumn)
            ->doUpdate($column, $value);
    }

    /**
     * @param mixed $indexColumn
     * @param mixed $indexPredicate
     * @return static
     */
    public function onConflict($indexColumn = '', $indexPredicate = null)
    {
        $this->indexColumn = $this->indexColumn ?? new ColumnListExpression();
        $this->indexColumn->append($indexColumn);
        if ($indexPredicate !== null) {
            if ($this->indexPredicate) {
                $this->indexPredicate->with($indexPredicate);
            } else {
                $this->indexPredicate = $indexPredicate instanceof ConditionalExpression ?
                    $indexPredicate : new ConditionalExpression($indexPredicate);
            }
        }
        $this->whereBelongsToConflict = true;
        $this->built = false;
        return $this;
    }

    /**
     * @return static
     */
    public function onConstraint(string $indexConstraint)
    {
        $this->indexConstraint = $indexConstraint;
        $this->built = false;
        return $this;
    }

    /**
     * @return static
     */
    public function doNothing()
    {
        $this->assignment = null;
        $this->built = false;
        return $this;
    }

    /**
     * @param mixed $column
     * @param mixed $value
     * @return static
     */
    public function doUpdate($column, $value = null)
    {
        $this->assignment = $this->assignment ?? new AssignmentExpression();
        $this->assignment->append($column, $value);
        $this->whereBelongsToConflict = false;
        $this->built = false;
        return $this;
    }

    /**
     * @param mixed $column
     * @param mixed $valueOrAssignmentPredicate
     * @param mixed $assignmentPredicate
     * @return static
     */
    public function doUpdateWithCondition($column, $valueOrAssignmentPredicate, $assignmentPredicate = null)
    {
        if ($assignmentPredicate === null) {
            $value = null;
            $assignmentPredicate = $valueOrAssignmentPredicate;
        } else {
            $value = $valueOrAssignmentPredicate;
        }
        $this->assignment = $this->assignment ?? new AssignmentExpression();
        $this->assignment->append($column, $value);
        if ($assignmentPredicate !== null) {
            if ($this->assignmentPredicate) {
                $this->assignmentPredicate->with($assignmentPredicate);
            } else {
                $this->assignmentPredicate = $assignmentPredicate instanceof ConditionalExpression ?
                    $assignmentPredicate : new ConditionalExpression($assignmentPredicate);
            }
        }
        $this->whereBelongsToConflict = false;
        $this->built = false;
        return $this;
    }

    /**
     * @param mixed $column
     * @param mixed $operator
     * @param mixed $value
     * @return static
     */
    public function andWhere($column, $operator = null, $value = null)
    {
        return $this->where($column, $operator, $value);
    }

    /**
     * @param mixed $column
     * @param mixed $operator
     * @param mixed $value
     * @return static
     */
    public function orWhere($column, $operator = null, $value = null)
    {
        return $this->where($column, $operator, $value, 'OR');
    }

    /**
     * @param mixed $column
     * @param mixed $operator
     * @param mixed $value
     * @return static
     */
    public function where($column, $operator = null, $value = null, string $connector = 'AND')
    {
        if ($this->whereBelongsToConflict) {
            $this->indexPredicate = $this->indexPredicate ?? new ConditionalExpression();
            $this->indexPredicate->where($column, $operator, $value, $connector);
        } else {
            $this->assignmentPredicate = $this->assignmentPredicate ?? new ConditionalExpression();
            $this->assignmentPredicate->where($column, $operator, $value, $connector);
        }
        $this->built = false;
        return $this;
    }

    protected function buildOnConflictDoUpdate(): void
    {
        if (!$this->indexColumn && !$this->indexPredicate && !$this->indexConstraint && !$this->assignment) {
            return;
        }
        $this->sql .= ' ON CONFLICT';
        if ($this->indexColumn) {
            $index = $this->indexColumn->toSql();
            if ($index !== '') {
                $this->sql .= " ($index)";
                $this->addParams($this->indexColumn->getParams());
            }
        }
        if ($this->indexPredicate) {
            $this->sql .= " WHERE $this->indexPredicate";
            $this->addParams($this->indexPredicate->getParams());
        }
        if ($this->indexConstraint !== null && $this->indexConstraint !== '') {
            $this->sql .= " ON CONSTRAINT $this->indexConstraint";
        }
        $this->sql .= ' DO ';
        if ($this->assignment) {
            $this->sql .= "UPDATE SET $this->assignment";
            $this->addParams($this->assignment->getParams());
            if ($this->assignmentPredicate) {
                $this->sql .= " WHERE $this->assignmentPredicate";
                $this->addParams($this->assignmentPredicate->getParams());
            }
        } else {
            $this->sql .= 'NOTHING';
        }
    }
}
