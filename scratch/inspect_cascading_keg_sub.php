<?php
$config = require __DIR__ . '/../common/config/main-local.php';
$dbConfig = $config['components']['db'];
try {
    $pdo = new PDO($dbConfig['dsn'], $dbConfig['username'], $dbConfig['password']);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    $stmt = $pdo->query("SHOW CREATE TABLE `sakip_cascadingkegiatan`");
    echo "=== Local sakip_cascadingkegiatan ===\n" . $stmt->fetch(PDO::FETCH_ASSOC)['Create Table'] . "\n\n";

    $stmt = $pdo->query("SHOW CREATE TABLE `sakip_cascadingsubkegiatan`");
    echo "=== Local sakip_cascadingsubkegiatan ===\n" . $stmt->fetch(PDO::FETCH_ASSOC)['Create Table'] . "\n\n";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
