<?php
$config = require __DIR__ . '/../common/config/main-local.php';
$dbConfig = $config['components']['db'];
try {
    $pdo = new PDO($dbConfig['dsn'], $dbConfig['username'], $dbConfig['password']);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $stmt = $pdo->query("SELECT version, apply_time FROM migration ORDER BY apply_time ASC");
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo "Migrations in database:\n";
    foreach ($rows as $row) {
        echo "- " . $row['version'] . " (applied at " . date('Y-m-d H:i:s', $row['apply_time']) . ")\n";
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
