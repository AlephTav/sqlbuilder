<?php

declare(strict_types=1);

namespace AlephTools\SqlBuilder\PostgreSql;

use AlephTools\SqlBuilder\PostgreSql\Clause\UnionClause;
use AlephTools\SqlBuilder\Sql\AbstractValuesStatement;
use AlephTools\SqlBuilder\Sql\Clause\OffsetClause;
use AlephTools\SqlBuilder\Sql\Clause\ValuesClause;
use AlephTools\SqlBuilder\Sql\Execution\DataFetching;

class ValuesStatement extends AbstractValuesStatement
{
    use UnionClause;
    use ValuesClause;
    use OffsetClause;
    use DataFetching;

    /**
     * @return static
     */
    public function copy()
    {
        $copy = new static($this->db);
        $copy->values = $this->values ? clone $this->values : null;
        $copy->order = $this->order ? clone $this->order : null;
        $copy->limit = $this->limit;
        $copy->offset = $this->offset;
        return $copy;
    }

    public function clean(): void
    {
        $this->values = null;
        $this->order = null;
        $this->limit = null;
        $this->offset = null;
    }

    /**
     * @return static
     */
    public function paginate(int $page, int $size)
    {
        $this->offset = $size * $page;
        $this->limit = $size;
        $this->built = false;
        return $this;
    }

    /**
     * @return static
     */
    public function build()
    {
        if ($this->built) {
            return $this;
        }
        $this->sql = '';
        $this->params = [];
        if ($this->union) {
            $this->buildUnion();
        } else {
            $this->buildValues();
        }
        $this->buildOrderBy();
        $this->buildLimit();
        $this->buildOffset();
        $this->built = true;
        return $this;
    }
}
