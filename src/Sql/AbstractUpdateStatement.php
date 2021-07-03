<?php

declare(strict_types=1);

namespace AlephTools\SqlBuilder\Sql;

use AlephTools\SqlBuilder\Command;
use AlephTools\SqlBuilder\Sql\Clause\AssignmentClause;
use AlephTools\SqlBuilder\Sql\Clause\UpdateClause;
use AlephTools\SqlBuilder\Sql\Clause\WhereClause;
use AlephTools\SqlBuilder\Sql\Clause\WithClause;
use AlephTools\SqlBuilder\Sql\Execution\StatementExecution;

abstract class AbstractUpdateStatement extends AbstractStatement implements Command
{
    use WithClause;
    use UpdateClause;
    use AssignmentClause;
    use WhereClause;
    use StatementExecution;
}
