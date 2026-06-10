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

echo "Testing Frontend Search Models and SiteController Portals:\n";
echo "---------------------------------------------------------\n";

$tests = [];

// 1. Test Search Models
$searchModels = [
    'frontend\models\search\SakipCascadingkegiatanSearch',
    'frontend\models\search\SakipCascadingprogramSearch',
    'frontend\models\search\SakipCascadingsubkegiatanSearch',
    'frontend\models\search\SakipIndikatorcascadingkegiatanSearch',
    'frontend\models\search\SakipIndikatorcascadingkegiatanTriwulanSearch',
    'frontend\models\search\SakipIndikatorcascadingprogramSearch',
    'frontend\models\search\SakipIndikatorcascadingprogramTriwulanSearch',
    'frontend\models\search\SakipIndikatorcascadingsubkegiatanSearch',
    'frontend\models\search\SakipIndikatorcascadingsubkegiatanTriwulanSearch',
    'frontend\models\search\SakipIndikatorsasaranrenstraSearch',
    'frontend\models\search\SakipIndikatorsasaranrenstraPSearch',
    'frontend\models\search\SakipIndikatorsasaranrenstraTriwulanSearch',
    'frontend\models\search\SakipIndikatorsasaranrenstraPTriwulanSearch',
    'frontend\models\search\SakipIndikatortujuanrenstraSearch',
    'frontend\models\search\SakipKebijakanSearch',
    'frontend\models\search\SakipRenstratujuanSearch',
    'frontend\models\search\SakipSasaranrenstraSearch',
    'frontend\models\search\SakipSasaranrenstraPSearch',
    'frontend\models\search\SakipStrategiSearch',
    'frontend\models\search\SakipTujuanrenstraSearch',
    'frontend\models\search\SakipTujuanrenstraPSearch',
    'frontend\models\search\SakipVisiSearch',
    'frontend\models\search\SakipVisiPSearch',
    'frontend\models\search\UserSearch',
];

foreach ($searchModels as $searchModelClass) {
    $tests[$searchModelClass] = function() use ($searchModelClass) {
        if (!class_exists($searchModelClass)) {
            return "Skipped (Class does not exist)";
        }
        $searchModel = new $searchModelClass();
        $dataProvider = $searchModel->search([]);
        return "OK (Count: " . $dataProvider->getTotalCount() . ")";
    };
}

// 2. Test SiteController actionIndexEsakip query logic
$tests['SiteController - actionIndexEsakip query logic'] = function() {
    $refperiode_id = 2; // Year 2025-2029
    $refskpd_id = 2;    // Some SKPD
    
    $programListAll = \frontend\models\SakipCascadingProgram::find()
        ->where(['refskpd_id' => $refskpd_id, 'refperiode_id' => $refperiode_id])
        ->with(['indikatorCascadingPrograms.refIndikatorCascadingProgramTriwulan' => function ($query) use ($refskpd_id, $refperiode_id) {
            $query->andWhere([
                'refskpd_id' => $refskpd_id,
                'refperiode_id' => $refperiode_id,
            ]);
        }])
        ->all();
        
    return "OK (Cascading Programs fetched: " . count($programListAll) . ")";
};

// 3. Test SiteController actionPortalPublikPerencanaan query logic
$tests['SiteController - actionPortalPublikPerencanaan query logic'] = function() {
    $refperiode_id = 2;
    $selectedPeriod = \frontend\models\SakipPeriode::find()->where(['refperiode_id' => $refperiode_id])->one();
    $refperiode_5tahun_id = $selectedPeriod ? $selectedPeriod->refperiode_5tahun_id : null;
    
    $allSasaranRenstra = \frontend\models\SakipSasaranrenstra::find()
        ->where(['refperiode_5tahun_id' => $refperiode_5tahun_id])
        ->with(['refVisi', 'refMisi', 'refTujuan', 'refSasaran'])
        ->all();
        
    $allStrategi = \frontend\models\SakipStrategi::find()
        ->where(['refperiode_5tahun_id' => $refperiode_5tahun_id])
        ->all();
        
    $allKebijakan = \frontend\models\SakipKebijakan::find()
        ->where(['refperiode_5tahun_id' => $refperiode_5tahun_id])
        ->all();
        
    $allIndikatorsIku = \frontend\models\SakipIndikatorsasaranrenstra::find()
        ->where(['refperiode_id' => $refperiode_id])
        ->all();
        
    return "OK (Sasaran: " . count($allSasaranRenstra) . ", Strategi: " . count($allStrategi) . ", Kebijakan: " . count($allKebijakan) . ", IKU: " . count($allIndikatorsIku) . ")";
};

// 4. Test SiteController actionPortalPublikCapkin query logic
$tests['SiteController - actionPortalPublikCapkin query logic'] = function() {
    $refperiode_id = 2;
    
    $allIndikators = \frontend\models\SakipIndikatorSasaranRenstra::find()
        ->andWhere(['refperiode_id' => $refperiode_id])
        ->with('refSasaranrenstra')
        ->all();
        
    $allTriwulanData = \frontend\models\SakipIndikatorSasaranRenstraTriwulan::find()
        ->where(['refperiode_id' => $refperiode_id])
        ->all();
        
    return "OK (Indicators: " . count($allIndikators) . ", Triwulan Data: " . count($allTriwulanData) . ")";
};

// 5. Test SiteController actionPortalPublikTabulasi query logic
$tests['SiteController - actionPortalPublikTabulasi query logic'] = function() {
    $refperiode_id = 2;
    
    $programAllWithRelations = \frontend\models\SakipCascadingProgram::find()
        ->where(['refperiode_id' => $refperiode_id])
        ->with([
            'refProgram',
            'indikatorCascadingPrograms.refIndikatorCascadingProgramTriwulan'
        ])->all();
        
    $kegiatanAllWithRelations = \frontend\models\SakipCascadingkegiatan::find()
        ->where(['refperiode_id' => $refperiode_id])
        ->with([
            'refKegiatan',
            'indikatorCascadingKegiatan.refIndikatorCascadingKegiatanTriwulan'
        ])->all();
        
    $subkegiatanAllWithRelations = \frontend\models\SakipCascadingsubkegiatan::find()
        ->where(['refperiode_id' => $refperiode_id])
        ->with([
            'refSubkegiatan',
            'refIndikatorcascadingsubkegiatan.refIndikatorCascadingSubkegiatanTriwulan'
        ])->all();
        
    return "OK (Programs: " . count($programAllWithRelations) . ", Kegiatan: " . count($kegiatanAllWithRelations) . ", Subkegiatan: " . count($subkegiatanAllWithRelations) . ")";
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
    echo "\nVerification Success: All frontend query checks passed successfully!\n";
    exit(0);
} else {
    echo "\nVerification Failed: Some frontend query checks failed.\n";
    exit(1);
}
