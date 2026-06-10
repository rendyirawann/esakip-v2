<style>
    .kegiatan-event {
        background-color: skyblue;
        color: white;
        /* Atur warna latar belakang untuk kegiatan */
        /* Tambahkan gaya lain sesuai kebutuhan */
    }

    .subkegiatan-event {
        background-color: green;
        /* Atur warna latar belakang untuk subkegiatan */
        /* Tambahkan gaya lain sesuai kebutuhan */
    }

    /* Warna latar belakang untuk lingkaran penanda warna */
    .event-dot {
        display: inline-block;
        width: 10px;
        height: 10px;
        border-radius: 50%;
        margin-right: 5px;
    }

    /* Warna latar belakang untuk lingkaran penanda warna pada kegiatan */
    .skyblue {
        background-color: skyblue;
    }

    /* Warna latar belakang untuk lingkaran penanda warna pada subkegiatan */
    .green {
        background-color: green;
    }
</style>
<?php

use edofre\fullcalendar\Fullcalendar;
use yii\helpers\Json;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\JsExpression;

/** @var yii\web\View $this */
/** @var frontend\models\search\SakipCascadingprogramSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'SIMONA - Perencanaan';
$this->params['breadcrumbs'][] = $this->title;

// Ambil data SimonaCascadingkegiatan dari database
$simonakegiatans = \frontend\models\SimonaCascadingkegiatan::find()->all();

// Ambil data SimonaCascadingsubkegiatan dari database
$simonasubkegiatans = \frontend\models\SimonaCascadingsubkegiatan::find()->all();

// Inisialisasi array untuk menyimpan data acara kalender
$events = [];

// Iterasi melalui data simonakegiatans
foreach ($simonakegiatans as $simonakegiatan) {
    // Ambil data cascadingkegiatan berdasarkan refcascadingkegiatan_id pada simonakegiatan
    $cascadingkegiatan = \frontend\models\SakipCascadingkegiatan::findOne($simonakegiatan->refcascadingkegiatan_id);
    // Pastikan cascadingkegiatan ditemukan sebelum melanjutkan
    if ($cascadingkegiatan) {
        // Buat judul acara yang mencakup nama cascadingkegiatan dan nama kegiatan
        $judulAcara = 'Tahapan Kegiatan: ' . $cascadingkegiatan->refKegiatan->nama_kegiatan . ' - ' . $simonakegiatan->nama_tahapankegiatan;

        // Tentukan kelas CSS tambahan untuk kegiatan
        $cssClass = 'kegiatan-event';

        // Ubah format waktuMulai dan waktuSelesai ke dalam format yang diterima oleh widget kalender
        $event = [
            'id' => $simonakegiatan->refsimonacascadingkegiatan_id,
            'title' => $judulAcara, // Judul acara dengan nama kategori dan nama tahapan
            'start' => $simonakegiatan->date_start, // Waktu mulai acara
            'end' => $simonakegiatan->expired_date, // Waktu selesai acara
            'className' => $cssClass, // Tambahkan kelas CSS tambahan untuk tahapan
            // Anda bisa menambahkan properti acara lainnya di sini sesuai kebutuhan
        ];

        // Tambahkan acara ke dalam array events
        $events[] = $event;
    }
}

// Iterasi melalui data simonasubkegiatans
foreach ($simonasubkegiatans as $simonasubkegiatan) {
    // Ambil data kategori berdasarkan kategori_id pada kegiatan
    $cascadingsubkegiatan = \frontend\models\SakipCascadingsubkegiatan::findOne($simonasubkegiatan->refcascadingsubkegiatan_id);
    // Pastikan cascadingsubkegiatan ditemukan sebelum melanjutkan
    if ($cascadingsubkegiatan) {
        // Buat judul acara yang mencakup nama cascadingsubkegiatan dan nama subkegiatan
        $judulAcara = 'Tahapan Sub Kegiatan: ' . $cascadingsubkegiatan->refSubkegiatan->nama_subkegiatan . ' - ' . $simonasubkegiatan->nama_tahapansubkegiatan;

        // Tentukan kelas CSS tambahan untuk kegiatan
        $cssClass = 'subkegiatan-event';

        // Ubah format tanggalKegiatan dan tanggalSelesai ke dalam format yang diterima oleh widget kalender
        $eventSubkegiatan = [
            'id' => $simonasubkegiatan->refsimonacascadingsubkegiatan_id,
            'title' => $judulAcara, // Judul acara dengan nama kategori dan nama kegiatan
            'start' => $simonasubkegiatan->date_start, // Waktu mulai acara
            'end' => $simonasubkegiatan->expired_date, // Waktu selesai acara
            'className' => $cssClass, // Tambahkan kelas CSS tambahan untuk kegiatan
            // Anda bisa menambahkan properti acara lainnya di sini sesuai kebutuhan
        ];

        // Tambahkan acara ke dalam array events
        $events[] = $eventSubkegiatan;
    }
}



// Buat widget kalender dengan data acara yang sudah disiapkan
$calendar = Fullcalendar::widget([
    'events' => $events,
]);

$js = <<< JS
// Menggunakan event 'eventClick' adalah cara yang lebih modern dan direkomendasikan
// daripada 'dayClick', karena ini hanya aktif saat sebuah acara diklik.
$('#calendar').fullCalendar('option', 'eventClick', function(event, jsEvent, view) {
    
    var modal = $('#eventModal');
    var modalTitle = modal.find('.modal-title');
    var modalBody = modal.find('.modal-body');

    // Set judul modal berdasarkan judul acara
    // Kita hapus prefix "Tahapan Kegiatan: " agar lebih bersih
    var cleanTitle = event.title.replace(/^(Tahapan Kegiatan: |Tahapan Sub Kegiatan: )/, '');
    modalTitle.html('<i class="fas fa-calendar-alt me-2"></i>' + cleanTitle);

    // Bangun konten HTML yang baru dan lebih rapi
    var eventDetails = '<dl class="row" style="margin-bottom: 0;">';
    
    // Menentukan Tipe berdasarkan kelas CSS yang Anda set sebelumnya
    var tipeAcara = event.className.includes('kegiatan-event') ? 'Kegiatan' : 'Sub Kegiatan';
    var tipeBadge = event.className.includes('kegiatan-event') 
        ? '<span class="badge bg-primary">KEGIATAN</span>' 
        : '<span class="badge bg-success">SUB KEGIATAN</span>';

    eventDetails += '<dt class="col-sm-4">Tipe</dt>';
    eventDetails += '<dd class="col-sm-8">' + tipeBadge + '</dd>';

    eventDetails += '<dt class="col-sm-4">Nama Tahapan</dt>';
    eventDetails += '<dd class="col-sm-8">' + cleanTitle + '</dd>';

    eventDetails += '<dt class="col-sm-4">Tanggal Mulai</dt>';
    eventDetails += '<dd class="col-sm-8">' + moment(event.start).format('DD MMMM YYYY') + '</dd>';
    
    eventDetails += '<dt class="col-sm-4">Tanggal Selesai</dt>';
    if (event.end) {
        // FullCalendar seringkali membuat tanggal selesai menjadi eksklusif, jadi kita kurangi 1 hari untuk tampilan
        var displayEnd = moment(event.end).subtract(1, 'day');
        eventDetails += '<dd class="col-sm-8">' + displayEnd.format('DD MMMM YYYY') + '</dd>';
    } else {
        eventDetails += '<dd class="col-sm-8">' + moment(event.start).format('DD MMMM YYYY') + '</dd>'; // Jika tidak ada end, samakan dengan start
    }

    eventDetails += '</dl>';

    // Masukkan konten ke modal dan tampilkan
    $('#eventDetails').html(eventDetails);
    $('#eventModal').modal('show');
});
JS;

// Daftarkan kode JavaScript ke dalam view
$this->registerJsFile('@web/js/jquery.min.js', ['depends' => [\yii\web\JqueryAsset::class]]);
$this->registerJs($js);
?>
<div class="pc-container">
    <div class="pc-content">
        <!-- [ breadcrumb ] start -->
        <div class="page-header">
            <div class="page-block">
                <div class="row align-items-center">
                    <div class="col-md-12">
                        <ul class="breadcrumb">
                            <li class="breadcrumb-item"><a href="<?= Url::to(['/site/index']) ?>">Home</a></li>
                            <li class="breadcrumb-item" aria-current="page">Data Perencanaan</li>
                        </ul>
                    </div>
                    <div class="col-md-12">
                        <div class="page-header-title">
                            <h2 class="mb-0">Data Perencanaan</h2>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- [ breadcrumb ] end -->


        <div class="row">
            <!-- start row -->
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header" style="background-color: #04A9F5; padding: 8px;">
                        <h6 style="color: white; margin: 0; cursor: pointer;" id="toggleAll">
                            <i class="fas fa-pen-fancy"></i> Data Perencanaan - <?= Html::decode($nama_skpd) ?>
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-sm-3">

                            </div>
                        </div>
                    </div>
                </div>
                <!-- End Card -->

                <!-- Table Start -->
                <!-- [ Main Content ] start -->
                <div class="row">
                    <!-- [ sample-page ] start -->
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="row" id="wrap">
                                    <div class="col-xxl-3 box-col-4e">
                                        <!-- <div class="md-sidebar mb-3"><a class="btn btn-primary md-sidebar-toggle" href="javascript:void(0)">calendar filter</a>a -->
                                        <div class="md-sidebar-aside job-left-aside custom-scrollbar">
                                            <div id="external-events">
                                                <h3>Hints</h3>
                                                <div id="external-events-list">
                                                    <div class="fc-event fc-h-event fc-daygrid-event fc-daygrid-block-event kegiatan" style="background:#1f2f3e">
                                                        <div class="fc-event-main" style="color: white;"> <span class="event-dot skyblue"></span>Kegiatan</div>
                                                    </div>
                                                    <div class="fc-event fc-h-event fc-daygrid-event fc-daygrid-block-event subkegiatan" style="background:#1f2f3e">
                                                        <div class="fc-event-main" style="color: white;"> <span class="event-dot green"></span>Sub Kegiatan</div>
                                                    </div>

                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-xxl-12 box-col-12">
                                    <div class="calendar-default" id="calendar-container">
                                        <div id="calendar"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- [ sample-page ] end -->
            </div>
            <!-- Table end -->
        </div>
        <!-- end row -->
    </div>


    <!-- [ Main Content ] end -->
</div>
</div>

<div class="modal fade" id="eventModal" tabindex="-1" role="dialog" aria-labelledby="eventModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="eventModalLabel">Detail Kegiatan/Sub Kegiatan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body p-4" id="eventDetails">
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="calendar-modal" data-bs-keyboard="false" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="calendar-modal-title f-w-600 text-truncate">Modal title</h3>
                <a href="#" class="avtar avtar-s btn-link-danger btn-pc-default" data-bs-dismiss="modal">
                    <i class="ti ti-x f-20"></i>
                </a>
            </div>
            <div class="modal-body">
                <div class="d-flex">
                    <div class="flex-shrink-0">
                        <div class="avtar avtar-xs bg-light-secondary">
                            <i class="ti ti-heading f-20"></i>
                        </div>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h5 class="mb-1"><b>Title</b></h5>
                        <p class="pc-event-title text-muted"></p>
                    </div>
                </div>
                <div class="d-flex">
                    <div class="flex-shrink-0">
                        <div class="avtar avtar-xs bg-light-warning">
                            <i class="ti ti-map-pin f-20"></i>
                        </div>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h5 class="mb-1"><b>Venue</b></h5>
                        <p class="pc-event-venue text-muted"></p>
                    </div>
                </div>
                <div class="d-flex">
                    <div class="flex-shrink-0">
                        <div class="avtar avtar-xs bg-light-danger">
                            <i class="ti ti-calendar-event f-20"></i>
                        </div>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h5 class="mb-1"><b>Date</b></h5>
                        <p class="pc-event-date text-muted"></p>
                    </div>
                </div>
                <div class="d-flex">
                    <div class="flex-shrink-0">
                        <div class="avtar avtar-xs bg-light-primary">
                            <i class="ti ti-file-text f-20"></i>
                        </div>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h5 class="mb-1"><b>Description</b></h5>
                        <p class="pc-event-description text-muted"></p>
                    </div>
                </div>
            </div>
            <div class="modal-footer justify-content-between">
                <ul class="list-inline me-auto mb-0">
                    <li class="list-inline-item align-bottom">
                        <a href="#" id="pc_event_remove" class="avtar avtar-s btn-link-danger btn-pc-default w-sm-auto" data-bs-toggle="tooltip"
                            title="Delete">
                            <i class="ti ti-trash f-18"></i>
                        </a>
                    </li>
                    <li class="list-inline-item align-bottom">
                        <a href="#" id="pc_event_edit" class="avtar avtar-s btn-link-success btn-pc-default" data-bs-toggle="tooltip"
                            title="Edit">
                            <i class="ti ti-edit-circle f-18"></i>
                        </a>
                    </li>
                </ul>
                <div class="flex-grow-1 text-end">
                    <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="offcanvas offcanvas-end cal-event-offcanvas" tabindex="-1" id="calendar-add_edit_event">
    <div class="offcanvas-header">
        <h3 class="f-w-600 text-truncate">Add Events</h3>
        <a href="#" class="avtar avtar-s btn-link-danger btn-pc-default" data-bs-dismiss="offcanvas">
            <i class="ti ti-x f-20"></i>
        </a>
    </div>
    <div class="offcanvas-body">
        <form id="pc-form-event" novalidate>
            <div class="form-group">
                <label class="form-label">Title</label>
                <input type="email" class="form-control" id="pc-e-title" placeholder="Enter event title" autofocus>
            </div>
            <div class="form-group">
                <label class="form-label">Venue</label>
                <input type="email" class="form-control" id="pc-e-venue" placeholder="Enter event venue">
            </div>
            <div class="form-group m-0">
                <input type="hidden" class="form-control" id="pc-e-sdate">
                <input type="hidden" class="form-control" id="pc-e-edate">
            </div>
            <div class="form-group">
                <label class="form-label">Description</label>
                <textarea class="form-control" placeholder="Enter event description" rows="3"
                    id="pc-e-description"></textarea>
            </div>
            <div class="form-group">
                <label class="form-label">Type</label>
                <select class="form-select" id="pc-e-type">
                    <option value="empty" selected>Type</option>
                    <option value="event-primary">Primary</option>
                    <option value="event-secondary">Secondary</option>
                    <option value="event-success">Success</option>
                    <option value="event-danger">Danger</option>
                    <option value="event-warning">Warning</option>
                    <option value="event-info">Info</option>
                </select>
            </div>
            <div class="row justify-content-between">
                <div class="col-auto"><button type="button" class="btn btn-link-danger btn-pc-default" data-bs-dismiss="offcanvas"><i
                            class="align-text-bottom me-1 ti ti-circle-x"></i> Close</button></div>
                <div class="col-auto">
                    <button id="pc_event_add" type="button" class="btn btn-secondary" data-pc-action="add">
                        <span id="pc-e-btn-text"><i class="align-text-bottom me-1 ti ti-calendar-plus"></i> Add</span>
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>