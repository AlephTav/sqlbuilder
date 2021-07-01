<?php

declare(strict_types=1);

namespace AlephTools\SqlBuilder\MySql\Clause;

use AlephTools\SqlBuilder\Sql\Clause\DeleteClause as BaseDeleteClause;

trait DeleteClause
{
    use BaseDeleteClause;

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
    public function quick()
    {
        return $this->modifier('QUICK');
    }

    /**
     * @return static
     */
    public function ignore()
    {
        return $this->modifier('IGNORE');
    }

    /**
     * @param string $modifier
     * @return static
     */
    public function modifier(string $modifier)
    {
        $this->modifiers .= " $modifier";
        $this->built = true;
        return $this;
    }

    protected function buildDelete(): void
    {
        $this->sql .= 'DELETE';
        if (strlen($this->modifiers)) {
            $this->sql .= " $this->modifiers";
        }
        if ($this->from) {
            $this->sql .= " FROM $this->from";
            $this->addParams($this->from->getParams());
        }
    }
}
