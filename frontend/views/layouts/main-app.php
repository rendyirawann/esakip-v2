<?php

/** @var \yii\web\View $this */
/** @var string $content */

use common\widgets\Alert;
use frontend\assets\MainAsset;
use yii\bootstrap5\Html;
use yii\helpers\Url;

MainAsset::register($this);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>" class="h-100">

<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <meta name="description" content="ESAKIP - PEMERINTAHAN KABUPATEN DELI SERDANG" />
    <meta name="author" content="rendyirawan" />
    <title>Switch Dashboard - ESAKIP SIMONALISA</title>
    <?php $this->registerCsrfMetaTags() ?>
    <!-- [Favicon] icon -->
    <link rel="icon" href="<?= Url::base(true) ?>/lightapp/assets/images/bappeda.png" type="image/x-icon" />
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap');

        * { box-sizing: border-box; }

        body {
            background: radial-gradient(900px 600px at 15% 8%, rgba(13, 148, 136, .28) 0%, transparent 55%),
                        radial-gradient(900px 600px at 88% 100%, rgba(16, 185, 129, .18) 0%, transparent 55%),
                        linear-gradient(135deg, #03201c 0%, #052b26 50%, #07382f 100%);
            color: #e8fffb;
            overflow: hidden;
            min-height: 100vh;
            font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
        }

        a { text-decoration: none; color: inherit; outline: none; }

        /* Hiasan glow di latar */
        body::before,
        body::after {
            content: "";
            position: fixed;
            border-radius: 50%;
            filter: blur(80px);
            z-index: 0;
            pointer-events: none;
        }
        body::before { width: 420px; height: 420px; top: -120px; left: -100px; background: rgba(20, 184, 166, .18); }
        body::after  { width: 360px; height: 360px; bottom: -140px; right: -80px; background: rgba(16, 185, 129, .14); }

        .profile-container {
            position: relative;
            z-index: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            padding: 24px;
            text-align: center;
            animation: fadeIn 1s ease-out;
        }

        .esk-switch-eyebrow {
            display: inline-block;
            font-size: .78rem;
            font-weight: 700;
            letter-spacing: 2.5px;
            text-transform: uppercase;
            color: #5eead4;
            background: rgba(45, 212, 191, .1);
            border: 1px solid rgba(45, 212, 191, .25);
            padding: 6px 16px;
            border-radius: 999px;
            margin-bottom: 1.1rem;
            animation: slideDown .7s ease-out;
        }

        .profile-title {
            font-size: 2.4rem;
            font-weight: 800;
            letter-spacing: -.5px;
            margin: 0 0 .5rem;
            color: #fff;
            animation: slideDown .8s ease-out;
        }

        .esk-switch-sub {
            color: rgba(232, 255, 251, .62);
            margin: 0 0 2.6rem;
            font-size: 1rem;
            animation: slideDown .9s ease-out;
        }

        .profile-list {
            display: flex;
            gap: 1.6rem;
            flex-wrap: wrap;
            justify-content: center;
            opacity: 0;
            transform: scale(.9);
            animation: scaleUp .8s ease-out forwards;
            animation-delay: .35s;
        }

        .profile-card {
            text-align: center;
            cursor: pointer;
            padding: 30px 26px;
            min-width: 190px;
            background: rgba(255, 255, 255, .05);
            border: 1px solid rgba(255, 255, 255, .1);
            border-radius: 22px;
            -webkit-backdrop-filter: blur(10px);
            backdrop-filter: blur(10px);
            transition: transform .25s ease, box-shadow .25s ease, border-color .25s ease, background .25s ease;
        }

        .profile-card:hover {
            transform: translateY(-8px);
            border-color: rgba(45, 212, 191, .55);
            background: rgba(13, 148, 136, .12);
            box-shadow: 0 24px 50px -18px rgba(13, 148, 136, .55);
        }

        .profile-avatar {
            width: 96px;
            height: 96px;
            border-radius: 50%;
            background: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto .95rem;
            overflow: hidden;
            box-shadow: 0 0 0 6px rgba(45, 212, 191, .12), 0 10px 24px rgba(0, 0, 0, .35);
            transition: box-shadow .25s ease;
        }
        .profile-card:hover .profile-avatar { box-shadow: 0 0 0 6px rgba(45, 212, 191, .3), 0 12px 28px rgba(0, 0, 0, .45); }
        .profile-avatar img { width: 64px !important; height: 64px; object-fit: contain; }

        .profile-card p {
            color: #fff;
            margin: 0;
            font-weight: 600;
            font-size: 1.05rem;
            letter-spacing: .2px;
        }

        .portal-button-container { margin-top: 2.6rem; position: relative; z-index: 1; }

        .manage-profiles-btn {
            background: linear-gradient(135deg, #14b8a6, #0f766e);
            border: none;
            color: #fff;
            padding: .72rem 2.3rem;
            border-radius: 999px;
            font-weight: 600;
            font-size: .98rem;
            cursor: pointer;
            box-shadow: 0 12px 26px -8px rgba(13, 148, 136, .65);
            transition: transform .15s ease, filter .15s ease;
            animation: fadeIn 1s ease-out .9s backwards;
        }
        .manage-profiles-btn:hover { transform: translateY(-2px); filter: brightness(1.07); }

        @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
        @keyframes slideDown { from { transform: translateY(-30px); opacity: 0; } to { transform: translateY(0); opacity: 1; } }
        @keyframes scaleUp { from { opacity: 0; transform: scale(.9); } to { opacity: 1; transform: scale(1); } }
        @keyframes eskPulse { 0%, 100% { transform: scale(1); } 50% { transform: scale(1.08); } }

        /* Loading overlay (teal, eksklusif) */
        .loading-overlay {
            position: fixed;
            inset: 0;
            width: 100%;
            height: 100%;
            background: radial-gradient(700px 500px at 50% 40%, rgba(13, 148, 136, .25) 0%, transparent 60%),
                        linear-gradient(135deg, #03201c, #052b26);
            display: none;
            z-index: 9999;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            color: #fff;
            opacity: 1;
            transition: opacity .5s ease-out;
        }
        .loading-overlay.hidden { opacity: 0; pointer-events: none; }
        .loading-overlay > div { width: 280px; text-align: center; }
        .loading-overlay > div::before {
            content: "";
            display: block;
            width: 72px;
            height: 72px;
            margin: 0 auto 24px;
            background: url('<?= Url::base(true) ?>/lightapp/assets/images/bappeda.png') center/contain no-repeat;
            filter: drop-shadow(0 8px 16px rgba(0, 0, 0, .45));
            animation: eskPulse 1.6s ease-in-out infinite;
        }
        .loading-overlay p { margin-top: 16px; font-size: 1rem; color: rgba(232, 255, 251, .85); font-weight: 500; }
        .progress-bar {
            width: 0%;
            height: 6px;
            border-radius: 999px;
            background: linear-gradient(90deg, #2dd4bf, #0d9488);
            box-shadow: 0 0 14px rgba(45, 212, 191, .6);
            transition: width 1s linear;
        }
    </style>
    <?php $this->head() ?>
</head>

<body>
    <?php $this->beginBody() ?>

    <?= $content ?>

    <?php $this->endBody() ?>
    <script>
        // Function untuk menghandle animasi dan redirect
        function handleLoading(url) {
            const overlay = document.getElementById('loadingOverlay');
            const progressBar = document.getElementById('progressBar');
            const loadingText = document.getElementById('loadingText');
            const profileContainer = document.querySelector('.profile-container');

            // Sembunyikan konten utama (profile container)
            profileContainer.style.display = 'none';

            // Tampilkan overlay
            overlay.style.display = 'flex';
            progressBar.style.width = '0%'; // Reset progress bar
            loadingText.textContent = 'Memuat dashboard...';

            let progress = 0;
            const interval = setInterval(() => {
                progress += 10;
                progressBar.style.width = progress + '%';
                if (progress === 100) {
                    clearInterval(interval);
                    overlay.classList.add('hidden'); // Mulai fade out
                    window.location.href = url; // Redirect ke URL setelah fade-out selesai
                }
            }, 100); // Update progress setiap 100ms
        }

        // Tambahkan event listener pada semua link
        document.querySelectorAll('.profile-card a').forEach((link) => {
            link.addEventListener('click', (e) => {
                e.preventDefault(); // Cegah navigasi default
                const url = e.currentTarget.href; // Ambil URL tujuan
                handleLoading(url); // Mulai animasi loading
            });
        });
    </script>
</body>

</html>
<?php $this->endPage(); ?>
