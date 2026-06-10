<?php

/** @var \yii\web\View $this */
/** @var string $content */

use common\widgets\Alert;
use frontend\assets\MainPortalAsset;
use yii\bootstrap5\Breadcrumbs;
// use yii\bootstrap5\Html;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use yii\bootstrap5\Nav;
use yii\bootstrap5\NavBar;

MainPortalAsset::register($this);

$this->registerJsFile('https://cdn.jsdelivr.net/npm/echarts/dist/echarts.min.js', ['depends' => [\yii\web\JqueryAsset::class]]);

$this->title = 'e-SAKIP Deli Serdang';

$this->registerCss(<<<CSS
.table th,
.table td {
    padding: 0.35rem 0.5rem;
    font-size: 0.78rem;
    white-space: nowrap;
}

.table thead th {
    font-weight: 600;
}

.card-title {
    font-size: 1rem;
    max-width: 100%;
    word-break: break-word;
}

.card-body > .row {
    margin-left: 0;
    margin-right: 0;
}

.table-responsive {
    overflow-x: auto;
}

@media (min-width: 768px) {
    .card .card-body {
        padding: 1rem;
    }
}
CSS);

// Sasaran Renstra

// Register SweetAlert2 via CDN
$this->registerJsFile('https://cdn.jsdelivr.net/npm/sweetalert2@11');

$script = <<< JS
    // Gunakan selector form yang spesifik agar tidak bentrok dengan form lain jika ada
    $('form').on('submit', function(e) {
        // Ambil value berdasarkan name/id (sesuaikan dengan dropdown Anda)
        var skpdValue = $('select[name="refskpd_id"]').val();
        var targetPage = $('select[name="target_page"]').val();

        // 1. Validasi Wajib Pilih SKPD untuk halaman Tabulasi
        if (targetPage === 'portal-publik-tabulasi' && (!skpdValue || skpdValue === "")) {
            e.preventDefault();
            Swal.fire({
                icon: 'warning',
                title: 'Perangkat Daerah Belum Dipilih',
                text: 'Silahkan pilih satu Perangkat Daerah untuk menampilkan data tabulasi.',
                confirmButtonColor: '#3085d6'
            });
            return false;
        }

        // 2. Jika validasi lolos, tampilkan Loading Overlay
        Swal.fire({
            title: 'Sedang Memproses...',
            text: 'Harap tunggu, data sedang ditarik dari sistem.',
            allowOutsideClick: false,
            showConfirmButton: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });
    });
JS;
$this->registerJs($script);

$this->registerJs("
// Fungsi untuk memformat angka menjadi format mata uang Rupiah
function formatRupiah(angka) {
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        minimumFractionDigits: 0
    }).format(angka);
}

document.querySelectorAll('.indicator-link').forEach(link => {
    link.addEventListener('click', function(event) {
        event.preventDefault();

        const indicatorText = this.dataset.indicatorText;
        const relatedData = JSON.parse(this.dataset.programs);

        const modalTitle = document.getElementById('programModalLabel');
        const modalBody = document.getElementById('programModalBody');

        modalTitle.textContent = indicatorText;

        let contentHtml = '';
        if (relatedData && relatedData.length > 0) {
            contentHtml = '<div class=\"table-responsive\">' +
                          '<table class=\"table table-bordered table-striped table-sm\">' +
                          '<thead class=\"table-secondary\">' + 
                          '  <tr>' +
                          '    <th rowspan=\"2\" style=\"vertical-align: middle;\">Nama Program</th>' +
                          '    <th rowspan=\"2\" class=\"text-end\" style=\"vertical-align: middle;\">Anggaran Program</th>' +
                          '    <th colspan=\"4\" class=\"text-center\">Detail Kegiatan & Sub Kegiatan</th>' +
                          '  </tr>' +
                          '  <tr>' +
                          '    <th>Nama Kegiatan</th>' +
                          '    <th class=\"text-end\">Anggaran Kegiatan</th>' +
                          '    <th>Nama Sub Kegiatan</th>' +
                          '    <th class=\"text-end\">Anggaran Sub Kegiatan</th>' +
                          '  </tr>' +
                          '</thead>' +
                          '<tbody>';

            // [PERBAIKAN] Inisialisasi semua variabel Grand Total
            let grandTotalProgram = 0;
            let grandTotalKegiatan = 0;
            let grandTotalSubkegiatan = 0;
            
            relatedData.forEach(program => {
                grandTotalProgram += parseFloat(program.budget) || 0;
                
                let programRowSpan = 0;
                if (program.kegiatans && program.kegiatans.length > 0) {
                    program.kegiatans.forEach(kegiatan => {
                        programRowSpan += (kegiatan.subkegiatans && kegiatan.subkegiatans.length > 0) ? kegiatan.subkegiatans.length : 1;
                    });
                } else {
                    programRowSpan = 1;
                }

                if (program.kegiatans && program.kegiatans.length > 0) {
                    program.kegiatans.forEach((kegiatan, kegIndex) => {
                        // [PERBAIKAN] Jumlahkan anggaran kegiatan di sini
                        grandTotalKegiatan += parseFloat(kegiatan.budget) || 0;
                        let kegiatanRowSpan = (kegiatan.subkegiatans && kegiatan.subkegiatans.length > 0) ? kegiatan.subkegiatans.length : 1;

                        if (kegiatan.subkegiatans && kegiatan.subkegiatans.length > 0) {
                            kegiatan.subkegiatans.forEach((subkegiatan, subkegIndex) => {
                                // [BARU] Jumlahkan anggaran sub kegiatan di sini
                                grandTotalSubkegiatan += parseFloat(subkegiatan.budget) || 0;

                                contentHtml += '<tr>';
                                if (kegIndex === 0 && subkegIndex === 0) {
                                    contentHtml += '<td rowspan=\"' + programRowSpan + '\">' + program.name + '</td>';
                                    contentHtml += '<td rowspan=\"' + programRowSpan + '\" class=\"text-end\">' + formatRupiah(program.budget) + '</td>';
                                }
                                if (subkegIndex === 0) {
                                    contentHtml += '<td rowspan=\"' + kegiatanRowSpan + '\">' + kegiatan.name + '</td>';
                                    contentHtml += '<td rowspan=\"' + kegiatanRowSpan + '\" class=\"text-end\">' + formatRupiah(kegiatan.budget) + '</td>';
                                }
                                contentHtml += '<td>' + subkegiatan.name + '</td>';
                                contentHtml += '<td class=\"text-end\">' + formatRupiah(subkegiatan.budget) + '</td>';
                                contentHtml += '</tr>';
                            });
                        } else {
                            contentHtml += '<tr>';
                            if (kegIndex === 0) {
                                contentHtml += '<td rowspan=\"' + programRowSpan + '\">' + program.name + '</td>';
                                contentHtml += '<td rowspan=\"' + programRowSpan + '\" class=\"text-end\">' + formatRupiah(program.budget) + '</td>';
                            }
                            contentHtml += '<td>' + kegiatan.name + '</td>';
                            contentHtml += '<td class=\"text-end\">' + formatRupiah(kegiatan.budget) + '</td>';
                            contentHtml += '<td class=\"text-center\"><span class=\"text-muted\">-</span></td><td><span class=\"text-muted\">-</span></td>';
                            contentHtml += '</tr>';
                        }
                    });
                } else {
                     contentHtml += '<tr><td>' + program.name + '</td><td class=\"text-end\">' + formatRupiah(program.budget) + '</td><td colspan=\"4\" class=\"text-center\"><span class=\"text-muted\">Tidak ada kegiatan dan sub kegiatan terkait</span></td></tr>';
                }
            });

            // [PERBAIKAN TOTAL] Modifikasi baris Grand Total untuk menampilkan semua total
            contentHtml += '<tr class=\"table-light fw-bold\">' +
                           '  <td class=\"text-end\">GRAND TOTAL</td>' +
                           '  <td class=\"text-end\">' + formatRupiah(grandTotalProgram) + '</td>' +
                           '  <td class=\"text-end\">GRAND TOTAL</td>' +
                           '  <td class=\"text-end\">' + formatRupiah(grandTotalKegiatan) + '</td>' +
                           '  <td class=\"text-end\">GRAND TOTAL</td>' +
                           '  <td class=\"text-end\">' + formatRupiah(grandTotalSubkegiatan) + '</td>' +
                           '</tr>';

            contentHtml += '</tbody></table></div>';
        } else {
            contentHtml = '<div class=\"alert alert-warning\">Tidak ada program yang terkait.</div>';
        }

        modalBody.innerHTML = contentHtml;
    });
});
", \yii\web\View::POS_READY, 'portal-tabulasi-modal-js');

// Sasaran Program

$this->registerJs("
// Fungsi untuk memformat angka menjadi format mata uang Rupiah
function formatRupiah(angka) {
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        minimumFractionDigits: 0
    }).format(angka);
}

document.querySelectorAll('.program-indicator-link').forEach(link => {
    link.addEventListener('click', function(event) {
        event.preventDefault();

        const indicatorText = this.dataset.indicatorText;
        const programName = this.dataset.programName;
        const programBudget = parseFloat(this.dataset.programBudget) || 0;
        const kegiatans = JSON.parse(this.dataset.kegiatans);

        const modalTitle = document.getElementById('detailProgramModalLabel');
        const modalBody = document.getElementById('detailProgramModalBody');

        modalTitle.textContent = indicatorText;

        let contentHtml = '';
        if (kegiatans && kegiatans.length > 0) {
            // [PERUBAHAN TOTAL] Buat tabel dengan 6 kolom
            contentHtml = '<div class=\"table-responsive\">' +
                          '<table class=\"table table-bordered table-striped table-sm\">' +
                          '<thead class=\"table-secondary\">' + 
                          '  <tr>' +
                          '    <th rowspan=\"2\" style=\"vertical-align: middle;\">Nama Program</th>' +
                          '    <th rowspan=\"2\" class=\"text-end\" style=\"vertical-align: middle;\">Anggaran Program</th>' +
                          '    <th colspan=\"4\" class=\"text-center\">Detail Kegiatan & Sub Kegiatan</th>' +
                          '  </tr>' +
                          '  <tr>' +
                          '    <th>Nama Kegiatan</th>' +
                          '    <th class=\"text-end\">Anggaran Kegiatan</th>' +
                          '    <th>Sub Kegiatan Terkait</th>' +
                          '    <th class=\"text-end\">Anggaran Sub Kegiatan</th>' +
                          '  </tr>' +
                          '</thead>' +
                          '<tbody>';

            let grandTotalKegiatan = 0;
            let grandTotalSubkegiatan = 0;

            // Kalkulasi rowspan yang rumit untuk program
            let programRowSpan = 0;
            kegiatans.forEach(kegiatan => {
                grandTotalKegiatan += parseFloat(kegiatan.budget) || 0;
                programRowSpan += (kegiatan.subkegiatans && kegiatan.subkegiatans.length > 0) ? kegiatan.subkegiatans.length : 1;
            });

            kegiatans.forEach((kegiatan, kegIndex) => {
                let kegiatanRowSpan = (kegiatan.subkegiatans && kegiatan.subkegiatans.length > 0) ? kegiatan.subkegiatans.length : 1;

                if (kegiatan.subkegiatans && kegiatan.subkegiatans.length > 0) {
                    kegiatan.subkegiatans.forEach((subkegiatan, subkegIndex) => {
                        grandTotalSubkegiatan += parseFloat(subkegiatan.budget) || 0;
                        contentHtml += '<tr>';
                        if (kegIndex === 0 && subkegIndex === 0) {
                            contentHtml += '<td rowspan=\"' + programRowSpan + '\">' + programName + '</td>';
                            contentHtml += '<td rowspan=\"' + programRowSpan + '\" class=\"text-end\">' + formatRupiah(programBudget) + '</td>';
                        }
                        if (subkegIndex === 0) {
                            contentHtml += '<td rowspan=\"' + kegiatanRowSpan + '\">' + kegiatan.name + '</td>';
                            contentHtml += '<td rowspan=\"' + kegiatanRowSpan + '\" class=\"text-end\">' + formatRupiah(kegiatan.budget) + '</td>';
                        }
                        contentHtml += '<td>' + subkegiatan.name + '</td>';
                        contentHtml += '<td class=\"text-end\">' + formatRupiah(subkegiatan.budget) + '</td>';
                        contentHtml += '</tr>';
                    });
                } else { // Jika kegiatan tidak punya sub kegiatan
                    contentHtml += '<tr>';
                    if (kegIndex === 0) {
                        contentHtml += '<td rowspan=\"' + programRowSpan + '\">' + programName + '</td>';
                        contentHtml += '<td rowspan=\"' + programRowSpan + '\" class=\"text-end\">' + formatRupiah(programBudget) + '</td>';
                    }
                    contentHtml += '<td>' + kegiatan.name + '</td>';
                    contentHtml += '<td class=\"text-end\">' + formatRupiah(kegiatan.budget) + '</td>';
                    contentHtml += '<td class=\"text-center\"><span class=\"text-muted\">-</span></td><td><span class=\"text-muted\">-</span></td>';
                    contentHtml += '</tr>';
                }
            });

            // Baris Grand Total
            contentHtml += '<tr class=\"table-light fw-bold\">' +
                           '  <td class=\"text-end\">GRAND TOTAL</td>' +
                           '  <td class=\"text-end\">' + formatRupiah(programBudget) + '</td>' +
                           '  <td class=\"text-end\">GRAND TOTAL</td>' +
                           '  <td class=\"text-end\">' + formatRupiah(grandTotalKegiatan) + '</td>' +
                           '  <td class=\"text-end\">GRAND TOTAL</td>' +
                           '  <td class=\"text-end\">' + formatRupiah(grandTotalSubkegiatan) + '</td>' +
                           '</tr>';

            contentHtml += '</tbody></table></div>';
        } else { // Jika program tidak punya kegiatan
            contentHtml += '<div class=\"alert alert-info\">Program ini tidak memiliki kegiatan terkait.</div>';
        }

        modalBody.innerHTML = contentHtml;
    });
});
", \yii\web\View::POS_READY, 'portal-program-modal-js');

// Sasaran Kegiatan
// [BARU] JavaScript untuk menangani Modal Kegiatan
$this->registerJs("
// Fungsi untuk memformat angka menjadi format mata uang Rupiah
function formatRupiah(angka) {
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        minimumFractionDigits: 0
    }).format(angka);
}

document.querySelectorAll('.kegiatan-indicator-link').forEach(link => {
    link.addEventListener('click', function(event) {
        event.preventDefault();

        // Ambil semua data dari atribut link
        const indicatorText = this.dataset.indicatorText;
        const kegiatanName = this.dataset.kegiatanName;
        const kegiatanBudget = parseFloat(this.dataset.kegiatanBudget) || 0;
        const subkegiatans = JSON.parse(this.dataset.subkegiatans);

        const modalTitle = document.getElementById('detailKegiatanModalLabel');
        const modalBody = document.getElementById('detailKegiatanModalBody');

        modalTitle.textContent = indicatorText;
        
        // [PERUBAHAN TOTAL] Buat tabel dengan 4 kolom
        let contentHtml = '<div class=\"table-responsive\">' +
                          '<table class=\"table table-bordered table-striped table-sm\">' +
                          '<thead class=\"table-secondary\">' + 
                          '  <tr>' +
                          '    <th>Nama Kegiatan</th>' +
                          '    <th class=\"text-end\">Total Anggaran Kegiatan</th>' +
                          '    <th>Sub Kegiatan Terkait</th>' +
                          '    <th class=\"text-end\">Anggaran Sub Kegiatan</th>' +
                          '  </tr>' +
                          '</thead>' +
                          '<tbody>';

        let grandTotalSubkegiatan = 0; // Inisialisasi Grand Total Sub Kegiatan

        if (subkegiatans && subkegiatans.length > 0) {
            const subkegiatanCount = subkegiatans.length;
            subkegiatans.forEach((subkeg, index) => {
                const subkegName = subkeg.name || '-';
                const subkegBudget = parseFloat(subkeg.budget) || 0;
                grandTotalSubkegiatan += subkegBudget;

                contentHtml += '<tr>';
                // Kolom kegiatan dan anggarannya hanya ditampilkan di baris pertama
                if (index === 0) {
                    contentHtml += '<td rowspan=\"' + subkegiatanCount + '\">' + kegiatanName + '</td>';
                    contentHtml += '<td rowspan=\"' + subkegiatanCount + '\" class=\"text-end\">' + formatRupiah(kegiatanBudget) + '</td>';
                }
                contentHtml += '<td>' + subkegName + '</td>';
                contentHtml += '<td class=\"text-end\">' + formatRupiah(subkegBudget) + '</td>';
                contentHtml += '</tr>';
            });
        } else {
            // Jika tidak ada sub kegiatan, tetap tampilkan baris kegiatan
            contentHtml += '<tr>' +
                           '  <td>' + kegiatanName + '</td>' +
                           '  <td class=\"text-end\">' + formatRupiah(kegiatanBudget) + '</td>' +
                           '  <td colspan=\"2\" class=\"text-center\"><span class=\"text-muted\">Tidak ada sub kegiatan terkait</span></td>' +
                           '</tr>';
        }

        contentHtml += '</tbody>' +
                       '<tfoot class=\"table-light fw-bold\">' +
                       '  <tr>' +
                       '    <td class=\"text-end\">GRAND TOTAL</td>' +
                       '    <td class=\"text-end\">' + formatRupiah(kegiatanBudget) + '</td>' +
                       '    <td class=\"text-end\">GRAND TOTAL</td>' +
                       '    <td class=\"text-end\">' + formatRupiah(grandTotalSubkegiatan) + '</td>' +
                       '  </tr>' +
                       '</tfoot>' +
                       '</table></div>';

        modalBody.innerHTML = contentHtml;
    });
});
", \yii\web\View::POS_READY, 'portal-kegiatan-modal-js');

// Sasaran Sub Kegiatan
// [BARU] JavaScript untuk menangani Modal Sub Kegiatan
$this->registerJs("
// Fungsi untuk memformat angka menjadi format mata uang Rupiah
function formatRupiah(angka) {
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        minimumFractionDigits: 0
    }).format(angka);
}

document.querySelectorAll('.subkegiatan-indicator-link').forEach(link => {
    link.addEventListener('click', function(event) {
        event.preventDefault();

        // Ambil semua data dari atribut link
        const indicatorText = this.dataset.indicatorText;
        const subkegiatanName = this.dataset.subkegiatanName;
        const subkegiatanBudget = parseFloat(this.dataset.subkegiatanBudget) || 0;

        const modalTitle = document.getElementById('detailSubkegiatanModalLabel');
        const modalBody = document.getElementById('detailSubkegiatanModalBody');

        // Isi judul modal dengan nama indikator
        modalTitle.textContent = indicatorText;
        
        // Buat konten tabel untuk modal
        let contentHtml = '<div class=\"table-responsive\">' +
                          '<table class=\"table table-bordered table-sm\">' +
                          '<thead class=\"table-secondary\"><tr><th>Sub Kegiatan Terkait</th><th class=\"text-end\">Total Anggaran</th></tr></thead>' +
                          '<tbody>' +
                          '  <tr>' +
                          '    <td>' + subkegiatanName + '</td>' +
                          '    <td class=\"text-end\">' + formatRupiah(subkegiatanBudget) + '</td>' +
                          '  </tr>' +
                          '</tbody>' +
                           '<tfoot class=\"table-light fw-bold\">' +
                           '  <tr>' +
                           '    <td class=\"text-end\">GRAND TOTAL</td>' +
                           '    <td class=\"text-end\">' + formatRupiah(subkegiatanBudget) + '</td>' +
                           '  </tr>' +
                           '</tfoot>' +
                          '</table></div>';

        modalBody.innerHTML = contentHtml;
    });
});
", \yii\web\View::POS_READY, 'portal-subkegiatan-modal-js');

// RegisterJS Triwulan Grafik Pie Chart

$dataChart = json_encode($triwulanTerendahPerSkpd);
$chartId = 'triwulan-terendah-pie';

$dataChartTerendah = json_encode($triwulanTerendahPerSkpd);
$dataChartTertinggi = json_encode($triwulanTertinggiPerSkpd);
$chartIdTerendah = 'triwulan-terendah-pie';
$chartIdTertinggi = 'triwulan-tertinggi-pie';
$namaSkpdTerendah = count($triwulanTerendahPerSkpd) > 0 ? $triwulanTerendahPerSkpd[0]['nama'] : 'Tidak ada data';
$namaSkpdTertinggi = count($triwulanTertinggiPerSkpd) > 0 ? $triwulanTertinggiPerSkpd[0]['nama'] : 'Tidak ada data';

$this->registerJs("
function buildProgramList(programs) {
    if (!programs || programs.length === 0) {
        return '<p class=\"mb-0 text-muted\" style=\"font-style: italic;\">Tidak ada program yang terkait.</p>';
    }

    let listHtml = '<ul class=\"mb-0 ps-4\">';
    programs.forEach(function(programName) {
        listHtml += '<li>- ' + programName + '.</li>';
    });
    listHtml += '</ul>';
    return listHtml;
}

// [BARU] Fungsi bantuan untuk membuat daftar HTML dari array kegiatan
function buildKegiatanList(kegiatans) {
    if (!kegiatans || kegiatans.length === 0) {
        return '<p class=\"mb-0 text-muted\" style=\"font-style: italic;\">Tidak ada Kegiatan yang terkait.</p>';
    }

    let listHtml = '<ul class=\"mb-0 ps-4\">';
    kegiatans.forEach(function(kegiatanName) {
        listHtml += '<li>- ' + kegiatanName + '.</li>';
    });
    listHtml += '</ul>';
    return listHtml;
}

// [BARU] Fungsi bantuan untuk membuat daftar HTML dari array sub kegiatan
function buildSubkegiatanList(subkegiatans) {
    if (!subkegiatans || subkegiatans.length === 0) {
        return '<p class=\"mb-0 text-muted\" style=\"font-style: italic;\">Tidak ada Sub Kegiatan yang terkait.</p>';
    }

    let listHtml = '<ul class=\"mb-0 ps-4\">';
    subkegiatans.forEach(function(subkegiatanName) {
        listHtml += '<li>- ' + subkegiatanName + '.</li>';
    });
    listHtml += '</ul>';
    return listHtml;
}

// [BARU] Fungsi pembuat opsi pie chart modern (Doughnut Chart)
function getModernPieOption(titleText, chartData) {
    return {
        color: ['#4F46E5', '#10B981', '#F59E0B', '#EF4444', '#8B5CF6', '#EC4899', '#06B6D4', '#F97316', '#14B8A6', '#F43F5E'],
        title: {
            text: titleText,
            left: 'center',
            textStyle: {
                fontSize: 14,
                fontWeight: '600',
                color: '#374151'
            },
            padding: [0, 0, 20, 0]
        },
        tooltip: {
            trigger: 'item',
            backgroundColor: 'rgba(255, 255, 255, 0.95)',
            borderColor: '#e5e7eb',
            borderWidth: 1,
            textStyle: {
                color: '#1f2937'
            },
            formatter: function(params) {
                var nama = params.data.nama;
                var value = params.value;
                return '<div style=\"font-weight:600;margin-bottom:4px; border-bottom:1px solid #eee; padding-bottom:4px;\">' + nama + '</div>' +
                       '<span style=\"display:inline-block;margin-right:6px;border-radius:50%;width:10px;height:10px;background-color:' + params.color + ';\"></span>' +
                       'Capaian: <span style=\"font-weight:bold;\">' + value + '%</span>';
            }
        },
        legend: {
            orient: 'horizontal',
            bottom: '0',
            type: 'scroll',
            textStyle: {
                color: '#4b5563',
                fontSize: 11
            },
            itemWidth: 12,
            itemHeight: 12
        },
        series: [{
            name: 'Capaian',
            type: 'pie',
            radius: ['40%', '65%'],
            center: ['50%', '50%'],
            avoidLabelOverlap: true,
            itemStyle: {
                borderRadius: 8,
                borderColor: '#fff',
                borderWidth: 2
            },
            label: {
                show: true,
                formatter: function(params) {
                    var nama = params.data.nama;
                    if (nama && nama.length > 15) { nama = nama.substring(0, 15) + '...'; }
                    return '{name|' + (nama || '') + '}\\n{val|' + params.value + '%}';
                },
                rich: {
                    name: { fontSize: 11, color: '#6b7280' },
                    val: { fontSize: 13, fontWeight: 'bold', color: '#1f2937', padding: [4, 0, 0, 0] }
                }
            },
            labelLine: {
                show: true,
                smooth: 0.2,
                length: 10,
                length2: 15
            },
            data: chartData,
            emphasis: {
                itemStyle: {
                    shadowBlur: 10,
                    shadowOffsetX: 0,
                    shadowColor: 'rgba(0, 0, 0, 0.15)'
                }
            }
        }]
    };
}

    var chartDom1 = document.getElementById('$chartIdTerendah');
    if ($dataChartTerendah && $dataChartTerendah.length > 0) {
        var myChart1 = echarts.init(chartDom1);
        var option1 = getModernPieOption('Indikator Sasaran - Capaian Terendah Semua Triwulan', $dataChartTerendah);
        myChart1.setOption(option1);
    } else {
        chartDom1.innerHTML = '<div class=\"d-flex align-items-center justify-content-center\" style=\"height: 100%; border: 2px dashed #ddd; border-radius: 10px; background-color: #fcfcfc; color: #888; flex-direction: column;\"><i class=\"fas fa-chart-pie mb-3\" style=\"font-size: 3rem; color: #ccc;\"></i><h5>Belum ada data capaian sasaran</h5><p class=\"text-muted\">Data visualisasi akan muncul setelah evaluasi tersedia.</p></div>';
    }

    // [!!!] TAMBAHAN: EVENT LISTENER UNTUK KLIK PADA CHART TERENDAH
    myChart1.on('click', function(params) {
        var detailContainer = document.getElementById('detail-indikator-terendah');
        // 'params.data' berisi objek dari array $dataChartTerendah {name, value, indikator}
        if (params.data && params.data.indikator) {
            var nama = params.data.nama;
            var indikator = params.data.indikator;
            var capaian = params.value;
            var programs = params.data.programs; // Ambil data program
            var kegiatans = params.data.kegiatans; // Ambil data kegiatan
            var subkegiatans = params.data.subkegiatans; // Ambil data sub kegiatan

        var programHtml = buildProgramList(programs); // Buat daftar HTML
        var kegiatanHtml = buildKegiatanList(kegiatans); // Buat daftar HTML
        var subkegiatanHtml = buildSubkegiatanList(subkegiatans); // Buat daftar HTML

            detailContainer.innerHTML = '<h6>' + nama + '</h6>' +
                                        '<p class=\"mb-1\"><strong>Indikator:</strong> ' + indikator + '</p>' +
                                        '<p class=\"mb-0\"><strong>Capaian:</strong> ' + capaian + ' %</p>' +
                                        '<strong class=\"d-block\"><strong>Terkait pada Program:</strong> ' + programHtml + '.</strong>' +
                                        '<strong class=\"d-block\"><strong>Terkait pada Kegiatan:</strong> ' + kegiatanHtml + '.</strong>' +
                                        '<strong class=\"d-block\"><strong>Terkait pada Sub Kegiatan:</strong> ' + subkegiatanHtml + '.</strong>'
        }
    });

    var chartDom2 = document.getElementById('$chartIdTertinggi');
    if ($dataChartTertinggi && $dataChartTertinggi.length > 0) {
        var myChart2 = echarts.init(chartDom2);
        var option2 = getModernPieOption('Indikator Sasaran - Capaian Tertinggi Semua Triwulan', $dataChartTertinggi);
        myChart2.setOption(option2);
    } else {
        chartDom2.innerHTML = '<div class=\"d-flex align-items-center justify-content-center\" style=\"height: 100%; border: 2px dashed #ddd; border-radius: 10px; background-color: #fcfcfc; color: #888; flex-direction: column;\"><i class=\"fas fa-chart-pie mb-3\" style=\"font-size: 3rem; color: #ccc;\"></i><h5>Belum ada data capaian sasaran</h5><p class=\"text-muted\">Data visualisasi akan muncul setelah evaluasi tersedia.</p></div>';
    }

    // [!!!] TAMBAHAN: EVENT LISTENER UNTUK KLIK PADA CHART TERTINGGI
    myChart2.on('click', function(params) {
        var detailContainer = document.getElementById('detail-indikator-tertinggi');
        // 'params.data' berisi objek dari array $dataChartTertinggi {name, value, indikator}
        if (params.data && params.data.indikator) {
            var nama = params.data.nama;
            var indikator = params.data.indikator;
            var capaian = params.value;
            var programs = params.data.programs; // Ambil data program
            var kegiatans = params.data.kegiatans; // Ambil data kegiatan
            var subkegiatans = params.data.subkegiatans; // Ambil data subkegiatan

        var programHtml = buildProgramList(programs); // Buat daftar HTML
        var kegiatanHtml = buildKegiatanList(kegiatans); // Buat daftar HTML
        var subkegiatanHtml = buildSubkegiatanList(subkegiatans); // Buat daftar HTML

            detailContainer.innerHTML = '<h6>' + nama + '</h6>' +
                                        '<p class=\"mb-1\"><strong>Indikator:</strong> ' + indikator + '</p>' +
                                        '<p class=\"mb-0\"><strong>Capaian:</strong> ' + capaian + '.</p>' +
                                        '<strong class=\"d-block\"><strong>Terkait pada Program:</strong> ' + programHtml + '.</strong>' +
                                        '<strong class=\"d-block\"><strong>Terkait pada Kegiatan:</strong> ' + kegiatanHtml + '.</strong>' +
                                        '<strong class=\"d-block\"><strong>Terkait pada Sub Kegiatan:</strong> ' + subkegiatanHtml + '.</strong>'
        }
    });

    // Menyesuaikan ukuran chart saat window di-resize
    window.addEventListener('resize', function() {
        myChart1.resize();
        myChart2.resize();
    });
");

$dataChartProgramTerendah = json_encode($triwulanTerendahProgram);
$dataChartProgramTertinggi = json_encode($triwulanTertinggiProgram);
$chartIdProgramTerendah = 'triwulan-terendah-program-pie';
$chartIdProgramTertinggi = 'triwulan-tertinggi-program-pie';

$namaProgramTerendah = count($triwulanTerendahProgram) > 0 ? $triwulanTerendahProgram[0]['nama'] : 'Tidak ada data';
$namaProgramTertinggi = count($triwulanTertinggiProgram) > 0 ? $triwulanTertinggiProgram[0]['nama'] : 'Tidak ada data';

$this->registerJs("
// Fungsi bantuan untuk membuat daftar HTML (lebih generik)
function buildList(items, title) {
    // [PERBAIKAN] Pesan dibuat lebih generik
    if (!items || items.length === 0) {
        return '<div class=\"mt-2\">' +
               '  <strong class=\"d-block\">' + title + '</strong>' +
               '  <p class=\"mb-0 text-muted\" style=\"font-style: italic;\">Tidak ada data terkait.</p>' +
               '</div>';
    }

    var listHtml = '<div class=\"mt-2\"><strong class=\"d-block\">' + title + '</strong><ul class=\"mb-0 ps-4\">';
    for (var i = 0; i < items.length; i++) {
        listHtml += '<li>- ' + items[i] + '.</li>';
    }
    listHtml += '</ul></div>';
    return listHtml;
}

// Fungsi untuk menangani update detail saat chart program di-klik
function handleProgramChartClick(params, detailContainerId) {
    var detailContainer = document.getElementById(detailContainerId);
    if (!params.data || !params.data.indikator) return;

    var nama = params.data.nama;
    var indikator = params.data.indikator;
    var capaian = params.value;
    var programName = params.data.program_name;
    var kegiatans = params.data.kegiatans;
    var subkegiatans = params.data.subkegiatans; // BARU: Ambil data sub kegiatan

    // Buat masing-masing blok HTML
    var kegiatanHtml = buildList(kegiatans, 'Terkait pada Kegiatan:');
    var subkegiatanHtml = buildList(subkegiatans, 'Terkait pada Sub Kegiatan:'); // BARU

    // Perbarui innerHTML
    detailContainer.innerHTML = '<h6>' + nama + '</h6>' +
        '<p class=\"mb-1\"><strong>Program:</strong> ' + programName + '</p>' +
        '<p class=\"mb-1\"><strong>Indikator:</strong> ' + indikator + '</p>' +
        '<p class=\"mb-1\"><strong>Capaian:</strong> ' + capaian + '%</p>' +
        kegiatanHtml +
        subkegiatanHtml; // BARU: Tambahkan daftar sub kegiatan
}
        
    var chartDomProgram1 = document.getElementById('$chartIdProgramTerendah');
    if ($dataChartProgramTerendah && $dataChartProgramTerendah.length > 0) {
        var myChartProgram1 = echarts.init(chartDomProgram1);
        var optionProgram1 = getModernPieOption('Indikator Program - Capaian Terendah Semua Triwulan', $dataChartProgramTerendah);
        myChartProgram1.setOption(optionProgram1);
    } else {
        chartDomProgram1.innerHTML = '<div class=\"d-flex align-items-center justify-content-center\" style=\"height: 100%; border: 2px dashed #ddd; border-radius: 10px; background-color: #fcfcfc; color: #888; flex-direction: column;\"><i class=\"fas fa-chart-pie mb-3\" style=\"font-size: 3rem; color: #ccc;\"></i><h5>Belum ada data capaian program</h5><p class=\"text-muted\">Data visualisasi akan muncul setelah evaluasi tersedia.</p></div>';
    }
    myChartProgram1.on('click', function(params) { handleProgramChartClick(params, 'detail-indikator-program-terendah'); });


    var chartDomProgram2 = document.getElementById('$chartIdProgramTertinggi');
    if ($dataChartProgramTertinggi && $dataChartProgramTertinggi.length > 0) {
        var myChartProgram2 = echarts.init(chartDomProgram2);
        var optionProgram2 = getModernPieOption('Indikator Program - Capaian Tertinggi Semua Triwulan', $dataChartProgramTertinggi);
        myChartProgram2.setOption(optionProgram2);
    } else {
        chartDomProgram2.innerHTML = '<div class=\"d-flex align-items-center justify-content-center\" style=\"height: 100%; border: 2px dashed #ddd; border-radius: 10px; background-color: #fcfcfc; color: #888; flex-direction: column;\"><i class=\"fas fa-chart-pie mb-3\" style=\"font-size: 3rem; color: #ccc;\"></i><h5>Belum ada data capaian program</h5><p class=\"text-muted\">Data visualisasi akan muncul setelah evaluasi tersedia.</p></div>';
    }
    myChartProgram2.on('click', function(params) { handleProgramChartClick(params, 'detail-indikator-program-tertinggi'); });

    // --- Event listener untuk resize window ---
window.addEventListener('resize', function() {
    myChartProgram1.resize();
    myChartProgram2.resize();
});
");

$dataChartKegiatanTertinggi = json_encode($triwulanTertinggiKegiatan);
$dataChartKegiatanTerendah = json_encode($triwulanTerendahKegiatan);
$chartIdKegiatanTertinggi = 'triwulan-tertinggi-kegiatan-pie';
$chartIdKegiatanTerendah = 'triwulan-terendah-kegiatan-pie';

$this->registerJs("
// Fungsi bantuan untuk membuat daftar HTML (generik)
function buildList(items, title) {
    if (!items || items.length === 0) {
        return '<div class=\"mt-2\">' +
               '  <strong class=\"d-block\">' + title + '</strong>' +
               '  <p class=\"mb-0 text-muted\" style=\"font-style: italic;\">Tidak ada data terkait.</p>' +
               '</div>';
    }
    var listHtml = '<div class=\"mt-2\"><strong class=\"d-block\">' + title + '</strong><ul class=\"mb-0 ps-4\">';
    for (var i = 0; i < items.length; i++) {
        listHtml += '<li>- ' + items[i] + '.</li>';
    }
    listHtml += '</ul></div>';
    return listHtml;
}

// Fungsi untuk menangani klik pada chart kegiatan
function handleKegiatanChartClick(params, detailContainerId) {
    var detailContainer = document.getElementById(detailContainerId);
    if (!params.data || !params.data.indikator) return;

    // Ambil semua data dari chart
    var nama = params.data.nama;
    var kegiatanName = params.data.kegiatan_name;
    var indikator = params.data.indikator;
    var capaian = params.value;
    var subkegiatans = params.data.subkegiatans; // [BARU] Ambil data sub kegiatan

    // Buat blok HTML untuk sub kegiatan
    var subkegiatanHtml = buildList(subkegiatans, 'Terkait pada Sub Kegiatan:'); // [BARU]

    // Tampilkan semua informasi dengan rapi
    detailContainer.innerHTML = '<h6>' + nama + '</h6>' +
        '<p class=\"mb-1\"><strong>Kegiatan:</strong> ' + kegiatanName + '</p>' +
        '<p class=\"mb-1\"><strong>Indikator:</strong> ' + indikator + '</p>' +
        '<p class=\"mb-1\"><strong>Capaian:</strong> ' + capaian + '%</p>' +
        subkegiatanHtml; // [BARU] Tambahkan daftar sub kegiatan
}

    var chartDomKeg1 = document.getElementById('$chartIdKegiatanTerendah');
    if ($dataChartKegiatanTerendah && $dataChartKegiatanTerendah.length > 0) {
        var myChartKeg1 = echarts.init(chartDomKeg1);
        var optionKeg1 = getModernPieOption('Indikator Kegiatan - Capaian Terendah Semua Triwulan', $dataChartKegiatanTerendah);
        myChartKeg1.setOption(optionKeg1);
    } else {
        chartDomKeg1.innerHTML = '<div class=\"d-flex align-items-center justify-content-center\" style=\"height: 100%; border: 2px dashed #ddd; border-radius: 10px; background-color: #fcfcfc; color: #888; flex-direction: column;\"><i class=\"fas fa-chart-pie mb-3\" style=\"font-size: 3rem; color: #ccc;\"></i><h5>Belum ada data capaian kegiatan</h5><p class=\"text-muted\">Data visualisasi akan muncul setelah evaluasi tersedia.</p></div>';
    }
    // [PENTING] Tambahkan event listener untuk fungsionalitas klik
    myChartKeg1.on('click', function(params) { handleKegiatanChartClick(params, 'detail-indikator-kegiatan-terendah'); });

    var chartDomKeg2 = document.getElementById('$chartIdKegiatanTertinggi');
    if ($dataChartKegiatanTertinggi && $dataChartKegiatanTertinggi.length > 0) {
        var myChartKeg2 = echarts.init(chartDomKeg2);
        var optionKeg2 = getModernPieOption('Indikator Kegiatan - Capaian Tertinggi Semua Triwulan', $dataChartKegiatanTertinggi);
        myChartKeg2.setOption(optionKeg2);
    } else {
        chartDomKeg2.innerHTML = '<div class=\"d-flex align-items-center justify-content-center\" style=\"height: 100%; border: 2px dashed #ddd; border-radius: 10px; background-color: #fcfcfc; color: #888; flex-direction: column;\"><i class=\"fas fa-chart-pie mb-3\" style=\"font-size: 3rem; color: #ccc;\"></i><h5>Belum ada data capaian kegiatan</h5><p class=\"text-muted\">Data visualisasi akan muncul setelah evaluasi tersedia.</p></div>';
    }
    // [PENTING] Tambahkan event listener untuk fungsionalitas klik
    myChartKeg2.on('click', function(params) { handleKegiatanChartClick(params, 'detail-indikator-kegiatan-tertinggi'); });

    window.addEventListener('resize', function() {
    if (myChartKeg1) myChartKeg1.resize();
    if (myChartKeg2) myChartKeg2.resize();
});
");

$dataChartSubKegiatanTertinggi = json_encode($triwulanTertinggiSubkegiatan);
$dataChartSubKegiatanTerendah = json_encode($triwulanTerendahSubkegiatan);
$chartIdSubKegiatanTertinggi = 'triwulan-tertinggi-subkegiatan-pie';
$chartIdSubKegiatanTerendah = 'triwulan-terendah-subkegiatan-pie';

$this->registerJs("
// Fungsi untuk menangani klik pada chart sub kegiatan
function handleSubkegiatanChartClick(params, detailContainerId) {
    var detailContainer = document.getElementById(detailContainerId);
    if (!params.data || !params.data.indikator) return;

    // Ambil semua data dari chart
    var nama = params.data.nama;
    var subkegiatanName = params.data.subkegiatan_name; // Data yang Anda minta
    var indikator = params.data.indikator;
    var capaian = params.value;

    // Tampilkan informasi sesuai permintaan Anda:
    // nama sub kegiatan, uraian indikator, dan capaian
    detailContainer.innerHTML = '<h6>' + nama + '</h6>' +
        '<p class=\"mb-1\"><strong>Sub Kegiatan:</strong> ' + subkegiatanName + '</p>' +
        '<p class=\"mb-1\"><strong>Indikator:</strong> ' + indikator + '</p>' +
        '<p class=\"mb-1\"><strong>Capaian:</strong> ' + capaian + '%</p>';
}
    var chartDomSubKeg1 = document.getElementById('$chartIdSubKegiatanTerendah');
    if ($dataChartSubKegiatanTerendah && $dataChartSubKegiatanTerendah.length > 0) {
        var myChartSubKeg1 = echarts.init(chartDomSubKeg1);
        var optionSubKeg1 = getModernPieOption('Indikator Sub Kegiatan - Capaian Terendah Semua Triwulan', $dataChartSubKegiatanTerendah);
        myChartSubKeg1.setOption(optionSubKeg1);
    } else {
        chartDomSubKeg1.innerHTML = '<div class=\"d-flex align-items-center justify-content-center\" style=\"height: 100%; border: 2px dashed #ddd; border-radius: 10px; background-color: #fcfcfc; color: #888; flex-direction: column;\"><i class=\"fas fa-chart-pie mb-3\" style=\"font-size: 3rem; color: #ccc;\"></i><h5>Belum ada data capaian sub kegiatan</h5><p class=\"text-muted\">Data visualisasi akan muncul setelah evaluasi tersedia.</p></div>';
    }
     // [PENTING] Tambahkan event listener untuk fungsionalitas klik
    myChartSubKeg1.on('click', function(params) { handleSubkegiatanChartClick(params, 'detail-indikator-subkegiatan-terendah'); });

    var chartDomSubKeg2 = document.getElementById('$chartIdSubKegiatanTertinggi');
    if ($dataChartSubKegiatanTertinggi && $dataChartSubKegiatanTertinggi.length > 0) {
        var myChartSubKeg2 = echarts.init(chartDomSubKeg2);
        var optionSubKeg2 = getModernPieOption('Indikator Sub Kegiatan - Capaian Tertinggi Semua Triwulan', $dataChartSubKegiatanTertinggi);
        myChartSubKeg2.setOption(optionSubKeg2);
    } else {
        chartDomSubKeg2.innerHTML = '<div class=\"d-flex align-items-center justify-content-center\" style=\"height: 100%; border: 2px dashed #ddd; border-radius: 10px; background-color: #fcfcfc; color: #888; flex-direction: column;\"><i class=\"fas fa-chart-pie mb-3\" style=\"font-size: 3rem; color: #ccc;\"></i><h5>Belum ada data capaian sub kegiatan</h5><p class=\"text-muted\">Data visualisasi akan muncul setelah evaluasi tersedia.</p></div>';
    }
     // [PENTING] Tambahkan event listener untuk fungsionalitas klik
    myChartSubKeg2.on('click', function(params) { handleSubkegiatanChartClick(params, 'detail-indikator-subkegiatan-tertinggi'); });

    window.addEventListener('resize', function() {
    if (myChartSubKeg1) myChartSubKeg1.resize();
    if (myChartSubKeg2) myChartSubKeg2.resize();
});
");


?>


<div class="esk-portal">
    <section class="esk-portal-section" style="padding-top: 36px;">
        <div class="esk-section-head" style="margin: 8px 0 22px;">
            <span class="eyebrow">Layanan Publik</span>
            <h2>Tabulasi</h2>
            <p>Tabulasi capaian sasaran kinerja per Perangkat Daerah</p>
        </div>

        <div class="esk-pub-grid" style="margin-bottom: 8px;">
            <a class="esk-pub-card active" href="<?= Url::to(['/site/portal-publik-tabulasi']) ?>">
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
    </section>
</div>

<div class="container mt-3">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card shadow-sm border-0">
                <div class="card-header text-white d-flex align-items-center"
                    style="background: radial-gradient(circle, rgba(81, 196, 248, 1) 0%, rgba(0, 81, 196, 0.83) 83%, rgba(0, 81, 196, 1) 100%);">
                    <i class="fas fa-bullseye me-2"></i>
                    <h6 class="mb-0" id="toggleAll" style="cursor: pointer;">Sakip Publik</h6>
                </div>

                <div class="card-body pb-0">
                    <?= \yii\helpers\Html::beginForm(['site/portal-publik-tabulasi'], 'get', ['class' => 'row g-3 align-items-end']); ?>

                    <?= \yii\helpers\Html::hiddenInput('r', 'site/portal-publik-tabulasi') ?>

                    <div class="col-md-3">
                        <?= \yii\helpers\Html::label('<i class="fas fa-calendar-alt me-1"></i> Periode', 'refperiode_id', ['class' => 'form-label']); ?>
                        <?= \yii\helpers\Html::dropDownList(
                            'refperiode_id',
                            $selectedPeriodId,
                            \yii\helpers\ArrayHelper::map($periodeList, 'refperiode_id', 'periode'),
                            ['class' => 'form-control', 'prompt' => 'Pilih Periode']
                        ); ?>
                    </div>

                    <div class="col-md-3">
                        <?= \yii\helpers\Html::label('<i class="fas fa-building me-1"></i> Perangkat Daerah', 'refskpd_id', ['class' => 'form-label']); ?>
                        <?= Html::dropDownList('refskpd_id', $selectedSkpdId, ArrayHelper::map($skpdList, 'refskpd_id', 'nama_skpd'), [
                            'class' => 'form-control',
                            'prompt' => 'Pilih Perangkat Daerah' // Ini akan menghasilkan value "" (kosong)
                        ]) ?>
                    </div>

                    <div class="col-md-3">
                        <?= \yii\helpers\Html::label('<i class="fas fa-list me-1"></i> Halaman Tujuan', 'target_page', ['class' => 'form-label']); ?>
                        <?= \yii\helpers\Html::dropDownList(
                            'target_page',
                            $target_page ?? 'portal-publik-tabulasi',
                            [
                                'portal-publik-tabulasi' => 'Tabulasi',
                                'portal-publik-perencanaan' => 'Perencanaan',
                                'portal-publik-capkin' => 'Capkin'
                            ],
                            ['class' => 'form-control', 'prompt' => 'Pilih Halaman']
                        ); ?>
                    </div>

                    <div class="col-md-3">
                        <?= \yii\helpers\Html::submitButton('<i class="fas fa-search me-1"></i> Tampilkan', [
                            'class' => 'btn btn-success btn-gradient w-100'
                        ]); ?>
                    </div>

                    <?= \yii\helpers\Html::endForm(); ?>
                </div>

                <div class="card shadow-sm mb-4">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-lg-12">
                                <h5 class="fw-bold">Peringkat Kinerja OPD</h5>
                                <p>Lihat peringkat kinerja seluruh Organisasi Perangkat Daerah (OPD) berdasarkan rata-rata capaian Indikator Program.</p>
                                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#rankingModal">
                                    <i class="fas fa-trophy me-2"></i>Tampilkan Peringkat OPD
                                </button>
                            </div>
                        </div>
                    </div>
                    <!--  -->
                    <div class="modal fade" id="rankingModal" tabindex="-1" aria-labelledby="rankingModalLabel" aria-hidden="true">
                        <div class="modal-dialog modal-lg modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="rankingModalLabel">🏆 Peringkat Kinerja OPD</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <p>Peringkat diurutkan berdasarkan **Skor Rata-Rata Capaian** tertinggi, kemudian berdasarkan **Rata-Rata Penyerapan Anggaran** tertinggi.</p>
                                    <div class="table-responsive">
                                        <table class="table table-striped table-hover">
                                            <thead class="table-dark">
                                                <tr>
                                                    <th scope="col" class="text-center">Peringkat</th>
                                                    <th scope="col">Nama OPD</th>
                                                    <th scope="col" class="text-end">Skor Rata-Rata Kinerja</th>
                                                    <th scope="col" class="text-end">Rata-Rata Penyerapan Anggaran</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php if (!empty($opdRanking)): ?>
                                                    <?php foreach ($opdRanking as $item): ?>
                                                        <tr>
                                                            <td class="text-center fw-bold">
                                                                <?php
                                                                if ($item['rank'] == 1) {
                                                                    echo '🥇';
                                                                } elseif ($item['rank'] == 2) {
                                                                    echo '🥈';
                                                                } elseif ($item['rank'] == 3) {
                                                                    echo '🥉';
                                                                } else {
                                                                    echo $item['rank'];
                                                                }
                                                                ?>
                                                            </td>
                                                            <td><?= Html::encode($item['nama_skpd']) ?></td>
                                                            <td class="text-end fw-bold"><?= number_format($item['skor'], 2, ',', '.') ?></td>
                                                            <td class="text-end fw-bold"><?= number_format($item['anggaran'], 2, ',', '.') ?>%</td>
                                                        </tr>
                                                    <?php endforeach; ?>
                                                <?php else: ?>
                                                    <tr>
                                                        <td colspan="4" class="text-center text-muted">Data peringkat tidak tersedia untuk periode ini.</td>
                                                    </tr>
                                                <?php endif; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card-body p-3">
                    <div class="row">
                        <div class="col-lg-12 mb-3">
                            <div class="card shadow-sm border-0">
                                <div class="card-body">
                                    <!-- <h5 class="card-title mb-3">Visualisasi Capaian Indikator</h5> -->

                                    <div class="tabel-wrapper" id="visualisasi-wrapper">
                                        <!-- Tabel 1: Indikator Sasaran -->
                                        <div class="tabel-content" data-index="0">
                                            <h5 class="fw-bold">Visualisasi Capaian Indikator Sasaran</h5>
                                            <div class="row">
                                                <div class="col-lg-6 mb-4">
                                                    <div class="card shadow-sm border-0">
                                                        <div class="card-body">
                                                            <h6 class="card-title">Capaian Terendah</h6>
                                                            <?php
                                                            // Buat map triwulan_id => data atau null
                                                            $mapTerendah = [];
                                                            foreach ($triwulanTerendahPerSkpd as $item) {
                                                                preg_match('/Triwulan (\d+)/', $item['nama'], $matches);
                                                                if (isset($matches[1])) {
                                                                    $mapTerendah[(int)$matches[1]] = $item;
                                                                }
                                                            }

                                                            foreach (range(1, 4) as $triwulanId): ?>
                                                                <?php $item = $mapTerendah[$triwulanId] ?? null; ?>
                                                                <p><strong>SKPD:</strong> <?= ($item && $item['value'] > 0) ? $item['nama'] : 'Belum ada capaian' ?></p>
                                                                <?php if ($item): ?>
                                                                    <p><strong>Indikator:</strong> <?= $item['indikator'] ?></p>
                                                                <?php endif; ?>
                                                            <?php endforeach; ?>
                                                            <div id="<?= $chartIdTerendah ?>" class="chart-container" style="width:100%; height:400px;"></div>

                                                            <div id="detail-indikator-terendah" class="mt-3 p-2 border rounded bg-light" style="min-height: 80px;">
                                                                <p class="text-muted mb-0" style="font-style: italic;">Klik pada salah satu bagian pie chart untuk melihat detail indikator.</p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-lg-6 mb-4">
                                                    <div class="card shadow-sm border-0">
                                                        <div class="card-body">
                                                            <h6 class="card-title">Capaian Tertinggi</h6>
                                                            <?php
                                                            // Buat map triwulan_id => data atau null
                                                            $mapTertinggi = [];
                                                            foreach ($triwulanTertinggiPerSkpd as $item) {
                                                                preg_match('/Triwulan (\d+)/', $item['nama'], $matches);
                                                                if (isset($matches[1])) {
                                                                    $mapTertinggi[(int)$matches[1]] = $item;
                                                                }
                                                            }

                                                            foreach (range(1, 4) as $triwulanId): ?>
                                                                <?php $item = $mapTertinggi[$triwulanId] ?? null; ?>
                                                                <p><strong>SKPD:</strong> <?= ($item && $item['value'] > 0) ? $item['nama'] : 'Belum ada capaian' ?></p>
                                                                <?php if ($item): ?>
                                                                    <p><strong>Indikator:</strong> <?= $item['indikator'] ?></p>
                                                                <?php endif; ?>
                                                            <?php endforeach; ?>
                                                            <div id="<?= $chartIdTertinggi ?>" class="chart-container" style="width:100%; height:400px;"></div>

                                                            <div id="detail-indikator-tertinggi" class="mt-3 p-2 border rounded bg-light" style="min-height: 80px;">
                                                                <p class="text-muted mb-0" style="font-style: italic;">Klik pada salah satu bagian pie chart untuk melihat detail indikator.</p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Tabel 2: Indikator Program -->
                                        <div class="tabel-content d-none" data-index="1">
                                            <h5 class="fw-bold">Visualisasi Capaian Indikator Program</h5>
                                            <div class="row">
                                                <div class="col-md-6 mb-4">
                                                    <div class="card shadow-sm border-0">
                                                        <div class="card-body">
                                                            <h6 class="card-title">Capaian Terendah</h6>
                                                            <?php
                                                            // Buat map triwulan_id => data atau null
                                                            $mapTerendahProgram = [];
                                                            foreach ($triwulanTerendahProgram as $item) {
                                                                preg_match('/Triwulan (\d+)/', $item['nama'], $matches);
                                                                if (isset($matches[1])) {
                                                                    $mapTerendahProgram[(int)$matches[1]] = $item;
                                                                }
                                                            }

                                                            foreach (range(1, 4) as $triwulanId): ?>
                                                                <?php $item = $mapTerendahProgram[$triwulanId] ?? null; ?>
                                                                <p><strong>SKPD:</strong> <?= ($item && $item['value'] > 0) ? $item['nama'] : 'Belum ada capaian' ?></p>
                                                                <?php if ($item): ?>
                                                                    <p><strong>Indikator:</strong> <?= $item['indikator'] ?></p>
                                                                <?php endif; ?>
                                                            <?php endforeach; ?>
                                                            <div id="<?= $chartIdProgramTerendah ?>" class="chart-container" style="width:100%; height:400px;"></div>

                                                            <div id="detail-indikator-program-terendah" class="mt-3 p-2 border rounded bg-light" style="min-height: 80px;">
                                                                <p class="text-muted mb-0" style="font-style: italic;">Klik pada salah satu bagian pie chart untuk melihat detail indikator program.</p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-6 mb-4">
                                                    <div class="card shadow-sm border-0">
                                                        <div class="card-body">
                                                            <h6 class="card-title">Capaian Tertinggi</h6>
                                                            <?php
                                                            // Buat map triwulan_id => data atau null
                                                            $mapTertinggiProgram = [];
                                                            foreach ($triwulanTertinggiProgram as $item) {
                                                                preg_match('/Triwulan (\d+)/', $item['nama'], $matches);
                                                                if (isset($matches[1])) {
                                                                    $mapTertinggiProgram[(int)$matches[1]] = $item;
                                                                }
                                                            }

                                                            foreach (range(1, 4) as $triwulanId): ?>
                                                                <?php $item = $mapTertinggiProgram[$triwulanId] ?? null; ?>
                                                                <p><strong>SKPD:</strong> <?= ($item && $item['value'] > 0) ? $item['nama'] : 'Belum ada capaian' ?></p>
                                                                <?php if ($item): ?>
                                                                    <p><strong>Indikator:</strong> <?= $item['indikator'] ?></p>
                                                                <?php endif; ?>
                                                            <?php endforeach; ?>
                                                            <div id="<?= $chartIdProgramTertinggi ?>" class="chart-container" style="width:100%; height:400px;"></div>

                                                            <div id="detail-indikator-program-tertinggi" class="mt-3 p-2 border rounded bg-light" style="min-height: 80px;">
                                                                <p class="text-muted mb-0" style="font-style: italic;">Klik pada salah satu bagian pie chart untuk melihat detail indikator program.</p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Tabel 2: Indikator Kegiatan -->
                                        <div class="tabel-content d-none" data-index="2">
                                            <h5 class="fw-bold">Visualisasi Capaian Indikator Kegiatan</h5>
                                            <div class="row">
                                                <div class="col-md-6 mb-4">
                                                    <div class="card shadow-sm border-0">
                                                        <div class="card-body">
                                                            <h6 class="card-title">Capaian Terendah</h6>
                                                            <?php
                                                            // Buat map triwulan_id => data atau null
                                                            $mapTerendahKegiatan = [];
                                                            foreach ($triwulanTerendahKegiatan as $item) {
                                                                preg_match('/Triwulan (\d+)/', $item['nama'], $matches);
                                                                if (isset($matches[1])) {
                                                                    $mapTerendahKegiatan[(int)$matches[1]] = $item;
                                                                }
                                                            }

                                                            foreach (range(1, 4) as $triwulanId): ?>
                                                                <?php $item = $mapTerendahKegiatan[$triwulanId] ?? null; ?>
                                                                <p><strong>SKPD:</strong> <?= ($item && $item['value'] > 0) ? $item['nama'] : 'Belum ada capaian' ?></p>
                                                                <?php if ($item): ?>
                                                                    <p><strong>Indikator:</strong> <?= $item['indikator'] ?></p>
                                                                <?php endif; ?>
                                                            <?php endforeach; ?>
                                                            <div id="<?= $chartIdKegiatanTerendah ?>" class="chart-container" style="width:100%; height:400px;"></div>
                                                            <div id="detail-indikator-kegiatan-terendah" class="mt-3 p-2 border rounded bg-light" style="min-height: 80px;">
                                                                <p class="text-muted mb-0" style="font-style: italic;">Klik pada salah satu bagian pie chart untuk melihat detail indikator kegiatan.</p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-6 mb-4">
                                                    <div class="card shadow-sm border-0">
                                                        <div class="card-body">
                                                            <h6 class="card-title">Capaian Tertinggi</h6>
                                                            <?php
                                                            // Buat map triwulan_id => data atau null
                                                            $mapTertinggiKegiatan = [];
                                                            foreach ($triwulanTertinggiKegiatan as $item) {
                                                                preg_match('/Triwulan (\d+)/', $item['nama'], $matches);
                                                                if (isset($matches[1])) {
                                                                    $mapTertinggiKegiatan[(int)$matches[1]] = $item;
                                                                }
                                                            }

                                                            foreach (range(1, 4) as $triwulanId): ?>
                                                                <?php $item = $mapTertinggiKegiatan[$triwulanId] ?? null; ?>
                                                                <p><strong>SKPD:</strong> <?= ($item && $item['value'] > 0) ? $item['nama'] : 'Belum ada capaian' ?></p>
                                                                <?php if ($item): ?>
                                                                    <p><strong>Indikator:</strong> <?= $item['indikator'] ?></p>
                                                                <?php endif; ?>
                                                            <?php endforeach; ?>
                                                            <div id="<?= $chartIdKegiatanTertinggi ?>" class="chart-container" style="width:100%; height:400px;"></div>
                                                            <div id="detail-indikator-kegiatan-tertinggi" class="mt-3 p-2 border rounded bg-light" style="min-height: 80px;">
                                                                <p class="text-muted mb-0" style="font-style: italic;">Klik pada salah satu bagian pie chart untuk melihat detail indikator kegiatan.</p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Tabel 2: Indikator Sub Kegiatan -->
                                        <div class="tabel-content d-none" data-index="3">
                                            <h5 class="fw-bold">Visualisasi Capaian Indikator Sub Kegiatan</h5>
                                            <div class="row">
                                                <div class="col-md-6 mb-4">
                                                    <div class="card shadow-sm border-0">
                                                        <div class="card-body">
                                                            <h6 class="card-title">Capaian Terendah</h6>
                                                            <?php
                                                            // Buat map triwulan_id => data atau null
                                                            $mapTerendahSubKegiatan = [];
                                                            foreach ($triwulanTerendahSubkegiatan as $item) {
                                                                preg_match('/Triwulan (\d+)/', $item['nama'], $matches);
                                                                if (isset($matches[1])) {
                                                                    $mapTerendahSubKegiatan[(int)$matches[1]] = $item;
                                                                }
                                                            }

                                                            foreach (range(1, 4) as $triwulanId):
                                                                $item = $mapTerendahSubKegiatan[$triwulanId] ?? null;
                                                            ?>
                                                                <p>
                                                                    <strong>Sub Kegiatan dengan capaian terendah:</strong>
                                                                    <?= ($item && $item['value'] > 0) ? $item['nama'] : 'Belum ada capaian' ?>
                                                                </p>
                                                                <?php if ($item): ?>
                                                                    <p><strong>Indikator Terendah:</strong> <?= $item['indikator'] ?></p>
                                                                <?php endif; ?>
                                                            <?php endforeach; ?>
                                                            <div id="<?= $chartIdSubKegiatanTerendah  ?>" class="chart-container" style="width:100%; height:400px;"></div>
                                                            <div id="detail-indikator-subkegiatan-terendah" class="mt-3 p-2 border rounded bg-light" style="min-height: 80px;">
                                                                <p class="text-muted mb-0" style="font-style: italic;">Klik pada salah satu bagian pie chart untuk melihat detail indikator sub kegiatan.</p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-6 mb-4">
                                                    <div class="card shadow-sm border-0">
                                                        <div class="card-body">
                                                            <h6 class="card-title">Capaian Tertinggi</h6>
                                                            <?php
                                                            // Buat map triwulan_id => data atau null
                                                            $mapTertinggiSubKegiatan = [];
                                                            foreach ($triwulanTertinggiSubkegiatan as $item) {
                                                                preg_match('/Triwulan (\d+)/', $item['nama'], $matches);
                                                                if (isset($matches[1])) {
                                                                    $mapTertinggiSubKegiatan[(int)$matches[1]] = $item;
                                                                }
                                                            }

                                                            foreach (range(1, 4) as $triwulanId):
                                                                $item = $mapTertinggiSubKegiatan[$triwulanId] ?? null;
                                                            ?>
                                                                <p>
                                                                    <strong>Sub Kegiatan dengan capaian tertinggi:</strong>
                                                                    <?= ($item && $item['value'] > 0) ? $item['nama'] : 'Belum ada capaian' ?>
                                                                </p>
                                                                <?php if ($item): ?>
                                                                    <p><strong>Indikator Tertinggi:</strong> <?= $item['indikator'] ?></p>
                                                                <?php endif; ?>
                                                            <?php endforeach; ?>
                                                            <div id="<?= $chartIdSubKegiatanTertinggi  ?>" class="chart-container" style="width:100%; height:400px;"></div>
                                                            <div id="detail-indikator-subkegiatan-tertinggi" class="mt-3 p-2 border rounded bg-light" style="min-height: 80px;">
                                                                <p class="text-muted mb-0" style="font-style: italic;">Klik pada salah satu bagian pie chart untuk melihat detail indikator sub kegiatan.</p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Tabel 3 & 4: Tambah bagian untuk Kegiatan & Sub Kegiatan jika tersedia -->

                                        <!-- Tombol Navigasi -->
                                        <div class="d-flex justify-content-between mt-2">
                                            <button class="btn btn-outline-primary btn-prev" data-scope="visualisasi-wrapper"><i class="bi bi-arrow-left"></i> Sebelumnya</button>
                                            <button class="btn btn-outline-primary btn-next" data-scope="visualisasi-wrapper">Selanjutnya <i class="bi bi-arrow-right"></i></button>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <script>
                    document.addEventListener("DOMContentLoaded", function() {
                        function resizeCharts(wrapper) {
                            const charts = wrapper.querySelectorAll(".echart, canvas"); // atau selector chart Anda
                            charts.forEach(chartEl => {
                                if (chartEl._chartInstance) {
                                    chartEl._chartInstance.resize?.(); // untuk Chart.js
                                } else if (chartEl.id && echarts.getInstanceByDom(chartEl)) {
                                    echarts.getInstanceByDom(chartEl).resize(); // untuk ECharts
                                }
                            });
                        }

                        document.querySelectorAll(".btn-next").forEach(btn => {
                            btn.addEventListener("click", function() {
                                const scope = this.dataset.scope;
                                const wrapper = document.getElementById(scope);
                                const current = wrapper.querySelector(".tabel-content:not(.d-none)");
                                const next = current.nextElementSibling;
                                if (next && next.classList.contains("tabel-content")) {
                                    current.classList.add("d-none");
                                    next.classList.remove("d-none");

                                    // Resize semua chart yang ada di dalam elemen yang baru tampil
                                    next.querySelectorAll(".chart-container").forEach(chartEl => {
                                        const chartInstance = echarts.getInstanceByDom(chartEl);
                                        if (chartInstance) chartInstance.resize();
                                    });
                                }
                            });
                        });

                        document.querySelectorAll(".btn-prev").forEach(btn => {
                            btn.addEventListener("click", function() {
                                const scope = this.dataset.scope;
                                const wrapper = document.getElementById(scope);
                                const current = wrapper.querySelector(".tabel-content:not(.d-none)");
                                const prev = current.previousElementSibling;
                                if (prev && prev.classList.contains("tabel-content")) {
                                    current.classList.add("d-none");
                                    prev.classList.remove("d-none");
                                    resizeCharts(prev);
                                }
                            });
                        });
                    });
                </script>






                <div class="card-body p-3">
                    <div class="row">
                        <?php if (!empty($displayedSkpdList)): ?>
                            <?php foreach ($displayedSkpdList as $index => $skpd): ?>
                                <?php if ($skpd->refskpd_id == 1) continue; ?>
                                <div class="col-lg-12 mb-3">
                                    <div class="card shadow-sm border-0">
                                        <div class="card-body">
                                            <h5 class="card-title mb-3"><?= Html::encode($skpd->nama_skpd) ?></h5>

                                            <div class="tabel-wrapper" id="tabel-wrapper-<?= $index ?>">
                                                <!-- Tabel 1: Sasaran -->
                                                <div class="tabel-content" data-index="0">
                                                    <p class="fw-bold">Tabel Indikator Sasaran</p>
                                                    <div class="mb-2 clearfix">
                                                        <div class="float-end">
                                                            <strong>Keterangan:</strong>
                                                            <div class="d-flex gap-2 mt-1 flex-wrap">
                                                                <span class="badge bg-danger px-3 py-2">&lt; 70%</span>
                                                                <span class="badge bg-warning text-dark px-3 py-2">70% - 99%</span>
                                                                <span class="badge bg-success px-3 py-2">100%</span>
                                                                <span class="badge bg-primary px-3 py-2">&gt; 100%</span>
                                                            </div>
                                                        </div>
                                                    </div>


                                                    <div class="table-responsive">
                                                        <table class="table table-bordered table-sm table-striped align-middle text-center">
                                                            <thead class="table-secondary">
                                                                <tr>
                                                                    <th colspan="10" class="text-center"><?= Html::encode($skpd->nama_skpd) ?></th>
                                                                </tr>
                                                                <tr>
                                                                    <th>No</th>
                                                                    <th>Indikator Sasaran Renstra</th>
                                                                    <th>Realisasi TW 1</th>
                                                                    <th>Capaian TW 1</th>
                                                                    <th>Realisasi TW 2</th>
                                                                    <th>Capaian TW 2</th>
                                                                    <th>Realisasi TW 3</th>
                                                                    <th>Capaian TW 3</th>
                                                                    <th>Realisasi TW 4</th>
                                                                    <th>Capaian TW 4</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                <?php
                                                                $renstraData = $dataRenstra[$skpd->refskpd_id] ?? [];
                                                                $no = 1;
                                                                foreach ($renstraData as $sasaranItem):
                                                                    $indikatorList = $sasaranItem['indikator'];
                                                                    $sasaranRowspan = count($indikatorList) > 0 ? count($indikatorList) : 1;
                                                                    foreach ($indikatorList as $index => $indikatorItem):
                                                                ?>
                                                                        <tr>

                                                                            <?php if ($index === 0): ?>
                                                                                <td rowspan="<?= $sasaranRowspan  ?>"><?= $no++ ?></td>
                                                                            <?php endif; ?>

                                                                            <td class="text-start" style="white-space: normal;">
                                                                                <a href="#" class="text-decoration-none indicator-link"
                                                                                    data-bs-toggle="modal"
                                                                                    data-bs-target="#programModal"
                                                                                    data-indicator-text="<?= Html::encode($indikatorItem['uraian_indikator']) ?>"
                                                                                    data-programs="<?= Html::encode(json_encode($indikatorItem['programs'])) ?>">
                                                                                    <?= Html::encode($indikatorItem['uraian_indikator']) ?>
                                                                                </a>
                                                                            </td>

                                                                            <?php
                                                                            foreach ($indikatorItem['triwulan'] as $tw) {
                                                                                $realisasi = Html::encode($tw['realisasi']);
                                                                                $capaian = Html::encode($tw['capaian']);
                                                                                // $satuan = Html::encode($tw->refIndikatorsasaranrenstra->indikatorsasaranrenstra_satuan ?? '');

                                                                                echo '<td>' . ($realisasi !== '' ? $realisasi : '-') . '</td>';

                                                                                if ($realisasi === '' || $realisasi === null || $realisasi == 0) {
                                                                                    echo '<td><span class="badge bg-danger">Belum ada capaian</span></td>';
                                                                                } else {
                                                                                    $colorClass = '';

                                                                                    if ($capaian < 70) {
                                                                                        $colorClass = 'bg-danger text-white';
                                                                                    } elseif ($capaian >= 70 && $capaian < 100) {
                                                                                        $colorClass = 'bg-warning text-dark';
                                                                                    } elseif ($capaian == 100) {
                                                                                        $colorClass = 'bg-success text-white';
                                                                                    } elseif ($capaian > 100) {
                                                                                        $colorClass = 'bg-primary text-white';
                                                                                    }

                                                                                    echo '<td><span class="badge ' . $colorClass . '">' . Html::encode($capaian) . '%</span></td>';
                                                                                }
                                                                            }
                                                                            ?>
                                                                        </tr>
                                                                <?php endforeach;
                                                                endforeach; ?>
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>

                                                <!-- Tabel 2: Program -->
                                                <div class="tabel-content d-none" data-index="1">
                                                    <p class="fw-bold">Tabel Indikator Program</p>
                                                    <div class="mb-2 clearfix">
                                                        <div class="float-end">
                                                            <strong>Keterangan:</strong>
                                                            <div class="d-flex gap-2 mt-1 flex-wrap">
                                                                <span class="badge bg-danger px-3 py-2">&lt; 70%</span>
                                                                <span class="badge bg-warning text-dark px-3 py-2">70% - 99%</span>
                                                                <span class="badge bg-success px-3 py-2">100%</span>
                                                                <span class="badge bg-primary px-3 py-2">&gt; 100%</span>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="table-responsive">
                                                        <table class="table table-bordered table-sm table-striped align-middle text-center">
                                                            <thead class="table-secondary">
                                                                <tr>
                                                                    <th colspan="10" class="text-center"><?= Html::encode($skpd->nama_skpd) ?></th>
                                                                </tr>
                                                                <tr>
                                                                    <th>No</th>
                                                                    <th>Indikator Program</th>
                                                                    <th>Realisasi TW 1</th>
                                                                    <th>Capaian TW 1</th>
                                                                    <th>Realisasi TW 2</th>
                                                                    <th>Capaian TW 2</th>
                                                                    <th>Realisasi TW 3</th>
                                                                    <th>Capaian TW 3</th>
                                                                    <th>Realisasi TW 4</th>
                                                                    <th>Capaian TW 4</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                <?php
                                                                $programData = $dataProgram[$skpd->refskpd_id] ?? [];
                                                                $no = 1;

                                                                foreach ($programData as $programItem):
                                                                    $indikatorList = $programItem['indikator'];
                                                                    $programRowspan = count($indikatorList);

                                                                    foreach ($indikatorList as $index => $indikatorItem):
                                                                ?>
                                                                        <tr>
                                                                            <?php if ($index === 0): ?>
                                                                                <td rowspan="<?= $programRowspan ?>"><?= $no++ ?></td>
                                                                            <?php endif; ?>

                                                                            <td class="text-start" style="white-space: normal;">
                                                                                <a href="#" class="text-decoration-none program-indicator-link"
                                                                                    data-bs-toggle="modal"
                                                                                    data-bs-target="#detailProgramModal"
                                                                                    data-indicator-text="<?= Html::encode($indikatorItem['uraian_indikator']) ?>"
                                                                                    data-program-name="<?= Html::encode($programItem['nama_program']) ?>"
                                                                                    data-program-budget="<?= $programItem['total_anggaran'] ?>"
                                                                                    data-kegiatans="<?= Html::encode(json_encode($programItem['kegiatans'])) ?>"> <?= Html::encode($indikatorItem['uraian_indikator']) ?>
                                                                                </a>
                                                                            </td>
                                                                            <?php
                                                                            foreach ($indikatorItem['triwulan'] as $tw) {
                                                                                $realisasi = Html::encode($tw->triwulan_realisasi);
                                                                                $capaian = Html::encode($tw->triwulan_capaian);
                                                                                $satuan = Html::encode($indikatorItem['program_satuan'] ?? '');

                                                                                echo '<td>' . ($realisasi !== '' ? $realisasi : '-') . '</td>';

                                                                                if ($realisasi === '' || $realisasi === null || $realisasi == 0) {
                                                                                    echo '<td><span class="badge bg-danger">Belum ada capaian</span></td>';
                                                                                } else {
                                                                                    $colorClass = '';

                                                                                    if ($capaian < 70) {
                                                                                        $colorClass = 'bg-danger text-white';
                                                                                    } elseif ($capaian >= 70 && $capaian < 100) {
                                                                                        $colorClass = 'bg-warning text-dark';
                                                                                    } elseif ($capaian == 100) {
                                                                                        $colorClass = 'bg-success text-white';
                                                                                    } elseif ($capaian > 100) {
                                                                                        $colorClass = 'bg-primary text-white';
                                                                                    }

                                                                                    echo '<td><span class="badge ' . $colorClass . '">' . Html::encode($capaian) . '%</span></td>';
                                                                                }
                                                                            }

                                                                            ?>
                                                                        </tr>
                                                                <?php endforeach;
                                                                endforeach; ?>
                                                            </tbody>

                                                        </table>
                                                    </div>
                                                </div>

                                                <!-- Tabel 3: Kegiatan -->
                                                <div class="tabel-content d-none" data-index="2">
                                                    <p class="fw-bold">Tabel Indikator Kegiatan</p>
                                                    <div class="mb-2 clearfix">
                                                        <div class="float-end">
                                                            <strong>Keterangan:</strong>
                                                            <div class="d-flex gap-2 mt-1 flex-wrap">
                                                                <span class="badge bg-danger px-3 py-2">&lt; 70%</span>
                                                                <span class="badge bg-warning text-dark px-3 py-2">70% - 99%</span>
                                                                <span class="badge bg-success px-3 py-2">100%</span>
                                                                <span class="badge bg-primary px-3 py-2">&gt; 100%</span>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="table-responsive">
                                                        <table class="table table-bordered table-sm table-striped align-middle text-center">
                                                            <thead class="table-secondary">
                                                                <tr>
                                                                    <th colspan="10" class="text-center"><?= Html::encode($skpd->nama_skpd) ?></th>
                                                                </tr>
                                                                <tr>
                                                                    <th>No</th>
                                                                    <th>Indikator Kegiatan</th>
                                                                    <th>Realisasi TW 1</th>
                                                                    <th>Capaian TW 1</th>
                                                                    <th>Realisasi TW 2</th>
                                                                    <th>Capaian TW 2</th>
                                                                    <th>Realisasi TW 3</th>
                                                                    <th>Capaian TW 3</th>
                                                                    <th>Realisasi TW 4</th>
                                                                    <th>Capaian TW 4</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                <?php
                                                                $kegiatanData = $dataKegiatan[$skpd->refskpd_id] ?? [];

                                                                $no = 1;

                                                                foreach ($kegiatanData as $kegiatanItem):
                                                                    $indikatorList = $kegiatanItem['indikator'];
                                                                    $kegiatanRowspan = count($indikatorList) > 0 ? count($indikatorList) : 1;
                                                                    foreach ($indikatorList as $index => $indikatorItem):
                                                                ?>
                                                                        <tr>

                                                                            <?php if ($index === 0): ?>
                                                                                <td rowspan="<?= $kegiatanRowspan ?>"><?= $no++ ?></td>
                                                                            <?php endif; ?>


                                                                            <td class="text-start" style="white-space: normal;">
                                                                                <a href="#" class="text-decoration-none kegiatan-indicator-link"
                                                                                    data-bs-toggle="modal"
                                                                                    data-bs-target="#detailKegiatanModal"
                                                                                    data-indicator-text="<?= Html::encode($indikatorItem['uraian_indikator']) ?>"
                                                                                    data-kegiatan-name="<?= Html::encode($kegiatanItem['nama_kegiatan']) ?>"
                                                                                    data-kegiatan-budget="<?= $kegiatanItem['total_anggaran'] ?>"
                                                                                    data-subkegiatans="<?= Html::encode(json_encode($kegiatanItem['subkegiatans'])) ?>"> <?= Html::encode($indikatorItem['uraian_indikator']) ?>
                                                                                </a>
                                                                            </td>
                                                                            <?php
                                                                            foreach ($indikatorItem['triwulan'] as $tw) {
                                                                                $realisasi = Html::encode($tw->triwulan_realisasi);
                                                                                $capaian = Html::encode($tw->triwulan_capaian);
                                                                                $satuan = Html::encode($indikatorItem['kegiatan_satuan'] ?? '');

                                                                                echo '<td>' . ($realisasi !== '' ? $realisasi : '-') . '</td>';

                                                                                if ($realisasi === '' || $realisasi === null || $realisasi == 0) {
                                                                                    echo '<td><span class="badge bg-danger">Belum ada capaian</span></td>';
                                                                                } else {
                                                                                    $colorClass = '';

                                                                                    if ($capaian < 70) {
                                                                                        $colorClass = 'bg-danger text-white';
                                                                                    } elseif ($capaian >= 70 && $capaian < 100) {
                                                                                        $colorClass = 'bg-warning text-dark';
                                                                                    } elseif ($capaian == 100) {
                                                                                        $colorClass = 'bg-success text-white';
                                                                                    } elseif ($capaian > 100) {
                                                                                        $colorClass = 'bg-primary text-white';
                                                                                    }

                                                                                    echo '<td><span class="badge ' . $colorClass . '"><strong>' . $capaian . '</strong> (' . $satuan . ')</span></td>';
                                                                                }
                                                                            }
                                                                            ?>
                                                                        </tr>
                                                                <?php endforeach;
                                                                endforeach; ?>
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>

                                                <!-- Tabel 4: Sub Kegiatan -->
                                                <div class="tabel-content d-none" data-index="3">
                                                    <p class="fw-bold">Tabel Indikator Sub Kegiatan</p>
                                                    <div class="mb-2 clearfix">
                                                        <div class="float-end">
                                                            <strong>Keterangan:</strong>
                                                            <div class="d-flex gap-2 mt-1 flex-wrap">
                                                                <span class="badge bg-danger px-3 py-2">&lt; 70%</span>
                                                                <span class="badge bg-warning text-dark px-3 py-2">70% - 99%</span>
                                                                <span class="badge bg-success px-3 py-2">100%</span>
                                                                <span class="badge bg-primary px-3 py-2">&gt; 100%</span>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="table-responsive">
                                                        <table class="table table-bordered table-sm table-striped align-middle text-center">
                                                            <thead class="table-secondary">
                                                                <tr>
                                                                    <th colspan="10" class="text-center"><?= Html::encode($skpd->nama_skpd) ?></th>
                                                                </tr>
                                                                <tr>
                                                                    <th>No</th>
                                                                    <th>Indikator Sub Kegiatan</th>
                                                                    <th>Realisasi TW 1</th>
                                                                    <th>Capaian TW 1</th>
                                                                    <th>Realisasi TW 2</th>
                                                                    <th>Capaian TW 2</th>
                                                                    <th>Realisasi TW 3</th>
                                                                    <th>Capaian TW 3</th>
                                                                    <th>Realisasi TW 4</th>
                                                                    <th>Capaian TW 4</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                <?php
                                                                $subkegiatanData = $dataSubkegiatan[$skpd->refskpd_id] ?? [];

                                                                $no = 1;

                                                                foreach ($subkegiatanData as $subkegiatanItem):
                                                                    $indikatorList = $subkegiatanItem['indikator'];
                                                                    $subkegiatanRowspan = count($indikatorList) > 0 ? count($indikatorList) : 1;
                                                                    foreach ($indikatorList as $index => $indikatorItem):
                                                                ?>
                                                                        <tr>
                                                                            <?php if ($index === 0): ?>
                                                                                <td rowspan="<?= $subkegiatanRowspan ?>"><?= $no++ ?></td>
                                                                            <?php endif; ?>

                                                                            <td class="text-start" style="white-space: normal;">
                                                                                <a href="#" class="text-decoration-none subkegiatan-indicator-link"
                                                                                    data-bs-toggle="modal"
                                                                                    data-bs-target="#detailSubkegiatanModal"
                                                                                    data-indicator-text="<?= Html::encode($indikatorItem['uraian_indikator']) ?>"
                                                                                    data-subkegiatan-name="<?= Html::encode($subkegiatanItem['nama_subkegiatan']) ?>"
                                                                                    data-subkegiatan-budget="<?= $subkegiatanItem['total_anggaran'] ?>">
                                                                                    <?= Html::encode($indikatorItem['uraian_indikator']) ?>
                                                                                </a>
                                                                            </td>
                                                                            <?php
                                                                            foreach ($indikatorItem['triwulan'] as $tw) {
                                                                                $realisasi = Html::encode($tw->triwulan_realisasi);
                                                                                $capaian = Html::encode($tw->triwulan_capaian);
                                                                                $satuan = Html::encode($indikatorItem['subkegiatan_satuan'] ?? '');

                                                                                echo '<td>' . ($realisasi !== '' ? $realisasi : '-') . '</td>';

                                                                                if ($realisasi === '' || $realisasi === null || $realisasi == 0) {
                                                                                    echo '<td><span class="badge bg-danger">Belum ada capaian</span></td>';
                                                                                } else {
                                                                                    $colorClass = '';

                                                                                    if ($capaian < 70) {
                                                                                        $colorClass = 'bg-danger text-white';
                                                                                    } elseif ($capaian >= 70 && $capaian < 100) {
                                                                                        $colorClass = 'bg-warning text-dark';
                                                                                    } elseif ($capaian == 100) {
                                                                                        $colorClass = 'bg-success text-white';
                                                                                    } elseif ($capaian > 100) {
                                                                                        $colorClass = 'bg-primary text-white';
                                                                                    }

                                                                                    echo '<td><span class="badge ' . $colorClass . '"><strong>' . $capaian . '</strong> (' . $satuan . ')</span></td>';
                                                                                }
                                                                            }

                                                                            ?>
                                                                        </tr>
                                                                <?php endforeach;
                                                                endforeach; ?>
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>

                                                <!-- Tombol Navigasi -->
                                                <div class="d-flex justify-content-between mt-2">
                                                    <button class="btn btn-outline-primary btn-prev" data-scope="<?= $index ?>"><i class="bi bi-arrow-left"></i> Sebelumnya</button>
                                                    <button class="btn btn-outline-primary btn-next" data-scope="<?= $index ?>">Selanjutnya <i class="bi bi-arrow-right"></i></button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="col-lg-12">
                                <div class="alert alert-warning text-center">Data SKPD tidak tersedia.</div>
                            </div>
                        <?php endif; ?>
                        <!-- Modal Sasaran -->
                        <div class="modal fade" id="programModal" tabindex="-1" aria-labelledby="programModalLabel" aria-hidden="true">
                            <div class="modal-dialog modal-lg">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="programModalLabel">Detail Rencana Aksi Terkait Indikator Sasaran Renstra</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body" id="programModalBody">
                                        <p>Memuat data...</p>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!--  -->
                        <!-- Modal Program -->
                        <div class="modal fade" id="detailProgramModal" tabindex="-1" aria-labelledby="detailProgramModalLabel" aria-hidden="true">
                            <div class="modal-dialog modal-lg">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="detailProgramModalLabel">Detail Rencana Aksi Terkait Indikator Program</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body" id="detailProgramModalBody">
                                        <p>Memuat data...</p>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!--  -->
                        <!-- Modal Kegiatan -->
                        <div class="modal fade" id="detailKegiatanModal" tabindex="-1" aria-labelledby="detailKegiatanModalLabel" aria-hidden="true">
                            <div class="modal-dialog modal-lg">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="detailKegiatanModalLabel">Detail Rencana Aksi Terkait Indikator Kegiatan</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body" id="detailKegiatanModalBody">
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!--  -->
                        <!-- Modal Sub Kegiatan -->
                        <div class="modal fade" id="detailSubkegiatanModal" tabindex="-1" aria-labelledby="detailSubkegiatanModalLabel" aria-hidden="true">
                            <div class="modal-dialog modal-lg">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="detailSubkegiatanModalLabel">Detail Rencana Aksi Terkait Indikator Sub Kegiatan</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body" id="detailSubkegiatanModalBody">
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!--  -->
                    </div>
                </div>
                <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        const wrappers = document.querySelectorAll('.tabel-wrapper');

                        wrappers.forEach(wrapper => {
                            const scopeIndex = wrapper.id.split('-').pop();
                            const contents = wrapper.querySelectorAll('.tabel-content');
                            let currentIndex = 0;

                            wrapper.querySelector(`.btn-prev`).addEventListener('click', () => {
                                contents[currentIndex].classList.add('d-none');
                                currentIndex = (currentIndex - 1 + contents.length) % contents.length;
                                contents[currentIndex].classList.remove('d-none');
                            });

                            wrapper.querySelector(`.btn-next`).addEventListener('click', () => {
                                contents[currentIndex].classList.add('d-none');
                                currentIndex = (currentIndex + 1) % contents.length;
                                contents[currentIndex].classList.remove('d-none');
                            });
                        });
                    });
                </script>


                <!-- CSS Styling -->
                <style>
                    .table-sm th,
                    .table-sm td {
                        font-size: 12px;
                        /* Memperkecil ukuran font */
                        padding: 4px 8px;
                        /* Memperkecil padding untuk membuat tabel lebih kecil */
                    }

                    /* Membuat konten dalam tabel dapat melakukan word wrapping */
                    .table td,
                    .table th {
                        white-space: normal !important;
                        word-wrap: break-word;
                    }

                    /* Responsif untuk ukuran layar kecil */
                    @media (max-width: 768px) {
                        .table-responsive {
                            overflow-x: auto;
                        }

                        .table-sm th,
                        .table-sm td {
                            font-size: 10px;
                            /* Ukuran font lebih kecil untuk perangkat mobile */
                            padding: 3px 5px;
                            /* Padding lebih kecil pada perangkat mobile */
                        }
                    }
                </style>



            </div>
        </div>
    </div>
</div>

<style>
    .btn-gradient {
        background: radial-gradient(circle, rgba(81, 196, 248, 1) 0%, rgba(0, 81, 196, 0.83) 83%, rgba(0, 81, 196, 1) 100%);
        color: #fff;
        border: none;
        transition: all 0.3s ease;
    }

    .btn-gradient:hover {
        background: radial-gradient(circle, rgba(0, 81, 196, 1) 0%, rgba(81, 196, 248, 1) 100%);
        color: #fff;
        transform: scale(1.05);
    }

    .form-label i {
        color: #007bff;
    }

    .table th,
    .table td {
        vertical-align: middle;
    }
</style>