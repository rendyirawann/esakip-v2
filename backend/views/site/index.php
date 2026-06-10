<?php

use yii\helpers\Url;
use yii\helpers\Html;
use backend\models\User;

/** @var yii\web\View $this */

$this->title = 'Aplikasi Infrastruktur dan Kewilayahan';

// --- Ringkasan (dihitung dari data yang sudah ada, tidak mengubah query) ---
$totalAttempt = count($attempts);
$successCount = 0;
$usernames = [];
foreach ($attempts as $a) {
    if ($a->user_isonline == 'T') {
        $successCount++;
    }
    if (!empty($a->username)) {
        $usernames[$a->username] = true;
    }
}
$failedCount = $totalAttempt - $successCount;
$uniqueUser = count($usernames);
$successRate = $totalAttempt > 0 ? round(($successCount / $totalAttempt) * 100) : 0;

// CSS dashboard didaftarkan ke <head> agar TIDAK terjadi flash tanpa gaya (FOUC) saat load.
$this->registerCss(<<<'CSS'
.esk-dash { font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif; }

/* ---------- Hero ---------- */
.esk-hero {
    position: relative; overflow: hidden; border-radius: 20px; padding: 30px 34px;
    margin-bottom: 24px; color: #eaf1ff; display: flex; align-items: center;
    justify-content: space-between; gap: 20px;
    background: linear-gradient(135deg, #1e3a8a 0%, #2563eb 55%, #1d4ed8 100%);
    box-shadow: 0 18px 40px -18px rgba(37, 99, 235, .6);
}
.esk-hero-eyebrow {
    display: inline-block; font-size: 12px; font-weight: 600; letter-spacing: .5px;
    text-transform: uppercase; background: rgba(255,255,255,.16); padding: 4px 12px; border-radius: 999px;
}
.esk-hero-text h2 { font-size: 26px; font-weight: 700; margin: 12px 0 8px; }
.esk-hero-text p { margin: 0; font-size: 14px; color: rgba(234,241,255,.82); line-height: 1.6; }
.esk-hero-logo {
    flex: 0 0 auto; width: 92px; height: 92px; border-radius: 20px;
    background: rgba(255,255,255,.15); border: 1px solid rgba(255,255,255,.25);
    display: flex; align-items: center; justify-content: center; backdrop-filter: blur(6px);
    position: relative; z-index: 1;
}
.esk-hero-logo img { width: 64px; height: 64px; object-fit: contain; }
.esk-hero-blob {
    position: absolute; width: 320px; height: 320px; border-radius: 50%;
    right: -60px; top: -120px; background: rgba(255,255,255,.08); pointer-events: none;
}

/* ---------- Stat cards ---------- */
.esk-stats { margin-bottom: 8px; }
.esk-stat {
    background: #fff; border-radius: 16px; padding: 20px; display: flex; align-items: center;
    gap: 16px; height: 100%; border: 1px solid #eef2f7;
    box-shadow: 0 8px 24px -16px rgba(15, 23, 42, .25);
    transition: transform .18s ease, box-shadow .18s ease;
}
.esk-stat:hover { transform: translateY(-3px); box-shadow: 0 16px 30px -16px rgba(15, 23, 42, .35); }
.esk-stat-ic {
    flex: 0 0 52px; width: 52px; height: 52px; border-radius: 14px;
    display: flex; align-items: center; justify-content: center; font-size: 24px; color: #fff;
}
.esk-ic-blue  { background: linear-gradient(135deg, #3b82f6, #2563eb); }
.esk-ic-green { background: linear-gradient(135deg, #22c55e, #16a34a); }
.esk-ic-red   { background: linear-gradient(135deg, #f87171, #dc2626); }
.esk-ic-amber { background: linear-gradient(135deg, #fbbf24, #f59e0b); }
.esk-stat-label { font-size: 12.5px; color: #64748b; font-weight: 500; }
.esk-stat-value { font-size: 24px; font-weight: 700; color: #0f172a; margin: 2px 0 0; line-height: 1.1; }
.esk-stat-sub { font-size: 11.5px; color: #16a34a; font-weight: 600; }

/* ---------- Panel / table ---------- */
.esk-panel {
    background: #fff; border-radius: 18px; padding: 22px 24px; margin-top: 16px;
    border: 1px solid #eef2f7; box-shadow: 0 8px 24px -16px rgba(15, 23, 42, .25);
}
.esk-panel-head { display: flex; align-items: center; justify-content: space-between; margin-bottom: 18px; }
.esk-panel-title { font-size: 17px; font-weight: 700; color: #0f172a; margin: 0; display: flex; align-items: center; gap: 8px; }
.esk-panel-title i { color: #2563eb; }
.esk-panel-sub { font-size: 13px; color: #64748b; margin: 4px 0 0; }

.esk-table { font-size: 13px !important; }
.esk-table thead th {
    background: #f8fafc; color: #475569; font-weight: 600; font-size: 12px;
    text-transform: uppercase; letter-spacing: .3px; border-bottom: 2px solid #e2e8f0;
}
.esk-table tbody td { vertical-align: middle; color: #334155; }
.esk-table tbody tr:hover { background: #f8fbff; }
.esk-hash {
    display: inline-block; max-width: 360px; overflow: hidden; text-overflow: ellipsis;
    white-space: nowrap; vertical-align: middle; font-family: monospace; font-size: 12px; color: #64748b;
}
.esk-badge { display: inline-flex; align-items: center; gap: 5px; padding: 4px 12px; border-radius: 999px; font-size: 12px; font-weight: 600; }
.esk-badge::before { content: ""; width: 7px; height: 7px; border-radius: 50%; background: currentColor; }
.esk-badge-success { color: #16a34a; background: rgba(22, 163, 74, .12); }
.esk-badge-failed  { color: #dc2626; background: rgba(220, 38, 38, .12); }

@media (max-width: 575px) {
    .esk-hero { flex-direction: column; text-align: center; }
    .esk-hero-logo { margin: 0 auto; }
}
CSS);
?>
<!-- [ Main Content ] start -->
<div class="pc-container">
  <div class="pc-content esk-dash">

    <!-- [ breadcrumb ] start -->
    <div class="page-header">
      <div class="page-block">
        <div class="row align-items-center">
          <div class="col-md-12">
            <ul class="breadcrumb">
              <li class="breadcrumb-item"><a href="<?= Url::to(['/site/index']) ?>">Dashboard</a></li>
              <li class="breadcrumb-item" aria-current="page">Home</li>
            </ul>
          </div>
        </div>
      </div>
    </div>
    <!-- [ breadcrumb ] end -->

    <!-- ============ HERO ============ -->
    <div class="esk-hero">
      <div class="esk-hero-text">
        <span class="esk-hero-eyebrow">Dashboard Aplikasi eSakip</span>
        <h2>Selamat datang kembali &#128075;</h2>
        <p>Sistem Akuntabilitas Kinerja Instansi Pemerintah<br>Bappedalitbang Kabupaten Deli Serdang</p>
      </div>
      <div class="esk-hero-logo">
        <img src="<?= Url::base(true) ?>/lightapp/assets/images/bappeda.png" alt="Logo Bappeda">
      </div>
      <span class="esk-hero-blob"></span>
    </div>

    <!-- ============ STAT CARDS ============ -->
    <div class="row g-3 esk-stats">
      <div class="col-xl-3 col-md-6">
        <div class="esk-stat">
          <div class="esk-stat-ic esk-ic-blue"><i class="ti ti-login"></i></div>
          <div>
            <span class="esk-stat-label">Total Percobaan Login</span>
            <h3 class="esk-stat-value"><?= number_format($totalAttempt, 0, ',', '.') ?></h3>
          </div>
        </div>
      </div>
      <div class="col-xl-3 col-md-6">
        <div class="esk-stat">
          <div class="esk-stat-ic esk-ic-green"><i class="ti ti-circle-check"></i></div>
          <div>
            <span class="esk-stat-label">Login Berhasil</span>
            <h3 class="esk-stat-value"><?= number_format($successCount, 0, ',', '.') ?></h3>
          </div>
        </div>
      </div>
      <div class="col-xl-3 col-md-6">
        <div class="esk-stat">
          <div class="esk-stat-ic esk-ic-red"><i class="ti ti-circle-x"></i></div>
          <div>
            <span class="esk-stat-label">Login Gagal</span>
            <h3 class="esk-stat-value"><?= number_format($failedCount, 0, ',', '.') ?></h3>
          </div>
        </div>
      </div>
      <div class="col-xl-3 col-md-6">
        <div class="esk-stat">
          <div class="esk-stat-ic esk-ic-amber"><i class="ti ti-users"></i></div>
          <div>
            <span class="esk-stat-label">Pengguna Unik</span>
            <h3 class="esk-stat-value"><?= number_format($uniqueUser, 0, ',', '.') ?></h3>
            <small class="esk-stat-sub"><?= $successRate ?>% tingkat keberhasilan</small>
          </div>
        </div>
      </div>
    </div>

    <!-- ============ TABLE ============ -->
    <div class="row">
      <div class="col-lg-12">
        <div class="esk-panel">
          <div class="esk-panel-head">
            <div>
              <h5 class="esk-panel-title"><i class="ti ti-shield-lock"></i> Riwayat Percobaan Login</h5>
              <p class="esk-panel-sub">Pantau aktivitas masuk pengguna ke dalam sistem.</p>
            </div>
          </div>
          <div class="dt-responsive table-responsive">
            <table id="table-style-hover" class="table table-hover table-bordered nowrap esk-table" style="width:100%;">
              <thead>
                <tr>
                  <th>IP Address</th>
                  <th>Username</th>
                  <th>Password (Hash)</th>
                  <th>Status</th>
                  <th>Date/Time</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($attempts as $attempt): ?>
                  <tr>
                    <td><?= $attempt->user_lastloginip ?></td>
                    <td><?= $attempt->username ?></td>
                    <td><span class="esk-hash"><?= $attempt->password_hash ?></span></td>
                    <td>
                      <?php if ($attempt->user_isonline == 'T'): ?>
                        <span class="esk-badge esk-badge-success">Successful</span>
                      <?php else: ?>
                        <span class="esk-badge esk-badge-failed">Failed</span>
                      <?php endif; ?>
                    </td>
                    <td><?= $attempt->user_lastlogin ?></td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>

  </div>
  <!-- [ Main Content ] end -->
</div>
<!-- [ Main Content ] end -->
