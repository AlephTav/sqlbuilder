<?php

declare(strict_types=1);

namespace AlephTools\SqlBuilder\PostgreSql\Clause;

use AlephTools\SqlBuilder\Sql\Clause\ValueListClause as BaseValueListClause;

trait ValueListClause
{
    use BaseValueListClause;

    protected string $overriding = '';

    public function overridingSystemValue(): static
    {
        $this->overriding = 'OVERRIDING SYSTEM VALUE';
        return $this;
    }

    public function overridingUserValue(): static
    {
        $this->overriding = 'OVERRIDING USER VALUE';
        return $this;
    }

    protected function cloneValueList(mixed $copy): void
    {
        parent::cloneValueList($copy);
        $copy->overriding = $this->overriding;
    }

    public function cleanValueList(): void
    {
        parent::cleanValueList();
        $this->overriding = '';
    }


    protected function buildValues(): void
    {
        if (isset($this->query)) {
            return;
        }
        if ($this->overriding !== '') {
            $this->sql .= " $this->overriding";
        }
        if ($this->values) {
            $this->sql .= " VALUES $this->values";
            $this->addParams($this->values->getParams());
        } else {
            $this->sql .= ' DEFAULT VALUES';
        }
    }
}
