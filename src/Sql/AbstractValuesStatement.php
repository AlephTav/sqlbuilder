<?php

namespace AlephTools\SqlBuilder\Sql;

use AlephTools\SqlBuilder\Query;
use AlephTools\SqlBuilder\Sql\Clause\LimitClause;
use AlephTools\SqlBuilder\Sql\Clause\OrderClause;
use AlephTools\SqlBuilder\Sql\Clause\UnionClause;
use AlephTools\SqlBuilder\Sql\Expression\OrderExpression;
use AlephTools\SqlBuilder\StatementExecutor;

abstract class AbstractValuesStatement extends AbstractStatement implements Query
{
    use UnionClause,
        OrderClause,
        LimitClause;

    public function __construct(
        StatementExecutor $db = null,
        OrderExpression $order = null,
        int $limit = null
    ) {
        parent::__construct($db);
        $this->order = $order;
        $this->limit = $limit;
    }
}