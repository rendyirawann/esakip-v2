<?php

namespace frontend\assets;

use yii\web\AssetBundle;

/**
 * Main frontend application asset bundle.
 */
class MainAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [
        // 'css/site.css',
        // 'https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&family=Raleway:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap',
        'https://fonts.googleapis.com/css2?family=Public+Sans:wght@400;500;600;700&display=swap',
        'mainassets/vendor/bootstrap/css/bootstrap.min.css',
        'mainassets/vendor/bootstrap-icons/bootstrap-icons.css',
        'mainassets/vendor/aos/aos.css',
        'mainassets/vendor/glightbox/css/glightbox.min.css',
        'mainassets/vendor/swiper/swiper-bundle.min.css',
        'mainassets/css/main.css',

    ];
    public $js = [

        'mainassets/vendor/bootstrap/js/bootstrap.bundle.min.js',
        'mainassets/vendor/php-email-form/validate.js',
        'mainassets/vendor/aos/aos.js',
        'mainassets/vendor/typed.js/typed.umd.js',
        'mainassets/vendor/purecounter/purecounter_vanilla.js',
        'mainassets/vendor/waypoints/noframework.waypoints.js',
        'mainassets/vendor/glightbox/js/glightbox.min.js',
        'mainassets/vendor/imagesloaded/imagesloaded.pkgd.min.js',
        'mainassets/vendor/isotope-layout/isotope.pkgd.min.js',
        'mainassets/vendor/swiper/swiper-bundle.min.js',
        'mainassets/js/main.js',
        // 'welcomeasset/script.js',
    ];
    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap5\BootstrapAsset',
    ];
}
