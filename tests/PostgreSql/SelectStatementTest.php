<?php

declare(strict_types=1);

namespace Tests\AlephTools\SqlBuilder\PostgreSql;

use AlephTools\SqlBuilder\PostgreSql\SelectStatement;
use AlephTools\SqlBuilder\PostgreSql\ValuesStatement;
use AlephTools\SqlBuilder\Sql\Expression\AbstractExpression;
use AlephTools\SqlBuilder\Sql\Expression\ConditionalExpression;
use AlephTools\SqlBuilder\Sql\Expression\RawExpression;
use AlephTools\SqlBuilder\Sql\Expression\ValueListExpression;
use AlephTools\SqlBuilder\StatementExecutor;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use RuntimeException;

/**
 * @internal
 */
class SelectStatementTest extends TestCase
{
    protected function setUp(): void
    {
        AbstractExpression::resetParameterIndex();
    }

    /**
     * @test
     */
    public function emptySelect(): void
    {
        $st = new SelectStatement();

        self::assertSame('SELECT *', $st->toString());
        self::assertEmpty($st->getParams());
    }

    //region FROM

    /**
     * @test
     */
    public function fromTable(): void
    {
        $st = (new SelectStatement())
            ->from('tb');

        self::assertSame('SELECT * FROM tb', $st->toSql());
        self::assertEmpty($st->getParams());
    }

    /**
     * @test
     */
    public function fromTableWithAlias(): void
    {
        $st = (new SelectStatement())
            ->from('tb', 't1');

        self::assertSame('SELECT * FROM tb t1', $st->toSql());
        self::assertEmpty($st->getParams());
    }

    /**
     * @test
     */
    public function fromArrayOfTables(): void
    {
        $st = (new SelectStatement())
            ->from(['t1', 't2', 't3']);

        self::assertSame('SELECT * FROM t1, t2, t3', $st->toSql());
        self::assertEmpty($st->getParams());
    }

    /**
     * @test
     */
    public function fromListOfTables(): void
    {
        $st = (new SelectStatement())
            ->from(['t1', 't2', 't3']);

        self::assertSame('SELECT * FROM t1, t2, t3', $st->toSql());
        self::assertEmpty($st->getParams());
    }

    /**
     * @test
     */
    public function fromMapOfTablesWithAliases(): void
    {
        $st = (new SelectStatement())
            ->from([
                'a1' => 't1',
                'a2' => 't2',
                'a3' => 't3',
            ]);

        self::assertSame('SELECT * FROM t1 a1, t2 a2, t3 a3', $st->toSql());
        self::assertEmpty($st->getParams());
    }

    /**
     * @test
     */
    public function appendTables(): void
    {
        $st = (new SelectStatement())
            ->from('t1')
            ->from('t2')
            ->from('t3');

        self::assertSame('SELECT * FROM t1, t2, t3', $st->toSql());
        self::assertEmpty($st->getParams());
    }

    /**
     * @test
     */
    public function appendTablesWithAliases(): void
    {
        $st = (new SelectStatement())
            ->from('t1', 'a1')
            ->from('t2', 'a2')
            ->from('t3', 'a3');

        self::assertSame('SELECT * FROM t1 a1, t2 a2, t3 a3', $st->toSql());
        self::assertEmpty($st->getParams());
    }

    /**
     * @test
     */
    public function fromRawExpression(): void
    {
        $st = (new SelectStatement())
            ->from(new RawExpression('tb AS a'));

        self::assertSame('SELECT * FROM tb AS a', $st->toSql());
        self::assertEmpty($st->getParams());
    }

    /**
     * @test
     */
    public function fromAnotherQuery(): void
    {
        $st = (new SelectStatement())
            ->from((new SelectStatement())->from('tb'));

        self::assertSame('SELECT * FROM (SELECT * FROM tb)', $st->toSql());
        self::assertEmpty($st->getParams());
    }

    /**
     * @test
     */
    public function fromAnotherQueryWithAlias(): void
    {
        $st = (new SelectStatement())
            ->from((new SelectStatement())->from('tb'), 'a1');

        self::assertSame('SELECT * FROM (SELECT * FROM tb) a1', $st->toSql());
        self::assertEmpty($st->getParams());
    }

    /**
     * @test
     */
    public function fromListOfQueries(): void
    {
        $st = (new SelectStatement())
            ->from([
                    (new SelectStatement())->from('t1'),
                    (new SelectStatement())->from('t2'),
                    (new SelectStatement())->from('t3'),
            ]);

        self::assertSame(
            'SELECT * FROM (SELECT * FROM t1), (SELECT * FROM t2), (SELECT * FROM t3)',
            $st->toSql()
        );
        self::assertEmpty($st->getParams());
    }

    /**
     * @test
     */
    public function fromMapOfQueriesWithAliases(): void
    {
        $st = (new SelectStatement())
            ->from([
                'a1' => (new SelectStatement())->from('t1'),
                'a2' => (new SelectStatement())->from('t2'),
                'a3' => (new SelectStatement())->from('t3'),
            ]);

        self::assertSame(
            'SELECT * FROM (SELECT * FROM t1) a1, (SELECT * FROM t2) a2, (SELECT * FROM t3) a3',
            $st->toSql()
        );
        self::assertEmpty($st->getParams());
    }

    /**
     * @test
     */
    public function appendMixedFrom(): void
    {
        $st = (new SelectStatement())
            ->from('t1', 'a1')
            ->from((new SelectStatement())->from('t2'), 'a2')
            ->from(['t3']);

        self::assertSame('SELECT * FROM t1 a1, (SELECT * FROM t2) a2, t3', $st->toSql());
        self::assertEmpty($st->getParams());
    }

    //endregion

    //region SELECT

    /**
     * @test
     */
    public function selectColumn(): void
    {
        $st = (new SelectStatement())
            ->select('column')
            ->from('tb');

        self::assertSame('SELECT column FROM tb', $st->toSql());
        self::assertEmpty($st->getParams());
    }

    /**
     * @test
     */
    public function selectColumnWithAlias(): void
    {
        $st = (new SelectStatement())
            ->select('column', 'a1')
            ->from('tb');

        self::assertSame('SELECT column a1 FROM tb', $st->toSql());
        self::assertEmpty($st->getParams());
    }

    /**
     * @test
     */
    public function appendColumnsToSelect(): void
    {
        $st = (new SelectStatement())
            ->select('c1')
            ->select('c2', 'a2')
            ->from('tb');

        self::assertSame('SELECT c1, c2 a2 FROM tb', $st->toSql());
        self::assertEmpty($st->getParams());
    }

    /**
     * @test
     */
    public function selectColumnList(): void
    {
        $st = (new SelectStatement())
            ->select(['c1', 'c2', 'c3'])
            ->from('tb');

        self::assertSame('SELECT c1, c2, c3 FROM tb', $st->toSql());
        self::assertEmpty($st->getParams());
    }

    /**
     * @test
     */
    public function selectColumnMapWithAliases(): void
    {
        $st = (new SelectStatement())
            ->select([
                'a1' => 'c1',
                'a2' => 'c2',
                'a3' => 'c3',
            ])
            ->from('tb');

        self::assertSame('SELECT c1 a1, c2 a2, c3 a3 FROM tb', $st->toSql());
        self::assertEmpty($st->getParams());
    }

    /**
     * @test
     */
    public function selectFromRawExpression(): void
    {
        $st = (new SelectStatement())
            ->select(new RawExpression('c1, c2, c3'))
            ->from('tb');

        self::assertSame('SELECT c1, c2, c3 FROM tb', $st->toSql());
        self::assertEmpty($st->getParams());
    }

    /**
     * @test
     */
    public function selectFromQuery(): void
    {
        $st = (new SelectStatement())
            ->select((new SelectStatement())->from('t2'))
            ->from('t1');

        self::assertSame('SELECT (SELECT * FROM t2) FROM t1', $st->toSql());
        self::assertEmpty($st->getParams());
    }

    /**
     * @test
     */
    public function selectFromQueryWithAlias(): void
    {
        $st = (new SelectStatement())
            ->select((new SelectStatement())->from('t2'), 'a1')
            ->from('t1');

        self::assertSame('SELECT (SELECT * FROM t2) a1 FROM t1', $st->toSql());
        self::assertEmpty($st->getParams());
    }

    /**
     * @test
     */
    public function selectFromMixedSources(): void
    {
        $st = (new SelectStatement())
            ->select([
                'a1' => (new SelectStatement())->from('t2'),
                'a2' => 'c2',
                null,
            ])
            ->select('c3')
            ->select(new ValueListExpression([1, 2, 3]), new RawExpression('a4'))
            ->from('t1');

        self::assertSame('SELECT (SELECT * FROM t2) a1, c2 a2, NULL, c3, (VALUES (:p1, :p2, :p3)) a4 FROM t1', $st->toSql());
        self::assertSame(['p1' => 1, 'p2' => 2, 'p3' => 3], $st->getParams());
    }

    //endregion

    //region JOIN

    /**
     * @test
     */
    public function joinTable(): void
    {
        $st = (new SelectStatement())
            ->from('t1')
            ->join('t2', 't2.id = t1.id');

        self::assertSame('SELECT * FROM t1 JOIN t2 ON t2.id = t1.id', $st->toSql());
        self::assertEmpty($st->getParams());
    }

    /**
     * @test
     */
    public function joinListOfTables(): void
    {
        $st = (new SelectStatement())
            ->from('t1')
            ->join(['t2', 't3'], 't2.id = t1.id AND t3.id = t1.id');

        self::assertSame('SELECT * FROM t1 JOIN (t2, t3) ON t2.id = t1.id AND t3.id = t1.id', $st->toSql());
        self::assertEmpty($st->getParams());
    }

    /**
     * @test
     */
    public function joinListOfTablesWithAliases(): void
    {
        $st = (new SelectStatement())
            ->from('t1')
            ->join(
                [
                'a2' => 't2',
                'a3' => 't3',
            ],
                't2.id = t1.id AND t3.id = t1.id'
            );

        self::assertSame(
            'SELECT * FROM t1 JOIN (t2 a2, t3 a3) ON t2.id = t1.id AND t3.id = t1.id',
            $st->toSql()
        );
        self::assertEmpty($st->getParams());
    }

    /**
     * @test
     */
    public function joinTableWithListOfUsingColumns(): void
    {
        $st = (new SelectStatement())
            ->from('t1')
            ->join('t2', ['t2.c1', 't2.c2']);

        self::assertSame('SELECT * FROM t1 JOIN t2 USING (t2.c1, t2.c2)', $st->toSql());
        self::assertEmpty($st->getParams());
    }

    /**
     * @test
     */
    public function joinSubQuery(): void
    {
        $st = (new SelectStatement())
            ->from('t1')
            ->join((new SelectStatement())->from('t2'), 't1.id = t2.id');

        self::assertSame('SELECT * FROM t1 JOIN (SELECT * FROM t2) ON t1.id = t2.id', $st->toSql());
        self::assertEmpty($st->getParams());
    }

    /**
     * @test
     */
    public function joinSubQueryWithAlias(): void
    {
        $st = (new SelectStatement())
            ->from('t1')
            ->join((new SelectStatement())->from('t2'), 'a2', 't1.id = a2.id');

        self::assertSame('SELECT * FROM t1 JOIN (SELECT * FROM t2) a2 ON t1.id = a2.id', $st->toSql());
        self::assertEmpty($st->getParams());
    }

    /**
     * @test
     */
    public function joinSubQueriesWithAliases(): void
    {
        $st = (new SelectStatement())
            ->from('t1')
            ->join(
                [
                    'a2' => (new SelectStatement())->from('t2'),
                    'a3' => (new SelectStatement())->from('t3'),
                ],
                't1.id = a2.id AND t1.id = a3.id'
            );

        self::assertSame(
            'SELECT * FROM t1 JOIN ((SELECT * FROM t2) a2, (SELECT * FROM t3) a3) ON t1.id = a2.id AND t1.id = a3.id',
            $st->toSql()
        );
        self::assertEmpty($st->getParams());
    }

    /**
     * @test
     */
    public function joinTableWithNestedConditionsClosure(): void
    {
        $st = (new SelectStatement())
            ->from('t1')
            ->join('t2', function (ConditionalExpression $condition) {
                return $condition->with('t2.id', '=', new RawExpression('t1.id'))
                    ->and('t1.f1', '>', 5)
                    ->or('t2.f3', '<>', 'a');
            });

        self::assertSame(
            'SELECT * FROM t1 JOIN t2 ON (t2.id = t1.id AND t1.f1 > :p1 OR t2.f3 <> :p2)',
            $st->toSql()
        );
        self::assertSame(['p1' => 5, 'p2' => 'a'], $st->getParams());
    }

    /**
     * @test
     */
    public function joinTableWithNestedConditions(): void
    {
        $st = (new SelectStatement())
            ->from('t1')
            ->join(
                't2',
                (new ConditionalExpression())
                ->with('t2.id', '=', new RawExpression('t1.id'))
                ->andWhere('t1.f1', '>', new RawExpression('t2.f2'))
                ->orWhere('t2.f3', '<>', new RawExpression('t1.f3'))
                ->or(null)
            );

        self::assertSame(
            'SELECT * FROM t1 JOIN t2 ON (t2.id = t1.id AND t1.f1 > t2.f2 OR t2.f3 <> t1.f3 OR NULL)',
            $st->toSql()
        );
        self::assertEmpty($st->getParams());
    }

    /**
     * @test
     */
    public function joinOfDifferentTypes(): void
    {
        $st = (new SelectStatement())
            ->from('t1')
            ->innerJoin('t2')
            ->naturalInnerJoin('t3')
            ->leftJoin('t4')
            ->leftOuterJoin('t5')
            ->naturalLeftJoin('t6')
            ->naturalLeftOuterJoin('t7')
            ->rightJoin('t8')
            ->rightOuterJoin('t9')
            ->naturalRightJoin('t10')
            ->naturalRightOuterJoin('t11')
            ->fullJoin('t12')
            ->fullOuterJoin('t13')
            ->naturalFullJoin('t14')
            ->naturalFullOuterJoin('t15')
            ->crossJoin('t16');

        self::assertSame(
            'SELECT * FROM t1 ' .
            'INNER JOIN t2 ' .
            'NATURAL INNER JOIN t3 ' .
            'LEFT JOIN t4 ' .
            'LEFT OUTER JOIN t5 ' .
            'NATURAL LEFT JOIN t6 ' .
            'NATURAL LEFT OUTER JOIN t7 ' .
            'RIGHT JOIN t8 ' .
            'RIGHT OUTER JOIN t9 ' .
            'NATURAL RIGHT JOIN t10 ' .
            'NATURAL RIGHT OUTER JOIN t11 ' .
            'FULL JOIN t12 ' .
            'FULL OUTER JOIN t13 ' .
            'NATURAL FULL JOIN t14 ' .
            'NATURAL FULL OUTER JOIN t15 ' .
            'CROSS JOIN t16',
            $st->toSql()
        );
        self::assertEmpty($st->getParams());
    }

    /**
     * @test
     */
    public function joinValues(): void
    {
        $st = (new SelectStatement())
            ->from('t1')
            ->rightJoin(
                (new ValuesStatement())
                    ->values([
                        ['a1', 1],
                        ['a2', 2],
                        ['a3', 3],
                    ]),
                't2 (name, id)',
                't1.id = t2.id'
            );

        self::assertSame(
            'SELECT * FROM t1 RIGHT JOIN (VALUES (:p1, :p2), (:p3, :p4), (:p5, :p6)) t2 (name, id) ON t1.id = t2.id',
            $st->toSql()
        );
        self::assertSame(
            [
                'p1' => 'a1',
                'p2' => 1,
                'p3' => 'a2',
                'p4' => 2,
                'p5' => 'a3',
                'p6' => 3,
            ],
            $st->getParams()
        );
    }

    //endregion

    //region WHERE

    /**
     * @test
     */
    public function whereAsString(): void
    {
        $st = (new SelectStatement())
            ->from('tb')
            ->where('c1 = c2');

        self::assertSame('SELECT * FROM tb WHERE c1 = c2', $st->toSql());
        self::assertEmpty($st->getParams());
    }

    /**
     * @test
     */
    public function whereAsRawValue(): void
    {
        $st = (new SelectStatement())
            ->from('tb')
            ->where(new RawExpression('c1 = c2'));

        self::assertSame('SELECT * FROM tb WHERE c1 = c2', $st->toSql());
        self::assertEmpty($st->getParams());
    }

    /**
     * @test
     */
    public function whereBinaryOpWithScalar(): void
    {
        $st = (new SelectStatement())
            ->from('tb')
            ->where('col', '=', 1);

        self::assertSame('SELECT * FROM tb WHERE col = :p1', $st->toSql());
        self::assertSame(['p1' => 1], $st->getParams());
    }

    /**
     * @test
     */
    public function whereBinaryOpWithNull(): void
    {
        $st = (new SelectStatement())
            ->from('tb')
            ->where('col', '=', null);

        self::assertSame('SELECT * FROM tb WHERE col = NULL', $st->toSql());
        self::assertEmpty($st->getParams());
    }

    /**
     * @test
     */
    public function whereBinaryOpWithQuery(): void
    {
        $st = (new SelectStatement())
            ->from('t1')
            ->where('t1.col', '=', (new SelectStatement())->from('t2')->select('COUNT(*)'));

        self::assertSame('SELECT * FROM t1 WHERE t1.col = (SELECT COUNT(*) FROM t2)', $st->toSql());
        self::assertEmpty($st->getParams());
    }

    /**
     * @test
     */
    public function whereBinaryOpWithTuple(): void
    {
        $st = (new SelectStatement())
            ->from('tb')
            ->where('col', 'IN', [1, 2, 3]);

        self::assertSame('SELECT * FROM tb WHERE col IN (:p1, :p2, :p3)', $st->toSql());
        self::assertSame(['p1' => 1, 'p2' => 2, 'p3' => 3], $st->getParams());
    }

    /**
     * @test
     */
    public function whereBinaryOpBetween(): void
    {
        $st = (new SelectStatement())
            ->from('tb')
            ->where('col', 'BETWEEN', [1, 2]);

        self::assertSame('SELECT * FROM tb WHERE col BETWEEN :p1 AND :p2', $st->toSql());
        self::assertSame(['p1' => 1, 'p2' => 2], $st->getParams());
    }

    /**
     * @test
     */
    public function whereBinaryOpWithRawValue(): void
    {
        $st = (new SelectStatement())
            ->from('tb')
            ->where('c1', '=', new RawExpression('c2'));

        self::assertSame('SELECT * FROM tb WHERE c1 = c2', $st->toSql());
        self::assertEmpty($st->getParams());
    }

    /**
     * @test
     */
    public function whereUnaryOpWithScalar(): void
    {
        $st = (new SelectStatement())
            ->from('tb')
            ->where('NOT', new RawExpression('col'));

        self::assertSame('SELECT * FROM tb WHERE NOT col', $st->toSql());
        self::assertEmpty($st->getParams());
    }

    /**
     * @test
     */
    public function whereUnaryOpWithQuery(): void
    {
        $st = (new SelectStatement())
            ->from('t1')
            ->where('NOT', (new SelectStatement())->from('t2')->select('COUNT(*)'));

        self::assertSame('SELECT * FROM t1 WHERE NOT (SELECT COUNT(*) FROM t2)', $st->toSql());
        self::assertEmpty($st->getParams());
    }

    /**
     * @test
     */
    public function whereWithQueryAsOperand(): void
    {
        $st = (new SelectStatement())
            ->from('t1')
            ->where((new SelectStatement())->from('t2')->select('COUNT(*)'), '>', 5);

        self::assertSame('SELECT * FROM t1 WHERE (SELECT COUNT(*) FROM t2) > :p1', $st->toSql());
        self::assertSame(['p1' => 5], $st->getParams());
    }

    /**
     * @test
     */
    public function whereWithQueriesAsOperandAndValue(): void
    {
        $st = (new SelectStatement())
            ->from('t1')
            ->where(
                (new SelectStatement())->from('t2')->select('COUNT(*)'),
                '<>',
                (new SelectStatement())->from('t3')->select('COUNT(*)')
            );

        self::assertSame(
            'SELECT * FROM t1 WHERE (SELECT COUNT(*) FROM t2) <> (SELECT COUNT(*) FROM t3)',
            $st->toSql()
        );
        self::assertEmpty($st->getParams());
    }

    /**
     * @test
     */
    public function whereAsConditionList(): void
    {
        $st = (new SelectStatement())
            ->from('tb')
            ->where(['c1 = c2', 'c3 <> c4']);

        self::assertSame('SELECT * FROM tb WHERE c1 = c2 AND c3 <> c4', $st->toSql());
        self::assertEmpty($st->getParams());
    }

    /**
     * @test
     */
    public function whereAsConditionMap(): void
    {
        $st = (new SelectStatement())
            ->from('tb')
            ->where(['c1' => 1, 'c2' => 2]);

        self::assertSame('SELECT * FROM tb WHERE c1 = :p1 AND c2 = :p2', $st->toSql());
        self::assertSame(['p1' => 1, 'p2' => 2], $st->getParams());
    }

    /**
     * @test
     */
    public function whereWithNestedConditionsAsConditionalExpression(): void
    {
        $st = (new SelectStatement())
            ->from('tb')
            ->where('c1 IS NULL')
            ->andWhere(
                (new ConditionalExpression())
                    ->orWhere('c2', '=', 1)
                    ->orWhere('c3', '<', 2)
            );

        self::assertSame('SELECT * FROM tb WHERE c1 IS NULL AND (c2 = :p1 OR c3 < :p2)', $st->toSql());
        self::assertSame(['p1' => 1, 'p2' => 2], $st->getParams());
    }

    /**
     * @test
     */
    public function whereWithNestedConditionsAsClosure(): void
    {
        $st = (new SelectStatement())
            ->from('tb')
            ->where('c1 IS NULL')
            ->where(function (ConditionalExpression $condition) {
                return $condition->orWhere('c2', '=', 1)
                    ->orWhere('c3', '<', 2);
            });

        self::assertSame('SELECT * FROM tb WHERE c1 IS NULL AND (c2 = :p1 OR c3 < :p2)', $st->toSql());
        self::assertSame(['p1' => 1, 'p2' => 2], $st->getParams());
    }

    //endregion

    //region HAVING

    /**
     * @test
     */
    public function havingAsString(): void
    {
        $st = (new SelectStatement())
            ->from('tb')
            ->having('c1 = c2');

        self::assertSame('SELECT * FROM tb HAVING c1 = c2', $st->toSql());
        self::assertEmpty($st->getParams());
    }

    /**
     * @test
     */
    public function havingAsRawValue(): void
    {
        $st = (new SelectStatement())
            ->from('tb')
            ->having(new RawExpression('c1 = c2'));

        self::assertSame('SELECT * FROM tb HAVING c1 = c2', $st->toSql());
        self::assertEmpty($st->getParams());
    }

    /**
     * @test
     */
    public function havingBinaryOpWithScalar(): void
    {
        $st = (new SelectStatement())
            ->from('tb')
            ->having('col', '=', 1);

        self::assertSame('SELECT * FROM tb HAVING col = :p1', $st->toSql());
        self::assertSame(['p1' => 1], $st->getParams());
    }

    /**
     * @test
     */
    public function havingBinaryOpWithNull(): void
    {
        $st = (new SelectStatement())
            ->from('tb')
            ->having('col', '=', null);

        self::assertSame('SELECT * FROM tb HAVING col = NULL', $st->toSql());
        self::assertEmpty($st->getParams());
    }

    /**
     * @test
     */
    public function havingBinaryOpWithQuery(): void
    {
        $st = (new SelectStatement())
            ->from('t1')
            ->having('t1.col', '=', (new SelectStatement())->from('t2')->select('COUNT(*)'));

        self::assertSame('SELECT * FROM t1 HAVING t1.col = (SELECT COUNT(*) FROM t2)', $st->toSql());
        self::assertEmpty($st->getParams());
    }

    /**
     * @test
     */
    public function havingBinaryOpWithTuple(): void
    {
        $st = (new SelectStatement())
            ->from('tb')
            ->having('col', 'IN', [1, 2, 3]);

        self::assertSame('SELECT * FROM tb HAVING col IN (:p1, :p2, :p3)', $st->toSql());
        self::assertSame(['p1' => 1, 'p2' => 2, 'p3' => 3], $st->getParams());
    }

    /**
     * @test
     */
    public function havingBinaryOpBetween(): void
    {
        $st = (new SelectStatement())
            ->from('tb')
            ->having('col', 'BETWEEN', [1, 2]);

        self::assertSame('SELECT * FROM tb HAVING col BETWEEN :p1 AND :p2', $st->toSql());
        self::assertSame(['p1' => 1, 'p2' => 2], $st->getParams());
    }

    /**
     * @test
     */
    public function havingBinaryOpWithRawValue(): void
    {
        $st = (new SelectStatement())
            ->from('tb')
            ->having('c1', '=', new RawExpression('c2'));

        self::assertSame('SELECT * FROM tb HAVING c1 = c2', $st->toSql());
        self::assertEmpty($st->getParams());
    }

    /**
     * @test
     */
    public function havingUnaryOpWithScalar(): void
    {
        $st = (new SelectStatement())
            ->from('tb')
            ->having('NOT', new RawExpression('col'));

        self::assertSame('SELECT * FROM tb HAVING NOT col', $st->toSql());
        self::assertEmpty($st->getParams());
    }

    /**
     * @test
     */
    public function havingUnaryOpWithQuery(): void
    {
        $st = (new SelectStatement())
            ->from('t1')
            ->having('NOT', (new SelectStatement())->from('t2')->select('COUNT(*)'));

        self::assertSame('SELECT * FROM t1 HAVING NOT (SELECT COUNT(*) FROM t2)', $st->toSql());
        self::assertEmpty($st->getParams());
    }

    /**
     * @test
     */
    public function havingWithQueryAsOperand(): void
    {
        $st = (new SelectStatement())
            ->from('t1')
            ->having((new SelectStatement())->from('t2')->select('COUNT(*)'), '>', 5);

        self::assertSame('SELECT * FROM t1 HAVING (SELECT COUNT(*) FROM t2) > :p1', $st->toSql());
        self::assertSame(['p1' => 5], $st->getParams());
    }

    /**
     * @test
     */
    public function havingWithQueriesAsOperandAndValue(): void
    {
        $st = (new SelectStatement())
            ->from('t1')
            ->having(
                (new SelectStatement())->from('t2')->select('COUNT(*)'),
                '<>',
                (new SelectStatement())->from('t3')->select('COUNT(*)')
            );

        self::assertSame(
            'SELECT * FROM t1 HAVING (SELECT COUNT(*) FROM t2) <> (SELECT COUNT(*) FROM t3)',
            $st->toSql()
        );
        self::assertEmpty($st->getParams());
    }

    /**
     * @test
     */
    public function havingAsConditionList(): void
    {
        $st = (new SelectStatement())
            ->from('tb')
            ->having(['c1 = c2', 'c3 <> c4']);

        self::assertSame('SELECT * FROM tb HAVING c1 = c2 AND c3 <> c4', $st->toSql());
        self::assertEmpty($st->getParams());
    }

    /**
     * @test
     */
    public function havingAsConditionMap(): void
    {
        $st = (new SelectStatement())
            ->from('tb')
            ->having(['c1' => 1, 'c2' => 2]);

        self::assertSame('SELECT * FROM tb HAVING c1 = :p1 AND c2 = :p2', $st->toSql());
        self::assertSame(['p1' => 1, 'p2' => 2], $st->getParams());
    }

    /**
     * @test
     */
    public function havingWithNestedConditionsAsConditionalExpression(): void
    {
        $st = (new SelectStatement())
            ->from('tb')
            ->having('c1 IS NULL')
            ->having(
                (new ConditionalExpression())
                ->or('c2', '=', 1)
                ->or('c3', '<', 2)
            );

        self::assertSame('SELECT * FROM tb HAVING c1 IS NULL AND (c2 = :p1 OR c3 < :p2)', $st->toSql());
        self::assertSame(['p1' => 1, 'p2' => 2], $st->getParams());
    }

    /**
     * @test
     */
    public function havingWithNestedConditionsAsClosure(): void
    {
        $st = (new SelectStatement())
            ->from('tb')
            ->andHaving('c1 IS NULL')
            ->orHaving(function (ConditionalExpression $condition) {
                return $condition->or('c2', '=', 1)
                    ->or('c3', '<', 2);
            });

        self::assertSame('SELECT * FROM tb HAVING c1 IS NULL OR (c2 = :p1 OR c3 < :p2)', $st->toSql());
        self::assertSame(['p1' => 1, 'p2' => 2], $st->getParams());
    }

    //endregion

    //region GROUP BY

    /**
     * @test
     */
    public function groupByColumn(): void
    {
        $st = (new SelectStatement())
            ->from('tb')
            ->groupBy('col');

        self::assertSame('SELECT * FROM tb GROUP BY col', $st->toSql());
        self::assertEmpty($st->getParams());
    }

    /**
     * @test
     */
    public function groupByColumnWithDirection(): void
    {
        $st = (new SelectStatement())
            ->from('tb')
            ->groupBy('col', 'DESC');

        self::assertSame('SELECT * FROM tb GROUP BY col DESC', $st->toSql());
        self::assertEmpty($st->getParams());
    }

    /**
     * @test
     */
    public function groupByColumnList(): void
    {
        $st = (new SelectStatement())
            ->from('tb')
            ->groupBy(['c1', 'c2', 'c3']);

        self::assertSame('SELECT * FROM tb GROUP BY c1, c2, c3', $st->toSql());
        self::assertEmpty($st->getParams());
    }

    /**
     * @test
     */
    public function groupByColumnsWithDirections(): void
    {
        $st = (new SelectStatement())
            ->from('tb')
            ->groupBy(['c1' => 'ASC', 'c2' => 'DESC', 'c3' => '']);

        self::assertSame('SELECT * FROM tb GROUP BY c1 ASC, c2 DESC, c3', $st->toSql());
        self::assertEmpty($st->getParams());
    }

    /**
     * @test
     */
    public function groupByAppendedColumns(): void
    {
        $st = (new SelectStatement())
            ->from('tb')
            ->groupBy('c1', '')
            ->groupBy('c2', 'ASC')
            ->groupBy('c3', 'DESC');

        self::assertSame('SELECT * FROM tb GROUP BY c1, c2 ASC, c3 DESC', $st->toSql());
        self::assertEmpty($st->getParams());
    }

    /**
     * @test
     */
    public function groupByQuery(): void
    {
        $st = (new SelectStatement())
            ->from('t1')
            ->groupBy((new SelectStatement())->from('t2')->select('t2.id'), 'DESC');

        self::assertSame('SELECT * FROM t1 GROUP BY (SELECT t2.id FROM t2) DESC', $st->toSql());
        self::assertEmpty($st->getParams());
    }

    /**
     * @test
     */
    public function groupByMixedSources(): void
    {
        $st = (new SelectStatement())
            ->from('t1')
            ->groupBy('c1 ASC')
            ->groupBy(new RawExpression('c2 DESC'))
            ->groupBy(['c3', 'c4'])
            ->groupBy(['c5' => 'DESC'])
            ->groupBy((new SelectStatement())->from('t2')->select('t2.id'));

        self::assertEquals(
            'SELECT * FROM t1 GROUP BY c1 ASC, c2 DESC, c3, c4, c5 DESC, (SELECT t2.id FROM t2)',
            $st->toSql()
        );
        self::assertEmpty($st->getParams());
    }

    //endregion

    //region ORDER BY

    /**
     * @test
     */
    public function orderByColumn(): void
    {
        $st = (new SelectStatement())
            ->from('tb')
            ->orderBy('col');

        self::assertEquals('SELECT * FROM tb ORDER BY col', $st->toSql());
        self::assertEmpty($st->getParams());
    }

    /**
     * @test
     */
    public function orderByColumnWithDirection(): void
    {
        $st = (new SelectStatement())
            ->from('tb')
            ->orderBy('col', 'DESC');

        self::assertSame('SELECT * FROM tb ORDER BY col DESC', $st->toSql());
        self::assertEmpty($st->getParams());
    }

    /**
     * @test
     */
    public function orderByColumnList(): void
    {
        $st = (new SelectStatement())
            ->from('tb')
            ->orderBy(['c1', 'c2', 'c3']);

        self::assertSame('SELECT * FROM tb ORDER BY c1, c2, c3', $st->toSql());
        self::assertEmpty($st->getParams());
    }

    /**
     * @test
     */
    public function orderByColumnsWithDirections(): void
    {
        $st = (new SelectStatement())
            ->from('tb')
            ->orderBy(['c1' => 'ASC', 'c2' => 'DESC', 'c3' => '']);

        self::assertSame('SELECT * FROM tb ORDER BY c1 ASC, c2 DESC, c3', $st->toSql());
        self::assertEmpty($st->getParams());
    }

    /**
     * @test
     */
    public function orderByAppendedColumns(): void
    {
        $st = (new SelectStatement())
            ->from('tb')
            ->orderBy('c1', '')
            ->orderBy('c2', 'ASC')
            ->orderBy('c3', 'DESC');

        self::assertSame('SELECT * FROM tb ORDER BY c1, c2 ASC, c3 DESC', $st->toSql());
        self::assertEmpty($st->getParams());
    }

    /**
     * @test
     */
    public function orderByQuery(): void
    {
        $st = (new SelectStatement())
            ->from('t1')
            ->orderBy((new SelectStatement())->from('t2')->select('t2.id'), 'DESC');

        self::assertSame('SELECT * FROM t1 ORDER BY (SELECT t2.id FROM t2) DESC', $st->toSql());
        self::assertEmpty($st->getParams());
    }

    /**
     * @test
     */
    public function orderByMixedSources(): void
    {
        $st = (new SelectStatement())
            ->from('t1')
            ->orderBy('c1 ASC')
            ->orderBy(new RawExpression('c2 DESC'))
            ->orderBy(['c3', 'c4'])
            ->orderBy(['c5' => 'DESC'])
            ->orderBy((new SelectStatement())->from('t2')->select('t2.id'));

        self::assertSame(
            'SELECT * FROM t1 ORDER BY c1 ASC, c2 DESC, c3, c4, c5 DESC, (SELECT t2.id FROM t2)',
            $st->toSql()
        );
        self::assertEmpty($st->getParams());
    }

    //endregion

    //region LIMIT & OFFSET

    /**
     * @test
     */
    public function limit(): void
    {
        $st = (new SelectStatement())
            ->from('tb')
            ->limit(10);

        self::assertSame('SELECT * FROM tb LIMIT 10', $st->toSql());
        self::assertEmpty($st->getParams());
    }

    /**
     * @test
     */
    public function offset(): void
    {
        $st = (new SelectStatement())
            ->from('tb')
            ->offset(12);

        self::assertSame('SELECT * FROM tb OFFSET 12', $st->toSql());
        self::assertEmpty($st->getParams());
    }

    /**
     * @test
     */
    public function limitAndOffset(): void
    {
        $st = (new SelectStatement())
            ->from('tb')
            ->offset(5)
            ->limit(12);

        self::assertSame('SELECT * FROM tb LIMIT 12 OFFSET 5', $st->toSql());
        self::assertEmpty($st->getParams());
    }

    /**
     * @test
     */
    public function pagination(): void
    {
        $st = (new SelectStatement())
            ->from('tb')
            ->paginate(3, 7);

        self::assertSame('SELECT * FROM tb LIMIT 7 OFFSET 21', $st->toSql());
        self::assertEmpty($st->getParams());
    }

    //endregion

    //region UNION

    /**
     * @test
     */
    public function unionOfSimpleQueries(): void
    {
        $st = (new SelectStatement())
            ->from('t1')
            ->union((new SelectStatement())->from('t2'));

        self::assertSame('(SELECT * FROM t1) UNION (SELECT * FROM t2)', $st->toSql());
        self::assertEmpty($st->getParams());
    }

    /**
     * @test
     */
    public function unionOfQueriesWithSorting(): void
    {
        $st = (new SelectStatement())
            ->from('t1')
            ->union(
                (new SelectStatement())
                    ->from('t2')
                    ->orderBy('t2.id', 'ASC')
            )
            ->union(
                (new SelectStatement())
                    ->from('t3')
                    ->orderBy('t3.id', 'DESC')
            )
            ->orderBy('id', 'DESC');

        self::assertSame(
            '(SELECT * FROM t1) UNION (SELECT * FROM t2 ORDER BY t2.id ASC) UNION ' .
            '(SELECT * FROM t3 ORDER BY t3.id DESC) ORDER BY id DESC',
            $st->toSql()
        );
        self::assertEmpty($st->getParams());
    }

    /**
     * @test
     */
    public function unionOfDifferentTypes(): void
    {
        $st = (new SelectStatement())
            ->from('t1')
            ->unionAll((new SelectStatement())->from('t2'))
            ->unionIntersect((new SelectStatement())->from('t3'))
            ->unionIntersectAll((new SelectStatement())->from('t4'))
            ->unionExcept((new SelectStatement())->from('t5'))
            ->unionExceptAll((new SelectStatement())->from('t6'))
            ->paginate(10, 5);

        self::assertEquals(
            '(SELECT * FROM t1) ' .
            'UNION ALL (SELECT * FROM t2) ' .
            'INTERSECT (SELECT * FROM t3) ' .
            'INTERSECT ALL (SELECT * FROM t4) ' .
            'EXCEPT (SELECT * FROM t5) ' .
            'EXCEPT ALL (SELECT * FROM t6) ' .
            'LIMIT 5 OFFSET 50',
            $st->toSql()
        );
        self::assertEmpty($st->getParams());
    }

    //endregion

    //region WITH

    /**
     * @test
     */
    public function withSimpleQuery(): void
    {
        $st = (new SelectStatement())
            ->with((new SelectStatement())->from('t1'), 'tb')
            ->from('tb');

        self::assertSame('WITH tb AS (SELECT * FROM t1) SELECT * FROM tb', $st->toSql());
        self::assertEmpty($st->getParams());
    }

    /**
     * @test
     */
    public function withRawExpression(): void
    {
        $st = (new SelectStatement())
            ->with('(SELECT * FROM t1)', 'tb')
            ->with(new RawExpression('n1 AS NULL'))
            ->with(null, 'n2')
            ->from('tb');

        self::assertSame(
            'WITH tb AS (SELECT * FROM t1), n1 AS NULL, n2 AS NULL SELECT * FROM tb',
            $st->toSql()
        );
        self::assertEmpty($st->getParams());
    }

    /**
     * @test
     */
    public function withSeveralQueries(): void
    {
        $st = (new SelectStatement())
            ->with(
                (new SelectStatement())
                    ->from('orders')
                    ->select(['region', 'total_sales' => 'SUM(amount)'])
                    ->groupBy("region"),
                'regional_sales'
            )
            ->with(
                (new SelectStatement())
                    ->from('regional_sales')
                    ->select('region')
                    ->select('SUM(total_sales) / 10')
                    ->where('total_sales', '>', (new SelectStatement())->from('regional_sales')),
                'top_regions'
            )
            ->from('orders')
            ->select('region')
            ->select('product')
            ->select([
                'product_units' => 'SUM(quantity)',
                'product_sales' => 'SUM(amount)',
            ])
            ->where('region', 'IN', (new SelectStatement())->from('top_regions')->select('region'))
            ->orderBy(['region', 'product']);

        self::assertSame(
            'WITH regional_sales AS (SELECT region, SUM(amount) total_sales FROM orders GROUP BY region), ' .
            'top_regions AS (SELECT region, SUM(total_sales) / 10 FROM regional_sales WHERE total_sales > ' .
            '(SELECT * FROM regional_sales)) SELECT region, product, SUM(quantity) product_units, ' .
            'SUM(amount) product_sales FROM orders WHERE region IN (SELECT region FROM top_regions) ' .
            'ORDER BY region, product',
            $st->toSql()
        );
        self::assertEmpty($st->getParams());
    }

    /**
     * @test
     */
    public function withRecursiveQuery(): void
    {
        $st = (new SelectStatement())
            ->withRecursive(
                (new ValuesStatement())
                    ->values([1])
                    ->unionAll(
                        (new SelectStatement())
                            ->from('t')
                            ->select((new RawExpression('n + 1'))->toString())
                            ->where('n', '<', 100)
                    ),
                't(n)'
            )
            ->from('t')
            ->select('SUM(n)');

        self::assertSame(
            'WITH RECURSIVE t(n) AS ((VALUES (:p1)) UNION ALL (SELECT n + 1 FROM t WHERE n < :p2)) ' .
            'SELECT SUM(n) FROM t',
            $st->toSql()
        );
        self::assertSame(['p1' => 1, 'p2' => 100], $st->getParams());
    }

    //endregion


    //region Statement Execution

    /**
     * @test
     */
    public function validateExecutorInstance(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('The statement executor must not be null.');

        (new SelectStatement())
            ->from('tb')
            ->rows();
    }

    /**
     * @test
     */
    public function scalar(): void
    {
        $executor = $this->getExecutorMock();

        $executor->method('scalar')->willReturnCallback(function (string $sql, array $params) {
            $this->assertSame('SELECT c1, c2 FROM tb WHERE c3 = :p1', $sql);
            $this->assertSame(['p1' => 5], $params);
            return 7;
        });

        $st = (new SelectStatement($executor))
            ->from('tb')
            ->select('c1, c2')
            ->where('c3', '=', 5);

        self::assertSame(7, $st->scalar());
    }

    /**
     * @test
     */
    public function scalarWithExpression(): void
    {
        $executor = $this->getExecutorMock();

        $executor->method('scalar')->willReturnCallback(function (string $sql, array $params) {
            $this->assertSame('SELECT c1 FROM tb WHERE c3 = :p1', $sql);
            $this->assertSame(['p1' => 5], $params);
            return 7;
        });

        $st = (new SelectStatement($executor))
            ->from('tb')
            ->select('c1, c2')
            ->where('c3', '=', 5);

        self::assertSame(7, $st->scalar('c1'));
    }

    /**
     * @test
     */
    public function countWithoutExpressionAndNonConditionalClauses(): void
    {
        $executor = $this->getExecutorMock();

        $executor->method('scalar')->willReturnCallback(function (string $sql, array $params) {
            $this->assertSame('SELECT COUNT(*) FROM tb WHERE c3 = :p1', $sql);
            $this->assertSame(['p1' => 5], $params);
            return 7;
        });

        $st = (new SelectStatement($executor))
            ->from('tb')
            ->select('c1, c2')
            ->where('c3', '=', 5)
            ->groupBy('c2')
            ->limit(10);

        self::assertSame(7, $st->count());
    }

    /**
     * @test
     */
    public function countWithNonConditionalClauses(): void
    {
        $executor = $this->getExecutorMock();

        $executor->method('scalar')->willReturnCallback(function (string $sql, array $params) {
            $this->assertSame('SELECT COUNT(*) FROM tb WHERE c3 = :p1 GROUP BY c2 LIMIT 10', $sql);
            $this->assertSame(['p1' => 5], $params);
            return 7;
        });

        $st = (new SelectStatement($executor))
            ->from('tb')
            ->select('c1, c2')
            ->where('c3', '=', 5)
            ->groupBy('c2')
            ->limit(10);

        self::assertSame(7, $st->countWithNonConditionalClauses());
    }

    /**
     * @test
     */
    public function countWithExpressionAndWithoutNonConditionalClauses(): void
    {
        $executor = $this->getExecutorMock();

        $executor->method('scalar')->willReturnCallback(function (string $sql, array $params) {
            $this->assertSame('SELECT COUNT(c4) FROM tb WHERE c3 = :p1', $sql);
            $this->assertSame(['p1' => 5], $params);
            return 7;
        });

        $st = (new SelectStatement($executor))
            ->from('tb')
            ->select('c1, c2')
            ->where('c3', '=', 5)
            ->groupBy('c2')
            ->limit(10);

        self::assertSame(7, $st->count('c4'));
    }

    /**
     * @test
     */
    public function countWithExpressionAndNonConditionalClauses(): void
    {
        $executor = $this->getExecutorMock();

        $executor->method('scalar')->willReturnCallback(function (string $sql, array $params) {
            $this->assertSame('SELECT COUNT(c4) FROM tb WHERE c3 = :p1 GROUP BY c2 LIMIT 10', $sql);
            $this->assertSame(['p1' => 5], $params);
            return 7;
        });

        $st = (new SelectStatement($executor))
            ->from('tb')
            ->select('c1, c2')
            ->where('c3', '=', 5)
            ->groupBy('c2')
            ->limit(10);

        self::assertSame(7, $st->countWithNonConditionalClauses('c4'));
    }

    /**
     * @test
     */
    public function column(): void
    {
        $executor = $this->getExecutorMock();

        $executor->method('column')->willReturnCallback(function (string $sql, array $params) {
            $this->assertSame('SELECT c1, c2 FROM tb WHERE c3 = :p1', $sql);
            $this->assertSame(['p1' => 5], $params);
            return [7];
        });

        $st = (new SelectStatement($executor))
            ->from('tb')
            ->select('c1, c2')
            ->where('c3', '=', 5);

        self::assertSame([7], $st->column());
    }

    /**
     * @test
     */
    public function columnWithExpression(): void
    {
        $executor = $this->getExecutorMock();

        $executor->method('column')->willReturnCallback(function (string $sql, array $params) {
            $this->assertSame('SELECT c1 FROM tb WHERE c3 = :p1', $sql);
            $this->assertSame(['p1' => 5], $params);
            return [7];
        });

        $st = (new SelectStatement($executor))
            ->from('tb')
            ->select('c1, c2')
            ->where('c3', '=', 5);

        self::assertSame([7], $st->column('c1'));
    }

    /**
     * @test
     */
    public function row(): void
    {
        $executor = $this->getExecutorMock();

        $executor->method('row')->willReturnCallback(function (string $sql, array $params) {
            $this->assertSame('SELECT c1, c2 FROM tb WHERE c3 = :p1', $sql);
            $this->assertSame(['p1' => 5], $params);
            return [7];
        });

        $st = (new SelectStatement($executor))
            ->from('tb')
            ->select('c1, c2')
            ->where('c3', '=', 5);

        self::assertSame([7], $st->row());
    }

    /**
     * @test
     */
    public function rows(): void
    {
        $executor = $this->getExecutorMock();

        $executor->method('rows')->willReturnCallback(function (string $sql, array $params) {
            $this->assertSame('SELECT c1, c2 FROM tb WHERE c3 = :p1', $sql);
            $this->assertSame(['p1' => 5], $params);
            return [[7]];
        });

        $st = (new SelectStatement($executor))
            ->from('tb')
            ->select('c1, c2')
            ->where('c3', '=', 5);

        self::assertSame([[7]], $st->rows());
    }

    /**
     * @test
     */
    public function pairsWithNonExistentKey(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Key "c4" is not found in the row set.');

        $this->getSelectStatementMock()->pairs('c4');
    }

    /**
     * @test
     */
    public function pairs(): void
    {
        self::assertSame(
            ['v1' => 'v2', 'v3' => 'v4', 'v5' => 'v6'],
            $this->getSelectStatementMock()->pairs()
        );

        self::assertSame(
            ['v1' => 'v2', 'v3' => 'v4', 'v5' => 'v6'],
            $this->getSelectStatementMock()->pairs('c1')
        );

        self::assertSame(
            ['v2' => 'v1', 'v4' => 'v3', 'v6' => 'v5'],
            $this->getSelectStatementMock()->pairs('c2')
        );

        self::assertSame(
            ['a' => 'v1', 'b' => 'v5'],
            $this->getSelectStatementMock()->pairs('c3')
        );
    }

    /**
     * @test
     */
    public function rowsByNonExistentKey(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Key "c4" is not found in the row set.');

        $this->getSelectStatementMock()->rowsByKey('c4');
    }

    /**
     * @test
     */
    public function rowsByKeyWithKey(): void
    {
        self::assertSame(
            [
                'v2' => ['c1' => 'v1', 'c2' => 'v2', 'c3' => 'a'],
                'v4' => ['c1' => 'v3', 'c2' => 'v4', 'c3' => 'b'],
                'v6' => ['c1' => 'v5', 'c2' => 'v6', 'c3' => 'b'],
            ],
            $this->getSelectStatementMock()->rowsByKey('c2')
        );

        self::assertSame(
            [
                'a' => ['c1' => 'v1', 'c2' => 'v2', 'c3' => 'a'],
                'b' => ['c1' => 'v5', 'c2' => 'v6', 'c3' => 'b'],
            ],
            $this->getSelectStatementMock()->rowsByKey('c3')
        );
    }

    /**
     * @test
     */
    public function rowsByKeyWithoutKey(): void
    {
        self::assertSame(
            [
                'v1' => ['c2' => 'v2', 'c3' => 'a'],
                'v3' => ['c2' => 'v4', 'c3' => 'b'],
                'v5' => ['c2' => 'v6', 'c3' => 'b'],
            ],
            $this->getSelectStatementMock()->rowsByKey('c1', true)
        );

        self::assertSame(
            [
                'a' => ['c1' => 'v1', 'c2' => 'v2'],
                'b' => ['c1' => 'v5', 'c2' => 'v6'],
            ],
            $this->getSelectStatementMock()->rowsByKey('c3', true)
        );
    }

    /**
     * @test
     */
    public function rowsByGroupWithNonExistentKey(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Key "c4" is not found in the row set.');

        $this->getSelectStatementMock()->rowsByGroup('c4');
    }

    /**
     * @test
     */
    public function rowsByGroupWithKey(): void
    {
        self::assertSame(
            [
                'v2' => [
                    ['c1' => 'v1', 'c2' => 'v2', 'c3' => 'a'],
                ],
                'v4' => [
                    ['c1' => 'v3', 'c2' => 'v4', 'c3' => 'b'],
                ],
                'v6' => [
                    ['c1' => 'v5', 'c2' => 'v6', 'c3' => 'b'],
                ],
            ],
            $this->getSelectStatementMock()->rowsByGroup('c2')
        );

        self::assertSame(
            [
                'a' => [
                    ['c1' => 'v1', 'c2' => 'v2', 'c3' => 'a'],
                ],
                'b' => [
                    ['c1' => 'v3', 'c2' => 'v4', 'c3' => 'b'],
                    ['c1' => 'v5', 'c2' => 'v6', 'c3' => 'b'],
                ],
            ],
            $this->getSelectStatementMock()->rowsByGroup('c3')
        );
    }

    /**
     * @test
     */
    public function rowsByGroupWithoutKey(): void
    {
        self::assertSame(
            [
                'v2' => [
                    ['c1' => 'v1', 'c3' => 'a'],
                ],
                'v4' => [
                    ['c1' => 'v3', 'c3' => 'b'],
                ],
                'v6' => [
                    ['c1' => 'v5', 'c3' => 'b'],
                ],
            ],
            $this->getSelectStatementMock()->rowsByGroup('c2', true)
        );

        self::assertSame(
            [
                'a' => [
                    ['c1' => 'v1', 'c2' => 'v2'],
                ],
                'b' => [
                    ['c1' => 'v3', 'c2' => 'v4'],
                    ['c1' => 'v5', 'c2' => 'v6'],
                ],
            ],
            $this->getSelectStatementMock()->rowsByGroup('c3', true)
        );
    }

    /**
     * @test
     */
    public function pagesWithZeroSize(): void
    {
        $st = $this->getSelectStatementMockWithGenerator();
        $pages = $st->pages(0);

        self::assertEmpty(iterator_to_array($pages));
    }

    /**
     * @test
     */
    public function pagesWithZeroOffset(): void
    {
        $st = $this->getSelectStatementMockWithGenerator();
        $pages = $st->pages(2);

        $i = 0;
        foreach ($pages as $row) {
            if ($i === 0) {
                self::assertSame(['c1' => 'v1', 'c2' => 'v2', 'c3' => 'a'], $row);
            } elseif ($i === 1) {
                self::assertSame(['c1' => 'v3', 'c2' => 'v4', 'c3' => 'b'], $row);
            } else {
                self::assertSame(['c1' => 'v5', 'c2' => 'v6', 'c3' => 'b'], $row);
            }
            ++$i;
        }
    }

    /**
     * @test
     */
    public function pagesWithOffset(): void
    {
        $st = $this->getSelectStatementMockWithGenerator();
        $pages = $st->pages(2, 1);

        foreach ($pages as $row) {
            self::assertSame(['c1' => 'v5', 'c2' => 'v6', 'c3' => 'b'], $row);
        }
    }

    /**
     * @test
     */
    public function batchesWithZeroSize(): void
    {
        $st = $this->getSelectStatementMockWithGenerator();
        $pages = $st->batches(0);

        self::assertEmpty(iterator_to_array($pages));
    }

    /**
     * @test
     */
    public function batchesWithZeroOffset(): void
    {
        $st = $this->getSelectStatementMockWithGenerator();
        $pages = $st->batches(2);

        $i = 0;
        foreach ($pages as $row) {
            if ($i === 0) {
                self::assertSame(
                    [
                        ['c1' => 'v1', 'c2' => 'v2', 'c3' => 'a'],
                        ['c1' => 'v3', 'c2' => 'v4', 'c3' => 'b'],
                    ],
                    $row
                );
            } else {
                self::assertSame(
                    [
                        ['c1' => 'v5', 'c2' => 'v6', 'c3' => 'b'],
                    ],
                    $row
                );
            }
            ++$i;
        }
    }

    /**
     * @test
     */
    public function batchesWithOffset(): void
    {
        $st = $this->getSelectStatementMockWithGenerator();
        $pages = $st->batches(2, 1);

        foreach ($pages as $row) {
            self::assertSame([['c1' => 'v5', 'c2' => 'v6', 'c3' => 'b']], $row);
        }
    }

    private function getSelectStatementMockWithGenerator(): SelectStatement
    {
        $p = $s = 0;

        $st = $this->getMockBuilder(SelectStatement::class)
            ->onlyMethods(['paginate', 'rows'])
            ->getMock();

        $st->method('paginate')->willReturnCallback(function (int $page, int $size) use (&$p, &$s, $st) {
            $p = $page;
            $s = $size;
            return $st;
        });

        $st->method('rows')->willReturnCallback(function () use (&$p, &$s) {
            return array_slice($this->getRowSet(), $p * $s, $s);
        });

        return $st;
    }

    private function getSelectStatementMock(): SelectStatement
    {
        $executor = $this->getExecutorMock();
        $executor->method('rows')->willReturn($this->getRowSet());
        return new SelectStatement($executor);
    }

    private function getRowSet(): array
    {
        return [
            ['c1' => 'v1', 'c2' => 'v2', 'c3' => 'a'],
            ['c1' => 'v3', 'c2' => 'v4', 'c3' => 'b'],
            ['c1' => 'v5', 'c2' => 'v6', 'c3' => 'b'],
         ];
    }

    /**
     * @psalm-return MockObject&StatementExecutor
     */
    private function getExecutorMock()
    {
        return $this->getMockBuilder(StatementExecutor::class)->getMock();
    }

    //endregion

    //region Copy & Clean

    /**
     * @test
     */
    public function copy(): void
    {
        $executor = $this->getMockBuilder(StatementExecutor::class)->getMock();

        $st = (new SelectStatement($executor))
            ->with('(SELECT * FROM t1)', 'tb')
            ->select('c1, c2')
            ->from('tb1', 't')
            ->innerJoin('tb', 'tb.id = t.id')
            ->where('c1', '>', 0)
            ->having('c2', '<', 1)
            ->orderBy('c3')
            ->groupBy('c4', 'DESC')
            ->limit(10)
            ->offset(5);

        $copy = $st->copy();

        self::assertSame($executor, $copy->getStatementExecutor());
        self::assertSame(
            'WITH tb AS (SELECT * FROM t1) ' .
            'SELECT c1, c2 FROM tb1 t INNER JOIN tb ON tb.id = t.id WHERE c1 > :p1 ' .
            'GROUP BY c4 DESC HAVING c2 < :p2 ORDER BY c3 LIMIT 10 OFFSET 5',
            $copy->toSql()
        );
        self::assertSame(
            [
                'p1' => 0,
                'p2' => 1,
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

        $st = (new SelectStatement($executor))
            ->with('(SELECT * FROM t1)', 'tb')
            ->select('c1, c2')
            ->from('tb1', 't')
            ->innerJoin('tb', 'tb.id = t.id')
            ->where('c1', '>', 0)
            ->having('c2', '<', 1)
            ->orderBy('c3')
            ->groupBy('c4', 'DESC')
            ->limit(10)
            ->offset(5);

        $st->clean();

        self::assertSame($executor, $st->getStatementExecutor());
        self::assertSame('SELECT *', $st->toSql());
        self::assertEmpty($st->getParams());
    }

    //endregion
}
