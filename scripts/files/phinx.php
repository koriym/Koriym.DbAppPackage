<?php

use josegonzalez\Dotenv\Loader as Dotenv;

Dotenv::load([
    'filepath' => dirname(dirname(__DIR__)) . '/.env',
    'toEnv' => true
]);

$pdo = new PDO($_ENV['DB_DSN'], $_ENV['DB_USER'], $_ENV['DB_PASS']);
preg_match("/dbname=(\w+)/i", $_ENV['DB_DSN'], $parts);
$name = $parts[1];

return [
    "paths" => [
        "migrations" => __DIR__
    ],
    "environments" => [
        "default_migration_table" => "phinxlog",
        "default_database" => "default",
        "default" => [
            "name" => $name,
            "connection" => $pdo
        ],
        "test" => [
            "name" => $name . '_test',
            "connection" => $pdo
        ]
    ]
];
