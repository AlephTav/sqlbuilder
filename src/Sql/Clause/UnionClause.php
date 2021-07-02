<?php

declare(strict_types=1);

namespace AlephTools\SqlBuilder\Sql\Clause;

use AlephTools\SqlBuilder\Query;

trait UnionClause
{
    /**
     * Format:
     * [
     *     [
     *         union type,
     *         query instance
     *     ],
     *     ...
     * ]
     *
     */
    protected array $union = [];

    /**
     * @return static
     */
    public function union(Query $query)
    {
        return $this->typeUnion('UNION', $query);
    }

    /**
     * @return static
     */
    public function unionAll(Query $query)
    {
        return $this->typeUnion('UNION ALL', $query);
    }

    /**
     * @return static
     */
    protected function typeUnion(string $type, Query $query)
    {
        if ($this->union) {
            $this->union[] = [$type, $query];
        } else {
            $self = $this->copy();
            $this->union = [
                [$type, $self],
                [$type, $query],
            ];
            $this->clean();
        }
        $this->built = false;
        return $this;
    }

    protected function buildUnion(): void
    {
        $first = true;
        foreach ($this->union as [$unionType, $query]) {
            if (!$first) {
                $this->sql .= " $unionType ";
            }
            $this->sql .= "($query)";
            $this->addParams($query->getParams());
            $first = false;
        }
    }
}
