<?php

/** @var \yii\web\View $this */

use frontend\assets\MainPortalAsset;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;

MainPortalAsset::register($this);

$this->title = 'e-SAKIP Deli Serdang';

// Register SweetAlert2
$this->registerJsFile('https://cdn.jsdelivr.net/npm/sweetalert2@11');

$script = <<< JS
    // 1. Logic Tampilkan/Sembunyikan Dropdown SKPD
    $('#target-page-dropdown').on('change', function() {
        if ($(this).val() === 'portal-publik-tabulasi') {
            $('#skpd-filter-container').slideDown();
        } else {
            $('#skpd-filter-container').slideUp();
            $('#skpd-select').val('');
        }
    });

    // 2. Logic Submit dengan Overlay Loading
    $('form').on('submit', function(e) {
        var targetPage = $('#target-page-dropdown').val();
        var skpdValue = $('#skpd-select').val();

        // Mencegah "Semua SKPD" jika halaman Tabulasi dipilih
        if (targetPage === 'portal-publik-tabulasi' && !skpdValue) {
            e.preventDefault();
            Swal.fire({
                icon: 'warning',
                title: 'Perhatian',
                text: 'Untuk halaman Tabulasi, Anda wajib memilih satu Perangkat Daerah.',
                confirmButtonColor: '#0051c4'
            });
            return false;
        }

        // Tampilkan Loading Overlay
        Swal.fire({
            title: 'Memproses Data...',
            text: 'Harap tunggu sebentar, sistem sedang menarik data dari Redis.',
            allowOutsideClick: false,
            showConfirmButton: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });
    });
JS;
$this->registerJs($script);
?>

<div class="esk-portal">
    <section class="esk-portal-section" style="padding-top: 44px;">

        <div class="esk-section-head" style="margin-top: 16px;">
            <span class="eyebrow">Layanan Publik</span>
            <h2>e-SAKIP Publik</h2>
            <p>Telusuri data kinerja Perangkat Daerah secara terbuka untuk masyarakat</p>
        </div>

        <!-- Menu publik (akses cepat) -->
        <div class="esk-pub-grid" style="margin-bottom: 40px;">
            <a class="esk-pub-card" href="<?= Url::to(['/site/portal-publik-tabulasi']) ?>">
                <span class="esk-pub-ic"><i class="bi bi-table"></i></span>
                <span class="t">Tabulasi</span>
                <span class="s">Tabulasi capaian sasaran per SKPD</span>
            </a>
            <a class="esk-pub-card" href="<?= Url::to(['/site/portal-publik-perencanaan']) ?>">
                <span class="esk-pub-ic"><i class="bi bi-diagram-3"></i></span>
                <span class="t">Perencanaan</span>
                <span class="s">Cascading perencanaan kinerja</span>
            </a>
            <a class="esk-pub-card" href="<?= Url::to(['/site/portal-publik-capkin']) ?>">
                <span class="esk-pub-ic"><i class="bi bi-graph-up-arrow"></i></span>
                <span class="t">Capaian Kinerja</span>
                <span class="s">Realisasi &amp; capaian indikator</span>
            </a>
            <a class="esk-pub-card" href="<?= Url::to(['/site/portal-publik-evaluasi-renja']) ?>">
                <span class="esk-pub-ic"><i class="bi bi-clipboard-data"></i></span>
                <span class="t">Evaluasi Renja</span>
                <span class="s">Hasil evaluasi Rencana Kerja</span>
            </a>
        </div>

        <!-- Filter data publik -->
        <div class="esk-filter-card">
            <div class="esk-filter-head"><i class="bi bi-funnel-fill"></i> Tampilkan Data Publik</div>
            <div class="esk-filter-body">
                <?= Html::beginForm(['site/portal-publik'], 'get'); ?>

                <div class="form-group mb-3">
                    <?= Html::label('Periode', 'refperiode_id', ['class' => 'form-label']); ?>
                    <?= Html::dropDownList(
                        'refperiode_id',
                        $selectedPeriodId,
                        ArrayHelper::map($periodeList, 'refperiode_id', 'periode'),
                        ['class' => 'form-control']
                    ); ?>
                </div>

                <div class="form-group mb-3">
                    <?= Html::label('Halaman Tujuan', 'target_page', ['class' => 'form-label']); ?>
                    <?= Html::dropDownList(
                        'target_page',
                        null,
                        [
                            'portal-publik-tabulasi' => 'Tabulasi',
                            'portal-publik-perencanaan' => 'Perencanaan',
                            'portal-publik-capkin' => 'Capkin',
                            'portal-publik-evaluasi-renja' => 'Evaluasi Renja'
                        ],
                        [
                            'id' => 'target-page-dropdown',
                            'class' => 'form-control',
                            'prompt' => 'Pilih Halaman',
                            'required' => true
                        ]
                    ); ?>
                </div>

                <div id="skpd-filter-container" class="form-group mb-3" style="display: none;">
                    <?= Html::label('Pilih Perangkat Daerah (SKPD)', 'refskpd_id', ['class' => 'form-label']); ?>
                    <?= Html::dropDownList(
                        'refskpd_id',
                        null,
                        ArrayHelper::map($skpdList, 'refskpd_id', 'nama_skpd'),
                        [
                            'id' => 'skpd-select',
                            'class' => 'form-control',
                            'prompt' => '- Silahkan Pilih SKPD -'
                        ]
                    ); ?>
                </div>

                <div class="form-group mt-4">
                    <?= Html::submitButton('<i class="bi bi-search"></i> Tampilkan', ['class' => 'esk-filter-btn']); ?>
                </div>

                <?= Html::endForm(); ?>
            </div>
        </div>

    </section>
</div>
