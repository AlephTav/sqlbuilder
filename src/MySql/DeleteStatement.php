<?php

namespace AlephTools\SqlBuilder\MySql;

use AlephTools\SqlBuilder\MySql\Clause\DeleteClause;
use AlephTools\SqlBuilder\MySql\Clause\PartitionClause;
use AlephTools\SqlBuilder\Sql\AbstractDeleteStatement;
use AlephTools\SqlBuilder\Sql\Clause\LimitClause;
use AlephTools\SqlBuilder\Sql\Clause\OrderClause;
use AlephTools\SqlBuilder\Sql\Expression\FromExpression;
use AlephTools\SqlBuilder\Sql\Expression\ListExpression;
use AlephTools\SqlBuilder\Sql\Expression\OrderExpression;
use AlephTools\SqlBuilder\Sql\Expression\WhereExpression;
use AlephTools\SqlBuilder\Sql\Expression\WithExpression;
use AlephTools\SqlBuilder\StatementExecutor;

class DeleteStatement extends AbstractDeleteStatement
{
    use DeleteClause,
        PartitionClause,
        OrderClause,
        LimitClause;

    public function __construct(
        StatementExecutor $db = null,
        WithExpression $with = null,
        string $modifiers = null,
        FromExpression $from = null,
        ListExpression $partition = null,
        FromExpression $using = null,
        WhereExpression $where = null,
        OrderExpression $order = null,
        int $limit = null
    ) {
        parent::__construct($db, $with, $from, $using, $where);
        $this->modifiers = $modifiers;
        $this->partition = $partition;
        $this->order = $order;
        $this->limit = $limit;
    }

    public function copy()
    {
        return new static(
            $this->db,
            $this->with ? clone $this->with : null,
            $this->modifiers,
            $this->from ? clone $this->from : null,
            $this->partition ? clone $this->partition : null,
            $this->using ? clone $this->using : null,
            $this->where ? clone $this->where : null,
            $this->order ? clone $this->order : null,
            $this->limit
        );
    }

    public function clean(): void
    {
        $this->with = null;
        $this->modifiers = null;
        $this->from = null;
        $this->partition = null;
        $this->using = null;
        $this->where = null;
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
        $this->buildWith();
        $this->buildDelete();
        $this->buildPartition();
        $this->buildUsing();
        $this->buildWhere();
        $this->buildOrderBy();
        $this->buildLimit();
        $this->built = true;
        return $this;
    }
}