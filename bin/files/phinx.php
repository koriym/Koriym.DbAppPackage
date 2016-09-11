<?php

use josegonzalez\Dotenv\Loader as Dotenv;

Dotenv::load([
    'filepath' => dirname(dirname(__DIR__)) . '/.env',
    'toEnv' => true
]);

preg_match("/(.*?):(.*)/", $_ENV['DB_DSN'], $parts);
$type = $parts[1];
preg_match("/host=(\w+)/",  $_ENV['DB_DSN'], $parts);
$host = $parts[1];
preg_match("/dbname=(\w+)/",  $_ENV['DB_DSN'], $parts);
$dbName = $parts[1];

$default = [
    "adapter" => $type,
    "host" => $host,
    "name" => $dbName,
    "user" => $_ENV['DB_USER'],
    "pass" => $_ENV['DB_PASS']
];

return [
    "paths" => [
        "migrations" => __DIR__
    ],
    "environments" => [
        "default_migration_table" => "phinxlog",
        "default_database" => "default",
        "default" => $default,
        "test" => [
            "name" => $dbName . '_test'
        ] + $default
    ]
];
