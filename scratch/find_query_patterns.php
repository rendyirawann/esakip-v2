<?php
$file = __DIR__ . '/../frontend/controllers/LaporanController.php';
$content = file_get_contents($file);
$lines = explode("\n", $content);

$models = [
    'SakipSasaranrenstra',
    'SakipSasaranrenstraP',
    'SakipTujuanrenstra',
    'SakipTujuanrenstraP',
    'SakipStrategi',
    'SakipKebijakan',
    'SakipVisi',
    'SakipVisiP',
    'SakipMisi',
    'SakipMisiP',
    'SakipTujuan',
    'SakipTujuanP',
    'SakipSasaran',
    'SakipSasaranP'
];

echo "Query patterns found in LaporanController.php:\n";
foreach ($lines as $index => $line) {
    foreach ($models as $m) {
        if (strpos($line, $m) !== false && strpos($line, 'refperiode_id') !== false) {
            echo ($index + 1) . " (Model: $m): " . trim($line) . "\n";
        }
    }
}
