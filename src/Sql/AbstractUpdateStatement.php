<?php

namespace AlephTools\SqlBuilder\Sql;

use AlephTools\SqlBuilder\Command;
use AlephTools\SqlBuilder\Sql\Clause\AssignmentClause;
use AlephTools\SqlBuilder\Sql\Clause\UpdateClause;
use AlephTools\SqlBuilder\Sql\Clause\WhereClause;
use AlephTools\SqlBuilder\Sql\Clause\WithClause;
use AlephTools\SqlBuilder\Sql\Execution\StatementExecution;
use AlephTools\SqlBuilder\Sql\Expression\AssignmentExpression;
use AlephTools\SqlBuilder\Sql\Expression\FromExpression;
use AlephTools\SqlBuilder\Sql\Expression\WhereExpression;
use AlephTools\SqlBuilder\Sql\Expression\WithExpression;
use AlephTools\SqlBuilder\StatementExecutor;

abstract class AbstractUpdateStatement extends AbstractStatement implements Command
{
    use WithClause,
        UpdateClause,
        AssignmentClause,
        WhereClause,
        StatementExecution;

    public function __construct(
        StatementExecutor $db = null,
        WithExpression $with = null,
        FromExpression $table = null,
        AssignmentExpression $assignment = null,
        WhereExpression $where = null
    ) {
        parent::__construct($db);
        $this->with = $with;
        $this->table = $table;
        $this->assignment = $assignment;
        $this->where = $where;
    }
}