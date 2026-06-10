<?php

$dir = __DIR__ . '/../frontend/models';
$files = glob($dir . '/*.php');

$relations = [];

foreach ($files as $file) {
    $content = file_get_contents($file);
    $className = basename($file, '.php');
    
    // Skip form models and search models (we only want ActiveRecord models)
    if (strpos($className, 'Form') !== false || strpos($className, 'Search') !== false) {
        continue;
    }
    
    // Find all public function get[A-Z]...
    preg_match_all('/public\s+function\s+(get([a-zA-Z0-9_]+))\s*\(/', $content, $matches);
    
    if (!empty($matches[2])) {
        foreach ($matches[2] as $rel) {
            $relations[$className][] = lcfirst($rel);
        }
    }
}

echo json_encode($relations, JSON_PRETTY_PRINT);
