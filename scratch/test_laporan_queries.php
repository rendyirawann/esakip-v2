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

echo "Testing LaporanController specific query blocks:\n";
echo "------------------------------------------------\n";

$refskpd_id = 8;     // From user screenshot
$refperiode_id = 8;  // From user screenshot (Year 2026 is usually refperiode_id = 8 in standard SAKIP)

$tests = [];

// 1. actionIndexLaporanRenstra queries
$tests['actionIndexLaporanRenstra'] = function() use ($refskpd_id, $refperiode_id) {
    $sasaranRenstra = \frontend\models\SakipSasaranrenstra::find()
        ->where(['refskpd_id' => $refskpd_id, 'refperiode_id' => $refperiode_id])
        ->with(['refVisi', 'refMisi', 'refTujuan', 'refSasaran'])
        ->all();

    $strategiList = \frontend\models\SakipStrategi::find()
        ->where(['refskpd_id' => $refskpd_id, 'refperiode_id' => $refperiode_id])
        ->all();

    $kebijakanList = \frontend\models\SakipKebijakan::find()
        ->where(['refskpd_id' => $refskpd_id, 'refperiode_id' => $refperiode_id])
        ->all();

    return "OK (Sasaran: " . count($sasaranRenstra) . ", Strategi: " . count($strategiList) . ", Kebijakan: " . count($kebijakanList) . ")";
};

// 2. actionIndexLaporanRenjaTahunan queries
$tests['actionIndexLaporanRenjaTahunan'] = function() use ($refskpd_id, $refperiode_id) {
    $sasaranRenstra = \frontend\models\SakipSasaranrenstra::find()
        ->where(['refskpd_id' => $refskpd_id, 'refperiode_id' => $refperiode_id])
        ->with(['refVisi', 'refMisi', 'refTujuan', 'refSasaran'])
        ->all();

    return "OK (Sasaran: " . count($sasaranRenstra) . ")";
};

// 3. actionCetakLaporanTapkin / TapkinExcel queries
$tests['actionCetakLaporanTapkin'] = function() use ($refskpd_id, $refperiode_id) {
    $sasaranRenstra = \frontend\models\SakipSasaranrenstra::find()->where(['refskpd_id' => $refskpd_id, 'refperiode_id' => $refperiode_id])->all();
    return "OK (Sasaran: " . count($sasaranRenstra) . ")";
};

// 4. actionIndexLaporanAnalisisSasaranTriwulan / Tahunan queries
$tests['actionIndexLaporanAnalisisSasaranTriwulan'] = function() use ($refskpd_id, $refperiode_id) {
    $sasaranRenstraList = \frontend\models\SakipSasaranrenstra::find()
        ->where(['refskpd_id' => $refskpd_id, 'refperiode_id' => $refperiode_id])
        ->all();
    return "OK (Sasaran: " . count($sasaranRenstraList) . ")";
};

// 5. actionCetakLaporanEkinerjaExcel queries
$tests['actionCetakLaporanEkinerjaExcel'] = function() use ($refskpd_id, $refperiode_id) {
    $sasaranRenstra = \frontend\models\SakipSasaranrenstra::find()
        ->where(['refskpd_id' => $refskpd_id, 'refperiode_id' => $refperiode_id])
        ->with(['indikatorSasaran.cascadingPrograms.refProgram'])
        ->all();
    return "OK (Sasaran: " . count($sasaranRenstra) . ")";
};

$allOk = true;
foreach ($tests as $name => $testFn) {
    try {
        echo "Testing {$name}... ";
        $res = $testFn();
        echo "{$res}\n";
    } catch (\Exception $e) {
        echo "FAILED!\n";
        echo "  Error: " . $e->getMessage() . "\n";
        $allOk = false;
    }
}

if ($allOk) {
    echo "\nVerification Success: All LaporanController query blocks passed successfully!\n";
    exit(0);
} else {
    echo "\nVerification Failed: Some query blocks failed.\n";
    exit(1);
}
