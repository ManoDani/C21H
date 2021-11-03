<<<<<<< HEAD
<?php

require __DIR__ . '/vendor/autoload.php';

$dotenv = new \Dotenv\Dotenv(__DIR__);
$dotenv->load();

return [
    "paths" => [
        "migrations" => __DIR__ . '/db/migrations',
        "seeds" => __DIR__ . '/db/seeds'
    ],
    "environments" => [
        "default_migration_table" => "migrations",
        "default_database" => "development",
        "development" => [
            "adapter" => "mysql",
            "name" => getenv('DB_NAME'),
            "user" => getenv('DB_USER'),
            "pass" => getenv('DB_PASS'),
            "host" => getenv('DB_HOST'),
            "charset" => getenv('DB_CHARSET'),
            "collation" => "utf8_general_ci",
        ],
    ],
];
=======
<?php

require __DIR__ . '/vendor/autoload.php';

$dotenv = new \Dotenv\Dotenv(__DIR__);
$dotenv->load();

return [
    "paths" => [
        "migrations" => __DIR__ . '/db/migrations',
        "seeds" => __DIR__ . '/db/seeds'
    ],
    "environments" => [
        "default_migration_table" => "migrations",
        "default_database" => "development",
        "development" => [
            "adapter" => "mysql",
            "name" => getenv('DB_NAME'),
            "user" => getenv('DB_USER'),
            "pass" => getenv('DB_PASS'),
            "host" => getenv('DB_HOST'),
            "charset" => getenv('DB_CHARSET'),
            "collation" => "utf8_general_ci",
        ],
    ],
];
>>>>>>> 9e6786124ee9095e51a202f022e0cd2f706854f3
