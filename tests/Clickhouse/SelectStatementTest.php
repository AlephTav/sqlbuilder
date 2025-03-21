<?php

declare(strict_types=1);

namespace Clickhouse;

use AlephTools\SqlBuilder\Clickhouse\SelectStatement;
use AlephTools\SqlBuilder\Sql\Expression\AbstractExpression;
use AlephTools\SqlBuilder\StatementExecutor;
use PHPUnit\Framework\TestCase;

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

    //region APPLY, EXCEPT, REPLACE

    /**
     * @test
     */
    public function selectWithApply(): void
    {
        $st = new SelectStatement();
        $st->apply('sum')
            ->from('table');

        self::assertSame('SELECT * APPLY(sum) FROM table', $st->toString());
        self::assertEmpty($st->getParams());
    }

    /**
     * @test
     */
    public function selectWithMultiApply(): void
    {
        $st = new SelectStatement();
        $st->apply('sum');
        $st->apply('toString');
        $st->apply('length');

        self::assertSame('SELECT * APPLY(sum) APPLY(toString) APPLY(length)', $st->toString());
        self::assertEmpty($st->getParams());
    }

    /**
     * @test
     */
    public function selectColumnsWithMultiApply(): void
    {
        $st = new SelectStatement();
        $st->select("COLUMNS('[jk]')")
            ->apply('toString')
            ->apply('length')
            ->apply('max')
            ->from('columns_transformers');

        self::assertSame(
            "SELECT COLUMNS('[jk]') APPLY(toString) APPLY(length) APPLY(max) FROM columns_transformers",
            $st->toString()
        );
        self::assertEmpty($st->getParams());
    }

    /**
     * @test
     */
    public function selectWithExpect(): void
    {
        $st = new SelectStatement();
        $st->except('c1')
            ->from('table');

        self::assertSame('SELECT * EXCEPT (c1) FROM table', $st->toString());
        self::assertEmpty($st->getParams());
    }

    /**
     * @test
     */
    public function selectWithMultiExpect(): void
    {
        $st = new SelectStatement();
        $st->except(['c1', 'c2', 'c3']);

        self::assertSame('SELECT * EXCEPT (c1, c2, c3)', $st->toString());
        self::assertEmpty($st->getParams());
    }

    /**
     * @test
     */
    public function selectWithSubQueryExcept(): void
    {
        $st = new SelectStatement();
        $st->select('crypto_name')
            ->from('holdings')
            ->exceptQuery(
                (new SelectStatement())->select('crypto_name')
                    ->from('crypto_prices')
                    ->where('price', '<', 10)
            );

        self::assertSame(
            'SELECT crypto_name FROM holdings EXCEPT (SELECT crypto_name FROM crypto_prices WHERE price < :p1)',
            $st->toString()
        );
        self::assertSame(['p1' => 10], $st->getParams());
    }

    /**
     * @test
     */
    public function selectWithSubQueryExceptDistinct(): void
    {
        $st = new SelectStatement();
        $st->select('crypto_name')
            ->from('holdings')
            ->exceptQuery(
                (new SelectStatement())->select('crypto_name')
                    ->from('crypto_prices')
                    ->where('price', '<', 10),
                true
            );

        self::assertSame(
            'SELECT crypto_name FROM holdings EXCEPT DISTINCT (SELECT crypto_name FROM crypto_prices WHERE price < :p1)',
            $st->toString()
        );
        self::assertSame(['p1' => 10], $st->getParams());
    }

    /**
     * @test
     */
    public function selectWithReplace(): void
    {
        $st = new SelectStatement();
        $st->select('*')
            ->from('columns_transformers')
            ->replace('i + 1', 'i');

        self::assertSame('SELECT * REPLACE(i + 1 i) FROM columns_transformers', $st->toString());
        self::assertEmpty($st->getParams());
    }

    /**
     * @test
     */
    public function selectWithReplaceExpectApply(): void
    {
        $st = new SelectStatement();
        $st->select('*')
            ->replace('i + 1', 'i')
            ->apply('sum')
            ->except('j')
            ->from('columns_transformers');

        self::assertSame('SELECT * REPLACE(i + 1 i) APPLY(sum) EXCEPT (j) FROM columns_transformers', $st->toString());
        self::assertEmpty($st->getParams());
    }

    //endregion

    //region SETTINGS

    /**
     * @test
     */
    public function selectWithSettings(): void
    {
        $st = new SelectStatement();
        $st->select('*')
            ->from('some_table')
            ->settings([
                'optimize_read_in_order=1',
                'cast_keep_nullable=1',
            ]);

        self::assertSame('SELECT * FROM some_table SETTINGS (:p1, :p2)', $st->toString());
        self::assertSame([
            'p1' => 'optimize_read_in_order=1',
            'p2' => 'cast_keep_nullable=1',
        ], $st->getParams());
    }

    //endregion

    //region INTERSECT

    /**
     * @test
     */
    public function selectWithIntersect(): void
    {
        $st = new SelectStatement();
        $st->select(['column1', 'column2'])
            ->from('table1')
            ->where('1=1')
            ->intersect(
                (new SelectStatement())
                    ->select(['column1', 'column2'])
                    ->from('table2')
                    ->where('2=2')
            );

        self::assertSame(
            'SELECT column1, column2 FROM table1 WHERE 1=1 INTERSECT SELECT column1, column2 FROM table2 WHERE 2=2',
            $st->toString()
        );
        self::assertEmpty($st->getParams());
    }

    /**
     * @test
     */
    public function selectWithIntersectDistinct(): void
    {
        $st = new SelectStatement();
        $st->select(['column1', 'column2'])
            ->from('table1')
            ->where('1=1')
            ->intersect(
                (new SelectStatement())
                    ->select(['column1', 'column2'])
                    ->from('table2')
                    ->where('2=2'),
                true
            );

        self::assertSame(
            'SELECT column1, column2 FROM table1 WHERE 1=1 INTERSECT DISTINCT SELECT column1, column2 FROM table2 WHERE 2=2',
            $st->toString()
        );
        self::assertEmpty($st->getParams());
    }

    //endregion

    // region FORMAT, INTO OUTFILE

    /**
     * @test
     */
    public function selectWithIntoOutfile(): void
    {
        $st = new SelectStatement();
        $st->select('*')
            ->from('my_table')
            ->intoOutfile('/var/lib/clickhouse/user_files/output.csv')
            ->format('CSV');

        self::assertSame(
            "SELECT * FROM my_table INTO OUTFILE '/var/lib/clickhouse/user_files/output.csv' FORMAT CSV",
            $st->toString()
        );
        self::assertEmpty($st->getParams());
    }

    /**
     * @test
     */
    public function selectWithFormat(): void
    {
        $st = new SelectStatement();
        $st->select('*')
            ->from('my_table')
            ->format('JSON');

        self::assertSame("SELECT * FROM my_table FORMAT JSON", $st->toString());
        self::assertEmpty($st->getParams());
    }

    //endregion

    //region PREWHERE, WHERE

    /**
     * @test
     */
    public function selectWithPreWhere(): void
    {
        $st = new SelectStatement();
        $st->select([
            'id',
            'name',
            'created_at',
        ])
            ->from('my_table')
            ->preWhere('created_at', '>=', '2024-01-01')
            ->where('name', 'like', 'AB%');
        self::assertSame(
            "SELECT id, name, created_at FROM my_table PREWHERE created_at >= :p1 WHERE name like :p2",
            $st->toString()
        );
        self::assertSame([
            'p1' => '2024-01-01',
            'p2' => 'AB%',
        ], $st->getParams());
    }

    /**
     * @test
     */
    public function selectWithMultiPreWhere(): void
    {
        $st = new SelectStatement();
        $st->select([
            'id',
            'name',
            'created_at',
        ])
            ->from('my_table')
            ->preWhere('created_at', '>=', '2024-01-01')
            ->preWhere('created_at', '<=', '2025-01-01')
            ->where('name', 'like', 'AB%');
        self::assertSame(
            "SELECT id, name, created_at FROM my_table PREWHERE created_at >= :p1 AND created_at <= :p2 WHERE name like :p3",
            $st->toString()
        );
        self::assertSame([
            'p1' => '2024-01-01',
            'p2' => '2025-01-01',
            'p3' => 'AB%',
        ], $st->getParams());
    }

    //endregion

    //region SAMPLE

    /**
     * @test
     */
    public function selectWithSample(): void
    {
        $st = new SelectStatement();
        $st->select('*')
            ->from('my_table')
            ->sample(0.05);

        self::assertSame("SELECT * FROM my_table SAMPLE 0.05", $st->toString());
        self::assertEmpty($st->getParams());

        $st = new SelectStatement();
        $st->select('*')
            ->from('my_table')
            ->sample(10000);

        self::assertSame("SELECT * FROM my_table SAMPLE 10000", $st->toString());
        self::assertEmpty($st->getParams());
    }

    /**
     * @test
     */
    public function selectWithSampleAndOffset(): void
    {
        $st = new SelectStatement();
        $st->select('*')
            ->from('my_table')
            ->sample(100, 100);

        self::assertSame("SELECT * FROM my_table SAMPLE 100 OFFSET 100", $st->toString());
        self::assertEmpty($st->getParams());

        $st = new SelectStatement();
        $st->select('*')
            ->from('my_table')
            ->sample(1 / 10, 1 / 100);

        self::assertSame("SELECT * FROM my_table SAMPLE 0.1 OFFSET 0.01", $st->toString());
        self::assertEmpty($st->getParams());
    }

    //endregion

    //region QUALIFY

    /**
     * @test
     */
    public function selectWithQualify(): void
    {
        $st = new SelectStatement();
        $st->select('user_id')
            ->select('order_id')
            ->select('order_date')
            ->select('ROW_NUMBER() OVER (PARTITION BY user_id ORDER BY order_date DESC)', 'AS rn')
            ->from('orders')
            ->qualify('rn = 1');

        self::assertSame(
            "SELECT user_id, order_id, order_date, ROW_NUMBER() OVER (PARTITION BY user_id ORDER BY order_date DESC) AS rn FROM orders QUALIFY rn = 1",
            $st->toString()
        );
        self::assertEmpty($st->getParams());

        $st = new SelectStatement();
        $st->select([
            'employee_id',
            'department',
            'salary',
            'rnk' => 'DENSE_RANK() OVER (PARTITION BY department ORDER BY salary DESC)',
        ])
            ->from('employees')
            ->qualify('rnk <= 5');

        self::assertSame(
            "SELECT employee_id, department, salary, DENSE_RANK() OVER (PARTITION BY department ORDER BY salary DESC) rnk FROM employees QUALIFY rnk <= 5",
            $st->toString()
        );
        self::assertEmpty($st->getParams());
    }
    //endregion

    /**
     * @test
     */
    public function selectWithAllClauses(): void
    {
        $st = new SelectStatement();
        $st->with('current_time', 'now()')
            ->with('1000 AS price_threshold')
            ->with('filtered_orders', (new SelectStatement())
                ->select('groupArray(order_id)')
                ->from('orders')
                ->where('orders.user_id = u.id'))
            ->select([
                'u.id',
                'u.name',
                'total_spent' => 'SUM(o.amount)',
                'order_count' => 'COUNT(*)',
                'rank_in_country' => 'RANK() OVER (PARTITION BY u.country ORDER BY SUM(o.amount) DESC)',
                'global_percent_rank' => 'PERCENT_RANK() OVER (ORDER BY SUM(o.amount) DESC)',
                'order_id',
            ])
            ->from('users', 'u')
            ->join('orders', 'o', 'u.id = o.user_id')
            ->preWhere('u.is_active = 1')
            ->where('u.status', 'NOT IN', ['banned', 'deleted'])
            ->sample(0.1, 0.01)
            ->exceptQuery(
                (new SelectStatement())->select('id')
                    ->from('users')
                    ->where('registration_date > current_time - INTERVAL 30 DAY')
            )
            ->groupBy(['u.id', 'u.name', 'u.country'])
            ->having('total_spent > price_threshold')
            ->window('win', 'PARTITION BY u.country ORDER BY SUM(o.amount) DESC')
            ->qualify('rank_in_country <= 5')
            ->orderBy('total_spent', 'DESC')
            ->limit(100)
            ->offset(10)
            ->arrayJoin('filtered_orders', 'order_id')
            ->intoOutfile('/var/lib/clickhouse/user_files/output.json')
            ->format('JSONEachRow');

        self::assertSame($st->toString(), $st->toSql());
        self::assertSame(
            "WITH now() AS current_time, 1000 AS price_threshold, (SELECT groupArray(order_id) FROM orders WHERE orders.user_id = u.id) AS filtered_orders " .
            "SELECT u.id, u.name, SUM(o.amount) total_spent, COUNT(*) order_count, RANK() OVER (PARTITION BY u.country ORDER BY SUM(o.amount) DESC) rank_in_country, PERCENT_RANK() OVER (ORDER BY SUM(o.amount) DESC) global_percent_rank, order_id " .
            "FROM users u " .
            "JOIN orders o ON u.id = o.user_id " .
            "PREWHERE u.is_active = 1 " .
            "WHERE u.status NOT IN (:p1, :p2) " .
            "SAMPLE 0.1 OFFSET 0.01 " .
            "EXCEPT (SELECT id FROM users WHERE registration_date > current_time - INTERVAL 30 DAY) " .
            "GROUP BY u.id, u.name, u.country " .
            "HAVING total_spent > price_threshold " .
            "WINDOW win AS (PARTITION BY u.country ORDER BY SUM(o.amount) DESC) " .
            "QUALIFY rank_in_country <= 5 " .
            "ORDER BY total_spent DESC " .
            "LIMIT 100 OFFSET 10 " .
            "ARRAY JOIN filtered_orders order_id " .
            "INTO OUTFILE '/var/lib/clickhouse/user_files/output.json' " .
            "FORMAT JSONEachRow",
            $st->toString()
        );
        self::assertSame([
            'p1' => 'banned',
            'p2' => 'deleted',
        ], $st->getParams());
    }

    /**
     * @test
     */
    public function copySelectStatement(): void
    {
        $executor = $this->getMockBuilder(StatementExecutor::class)->getMock();
        $st = new SelectStatement($executor);
        $st->with('current_time', 'now()')
            ->with('1000 AS price_threshold')
            ->with('filtered_orders', (new SelectStatement())
                ->select('groupArray(order_id)')
                ->from('orders')
                ->where('orders.user_id = u.id'))
            ->select([
                'u.id',
                'u.name',
                'total_spent' => 'SUM(o.amount)',
                'order_count' => 'COUNT(*)',
                'rank_in_country' => 'RANK() OVER (PARTITION BY u.country ORDER BY SUM(o.amount) DESC)',
                'global_percent_rank' => 'PERCENT_RANK() OVER (ORDER BY SUM(o.amount) DESC)',
                'order_id',
            ])
            ->from('users', 'u')
            ->join('orders', 'o', 'u.id = o.user_id')
            ->preWhere('u.is_active = 1')
            ->where('u.status', 'NOT IN', ['banned', 'deleted'])
            ->sample(0.1, 0.01)
            ->exceptQuery(
                (new SelectStatement())->select('id')
                    ->from('users')
                    ->where('registration_date > current_time - INTERVAL 30 DAY')
            )
            ->groupBy(['u.id', 'u.name', 'u.country'])
            ->having('total_spent > price_threshold')
            ->window('win', 'PARTITION BY u.country ORDER BY SUM(o.amount) DESC')
            ->qualify('rank_in_country <= 5')
            ->orderBy('total_spent', 'DESC')
            ->limit(100)
            ->offset(10)
            ->arrayJoin('filtered_orders', 'order_id')
            ->intoOutfile('/var/lib/clickhouse/user_files/output.json')
            ->format('JSONEachRow');

        $copy = $st->copy();
        self::assertSame($copy->getStatementExecutor(), $executor);
        self::assertSame($st->toSql(), $copy->toSql());
        self::assertSame($st->getParams(), $copy->getParams());
    }

    /**
     * @test
     */
    public function cleanSelectStatement(): void
    {
        $executor = $this->getMockBuilder(StatementExecutor::class)->getMock();
        $st = new SelectStatement($executor);
        $st->with('current_time', 'now()')
            ->with('1000 AS price_threshold')
            ->with('filtered_orders', (new SelectStatement())
                ->select('groupArray(order_id)')
                ->from('orders')
                ->where('orders.user_id = u.id'))
            ->select([
                'u.id',
                'u.name',
                'total_spent' => 'SUM(o.amount)',
                'order_count' => 'COUNT(*)',
                'rank_in_country' => 'RANK() OVER (PARTITION BY u.country ORDER BY SUM(o.amount) DESC)',
                'global_percent_rank' => 'PERCENT_RANK() OVER (ORDER BY SUM(o.amount) DESC)',
                'order_id',
            ])
            ->from('users', 'u')
            ->join('orders', 'o', 'u.id = o.user_id')
            ->preWhere('u.is_active = 1')
            ->where('u.status', 'NOT IN', ['banned', 'deleted'])
            ->sample(0.1, 0.01)
            ->exceptQuery(
                (new SelectStatement())->select('id')
                    ->from('users')
                    ->where('registration_date > current_time - INTERVAL 30 DAY')
            )
            ->groupBy(['u.id', 'u.name', 'u.country'])
            ->having('total_spent > price_threshold')
            ->window('win', 'PARTITION BY u.country ORDER BY SUM(o.amount) DESC')
            ->qualify('rank_in_country <= 5')
            ->orderBy('total_spent', 'DESC')
            ->limit(100)
            ->offset(10)
            ->arrayJoin('filtered_orders', 'order_id')
            ->intoOutfile('/var/lib/clickhouse/user_files/output.json')
            ->format('JSONEachRow');

        $st->clean();

        self::assertSame($executor, $st->getStatementExecutor());
        self::assertSame("SELECT *", $st->toSql());
        self::assertEmpty($st->getParams());
    }
}
