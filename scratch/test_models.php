<?php
defined('YII_DEBUG') or define('YII_DEBUG', true);
defined('YII_ENV') or define('YII_ENV', 'dev');

require dirname(__DIR__) . '/vendor/autoload.php';
require dirname(__DIR__) . '/vendor/yiisoft/yii2/Yii.php';
require dirname(__DIR__) . '/common/config/bootstrap.php';
require dirname(__DIR__) . '/console/config/bootstrap.php';

$config = yii\helpers\ArrayHelper::merge(
    require dirname(__DIR__) . '/common/config/main.php',
    require dirname(__DIR__) . '/common/config/main-local.php',
    require dirname(__DIR__) . '/console/config/main.php',
    require dirname(__DIR__) . '/console/config/main-local.php'
);

$application = new yii\console\Application($config);

$modelsToCheck = [
    'backend\models\SakipVisi',
    'backend\models\SakipVisiP',
    'backend\models\SakipMisi',
    'backend\models\SakipMisiP',
    'backend\models\SakipTujuan',
    'backend\models\SakipTujuanP',
    'backend\models\SakipSasaran',
    'backend\models\SakipSasaranP',
    'frontend\models\SakipVisi',
    'frontend\models\SakipVisiP',
    'frontend\models\SakipMisi',
    'frontend\models\SakipMisiP',
    'frontend\models\SakipTujuan',
    'frontend\models\SakipTujuanP',
    'frontend\models\SakipSasaran',
    'frontend\models\SakipSasaranP',
    'frontend\models\SakipTujuanrenstra',
    'frontend\models\SakipTujuanrenstraP',
    'frontend\models\SakipSasaranrenstra',
    'frontend\models\SakipSasaranrenstraP',
    'frontend\models\SakipStrategi',
    'frontend\models\SakipKebijakan'
];

echo "Verifying SAKIP 5-Yearly and Renstra Models:\n";
echo "-----------------------------------------\n";

$allOk = true;

foreach ($modelsToCheck as $modelClass) {
    try {
        echo "Querying {$modelClass}... ";
        $count = $modelClass::find()->count();
        echo "OK (Count: {$count})\n";
        
        // Let's test the relationships for the first model if available
        $first = $modelClass::find()->one();
        if ($first) {
            echo "  - Testing getPeriode() for {$modelClass}... ";
            if ($first->periode) {
                echo "OK (Year: {$first->periode->periode})\n";
            } else {
                echo "NULL (no matching active year)\n";
            }
            
            echo "  - Testing getPeriode5Tahun() for {$modelClass}... ";
            if ($first->periode5Tahun) {
                echo "OK (Name: {$first->periode5Tahun->nama_periode})\n";
            } else {
                echo "NULL (no matching 5-yearly period)\n";
            }
        }
    } catch (\Exception $e) {
        echo "FAILED!\n";
        echo "  Error: " . $e->getMessage() . "\n";
        $allOk = false;
    }
}

if ($allOk) {
    echo "\nVerification Success: All models queried and relations evaluated successfully!\n";
    exit(0);
} else {
    echo "\nVerification Failed: Some models encountered errors.\n";
    exit(1);
}
