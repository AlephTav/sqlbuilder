<?php

declare(strict_types=1);

namespace AlephTools\SqlBuilder\Clickhouse\Clause;

use AlephTools\SqlBuilder\Sql\Expression\ValueListExpression;

trait SettingsClause
{
    protected ?ValueListExpression $settings = null;

    public function settings(mixed $values): static
    {
        $this->settings ??= new ValueListExpression();
        $this->settings->append($values);
        $this->built = false;
        return $this;
    }

    protected function cloneSettings(mixed $copy): void
    {
        $copy->settings = $this->settings ? clone $this->settings : null;
    }

    public function cleanSettings(): void
    {
        $this->settings = null;
    }

    protected function buildSettings(): void
    {
        if ($this->settings !== null) {
            $this->sql .= " SETTINGS $this->settings";
            $this->addParams($this->settings->getParams());
        }
    }
}
