<?php

declare(strict_types=1);

namespace AlephTools\SqlBuilder\Sql\Expression;

class RawExpression extends AbstractExpression
{
    public function __construct(string $expression, array $params = [])
    {
        $this->sql = $expression;
        $this->params = $params;
    }
}
