<?php

declare(strict_types=1);

namespace AlephTools\SqlBuilder\Sql;

use AlephTools\SqlBuilder\Command;
use AlephTools\SqlBuilder\Query;
use AlephTools\SqlBuilder\Sql\Clause\ColumnsClause;
use AlephTools\SqlBuilder\Sql\Clause\InsertClause;
use AlephTools\SqlBuilder\Sql\Clause\QueryClause;
use AlephTools\SqlBuilder\Sql\Clause\ValueListClause;
use AlephTools\SqlBuilder\Sql\Expression\ColumnListExpression;
use AlephTools\SqlBuilder\Sql\Expression\FromExpression;
use AlephTools\SqlBuilder\Sql\Expression\ValueListExpression;
use AlephTools\SqlBuilder\StatementExecutor;

abstract class AbstractInsertStatement extends AbstractStatement implements Command
{
    use InsertClause;
    use ColumnsClause;
    use ValueListClause;
    use QueryClause;

    public function __construct(
        StatementExecutor $db = null,
        FromExpression $table = null,
        ColumnListExpression $columns = null,
        ValueListExpression $values = null,
        Query $query = null
    ) {
        parent::__construct($db);
        $this->table = $table;
        $this->columns = $columns;
        $this->values = $values;
        $this->query = $query;
    }

    /**
     * Executes this insert statement.
     *
     * @param string|null $sequence Name of the sequence object from which the ID should be returned.
     * @return mixed Returns the ID of the last inserted row or sequence value.
     */
    public function exec(string $sequence = null)
    {
        $this->validateAndBuild();
        return $this->db->insert($this->toSql(), $this->getParams(), $sequence);
    }
}
