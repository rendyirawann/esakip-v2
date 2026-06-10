<?php

/** @var yii\web\View $this */
/** @var yii\bootstrap5\ActiveForm $form */
/** @var \common\models\LoginForm $model */

use yii\bootstrap5\ActiveForm;
use yii\bootstrap5\Html;
use yii\helpers\Url;

$this->title = 'Login';
?>

<div class="esk-auth">
    <!-- decorative background blobs -->
    <span class="esk-blob esk-blob-1"></span>
    <span class="esk-blob esk-blob-2"></span>
    <span class="esk-blob esk-blob-3"></span>

    <div class="esk-card">
        <!-- ============ BRAND PANEL ============ -->
        <div class="esk-brand">
            <div class="esk-brand-top">
                <div class="esk-logo-badge">
                    <img src="<?= Url::base(true) ?>/lightapp/assets/images/bappeda.png" alt="Logo Bappeda" />
                </div>
                <div class="esk-brand-title">
                    <h1>eSakip <span class="esk-ver">v2.0</span></h1>
                    <p>Sistem Akuntabilitas Kinerja Instansi Pemerintah</p>
                </div>
            </div>

            <ul class="esk-features">
                <li>
                    <span class="esk-feat-ic"><i class="ti ti-layout-grid"></i></span>
                    <div>
                        <strong>Perencanaan Terintegrasi</strong>
                        <small>Renstra, Renja, dan Cascading dalam satu sistem</small>
                    </div>
                </li>
                <li>
                    <span class="esk-feat-ic"><i class="ti ti-chart-arcs"></i></span>
                    <div>
                        <strong>Evaluasi Kinerja</strong>
                        <small>Pengukuran capaian yang akurat &amp; transparan</small>
                    </div>
                </li>
                <li>
                    <span class="esk-feat-ic"><i class="ti ti-report-analytics"></i></span>
                    <div>
                        <strong>Pelaporan Real-time</strong>
                        <small>Monitoring data secara cepat dan terpusat</small>
                    </div>
                </li>
            </ul>

            <div class="esk-brand-footer">
                eSakip. Sistem Akuntabilitas Kinerja Instansi Pemerintah &copy; <?= date('Y') ?>
                <a href="https://bappedalitbang.deliserdangkab.go.id" target="_blank">Bappedalitbang Deli Serdang</a>
            </div>
        </div>

        <!-- ============ FORM PANEL ============ -->
        <div class="esk-form-wrap">
            <div class="esk-mobile-logo">
                <img src="<?= Url::base(true) ?>/lightapp/assets/images/bappeda.png" alt="Logo Bappeda" />
            </div>

            <span class="esk-welcome">Selamat Datang &#128075;</span>
            <h2 class="esk-form-title">Aplikasi eSakip <span>(ADMIN)</span></h2>
            <p class="esk-form-sub">Masukkan username dan password untuk masuk ke sistem.</p>

            <?php if (Yii::$app->session->hasFlash('success')) : ?>
                <div class="alert alert-success esk-alert"><?= Yii::$app->session->getFlash('success') ?></div>
            <?php endif; ?>
            <?php if (Yii::$app->session->hasFlash('error')) : ?>
                <div class="alert alert-danger esk-alert"><?= Yii::$app->session->getFlash('error') ?></div>
            <?php endif; ?>

            <form class="esk-form" method="post" action="<?= Url::to(['/site/login']) ?>">
                <?= Html::hiddenInput(Yii::$app->request->csrfParam, Yii::$app->request->csrfToken) ?>

                <div class="esk-field">
                    <label class="esk-label">Username</label>
                    <div class="esk-input">
                        <span class="esk-input-ic"><i class="ti ti-user"></i></span>
                        <input type="text" class="esk-control" name="LoginForm[username]" placeholder="Masukkan username" autocomplete="username" autofocus>
                    </div>
                </div>

                <div class="esk-field">
                    <label class="esk-label">Password</label>
                    <div class="esk-input">
                        <span class="esk-input-ic"><i class="ti ti-lock"></i></span>
                        <input type="password" class="esk-control" id="password-input" name="LoginForm[password]" placeholder="Masukkan password" autocomplete="current-password">
                        <div class="esk-eye" id="toggle-password" title="Tampilkan / sembunyikan password">
                            <span class="eye-icon">&#128065; <small class="text-muted">Show</small></span>
                        </div>
                    </div>
                </div>

                <div class="esk-row">
                    <label class="esk-check">
                        <input class="form-check-input input-primary" type="checkbox" id="customCheckc1" checked="">
                        <span>Ingat saya</span>
                    </label>
                    <!-- <a href="#" class="esk-forgot">Lupa password?</a> -->
                </div>

                <button type="submit" class="esk-btn">
                    <i class="ti ti-login"></i> Masuk
                </button>
            </form>

            <p class="esk-form-foot">Hak akses terbatas. Hubungi admin Bappedalitbang bila mengalami kendala.</p>
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
       eSakip — Login redesign (scoped, tanpa mengubah fungsi)
       ========================================================= */
    html, body { overflow-x: hidden; max-width: 100%; }

    .esk-auth {
        --esk-primary: #2563eb;
        --esk-primary-dark: #1d4ed8;
        --esk-ink: #0f172a;
        --esk-muted: #64748b;
        position: relative;
        min-height: 100vh;
        width: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 24px;
        font-family: 'Public Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
        background: radial-gradient(1200px 600px at 12% 18%, #1e3a8a 0%, transparent 55%),
                    radial-gradient(900px 500px at 90% 90%, #1e40af 0%, transparent 50%),
                    linear-gradient(135deg, #0b1220 0%, #0f172a 45%, #111c3a 100%);
        overflow: hidden;
    }

    .esk-blob {
        position: absolute;
        border-radius: 50%;
        filter: blur(60px);
        opacity: .45;
        z-index: 0;
        pointer-events: none;
    }
    .esk-blob-1 { width: 380px; height: 380px; top: -90px; left: -80px;  background: #3b82f6; }
    .esk-blob-2 { width: 320px; height: 320px; bottom: -100px; right: -60px; background: #6366f1; opacity:.35; }
    .esk-blob-3 { width: 220px; height: 220px; top: 40%; left: 45%; background: #f59e0b; opacity:.18; }

    .esk-card {
        position: relative;
        z-index: 2;
        display: flex;
        width: min(960px, 100%);
        min-height: 560px;
        background: #ffffff;
        border-radius: 24px;
        overflow: hidden;
        box-shadow: 0 30px 70px -20px rgba(2, 6, 23, .65);
        animation: eskFadeUp .5s ease both;
    }
    @keyframes eskFadeUp {
        from { opacity: 0; transform: translateY(18px); }
        to   { opacity: 1; transform: translateY(0); }
    }

    /* ---------- Brand panel ---------- */
    .esk-brand {
        position: relative;
        flex: 1 1 45%;
        padding: 44px 40px;
        color: #eaf1ff;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        background: linear-gradient(160deg, #1e3a8a 0%, #2563eb 60%, #1d4ed8 100%);
        overflow: hidden;
    }
    .esk-brand::after {
        content: "";
        position: absolute;
        width: 260px; height: 260px;
        right: -90px; top: -90px;
        background: rgba(255,255,255,.08);
        border-radius: 50%;
    }
    .esk-brand::before {
        content: "";
        position: absolute;
        width: 180px; height: 180px;
        left: -60px; bottom: 30px;
        background: rgba(255,255,255,.06);
        border-radius: 50%;
    }
    .esk-brand-top { position: relative; z-index: 1; display: flex; align-items: center; gap: 16px; }
    .esk-logo-badge {
        width: 64px; height: 64px;
        flex: 0 0 64px;
        border-radius: 18px;
        background: rgba(255,255,255,.16);
        backdrop-filter: blur(6px);
        border: 1px solid rgba(255,255,255,.25);
        display: flex; align-items: center; justify-content: center;
    }
    .esk-logo-badge img { width: 42px; height: 42px; object-fit: contain; }
    .esk-brand-title h1 { font-size: 30px; font-weight: 700; margin: 0; line-height: 1; }
    .esk-ver {
        font-size: 11px; font-weight: 600; vertical-align: middle;
        background: #f59e0b; color: #1f2937;
        padding: 2px 8px; border-radius: 999px; margin-left: 6px;
    }
    .esk-brand-title p { margin: 6px 0 0; font-size: 13px; color: rgba(234,241,255,.8); max-width: 260px; }

    .esk-features { list-style: none; margin: 28px 0; padding: 0; position: relative; z-index: 1; }
    .esk-features li { display: flex; align-items: flex-start; gap: 14px; margin-bottom: 18px; }
    .esk-feat-ic {
        flex: 0 0 40px; width: 40px; height: 40px;
        border-radius: 12px;
        background: rgba(255,255,255,.14);
        display: flex; align-items: center; justify-content: center;
        font-size: 20px; color: #fff;
    }
    .esk-features strong { display: block; font-size: 14px; font-weight: 600; }
    .esk-features small { color: rgba(234,241,255,.72); font-size: 12px; }

    .esk-brand-footer { position: relative; z-index: 1; font-size: 11.5px; color: rgba(234,241,255,.7); line-height: 1.5; }
    .esk-brand-footer a { color: #fff; font-weight: 600; text-decoration: none; }
    .esk-brand-footer a:hover { text-decoration: underline; }

    /* ---------- Form panel ---------- */
    .esk-form-wrap {
        flex: 1 1 55%;
        padding: 48px 46px;
        display: flex;
        flex-direction: column;
        justify-content: center;
    }
    .esk-mobile-logo { display: none; text-align: center; margin-bottom: 14px; }
    .esk-mobile-logo img { width: 56px; height: 56px; }

    .esk-welcome { font-size: 13px; color: var(--esk-muted); font-weight: 600; }
    .esk-form-title { font-size: 24px; font-weight: 700; color: var(--esk-ink); margin: 6px 0 4px; }
    .esk-form-title span { color: var(--esk-primary); }
    .esk-form-sub { font-size: 13.5px; color: var(--esk-muted); margin: 0 0 26px; }

    .esk-alert { font-size: 13px; border-radius: 12px; padding: 10px 14px; }

    .esk-field { margin-bottom: 18px; }
    .esk-label { display: block; font-size: 13px; font-weight: 600; color: #334155; margin-bottom: 7px; }
    .esk-input { position: relative; }
    .esk-input-ic {
        position: absolute; left: 14px; top: 50%; transform: translateY(-50%);
        color: #94a3b8; font-size: 18px; pointer-events: none;
    }
    .esk-control {
        width: 100%;
        height: 50px;
        border: 1.5px solid #e2e8f0;
        border-radius: 14px;
        background: #f8fafc;
        padding: 0 46px 0 44px;
        font-size: 14px;
        color: var(--esk-ink);
        outline: none;
        transition: border-color .18s, box-shadow .18s, background .18s;
    }
    .esk-control::placeholder { color: #94a3b8; }
    .esk-control:focus {
        border-color: var(--esk-primary);
        background: #fff;
        box-shadow: 0 0 0 4px rgba(37, 99, 235, .14);
    }
    .esk-eye {
        position: absolute; right: 12px; top: 50%; transform: translateY(-50%);
        cursor: pointer; user-select: none; font-size: 14px;
        display: flex; align-items: center; gap: 4px;
        color: #64748b;
    }
    .esk-eye small { font-size: 11.5px; }

    .esk-row { display: flex; align-items: center; justify-content: space-between; margin: 4px 0 22px; }
    .esk-check { display: flex; align-items: center; gap: 8px; font-size: 13px; color: #475569; cursor: pointer; margin: 0; }
    .esk-check input { width: 16px; height: 16px; cursor: pointer; }
    .esk-forgot { font-size: 13px; color: var(--esk-primary); text-decoration: none; font-weight: 600; }

    .esk-btn {
        width: 100%;
        height: 52px;
        border: none;
        border-radius: 14px;
        color: #fff;
        font-size: 15px;
        font-weight: 600;
        cursor: pointer;
        display: flex; align-items: center; justify-content: center; gap: 8px;
        background: linear-gradient(135deg, var(--esk-primary) 0%, var(--esk-primary-dark) 100%);
        box-shadow: 0 12px 24px -8px rgba(37, 99, 235, .6);
        transition: transform .15s ease, box-shadow .15s ease, filter .15s ease;
    }
    .esk-btn:hover { transform: translateY(-2px); filter: brightness(1.05); box-shadow: 0 16px 30px -8px rgba(37, 99, 235, .7); }
    .esk-btn:active { transform: translateY(0); }
    .esk-btn i { font-size: 18px; }

    .esk-form-foot { margin: 22px 0 0; font-size: 12px; color: #94a3b8; text-align: center; }

    /* ---------- Responsive ---------- */
    @media (max-width: 860px) {
        .esk-card { flex-direction: column; min-height: 0; width: min(440px, 100%); }
        .esk-brand { display: none; }
        .esk-form-wrap { padding: 40px 30px; }
        .esk-mobile-logo { display: block; }
    }

    /* ---------- Modal notifikasi (dipertahankan) ---------- */
    .modal {
        display: none; position: fixed; z-index: 10000; left: 0; top: 0;
        width: 100%; height: 100%; background-color: rgba(15, 23, 42, .6);
        justify-content: center; align-items: center; animation: fadeIn .3s ease-in-out;
        backdrop-filter: blur(2px);
    }
    .modal-content {
        background-color: #fff; padding: 28px; width: 360px; text-align: center;
        border-radius: 18px; box-shadow: 0 20px 50px rgba(2, 6, 23, .35);
        animation: slideIn .3s ease-in-out; position: relative;
    }
    .modal-header { font-size: 20px; font-weight: 700; margin-bottom: 8px; }
    .modal-icon { font-size: 42px; margin-bottom: 6px; }
    .success-icon { color: #16a34a; }
    .error-icon { color: #dc2626; }
    #redirectMessage { font-size: 13px; color: #64748b; margin-top: 8px; }
    .modal button {
        margin-top: 16px; padding: 11px 26px; border: none;
        background: linear-gradient(135deg, #2563eb, #1d4ed8); color: #fff;
        font-size: 15px; font-weight: 600; border-radius: 12px; cursor: pointer; transition: filter .2s;
    }
    .modal button:hover { filter: brightness(1.07); }
    @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
    @keyframes slideIn { from { transform: translateY(-24px); opacity: 0; } to { transform: translateY(0); opacity: 1; } }
</style>
