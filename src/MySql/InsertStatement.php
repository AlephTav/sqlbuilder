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

    public function copy(): static
    {
        $copy = new static($this->db);
        $this->cloneInsert($copy);
        $this->clonePartition($copy);
        $this->cloneColumns($copy);
        $this->cloneRowAlias($copy);
        $this->cloneValueList($copy);
        $this->cloneQuery($copy);
        $this->cloneAssignment($copy);
        $this->cloneDuplicateKey($copy);
        return $copy;
    }

    public function clean(): static
    {
        $this->cleanInsert();
        $this->cleanPartition();
        $this->cleanColumns();
        $this->cleanRowAlias();
        $this->cleanValueList();
        $this->cleanQuery();
        $this->cleanAssignment();
        $this->cleanDuplicateKey();
        return $this;
    }

    public function build(): static
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
