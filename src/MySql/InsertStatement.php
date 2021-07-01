<?php

namespace AlephTools\SqlBuilder\MySql;

use AlephTools\SqlBuilder\MySql\Clause\DuplicateKeyClause;
use AlephTools\SqlBuilder\MySql\Clause\InsertClause;
use AlephTools\SqlBuilder\MySql\Clause\PartitionClause;
use AlephTools\SqlBuilder\MySql\Clause\RowAliasClause;
use AlephTools\SqlBuilder\MySql\Clause\ValueListClause;
use AlephTools\SqlBuilder\Sql\AbstractInsertStatement;
use AlephTools\SqlBuilder\Sql\Clause\AssignmentClause;
use AlephTools\SqlBuilder\Sql\Expression\AssignmentExpression;
use AlephTools\SqlBuilder\Sql\Expression\FromExpression;
use AlephTools\SqlBuilder\Sql\Expression\ListExpression;
use AlephTools\SqlBuilder\Sql\Expression\ValueListExpression;
use AlephTools\SqlBuilder\StatementExecutor;
use AlephTools\SqlBuilder\Query;

class InsertStatement extends AbstractInsertStatement
{
    use InsertClause,
        PartitionClause,
        RowAliasClause,
        ValueListClause,
        AssignmentClause,
        DuplicateKeyClause;

    public function __construct(
        StatementExecutor $db = null,
        string $modifiers = null,
        FromExpression $table = null,
        ListExpression $partition = null,
        ListExpression $columns = null,
        string $rowAlias = null,
        ListExpression $columnAliases = null,
        ValueListExpression $values = null,
        Query $query = null,
        AssignmentExpression $assignment = null,
        AssignmentExpression $assignmentOnUpdate = null
    ) {
        parent::__construct($db, $table, $columns, $values, $query);
        $this->modifiers = $modifiers;
        $this->partition = $partition;
        $this->rowAlias = $rowAlias;
        $this->columnAliases = $columnAliases;
        $this->assignment = $assignment;
        $this->assignmentOnUpdate = $assignmentOnUpdate;
    }

    /**
     * @return static
     */
    public function copy()
    {
        return new static(
            $this->db,
            $this->modifiers,
            $this->table ? clone $this->table : null,
            $this->partition ? clone $this->partition : null,
            $this->columns ? clone $this->columns : null,
            $this->rowAlias,
            $this->columnAliases ? clone $this->columnAliases : null,
            $this->values ? clone $this->values : null,
            $this->query ? $this->query->copy() : null,
            $this->assignment ? clone $this->assignment : $this->assignment,
            $this->assignmentOnUpdate ? clone $this->assignmentOnUpdate : $this->assignmentOnUpdate
        );
    }

    public function clean(): void
    {
        $this->modifiers = null;
        $this->table = null;
        $this->partition = null;
        $this->columns = null;
        $this->rowAlias = null;
        $this->columnAliases = null;
        $this->values = null;
        $this->query = null;
        $this->assignment = null;
        $this->assignmentOnUpdate = null;
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
        $this->buildInsert();
        $this->buildPartition();
        $this->buildColumns();
        $this->buildRowAndColumnAliases();
        $this->buildValues();
        $this->buildQuery();
        $this->buildAssignment();
        $this->buildOnDuplicateKeyUpdate();
        $this->built = true;
        return $this;
    }
}