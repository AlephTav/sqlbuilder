# SQL Builder

The library bases on two ideas:
- an SQL statement expressed with this library should be similar to the statement itself;
- flexible combination of statement components.

Thus, using this library you get simple but, at the same time, powerful tool to build SQL statements in flexible and
intuitive way.

```php

use AlephTools\SqlBuilder\PostgreSql\SelectStatement;
use AlephTools\SqlBuilder\Sql\Expression\ConditionalExpression;

$st = (new SelectStatement())
    ->select([
        'u.id',
        'u.name',
        'company' => 'c.name',
        'unreadMessageCount' => (new SelectStatement())
            ->select('COUNT(*)')
            ->from('user_messages', 'm')
            ->where('m.user_id = u.id')
            ->andWhere('m.read_at IS NULL')
    ])
    ->from('users u')
    ->innerJoin('companies c', 'c.id = u.company_id')
    ->where('u.deleted_at IS NULL')
    ->andWhere((new ConditionalExpression())
        ->where('u.roles', 'IN', ['ADMIN', 'RESELLER'])
        ->orWhere(
            (new SelectStatement())
                ->select('COUNT(*)')
                ->from('user_contacts uc')
                ->where('uc.user_id = u.id'),
            '>',
            5
        )
    );
    
// Outputs: 
// SELECT
//     u.id, u.name, c.name company,
//     (SELECT COUNT(*) FROM user_messages m WHERE m.user_id = u.id AND m.read_at IS NULL) unreadMessageCount
// FROM users u
// INNER JOIN companies c ON c.id = u.company_id
// WHERE
//     u.deleted_at IS NULL AND (
//         u.roles IN (:p1, :p2) OR 
//         (SELECT COUNT(*) FROM user_contacts uc WHERE uc.user_id = u.id) > :p3
//     )
echo $st->toSql();

// Outputs:
// ['p1' => 'ADMIN', 'p2' => 'RESELLER', 'p3' => 5]
print_r($st->getParams());

// Executes statement if StatementExecutor is defined, otherwise an exception is thrown
$rows = $st->rows();

```