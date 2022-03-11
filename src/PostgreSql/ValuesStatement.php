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

    public function copy(): static
    {
        $copy = new static($this->db);
        $this->cloneValues($copy);
        $this->cloneOrderBy($copy);
        $this->cloneLimit($copy);
        $this->cloneOffset($copy);
        return $copy;
    }

    public function clean(): static
    {
        $this->cleanValues();
        $this->cleanOrderBy();
        $this->cleanLimit();
        $this->cleanOffset();
        return $this;
    }

    public function paginate(int $page, int $size): static
    {
        $this->offset = $size * $page;
        $this->limit = $size;
        $this->built = false;
        return $this;
    }

    public function build(): static
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
