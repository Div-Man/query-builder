Тестовое задание - Разработать **QueryBuilder**.

**Выполнил:** Кахаров Дмитрий

#### Установка:

Версия PHP 8.1.

1. ```git clone git@github.com:Div-Man/query-builder.git```
2. Создать БД из дампа, в папке **DumpMySQL** и подключиться

Структура массива, для конфигурации соединения:

```php
$configMySQL = [
    'dbtype' => 'mysql',
    'host' => 'localhost',
    'port' => '3306',
    'username' => 'dimak',
    'password' => '123456',
    'dbname' => 'testbase',
];
```

Все запросы проверены в **MySQL** и в **PostgreSQL**
***
#### Использование:

```php
require('queryBuilder.php');

//Для MySQL
$configMySQL = [
    'dbtype' => 'mysql',
    'host' => 'localhost',
    'port' => '3306',
    'username' => 'dimak',
    'password' => '123456',
    'dbname' => 'testbase',
];

//Для PostgreSQL
$configPostgreSQL = [
    'dbtype' => 'pgsql',
    'host' => 'localhost',
    'port' => '5432',
    'username' => 'dimak',
    'password' => '123456',
    'dbname' => 'mybase',
];

$db = new QueryBuilder($configMySQL);
```

Варианты выборок:

```php
$result = $db->table('users')->orderBy('id')->get();

echo '<pre>';
  print_r($result);
echo '</pre>';
```

Вывод:

```php
Array
(
    [0] => Array
        (
            [id] => 1
            [name] => Иван
            [password] => zxcvqwer
        )

    [1] => Array
        (
            [id] => 2
            [name] => Егор
            [password] => fgtbnn
        )

)
```

Остальные:

```php
$result = $db->table('posts')->orderBy('created_at', 'DESC')->get();
$result = $db->table('posts')->find(2);
$result = $db->table('posts')->get();
$result = $db->table('posts')->where('id', '>', '5')->limit(5)->get();
$result = $db->table('posts')->limit(5)->get();
$result = $db->table('posts')->orderBy('created_at', 'DESC')->limit(5)->get();
$result = $db->table('posts')->select(['id', 'title'])->get();
$result = $db->table('posts')->select(['id', 'title'])->limit(3)->get();
$result = $db->table('posts')->select(['id', 'title'])->orderBy('created_at')->limit(3)->get();
$result = $db->table('posts')->select(['*'])->get();
```
***

Джоины

Вывести посты и его автора


