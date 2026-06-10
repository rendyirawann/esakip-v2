<?php

namespace frontend\assets;

use yii\web\AssetBundle;

/**
 * Main frontend application asset bundle.
 */
class AppAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [
        'swagger/swagger-ui.css',
        'swagger/index.css',
    ];
    public $js = [
        'swagger/swagger-ui-bundle.js',
        'swagger/swagger-ui-standalone-preset.js',
        'swagger/swagger-initializer.js',
    ];
    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap5\BootstrapAsset',
    ];
}
