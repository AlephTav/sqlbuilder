<?php

declare(strict_types=1);

namespace Clickhouse;

use AlephTools\SqlBuilder\Clickhouse\InsertStatement;
use AlephTools\SqlBuilder\Clickhouse\SelectStatement;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
class InsertStatementTest extends TestCase
{
    /**
     * @test
     */
    public function insertWithSubSelect(): void
    {
        $st = new InsertStatement();
        $st->with('now() AS current_time')
            ->with('1000 AS price_threshold')
            ->with(
                'high_value_orders',
                (new SelectStatement())
                    ->select('groupArray(order_id)')
                    ->from('orders')
                    ->where('amount > price_threshold')
            )
            ->into('premium_users')
            ->columns(['id', 'name', 'total_spent', 'country', 'created_at', 'order_ids'])
            ->select(
                (new SelectStatement())
                    ->select([
                        'u.id',
                        'u.name',
                        'SUM(o.amount) AS total_spent',
                        'u.country',
                        'current_time AS created_at',
                        'high_value_orders AS order_ids',
                    ])
                    ->from('users AS u')
                    ->join('orders AS o ON u.id = o.user_id')
                    ->preWhere('u.is_active = 1')
                    ->exceptQuery(
                        (new SelectStatement())
                            ->select('id')
                            ->from('users')
                            ->where('registration_date > current_time - INTERVAL 30 DAY')
                    )
                    ->sample(0.1, 0.01)
                    ->groupBy('u.id, u.name, u.country')
                    ->having('total_spent > price_threshold')
                    ->window('win', 'PARTITION BY u.country ORDER BY SUM(o.amount) DESC')
                    ->qualify('RANK() OVER win <= 5')
                    ->orderBy('total_spent', 'DESC')
                    ->limit(50)
                    ->offset(10)
            );

        self::assertSame(
            "WITH now() AS current_time, 1000 AS price_threshold, (SELECT groupArray(order_id) FROM orders WHERE amount > price_threshold) AS high_value_orders " .
            "INSERT INTO premium_users " .
            "(id, name, total_spent, country, created_at, order_ids) " .
            "SELECT u.id, u.name, SUM(o.amount) AS total_spent, u.country, current_time AS created_at, high_value_orders AS order_ids " .
            "FROM users AS u " .
            "JOIN orders AS o ON u.id = o.user_id " .
            "PREWHERE u.is_active = 1 " .
            "SAMPLE 0.1 OFFSET 0.01 " .
            "EXCEPT (SELECT id FROM users WHERE registration_date > current_time - INTERVAL 30 DAY) " .
            "GROUP BY u.id, u.name, u.country " .
            "HAVING total_spent > price_threshold " .
            "WINDOW win AS (PARTITION BY u.country ORDER BY SUM(o.amount) DESC) " .
            "QUALIFY RANK() OVER win <= 5 " .
            "ORDER BY total_spent DESC " .
            "LIMIT 50 OFFSET 10",
            $st->toSql()
        );
        self::assertEmpty($st->getParams());
    }
}
