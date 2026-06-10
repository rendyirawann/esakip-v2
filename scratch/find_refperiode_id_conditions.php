<?php
$file = __DIR__ . '/../frontend/controllers/LaporanController.php';
$content = file_get_contents($file);
$lines = explode("\n", $content);

echo "Lines with refperiode_id assignments/conditions in LaporanController.php:\n";
foreach ($lines as $index => $line) {
    if (strpos($line, 'refperiode_id') !== false && (strpos($line, '=>') !== false || strpos($line, '=') !== false)) {
        // We only care about lines around database operations
        // Let's filter out simple assignments like $refperiode_id = ...
        if (strpos($line, '$refperiode_id =') === false && strpos($line, '$refperiode_id ===') === false && strpos($line, 'function') === false) {
            echo ($index + 1) . ": " . trim($line) . "\n";
        }
    }
}
