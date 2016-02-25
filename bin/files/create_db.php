<?php

use josegonzalez\Dotenv\Loader as Dotenv;

require dirname(__DIR__) . '/vendor/autoload.php';

Dotenv::load([
    'filepath' => dirname(__DIR__) . '/.env',
    'expect' => ['DB_DSN', 'DB_USER'],
    'toEnv' => true
]);

preg_match("/(.*?):(.*)/", $_ENV['DB_DSN'], $parts);
$type = $parts[1];
preg_match("/host=(\w+)/",  $_ENV['DB_DSN'], $parts);
$host = $parts[1];
preg_match("/dbname=(\w+)/",  $_ENV['DB_DSN'], $parts);
$dbName = $parts[1];
$dsn = sprintf('%s:host=%s', $type, $host);

try {
    $pdo = new \PDO($dsn, $_ENV['DB_USER'], $_ENV['DB_PASS']);
    $pdo->exec("CREATE DATABASE IF NOT EXISTS {$dbName}");
    $pdo->exec("CREATE DATABASE IF NOT EXISTS {$dbName}_test");
    error_log("Database [{$dbName}] and [{$dbName}_test] are created.");
} catch (PDOException $e) {
    echo $e->getMessage() . PHP_EOL;
    exit(1);
}
