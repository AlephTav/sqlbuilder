<?php

declare(strict_types=1);

namespace AlephTools\SqlBuilder\PostgreSql;

use AlephTools\SqlBuilder\PostgreSql\Clause\ConflictClause;
use AlephTools\SqlBuilder\PostgreSql\Clause\InsertClause;
use AlephTools\SqlBuilder\PostgreSql\Clause\ValueListClause;
use AlephTools\SqlBuilder\Sql\AbstractInsertStatement;
use AlephTools\SqlBuilder\Sql\Clause\ReturningClause;
use AlephTools\SqlBuilder\Sql\Clause\WithClause;
use AlephTools\SqlBuilder\Sql\Execution\DataFetching;
use AlephTools\SqlBuilder\Sql\Expression\AssignmentExpression;
use AlephTools\SqlBuilder\Sql\Expression\ColumnListExpression;
use AlephTools\SqlBuilder\Sql\Expression\ConditionalExpression;
use AlephTools\SqlBuilder\Sql\Expression\FromExpression;
use AlephTools\SqlBuilder\Sql\Expression\ReturningExpression;
use AlephTools\SqlBuilder\Sql\Expression\ValueListExpression;
use AlephTools\SqlBuilder\Sql\Expression\WithExpression;
use AlephTools\SqlBuilder\StatementExecutor;
use AlephTools\SqlBuilder\Query;

class InsertStatement extends AbstractInsertStatement
{
    use WithClause;
    use InsertClause;
    use ValueListClause;
    use ConflictClause;
    use ReturningClause;
    use DataFetching;

    public function __construct(
        StatementExecutor $db = null,
        WithExpression $with = null,
        FromExpression $table = null,
        ColumnListExpression $columns = null,
        ValueListExpression $values = null,
        Query $query = null,
        ColumnListExpression $indexColumn = null,
        ConditionalExpression $indexPredicate = null,
        string $indexConstraint = null,
        AssignmentExpression $assignment = null,
        ConditionalExpression $assignmentPredicate = null,
        ReturningExpression $returning = null
    ) {
        parent::__construct($db, $table, $columns, $values, $query);
        $this->with = $with;
        $this->indexColumn = $indexColumn;
        $this->indexPredicate = $indexPredicate;
        $this->indexConstraint = $indexConstraint;
        $this->assignment = $assignment;
        $this->assignmentPredicate = $assignmentPredicate;
        $this->returning = $returning;
    }

    public function copy()
    {
        return new static(
            $this->db,
            $this->with ? clone $this->with : null,
            $this->table ? clone $this->table : null,
            $this->columns ? clone $this->columns : null,
            $this->values ? clone $this->values : null,
            $this->query ? $this->query->copy() : null,
            $this->indexColumn ? clone $this->indexColumn : null,
            $this->indexPredicate ? clone $this->indexPredicate : null,
            $this->indexConstraint,
            $this->assignment ? clone $this->assignment : null,
            $this->assignmentPredicate ? clone $this->assignmentPredicate : null,
            $this->returning ? clone $this->returning : null
        );
    }

    public function clean(): void
    {
        $this->with = null;
        $this->table = null;
        $this->columns = null;
        $this->values = null;
        $this->query = null;
        $this->indexColumn = null;
        $this->indexPredicate = null;
        $this->indexConstraint = null;
        $this->assignment = null;
        $this->assignmentPredicate = null;
        $this->returning = null;
    }

    /**
     * @return static
     */
    public function build()
    {
        if ($this->built) {
            return $this;
        }
        $this->sql = '';
        $this->params = [];
        $this->buildWith();
        $this->buildInsert();
        $this->buildColumns();
        $this->buildValues();
        $this->buildQuery();
        $this->buildOnConflictDoUpdate();
        $this->buildReturning();
        $this->built = true;
        return $this;
    }
}
