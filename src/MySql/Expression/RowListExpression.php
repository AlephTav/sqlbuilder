<?php

namespace AlephTools\SqlBuilder\MySql\Expression;

use AlephTools\SqlBuilder\Sql\Expression\ValueListExpression;

class RowListExpression extends ValueListExpression
{
    protected function convertListOfValueListsToString(array $expression): string
    {
        $str = parent::convertListOfValueListsToString($expression);
        if (is_array(reset($expression))) {
            return $str;
        }
        return "ROW$str";
    }
}