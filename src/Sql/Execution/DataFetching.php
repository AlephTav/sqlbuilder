<?php

declare(strict_types=1);

namespace AlephTools\SqlBuilder\Sql\Execution;

use RuntimeException;

trait DataFetching
{
    /**
     * @psalm-return list<array<string,mixed>>
     */
    public function rows(): array
    {
        return $this->db()->rows($this->toSql(), $this->getParams());
    }

    public function pairs(string $key = ''): array
    {
        $rows = $this->rows();
        if ($rows && $key !== '' && !array_key_exists($key, $rows[0])) {
            throw new RuntimeException("Key \"$key\" is not found in the row set.");
        }
        $result = [];
        if ($key === '') {
            foreach ($rows as $row) {
                $key = array_shift($row);
                $value = array_shift($row);
                $result[$key] = $value;
            }
        } else {
            foreach ($rows as $row) {
                $pairKey = $row[$key];
                unset($row[$key]);
                $value = array_shift($row);
                $result[$pairKey] = $value;
            }
        }
        return $result;
    }

    public function rowsByKey(string $key, bool $removeKeyFromRow = false): array
    {
        $rows = $this->rows();
        if ($rows && !array_key_exists($key, $rows[0])) {
            throw new RuntimeException("Key \"$key\" is not found in the row set.");
        }
        $result = [];
        if ($removeKeyFromRow) {
            foreach ($rows as $row) {
                $keyValue = $row[$key];
                unset($row[$key]);
                $result[$keyValue] = $row;
            }
        } else {
            foreach ($rows as $row) {
                $result[$row[$key]] = $row;
            }
        }
        return $result;
    }

    public function rowsByGroup(string $key, bool $removeKeyFromRow = false): array
    {
        $rows = $this->rows();
        if ($rows && !array_key_exists($key, $rows[0])) {
            throw new RuntimeException("Key \"$key\" is not found in the row set.");
        }
        $result = [];
        if ($removeKeyFromRow) {
            foreach ($rows as $row) {
                $keyValue = $row[$key];
                unset($row[$key]);
                $result[$keyValue][] = $row;
            }
        } else {
            foreach ($rows as $row) {
                $result[$row[$key]][] = $row;
            }
        }
        return $result;
    }

    public function row(): array
    {
        return $this->db()->row($this->toSql(), $this->getParams());
    }

    public function column(): array
    {
        return $this->db()->column($this->toSql(), $this->getParams());
    }

    /**
     * @return mixed
     */
    public function scalar()
    {
        return $this->db()->scalar($this->toSql(), $this->getParams());
    }
}
