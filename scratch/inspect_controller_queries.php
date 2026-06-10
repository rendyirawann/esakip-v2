<?php
$files = [
    __DIR__ . '/../frontend/controllers/SakipCascadingprogramController.php',
    __DIR__ . '/../frontend/controllers/SakipKebijakanController.php',
    __DIR__ . '/../frontend/controllers/SakipRenstratujuanController.php',
    __DIR__ . '/../frontend/controllers/SakipStrategiController.php',
    __DIR__ . '/../frontend/controllers/SakipTujuanrenstraController.php',
    __DIR__ . '/../frontend/controllers/SakipVisiController.php'
];

foreach ($files as $file) {
    if (!file_exists($file)) continue;
    $content = file_get_contents($file);
    $lines = explode("\n", $content);
    echo "Inspecting file: " . basename($file) . "\n";
    foreach ($lines as $index => $line) {
        if (strpos($line, 'sakip_sasaranrenstra') !== false && strpos($line, 'refperiode_id') !== false) {
            echo "  Line " . ($index + 1) . ": " . trim($line) . "\n";
        }
    }
}
