<?php

namespace PostgreSql;

use AlephTools\SqlBuilder\MySql\SelectStatement;
use AlephTools\SqlBuilder\PostgreSql\MergeStatement;
use AlephTools\SqlBuilder\Sql\Expression\AbstractExpression;
use AlephTools\SqlBuilder\Sql\Expression\ConditionalExpression;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
class MergeStatementTest extends TestCase
{
    protected function setUp(): void
    {
        AbstractExpression::resetParameterIndex();
    }

    /**
     * @test
     */
    public function mergeTwoTablesDoNothing(): void
    {
        $st = (new MergeStatement())
            ->into('target')
            ->using('source')
            ->on(
                (new ConditionalExpression('c1 = c2'))
                    ->and('c3', '=', 'c4')
            )
            ->whenNotMatched()
            ->thenDoNothing();

        self::assertSame('MERGE INTO target USING source ON (c1 = c2 AND c3 = :p1) WHEN NOT MATCHED THEN DO NOTHING',
            $st->toSql());
        self::assertSame(['p1' => 'c4'], $st->getParams());
    }

    /**
     * @test
     */
    public function mergeTwoTablesThenDeleteAndDoNothing(): void
    {
        $st = (new MergeStatement())
            ->into('target')
            ->using('source')
            ->on('c1 = c2')
            ->whenNotMatched()
            ->thenDoNothing()
            ->whenMatched()
            ->thenDelete();

        self::assertSame('MERGE INTO target USING source ON c1 = c2 WHEN NOT MATCHED THEN DO NOTHING WHEN MATCHED THEN DELETE',
            $st->toSql());
        self::assertEmpty($st->getParams());
    }

    /**
     * @test
     */
    public function mergeTwoTableThenInsertUpdateDeleteDoNothing(): void
    {
        $st = (new MergeStatement())
            ->into('target', 't')
            ->using('source', 's')
            ->on('t.c1 = s.c2')
            ->whenMatchedAnd('t.c3 = s.c4')
            ->thenUpdate([
                't.c1 = s.c2 * 2'
            ])
            ->whenMatchedAnd(
                (new ConditionalExpression())
                    ->where('t.c1', '>', 10)
                    ->andWhere('t.c2', '<', 20)
                    ->or(
                        (new ConditionalExpression())
                            ->and('s.c3', '>', 10)
                            ->and('s.c4', '<', 20)
                    )
            )
            ->thenDelete()
            ->whenNotMatchedAnd('t.c1 + s.c2 = 30')
            ->thenInsert(['c1', 'c3'])
            ->whenNotMatched()
            ->thenDoNothing();

        self::assertSame('MERGE INTO target t USING source s ON t.c1 = s.c2 WHEN MATCHED AND (t.c3 = s.c4) THEN UPDATE SET t.c1 = s.c2 * 2 WHEN MATCHED AND ((t.c1 > :p1 AND t.c2 < :p2 OR (s.c3 > :p3 AND s.c4 < :p4))) THEN DELETE WHEN NOT MATCHED AND (t.c1 + s.c2 = 30) THEN INSERT (c1, c3) WHEN NOT MATCHED THEN DO NOTHING',
            $st->toSql());
        self::assertSame(
            [
                'p1' => 10,
                'p2' => 20,
                'p3' => 10,
                'p4' => 20,
            ],
            $st->getParams()
        );
    }

    /**
     * @test
     */
    public function mergeTableWithDataSourceThenDoNothing(): void
    {
        $st = (new MergeStatement())
            ->into('target', 't')
            ->using(
                (new SelectStatement())
                    ->from('source', 's')
                    ->select(['id', 'name', 'age'])
                    ->where('age', '>=', 18)
            )
            ->on('t.id', '=', 's.id')
            ->whenNotMatched()
            ->thenDoNothing();

        self::assertSame('MERGE INTO target t USING (SELECT id, name, age FROM source s WHERE age >= :p1) ON t.id = :p2 WHEN NOT MATCHED THEN DO NOTHING',
            $st->toSql());
        self::assertSame([
            'p1' => 18,
            'p2' => 's.id',
        ], $st->getParams());
    }
}