<?php

declare(strict_types=1);

namespace AlephTools\SqlBuilder\MySql\Clause;

use AlephTools\SqlBuilder\MySql\Expression\RowListExpression;
use AlephTools\SqlBuilder\Sql\Clause\ValuesClause;

trait RowListClause
{
    use ValuesClause;

    /**
     * @var RowListExpression|null $values
     */
    protected $values;

    /**
     * @return RowListExpression
     */
    protected function createValuesExpression()
    {
        return new RowListExpression();
    }
}
