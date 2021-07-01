<?php

namespace AlephTools\SqlBuilder\MySql;

use AlephTools\SqlBuilder\MySql\Clause\UpdateClause;
use AlephTools\SqlBuilder\Sql\AbstractUpdateStatement;
use AlephTools\SqlBuilder\Sql\Clause\LimitClause;
use AlephTools\SqlBuilder\Sql\Clause\OrderClause;
use AlephTools\SqlBuilder\Sql\Expression\AssignmentExpression;
use AlephTools\SqlBuilder\Sql\Expression\FromExpression;
use AlephTools\SqlBuilder\Sql\Expression\OrderExpression;
use AlephTools\SqlBuilder\Sql\Expression\WhereExpression;
use AlephTools\SqlBuilder\Sql\Expression\WithExpression;
use AlephTools\SqlBuilder\StatementExecutor;

class UpdateStatement extends AbstractUpdateStatement
{
    use UpdateClause,
        OrderClause,
        LimitClause;

    public function __construct(
        StatementExecutor $db = null,
        WithExpression $with = null,
        string $modifiers = null,
        FromExpression $table = null,
        AssignmentExpression $assignment = null,
        WhereExpression $where = null,
        OrderExpression $order = null,
        int $limit = null
    ) {
        parent::__construct($db, $with, $table, $assignment, $where);
        $this->modifiers = $modifiers;
        $this->order = $order;
        $this->limit = $limit;
    }

    /**
     * @return static
     */
    public function copy()
    {
        return new static(
            $this->db,
            $this->with ? clone $this->with : null,
            $this->modifiers,
            $this->table ? clone $this->table : null,
            $this->assignment ? clone $this->assignment : null,
            $this->where ? clone $this->where : null,
            $this->order ? clone $this->order : null,
            $this->limit
        );
    }

    public function clean(): void
    {
        $this->with = null;
        $this->modifiers = null;
        $this->table = null;
        $this->assignment = null;
        $this->where = null;
        $this->order = null;
        $this->limit = null;
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
        $this->buildWith();
        $this->buildUpdate();
        $this->buildAssignment();
        $this->buildWhere();
        $this->buildOrderBy();
        $this->buildLimit();
        $this->built = true;
        return $this;
    }
}