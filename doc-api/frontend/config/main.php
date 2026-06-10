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
            'enableCsrfValidation' => false, // <- Tambahkan baris ini
            'parsers' => [
                'application/json' => 'yii\web\JsonParser',
            ],
        ],
        'user' => [
            'identityClass' => 'common\models\User',
            'enableAutoLogin' => true,
            'identityCookie' => ['name' => '_identity-frontend', 'httpOnly' => true],
        ],
        'session' => [
            // this is the name of the session cookie used for login on the frontend
            'name' => 'advanced-frontend',
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
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'enableStrictParsing' => true,
            'rules' => [
                // === Aturan Dasar ===
                'GET api/periode' => 'esakip-api/periode',
                'GET api/skpd' => 'esakip-api/skpd',
                'GET api/program' => 'esakip-api/program',
                'GET api/kegiatan' => 'esakip-api/kegiatan',
                'GET api/subkegiatan' => 'esakip-api/subkegiatan',

                // === Aturan Capaian Kinerja RENSTRA (Paginated) ===
                'GET api/capkin-renstra' => 'esakip-api/list-capkin-renstra-skpd-paginated',
                'GET api/capkin-renstra/<refperiode_id:\d+>' => 'esakip-api/list-capkin-renstra-skpd-paginated',
                'GET api/capkin-renstra/<refperiode_id:\d+>/<filter_refskpd_id:\d+>' => 'esakip-api/list-capkin-renstra-skpd-paginated',

                // === Aturan Capaian Kinerja PROGRAM (Paginated) ===
                'GET api/capkin-program' => 'esakip-api/list-capkin-program-skpd-paginated',
                'GET api/capkin-program/<refperiode_id:\d+>' => 'esakip-api/list-capkin-program-skpd-paginated',
                'GET api/capkin-program/<refperiode_id:\d+>/<filter_refskpd_id:\d+>' => 'esakip-api/list-capkin-program-skpd-paginated',

                // === Aturan Capaian Kinerja KEGIATAN (Paginated) ===
                'GET api/capkin-kegiatan' => 'esakip-api/list-capkin-kegiatan-skpd-paginated',
                'GET api/capkin-kegiatan/<refperiode_id:\d+>' => 'esakip-api/list-capkin-kegiatan-skpd-paginated',
                'GET api/capkin-kegiatan/<refperiode_id:\d+>/<filter_refskpd_id:\d+>' => 'esakip-api/list-capkin-kegiatan-skpd-paginated',

                // === Aturan Capaian Kinerja SUBKEGIATAN (Paginated) ===
                'GET api/capkin-subkegiatan' => 'esakip-api/list-capkin-subkegiatan-skpd-paginated',
                'GET api/capkin-subkegiatan/<refperiode_id:\d+>' => 'esakip-api/list-capkin-subkegiatan-skpd-paginated',
                'GET api/capkin-subkegiatan/<refperiode_id:\d+>/<filter_refskpd_id:\d+>' => 'esakip-api/list-capkin-subkegiatan-skpd-paginated',

                // === Aturan untuk endpoint lama (Non-Paginated) ===
                'GET api/capkin-indikator-renstra' => 'esakip-api/capkin-indikator-renstra',
                'GET api/capkin-indikator-renstra/<refperiode_id:\d+>' => 'esakip-api/capkin-indikator-renstra',
                'GET api/capkin-indikator-renstra/<refperiode_id:\d+>/<refskpd_id:\d+>' => 'esakip-api/capkin-indikator-renstra',

                'GET api/capkin-indikator-program' => 'esakip-api/capkin-indikator-program',
                'GET api/capkin-indikator-program/<refperiode_id:\d+>' => 'esakip-api/capkin-indikator-program',
                'GET api/capkin-indikator-program/<refperiode_id:\d+>/<refskpd_id:\d+>' => 'esakip-api/capkin-indikator-program',

                'GET api/capkin-indikator-kegiatan' => 'esakip-api/capkin-indikator-kegiatan',
                'GET api/capkin-indikator-kegiatan/<refperiode_id:\d+>' => 'esakip-api/capkin-indikator-kegiatan',
                'GET api/capkin-indikator-kegiatan/<refperiode_id:\d+>/<refskpd_id:\d+>' => 'esakip-api/capkin-indikator-kegiatan',

                'GET api/capkin-indikator-subkegiatan' => 'esakip-api/capkin-indikator-subkegiatan',
                'GET api/capkin-indikator-subkegiatan/<refperiode_id:\d+>' => 'esakip-api/capkin-indikator-subkegiatan',
                'GET api/capkin-indikator-subkegiatan/<refperiode_id:\d+>/<refskpd_id:\d+>' => 'esakip-api/capkin-indikator-subkegiatan',
            ],
        ],
    ],
    'params' => $params,
];
