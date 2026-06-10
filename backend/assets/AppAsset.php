<?php

namespace backend\assets;

use yii\web\AssetBundle;

/**
 * Main backend application asset bundle.
 */
class AppAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [
        // 'css/site.css',
        'https://unpkg.com/leaflet/dist/leaflet.css',
        'lightapp/assets/css/plugins/jsvectormap.min.css',
        'https://fonts.googleapis.com/css2?family=Public+Sans:wght@400;500;600;700&display=swap',
        'lightapp/assets/fonts/tabler-icons.min.css',
        'lightapp/assets/fonts/feather.css',
        'lightapp/assets/fonts/fontawesome.css',
        'lightapp/assets/fonts/material.css',
        'lightapp/assets/css/style.css',
        'lightapp/assets/css/style-preset.css',
        'lightapp/assets/css/plugins/dataTables.bootstrap5.min.css',
        'lightapp/assets/css/plugins/responsive.bootstrap5.min.css',
        'lightapp/assets/css/plugins/animate.min.css',
        'lightapp/assets/css/uikit.css',
        'lightapp/assets/css/plugins/swiper-bundle.css',
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
    ];
    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap5\BootstrapAsset',
    ];
}
