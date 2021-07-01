<?php

namespace Tests\AlephTools\SqlBuilder\PostgreSql;

use AlephTools\SqlBuilder\PostgreSql\SelectStatement;
use AlephTools\SqlBuilder\PostgreSql\InsertStatement;
use AlephTools\SqlBuilder\Sql\Expression\AbstractExpression;
use AlephTools\SqlBuilder\Sql\Expression\ConditionalExpression;
use AlephTools\SqlBuilder\Sql\Expression\RawExpression;
use AlephTools\SqlBuilder\StatementExecutor;
use PHPUnit\Framework\TestCase;

class InsertStatementTest extends TestCase
{
    public function setUp(): void
    {
        AbstractExpression::resetParameterIndex();
    }

    /**
     * @test
     */
    public function emptyInsert(): void
    {
        $st = (new InsertStatement())
            ->into('tb');

        $this->assertSame('INSERT INTO tb DEFAULT VALUES', $st->toSql());
        $this->assertEmpty($st->getParams());
    }

    //region TABLE

    /**
     * @test
     */
    public function intoTable(): void
    {
        $st = (new InsertStatement())
            ->into('tb')
            ->values(['col' => 'val']);

        $this->assertSame('INSERT INTO tb (col) VALUES (:p1)', $st->toSql());
        $this->assertSame(['p1' => 'val'], $st->getParams());
    }

    /**
     * @test
     */
    public function intoTableWithAlias(): void
    {
        $st = (new InsertStatement())
            ->into('tb', 't')
            ->values(['col' => 'val']);

        $this->assertSame('INSERT INTO tb t (col) VALUES (:p1)', $st->toSql());
        $this->assertSame(['p1' => 'val'], $st->getParams());
    }

    /**
     * @test
     */
    public function intoRawExpression(): void
    {
        $st = (new InsertStatement())
            ->into(new RawExpression('tb AS t'))
            ->values(['col' => 'val']);

        $this->assertSame('INSERT INTO tb AS t (col) VALUES (:p1)', $st->toSql());
        $this->assertSame(['p1' => 'val'], $st->getParams());
    }

    //endregion

    //region COLUMNS & VALUES

    /**
     * @test
     */
    public function columnsWithDefaultValues(): void
    {
        $st = (new InsertStatement())
            ->into('tb')
            ->columns(['c1', 'c2', 'c3']);

        $this->assertSame('INSERT INTO tb (c1, c2, c3) DEFAULT VALUES', $st->toSql());
        $this->assertEmpty($st->getParams());
    }

    /**
     * @test
     */
    public function columnsAndValuesAsStrings(): void
    {
        $st = (new InsertStatement())
            ->into('tb')
            ->columns('c1, c2, c3')
            ->values('(1, 2, 3)');

        $this->assertSame('INSERT INTO tb (c1, c2, c3) VALUES (1, 2, 3)', $st->toSql());
        $this->assertEmpty($st->getParams());
    }

    /**
     * @test
     */
    public function columnsSeparatelyFromValues(): void
    {
        $st = (new InsertStatement())
            ->into('tb')
            ->columns(['c1', 'c2', 'c3'])
            ->values(['v1', 'v2', 'v3']);

        $this->assertSame('INSERT INTO tb (c1, c2, c3) VALUES (:p1, :p2, :p3)', $st->toSql());
        $this->assertSame(['p1' => 'v1', 'p2' => 'v2', 'p3' => 'v3'], $st->getParams());
    }

    /**
     * @test
     */
    public function columnsSeparatelyFromSetOfValues(): void
    {
        $st = (new InsertStatement())
            ->into('tb')
            ->columns(['c1', 'c2', 'c3'])
            ->values([
                ['v1', 'v2', 'v3'],
                ['v4', 'v5', 'v6'],
                ['v7', 'v8', 'v9']
            ]);

        $this->assertSame(
            'INSERT INTO tb (c1, c2, c3) VALUES (:p1, :p2, :p3), (:p4, :p5, :p6), (:p7, :p8, :p9)',
            $st->toSql()
        );
        $this->assertSame(
            [
                'p1' => 'v1', 'p2' => 'v2', 'p3' => 'v3',
                'p4' => 'v4', 'p5' => 'v5', 'p6' => 'v6',
                'p7' => 'v7', 'p8' => 'v8', 'p9' => 'v9'
            ],
            $st->getParams()
        );
    }

    /**
     * @test
     */
    public function columnsWithValues(): void
    {
        $st = (new InsertStatement())
            ->into('tb')
            ->values(['v1', 'v2', 'v3'], ['c1', 'c2', 'c3']);

        $this->assertSame('INSERT INTO tb (c1, c2, c3) VALUES (:p1, :p2, :p3)', $st->toSql());
        $this->assertSame(['p1' => 'v1', 'p2' => 'v2', 'p3' => 'v3'], $st->getParams());
    }

    /**
     * @test
     */
    public function columnsWithSetOfValues(): void
    {
        $st = (new InsertStatement())
            ->into('tb')
            ->values(
                [
                    ['v1', 'v2', 'v3'],
                    ['v4', 'v5', 'v6'],
                    ['v7', 'v8', 'v9']
                ],
                ['c1', 'c2', 'c3']
            );

        $this->assertSame(
            'INSERT INTO tb (c1, c2, c3) VALUES (:p1, :p2, :p3), (:p4, :p5, :p6), (:p7, :p8, :p9)',
            $st->toSql()
        );
        $this->assertSame(
            [
                'p1' => 'v1', 'p2' => 'v2', 'p3' => 'v3',
                'p4' => 'v4', 'p5' => 'v5', 'p6' => 'v6',
                'p7' => 'v7', 'p8' => 'v8', 'p9' => 'v9'
            ],
            $st->getParams()
        );
    }

    /**
     * @test
     */
    public function columnsAndValuesAsSingleParameter(): void
    {
        $st = (new InsertStatement())
            ->into('tb')
            ->values(['c1' => 'v1', 'c2' => 'v2', 'c3' => 'v3']);

        $this->assertSame('INSERT INTO tb (c1, c2, c3) VALUES (:p1, :p2, :p3)', $st->toSql());
        $this->assertSame(['p1' => 'v1', 'p2' => 'v2', 'p3' => 'v3'], $st->getParams());
    }

    /**
     * @test
     */
    public function columnsAndSetOfValuesAsSingleParameter(): void
    {
        $st = (new InsertStatement())
            ->into('tb')
            ->values([
                ['c1' => 'v1', 'c2' => 'v2', 'c3' => 'v3'],
                ['c1' => 'v4', 'c2' => 'v5', 'c3' => 'v6'],
                ['c1' => 'v7', 'c2' => null, 'c3' => new RawExpression('DEFAULT')]
            ]);

        $this->assertSame(
            'INSERT INTO tb (c1, c2, c3) VALUES (:p1, :p2, :p3), (:p4, :p5, :p6), (:p7, NULL, DEFAULT)',
            $st->toSql()
        );
        $this->assertSame(
            [
                'p1' => 'v1', 'p2' => 'v2', 'p3' => 'v3',
                'p4' => 'v4', 'p5' => 'v5', 'p6' => 'v6',
                'p7' => 'v7'
            ],
            $st->getParams()
        );
    }

    /**
     * @test
     */
    public function valuesWithoutColumns(): void
    {
        $st = (new InsertStatement())
            ->into('tb')
            ->values(['v1', 'v2', 'v3']);

        $this->assertSame('INSERT INTO tb VALUES (:p1, :p2, :p3)', $st->toSql());
        $this->assertSame(['p1' => 'v1', 'p2' => 'v2', 'p3' => 'v3'], $st->getParams());
    }

    //endregion

    //region SELECT

    /**
     * @test
     */
    public function insertFromQuery(): void
    {
        $st = (new InsertStatement())
            ->into('t1')
            ->columns(['t1.c1', 't1.c2', 't1.c3'])
            ->select((new SelectStatement())
                ->from('t2')
                ->select(['t2.c1', 't2.c2', 't3.c3'])
                ->where('t2.c1', '=', 123)
            );

        $this->assertSame(
            'INSERT INTO t1 (t1.c1, t1.c2, t1.c3) SELECT t2.c1, t2.c2, t3.c3 FROM t2 WHERE t2.c1 = :p1',
            $st->toSql()
        );
        $this->assertSame(['p1' => 123], $st->getParams());
    }

    //endregion

    //region ON CONFLICT DO UPDATE

    /**
     * @test
     */
    public function onConflictDoNothing(): void
    {
        $st = (new InsertStatement())
            ->into('tb')
            ->onConflict()
            ->doNothing();

        $this->assertSame('INSERT INTO tb DEFAULT VALUES ON CONFLICT DO NOTHING', $st->toSql());
        $this->assertEmpty($st->getParams());
    }

    /**
     * @test
     */
    public function onConflictDoNothingAsOneMethodCall(): void
    {
        $st = (new InsertStatement())
            ->into('tb')
            ->onConflictDoNothing();

        $this->assertSame('INSERT INTO tb DEFAULT VALUES ON CONFLICT DO NOTHING', $st->toSql());
        $this->assertEmpty($st->getParams());
    }

    /**
     * @test
     */
    public function onSingleIndexConflictDoNothing(): void
    {
        $st = (new InsertStatement())
            ->into('tb')
            ->onConflict('col')
            ->doNothing();

        $this->assertSame('INSERT INTO tb DEFAULT VALUES ON CONFLICT (col) DO NOTHING', $st->toSql());
        $this->assertEmpty($st->getParams());
    }

    /**
     * @test
     */
    public function onSingleIndexConflictDoNothingAsOneMethodCall(): void
    {
        $st = (new InsertStatement())
            ->into('tb')
            ->onConflictDoNothing('col');

        $this->assertSame('INSERT INTO tb DEFAULT VALUES ON CONFLICT (col) DO NOTHING', $st->toSql());
        $this->assertEmpty($st->getParams());
    }

    /**
     * @test
     */
    public function onSingleIndexConflictDoUpdateSingleColumn(): void
    {
        $st = (new InsertStatement())
            ->into('tb')
            ->onConflict('c1')
            ->doUpdate('c2', 123);

        $this->assertSame('INSERT INTO tb DEFAULT VALUES ON CONFLICT (c1) DO UPDATE SET c2 = :p1', $st->toSql());
        $this->assertSame(['p1' => 123], $st->getParams());
    }

    /**
     * @test
     */
    public function onSingleIndexConflictDoUpdateSingleColumnAsOneMethodCall(): void
    {
        $st = (new InsertStatement())
            ->into('tb')
            ->onConflictDoUpdate('c1', 'c2', 123);

        $this->assertSame('INSERT INTO tb DEFAULT VALUES ON CONFLICT (c1) DO UPDATE SET c2 = :p1', $st->toSql());
        $this->assertSame(['p1' => 123], $st->getParams());
    }

    /**
     * @test
     */
    public function onMultipleIndexConflictDoUpdateMultipleColumns(): void
    {
        $st = (new InsertStatement())
            ->into('tb')
            ->onConflict(['c1', 'c2', 'c3'])
            ->doUpdate(['c4' => 123, 'c5' => 'abc']);

        $this->assertSame(
            'INSERT INTO tb DEFAULT VALUES ON CONFLICT (c1, c2, c3) DO UPDATE SET c4 = :p1, c5 = :p2',
            $st->toSql()
        );
        $this->assertSame(['p1' => 123, 'p2' => 'abc'], $st->getParams());
    }

    /**
     * @test
     */
    public function onConflictDoUpdateWithAppendedColumns(): void
    {
        $st = (new InsertStatement())
            ->into('tb')
            ->onConflict('c1')
            ->onConflict('c2')
            ->doUpdate('c3', 123)
            ->doUpdate('c4', 'abc')
            ->doUpdate('c5', new RawExpression('NULL'));

        $this->assertSame(
            'INSERT INTO tb DEFAULT VALUES ON CONFLICT (c1, c2) DO UPDATE SET c3 = :p1, c4 = :p2, c5 = NULL',
            $st->toSql()
        );
        $this->assertSame(['p1' => 123, 'p2' => 'abc'], $st->getParams());
    }

    /**
     * @test
     */
    public function onConflictWithConstraint(): void
    {
        $st = (new InsertStatement())
            ->into('tb')
            ->onConflict('c1')
            ->onConstraint('const_name');

        $this->assertSame(
            'INSERT INTO tb DEFAULT VALUES ON CONFLICT (c1) ON CONSTRAINT const_name DO NOTHING',
            $st->toSql()
        );
        $this->assertEmpty($st->getParams());
    }

    /**
     * @test
     */
    public function onConflictWithConditionAsString(): void
    {
        $st = (new InsertStatement())
            ->into('tb')
            ->onConflict('c1', 'c1 IS NULL')
            ->onConflict('c2', 'c2 IS NULL');

        $this->assertSame(
            'INSERT INTO tb DEFAULT VALUES ON CONFLICT (c1, c2) WHERE c1 IS NULL AND c2 IS NULL DO NOTHING',
            $st->toSql()
        );
        $this->assertEmpty($st->getParams());
    }

    /**
     * @test
     */
    public function onConflictWithConditionAsExpression(): void
    {
        $st = (new InsertStatement())
            ->into('tb')
            ->onConflict(
                'c1',
                (new ConditionalExpression())
                    ->where('c2', '=', true)
                    ->orWhere('c3', '>', 5)
            );

        $this->assertSame(
            'INSERT INTO tb DEFAULT VALUES ON CONFLICT (c1) WHERE c2 = :p1 OR c3 > :p2 DO NOTHING',
            $st->toSql()
        );
        $this->assertSame(['p1' => true, 'p2' => 5], $st->getParams());
    }

    /**
     * @test
     */
    public function onConflictDoUpdateWithCondition(): void
    {
        $st = (new InsertStatement())
            ->into('tb')
            ->onConflict('c1')
            ->doUpdateWithCondition('c1 = NULL', 'c1 > 5')
            ->doUpdateWithCondition(
                'c2',
                123,
                (new ConditionalExpression())
                    ->where('c2', '<', 300)
                    ->and('c3', '>', 5)
            );

        $this->assertSame(
            'INSERT INTO tb DEFAULT VALUES ON CONFLICT (c1) DO UPDATE SET ' .
            'c1 = NULL, c2 = :p3 WHERE c1 > 5 AND (c2 < :p1 AND c3 > :p2)',
            $st->toSql()
        );
        $this->assertEquals(['p1' => 300, 'p2' => 5, 'p3' => 123], $st->getParams());
    }

    /**
     * @test
     */
    public function onConflictDoUpdateWithConstraintAndMultipleConditions(): void
    {
        $st = (new InsertStatement())
            ->into('tb')
            ->onConstraint('const')
            ->onConflict('c1')
            ->where('c2', '=', true)
            ->where('c3', '<', 3)
            ->doUpdate('c3', 1)
            ->andwhere('c5', '>', 10)
            ->orWhere('c6', '<', 0);

        $this->assertSame(
            'INSERT INTO tb DEFAULT VALUES ON CONFLICT (c1) WHERE c2 = :p1 AND c3 < :p2 ON CONSTRAINT const ' .
            'DO UPDATE SET c3 = :p3 WHERE c5 > :p4 OR c6 < :p5',
            $st->toSql()
        );
        $this->assertEquals(
            ['p1' => true, 'p2' => 3, 'p3' => 1, 'p4' => 10, 'p5' => 0],
            $st->getParams()
        );
    }

    //endregion

    //region RETURNING

    /**
     * @test
     */
    public function returningAllColumns(): void
    {
        $st = (new InsertStatement())
            ->into('tb')
            ->values(['c1' => 'v1'])
            ->returning();

        $this->assertSame('INSERT INTO tb (c1) VALUES (:p1) RETURNING *', $st->toSql());
        $this->assertSame(['p1' => 'v1'], $st->getParams());
    }

    /**
     * @test
     */
    public function returningSpecificColumns(): void
    {
        $st = (new InsertStatement())
            ->into('tb')
            ->values(['c1' => 'v1'])
            ->returning('c1')
            ->returning('c2', null)
            ->returning('col', 'c3');

        $this->assertSame('INSERT INTO tb (c1) VALUES (:p1) RETURNING c1, c2, col c3', $st->toSql());
        $this->assertSame(['p1' => 'v1'], $st->getParams());
    }

    /**
     * @test
     */
    public function returningColumnList(): void
    {
        $st = (new InsertStatement())
            ->into('tb')
            ->values(['c1' => 'v1'])
            ->returning(['c1', 'c2', 'c3']);

        $this->assertSame('INSERT INTO tb (c1) VALUES (:p1) RETURNING c1, c2, c3', $st->toSql());
        $this->assertSame(['p1' => 'v1'], $st->getParams());
    }

    /**
     * @test
     */
    public function returningColumnListWithAliases(): void
    {
        $st = (new InsertStatement())
            ->into('tb')
            ->values(['c1' => 'v1'])
            ->returning(['a' => 'c1', 'b' => 'c2', 'c' => 'c3']);

        $this->assertSame('INSERT INTO tb (c1) VALUES (:p1) RETURNING c1 a, c2 b, c3 c', $st->toSql());
        $this->assertSame(['p1' => 'v1'], $st->getParams());
    }

    //endregion

    //region Statement Execution

    /**
     * @test
     */
    public function validateExecutorInstance(): void
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('The statement executor must not be null.');

        (new InsertStatement())
            ->into('tb')
            ->values(['c1' => 'v1'])
            ->exec();
    }

    /**
     * @test
     */
    public function exec(): void
    {
        $executor = $this->getMockBuilder(StatementExecutor::class)->getMock();

        $st = (new InsertStatement($executor))
            ->into('tb')
            ->values(['c1' => 'v1']);

        $executor->method('insert')->willReturnCallback(function (string $sql, array $params) use ($st) {
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

        $st = (new InsertStatement($executor))
            ->into('tb', 't')
            ->with('(SELECT * FROM t1)', 'tb')
            ->onConflict('c1')
            ->doNothing()
            ->values(['c1' => 'v1'])
            ->returning(['c1', 'c2', 'c3']);

        $copy = $st->copy();

        $this->assertSame($executor, $copy->getStatementExecutor());
        $this->assertSame(
            'WITH tb AS (SELECT * FROM t1) ' .
            'INSERT INTO tb t (c1) VALUES (:p1) ON CONFLICT (c1) DO NOTHING RETURNING c1, c2, c3',
            $copy->toSql()
        );
        $this->assertSame(
            [
                'p1' => 'v1',
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

        $st = (new InsertStatement($executor))
            ->into('tb', 't')
            ->with('(SELECT * FROM t1)', 'tb')
            ->onConflict('c1')
            ->doNothing()
            ->values(['c1' => 'v1'])
            ->returning(['c1', 'c2', 'c3']);

        $st->clean();

        $this->assertSame($executor, $st->getStatementExecutor());
        $this->assertSame('INSERT INTO DEFAULT VALUES', $st->toSql());
        $this->assertEmpty($st->getParams());
    }

    //endregion
}