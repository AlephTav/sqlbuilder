<?php

declare(strict_types=1);

namespace AlephTools\SqlBuilder\PostgreSql;

use AlephTools\SqlBuilder\Command;
use AlephTools\SqlBuilder\PostgreSql\Clause\MatchClause;
use AlephTools\SqlBuilder\PostgreSql\Clause\MergeClause;
use AlephTools\SqlBuilder\PostgreSql\Clause\OnClause;
use AlephTools\SqlBuilder\Sql\AbstractStatement;
use AlephTools\SqlBuilder\Sql\Clause\UsingClause;
use AlephTools\SqlBuilder\Sql\Clause\WithClause;
use AlephTools\SqlBuilder\Sql\Execution\StatementExecution;

class MergeStatement extends AbstractStatement implements Command
{
    use WithClause;
    use MergeClause;
    use UsingClause;
    use OnClause;
    use MatchClause;
    use StatementExecution;

    public function copy(): static
    {
        $copy = new static($this->db);
        $this->cloneWith($copy);
        $this->cloneMerge($copy);
        $this->cloneUsing($copy);
        $this->cloneOn($copy);
        $this->cloneMatches($copy);
        return $this;
    }

    public function clean(): static
    {
        $this->cleanWith();
        $this->cleanMerge();
        $this->cleanUsing();
        $this->cleanOn();
        $this->cleanMatches();
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
        $this->buildMerge();
        $this->buildUsing();
        $this->buildOn();
        $this->buildMatches();
        $this->built = true;
        return $this;
    }
}