<?php
$file = __DIR__ . '/../esakipprod.sql';
if (!file_exists($file)) {
    die("File not found\n");
}

$handle = fopen($file, 'r');
$insertCount = 0;
while (($line = fgets($handle)) !== false) {
    if (stripos($line, 'INSERT INTO `sakip_evaluasi_renja`') !== false || stripos($line, 'INSERT INTO sakip_evaluasi_renja') !== false) {
        $insertCount++;
    }
}
fclose($handle);

echo "Total INSERT statements for sakip_evaluasi_renja in production dump: $insertCount\n";
