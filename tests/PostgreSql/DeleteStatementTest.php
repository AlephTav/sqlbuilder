<?php

namespace Tests\AlephTools\SqlBuilder\PostgreSql;

use AlephTools\SqlBuilder\PostgreSql\SelectStatement;
use AlephTools\SqlBuilder\PostgreSql\DeleteStatement;
use AlephTools\SqlBuilder\Sql\Expression\AbstractExpression;
use AlephTools\SqlBuilder\Sql\Expression\ConditionalExpression;
use AlephTools\SqlBuilder\Sql\Expression\RawExpression;
use AlephTools\SqlBuilder\StatementExecutor;
use PHPUnit\Framework\TestCase;

class DeleteStatementTest extends TestCase
{
    public function setUp(): void
    {
        AbstractExpression::resetParameterIndex();
    }

    /**
     * @test
     */
    public function emptyDelete(): void
    {
        $st = new DeleteStatement();

        $this->assertSame('DELETE FROM', $st->toSql());
        $this->assertEmpty($st->getParams());
    }

    //region FROM

    /**
     * @test
     */
    public function fromTable(): void
    {
        $st = (new DeleteStatement())
            ->from('tb');

        $this->assertSame('DELETE FROM tb', $st->toSql());
        $this->assertEmpty($st->getParams());
    }

    /**
     * @test
     */
    public function fromTableWithAlias(): void
    {
        $st = (new DeleteStatement())
            ->from('tb', 't');

        $this->assertSame('DELETE FROM tb t', $st->toSql());
        $this->assertEmpty($st->getParams());
    }

    /**
     * @test
     */
    public function fromTables(): void
    {
        $st = (new DeleteStatement())
            ->from(['t1', 't2']);

        $this->assertSame('DELETE FROM t1, t2', $st->toSql());
        $this->assertEmpty($st->getParams());
    }

    /**
     * @test
     */
    public function fromTablesWithAliases(): void
    {
        $st = (new DeleteStatement())
            ->from(['a' => 't1', 'b' => 't2']);

        $this->assertSame('DELETE FROM t1 a, t2 b', $st->toSql());
        $this->assertEmpty($st->getParams());
    }

    /**
     * @test
     */
    public function fromRawExpression(): void
    {
        $st = (new DeleteStatement())
            ->from(new RawExpression('tb AS t'));

        $this->assertSame('DELETE FROM tb AS t', $st->toSql());
        $this->assertEmpty($st->getParams());
    }

    /**
     * @test
     */
    public function fromOnly(): void
    {
        $st = (new DeleteStatement())
            ->fromOnly('tb');

        $this->assertSame('DELETE FROM ONLY tb', $st->toSql());
        $this->assertEmpty($st->getParams());
    }

    /**
     * @test
     */
    public function fromOnlyWithAlias(): void
    {
        $st = (new DeleteStatement())
            ->fromOnly('tb', 't');

        $this->assertSame('DELETE FROM ONLY tb t', $st->toSql());
        $this->assertEmpty($st->getParams());
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

        $this->assertSame('DELETE FROM tb USING t', $st->toSql());
        $this->assertEmpty($st->getParams());
    }

    /**
     * @test
     */
    public function usingTableWithAlias(): void
    {
        $st = (new DeleteStatement())
            ->from('tb')
            ->using('t1', 't');

        $this->assertSame('DELETE FROM tb USING t1 t', $st->toSql());
        $this->assertEmpty($st->getParams());
    }

    /**
     * @test
     */
    public function usingTables(): void
    {
        $st = (new DeleteStatement())
            ->from('tb')
            ->using(['t1', 't2']);

        $this->assertSame('DELETE FROM tb USING t1, t2', $st->toSql());
        $this->assertEmpty($st->getParams());
    }

    /**
     * @test
     */
    public function usingTablesWithAliases(): void
    {
        $st = (new DeleteStatement())
            ->from('tb')
            ->using(['a' => 't1', 'b' => 't2']);

        $this->assertSame('DELETE FROM tb USING t1 a, t2 b', $st->toSql());
        $this->assertEmpty($st->getParams());
    }

    /**
     * @test
     */
    public function usingRawExpression(): void
    {
        $st = (new DeleteStatement())
            ->from('tb')
            ->using(new RawExpression('t1 AS t'));

        $this->assertEquals('DELETE FROM tb USING t1 AS t', $st->toSql());
        $this->assertEmpty($st->getParams());
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

        $this->assertSame('DELETE FROM tb WHERE c1 = c2', $st->toSql());
        $this->assertEmpty($st->getParams());
    }

    /**
     * @test
     */
    public function whereAsRawValue(): void
    {
        $st = (new DeleteStatement())
            ->from('tb')
            ->where(new RawExpression('c1 = c2'));

        $this->assertSame('DELETE FROM tb WHERE c1 = c2', $st->toSql());
        $this->assertEmpty($st->getParams());
    }

    /**
     * @test
     */
    public function whereBinaryOpWithScalar(): void
    {
        $st = (new DeleteStatement())
            ->from('tb')
            ->where('col', '=', 1);

        $this->assertSame('DELETE FROM tb WHERE col = :p1', $st->toSql());
        $this->assertSame(['p1' => 1], $st->getParams());
    }

    /**
     * @test
     */
    public function whereBinaryOpWithNull(): void
    {
        $st = (new DeleteStatement())
            ->from('tb')
            ->where('col', '=', null);

        $this->assertSame('DELETE FROM tb WHERE col = NULL', $st->toSql());
        $this->assertEmpty($st->getParams());
    }

    /**
     * @test
     */
    public function whereBinaryOpWithQuery()
    {
        $st = (new DeleteStatement())
            ->from('t1')
            ->where('t1.col', '=', (new SelectStatement())->from('t2')->select('COUNT(*)'));

        $this->assertSame('DELETE FROM t1 WHERE t1.col = (SELECT COUNT(*) FROM t2)', $st->toSql());
        $this->assertEmpty($st->getParams());
    }

    /**
     * @test
     */
    public function whereBinaryOpWithList(): void
    {
        $st = (new DeleteStatement())
            ->from('tb')
            ->where('col', 'IN', [1, 2, 3]);

        $this->assertSame('DELETE FROM tb WHERE col IN (:p1, :p2, :p3)', $st->toSql());
        $this->assertSame(
            [
                'p1' => 1,
                'p2' => 2,
                'p3' => 3
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

        $this->assertSame('DELETE FROM tb WHERE col BETWEEN :p1 AND :p2', $st->toSql());
        $this->assertSame(['p1' => 1, 'p2' => 2], $st->getParams());
    }

    /**
     * @test
     */
    public function whereBinaryOpWithRawValue(): void
    {
        $st = (new DeleteStatement())
            ->from('tb')
            ->where('c1', '=', new RawExpression('c2'));

        $this->assertSame('DELETE FROM tb WHERE c1 = c2', $st->toSql());
        $this->assertEmpty($st->getParams());
    }

    /**
     * @test
     */
    public function whereUnaryOpWithScalar(): void
    {
        $st = (new DeleteStatement())
            ->from('tb')
            ->where('NOT', new RawExpression('col'));

        $this->assertSame('DELETE FROM tb WHERE NOT col', $st->toSql());
        $this->assertEmpty($st->getParams());
    }

    /**
     * @test
     */
    public function whereUnaryOpWithQuery(): void
    {
        $st = (new DeleteStatement())
            ->from('t1')
            ->where('NOT', (new SelectStatement())->from('t2')->select('COUNT(*)'));

        $this->assertSame('DELETE FROM t1 WHERE NOT (SELECT COUNT(*) FROM t2)', $st->toSql());
        $this->assertEmpty($st->getParams());
    }

    /**
     * @test
     */
    public function whereWithQueryAsOperand(): void
    {
        $st = (new DeleteStatement())
            ->from('t1')
            ->where((new SelectStatement())->from('t2')->select('COUNT(*)'), '>', 5);

        $this->assertSame('DELETE FROM t1 WHERE (SELECT COUNT(*) FROM t2) > :p1', $st->toSql());
        $this->assertSame(['p1' => 5], $st->getParams());
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

        $this->assertSame(
            'DELETE FROM t1 WHERE (SELECT COUNT(*) FROM t2) <> (SELECT COUNT(*) FROM t3)',
            $st->toSql()
        );
        $this->assertEmpty($st->getParams());
    }

    /**
     * @test
     */
    public function whereAsConditionList(): void
    {
        $st = (new DeleteStatement())
            ->from('tb')
            ->where(['c1 = c2', 'c3 <> c4']);

        $this->assertSame('DELETE FROM tb WHERE c1 = c2 AND c3 <> c4', $st->toSql());
        $this->assertEmpty($st->getParams());
    }

    /**
     * @test
     */
    public function whereAsConditionMap(): void
    {
        $st = (new DeleteStatement())
            ->from('tb')
            ->where(['c1' => 1, 'c2' => 2]);

        $this->assertSame('DELETE FROM tb WHERE c1 = :p1 AND c2 = :p2', $st->toSql());
        $this->assertSame(['p1' => 1, 'p2' => 2], $st->getParams());
    }

    /**
     * @test
     */
    public function whereWithNestedConditionsAsConditionalExpression(): void
    {
        $st = (new DeleteStatement())
            ->from('tb')
            ->where('c1 IS NULL')
            ->where((new ConditionalExpression())
                ->orWhere('c2', '=', 1)
                ->orWhere('c3', '<', 2)
            );

        $this->assertSame('DELETE FROM tb WHERE c1 IS NULL AND (c2 = :p1 OR c3 < :p2)', $st->toSql());
        $this->assertSame(['p1' => 1, 'p2' => 2], $st->getParams());
    }

    /**
     * @test
     */
    public function whereWithNestedConditionsAsClosure(): void
    {
        $st = (new DeleteStatement())
            ->from('tb')
            ->where('c1 IS NULL')
            ->where(function (ConditionalExpression $condition) {
                $condition->orWhere('c2', '=', 1)
                    ->orWhere('c3', '<', 2);
            });

        $this->assertSame('DELETE FROM tb WHERE c1 IS NULL AND (c2 = :p1 OR c3 < :p2)', $st->toSql());
        $this->assertSame(['p1' => 1, 'p2' => 2], $st->getParams());
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

        $this->assertSame('DELETE FROM tb RETURNING *', $st->toSql());
        $this->assertEmpty($st->getParams());
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

        $this->assertSame('DELETE FROM tb RETURNING c1, c2, col c3', $st->toSql());
        $this->assertEmpty($st->getParams());
    }

    /**
     * @test
     */
    public function returningColumnList(): void
    {
        $st = (new DeleteStatement())
            ->from('tb')
            ->returning(['c1', 'c2', 'c3']);

        $this->assertSame('DELETE FROM tb RETURNING c1, c2, c3', $st->toSql());
        $this->assertEmpty($st->getParams());
    }

    public function returningColumnListWithAliases(): void
    {
        $st = (new DeleteStatement())
            ->from('tb')
            ->returning(['a' => 'c1', 'b' => 'c2', 'c' => 'c3']);

        $this->assertEquals('DELETE FROM tb RETURNING c1 a, c2 b, c3 c', $st->toSql());
        $this->assertEmpty($st->getParams());
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

        $this->assertSame('WITH tb AS (SELECT * FROM t1) DELETE FROM tb', $st->toSql());
        $this->assertEmpty($st->getParams());
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

        $this->assertSame(
            'WITH tb AS (SELECT * FROM t1), n1 AS NULL, n2 AS NULL DELETE FROM tb',
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

        $st = (new DeleteStatement($executor))
            ->with('(SELECT * FROM t1)', 'tb')
            ->fromOnly('tb', 't')
            ->using('t1 AS t')
            ->where('c1', '>', 0)
            ->orWhere('c2', 'IN', [1, 2, 3])
            ->returning('c3');

        $copy = $st->copy();

        $this->assertSame($executor, $copy->getStatementExecutor());
        $this->assertSame(
            'WITH tb AS (SELECT * FROM t1) ' .
            'DELETE FROM ONLY tb t USING t1 AS t WHERE c1 > :p1 OR c2 IN (:p2, :p3, :p4) RETURNING c3',
            $copy->toSql()
        );
        $this->assertSame(
            [
                'p1' => 0,
                'p2' => 1,
                'p3' => 2,
                'p4' => 3
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

        $this->assertSame($executor, $st->getStatementExecutor());
        $this->assertSame('DELETE FROM', $st->toSql());
        $this->assertEmpty($st->getParams());
    }

    //endregion
}