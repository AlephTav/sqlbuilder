<?php

declare(strict_types=1);

namespace AlephTools\SqlBuilder\PostgreSql;

use AlephTools\SqlBuilder\PostgreSql\Clause\JoinClause;
use AlephTools\SqlBuilder\PostgreSql\Clause\LockingClause;
use AlephTools\SqlBuilder\PostgreSql\Clause\UnionClause;
use AlephTools\SqlBuilder\Sql\AbstractSelectStatement;

class SelectStatement extends AbstractSelectStatement
{
    use UnionClause;
    use JoinClause;
    use LockingClause;
}
