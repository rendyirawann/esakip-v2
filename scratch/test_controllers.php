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
    require dirname(__DIR__) . '/frontend/config/main.php',
    require dirname(__DIR__) . '/frontend/config/main-local.php'
);

$application = new yii\web\Application($config);

echo "Testing Frontend Controller Query Logic:\n";
echo "----------------------------------------\n";

$tests = [];

// 1. Test SakipVisiController query logic
$tests['SakipVisiController'] = function() {
    $refperiode_id = 1; // 2019-2023 cycle year
    $refskpd_id = 1;
    $selectedPeriod = \frontend\models\SakipPeriode::findOne($refperiode_id);
    $refperiode_5tahun_id = $selectedPeriod ? $selectedPeriod->refperiode_5tahun_id : null;
    
    $visi = \frontend\models\SakipVisi::find()->where(['refperiode_5tahun_id' => $refperiode_5tahun_id])->one();
    $refMisiIds = \frontend\models\SakipSasaranrenstra::find()
        ->select('refmisi_id')
        ->where(['refskpd_id' => $refskpd_id, 'refperiode_5tahun_id' => $refperiode_5tahun_id])
        ->distinct()
        ->column();
    $misiData = \frontend\models\SakipMisi::find()
        ->where(['refmisi_id' => $refMisiIds, 'refperiode_5tahun_id' => $refperiode_5tahun_id])
        ->all();
        
    return "Visi count for cycle " . ($refperiode_5tahun_id ?? 'NULL') . ": " . ($visi ? 1 : 0) . ", Misi count: " . count($misiData);
};

// 2. Test SakipTujuanrenstraController query logic
$tests['SakipTujuanrenstraController'] = function() {
    $refperiode_id = 2; // 2025-2029 cycle year
    $refskpd_id = 1;
    $selectedPeriod = \frontend\models\SakipPeriode::findOne($refperiode_id);
    $refperiode_5tahun_id = $selectedPeriod ? $selectedPeriod->refperiode_5tahun_id : null;
    
    $sasaranRenstraList = \frontend\models\SakipSasaranrenstra::find()
        ->where(['refskpd_id' => $refskpd_id, 'refperiode_5tahun_id' => $refperiode_5tahun_id])
        ->with(['misi', 'tujuan', 'tujuanRenstra'])
        ->all();
        
    return "Sasaran Renstra list count: " . count($sasaranRenstraList);
};

// 3. Test SakipSasaranrenstraController query logic
$tests['SakipSasaranrenstraController'] = function() {
    $refperiode_id = 2;
    $refskpd_id = 1;
    $selectedPeriod = \frontend\models\SakipPeriode::findOne($refperiode_id);
    $refperiode_5tahun_id = $selectedPeriod ? $selectedPeriod->refperiode_5tahun_id : null;
    
    $searchModel = new \frontend\models\search\SakipSasaranrenstraSearch();
    $dataProvider = $searchModel->search([
        'SakipSasaranrenstraSearch' => [
            'refskpd_id' => $refskpd_id,
            'refperiode_5tahun_id' => $refperiode_5tahun_id
        ]
    ]);
    
    return "DataProvider count: " . $dataProvider->getTotalCount();
};

// 4. Test SiteController actionIndexEsakip query logic
$tests['SiteController - actionIndexEsakip'] = function() {
    $refperiode_id = 2;
    $refskpd_id = 1;
    $selectedPeriod = \frontend\models\SakipPeriode::findOne($refperiode_id);
    $refperiode_5tahun_id = $selectedPeriod ? $selectedPeriod->refperiode_5tahun_id : null;
    
    $statusSasaranRenstra = (bool) \frontend\models\SakipSasaranrenstra::find()
        ->where(['refskpd_id' => $refskpd_id, 'refperiode_5tahun_id' => $refperiode_5tahun_id])
        ->exists();
    $statusTujuanRenstra = (bool) \frontend\models\SakipTujuanrenstra::find()
        ->where(['refskpd_id' => $refskpd_id, 'refperiode_5tahun_id' => $refperiode_5tahun_id])
        ->exists();
    $sakipSasaranRenstra = \frontend\models\SakipSasaranrenstra::find()
        ->where(['refskpd_id' => $refskpd_id, 'refperiode_5tahun_id' => $refperiode_5tahun_id])
        ->with(['refMisi', 'refTujuan', 'refVisi'])
        ->all();
        
    return "statusSasaranRenstra: " . ($statusSasaranRenstra ? 'true' : 'false') . ", statusTujuanRenstra: " . ($statusTujuanRenstra ? 'true' : 'false') . ", items: " . count($sakipSasaranRenstra);
};

$allOk = true;
foreach ($tests as $name => $testFn) {
    try {
        echo "Testing {$name}... ";
        $res = $testFn();
        echo "OK ({$res})\n";
    } catch (\Exception $e) {
        echo "FAILED!\n";
        echo "  Error: " . $e->getMessage() . "\n";
        $allOk = false;
    }
}

if ($allOk) {
    echo "\nVerification Success: All controller queries executed successfully!\n";
    exit(0);
} else {
    echo "\nVerification Failed: Some controller queries failed.\n";
    exit(1);
}
