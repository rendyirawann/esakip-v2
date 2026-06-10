<?php
$config = require __DIR__ . '/../common/config/main-local.php';
$dbConfig = $config['components']['db'];
try {
    $pdo = new PDO($dbConfig['dsn'], $dbConfig['username'], $dbConfig['password']);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    $stmt = $pdo->query("SHOW CREATE TABLE `sakip_indikatorsasaranrenstra_p_triwulan`");
    echo "=== Local sakip_indikatorsasaranrenstra_p_triwulan ===\n" . $stmt->fetch(PDO::FETCH_ASSOC)['Create Table'] . "\n\n";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
