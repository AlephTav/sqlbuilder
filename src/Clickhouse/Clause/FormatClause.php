<?php

declare(strict_types=1);

namespace AlephTools\SqlBuilder\Clickhouse\Clause;

trait FormatClause
{
    protected ?string $format = null;

    public function format(mixed $format): static
    {
        $this->format = $format;
        $this->built = false;
        return $this;
    }

    public function cleanFormat(): void
    {
        $this->format = null;
    }

    protected function cloneFormat(mixed $copy): void
    {
        $copy->format = $this->format;
    }

    protected function buildFormat(): void
    {
        if ($this->format !== null) {
            $this->sql .= " FORMAT $this->format";
        }
    }
}
