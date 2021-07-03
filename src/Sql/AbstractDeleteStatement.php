<?php

declare(strict_types=1);

namespace AlephTools\SqlBuilder\Sql;

use AlephTools\SqlBuilder\Command;
use AlephTools\SqlBuilder\Sql\Clause\DeleteClause;
use AlephTools\SqlBuilder\Sql\Clause\UsingClause;
use AlephTools\SqlBuilder\Sql\Clause\WhereClause;
use AlephTools\SqlBuilder\Sql\Clause\WithClause;
use AlephTools\SqlBuilder\Sql\Execution\StatementExecution;

abstract class AbstractDeleteStatement extends AbstractStatement implements Command
{
    use WithClause;
    use DeleteClause;
    use UsingClause;
    use WhereClause;
    use StatementExecution;
}
