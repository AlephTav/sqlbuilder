<?php

namespace Tests\AlephTools\SqlBuilder\PostgreSql;

use AlephTools\SqlBuilder\PostgreSql\SelectStatement;
use AlephTools\SqlBuilder\PostgreSql\ValuesStatement;
use AlephTools\SqlBuilder\Sql\Expression\AbstractExpression;
use AlephTools\SqlBuilder\Sql\Expression\RawExpression;
use AlephTools\SqlBuilder\StatementExecutor;
use PHPUnit\Framework\TestCase;

class ValuesStatementTest extends TestCase
{
    public function setUp(): void
    {
        AbstractExpression::resetParameterIndex();
    }

    /**
     * @test
     */
    public function emptyValues(): void
    {
        $st = new ValuesStatement();

        $this->assertSame('VALUES', $st->toSql());
        $this->assertEmpty($st->getParams());
    }

    //region VALUES

    /**
     * @test
     */
    public function valuesAsString(): void
    {
        $st = (new ValuesStatement())
            ->values('(1), (2), (3)');

        $this->assertSame('VALUES (1), (2), (3)', $st->toSql());
        $this->assertEmpty($st->getParams());
    }

    /**
     * @test
     */
    public function valuesAsRawExpression(): void
    {
        $st = (new ValuesStatement())
            ->values(new RawExpression('(1), (2), (3)'));

        $this->assertSame('VALUES (1), (2), (3)', $st->toSql());
        $this->assertEmpty($st->getParams());
    }

    /**
     * @test
     */
    public function valuesAsCollectionOfScalars(): void
    {
        $st = (new ValuesStatement())
            ->values([1, 2, 3]);

        $this->assertSame('VALUES (:p1, :p2, :p3)', $st->toSql());
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
    public function valuesAsMapOfScalars(): void
    {
        $st = (new ValuesStatement())
            ->values([
                'k1' => 1,
                'k2' => 2,
                'k3' => 3
            ]);

        $this->assertSame('VALUES (:p1, :p2, :p3)', $st->toSql());
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
    public function valuesAsCollectionOfCollections(): void
    {
        $st = (new ValuesStatement())
            ->values([
                [1, 2],
                ['a', ['b', 'c']]
            ]);

        $this->assertSame('VALUES (:p1, :p2), (:p3, :p4, :p5)', $st->toSql());
        $this->assertSame(
            [
                'p1' => 1,
                'p2' => 2,
                'p3' => 'a',
                'p4' => 'b',
                'p5' => 'c'
            ],
            $st->getParams()
        );
    }

    /**
     * @test
     */
    public function valuesAsMapOfCollections(): void
    {
        $st = (new ValuesStatement())
            ->values([
                'k1' => [1, 2],
                'k2' => ['a', 'b']
            ]);

        $this->assertSame('VALUES (:p1, :p2), (:p3, :p4)', $st->toSql());
        $this->assertSame(
            [
                'p1' => 1,
                'p2' => 2,
                'p3' => 'a',
                'p4' => 'b'
            ],
            $st->getParams()
        );
    }

    /**
     * @test
     */
    public function valuesAsCollectionOfMaps(): void
    {
        $st = (new ValuesStatement())
            ->values([
                ['k1' => 1, 'k2' => 2],
                ['k1' => 'a', 'k2' => 'b']
            ]);

        $this->assertSame('VALUES (:p1, :p2), (:p3, :p4)', $st->toSql());
        $this->assertSame(
            [
                'p1' => 1,
                'p2' => 2,
                'p3' => 'a',
                'p4' => 'b'
            ],
            $st->getParams()
        );
    }

    /**
     * @test
     */
    public function valuesAsMapOfMaps(): void
    {
        $st = (new ValuesStatement())
            ->values([
                'k1' => ['k1' => 1, 'k2' => 2],
                'k2' => ['k1' => 'a', 'k2' => 'b']
            ]);

        $this->assertSame('VALUES (:p1, :p2), (:p3, :p4)', $st->toSql());
        $this->assertSame(
            [
                'p1' => 1,
                'p2' => 2,
                'p3' => 'a',
                'p4' => 'b'
            ],
            $st->getParams()
        );
    }

    /**
     * @test
     */
    public function appendValues(): void
    {
        $st = (new ValuesStatement())
            ->values([1])
            ->values(['a'])
            ->values([
                [1, 2],
                [true]
            ]);

        $this->assertSame('VALUES (:p1), (:p2), (:p3, :p4), (:p5)', $st->toSql());
        $this->assertSame(
            [
                'p1' => 1,
                'p2' => 'a',
                'p3' => 1,
                'p4' => 2,
                'p5' => true
            ],
            $st->getParams()
        );
    }

    /**
     * @test
     */
    public function valuesAsQuery(): void
    {
        $st = (new ValuesStatement())
            ->values('(1), (2)')
            ->values(
                (new ValuesStatement())
                    ->values('(3)')
            );

        $this->assertEquals('VALUES (1), (2), ((VALUES (3)))', $st->toSql());
        $this->assertEmpty($st->getParams());
    }

    //endregion

    //region ORDER BY

    /**
     * @test
     */
    public function orderByColumn(): void
    {
        $st = (new ValuesStatement())
            ->values('(1), (2), (3)')
            ->orderBy('column1');

        $this->assertSame('VALUES (1), (2), (3) ORDER BY column1', $st->toSql());
        $this->assertEmpty($st->getParams());
    }

    /**
     * @test
     */
    public function orderByColumnWithOrder(): void
    {
        $st = (new ValuesStatement())
            ->values('(1), (2), (3)')
            ->orderBy('column1', 'DESC');

        $this->assertSame('VALUES (1), (2), (3) ORDER BY column1 DESC', $st->toSql());
        $this->assertEmpty($st->getParams());
    }

    //endregion

    //region LIMIT & OFFSET

    /**
     * @test
     */
    public function limitAndOffset(): void
    {
        $st = (new ValuesStatement())
            ->values('(1), (2), (3)')
            ->limit(2)
            ->offset(1);

        $this->assertSame('VALUES (1), (2), (3) LIMIT 2 OFFSET 1', $st->toSql());
        $this->assertEmpty($st->getParams());
    }

    /**
     * @test
     */
    public function pagination(): void
    {
        $st = (new ValuesStatement())
            ->values('(1), (2), (3)')
            ->paginate(1, 2);

        $this->assertSame('VALUES (1), (2), (3) LIMIT 2 OFFSET 2', $st->toSql());
        $this->assertEmpty($st->getParams());
    }

    //endregion

    //region UNION

    /**
     * @test
     */
    public function unionWithSelectStatement(): void
    {
        $st = (new ValuesStatement())
            ->values('(1), (2), (3)')
            ->union(
                (new SelectStatement())
                    ->from('tb')
            );

        $this->assertSame('(VALUES (1), (2), (3)) UNION (SELECT * FROM tb)', $st->toSql());
        $this->assertEmpty($st->getParams());
    }

    /**
     * @test
     */
    public function unionOfValuesAndQueriesWithSorting(): void
    {
        $st = (new ValuesStatement())
            ->values('(1), (2), (3)')
            ->union(
                (new SelectStatement())
                    ->from("tb")
                    ->orderBy('id', 'ASC')
            )
            ->union(
                (new ValuesStatement())
                ->values("('a'), ('b'), ('b')")
                ->orderBy('column1', 'DESC')
            )
            ->orderBy('column1', 'ASC');

        $this->assertSame(
            '(VALUES (1), (2), (3)) UNION (SELECT * FROM tb ORDER BY id ASC) UNION ' .
            "(VALUES ('a'), ('b'), ('b') ORDER BY column1 DESC) ORDER BY column1 ASC",
            $st->toSql()
        );
        $this->assertEmpty($st->getParams());
    }

    /**
     * @test
     */
    public function unionOfDifferentTypes(): void
    {
        $st = (new ValuesStatement())
            ->values('(1), (2), (3)')
            ->unionAll((new SelectStatement())->from('t2'))
            ->unionIntersect((new SelectStatement())->from('t3'))
            ->unionIntersectAll((new SelectStatement())->from('t4'))
            ->unionExcept((new ValuesStatement())->values("('a')"))
            ->unionExceptAll((new SelectStatement())->from('t6'))
            ->paginate(10, 5);

        $this->assertSame(
            '(VALUES (1), (2), (3)) ' .
            'UNION ALL (SELECT * FROM t2) ' .
            'INTERSECT (SELECT * FROM t3) ' .
            'INTERSECT ALL (SELECT * FROM t4) ' .
            "EXCEPT (VALUES ('a')) " .
            'EXCEPT ALL (SELECT * FROM t6) ' .
            'LIMIT 5 OFFSET 50',
            $st->toSql()
        );
        $this->assertEmpty($st->getParams());
    }

    //endregion

    //region Copy & Clean

    /**
     * @test
     */
    public function copy(): void
    {
        $executor = $this->getMockBuilder(StatementExecutor::class)->getMock();

        $st = (new ValuesStatement($executor))
            ->values([1])
            ->orderBy('column1', 'DESC')
            ->limit(2)
            ->offset(1);

        $copy = $st->copy();

        $this->assertSame($executor, $copy->getStatementExecutor());
        $this->assertSame(
            'VALUES (:p1) ORDER BY column1 DESC LIMIT 2 OFFSET 1',
            $copy->toSql()
        );
        $this->assertSame(
            [
                'p1' => 1
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

        $st = (new ValuesStatement($executor))
            ->values([1])
            ->orderBy('column1', 'DESC')
            ->limit(2)
            ->offset(1);

        $st->clean();

        $this->assertSame($executor, $st->getStatementExecutor());
        $this->assertSame('VALUES', $st->toSql());
        $this->assertEmpty($st->getParams());
    }

    //endregion
}