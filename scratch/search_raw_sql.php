<?php
function searchDir($dir, $pattern) {
    $it = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir));
    foreach ($it as $file) {
        if ($file->isDir() || $file->getExtension() !== 'php') continue;
        $content = file_get_contents($file->getPathname());
        if (strpos($content, $pattern) !== false && strpos($content, 'refperiode_id') !== false) {
            echo "Found raw SQL references: " . $file->getPathname() . "\n";
        }
    }
}

$tables = [
    'sakip_sasaranrenstra',
    'sakip_strategi',
    'sakip_kebijakan'
];

foreach ($tables as $t) {
    echo "Searching for raw SQL on $t:\n";
    searchDir(__DIR__ . '/../frontend', $t);
}
