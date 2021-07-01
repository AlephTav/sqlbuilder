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

class InsertStatement extends AbstractInsertStatement
{
    use WithClause;
    use InsertClause;
    use ValueListClause;
    use ConflictClause;
    use ReturningClause;
    use DataFetching;

    /**
     * @return static
     */
    public function copy()
    {
        $copy = new static();
        $copy->with = $this->with ? clone $this->with : null;
        $copy->table = $this->table ? clone $this->table : null;
        $copy->columns = $this->columns ? clone $this->columns : null;
        $copy->values = $this->values ? clone $this->values : null;
        $copy->query = $this->query ? $this->query->copy() : null;
        $copy->indexColumn = $this->indexColumn ? clone $this->indexColumn : null;
        $copy->indexPredicate = $this->indexPredicate ? clone $this->indexPredicate : null;
        $copy->indexConstraint = $this->indexConstraint;
        $copy->assignment = $this->assignment ? clone $this->assignment : null;
        $copy->assignmentPredicate = $this->assignmentPredicate ? clone $this->assignmentPredicate : null;
        $copy->returning = $this->returning ? clone $this->returning : null;
        return $copy;
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
