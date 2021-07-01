<?php

declare(strict_types=1);

namespace AlephTools\SqlBuilder\MySql;

use AlephTools\SqlBuilder\MySql\Clause\DuplicateKeyClause;
use AlephTools\SqlBuilder\MySql\Clause\InsertClause;
use AlephTools\SqlBuilder\MySql\Clause\PartitionClause;
use AlephTools\SqlBuilder\MySql\Clause\RowAliasClause;
use AlephTools\SqlBuilder\MySql\Clause\ValueListClause;
use AlephTools\SqlBuilder\Sql\AbstractInsertStatement;
use AlephTools\SqlBuilder\Sql\Clause\AssignmentClause;

class InsertStatement extends AbstractInsertStatement
{
    use InsertClause;
    use PartitionClause;
    use RowAliasClause;
    use ValueListClause;
    use AssignmentClause;
    use DuplicateKeyClause;

    /**
     * @return static
     */
    public function copy()
    {
        $copy = new static();
        $copy->modifiers = $this->modifiers;
        $copy->table = $this->table ? clone $this->table : null;
        $copy->partition = $this->partition ? clone $this->partition : null;
        $copy->columns = $this->columns ? clone $this->columns : null;
        $copy->rowAlias = $this->rowAlias;
        $copy->columnAliases = $this->columnAliases ? clone $this->columnAliases : null;
        $copy->values = $this->values ? clone $this->values : null;
        $copy->query = $this->query ? $this->query->copy() : null;
        $copy->assignment = $this->assignment ? clone $this->assignment : $this->assignment;
        $copy->assignmentOnUpdate = $this->assignmentOnUpdate ?
            clone $this->assignmentOnUpdate : $this->assignmentOnUpdate;
        return $copy;
    }

    public function clean(): void
    {
        $this->modifiers = '';
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
