<?php
$file = __DIR__ . '/../esakipprod.sql';
if (!file_exists($file)) {
    die("File not found\n");
}

$handle = fopen($file, 'r');
$tables = [];
while (($line = fgets($handle)) !== false) {
    if (preg_match('/CREATE TABLE\s+`([^`]+)`/i', $line, $matches)) {
        $tables[] = $matches[1];
    }
}
fclose($handle);

sort($tables);
echo "Total tables in production dump: " . count($tables) . "\n";
echo "Tables:\n";
foreach ($tables as $t) {
    echo "- $t\n";
}
