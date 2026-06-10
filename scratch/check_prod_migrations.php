<?php
$file = __DIR__ . '/../esakipprod.sql';
if (!file_exists($file)) {
    die("File not found\n");
}

$handle = fopen($file, 'r');
while (($line = fgets($handle)) !== false) {
    if (stripos($line, 'INSERT INTO `migration`') !== false || stripos($line, 'INSERT INTO migration') !== false) {
        echo trim($line) . "\n";
    }
}
fclose($handle);
