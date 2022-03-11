<?php

declare(strict_types=1);

namespace AlephTools\SqlBuilder\PostgreSql;

use AlephTools\SqlBuilder\PostgreSql\Clause\ConflictClause;
use AlephTools\SqlBuilder\PostgreSql\Clause\InsertClause;
use AlephTools\SqlBuilder\PostgreSql\Clause\ValueListClause;
use AlephTools\SqlBuilder\Sql\AbstractInsertStatement;
use AlephTools\SqlBuilder\Sql\Clause\ReturningClause;
use AlephTools\SqlBuilder\Sql\Clause\WithClause;
use AlephTools\SqlBuilder\Sql\Execution\DataFetching;

class InsertStatement extends AbstractInsertStatement
{
    use WithClause;
    use InsertClause;
    use ValueListClause;
    use ConflictClause;
    use ReturningClause;
    use DataFetching;

    public function copy(): static
    {
        $copy = new static($this->db);
        $this->cloneWith($copy);
        $this->cloneInsert($copy);
        $this->cloneColumns($copy);
        $this->cloneValueList($copy);
        $this->cloneQuery($copy);
        $this->cloneConflict($copy);
        $this->cloneReturning($copy);
        return $copy;
    }

    public function clean(): static
    {
        $this->cleanWith();
        $this->cleanInsert();
        $this->cleanColumns();
        $this->cleanValueList();
        $this->cleanQuery();
        $this->cleanConflict();
        $this->cleanReturning();
        return $this;
    }

    public function build(): static
    {
        if ($this->built) {
            return $this;
        }
        $this->sql = '';
        $this->params = [];
        $this->buildWith();
        $this->buildInsert();
        $this->buildColumns();
        $this->buildValues();
        $this->buildQuery();
        $this->buildOnConflictDoUpdate();
        $this->buildReturning();
        $this->built = true;
        return $this;
    }
}
