<?php
$config = require __DIR__ . '/../common/config/main-local.php';
$dbConfig = $config['components']['db'];
try {
    $pdo = new PDO($dbConfig['dsn'], $dbConfig['username'], $dbConfig['password']);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $versions = [
        'm260505_090000_create_sakip_evaluasi_renja_table',
        'm260505_100000_alter_sakip_evaluasi_renja_table',
        'm260505_100001_fix_sakip_evaluasi_renja_table'
    ];

    $stmt = $pdo->prepare("SELECT COUNT(*) FROM migration WHERE version = ?");
    $insertStmt = $pdo->prepare("INSERT INTO migration (version, apply_time) VALUES (?, ?)");

    foreach ($versions as $v) {
        $stmt->execute([$v]);
        $count = $stmt->fetchColumn();
        if ($count == 0) {
            $insertStmt->execute([$v, time()]);
            echo "Registered migration: $v\n";
        } else {
            echo "Migration $v is already registered.\n";
        }
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
