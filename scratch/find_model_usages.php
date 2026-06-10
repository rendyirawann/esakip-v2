<?php
function searchDir($dir, $pattern) {
    $it = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir));
    foreach ($it as $file) {
        if ($file->isDir() || $file->getExtension() !== 'php') continue;
        $content = file_get_contents($file->getPathname());
        if (strpos($content, $pattern) !== false) {
            echo "Found pattern '$pattern' in: " . $file->getPathname() . "\n";
        }
    }
}

echo "Searching backend folder for SakipSasaranrenstra:\n";
searchDir(__DIR__ . '/../backend', 'SakipSasaranrenstra');
echo "\nSearching backend folder for SakipStrategi:\n";
searchDir(__DIR__ . '/../backend', 'SakipStrategi');
echo "\nSearching backend folder for SakipKebijakan:\n";
searchDir(__DIR__ . '/../backend', 'SakipKebijakan');
