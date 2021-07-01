<?php

declare(strict_types=1);

namespace AlephTools\SqlBuilder\MySql\Clause;

use AlephTools\SqlBuilder\Sql\Clause\UpdateClause as BaseUpdateClause;

trait UpdateClause
{
    use BaseUpdateClause;

    protected ?string $modifiers = null;

    /**
     * @return static
     */
    public function lowPriority()
    {
        return $this->modifier('LOW_PRIORITY');
    }

    /**
     * @return static
     */
    public function ignore()
    {
        return $this->modifier('IGNORE');
    }

    /**
     * @return static
     */
    public function modifier(string $modifier)
    {
        $this->modifiers .= " $modifier";
        $this->built = true;
        return $this;
    }

    protected function buildUpdate(): void
    {
        $this->sql .= 'UPDATE';
        if (strlen($this->modifiers)) {
            $this->sql .= " $this->modifiers";
        }
        if ($this->table) {
            $this->sql .= " $this->table";
            $this->addParams($this->table->getParams());
        }
    }
}
