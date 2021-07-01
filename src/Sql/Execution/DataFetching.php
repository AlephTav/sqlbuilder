<?php

declare(strict_types=1);

namespace AlephTools\SqlBuilder\Sql\Execution;

use AlephTools\SqlBuilder\StatementExecutor;
use RuntimeException;

trait DataFetching
{
    public function rows(StatementExecutor $db): array
    {
        return $db->rows($this->toSql(), $this->getParams());
    }

    public function pairs(StatementExecutor $db, string $key = ''): array
    {
        $rows = $this->rows($db);
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

    public function rowsByKey(StatementExecutor $db, string $key, bool $removeKeyFromRow = false): array
    {
        $rows = $this->rows($db);
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

    public function rowsByGroup(StatementExecutor $db, string $key, bool $removeKeyFromRow = false): array
    {
        $rows = $this->rows($db);
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

    public function row(StatementExecutor $db): array
    {
        return $db->row($this->toSql(), $this->getParams());
    }

    public function column(StatementExecutor $db): array
    {
        return $db->column($this->toSql(), $this->getParams());
    }

    /**
     * @return mixed
     */
    public function scalar(StatementExecutor $db)
    {
        return $db->scalar($this->toSql(), $this->getParams());
    }
}
