<!DOCTYPE html>
<html lang="en">
<!-- [Head] start -->

<head>
  <title>E-SAKIP | LOGIN</title>
  <!-- [Meta] -->
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge" />
  <meta name="description"
    content="ESAKIP" />
  <meta name="author" content="ESAKIP" />

  <!-- [Google Font : Public Sans] icon -->
  <link href="https://fonts.googleapis.com/css2?family=Public+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">

  <!-- [Tabler Icons] https://tablericons.com -->
  <link rel="stylesheet" href="<?= base_url() ?>/assetnew/assets/fonts/tabler-icons.min.css">
  <!-- [Feather Icons] https://feathericons.com -->
  <link rel="stylesheet" href="<?= base_url() ?>/assetnew/assets/fonts/feather.css">
  <!-- [Font Awesome Icons] https://fontawesome.com/icons -->
  <link rel="stylesheet" href="<?= base_url() ?>/assetnew/assets/fonts/fontawesome.css">
  <!-- [Material Icons] https://fonts.google.com/icons -->
  <link rel="stylesheet" href="<?= base_url() ?>/assetnew/assets/fonts/material.css">
  <!-- [Template CSS Files] -->
  <link rel="stylesheet" href="<?= base_url() ?>/assetnew/assets/css/style.css" id="main-style-link">
  <link rel="stylesheet" href="<?= base_url() ?>/assetnew/assets/css/style-preset.css">

  <script src="<?= base_url() ?>js/jquery-1.9.1.min.js"></script>
  <!-- PNotify -->
  <script type="text/javascript" src="<?= base_url() ?>plugins/notify/pnotify.core.js"></script>
  <script type="text/javascript" src="<?= base_url() ?>plugins/notify/pnotify.buttons.js"></script>
  <script type="text/javascript" src="<?= base_url() ?>plugins/notify/pnotify.nonblock.js"></script>

</head>
<!-- [Head] end -->
<!-- [Body] Start -->

<body data-pc-preset="preset-1" data-pc-sidebar-theme="light" data-pc-sidebar-caption="true" data-pc-direction="ltr"
  data-pc-theme="light">
  <!-- [ Pre-loader ] start -->
  <div class="loader-bg">
    <div class="loader-track">
      <div class="loader-fill"></div>
    </div>
  </div>
  <!-- [ Pre-loader ] End -->

  <div class="auth-main v2">
    <div class="bg-overlay bg-dark"></div>
    <div class="auth-wrapper">
      <div class="auth-sidecontent">
        <div class="auth-sidefooter">
          <img src="<?= base_url() ?>assetnew/lightapp/assets/images/bappeda.png" width="32px;" alt="images" />
          <hr class="mb-3 mt-4" />
          <div class="row">
            <div class="col my-1">
              <p class="m-0">eSakip. Sistem Akuntabilitas Kinerja Instansi Pemerintah &copy; 2024 <a href="http://esakip.deliserdangkab.go.id/2024/portal" target="_blank">
                  Bappedalitbang</a></p>
            </div>
            <div class="col-auto my-1">
              <!-- <ul class="list-inline footer-link mb-0">
                            <li class="list-inline-item"><a href="../index.html">Home</a></li>
                            <li class="list-inline-item"><a href="https://pcoded.gitbook.io/light-able/" target="_blank">Documentation</a></li>
                            <li class="list-inline-item"><a href="https://phoenixcoded.support-hub.io/" target="_blank">Support</a>
                            </li>
                        </ul> -->
            </div>
          </div>
        </div>

      </div>
      <form action="" class="auth-form" id="formlogin" method="post">
        <div class="card my-5 mx-3">
          <div class="card-body">
            <h4 class="f-w-500 mb-1">Aplikasi eSakip</h4>
            <p class="mb-3">Login with Username <a href="../pages/register-v2.html" class="link-primary ms-1"></a></p>
            <div class="form-group mb-3">
              <input type="text" class="form-control" placeholder="Username" name="username" id="username" required="" />
            </div>
            <div class=" form-group mb-3">
              <input type="password" class="form-control" placeholder="Password" name="password" id="floatingInput1" required="" />
            </div>

            <div class="form-check">
              <input type="checkbox" class="form-check-input" id="show-password" onclick="togglePassword()">
              <label class="form-check-label" for="show-password">Show Password</label>
            </div>


            <div class="d-grid mt-4">
              <button type="submit" class="btn btn-default submit"><span class="fa fa-sign-in"></span> Log in</button>
              <button type="button" class="btn btn-default submit" onClick="window.location.href='<?php echo base_url() ?>'"><span class="fa fa-home"></span> Portal</button>
            </div>

            <div class="saprator my-3">
              <!-- <span>Or continue with</span> -->
            </div>
            <div class="text-center">
              <p>&copy; Pemerintah Kabupaten Deli Serdang.</p>
              <p>Dengan melanjutkan, Anda menyetujui <a href="/2024/download/syarat-ketentuan-esakip.pdf">Syarat & Ketentuan</a> dan <a href="/2024/download/kebijakan-privasi-esakip.pdf">Kebijakan Privasi</a>kami. Anda juga dapat melihat <a href="/2024/portal/home/dokumen">petunjuk penggunaan aplikasi</a>.</p>
              <ul class="list-inline mx-auto mt-3 mb-0">
                <!-- <li class="list-inline-item">
                                <a href="https://www.facebook.com/" class="avtar avtar-s rounded-circle bg-facebook" target="_blank">
                                    <i class="fab fa-facebook-f text-white"></i>
                                </a>
                            </li>
                            <li class="list-inline-item">
                                <a href="https://twitter.com/" class="avtar avtar-s rounded-circle bg-twitter" target="_blank">
                                    <i class="fab fa-twitter text-white"></i>
                                </a>
                            </li>
                            <li class="list-inline-item">
                                <a href="https://myaccount.google.com/" class="avtar avtar-s rounded-circle bg-googleplus" target="_blank">
                                    <i class="fab fa-google text-white"></i>
                                </a>
                            </li> -->
              </ul>
            </div>
          </div>
        </div>
      </form>
    </div>
  </div>
  <!-- [ Main Content ] end -->
  <!-- [ Main Content ] end -->
  <!-- Required Js -->
  <script>
    $("#formlogin").submit(function(e) {
      e.preventDefault();
      $.post("<?php echo base_url(); ?>login/proses", $("#formlogin").serialize(), function(result) {
        result = $.trim(result);
        var obj = jQuery.parseJSON(result);
        if (obj.title !== "success") {
          new PNotify(obj);
          $("#username").focus();
        } else {
          location.reload();
        }
        //$( ".result" ).html( data );
      });

    });
  </script>

  <script>
    function togglePassword() {
      var passwordInput = document.getElementById('floatingInput1'); // id updated
      var showPasswordCheckbox = document.getElementById('show-password');

      if (showPasswordCheckbox.checked) {
        passwordInput.type = 'text';
      } else {
        passwordInput.type = 'password';
      }
    }
  </script>
  <script src="<?= base_url() ?>assetnew/assets/js/plugins/popper.min.js"></script>
  <script src="<?= base_url() ?>assetnew/assets/js/plugins/simplebar.min.js"></script>
  <script src="<?= base_url() ?>assetnew/assets/js/plugins/bootstrap.min.js"></script>
  <script src="<?= base_url() ?>assetnew/assets/js/fonts/custom-font.js"></script>
  <script src="<?= base_url() ?>assetnew/assets/js/pcoded.js"></script>
  <script src="<?= base_url() ?>assetnew/assets/js/plugins/feather.min.js"></script>





  <script>
    layout_change('light');
  </script>




  <script>
    layout_sidebar_change('light');
  </script>



  <script>
    change_box_container('false');
  </script>


  <script>
    layout_caption_change('true');
  </script>




  <script>
    layout_rtl_change('false');
  </script>


  <script>
    preset_change("preset-1");
  </script>
  <div class="offcanvas border-0 pct-offcanvas offcanvas-end" tabindex="-1" id="offcanvas_pc_layout">
    <div class="offcanvas-header justify-content-between">
      <h5 class="offcanvas-title">Settings</h5>
      <button type="button" class="btn btn-icon btn-link-danger" data-bs-dismiss="offcanvas" aria-label="Close"><i
          class="ti ti-x"></i></button>
    </div>
    <div class="pct-body customizer-body">
      <div class="offcanvas-body py-0">
        <ul class="list-group list-group-flush">
          <li class="list-group-item">
            <div class="pc-dark">
              <h6 class="mb-1">Theme Mode</h6>
              <p class="text-muted text-sm">Choose light or dark mode or Auto</p>
              <div class="row theme-color theme-layout">
                <div class="col-4">
                  <div class="d-grid">
                    <button class="preset-btn btn active" data-value="true" onclick="layout_change('light');">
                      <span class="btn-label">Light</span>
                      <span class="pc-lay-icon"><span></span><span></span><span></span><span></span></span>
                    </button>
                  </div>
                </div>
                <div class="col-4">
                  <div class="d-grid">
                    <button class="preset-btn btn" data-value="false" onclick="layout_change('dark');">
                      <span class="btn-label">Dark</span>
                      <span class="pc-lay-icon"><span></span><span></span><span></span><span></span></span>
                    </button>
                  </div>
                </div>
                <div class="col-4">
                  <div class="d-grid">
                    <button class="preset-btn btn" data-value="default" onclick="layout_change_default();"
                      data-bs-toggle="tooltip"
                      title="Automatically sets the theme based on user's operating system's color scheme.">
                      <span class="btn-label">Default</span>
                      <span class="pc-lay-icon d-flex align-items-center justify-content-center">
                        <i class="ph-duotone ph-cpu"></i>
                      </span>
                    </button>
                  </div>
                </div>
              </div>
            </div>
          </li>
          <li class="list-group-item">
            <h6 class="mb-1">Sidebar Theme</h6>
            <p class="text-muted text-sm">Choose Sidebar Theme</p>
            <div class="row theme-color theme-sidebar-color">
              <div class="col-6">
                <div class="d-grid">
                  <button class="preset-btn btn" data-value="true" onclick="layout_sidebar_change('dark');">
                    <span class="btn-label">Dark</span>
                    <span class="pc-lay-icon"><span></span><span></span><span></span><span></span></span>
                  </button>
                </div>
              </div>
              <div class="col-6">
                <div class="d-grid">
                  <button class="preset-btn btn active" data-value="false" onclick="layout_sidebar_change('light');">
                    <span class="btn-label">Light</span>
                    <span class="pc-lay-icon"><span></span><span></span><span></span><span></span></span>
                  </button>
                </div>
              </div>
            </div>
          </li>
          <li class="list-group-item">
            <h6 class="mb-1">Accent color</h6>
            <p class="text-muted text-sm">Choose your primary theme color</p>
            <div class="theme-color preset-color">
              <a href="#!" class="active" data-value="preset-1"><i class="ti ti-check"></i></a>
              <a href="#!" data-value="preset-2"><i class="ti ti-check"></i></a>
              <a href="#!" data-value="preset-3"><i class="ti ti-check"></i></a>
              <a href="#!" data-value="preset-4"><i class="ti ti-check"></i></a>
              <a href="#!" data-value="preset-5"><i class="ti ti-check"></i></a>
              <a href="#!" data-value="preset-6"><i class="ti ti-check"></i></a>
              <a href="#!" data-value="preset-7"><i class="ti ti-check"></i></a>
              <a href="#!" data-value="preset-8"><i class="ti ti-check"></i></a>
              <a href="#!" data-value="preset-9"><i class="ti ti-check"></i></a>
              <a href="#!" data-value="preset-10"><i class="ti ti-check"></i></a>
            </div>
          </li>
          <li class="list-group-item">
            <h6 class="mb-1">Sidebar Caption</h6>
            <p class="text-muted text-sm">Sidebar Caption Hide/Show</p>
            <div class="row theme-color theme-nav-caption">
              <div class="col-6">
                <div class="d-grid">
                  <button class="preset-btn btn active" data-value="true" onclick="layout_caption_change('true');">
                    <span class="btn-label">Caption Show</span>
                    <span
                      class="pc-lay-icon"><span></span><span></span><span><span></span><span></span></span><span></span></span>
                  </button>
                </div>
              </div>
              <div class="col-6">
                <div class="d-grid">
                  <button class="preset-btn btn" data-value="false" onclick="layout_caption_change('false');">
                    <span class="btn-label">Caption Hide</span>
                    <span
                      class="pc-lay-icon"><span></span><span></span><span><span></span><span></span></span><span></span></span>
                  </button>
                </div>
              </div>
            </div>
          </li>
          <li class="list-group-item">
            <div class="pc-rtl">
              <h6 class="mb-1">Theme Layout</h6>
              <p class="text-muted text-sm">LTR/RTL</p>
              <div class="row theme-color theme-direction">
                <div class="col-6">
                  <div class="d-grid">
                    <button class="preset-btn btn active" data-value="false" onclick="layout_rtl_change('false');">
                      <span class="btn-label">LTR</span>
                      <span class="pc-lay-icon"><span></span><span></span><span></span><span></span></span>
                    </button>
                  </div>
                </div>
                <div class="col-6">
                  <div class="d-grid">
                    <button class="preset-btn btn" data-value="true" onclick="layout_rtl_change('true');">
                      <span class="btn-label">RTL</span>
                      <span class="pc-lay-icon"><span></span><span></span><span></span><span></span></span>
                    </button>
                  </div>
                </div>
              </div>
            </div>
          </li>
          <li class="list-group-item pc-box-width">
            <div class="pc-container-width">
              <h6 class="mb-1">Layout Width</h6>
              <p class="text-muted text-sm">Choose Full or Container Layout</p>
              <div class="row theme-color theme-container">
                <div class="col-6">
                  <div class="d-grid">
                    <button class="preset-btn btn active" data-value="false" onclick="change_box_container('false')">
                      <span class="btn-label">Full Width</span>
                      <span class="pc-lay-icon"><span></span><span></span><span></span><span><span></span></span></span>
                    </button>
                  </div>
                </div>
                <div class="col-6">
                  <div class="d-grid">
                    <button class="preset-btn btn" data-value="true" onclick="change_box_container('true')">
                      <span class="btn-label">Fixed Width</span>
                      <span class="pc-lay-icon"><span></span><span></span><span></span><span><span></span></span></span>
                    </button>
                  </div>
                </div>
              </div>
            </div>
          </li>
          <li class="list-group-item">
            <div class="d-grid">
              <button class="btn btn-light-danger" id="layoutreset">Reset Layout</button>
            </div>
          </li>
        </ul>
      </div>
    </div>
  </div>
</body>
<!-- [Body] end -->

</html>