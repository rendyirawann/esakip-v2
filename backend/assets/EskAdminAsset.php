<?php

namespace backend\assets;

use yii\web\AssetBundle;

/**
 * Stylesheet kustom eSakip untuk mempercantik tampilan admin
 * (konten, sidebar, dan header). Bergantung pada AppAsset agar selalu
 * dimuat SETELAH CSS template, sehingga override-nya pasti diterapkan.
 *
 * Hanya tampilan — tidak mengubah markup/fungsi.
 */
class EskAdminAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [
        'css/esk-admin.css',
    ];
    public $depends = [
        'backend\assets\AppAsset',
    ];
}
