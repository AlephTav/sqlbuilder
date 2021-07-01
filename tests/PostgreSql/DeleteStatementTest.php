<?php

declare(strict_types=1);

namespace Tests\AlephTools\SqlBuilder\PostgreSql;

use AlephTools\SqlBuilder\PostgreSql\DeleteStatement;
use AlephTools\SqlBuilder\PostgreSql\SelectStatement;
use AlephTools\SqlBuilder\Sql\Expression\AbstractExpression;
use AlephTools\SqlBuilder\Sql\Expression\ConditionalExpression;
use AlephTools\SqlBuilder\Sql\Expression\RawExpression;
use AlephTools\SqlBuilder\StatementExecutor;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
class DeleteStatementTest extends TestCase
{
    protected function setUp(): void
    {
        AbstractExpression::resetParameterIndex();
    }

    /**
     * @test
     */
    public function emptyDelete(): void
    {
        $st = new DeleteStatement();

        self::assertSame('DELETE FROM', $st->toSql());
        self::assertEmpty($st->getParams());
    }

    //region FROM

    /**
     * @test
     */
    public function fromTable(): void
    {
        $st = (new DeleteStatement())
            ->from('tb');

        self::assertSame('DELETE FROM tb', $st->toSql());
        self::assertEmpty($st->getParams());
    }

    /**
     * @test
     */
    public function fromTableWithAlias(): void
    {
        $st = (new DeleteStatement())
            ->from('tb', 't');

        self::assertSame('DELETE FROM tb t', $st->toSql());
        self::assertEmpty($st->getParams());
    }

    /**
     * @test
     */
    public function fromTables(): void
    {
        $st = (new DeleteStatement())
            ->from(['t1', 't2']);

        self::assertSame('DELETE FROM t1, t2', $st->toSql());
        self::assertEmpty($st->getParams());
    }

    /**
     * @test
     */
    public function fromTablesWithAliases(): void
    {
        $st = (new DeleteStatement())
            ->from(['a' => 't1', 'b' => 't2']);

        self::assertSame('DELETE FROM t1 a, t2 b', $st->toSql());
        self::assertEmpty($st->getParams());
    }

    /**
     * @test
     */
    public function fromRawExpression(): void
    {
        $st = (new DeleteStatement())
            ->from(new RawExpression('tb AS t'));

        self::assertSame('DELETE FROM tb AS t', $st->toSql());
        self::assertEmpty($st->getParams());
    }

    /**
     * @test
     */
    public function fromOnly(): void
    {
        $st = (new DeleteStatement())
            ->fromOnly('tb');

        self::assertSame('DELETE FROM ONLY tb', $st->toSql());
        self::assertEmpty($st->getParams());
    }

    /**
     * @test
     */
    public function fromOnlyWithAlias(): void
    {
        $st = (new DeleteStatement())
            ->fromOnly('tb', 't');

        self::assertSame('DELETE FROM ONLY tb t', $st->toSql());
        self::assertEmpty($st->getParams());
    }

    //endregion

    //region USING

    /**
     * @test
     */
    public function usingTable(): void
    {
        $st = (new DeleteStatement())
            ->from('tb')
            ->using('t');

        self::assertSame('DELETE FROM tb USING t', $st->toSql());
        self::assertEmpty($st->getParams());
    }

    /**
     * @test
     */
    public function usingTableWithAlias(): void
    {
        $st = (new DeleteStatement())
            ->from('tb')
            ->using('t1', 't');

        self::assertSame('DELETE FROM tb USING t1 t', $st->toSql());
        self::assertEmpty($st->getParams());
    }

    /**
     * @test
     */
    public function usingTables(): void
    {
        $st = (new DeleteStatement())
            ->from('tb')
            ->using(['t1', 't2']);

        self::assertSame('DELETE FROM tb USING t1, t2', $st->toSql());
        self::assertEmpty($st->getParams());
    }

    /**
     * @test
     */
    public function usingTablesWithAliases(): void
    {
        $st = (new DeleteStatement())
            ->from('tb')
            ->using(['a' => 't1', 'b' => 't2']);

        self::assertSame('DELETE FROM tb USING t1 a, t2 b', $st->toSql());
        self::assertEmpty($st->getParams());
    }

    /**
     * @test
     */
    public function usingRawExpression(): void
    {
        $st = (new DeleteStatement())
            ->from('tb')
            ->using(new RawExpression('t1 AS t'));

        self::assertEquals('DELETE FROM tb USING t1 AS t', $st->toSql());
        self::assertEmpty($st->getParams());
    }

    //endregion

    //region WHERE

    /**
     * @test
     */
    public function whereAsString(): void
    {
        $st = (new DeleteStatement())
            ->from('tb')
            ->where('c1 = c2');

        self::assertSame('DELETE FROM tb WHERE c1 = c2', $st->toSql());
        self::assertEmpty($st->getParams());
    }

    /**
     * @test
     */
    public function whereAsRawValue(): void
    {
        $st = (new DeleteStatement())
            ->from('tb')
            ->where(new RawExpression('c1 = c2'));

        self::assertSame('DELETE FROM tb WHERE c1 = c2', $st->toSql());
        self::assertEmpty($st->getParams());
    }

    /**
     * @test
     */
    public function whereBinaryOpWithScalar(): void
    {
        $st = (new DeleteStatement())
            ->from('tb')
            ->where('col', '=', 1);

        self::assertSame('DELETE FROM tb WHERE col = :p1', $st->toSql());
        self::assertSame(['p1' => 1], $st->getParams());
    }

    /**
     * @test
     */
    public function whereBinaryOpWithNull(): void
    {
        $st = (new DeleteStatement())
            ->from('tb')
            ->where('col', '=', null);

        self::assertSame('DELETE FROM tb WHERE col = NULL', $st->toSql());
        self::assertEmpty($st->getParams());
    }

    /**
     * @test
     */
    public function whereBinaryOpWithQuery(): void
    {
        $st = (new DeleteStatement())
            ->from('t1')
            ->where('t1.col', '=', (new SelectStatement())->from('t2')->select('COUNT(*)'));

        self::assertSame('DELETE FROM t1 WHERE t1.col = (SELECT COUNT(*) FROM t2)', $st->toSql());
        self::assertEmpty($st->getParams());
    }

    /**
     * @test
     */
    public function whereBinaryOpWithList(): void
    {
        $st = (new DeleteStatement())
            ->from('tb')
            ->where('col', 'IN', [1, 2, 3]);

        self::assertSame('DELETE FROM tb WHERE col IN (:p1, :p2, :p3)', $st->toSql());
        self::assertSame(
            [
                'p1' => 1,
                'p2' => 2,
                'p3' => 3,
            ],
            $st->getParams()
        );
    }

    /**
     * @test
     */
    public function whereBinaryOpBetween(): void
    {
        $st = (new DeleteStatement())
            ->from('tb')
            ->where('col', 'BETWEEN', [1, 2]);

        self::assertSame('DELETE FROM tb WHERE col BETWEEN :p1 AND :p2', $st->toSql());
        self::assertSame(['p1' => 1, 'p2' => 2], $st->getParams());
    }

    /**
     * @test
     */
    public function whereBinaryOpWithRawValue(): void
    {
        $st = (new DeleteStatement())
            ->from('tb')
            ->where('c1', '=', new RawExpression('c2'));

        self::assertSame('DELETE FROM tb WHERE c1 = c2', $st->toSql());
        self::assertEmpty($st->getParams());
    }

    /**
     * @test
     */
    public function whereUnaryOpWithScalar(): void
    {
        $st = (new DeleteStatement())
            ->from('tb')
            ->where('NOT', new RawExpression('col'));

        self::assertSame('DELETE FROM tb WHERE NOT col', $st->toSql());
        self::assertEmpty($st->getParams());
    }

    /**
     * @test
     */
    public function whereUnaryOpWithQuery(): void
    {
        $st = (new DeleteStatement())
            ->from('t1')
            ->where('NOT', (new SelectStatement())->from('t2')->select('COUNT(*)'));

        self::assertSame('DELETE FROM t1 WHERE NOT (SELECT COUNT(*) FROM t2)', $st->toSql());
        self::assertEmpty($st->getParams());
    }

    /**
     * @test
     */
    public function whereWithQueryAsOperand(): void
    {
        $st = (new DeleteStatement())
            ->from('t1')
            ->where((new SelectStatement())->from('t2')->select('COUNT(*)'), '>', 5);

        self::assertSame('DELETE FROM t1 WHERE (SELECT COUNT(*) FROM t2) > :p1', $st->toSql());
        self::assertSame(['p1' => 5], $st->getParams());
    }

    /**
     * @test
     */
    public function whereWithQueriesAsOperandAndValue(): void
    {
        $st = (new DeleteStatement())
            ->from('t1')
            ->where(
                (new SelectStatement())->from('t2')->select('COUNT(*)'),
                '<>',
                (new SelectStatement())->from('t3')->select('COUNT(*)')
            );

        self::assertSame(
            'DELETE FROM t1 WHERE (SELECT COUNT(*) FROM t2) <> (SELECT COUNT(*) FROM t3)',
            $st->toSql()
        );
        self::assertEmpty($st->getParams());
    }

    /**
     * @test
     */
    public function whereAsConditionList(): void
    {
        $st = (new DeleteStatement())
            ->from('tb')
            ->where(['c1 = c2', 'c3 <> c4']);

        self::assertSame('DELETE FROM tb WHERE c1 = c2 AND c3 <> c4', $st->toSql());
        self::assertEmpty($st->getParams());
    }

    /**
     * @test
     */
    public function whereAsConditionMap(): void
    {
        $st = (new DeleteStatement())
            ->from('tb')
            ->where(['c1' => 1, 'c2' => 2]);

        self::assertSame('DELETE FROM tb WHERE c1 = :p1 AND c2 = :p2', $st->toSql());
        self::assertSame(['p1' => 1, 'p2' => 2], $st->getParams());
    }

    /**
     * @test
     */
    public function whereWithNestedConditionsAsConditionalExpression(): void
    {
        $st = (new DeleteStatement())
            ->from('tb')
            ->where('c1 IS NULL')
            ->where(
                (new ConditionalExpression())
                ->orWhere('c2', '=', 1)
                ->orWhere('c3', '<', 2)
            );

        self::assertSame('DELETE FROM tb WHERE c1 IS NULL AND (c2 = :p1 OR c3 < :p2)', $st->toSql());
        self::assertSame(['p1' => 1, 'p2' => 2], $st->getParams());
    }

    /**
     * @test
     */
    public function whereWithNestedConditionsAsClosure(): void
    {
        $st = (new DeleteStatement())
            ->from('tb')
            ->where('c1 IS NULL')
            ->where(function (ConditionalExpression $condition): void {
                $condition->orWhere('c2', '=', 1)
                    ->orWhere('c3', '<', 2);
            });

        self::assertSame('DELETE FROM tb WHERE c1 IS NULL AND (c2 = :p1 OR c3 < :p2)', $st->toSql());
        self::assertSame(['p1' => 1, 'p2' => 2], $st->getParams());
    }

    //endregion

    //region RETURNING

    /**
     * @test
     */
    public function returningAllColumns(): void
    {
        $st = (new DeleteStatement())
            ->from('tb')
            ->returning();

        self::assertSame('DELETE FROM tb RETURNING *', $st->toSql());
        self::assertEmpty($st->getParams());
    }

    /**
     * @test
     */
    public function returningSpecificColumns(): void
    {
        $st = (new DeleteStatement())
            ->from('tb')
            ->returning('c1')
            ->returning('c2', null)
            ->returning('col', 'c3');

        self::assertSame('DELETE FROM tb RETURNING c1, c2, col c3', $st->toSql());
        self::assertEmpty($st->getParams());
    }

    /**
     * @test
     */
    public function returningColumnList(): void
    {
        $st = (new DeleteStatement())
            ->from('tb')
            ->returning(['c1', 'c2', 'c3']);

        self::assertSame('DELETE FROM tb RETURNING c1, c2, c3', $st->toSql());
        self::assertEmpty($st->getParams());
    }

    public function returningColumnListWithAliases(): void
    {
        $st = (new DeleteStatement())
            ->from('tb')
            ->returning(['a' => 'c1', 'b' => 'c2', 'c' => 'c3']);

        self::assertEquals('DELETE FROM tb RETURNING c1 a, c2 b, c3 c', $st->toSql());
        self::assertEmpty($st->getParams());
    }

    //endregion

    //region WITH

    /**
     * @test
     */
    public function withSimpleQuery(): void
    {
        $st = (new DeleteStatement())
            ->with((new SelectStatement())->from('t1'), 'tb')
            ->from('tb');

        self::assertSame('WITH tb AS (SELECT * FROM t1) DELETE FROM tb', $st->toSql());
        self::assertEmpty($st->getParams());
    }

    /**
     * @test
     */
    public function withRawExpression(): void
    {
        $st = (new DeleteStatement())
            ->with('(SELECT * FROM t1)', 'tb')
            ->with([new RawExpression('n1 AS NULL')])
            ->with(null, new RawExpression('n2'))
            ->from('tb');

        self::assertSame(
            'WITH tb AS (SELECT * FROM t1), n1 AS NULL, n2 AS NULL DELETE FROM tb',
            $st->toSql()
        );
        self::assertEmpty($st->getParams());
    }

    //endregion

    //region Query Execution

    /**
     * @test
     */
    public function validateExecutorInstance(): void
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('The statement executor must not be null.');

        (new DeleteStatement())
            ->with((new SelectStatement())->from('t1'), 'tb')
            ->from("tb")
            ->where("c1", ">", 5)
            ->orWhere("c2", "<", 0)
            ->exec();
    }

    /**
     * @test
     */
    public function exec(): void
    {
        $executor = $this->getMockBuilder(StatementExecutor::class)->getMock();

        $st = (new DeleteStatement($executor))
            ->from('tb')
            ->where('c1', '>', 5)
            ->orWhere('c2', '<', 0);

        $executor->method('execute')->willReturnCallback(function (string $sql, array $params) use ($st) {
            $this->assertSame($st->toSql(), $sql);
            $this->assertSame($st->getParams(), $params);
            return 7;
        });

        self::assertSame(7, $st->exec());
    }

    //endregion

    //region Copy & Clean

    /**
     * @test
     */
    public function copy(): void
    {
        $executor = $this->getMockBuilder(StatementExecutor::class)->getMock();

        $st = (new DeleteStatement($executor))
            ->with('(SELECT * FROM t1)', 'tb')
            ->fromOnly('tb', 't')
            ->using('t1 AS t')
            ->where('c1', '>', 0)
            ->orWhere('c2', 'IN', [1, 2, 3])
            ->returning('c3');

        $copy = $st->copy();

        self::assertSame($executor, $copy->getStatementExecutor());
        self::assertSame(
            'WITH tb AS (SELECT * FROM t1) ' .
            'DELETE FROM ONLY tb t USING t1 AS t WHERE c1 > :p1 OR c2 IN (:p2, :p3, :p4) RETURNING c3',
            $copy->toSql()
        );
        self::assertSame(
            [
                'p1' => 0,
                'p2' => 1,
                'p3' => 2,
                'p4' => 3,
            ],
            $copy->getParams()
        );
    }

    /**
     * @test
     */
    public function clean(): void
    {
        $executor = $this->getMockBuilder(StatementExecutor::class)->getMock();

        $st = (new DeleteStatement($executor))
            ->with('(SELECT * FROM t1)', 'tb')
            ->fromOnly('tb', 't')
            ->using('t1 AS t')
            ->where('c1', '>', 0)
            ->orWhere('c2', 'IN', [1, 2, 3])
            ->returning('c3');

        $st->clean();

        self::assertSame($executor, $st->getStatementExecutor());
        self::assertSame('DELETE FROM', $st->toSql());
        self::assertEmpty($st->getParams());
    }

    //endregion
}
