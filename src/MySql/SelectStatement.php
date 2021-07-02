<?php

declare(strict_types=1);

namespace AlephTools\SqlBuilder\MySql;

use AlephTools\SqlBuilder\MySql\Clause\JoinClause;
use AlephTools\SqlBuilder\MySql\Clause\UnionClause;
use AlephTools\SqlBuilder\Sql\AbstractSelectStatement;

class SelectStatement extends AbstractSelectStatement
{
    use UnionClause;
    use JoinClause;
}
