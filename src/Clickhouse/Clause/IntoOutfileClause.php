<?php

declare(strict_types=1);

namespace AlephTools\SqlBuilder\Clickhouse\Clause;

trait IntoOutfileClause
{
    protected ?string $fileName = null;
    protected bool $existed = false;
    protected bool $stdout = false;
    protected bool $truncate = false;
    protected ?string $compression = null;
    protected ?int $compressionLevel = null;

    public function intoOutfile(
        string $file,
        bool $withStdout = false,
        ?string $compression = null,
        ?int $compressionLevel = null
    ): static {
        $this->existed = false;
        $this->fileName = $file;
        $this->stdout = $withStdout;
        $this->compression = $compression;
        $this->compressionLevel = $compressionLevel;
        $this->built = false;

        return $this;
    }

    public function intoExistedOutfile(
        string $file,
        bool $withStdout = false,
        bool $truncate = false,
        ?string $compression = null,
        ?int $compressionLevel = null
    ): static {
        $this->existed = true;
        $this->fileName = $file;
        $this->stdout = $withStdout;
        $this->truncate = $truncate;
        $this->compression = $compression;
        $this->compressionLevel = $compressionLevel;
        $this->built = false;

        return $this;
    }

    protected function cloneIntoOutfile(mixed $copy): void
    {
        $copy->existed = $this->existed;
        $copy->fileName = $this->fileName;
        $copy->stdout = $this->stdout;
        $copy->truncate = $this->truncate;
        $copy->compression = $this->compression;
        $copy->compressionLevel = $this->compressionLevel;
    }

    public function cleanIntoOutfile(): void
    {
        $this->fileName = null;
        $this->stdout = false;
        $this->truncate = false;
        $this->compression = null;
        $this->compressionLevel = null;
    }

    protected function buildIntoOutfile(): void
    {
        if ($this->fileName !== null) {
            $this->sql .= " INTO OUTFILE '$this->fileName'";
            if ($this->stdout) {
                $this->sql .= " AND STDOUT";
            }
            if ($this->existed) {
                if ($this->truncate) {
                    $this->sql .= " TRUNCATE";
                } else {
                    $this->sql .= " APPEND";
                }
            }
            if ($this->compression !== null) {
                $this->sql .= " COMPRESSION $this->compression";
                if ($this->compressionLevel !== null) {
                    $this->sql .= " LEVEL $this->compressionLevel";
                }
            }
        }
    }
}
