<?php

declare(strict_types=1);

namespace AlephTools\SqlBuilder\PostgreSql\Clause;

use AlephTools\SqlBuilder\Sql\Expression\AssignmentExpression;
use AlephTools\SqlBuilder\Sql\Expression\ColumnListExpression;
use AlephTools\SqlBuilder\Sql\Expression\ConditionalExpression;

trait ConflictClause
{
    protected ?ColumnListExpression $indexColumn = null;
    protected ?ConditionalExpression $indexPredicate = null;
    protected ?AssignmentExpression $assignment = null;
    protected ?ConditionalExpression $assignmentPredicate = null;
    protected ?string $indexConstraint = null;
    private bool $whereBelongsToConflict = false;

    public function onConflictDoNothing(mixed $indexColumn = ''): static
    {
        return $this->onConflict($indexColumn)
            ->doNothing();
    }

    public function onConflictDoUpdate(mixed $indexColumn, mixed $column, mixed $value = null): static
    {
        return $this->onConflict($indexColumn)
            ->doUpdate($column, $value);
    }

    public function onConflict(mixed $indexColumn = '', mixed $indexPredicate = null): static
    {
        $this->indexColumn = $this->indexColumn ?? new ColumnListExpression();
        $this->indexColumn->append($indexColumn);
        if ($indexPredicate !== null) {
            if ($this->indexPredicate) {
                $this->indexPredicate->and($indexPredicate);
            } else {
                $this->indexPredicate = $indexPredicate instanceof ConditionalExpression ?
                    $indexPredicate : new ConditionalExpression($indexPredicate);
            }
        }
        $this->whereBelongsToConflict = true;
        $this->built = false;
        return $this;
    }

    public function onConstraint(string $indexConstraint): static
    {
        $this->indexConstraint = $indexConstraint;
        $this->built = false;
        return $this;
    }

    public function doNothing(): static
    {
        $this->assignment = null;
        $this->built = false;
        return $this;
    }

    public function doUpdate(mixed $column, mixed $value = null): static
    {
        $this->assignment = $this->assignment ?? new AssignmentExpression();
        $this->assignment->append($column, $value);
        $this->whereBelongsToConflict = false;
        $this->built = false;
        return $this;
    }

    public function doUpdateWithCondition(
        mixed $column,
        mixed $valueOrAssignmentPredicate,
        mixed $assignmentPredicate = null
    ): static {
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
                $this->assignmentPredicate->and($assignmentPredicate);
            } else {
                $this->assignmentPredicate = $assignmentPredicate instanceof ConditionalExpression ?
                    $assignmentPredicate : new ConditionalExpression($assignmentPredicate);
            }
        }
        $this->whereBelongsToConflict = false;
        $this->built = false;
        return $this;
    }

    public function andWhere(mixed $column, mixed $operator = null, mixed $value = null): static
    {
        return $this->where($column, $operator, $value);
    }

    public function orWhere(mixed $column, mixed $operator = null, mixed $value = null): static
    {
        return $this->where($column, $operator, $value, 'OR');
    }

    public function where(mixed $column, mixed $operator = null, mixed $value = null, string $connector = 'AND'): static
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

    protected function cloneConflict(mixed $copy): void
    {
        $copy->indexColumn = $this->indexColumn ? clone $this->indexColumn : null;
        $copy->indexPredicate = $this->indexPredicate ? clone $this->indexPredicate : null;
        $copy->indexConstraint = $this->indexConstraint;
        $copy->assignment = $this->assignment ? clone $this->assignment : null;
        $copy->assignmentPredicate = $this->assignmentPredicate ? clone $this->assignmentPredicate : null;
    }

    public function cleanConflict(): void
    {
        $this->indexColumn = null;
        $this->indexPredicate = null;
        $this->indexConstraint = null;
        $this->assignment = null;
        $this->assignmentPredicate = null;
        $this->whereBelongsToConflict = false;
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
