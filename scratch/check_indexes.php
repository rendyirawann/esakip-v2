<?php
// Initialize Yii2
define('YII_DEBUG', true);
define('YII_ENV', 'dev');
require(__DIR__ . '/../vendor/autoload.php');
require(__DIR__ . '/../vendor/yiisoft/yii2/Yii.php');
require(__DIR__ . '/../common/config/bootstrap.php');
require(__DIR__ . '/../backend/config/bootstrap.php');

$config = yii\helpers\ArrayHelper::merge(
    require(__DIR__ . '/../common/config/main.php'),
    require(__DIR__ . '/../common/config/main-local.php'),
    require(__DIR__ . '/../backend/config/main.php'),
    require(__DIR__ . '/../backend/config/main-local.php')
);

(new yii\web\Application($config));

$db = Yii::$app->db;

$tables = [
    'v2_sakip_periode_5tahun',
    'v2_sakip_periode',
    'v2_sakip_visi',
    'v2_sakip_visi_p',
    'v2_sakip_misi',
    'v2_sakip_misi_p',
    'v2_sakip_tujuan',
    'v2_sakip_tujuan_p',
    'v2_sakip_sasaran',
    'v2_sakip_sasaran_p',
    'v2_sakip_tujuanrenstra',
    'v2_sakip_tujuanrenstra_p',
    'v2_sakip_sasaranrenstra',
    'v2_sakip_sasaranrenstra_p',
    'v2_sakip_strategi',
    'v2_sakip_kebijakan'
];

foreach ($tables as $table) {
    echo "=== Indexes for table: $table ===\n";
    try {
        $indexes = $db->createCommand("SHOW INDEX FROM `$table`")->queryAll();
        foreach ($indexes as $index) {
            echo "  Key: {$index['Key_name']} | Column: {$index['Column_name']} | Non-unique: {$index['Non_unique']}\n";
        }
    } catch (\Exception $e) {
        echo "  Error: " . $e->getMessage() . "\n";
    }
    echo "\n";
}
