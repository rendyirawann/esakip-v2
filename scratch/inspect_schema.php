<?php
$config = require __DIR__ . '/../common/config/main-local.php';
$dbConfig = $config['components']['db'];
try {
    $pdo = new PDO($dbConfig['dsn'], $dbConfig['username'], $dbConfig['password']);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Check user table columns
    $stmt = $pdo->query("DESCRIBE `user`");
    $userCols = $stmt->fetchAll(PDO::FETCH_COLUMN);
    echo "Columns in user table:\n";
    foreach ($userCols as $col) {
        echo "- $col\n";
    }

    // Check if sakip_unit_kerja exists
    $stmt = $pdo->query("SHOW TABLES LIKE 'sakip_unit_kerja'");
    $exists = $stmt->fetch();
    echo "sakip_unit_kerja exists: " . ($exists ? "YES" : "NO") . "\n";

    // Check if sakip_evaluasi_renja exists
    $stmt = $pdo->query("SHOW TABLES LIKE 'sakip_evaluasi_renja'");
    $exists = $stmt->fetch();
    echo "sakip_evaluasi_renja exists: " . ($exists ? "YES" : "NO") . "\n";

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
