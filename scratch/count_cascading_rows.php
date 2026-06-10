<?php
$config = require __DIR__ . '/../common/config/main-local.php';
$dbConfig = $config['components']['db'];
try {
    $pdo = new PDO($dbConfig['dsn'], $dbConfig['username'], $dbConfig['password']);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    $stmt = $pdo->query("SELECT COUNT(*) FROM `sakip_cascadingprogram`");
    echo "Local sakip_cascadingprogram count: " . $stmt->fetchColumn() . "\n";
} catch (Exception $e) {
    echo "Local Error: " . $e->getMessage() . "\n";
}

$file = __DIR__ . '/../esakipprod.sql';
if (file_exists($file)) {
    $handle = fopen($file, 'r');
    $count = 0;
    while (($line = fgets($handle)) !== false) {
        if (stripos($line, 'INSERT INTO `sakip_cascadingprogram`') !== false || stripos($line, 'INSERT INTO sakip_cascadingprogram') !== false) {
            $count++;
        }
    }
    fclose($handle);
    echo "Production sakip_cascadingprogram insert count: $count\n";
}
