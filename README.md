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

//Количество постов, по одинакому времени создания
$result = $db->table('posts')->select(['created_at, count(posts.id) as count_post'])->groupBy('created_at');
```
***

**Джоины**

Вывести посты и его автора:

```php
 $users = $db->table('posts')
  ->join('users', 'posts.user_id', 'users.id')->orderBy('posts.user_id')->get();
  ```
  Количество постов у всех юзеров:
  ```php
  $post = $db->table('users')
  ->leftJoin('posts', 'posts.user_id', 'users.id')->select(['users.name, count(posts.id) as count_post'])
  ->groupBy('users.name');
   ```
   Количество постов у определённого юзера:
   
   ```php
   $post = $db->table('users')->select(['users.name, count(posts.id) as count_post'])
  ->leftJoin('posts', 'posts.user_id', 'users.id')->where('users.id', '=', 1)->groupBy('users.name');
  ```
  ***

**INSERT**

Массовая вставка:
```php
$newUser = $db->table('users')->insert([
    ['name' => 'Иван', 'password' => '1133311111661'],
    ['name' => 'Егор','password' => 'fgtbnn'],
  ]);
 ```
 
```php
$newPost = $db->table('posts')->insert([
    [
        'title' => 'Новая запись',
        'description' => 'текст текст текст текст',
        'user_id' => 2
    ],
    [
        'title' => 'Про то и сё',
        'description' => 'Очень интересно прочитать',
        'user_id' => 2
    ],
    [
        'title' => 'Без названия',
        'description' => 'Как-то раз, вместо школы, я пошёл гулять.',
        'user_id' => 1
    ],
    [
        'title' => 'Ёлки в лесу',
        'description' => 'текст текст текст',
        'user_id' => 2
    ],
    [
        'title' => 'Изучение языков',
        'description' => 'бла бла бла бла бла',
        'user_id' => 2
    ]
]);
```

Вставка одного массива:

```php
$newUser = $db->table('users')->insert(
    ['name' => 'Настя','password' => 'qqq']
);
```

**UPDATE**

```php
$updateUser = $db->table('users')
    ->where('id', '=', 1)
    ->update([
        'password' => 'zxcvqwer'
]);
```

**DELETE**

```php
$deleteUser = $db->table('users')->where('id', '=', '3')->delete();
```
  
  


