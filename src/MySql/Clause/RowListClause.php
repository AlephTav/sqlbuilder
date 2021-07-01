<?php

namespace AlephTools\SqlBuilder\MySql\Clause;

use AlephTools\SqlBuilder\MySql\Expression\RowListExpression;
use AlephTools\SqlBuilder\Sql\Clause\ValuesClause;

trait RowListClause
{
    use ValuesClause;

    protected function createValuesExpression()
    {
        return new RowListExpression();
    }
}