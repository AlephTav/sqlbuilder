<?php

declare(strict_types=1);

namespace AlephTools\SqlBuilder\MySql\Clause;

use AlephTools\SqlBuilder\MySql\Expression\RowListExpression;
use AlephTools\SqlBuilder\Sql\Clause\ValuesClause;

/**
 * @property RowListExpression|null $values
 */
trait RowListClause
{
    use ValuesClause;

    protected function createValuesExpression()
    {
        return new RowListExpression();
    }
}
