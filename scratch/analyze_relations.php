<?php
// Script to scan all view files and extract relation property accesses ($model->relation->attribute)
$viewDir = __DIR__ . '/../backend/views';
$searchModels = [];

function scan_dir($dir) {
    $files = [];
    foreach (scandir($dir) as $item) {
        if ($item == '.' || $item == '..') continue;
        $path = $dir . '/' . $item;
        if (is_dir($path)) {
            $files = array_merge($files, scan_dir($path));
        } else if (is_file($path) && pathinfo($path, PATHINFO_EXTENSION) == 'php') {
            $files[] = $path;
        }
    }
    return $files;
}

$allFiles = scan_dir($viewDir);
$accesses = [];

foreach ($allFiles as $file) {
    $content = file_get_contents($file);
    // Matches like $model->relation->property
    if (preg_match_all('/\$model->([a-zA-Z0-9_]+)->[a-zA-Z0-9_]+/', $content, $matches)) {
        $relPath = str_replace(realpath($viewDir), '', realpath($file));
        $parts = explode(DIRECTORY_SEPARATOR, ltrim($relPath, DIRECTORY_SEPARATOR));
        $folder = $parts[0] ?? 'unknown';
        foreach ($matches[1] as $rel) {
            $accesses[$folder][$rel] = true;
        }
    }
    // Also check loop variables if any, e.g. foreach ($dataProvider->models as $model) or similar
    // But $model->relation->... is the most standard
}

echo "=== Detected Relation Accesses per View Folder ===\n";
foreach ($accesses as $folder => $rels) {
    echo "Folder: $folder\n";
    foreach (array_keys($rels) as $rel) {
        echo "  - Relation: $rel\n";
    }
    echo "\n";
}
