<?php

declare(strict_types=1);

namespace AlephTools\SqlBuilder\Clickhouse\Clause;

use AlephTools\SqlBuilder\Sql\Expression\FromExpression;

trait ArrayJoinClause
{
    protected ?FromExpression $arrayJoin = null;

    public function arrayJoin(mixed $column, mixed $alias): static
    {
        $this->arrayJoin ??= $this->createArrayJoinExpression();
        $this->arrayJoin->append($column, $alias);
        $this->built = false;
        return $this;
    }

    private function createArrayJoinExpression(): FromExpression
    {
        return new FromExpression();
    }

    public function leftArrayJoin(): static
    {
        return $this;
    }

    public function cloneArrayJoin(mixed $copy): void
    {
        $copy->arrayJoin = $this->arrayJoin ? clone $this->arrayJoin : null;
    }

    public function cleanArrayJoin(): void
    {
        $this->arrayJoin = null;
    }

    public function buildArrayJoin(): void
    {
        if ($this->arrayJoin) {
            $this->sql .= " ARRAY JOIN $this->arrayJoin";
            $this->addParams($this->arrayJoin->getParams());
        }
    }
}
