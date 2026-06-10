<?php
$config = require __DIR__ . '/../common/config/main-local.php';
$dbConfig = $config['components']['db'];
try {
    $pdo = new PDO($dbConfig['dsn'], $dbConfig['username'], $dbConfig['password']);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    $stmt = $pdo->query("SHOW CREATE TABLE `sakip_unit_kerja`");
    echo "=== Local sakip_unit_kerja ===\n" . $stmt->fetch(PDO::FETCH_ASSOC)['Create Table'] . "\n\n";

    $stmt = $pdo->query("SHOW CREATE TABLE `user`");
    echo "=== Local user ===\n" . $stmt->fetch(PDO::FETCH_ASSOC)['Create Table'] . "\n\n";
} catch (Exception $e) {
    echo "Local DB Error: " . $e->getMessage() . "\n\n";
}

$file = __DIR__ . '/../esakipprod.sql';
if (file_exists($file)) {
    $handle = fopen($file, 'r');
    $inUserTable = false;
    $linesCount = 0;
    echo "=== Production user table DDL ===\n";
    while (($line = fgets($handle)) !== false) {
        if (preg_match('/CREATE TABLE\s+`user`\s*\(/i', $line)) {
            $inUserTable = true;
        }
        if ($inUserTable) {
            echo $line;
            $linesCount++;
            if ($linesCount > 50 || (strpos($line, ';') !== false && (strpos($line, 'ENGINE') !== false || strpos($line, ')') === 0))) {
                break;
            }
        }
    }
    fclose($handle);
}
