<?php

declare(strict_types=1);

namespace AlephTools\SqlBuilder\MySql\Clause;

use AlephTools\SqlBuilder\MySql\Expression\RowListExpression;
use AlephTools\SqlBuilder\Sql\Clause\ValuesClause;

trait RowListClause
{
    use ValuesClause;

    protected function createValuesExpression(): RowListExpression
    {
        return new RowListExpression();
    }
}
