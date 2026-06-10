<?php

namespace frontend\assets;

use yii\web\AssetBundle;

/**
 * Main frontend application asset bundle.
 */
class MainPortalAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [
        // 'css/site.css',
        // 'https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&family=Raleway:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap',
        'https://fonts.googleapis.com/css2?family=Public+Sans:wght@400;500;600;700&display=swap',
        'mainassets/vendor/bootstrap-icons/bootstrap-icons.css',
        'mainassets/vendor/aos/aos.css',
        'mainassets/vendor/glightbox/css/glightbox.min.css',
        'mainassets/vendor/swiper/swiper-bundle.min.css',
        // 'mainassets/css/main.css',
        // 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css',
        // 'https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.1/css/bootstrap.min.css',
        'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css',
        'https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css',
        'https://unpkg.com/aos@next/dist/aos.css',
        'udema/css/bootstrap.min.css',
        'udema/css/style.css',
        'udema/css/vendors.css',
        'udema/css/icon_fonts/css/all_icons.min.css',
        'udema/css/custom.css',
        'udema/layerslider/css/layerslider.css',
        'udema/css/blog.css',
        'udema/vendor/font-awesome/css/font-awesome.min.css',

    ];
    public $js = [
        // 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js',
        'https://unpkg.com/vue@2.6.14/dist/vue.js',
        'https://unpkg.com/axios/dist/axios.min.js',
        'https://unpkg.com/aos@next/dist/aos.js',
        'mainassets/vendor/php-email-form/validate.js',
        'mainassets/vendor/aos/aos.js',
        'mainassets/vendor/typed.js/typed.umd.js',
        'mainassets/vendor/purecounter/purecounter_vanilla.js',
        'mainassets/vendor/waypoints/noframework.waypoints.js',
        'mainassets/vendor/glightbox/js/glightbox.min.js',
        'mainassets/vendor/imagesloaded/imagesloaded.pkgd.min.js',
        'mainassets/vendor/isotope-layout/isotope.pkgd.min.js',
        'mainassets/vendor/swiper/swiper-bundle.min.js',
        // 'mainassets/js/main.js',
        // 'welcomeasset/script.js',
        'udema/js/modernizr.js',
        'udema/js/jquery-3.7.1.min.js',
        'udema/js/common_scripts.js',
        'udema/js/main.js',
        'udema/assets/validate.js',
        'udema/js/video_header.js',
        'udema/layerslider/js/greensock.js',
        'udema/layerslider/js/layerslider.transitions.js',
        'udema/layerslider/js/layerslider.kreaturamedia.jquery.js',
    ];
    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap5\BootstrapAsset',
    ];
}
