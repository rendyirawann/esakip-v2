<?php
$config = require __DIR__ . '/../common/config/main-local.php';
$dbConfig = $config['components']['db'];
try {
    $pdo = new PDO($dbConfig['dsn'], $dbConfig['username'], $dbConfig['password']);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $stmt = $pdo->query("SHOW TABLES");
    $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
    sort($tables);
    echo "Total tables in local DB: " . count($tables) . "\n";
    foreach ($tables as $t) {
        echo "- $t\n";
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
