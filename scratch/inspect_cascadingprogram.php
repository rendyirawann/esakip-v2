<?php
$config = require __DIR__ . '/../common/config/main-local.php';
$dbConfig = $config['components']['db'];
try {
    $pdo = new PDO($dbConfig['dsn'], $dbConfig['username'], $dbConfig['password']);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    $stmt = $pdo->query("SHOW CREATE TABLE `sakip_cascadingprogram`");
    echo "=== Local sakip_cascadingprogram ===\n" . $stmt->fetch(PDO::FETCH_ASSOC)['Create Table'] . "\n\n";
} catch (Exception $e) {
    echo "Local DB Error: " . $e->getMessage() . "\n\n";
}

$file = __DIR__ . '/../esakipprod.sql';
if (file_exists($file)) {
    $handle = fopen($file, 'r');
    $inTable = false;
    $ddl = "";
    $linesCount = 0;
    echo "=== Production sakip_cascadingprogram DDL ===\n";
    while (($line = fgets($handle)) !== false) {
        if (preg_match('/CREATE TABLE\s+`sakip_cascadingprogram`/i', $line)) {
            $inTable = true;
        }
        if ($inTable) {
            echo $line;
            $linesCount++;
            if ($linesCount > 40 || (strpos($line, ';') !== false && (strpos($line, 'ENGINE') !== false || strpos($line, ')') === 0))) {
                break;
            }
        }
    }
    fclose($handle);
}
