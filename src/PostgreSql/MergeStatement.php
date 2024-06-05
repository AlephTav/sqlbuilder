<?php

namespace AlephTools\SqlBuilder\PostgreSql;

use AlephTools\SqlBuilder\Command;
use AlephTools\SqlBuilder\PostgreSql\Clause\MatchClause;
use AlephTools\SqlBuilder\PostgreSql\Clause\MergeClause;
use AlephTools\SqlBuilder\PostgreSql\Clause\OnClause;
use AlephTools\SqlBuilder\Sql\AbstractStatement;
use AlephTools\SqlBuilder\Sql\Clause\UsingClause;
use AlephTools\SqlBuilder\Sql\Clause\WithClause;

class MergeStatement extends AbstractStatement implements Command
{
    use MergeClause;
    use UsingClause;
    use OnClause;
    use MatchClause;
    use WithClause;

    public function copy(): static
    {
        $copy = new static($this->db);
        $this->cloneWith($copy);
        $this->cloneMerge($copy);
        $this->cloneUsing($copy);
        $this->cloneOn($copy);
        $this->cloneWhenMatched($copy);
        return $this;
    }

    public function clean(): static
    {
        $this->cleanWith();
        $this->cleanMerge();
        $this->cleanUsing();
        $this->cleanOn();
        $this->cleanWhenMatched();
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
        $this->buildWhenMatched();
        $this->built = true;

        return $this;
    }
}