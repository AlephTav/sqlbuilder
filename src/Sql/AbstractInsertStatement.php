<?php

declare(strict_types=1);

namespace AlephTools\SqlBuilder\Sql;

use AlephTools\SqlBuilder\Command;
use AlephTools\SqlBuilder\Sql\Clause\ColumnsClause;
use AlephTools\SqlBuilder\Sql\Clause\InsertClause;
use AlephTools\SqlBuilder\Sql\Clause\QueryClause;
use AlephTools\SqlBuilder\Sql\Clause\ValueListClause;

abstract class AbstractInsertStatement extends AbstractStatement implements Command
{
    use InsertClause;
    use ColumnsClause;
    use ValueListClause;
    use QueryClause;

    /**
     * Executes this insert statement.
     *
     * @param string|null $sequence Name of the sequence object from which the ID should be returned.
     * @return mixed Returns the ID of the last inserted row or sequence value.
     */
    public function exec(string $sequence = null): mixed
    {
        return $this->db()->insert($this->toSql(), $this->getParams(), $sequence);
    }
}
