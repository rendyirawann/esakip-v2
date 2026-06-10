<?php

/** @var \yii\web\View $this */
/** @var string $content */

use common\widgets\Alert;
use frontend\assets\MainPortalAsset;
use yii\bootstrap5\Breadcrumbs;
use yii\bootstrap5\Html;
use yii\helpers\Url;
use yii\bootstrap5\Nav;
use yii\bootstrap5\NavBar;

MainPortalAsset::register($this);

$this->title = 'e-SAKIP Deli Serdang';

$this->registerCss("
    /* Style untuk container loading yang baru */
    .loading-indicator {
        /* Tidak lagi 'absolute', menjadi elemen blok biasa */
        width: 100%;
        padding: 40px 0; /* Memberi ruang atas-bawah */
        display: flex;
        justify-content: center;
        align-items: center;
        background-color: #f8f9fa; /* Warna latar yang lembut */
        border: 1px solid #dee2e6;
        border-radius: .25rem;
        margin-top: 15px;
    }

    /* Konten di tengah (teks 'Mencari...') */
    .loading-content {
        text-align: center;
        font-weight: bold;
        color: #555;
    }

    /* Class untuk gambar spinner agar berputar */
    .loading-spinner {
        width: 60px;
        height: 60px;
        animation: spin 1.5s linear infinite;
    }

    /* Animasi putaran (Keyframes) */
    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }
");
?>

<div class="container p-2 mt-1">
    <div class="row">

        <div class="col-md-12">

            <!-- Menu list -->

            <div class="justify-content-center">
                <div class="card">
                    <div class="card-header" style="background: radial-gradient(circle, rgba(81, 196, 248, 1) 0%, rgba(0, 81, 196, 0.83) 83%, rgba(0, 81, 196, 1) 100%); padding: 8px;">
                        <h6 style="color: white; margin: 0; cursor: pointer;" id="toggleAll">
                            <i class="fas fa-pen-fancy"></i> Cari Dokumen Keluaran Kegiatan OPD
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <label for="searchKeyword">Cari Berdasarkan Nama File:</label>
                                    <input type="text" id="searchKeyword" class="form-control" placeholder="Masukkan keyword">
                                </div>
                                <button id="searchBtn" class="btn btn-primary mt-2">Cari</button>

                                <div id="loading-indicator" class="loading-indicator" style="display: none;">
                                    <div class="loading-content">
                                        <img src="<?= Yii::getAlias('@web/udema/bappeda/loading.png') ?>" alt="Loading..." class="loading-spinner">
                                        <p class="mt-2">Mencari...</p>
                                    </div>
                                </div>
                                <div id="searchResults" class="mt-4"></div>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


<?php
// Load the necessary assets for jQuery
$this->registerJsFile('https://code.jquery.com/jquery-3.6.0.min.js', ['position' => \yii\web\View::POS_HEAD]);
?>

<script>
    $(document).ready(function() {
        $('#searchBtn').click(function() {
            var searchKeyword = $('#searchKeyword').val();
            var searchResults = $('#searchResults');
            // Ganti target ke ID yang baru
            var loadingIndicator = $('#loading-indicator');

            searchResults.html('');

            if (searchKeyword.trim() === '') {
                searchResults.html('<div class="alert alert-warning">Masukkan keyword pencarian.</div>');
                return; // Hentikan fungsi jika keyword kosong
            }

            // --- LOGIKA BARU DENGAN TIMER ---

            // Tampilkan animasi loading
            loadingIndicator.show();
            $(this).prop('disabled', true);
            var searchButton = $(this);

            var timerPromise = new Promise(function(resolve) {
                setTimeout(resolve, 500); // Timer 5 detik
            });


            // 2. Buat request AJAX
            var ajaxPromise = new Promise(function(resolve, reject) {
                $.ajax({
                    url: '<?= \yii\helpers\Url::to(['portal-dokumen-kegiatan']) ?>',
                    method: 'GET',
                    data: {
                        searchKeyword: searchKeyword
                    },
                    success: function(response) {
                        resolve(response); // Kirim response jika sukses
                    },
                    error: function() {
                        reject(); // Kirim sinyal error
                    }
                });
            });

            // 3. Jalankan keduanya secara bersamaan dan tunggu keduanya selesai
            Promise.all([ajaxPromise, timerPromise]).then(function(results) {
                // `results[0]` berisi response dari AJAX
                var response = results[0];

                // Tampilkan hasil pencarian
                if (response.dokumenList.length > 0) {
                    var html = '<table class="table table-striped table-bordered">';
                    html += '<thead><tr><th>No</th><th>Nama File</th><th class="text-center">Aksi</th></tr></thead><tbody>';
                    $.each(response.dokumenList, function(index, dokumen) {
                        html += '<tr>';
                        html += '<td>' + (index + 1) + '</td>';
                        html += '<td>' + dokumen.nama_file + '</td>';
                        html += '<td class="text-center"><a href="' + '<?= \yii\helpers\Url::to(['simona-keluaranmediacascadingkegiatan/download']) ?>?refsimonakeluaranmediacascadingkegiatan_id=' + dokumen.refsimonakeluaranmediacascadingkegiatan_id + '" class="btn btn-success btn-sm" target="_blank"><i class="fas fa-download"></i> Download</a></td>';
                        html += '</tr>';
                    });
                    html += '</tbody></table>';
                    searchResults.html(html);
                } else {
                    searchResults.html('<div class="alert alert-warning">Tidak ada dokumen yang ditemukan.</div>');
                }

            }).catch(function() {
                // Tangani jika AJAX error
                searchResults.html('<div class="alert alert-danger">Terjadi kesalahan saat pencarian.</div>');
            }).finally(function() {
                // Ini akan selalu berjalan setelah 5 detik DAN setelah AJAX selesai
                loadingIndicator.hide();
                searchButton.prop('disabled', false);
            });
        });
    });
</script>