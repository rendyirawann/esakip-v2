<?php
$file = __DIR__ . '/../frontend/controllers/LaporanController.php';
$content = file_get_contents($file);
$lines = explode("\n", $content);
echo "Occurrences of refperiode_id in LaporanController.php:\n";
foreach ($lines as $index => $line) {
    if (strpos($line, 'refperiode_id') !== false) {
        echo ($index + 1) . ": " . trim($line) . "\n";
    }
}
