<?php
$config = require __DIR__ . '/../common/config/main-local.php';
$dbConfig = $config['components']['db'];
try {
    $pdo = new PDO($dbConfig['dsn'], $dbConfig['username'], $dbConfig['password']);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    $stmt = $pdo->query("SELECT COUNT(*) FROM `sakip_unit_kerja`");
    $count = $stmt->fetchColumn();
    echo "Total rows in sakip_unit_kerja: $count\n";
    if ($count > 0) {
        $stmt = $pdo->query("SELECT * FROM `sakip_unit_kerja` LIMIT 5");
        print_r($stmt->fetchAll(PDO::FETCH_ASSOC));
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
