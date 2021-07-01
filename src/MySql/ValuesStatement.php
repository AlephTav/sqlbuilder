<?php

declare(strict_types=1);

namespace AlephTools\SqlBuilder\MySql;

use AlephTools\SqlBuilder\MySql\Clause\LimitByClause;
use AlephTools\SqlBuilder\MySql\Clause\RowListClause;
use AlephTools\SqlBuilder\MySql\Expression\RowListExpression;
use AlephTools\SqlBuilder\Sql\AbstractValuesStatement;
use AlephTools\SqlBuilder\Sql\Expression\OrderExpression;
use AlephTools\SqlBuilder\StatementExecutor;

class ValuesStatement extends AbstractValuesStatement
{
    use RowListClause;
    use LimitByClause;

    public function __construct(
        StatementExecutor $db = null,
        RowListExpression $values = null,
        OrderExpression $order = null,
        int $limit = null
    ) {
        parent::__construct($db, $order, $limit);
        $this->values = $values;
    }

    /**
     * @return static
     */
    public function copy()
    {
        return new static(
            $this->db,
            $this->values ? clone $this->values : null,
            $this->order ? clone $this->order : null,
            $this->limit
        );
    }

    public function clean(): void
    {
        $this->values = null;
        $this->order = null;
        $this->limit = null;
    }

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
        $this->built = true;
        return $this;
    }
}
