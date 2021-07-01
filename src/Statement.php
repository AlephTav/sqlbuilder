<?php

namespace AlephTools\SqlBuilder;

interface Statement
{
    public function getParams(): array;

    public function toSql(): string;

    /**
     * @return static
     */
    public function copy();

    public function clean(): void;
}