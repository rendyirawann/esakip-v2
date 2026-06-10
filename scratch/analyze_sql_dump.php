<?php

$file = __DIR__ . '/../esakipprod.sql';
if (!file_exists($file)) {
    die("Dump file not found!\n");
}

$handle = fopen($file, 'r');
$tables = [];
$indexes = [];

while (($line = fgets($handle)) !== false) {
    // Look for CREATE TABLE `tableName`
    if (preg_match('/CREATE TABLE\s+`([^`]+)`/i', $line, $matches)) {
        $tables[] = $matches[1];
    }
}
fclose($handle);

echo "Total tables found: " . count($tables) . "\n";
echo "List of v2_ tables:\n";
$v2Tables = array_filter($tables, function($t) { return strpos($t, 'v2_') === 0; });
foreach ($v2Tables as $t) {
    echo "- $t\n";
}

echo "\nList of other tables (first 20):\n";
$otherTables = array_filter($tables, function($t) { return strpos($t, 'v2_') !== 0; });
$i = 0;
foreach ($otherTables as $t) {
    if ($i++ >= 20) break;
    echo "- $t\n";
}
