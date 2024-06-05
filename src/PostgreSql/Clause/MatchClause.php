<?php

declare(strict_types=1);

namespace AlephTools\SqlBuilder\PostgreSql\Clause;

use AlephTools\SqlBuilder\PostgreSql\InsertStatement;
use AlephTools\SqlBuilder\PostgreSql\UpdateStatement;
use AlephTools\SqlBuilder\Sql\Expression\ConditionalExpression;

class MatchItem {
    public function __construct(
        public ?ConditionalExpression $condition,
        public string|InsertStatement|UpdateStatement $statement,
        public bool $matched
    ){}
}

trait MatchClause
{
    /**
     * @var MatchItem[]
     */
    protected array $matches = [];

    public function whenMatched(): static
    {
        return $this->when(true);
    }

    public function whenMatchedThenDelete(): static
    {
        return $this->when(true)->thenDelete();
    }

    public function whenMatchedThenDoNothing(): static
    {
        return $this->when(true)->thenDoNothing();
    }

    public function whenNotMatched(): static
    {
        return $this->when(false);
    }

    public function whenNotMatchedThenDoNothing(): static
    {
        return $this->when(false)->thenDoNothing();
    }

    public function whenMatchedAnd(mixed $column, mixed $operator = null, mixed $value = null): static
    {
        return $this->when(true, $column, $operator, $value);
    }

    public function whenNotMatchedAnd(mixed $column, mixed $operator = null, mixed $value = null): static
    {
        return $this->when(false, $column, $operator, $value);
    }

    protected function when(
        bool $matched,
        mixed $column = null,
        mixed $operator = null,
        mixed $value = null,
    ): static {
        $this->matches[] = new MatchItem(
            $column !== null ? new ConditionalExpression($column, $operator, $value) : null,
            '',
            $matched,
        );
        return $this;
    }

    public function thenDoNothing(): static
    {
        if ($match = $this->lastMatch()) {
            $match->statement = 'DO NOTHING';
        }
        return $this;
    }

    public function thenDelete(): static
    {
        if ($match = $this->lastMatch()) {
            $match->statement = 'DELETE';
        }
        return $this;
    }

    public function thenInsert(InsertStatement $statement): static
    {
        return $this->then($statement);
    }

    public function thenUpdate(UpdateStatement $statement): static
    {
        return $this->then($statement);
    }

    public function then(InsertStatement|UpdateStatement $statement): static
    {
        if ($match = $this->lastMatch()) {
            $match->statement = $statement;
        }
        return $this;
    }

    private function lastMatch(): ?MatchItem
    {
        $count = count($this->matches);
        if ($count === 0) {
            return null;
        }
        return $this->matches[$count - 1];
    }

    protected function cloneMatches(mixed $copy): void
    {
        $copy->matches = [];
        foreach ($this->matches as $match) {
            $copy->matches[] = new MatchItem(
                $match->condition !== null ? clone $match->condition : null,
                clone $match->statement,
                $match->matched,
            );
        }
    }

    public function cleanMatches(): void
    {
        $this->matches = [];
    }

    protected function buildMatches(): void
    {
        foreach ($this->matches as $match) {
            $this->sql .= ' WHEN';
            if (!$match->matched) {
                $this->sql .= ' NOT';
            }
            $this->sql .= ' MATCHED';
            if ($match->condition) {
                $this->sql .= " AND $match->condition";
                $this->addParams($match->condition->getParams());
            }
            $this->sql .= " THEN $match->statement";
            if (!is_string($match->statement)) {
                $this->addParams($match->statement->getParams());
            }
        }
    }
}