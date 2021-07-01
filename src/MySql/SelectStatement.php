<?php

namespace AlephTools\SqlBuilder\MySql;

use AlephTools\SqlBuilder\MySql\Clause\JoinClause;
use AlephTools\SqlBuilder\MySql\Clause\UnionClause;
use AlephTools\SqlBuilder\Sql\AbstractSelectStatement;

class SelectStatement extends AbstractSelectStatement
{
    use UnionClause, JoinClause;
}