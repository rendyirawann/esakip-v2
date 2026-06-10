<script type="text/javascript" src="<?php echo base_url(); ?>js/jquery.dataTables.min.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>js/dataTables.bootstrap.js"></script>

<style>
    .btn-xxs {
        padding: 1px 3px;
        font-size: 10px;
        line-height: 1.2;
        border-radius: 3px;
    }
</style>
<?php
$alert = $this->session->flashdata('alert_form');
if (!empty($alert)) {
    echo $this->session->flashdata('alert_form');
}
?>
<div class="panel panel-primary">
    <div class="panel-heading">
        <h3 class="panel-title"><i class="fa fa-user"></i> TABEL <?= strtoupper($this->uri->segment(2)); ?> <button class="btn btn-success btn-xs" onclick="kontenimport();"><span class='fa fa-plus'></span> Import</button>
    </div>
    <div class="panel-body">

        <!-- <button class="btn btn-primary btn-sm" onclick="addsubkegiatan();" style="margin-bottom:10px;"><span class='fa fa-plus'></span> Tambah Sub Kegiatan</button></h3> -->
        <div style="position:relative;">


            <div id="loadskpd">
            </div>
            <div id="loaddiv" class="hide" style="background:#fff; position:absolute; top:0; z-index:9999999999; opacity:0.3; height:100%; width:100%;">
                <div>
                </div>
            </div>
        </div>
        <!-- modal add user -->
        <div class="modal fade" id="modal-add-subkegiatan">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                        <h4 class="modal-title">Tambah Sub Kegiatan</h4>
                    </div>
                    <div class="modal-body">
                        <!-- form -->
                        <form role="form" method="post" id="form-add-user">
                            <div class="form-group">
                                <label for="skpd_kode" class="col-sm-3 control-label">KODE</label>
                                <div class="col-sm-8">
                                    <input type="text" id="refsubkegiatan_kode" class="form-control inpmain" name="refsubkegiatan_kode" required>
                                    <p class="text-danger" id="error-user"></p>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="skpd_nama" class="col-sm-3 control-label">NAMA</label>
                                <div class="col-sm-8">
                                    <input type="text" id="refsubkegiatan_nama" class="form-control inpmain" name="refsubkegiatan_nama" required>
                                    <p class="text-danger" id="error-user"></p>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-default" data-dismiss="modal">Tutup</button>
                                <button type="button" class="btn btn-primary" id="btn-simpan">Simpan</button>
                            </div>
                        </form>
                        <!-- end form -->
                    </div><!-- /.modal-content -->
                </div><!-- /.modal-dialog -->
            </div>
            <!-- end modal add user -->
            <Script>
                $(document).ready(function() {
                    loadisi();
                    $("#btn-simpan").click(function() {
                        var main = {};
                        $('.inpmain').each(function() {
                            var nama = $(this).attr("name");
                            var val = $(this).val();
                            main[nama] = val;
                        });
                        $.post("<?= base_url() ?>master/simpansubkegiatan", {
                            main
                        }, function(result) {
                            result = $.trim(result);
                            var obj = jQuery.parseJSON(result);
                            if (obj.type == "success") {
                                $("#modal-add-subkegiatan").modal("hide");
                                loadisi();
                            }
                            new PNotify(obj);
                        });
                    });
                });

                function loadisi() {
                    $.post("<?= base_url() ?>master/load<?= $this->uri->segment(2); ?>", {}, function(result) {
                        $("#loadskpd").html(result);
                        $("#loaddiv").addClass('hide');
                    });

                }

                function kontenimport() {
                    $("#loaddiv").removeClass('hide');
                    $.post("<?= base_url() ?>master/import<?= $this->uri->segment(2); ?>", {}, function(result) {
                        result = $.trim(result);
                        var obj = jQuery.parseJSON(result);
                        if (obj.type == "success") {
                            $("#modal-add-skpd").modal("hide");
                            loadisi();
                        }
                        new PNotify(obj);
                    });

                }

                function addsubkegiatan() {
                    $("#modal-add-subkegiatan").modal("show");
                }
            </Script>