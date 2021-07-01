<?php

declare(strict_types=1);

namespace AlephTools\SqlBuilder\PostgreSql;

use AlephTools\SqlBuilder\PostgreSql\Clause\UpdateClause;
use AlephTools\SqlBuilder\Sql\AbstractUpdateStatement;
use AlephTools\SqlBuilder\Sql\Clause\FromClause;
use AlephTools\SqlBuilder\Sql\Clause\ReturningClause;
use AlephTools\SqlBuilder\Sql\Execution\DataFetching;
use AlephTools\SqlBuilder\Sql\Expression\AssignmentExpression;
use AlephTools\SqlBuilder\Sql\Expression\FromExpression;
use AlephTools\SqlBuilder\Sql\Expression\ReturningExpression;
use AlephTools\SqlBuilder\Sql\Expression\WhereExpression;
use AlephTools\SqlBuilder\Sql\Expression\WithExpression;
use AlephTools\SqlBuilder\StatementExecutor;

class UpdateStatement extends AbstractUpdateStatement
{
    use UpdateClause;
    use FromClause;
    use ReturningClause;
    use DataFetching;

    public function __construct(
        StatementExecutor $db = null,
        WithExpression $with = null,
        FromExpression $table = null,
        bool $only = false,
        AssignmentExpression $assignment = null,
        FromExpression $from = null,
        WhereExpression $where = null,
        ReturningExpression $returning = null
    ) {
        parent::__construct($db, $with, $table, $assignment, $where);
        $this->only = $only;
        $this->from = $from;
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
            $this->table ? clone $this->table : null,
            $this->only,
            $this->assignment ? clone $this->assignment : null,
            $this->from ? clone $this->from : null,
            $this->where ? clone $this->where : null,
            $this->returning ? clone $this->returning : null
        );
    }

    public function clean(): void
    {
        $this->with = null;
        $this->table = null;
        $this->only = false;
        $this->assignment = null;
        $this->from = null;
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
        $this->buildUpdate();
        $this->buildAssignment();
        $this->buildFrom();
        $this->buildWhere();
        $this->buildReturning();
        $this->built = true;
        return $this;
    }
}
