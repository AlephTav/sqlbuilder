<?php

declare(strict_types=1);

namespace Tests\AlephTools\SqlBuilder\PostgreSql;

use AlephTools\SqlBuilder\PostgreSql\SelectStatement;
use AlephTools\SqlBuilder\PostgreSql\UpdateStatement;
use AlephTools\SqlBuilder\Sql\Expression\AbstractExpression;
use AlephTools\SqlBuilder\Sql\Expression\ConditionalExpression;
use AlephTools\SqlBuilder\Sql\Expression\RawExpression;
use AlephTools\SqlBuilder\StatementExecutor;
use PHPUnit\Framework\TestCase;

class UpdateStatementTest extends TestCase
{
    public function setUp(): void
    {
        AbstractExpression::resetParameterIndex();
    }

    /**
     * @test
     */
    public function emptyUpdate(): void
    {
        $st = new UpdateStatement();

        $this->assertEquals('UPDATE', $st->toSql());
        $this->assertEmpty($st->getParams());
    }

    //region TABLE

    /**
     * @test
     */
    public function updateTable(): void
    {
        $st = (new UpdateStatement())
            ->table('tb');

        $this->assertSame('UPDATE tb', $st->toSql());
        $this->assertEmpty($st->getParams());
    }

    /**
     * @test
     */
    public function updateTableWithAlias(): void
    {
        $st = (new UpdateStatement())
            ->table('tb', 't');

        $this->assertSame('UPDATE tb t', $st->toSql());
        $this->assertEmpty($st->getParams());
    }

    /**
     * @test
     */
    public function updateTables(): void
    {
        $st = (new UpdateStatement())
            ->table(['t1', 't2']);

        $this->assertEquals('UPDATE t1, t2', $st->toSql());
        $this->assertEmpty($st->getParams());
    }

    /**
     * @test
     */
    public function updateTablesWithAliases(): void
    {
        $st = (new UpdateStatement())
            ->table(['a' => 't1', 'b' => 't2']);

        $this->assertSame('UPDATE t1 a, t2 b', $st->toSql());
        $this->assertEmpty($st->getParams());
    }

    /**
     * @test
     */
    public function updateRawExpression(): void
    {
        $st = (new UpdateStatement())
            ->table(new RawExpression('tb AS t'));

        $this->assertSame('UPDATE tb AS t', $st->toSql());
        $this->assertEmpty($st->getParams());
    }

    /**
     * @test
     */
    public function updateOnlyTable(): void
    {
        $st = (new UpdateStatement())
            ->onlyTable('tb');

        $this->assertSame('UPDATE ONLY tb', $st->toSql());
        $this->assertEmpty($st->getParams());
    }

    //endregion

    //region SET (Assignment List)

    /**
     * @test
     */
    public function testAssignSingleValue(): void
    {
        $st = (new UpdateStatement())
            ->table('tb')
            ->assign('c1', 'v1');

        $this->assertSame('UPDATE tb SET c1 = :p1', $st->toSql());
        $this->assertSame(['p1' => 'v1'], $st->getParams());
    }

    /**
     * @test
     */
    public function testAssignMultipleValues(): void
    {
        $st = (new UpdateStatement())
            ->table('t1')
            ->assign('c1', 'v1')
            ->assign('c2', new RawExpression('DEFAULT'))
            ->assign(['c3' => null])
            ->assign(
                new RawExpression('(c4, c5)'),
                (new SelectStatement())->from('t2')->select('t2.c1, t2.c2')
            )
            ->assign((new SelectStatement())->from('t3'))
            ->assign(['c6 = 5'])
            ->assign(null);

        $this->assertSame(
            'UPDATE t1 SET c1 = :p1, c2 = DEFAULT, c3 = NULL, ' .
            '(c4, c5) = (SELECT t2.c1, t2.c2 FROM t2), (SELECT * FROM t3), c6 = 5, NULL',
            $st->toSql()
        );
        $this->assertSame(['p1' => 'v1'], $st->getParams());
    }

    //endregion

    //region FROM

    /**
     * @test
     */
    public function fromTable(): void
    {
        $st = (new UpdateStatement())
            ->table('t1')
            ->assign('c1', 'v1')
            ->from('t2');

        $this->assertSame('UPDATE t1 SET c1 = :p1 FROM t2', $st->toSql());
        $this->assertSame(['p1' => 'v1'], $st->getParams());
    }

    /**
     * @test
     */
    public function fromTableWithAlias(): void
    {
        $st = (new UpdateStatement())
            ->table('t1')
            ->assign('c1', 'v1')
            ->from('t2', 't');

        $this->assertSame('UPDATE t1 SET c1 = :p1 FROM t2 t', $st->toSql());
        $this->assertSame(['p1' => 'v1'], $st->getParams());
    }

    /**
     * @test
     */
    public function fromTables(): void
    {
        $st = (new UpdateStatement())
            ->table('tb')
            ->assign('c1', 'v1')
            ->from(['t1', 't2']);

        $this->assertSame('UPDATE tb SET c1 = :p1 FROM t1, t2', $st->toSql());
        $this->assertSame(['p1' => 'v1'], $st->getParams());
    }

    /**
     * @test
     */
    public function fromTablesWithAliases(): void
    {
        $st = (new UpdateStatement())
            ->table('tb')
            ->assign('c1', 'v1')
            ->from(['a' => 't1', 'b' => 't2']);

        $this->assertSame('UPDATE tb SET c1 = :p1 FROM t1 a, t2 b', $st->toSql());
        $this->assertSame(['p1' => 'v1'], $st->getParams());
    }

    /**
     * @test
     */
    public function fromRawExpression(): void
    {
        $st = (new UpdateStatement())
            ->table('t1')
            ->assign('c1', 'v1')
            ->from(new RawExpression('t2 AS t'));

        $this->assertSame('UPDATE t1 SET c1 = :p1 FROM t2 AS t', $st->toSql());
        $this->assertSame(['p1' => 'v1'], $st->getParams());
    }

    //endregion

    //region WHERE

    /**
     * @test
     */
    public function whereAsString(): void
    {
        $st = (new UpdateStatement())
            ->table('tb')
            ->where('c1 = c2');

        $this->assertSame('UPDATE tb WHERE c1 = c2', $st->toSql());
        $this->assertEmpty($st->getParams());
    }

    /**
     * @test
     */
    public function whereAsRawValue(): void
    {
        $st = (new UpdateStatement())
            ->table('tb')
            ->where(new RawExpression('c1 = c2'));

        $this->assertSame('UPDATE tb WHERE c1 = c2', $st->toSql());
        $this->assertEmpty($st->getParams());
    }

    /**
     * @test
     */
    public function whereBinaryOpWithScalar(): void
    {
        $st = (new UpdateStatement())
            ->table('tb')
            ->where('col', '=', 1);

        $this->assertSame('UPDATE tb WHERE col = :p1', $st->toSql());
        $this->assertSame(['p1' => 1], $st->getParams());
    }

    /**
     * @test
     */
    public function whereBinaryOpWithNull(): void
    {
        $st = (new UpdateStatement())
            ->table('tb')
            ->where('col', '=', null);

        $this->assertSame('UPDATE tb WHERE col = NULL', $st->toSql());
        $this->assertEmpty($st->getParams());
    }

    /**
     * @test
     */
    public function whereBinaryOpWithQuery(): void
    {
        $st = (new UpdateStatement())
            ->table('t1')
            ->where('t1.col', '=', (new SelectStatement())->from('t2')->select('COUNT(*)'));

        $this->assertEquals('UPDATE t1 WHERE t1.col = (SELECT COUNT(*) FROM t2)', $st->toSql());
        $this->assertEmpty($st->getParams());
    }

    /**
     * @test
     */
    public function whereBinaryOpWithTuple(): void
    {
        $st = (new UpdateStatement())
            ->table('tb')
            ->where('col', 'IN', [1, 2, 3]);

        $this->assertSame('UPDATE tb WHERE col IN (:p1, :p2, :p3)', $st->toSql());
        $this->assertSame(['p1' => 1, 'p2' => 2, 'p3' => 3], $st->getParams());
    }

    /**
     * @test
     */
    public function whereBinaryOpBetween(): void
    {
        $st = (new UpdateStatement())
            ->table('tb')
            ->where('col', 'BETWEEN', [1, 2]);

        $this->assertSame('UPDATE tb WHERE col BETWEEN :p1 AND :p2', $st->toSql());
        $this->assertSame(['p1' => 1, 'p2' => 2], $st->getParams());
    }

    /**
     * @test
     */
    public function whereBinaryOpWithRawValue(): void
    {
        $st = (new UpdateStatement())
            ->table('tb')
            ->where('c1', '=', new RawExpression('c2'));

        $this->assertSame('UPDATE tb WHERE c1 = c2', $st->toSql());
        $this->assertEmpty($st->getParams());
    }

    /**
     * @test
     */
    public function whereUnaryOpWithScalar(): void
    {
        $st = (new UpdateStatement())
            ->table('tb')
            ->where('NOT', new RawExpression('col'));

        $this->assertSame('UPDATE tb WHERE NOT col', $st->toSql());
        $this->assertEmpty($st->getParams());
    }

    /**
     * @test
     */
    public function whereUnaryOpWithQuery(): void
    {
        $st = (new UpdateStatement())
            ->table('t1')
            ->where('NOT', (new SelectStatement())->from('t2')->select('COUNT(*)'));

        $this->assertSame('UPDATE t1 WHERE NOT (SELECT COUNT(*) FROM t2)', $st->toSql());
        $this->assertEmpty($st->getParams());
    }

    /**
     * @test
     */
    public function whereWithQueryAsOperand(): void
    {
        $st = (new UpdateStatement())
            ->table('t1')
            ->where((new SelectStatement())->from('t2')->select('COUNT(*)'), '>', 5);

        $this->assertSame('UPDATE t1 WHERE (SELECT COUNT(*) FROM t2) > :p1', $st->toSql());
        $this->assertSame(['p1' => 5], $st->getParams());
    }

    /**
     * @test
     */
    public function whereWithQueriesAsOperandAndValue(): void
    {
        $st = (new UpdateStatement())
            ->table('t1')
            ->where(
                (new SelectStatement())->from('t2')->select('COUNT(*)'),
                '<>',
                (new SelectStatement())->from('t3')->select('COUNT(*)')
            );

        $this->assertSame(
            'UPDATE t1 WHERE (SELECT COUNT(*) FROM t2) <> (SELECT COUNT(*) FROM t3)',
            $st->toSql()
        );
        $this->assertEmpty($st->getParams());
    }

    /**
     * @test
     */
    public function whereAsConditionList(): void
    {
        $st = (new UpdateStatement())
            ->table('tb')
            ->where(['c1 = c2', 'c3 <> c4']);

        $this->assertSame('UPDATE tb WHERE c1 = c2 AND c3 <> c4', $st->toSql());
        $this->assertEmpty($st->getParams());
    }

    /**
     * @test
     */
    public function whereAsConditionMap(): void
    {
        $st = (new UpdateStatement())
            ->table('tb')
            ->where(['c1' => 1, 'c2' => 2]);

        $this->assertSame('UPDATE tb WHERE c1 = :p1 AND c2 = :p2', $st->toSql());
        $this->assertSame(['p1' => 1, 'p2' => 2], $st->getParams());
    }

    /**
     * @test
     */
    public function whereWithNestedConditionsAsConditionalExpression(): void
    {
        $st = (new UpdateStatement())
            ->table('tb')
            ->where('c1 IS NULL')
            ->where(
                (new ConditionalExpression())
                ->orWhere('c2', '=', 1)
                ->orWhere('c3', '<', 2)
            );

        $this->assertSame('UPDATE tb WHERE c1 IS NULL AND (c2 = :p1 OR c3 < :p2)', $st->toSql());
        $this->assertSame(['p1' => 1, 'p2' => 2], $st->getParams());
    }

    /**
     * @test
     */
    public function whereWithNestedConditionsAsClosure(): void
    {
        $st = (new UpdateStatement())
            ->table('tb')
            ->where('c1 IS NULL')
            ->where(function (ConditionalExpression $condition): void {
                $condition->orWhere('c2', '=', 1)
                    ->orWhere('c3', '<', 2);
            });

        $this->assertSame('UPDATE tb WHERE c1 IS NULL AND (c2 = :p1 OR c3 < :p2)', $st->toSql());
        $this->assertSame(['p1' => 1, 'p2' => 2], $st->getParams());
    }

    //endregion

    //region RETURNING

    /**
     * @test
     */
    public function returningAllColumns(): void
    {
        $st = (new UpdateStatement())
            ->table('tb')
            ->returning();

        $this->assertSame('UPDATE tb RETURNING *', $st->toSql());
        $this->assertEmpty($st->getParams());
    }

    /**
     * @test
     */
    public function returningSpecificColumns(): void
    {
        $st = (new UpdateStatement())
            ->table('tb')
            ->returning('c1')
            ->returning('c2', null)
            ->returning('col', 'c3');

        $this->assertSame('UPDATE tb RETURNING c1, c2, col c3', $st->toSql());
        $this->assertEmpty($st->getParams());
    }

    /**
     * @test
     */
    public function returningColumnList(): void
    {
        $st = (new UpdateStatement())
            ->table('tb')
            ->returning(['c1', 'c2', 'c3']);

        $this->assertSame('UPDATE tb RETURNING c1, c2, c3', $st->toSql());
        $this->assertEmpty($st->getParams());
    }

    /**
     * @test
     */
    public function returningColumnListWithAliases(): void
    {
        $st = (new UpdateStatement())
            ->table('tb')
            ->returning(['a' => 'c1', 'b' => 'c2', 'c' => 'c3']);

        $this->assertSame('UPDATE tb RETURNING c1 a, c2 b, c3 c', $st->toSql());
        $this->assertEmpty($st->getParams());
    }

    //endregion

    //region WITH

    /**
     * @test
     */
    public function withSimpleQuery(): void
    {
        $st = (new UpdateStatement())
            ->with((new SelectStatement())->from('t1'), 'tb')
            ->table('tb');

        $this->assertSame('WITH tb AS (SELECT * FROM t1) UPDATE tb', $st->toSql());
        $this->assertEmpty($st->getParams());
    }

    /**
     * @test
     */
    public function withRawExpression(): void
    {
        $st = (new UpdateStatement())
            ->with('(SELECT * FROM t1)', 'tb')
            ->with(new RawExpression('n1 AS NULL'))
            ->with(null, 'n2')
            ->table('tb');

        $this->assertSame(
            'WITH tb AS (SELECT * FROM t1), n1 AS NULL, n2 AS NULL UPDATE tb',
            $st->toSql()
        );
        $this->assertEmpty($st->getParams());
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

        (new UpdateStatement())
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

        $st = (new UpdateStatement($executor))
            ->from('tb')
            ->where('c1', '>', 5)
            ->orWhere('c2', '<', 0);

        $executor->method('execute')->willReturnCallback(function (string $sql, array $params) use ($st) {
            $this->assertSame($st->toSql(), $sql);
            $this->assertSame($st->getParams(), $params);
            return 7;
        });

        $this->assertSame(7, $st->exec());
    }

    //endregion

    //region Copy & Clean

    /**
     * @test
     */
    public function copy(): void
    {
        $executor = $this->getMockBuilder(StatementExecutor::class)->getMock();

        $st = (new UpdateStatement($executor))
            ->with('(SELECT * FROM t1)', 'tb')
            ->onlyTable('tb', 't')
            ->assign('c2', 'abc')
            ->where('c1', '>', 0)
            ->returning('c3');

        $copy = $st->copy();

        $this->assertSame($executor, $copy->getStatementExecutor());
        $this->assertSame(
            'WITH tb AS (SELECT * FROM t1) ' .
            'UPDATE ONLY tb t SET c2 = :p1 WHERE c1 > :p2 RETURNING c3',
            $copy->toSql()
        );
        $this->assertSame(
            [
                'p1' => 'abc',
                'p2' => 0,
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

        $st = (new UpdateStatement($executor))
            ->with('(SELECT * FROM t1)', 'tb')
            ->onlyTable('tb', 't')
            ->where('c1', '>', 0)
            ->assign('c2', 'abc')
            ->returning('c3');

        $st->clean();

        $this->assertSame($executor, $st->getStatementExecutor());
        $this->assertSame('UPDATE', $st->toSql());
        $this->assertEmpty($st->getParams());
    }

    //endregion
}
