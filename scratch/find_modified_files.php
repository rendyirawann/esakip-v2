<?php
$dirs = ['backend', 'common', 'frontend', 'console'];
$recentFiles = [];
$threshold = strtotime('2026-06-01 00:00:00');

function scanDirRecursive($dir, $threshold, &$recentFiles) {
    if (!is_dir($dir)) return;
    $files = scandir($dir);
    foreach ($files as $file) {
        if ($file === '.' || $file === '..') continue;
        $path = $dir . '/' . $file;
        if (is_dir($path)) {
            if ($file === 'runtime' || $file === 'assets') continue;
            scanDirRecursive($path, $threshold, $recentFiles);
        } else {
            $mtime = filemtime($path);
            if ($mtime >= $threshold) {
                if (strpos($path, 'main-local.php') !== false) continue;
                $recentFiles[] = [
                    'path' => $path,
                    'mtime' => date('Y-m-d H:i:s', $mtime),
                ];
            }
        }
    }
}

foreach ($dirs as $d) {
    scanDirRecursive(__DIR__ . '/../' . $d, $threshold, $recentFiles);
}

$categorized = [
    'backend' => [],
    'common' => [],
    'frontend' => [],
    'console' => [],
];

foreach ($recentFiles as $rf) {
    $relativePath = str_replace(__DIR__ . '/../', '', $rf['path']);
    $parts = explode('/', $relativePath);
    $root = $parts[0];
    if (isset($categorized[$root])) {
        $categorized[$root][] = $relativePath;
    }
}

foreach ($categorized as $key => $files) {
    echo "=== FOLDER: $key ===\n";
    echo "Total files: " . count($files) . "\n";
    
    // Group files by their subfolder to make it easy to copy
    $grouped = [];
    foreach ($files as $f) {
        $parts = explode('/', $f);
        array_shift($parts); // Remove root folder
        $filename = array_pop($parts);
        $subfolder = implode('/', $parts);
        if (empty($subfolder)) {
            $subfolder = '(root)';
        }
        $grouped[$subfolder][] = $filename;
    }
    
    foreach ($grouped as $sub => $items) {
        echo "  Subfolder: $sub/\n";
        foreach ($items as $item) {
            echo "    - $item\n";
        }
    }
    echo "\n";
}
