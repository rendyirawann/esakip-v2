<?php

/** @var yii\web\View $this */
/** @var yii\bootstrap5\ActiveForm $form */
/** @var \common\models\LoginForm $model */

use yii\bootstrap5\ActiveForm;
use yii\bootstrap5\Html;
use yii\helpers\Url;

$this->title = 'Login SKPD';
?>

<div class="eskf-auth">
    <span class="eskf-blob eskf-blob-1"></span>
    <span class="eskf-blob eskf-blob-2"></span>
    <span class="eskf-blob eskf-blob-3"></span>

    <div class="eskf-card">
        <!-- ============ BRAND PANEL ============ -->
        <div class="eskf-brand">
            <div class="eskf-brand-top">
                <div class="eskf-logo-badge">
                    <img src="<?= Url::base(true) ?>/lightapp/assets/images/bappeda.png" alt="Logo Bappeda" />
                </div>
                <div class="eskf-brand-title">
                    <h1>eSakip <span class="eskf-ver">SKPD</span></h1>
                    <p>Sistem Akuntabilitas Kinerja Instansi Pemerintah</p>
                </div>
            </div>

            <ul class="eskf-features">
                <li>
                    <span class="eskf-feat-ic"><i class="ti ti-target-arrow"></i></span>
                    <div>
                        <strong>Renstra &amp; Perencanaan</strong>
                        <small>Kelola Renstra, RKT, PK, dan Cascading SKPD</small>
                    </div>
                </li>
                <li>
                    <span class="eskf-feat-ic"><i class="ti ti-chart-bar"></i></span>
                    <div>
                        <strong>Capaian Kinerja</strong>
                        <small>Pengukuran realisasi &amp; capaian triwulan</small>
                    </div>
                </li>
                <li>
                    <span class="eskf-feat-ic"><i class="ti ti-files"></i></span>
                    <div>
                        <strong>Dokumen &amp; Laporan</strong>
                        <small>Unduh dokumen perencanaan dan pelaporan</small>
                    </div>
                </li>
            </ul>

            <div class="eskf-brand-footer">
                eSakip. Sistem Akuntabilitas Kinerja Instansi Pemerintah &copy; <?= date('Y') ?>
                <a href="https://bappedalitbang.deliserdangkab.go.id" target="_blank">Bappedalitbang Deli Serdang</a>
            </div>
        </div>

        <!-- ============ FORM PANEL ============ -->
        <div class="eskf-form-wrap">
            <div class="eskf-mobile-logo">
                <img src="<?= Url::base(true) ?>/lightapp/assets/images/bappeda.png" alt="Logo Bappeda" />
            </div>

            <span class="eskf-welcome">Selamat Datang &#128075;</span>
            <h2 class="eskf-form-title">Login <span>SKPD</span></h2>
            <p class="eskf-form-sub">Masuk untuk mengelola data kinerja Perangkat Daerah Anda.</p>

            <?php if (Yii::$app->session->hasFlash('success')) : ?>
                <div class="alert alert-success eskf-alert"><?= Yii::$app->session->getFlash('success') ?></div>
            <?php endif; ?>
            <?php if (Yii::$app->session->hasFlash('error')) : ?>
                <div class="alert alert-danger eskf-alert"><?= Yii::$app->session->getFlash('error') ?></div>
            <?php endif; ?>

            <form class="auth-form eskf-form" method="post" action="<?= Url::to(['/site/login']) ?>">
                <?= Html::hiddenInput(Yii::$app->request->csrfParam, Yii::$app->request->csrfToken) ?>

                <div class="eskf-field">
                    <label class="eskf-label">Username</label>
                    <div class="eskf-input">
                        <span class="eskf-input-ic"><i class="ti ti-user"></i></span>
                        <input type="text" class="eskf-control" id="floatingInput" name="LoginForm[username]" placeholder="Masukkan username" autocomplete="username" autofocus>
                    </div>
                </div>

                <div class="eskf-field">
                    <label class="eskf-label">Password</label>
                    <div class="eskf-input">
                        <span class="eskf-input-ic"><i class="ti ti-lock"></i></span>
                        <input type="password" class="eskf-control" id="password-input" name="LoginForm[password]" placeholder="Masukkan password" autocomplete="current-password">
                        <div class="eskf-eye" id="toggle-password" title="Tampilkan / sembunyikan password">
                            <span class="eye-icon">&#128065; <small class="text-muted">Show</small></span>
                        </div>
                    </div>
                </div>

                <div class="eskf-row">
                    <label class="eskf-check">
                        <input class="form-check-input input-primary" type="checkbox" id="customCheckc1" checked="">
                        <span>Ingat saya</span>
                    </label>
                </div>

                <button type="submit" class="eskf-btn">
                    <i class="ti ti-login"></i> Masuk
                </button>
            </form>

            <div class="eskf-back">
                <a href="<?= Url::to(['/site/portal']) ?>"><i class="ti ti-arrow-left"></i> Kembali ke Portal</a>
            </div>
        </div>
    </div>

    <!-- Modal Notifikasi -->
    <div id="modalNotif" class="modal">
        <div class="modal-content">
            <div id="modalIcon" class="modal-icon"></div>
            <div class="modal-header" id="modalHeader"></div>
            <p id="modalMessage"></p>
            <p id="redirectMessage" style="display: none;">Mengalihkan dalam <span id="countdown">5</span> detik...</p>
            <button onclick="closeModal()">OK</button>
        </div>
    </div>
</div>

<style>
    /* =========================================================
       eSakip FRONTEND — Login redesign (tema teal, beda dari backend)
       Hook fungsi (form, csrf, #password-input, #toggle-password,
       .eye-icon, #modalNotif) dipertahankan.
       ========================================================= */
    .eskf-auth {
        --eskf-primary: #0d9488;
        --eskf-primary-dark: #0f766e;
        --eskf-ink: #0f172a;
        --eskf-muted: #64748b;
        position: relative;
        min-height: 100vh;
        width: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 24px;
        font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
        background: radial-gradient(1200px 600px at 12% 18%, #0f766e 0%, transparent 55%),
                    radial-gradient(900px 500px at 90% 90%, #115e59 0%, transparent 50%),
                    linear-gradient(135deg, #04201d 0%, #052e29 45%, #07382f 100%);
        overflow: hidden;
    }
    html, body { overflow-x: hidden; max-width: 100%; }

    .eskf-blob { position: absolute; border-radius: 50%; filter: blur(60px); opacity: .4; z-index: 0; pointer-events: none; }
    .eskf-blob-1 { width: 380px; height: 380px; top: -90px; left: -80px; background: #14b8a6; }
    .eskf-blob-2 { width: 320px; height: 320px; bottom: -100px; right: -60px; background: #10b981; opacity: .32; }
    .eskf-blob-3 { width: 220px; height: 220px; top: 40%; left: 45%; background: #f59e0b; opacity: .14; }

    .eskf-card {
        position: relative;
        z-index: 2;
        display: flex;
        width: min(960px, 100%);
        min-height: 560px;
        background: #ffffff;
        border-radius: 24px;
        overflow: hidden;
        box-shadow: 0 30px 70px -20px rgba(2, 18, 16, .65);
        animation: eskfFadeUp .5s ease both;
    }
    @keyframes eskfFadeUp { from { opacity: 0; transform: translateY(18px); } to { opacity: 1; transform: translateY(0); } }

    .eskf-brand {
        position: relative;
        flex: 1 1 45%;
        padding: 44px 40px;
        color: #e8fffb;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        background: linear-gradient(160deg, #0f766e 0%, #0d9488 60%, #115e59 100%);
        overflow: hidden;
    }
    .eskf-brand::after { content: ""; position: absolute; width: 260px; height: 260px; right: -90px; top: -90px; background: rgba(255,255,255,.08); border-radius: 50%; }
    .eskf-brand::before { content: ""; position: absolute; width: 180px; height: 180px; left: -60px; bottom: 30px; background: rgba(255,255,255,.06); border-radius: 50%; }
    .eskf-brand-top { position: relative; z-index: 1; display: flex; align-items: center; gap: 16px; }
    .eskf-logo-badge {
        width: 64px; height: 64px; flex: 0 0 64px; border-radius: 18px;
        background: rgba(255,255,255,.16); backdrop-filter: blur(6px); border: 1px solid rgba(255,255,255,.25);
        display: flex; align-items: center; justify-content: center;
    }
    .eskf-logo-badge img { width: 42px; height: 42px; object-fit: contain; }
    .eskf-brand-title h1 { font-size: 30px; font-weight: 700; margin: 0; line-height: 1; }
    .eskf-ver { font-size: 11px; font-weight: 700; vertical-align: middle; background: #f59e0b; color: #1f2937; padding: 2px 9px; border-radius: 999px; margin-left: 6px; letter-spacing: .5px; }
    .eskf-brand-title p { margin: 6px 0 0; font-size: 13px; color: rgba(232,255,251,.82); max-width: 260px; }

    .eskf-features { list-style: none; margin: 28px 0; padding: 0; position: relative; z-index: 1; }
    .eskf-features li { display: flex; align-items: flex-start; gap: 14px; margin-bottom: 18px; }
    .eskf-feat-ic { flex: 0 0 40px; width: 40px; height: 40px; border-radius: 12px; background: rgba(255,255,255,.14); display: flex; align-items: center; justify-content: center; font-size: 20px; color: #fff; }
    .eskf-features strong { display: block; font-size: 14px; font-weight: 600; }
    .eskf-features small { color: rgba(232,255,251,.74); font-size: 12px; }

    .eskf-brand-footer { position: relative; z-index: 1; font-size: 11.5px; color: rgba(232,255,251,.72); line-height: 1.5; }
    .eskf-brand-footer a { color: #fff; font-weight: 600; text-decoration: none; }
    .eskf-brand-footer a:hover { text-decoration: underline; }

    .eskf-form-wrap { flex: 1 1 55%; padding: 48px 46px; display: flex; flex-direction: column; justify-content: center; }
    .eskf-mobile-logo { display: none; text-align: center; margin-bottom: 14px; }
    .eskf-mobile-logo img { width: 56px; height: 56px; }

    .eskf-welcome { font-size: 13px; color: var(--eskf-muted); font-weight: 600; }
    .eskf-form-title { font-size: 24px; font-weight: 700; color: var(--eskf-ink); margin: 6px 0 4px; }
    .eskf-form-title span { color: var(--eskf-primary); }
    .eskf-form-sub { font-size: 13.5px; color: var(--eskf-muted); margin: 0 0 26px; }
    .eskf-alert { font-size: 13px; border-radius: 12px; padding: 10px 14px; }

    .eskf-field { margin-bottom: 18px; }
    .eskf-label { display: block; font-size: 13px; font-weight: 600; color: #334155; margin-bottom: 7px; }
    .eskf-input { position: relative; }
    .eskf-input-ic { position: absolute; left: 14px; top: 50%; transform: translateY(-50%); color: #94a3b8; font-size: 18px; pointer-events: none; }
    .eskf-control {
        width: 100%; height: 50px; border: 1.5px solid #e2e8f0; border-radius: 14px; background: #f8fafc;
        padding: 0 46px 0 44px; font-size: 14px; color: var(--eskf-ink); outline: none;
        transition: border-color .18s, box-shadow .18s, background .18s;
    }
    .eskf-control::placeholder { color: #94a3b8; }
    .eskf-control:focus { border-color: var(--eskf-primary); background: #fff; box-shadow: 0 0 0 4px rgba(13, 148, 136, .14); }
    .eskf-eye { position: absolute; right: 12px; top: 50%; transform: translateY(-50%); cursor: pointer; user-select: none; font-size: 14px; display: flex; align-items: center; gap: 4px; color: #64748b; }
    .eskf-eye small { font-size: 11.5px; }

    .eskf-row { display: flex; align-items: center; justify-content: space-between; margin: 4px 0 22px; }
    .eskf-check { display: flex; align-items: center; gap: 8px; font-size: 13px; color: #475569; cursor: pointer; margin: 0; }
    .eskf-check input { width: 16px; height: 16px; cursor: pointer; }

    .eskf-btn {
        width: 100%; height: 52px; border: none; border-radius: 14px; color: #fff; font-size: 15px; font-weight: 600;
        cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 8px;
        background: linear-gradient(135deg, var(--eskf-primary) 0%, var(--eskf-primary-dark) 100%);
        box-shadow: 0 12px 24px -8px rgba(13, 148, 136, .6);
        transition: transform .15s ease, box-shadow .15s ease, filter .15s ease;
    }
    .eskf-btn:hover { transform: translateY(-2px); filter: brightness(1.05); box-shadow: 0 16px 30px -8px rgba(13, 148, 136, .7); }
    .eskf-btn:active { transform: translateY(0); }
    .eskf-btn i { font-size: 18px; }

    .eskf-back { margin-top: 22px; text-align: center; }
    .eskf-back a { font-size: 13px; color: var(--eskf-muted); text-decoration: none; font-weight: 600; display: inline-flex; align-items: center; gap: 5px; }
    .eskf-back a:hover { color: var(--eskf-primary); }

    @media (max-width: 860px) {
        .eskf-card { flex-direction: column; min-height: 0; width: min(440px, 100%); }
        .eskf-brand { display: none; }
        .eskf-form-wrap { padding: 40px 30px; }
        .eskf-mobile-logo { display: block; }
    }

    /* Modal notifikasi (dipertahankan) */
    .modal { display: none; position: fixed; z-index: 10000; left: 0; top: 0; width: 100%; height: 100%; background-color: rgba(2, 18, 16, .6); justify-content: center; align-items: center; animation: fadeIn .3s ease-in-out; backdrop-filter: blur(2px); }
    .modal-content { background-color: #fff; padding: 28px; width: 360px; text-align: center; border-radius: 18px; box-shadow: 0 20px 50px rgba(2, 18, 16, .35); animation: slideIn .3s ease-in-out; position: relative; }
    .modal-header { font-size: 20px; font-weight: 700; margin-bottom: 8px; }
    .modal-icon { font-size: 42px; margin-bottom: 6px; }
    .success-icon { color: #16a34a; }
    .error-icon { color: #dc2626; }
    #redirectMessage { font-size: 13px; color: #64748b; margin-top: 8px; }
    .modal button { margin-top: 16px; padding: 11px 26px; border: none; background: linear-gradient(135deg, #0d9488, #0f766e); color: #fff; font-size: 15px; font-weight: 600; border-radius: 12px; cursor: pointer; transition: filter .2s; }
    .modal button:hover { filter: brightness(1.07); }
    @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
    @keyframes slideIn { from { transform: translateY(-24px); opacity: 0; } to { transform: translateY(0); opacity: 1; } }
</style>
