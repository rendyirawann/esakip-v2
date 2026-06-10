<?php

namespace frontend\assets;

use yii\web\AssetBundle;

/**
 * Main frontend application asset bundle.
 */
class ComingSoonAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [
        'comingsoon/vendor/bootstrap/css/bootstrap.min.css',
        'comingsoon/fonts/font-awesome-4.7.0/css/font-awesome.min.css',
        'comingsoon/vendor/animate/animate.css',
        'comingsoon/vendor/select2/select2.min.css',
        'comingsoon/css/util.css',
        'comingsoon/css/main.css',
    ];
    public $js = [
        'https://cdn.jsdelivr.net/npm/echarts/dist/echarts.min.js',
        'https://cdn.jsdelivr.net/npm/chart.js',
        'lightapp/assets/js/pages/chart-apex.js',
        'lightapp/assets/js/pages/w-chart.js',
        'lightapp/assets/js/plugins/wow.min.js',
        'lightapp/assets/js/plugins/swiper-bundle.js',
        'lightapp/assets/js/plugins/choices.min.js',
        'https://unpkg.com/leaflet/dist/leaflet.js',
        'lightapp/assets/js/plugins/apexcharts.min.js',
        'lightapp/assets/js/plugins/jsvectormap.min.js',
        'lightapp/assets/js/plugins/world.js',
        'lightapp/assets/js/plugins/world-merc.js',
        'lightapp/assets/js/pages/dashboard-default.js',
        'lightapp/assets/js/plugins/popper.min.js',
        'lightapp/assets/js/plugins/simplebar.min.js',
        'lightapp/assets/js/plugins/bootstrap.min.js',
        'lightapp/assets/js/fonts/custom-font.js',
        'lightapp/assets/js/pcoded.js',
        'lightapp/assets/js/plugins/feather.min.js',
        //    'https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js',
        'lightapp/assets/js/plugins/dataTables.min.js',
        'lightapp/assets/js/plugins/dataTables.bootstrap5.min.js',
        'lightapp/assets/js/plugins/dataTables.responsive.min.js',
        'lightapp/assets/js/plugins/responsive.bootstrap5.min.js',
        'https://cdnjs.cloudflare.com/ajax/libs/highlight.js/9.12.0/highlight.min.js',
        'lightapp/assets/js/plugins/clipboard.min.js',
        'lightapp/assets/js/component.js',
        'lightapp/assets/js/plugins/index.global.min.js',
        'lightapp/assets/js/plugins/sweetalert2.all.min.js',
        // 'lightapp/assets/js/pages/calendar.js',
    ];
    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap5\BootstrapAsset',
    ];
}
