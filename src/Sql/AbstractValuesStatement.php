<?php

declare(strict_types=1);

namespace AlephTools\SqlBuilder\Sql;

use AlephTools\SqlBuilder\Query;
use AlephTools\SqlBuilder\Sql\Clause\LimitClause;
use AlephTools\SqlBuilder\Sql\Clause\OrderClause;
use AlephTools\SqlBuilder\Sql\Clause\UnionClause;

abstract class AbstractValuesStatement extends AbstractStatement implements Query
{
    use UnionClause;
    use OrderClause;
    use LimitClause;
}
