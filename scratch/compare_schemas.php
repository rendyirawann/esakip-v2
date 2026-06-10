<?php
// Set up comparison
$config = require __DIR__ . '/../common/config/main-local.php';
$dbConfig = $config['components']['db'];
$localPdo = new PDO($dbConfig['dsn'], $dbConfig['username'], $dbConfig['password']);
$localPdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Read production SQL dump and build the table/column dictionary
$prodFile = __DIR__ . '/../esakipprod.sql';
if (!file_exists($prodFile)) {
    die("Production SQL dump not found!\n");
}

$prodTables = [];
$currentTable = null;

$handle = fopen($prodFile, 'r');
while (($line = fgets($handle)) !== false) {
    if (preg_match('/CREATE TABLE\s+`([^`]+)`/i', $line, $matches)) {
        $currentTable = $matches[1];
        $prodTables[$currentTable] = [];
    } elseif ($currentTable && preg_match('/^\s*`([^`]+)`\s+([a-z0-9_()]+)/i', $line, $matches)) {
        // Matches `columnName` type
        $colName = $matches[1];
        $colType = strtolower($matches[2]);
        $prodTables[$currentTable][$colName] = $colType;
    }
}
fclose($handle);

echo "Loaded " . count($prodTables) . " tables from production dump.\n\n";

// Get list of local tables
$stmt = $localPdo->query("SHOW TABLES");
$localTables = $stmt->fetchAll(PDO::FETCH_COLUMN);

// Compare tables
$missingInProd = array_diff($localTables, array_keys($prodTables));
$extraInProd = array_diff(array_keys($prodTables), $localTables);

echo "=== Tables present in Local but missing in Production (" . count($missingInProd) . ") ===\n";
foreach ($missingInProd as $t) {
    echo "- $t\n";
}
echo "\n";

echo "=== Tables present in Production but missing in Local (" . count($extraInProd) . ") ===\n";
foreach ($extraInProd as $t) {
    echo "- $t\n";
}
echo "\n";

echo "=== Column Comparisons for Shared Tables ===\n";
$sharedTables = array_intersect($localTables, array_keys($prodTables));
foreach ($sharedTables as $table) {
    // Get local columns
    $stmt = $localPdo->query("SHOW COLUMNS FROM `$table`");
    $localCols = [];
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        // Clean data type (remove unsigned, lowercase, etc.)
        $type = preg_replace('/\s+.*/', '', $row['Type']);
        $localCols[$row['Field']] = strtolower($type);
    }
    
    $prodCols = $prodTables[$table];
    
    // Find column differences
    $missingInProdCols = array_diff(array_keys($localCols), array_keys($prodCols));
    $extraInProdCols = array_diff(array_keys($prodCols), array_keys($localCols));
    
    if (!empty($missingInProdCols) || !empty($extraInProdCols)) {
        echo "Table: $table\n";
        if (!empty($missingInProdCols)) {
            echo "  * Columns present in Local but MISSING in Production:\n";
            foreach ($missingInProdCols as $col) {
                echo "    - $col ({$localCols[$col]})\n";
            }
        }
        if (!empty($extraInProdCols)) {
            echo "  * Columns present in Production but MISSING in Local:\n";
            foreach ($extraInProdCols as $col) {
                echo "    - $col ({$prodCols[$col]})\n";
            }
        }
        echo "\n";
    }
}
