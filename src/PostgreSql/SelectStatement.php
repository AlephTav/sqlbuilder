<?php

namespace AlephTools\SqlBuilder\PostgreSql;

use AlephTools\SqlBuilder\PostgreSql\Clause\JoinClause;
use AlephTools\SqlBuilder\PostgreSql\Clause\UnionClause;
use AlephTools\SqlBuilder\Sql\AbstractSelectStatement;

class SelectStatement extends AbstractSelectStatement
{
    use UnionClause, JoinClause;
}