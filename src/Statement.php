<?php

declare(strict_types=1);

namespace AlephTools\SqlBuilder;

interface Statement
{
    public function getParams(): array;

    public function toSql(): string;

    public function copy(): static;

    public function clean(): static;

    public function __toString(): string;
}
