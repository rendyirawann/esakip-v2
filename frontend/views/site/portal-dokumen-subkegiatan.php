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
?>


<div class="container p-2 mt-1">
    <div class="row">

        <div class="col-md-12">

            <!-- Menu list -->

            <div class="justify-content-center">
                <div class="card">
                    <div class="card-header" style="background: radial-gradient(circle, rgba(81, 196, 248, 1) 0%, rgba(0, 81, 196, 0.83) 83%, rgba(0, 81, 196, 1) 100%); padding: 8px;">
                        <h6 style="color: white; margin: 0; cursor: pointer;" id="toggleAll">
                            <i class="fas fa-pen-fancy"></i> Cari Dokumen Keluaran Sub Kegiatan OPD
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-sm-12">
                                <!-- Form Pencarian -->
                                <div class="form-group">
                                    <label for="searchKeyword">Cari Berdasarkan Nama File:</label>
                                    <input type="text" id="searchKeyword" class="form-control" placeholder="Masukkan keyword">
                                </div>
                                <button id="searchBtn" class="btn btn-primary" style="background: radial-gradient(circle, rgba(81, 196, 248, 1) 0%, rgba(0, 81, 196, 0.83) 83%, rgba(0, 81, 196, 1) 100%);">Cari</button>
                                <div id="searchResults" class="mt-4"></div> <!-- Hasil pencarian ditampilkan di sini -->
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
        // Fungsi untuk melakukan pencarian
        $('#searchBtn').click(function() {
            var searchKeyword = $('#searchKeyword').val();

            // Cek apakah ada keyword pencarian
            if (searchKeyword.trim() !== '') {
                // Kirim AJAX request ke server
                $.ajax({
                    url: '<?= \yii\helpers\Url::to(['portal-dokumen-subkegiatan']) ?>', // URL action yang menangani pencarian
                    method: 'GET',
                    data: {
                        searchKeyword: searchKeyword
                    },
                    success: function(response) {
                        // Jika ada hasil, tampilkan dalam tabel
                        if (response.dokumenList.length > 0) {
                            var html = '<table class="table table-striped table-bordered">';
                            html += '<thead><tr><th>No</th><th>Nama File</th><th>Download</th></tr></thead>';
                            html += '<tbody>';

                            $.each(response.dokumenList, function(index, dokumen) {
                                html += '<tr>';
                                html += '<td>' + (index + 1) + '</td>';
                                html += '<td>' + dokumen.nama_file + '</td>';
                                html += '<td><button class="btn btn-success download-btn" data-id="' + dokumen.refsimonakeluaranmediacascadingsubkegiatan_id + '">Download</button></td>';
                                html += '</tr>';
                            });

                            html += '</tbody></table>';
                            $('#searchResults').html(html);
                        } else {
                            $('#searchResults').html('<div class="alert alert-warning">Tidak ada dokumen yang ditemukan.</div>');
                        }
                    },
                    error: function() {
                        $('#searchResults').html('<div class="alert alert-danger">Terjadi kesalahan saat pencarian.</div>');
                    }
                });
            } else {
                // Jika tidak ada keyword, tampilkan peringatan
                $('#searchResults').html('<div class="alert alert-warning">Masukkan keyword pencarian.</div>');
            }
        });

        // Event listener untuk tombol download
        $(document).on('click', '.download-btn', function() {
            var fileId = $(this).data('id');

            // Membuka halaman download berdasarkan refsimonakeluaranmediacascadingkegiatan_id
            window.location.href = '<?= \yii\helpers\Url::to(['simona-keluaranmediacascadingsubkegiatan/download']) ?>&refsimonakeluaranmediacascadingsubkegiatan_id=' + fileId;
        });
    });
</script>