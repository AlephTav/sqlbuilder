<?php

namespace AlephTools\SqlBuilder\PostgreSql;

use AlephTools\SqlBuilder\PostgreSql\Clause\DeleteClause;
use AlephTools\SqlBuilder\Sql\AbstractDeleteStatement;
use AlephTools\SqlBuilder\Sql\Clause\ReturningClause;
use AlephTools\SqlBuilder\Sql\Execution\DataFetching;
use AlephTools\SqlBuilder\Sql\Expression\FromExpression;
use AlephTools\SqlBuilder\Sql\Expression\ReturningExpression;
use AlephTools\SqlBuilder\Sql\Expression\WhereExpression;
use AlephTools\SqlBuilder\Sql\Expression\WithExpression;
use AlephTools\SqlBuilder\StatementExecutor;

class DeleteStatement extends AbstractDeleteStatement
{
    use DeleteClause,
        ReturningClause,
        DataFetching;

    public function __construct(
        StatementExecutor $db = null,
        WithExpression $with = null,
        FromExpression $from = null,
        bool $only = false,
        FromExpression $using = null,
        WhereExpression $where = null,
        ReturningExpression $returning = null
    ) {
        parent::__construct($db, $with, $from, $using, $where);
        $this->only = $only;
        $this->returning = $returning;
    }

    /**
     * @return static
     */
    public function copy()
    {
        return new static(
            $this->db,
            $this->with ? clone $this->with : null,
            $this->from ? clone $this->from : null,
            $this->only,
            $this->using ? clone $this->using : null,
            $this->where ? clone $this->where : null,
            $this->returning ? clone $this->returning : null
        );
    }

    public function clean(): void
    {
        $this->with = null;
        $this->from = null;
        $this->only = false;
        $this->using = null;
        $this->where = null;
        $this->returning = null;
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
        $this->buildDelete();
        $this->buildUsing();
        $this->buildWhere();
        $this->buildReturning();
        $this->built = true;
        return $this;
    }
}