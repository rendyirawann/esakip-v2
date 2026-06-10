<?php
$params = array_merge(
    require __DIR__ . '/../../common/config/params.php',
    require __DIR__ . '/../../common/config/params-local.php',
    require __DIR__ . '/params.php',
    require __DIR__ . '/params-local.php'
);

return [
    'id' => 'app-frontend',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'controllerNamespace' => 'frontend\controllers',
    'components' => [
        'request' => [
            'csrfParam' => '_csrf-frontend',
        ],
        'user' => [
            'identityClass' => 'common\models\User',
            'enableAutoLogin' => true,
            'identityCookie' => ['name' => '_identity-frontend', 'httpOnly' => true],
        ],
        // 'session' => [
        //     // this is the name of the session cookie used for login on the frontend
        //     'name' => 'advanced-frontend',
        // ],
        // 1. Definisikan Connection Redis
        'redis' => [
            'class' => 'yii\redis\Connection',
            'hostname' => 'localhost',
            'port' => 6379,
            'database' => 0,
        ],
        'cache' => [
            'class' => 'yii\redis\Cache',
        ],

        // 3. (Opsional) Pindahkan Session ke Redis agar lebih cepat
        'session' => [
            'class' => 'yii\redis\Session',
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => \yii\log\FileTarget::class,
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        // TAMBAHKAN BLOK INI
        'pdf' => [
            'class' => \kartik\mpdf\Pdf::class,
            'format' => \kartik\mpdf\Pdf::FORMAT_A4,
            'orientation' => \kartik\mpdf\Pdf::ORIENT_LANDSCAPE, // Ganti jadi 'PORTRAIT' jika perlu
            'destination' => \kartik\mpdf\Pdf::DEST_BROWSER,
            // Lihat dokumentasi kartik-v/yii2-mpdf untuk opsi lainnya
        ],

        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        /*
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'rules' => [
            ],
        ],
        */
    ],
    'params' => $params,
];
