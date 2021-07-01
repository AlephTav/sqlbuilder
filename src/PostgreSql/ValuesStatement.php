<?php

declare(strict_types=1);

namespace AlephTools\SqlBuilder\PostgreSql;

use AlephTools\SqlBuilder\PostgreSql\Clause\UnionClause;
use AlephTools\SqlBuilder\Sql\AbstractValuesStatement;
use AlephTools\SqlBuilder\Sql\Clause\OffsetClause;
use AlephTools\SqlBuilder\Sql\Clause\ValuesClause;
use AlephTools\SqlBuilder\Sql\Execution\DataFetching;
use AlephTools\SqlBuilder\Sql\Expression\OrderExpression;
use AlephTools\SqlBuilder\Sql\Expression\ValueListExpression;
use AlephTools\SqlBuilder\StatementExecutor;

class ValuesStatement extends AbstractValuesStatement
{
    use UnionClause;
    use ValuesClause;
    use OffsetClause;
    use DataFetching;

    public function __construct(
        StatementExecutor $db = null,
        ValueListExpression $values = null,
        OrderExpression $order = null,
        int $limit = null,
        int $offset = null
    ) {
        parent::__construct($db, $order, $limit);
        $this->values = $values;
        $this->order = $order;
        $this->limit = $limit;
        $this->offset = $offset;
    }

    public function copy()
    {
        return new static(
            $this->db,
            $this->values ? clone $this->values : null,
            $this->order ? clone $this->order : null,
            $this->limit,
            $this->offset
        );
    }

    public function clean(): void
    {
        $this->values = null;
        $this->order = null;
        $this->limit = null;
        $this->offset = null;
    }

    /**
     * @param int $page
     * @param int $size
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
