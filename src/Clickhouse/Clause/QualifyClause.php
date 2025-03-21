<?php

declare(strict_types=1);

namespace AlephTools\SqlBuilder\Clickhouse\Clause;

use AlephTools\SqlBuilder\Sql\Expression\ValueListExpression;

trait QualifyClause
{
    protected ?ValueListExpression $qualify = null;

    public function qualify(mixed $value): static
    {
        $this->qualify ??= new ValueListExpression();
        $this->qualify->append($value);
        $this->built = false;
        return $this;
    }

    protected function cloneQualify(mixed $copy): void
    {
        $copy->qualify = $this->qualify ? clone $this->qualify : null;
    }

    public function cleanQualify(): void
    {
        $this->qualify = null;
    }

    protected function buildQualify(): void
    {
        if ($this->qualify) {
            $this->sql .= " QUALIFY $this->qualify";
            $this->addParams($this->qualify->getParams());
        }
    }
}
