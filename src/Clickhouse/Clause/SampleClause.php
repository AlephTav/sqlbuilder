<?php

declare(strict_types=1);

namespace AlephTools\SqlBuilder\Clickhouse\Clause;

trait SampleClause
{
    protected float|int|null $sample = null;
    protected ?float $sampleOffset = null;

    public function sample(float|int $sample, ?float $offset = null): static
    {
        $this->sample = $sample;
        $this->sampleOffset = $offset;
        $this->built = false;
        return $this;
    }

    protected function cloneSample(mixed $copy): void
    {
        $copy->sample = $this->sample;
        $copy->sampleOffset = $this->sampleOffset;
    }

    public function cleanSample(): void
    {
        $this->sample = null;
        $this->sampleOffset = null;
    }

    protected function buildSample(): void
    {
        if ($this->sample !== null) {
            $this->sql .= " SAMPLE $this->sample";
            if ($this->sampleOffset !== null) {
                $this->sql .= " OFFSET $this->sampleOffset";
            }
        }
    }
}
