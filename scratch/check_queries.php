<?php
$content = file_get_contents(__DIR__ . '/../console/migrations/m260603_120000_reconstruct_sakip_to_5yearly.php');
$lines = explode("\n", $content);
foreach ($lines as $index => $line) {
    if (strpos($line, 'Query()') !== false) {
        echo ($index + 1) . ": " . trim($line) . "\n";
    }
}
