<?php

declare(strict_types=1);

namespace AlephTools\SqlBuilder\Sql;

use AlephTools\SqlBuilder\Command;
use AlephTools\SqlBuilder\Sql\Clause\DeleteClause;
use AlephTools\SqlBuilder\Sql\Clause\UsingClause;
use AlephTools\SqlBuilder\Sql\Clause\WhereClause;
use AlephTools\SqlBuilder\Sql\Clause\WithClause;
use AlephTools\SqlBuilder\Sql\Execution\StatementExecution;
use AlephTools\SqlBuilder\Sql\Expression\FromExpression;
use AlephTools\SqlBuilder\Sql\Expression\WhereExpression;
use AlephTools\SqlBuilder\Sql\Expression\WithExpression;
use AlephTools\SqlBuilder\StatementExecutor;

abstract class AbstractDeleteStatement extends AbstractStatement implements Command
{
    use WithClause;
    use DeleteClause;
    use UsingClause;
    use WhereClause;
    use StatementExecution;

    public function __construct(
        StatementExecutor $db = null,
        WithExpression $with = null,
        FromExpression $from = null,
        FromExpression $using = null,
        WhereExpression $where = null
    ) {
        parent::__construct($db);
        $this->with = $with;
        $this->from = $from;
        $this->using = $using;
        $this->where = $where;
    }
}
