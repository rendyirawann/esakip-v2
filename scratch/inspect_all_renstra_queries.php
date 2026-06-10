<?php
$file = __DIR__ . '/../frontend/controllers/LaporanController.php';
$content = file_get_contents($file);
$lines = explode("\n", $content);

$models = [
    'SakipSasaranrenstra',
    'SakipStrategi',
    'SakipKebijakan'
];

foreach ($lines as $index => $line) {
    foreach ($models as $m) {
        if (strpos($line, $m . '::find()') !== false) {
            echo "Line " . ($index + 1) . ":\n";
            // Print current line and next 5 lines
            for ($i = 0; $i < 6; $i++) {
                if (isset($lines[$index + $i])) {
                    echo "  " . ($index + 1 + $i) . ": " . $lines[$index + $i] . "\n";
                }
            }
            echo "\n";
        }
    }
}
