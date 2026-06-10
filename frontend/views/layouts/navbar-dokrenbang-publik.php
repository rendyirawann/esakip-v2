<style>
  /* Ensure modal takes full screen width and height */
  .modal-fullscreen {
    max-width: 100% !important;
    height: 100% !important;
    margin: 0;
  }

  .modal-content {
    height: 100% !important;
    border-radius: 0;
  }

  .modal-body {
    padding: 0;
    height: 100%;
    overflow-y: auto;
  }
</style>
<?php

/** @var \yii\web\View $this */
/** @var string $content */

use frontend\assets\AppAsset;
use frontend\models\User;
use common\widgets\Alert;
use yii\bootstrap5\Breadcrumbs;
use yii\bootstrap5\Html;
use yii\bootstrap5\Nav;
use yii\bootstrap5\NavBar;
use yii\helpers\Url;
use mdm\admin\components\MenuHelper;

AppAsset::register($this);

// Define the current user's refskpd_id
$refskpd_id = Yii::$app->user->identity->refskpd_id;


?>
<!-- [ Pre-loader ] start -->
<div class="loader-bg">
  <div class="loader-track">
    <div class="loader-fill"></div>
  </div>
</div>
<!-- [ Pre-loader ] End -->
<!-- [ Sidebar Menu ] start -->
<nav class="pc-sidebar" style="font-size:small;">
  <div class="navbar-wrapper">
    <div class="m-header">
      <a href="<?= Url::to(['/site/index']) ?>" class="b-brand text-primary">
        <!-- ========   Change your logo from here   ============ -->
        <h5><img src="<?= Url::base(true) ?>/lightapp/assets/images/bappeda.png" alt="logo image" height="60" width="60" class="logo-lg" />SIMONA<span class="badge bg-brand-color-2 rounded-pill ms-2 theme-version">v2.0</span></h5>

      </a>
    </div>
    <div class="navbar-content">
      <ul class="pc-navbar">
        <li class="pc-item pc-caption">
          <label>Menu Navigasi</label>
        </li>
        <li class="pc-item pc-hasmenu">
          <a href="<?= Url::to(['/site/index']) ?>" class="pc-link">
            <span class="pc-micon">
              <i class="ph-duotone ph-gauge"></i>
            </span>
            <span class="pc-mtext">Dashboard</span>
            <span class="pc-arrow"><i data-feather="chevron-right"></i></span>
            <!-- <span class="pc-badge">2</span> -->
          </a>
        </li>
        <!-- <li class="pc-item pc-hasmenu">
          <a href="#!" class="pc-link"
            ><span class="pc-micon"> <i class="ph-duotone ph-layout"></i></span><span class="pc-mtext">Layouts</span
            ><span class="pc-arrow"><i data-feather="chevron-right"></i></span
          ></a>
          <ul class="pc-submenu">
            <li class="pc-item"><a class="pc-link" href="../demo/layout-compact.html">Compact</a></li>
            <li class="pc-item"><a class="pc-link" href="../demo/layout-horizontal.html">Horizontal</a></li>
            <li class="pc-item"><a class="pc-link" href="../demo/layout-2.html">Creative</a></li>
            <li class="pc-item"><a class="pc-link" href="../demo/layout-tab.html">Tab</a></li>
            <li class="pc-item"><a class="pc-link" href="../demo/layout-vertical.html">Vertical</a></li>
          </ul>
        </li> -->
        <li class="pc-item pc-caption">
          <label>Apps</label>
          <i class="ph-duotone ph-chart-pie"></i>
        </li>

        <li class="pc-item pc-hasmenu">
          <a href="#!" class="pc-link">
            <span class="pc-micon">
              <i class="ph-duotone ph-file"></i>
            </span>
            <span class="pc-mtext">Dokumen</span><span class="pc-arrow"><i data-feather="chevron-right"></i></span></a>
          <ul class="pc-submenu">
            <li class="pc-item pc-hasmenu">
              <a class="pc-link" href="#">Dokumen Keluar<span class="pc-arrow"><i data-feather="chevron-right"></i></span></a>
              <ul class="pc-submenu">
                <li class="pc-item"><a class="pc-link" href="<?= Url::to(['/dokrenbang-dokumen-keluar-kegiatan/index-publik']) ?>">Dokumen Kegiatan</a></li>
                <li class="pc-item"><a class="pc-link" href="<?= Url::to(['/dokrenbang-dokumen-keluar-subkegiatan/index-publik']) ?>">Dokumen Sub Kegiatan</a></li>
              </ul>
            </li>
          </ul>
        </li>





        <li class="pc-item pc-caption">
          <label>Site Manajemen</label>
          <i class="ph-duotone ph-compass-tool"></i>
        </li>
        <li class="pc-item pc-hasmenu">
          <a href="#!" class="pc-link"><span class="pc-micon"> <i class="ph-duotone ph-user-circle-minus"></i></span><span class="pc-mtext">RBAC Admin</span><span class="pc-arrow"><i data-feather="chevron-right"></i></span></a>
          <ul class="pc-submenu">
            <li class="pc-item"><a class="pc-link" href="<?= Url::to(['/admin/assignment']) ?>">Assignment</a></li>
            <li class="pc-item"><a class="pc-link" href="<?= Url::to(['/admin/menu']) ?>">Menu</a></li>
            <li class="pc-item"><a class="pc-link" href="<?= Url::to(['/admin/permission']) ?>">Permissions</a></li>
            <li class="pc-item"><a class="pc-link" href="<?= Url::to(['/admin/role']) ?>">Roles</a></li>
            <li class="pc-item"><a class="pc-link" href="<?= Url::to(['/admin/route']) ?>">Routes</a></li>
            <li class="pc-item"><a class="pc-link" href="<?= Url::to(['/admin/rule']) ?>">Rules</a></li>
          </ul>
        </li>
        <li class="pc-item pc-hasmenu">
          <a href="#!" class="pc-link"><span class="pc-micon"> <i class="ph-duotone ph-gear"></i></span><span class="pc-mtext">Pengaturan</span><span class="pc-arrow"><i data-feather="chevron-right"></i></span></a>
          <ul class="pc-submenu">
            <li class="pc-item"><a class="pc-link" href="<?= Url::to(['/instansi/index']) ?>">Instansi</a></li>
            <li class="pc-item"><a class="pc-link" href="<?= Url::to(['/user-group/index']) ?>">User Group</a></li>
            <li class="pc-item"><a class="pc-link" href="<?= Url::to(['/user/index']) ?>">User</a></li>
          </ul>
        </li>

      </ul>
      <div class="card nav-action-card bg-brand-color-4">
        <div class="card-body" style="background-image: url('<?= Url::base(true) ?>/lightapp/assets/images/layout/nav-card-bg.svg')">
          <h5 class="text-dark">Account</h5>
          <p class="text-dark text-opacity-75"><?= Yii::$app->user->isGuest ? '' : Yii::$app->user->identity->username ?></p>
          <a href="<?= Url::to(['/site/logout']) ?>" class="btn btn-danger" data-method="post">Logout</a>
        </div>
      </div>
    </div>
    <div class="card pc-user-card">
      <!--  -->
      <!--  -->
      <div class="card-body">
        <div class="d-flex align-items-center">
          <div class="flex-shrink-0">
            <img src="<?= Url::base(true) ?>/lightapp/assets/images/face1.jpg" alt="user-image" class="user-avtar wid-45 rounded-circle" />
          </div>
          <div class="flex-grow-1 ms-3 me-2">
            <h6 class="mb-0"><?= Yii::$app->user->isGuest ? '' : Yii::$app->user->identity->username ?></h6>
            <small>
              <?php
              if (!Yii::$app->user->isGuest) {
                $authManager = Yii::$app->authManager;
                $userId = Yii::$app->user->id;
                $assignments = $authManager->getAssignments($userId);

                $rolesPermissions = [];
                foreach ($assignments as $assignment) {
                  $rolesPermissions[] = $assignment->roleName; // atau ->permissionName tergantung implementasi Anda
                }

                echo implode(', ', $rolesPermissions);
              }
              ?>
            </small>
          </div>

          <div class="dropdown">
            <a href="#" class="btn btn-icon btn-link-secondary avtar arrow-none dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false" data-bs-offset="0,20">
              <i class="ph-duotone ph-windows-logo"></i>
            </a>
            <div class="dropdown-menu">
              <ul>
                <li><a class="pc-user-links">
                    <i class="ph-duotone ph-user"></i>
                    <span>My Account</span>
                  </a></li>
                <li><a class="pc-user-links">
                    <i class="ph-duotone ph-gear"></i>
                    <span>Settings</span>
                  </a></li>
                <li><a class="pc-user-links">
                    <i class="ph-duotone ph-lock-key"></i>
                    <span>Lock Screen</span>
                  </a></li>
                <li><a class="pc-user-links" href="<?= Url::to(['/site/logout']) ?>" data-method="post">
                    <i class="ph-duotone ph-power"></i>
                    <span>Logout</span>
                  </a>
                </li>
              </ul>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</nav>
<!-- [ Sidebar Menu ] end -->
<!-- [ Header Topbar ] start -->
<header class="pc-header">
  <div class="header-wrapper"> <!-- [Mobile Media Block] start -->
    <div class="me-auto pc-mob-drp">
      <ul class="list-unstyled">
        <!-- ======= Menu collapse Icon ===== -->
        <li class="pc-h-item pc-sidebar-collapse">
          <a href="#" class="pc-head-link ms-0" id="sidebar-hide">
            <i class="ti ti-menu-2"></i>
          </a>
        </li>

        <li class="pc-h-item pc-sidebar-popup">
          <a href="#" class="pc-head-link ms-0" id="mobile-collapse">
            <i class="ti ti-menu-2"></i>
          </a>

        </li>


      </ul>
    </div>
    <!-- [Mobile Media Block end] -->
    <div class="ms-auto">
      <ul class="list-unstyled">
        <!-- <li class="dropdown pc-h-item">
      <a
        class="pc-head-link dropdown-toggle arrow-none me-0"
        data-bs-toggle="dropdown"
        href="#"
        role="button"
        aria-haspopup="false"
        aria-expanded="false"
      >
        <i class="ph-duotone ph-sun-dim"></i>
      </a>
      <div class="dropdown-menu dropdown-menu-end pc-h-dropdown">
        <a href="#!" class="dropdown-item" onclick="layout_change('dark')">
          <i class="ph-duotone ph-moon"></i>
          <span>Dark</span>
        </a>
        <a href="#!" class="dropdown-item" onclick="layout_change('light')">
          <i class="ph-duotone ph-sun-dim"></i>
          <span>Light</span>
        </a>
        <a href="#!" class="dropdown-item" onclick="layout_change_default()">
          <i class="ph-duotone ph-cpu"></i>
          <span>Default</span>
        </a>
      </div>
    </li> -->
        <!-- <li class="pc-h-item">
      <a class="pc-head-link pct-c-btn" href="#" data-bs-toggle="offcanvas" data-bs-target="#offcanvas_pc_layout">
        <i class="ph-duotone ph-gear-six"></i>
      </a>
    </li> -->
        <li class="dropdown pc-h-item">
          <a class="pc-head-link dropdown-toggle arrow-none me-0" data-bs-toggle="dropdown" href="#" role="button" aria-haspopup="false" aria-expanded="false">
            <i class="ph-duotone ph-diamonds-four"></i>
          </a>
          <div class="dropdown-menu dropdown-menu-end pc-h-dropdown">
            <!-- <a href="#!" class="dropdown-item">
              <i class="ph-duotone ph-user"></i>
              <span>My Account</span>
            </a>
            <a href="#!" class="dropdown-item">
              <i class="ph-duotone ph-gear"></i>
              <span>Settings</span>
            </a>
            <a href="#!" class="dropdown-item">
              <i class="ph-duotone ph-lifebuoy"></i>
              <span>Support</span>
            </a>
            <a href="#!" class="dropdown-item">
              <i class="ph-duotone ph-lock-key"></i>
              <span>Lock Screen</span>
            </a> -->
            <a href="<?= Url::to(['/site/logout']) ?>" class="dropdown-item" data-method="post">
              <i class="ph-duotone ph-power"></i>
              <span>Logout</span>
            </a>
          </div>
        </li>
        <!-- <li class="dropdown pc-h-item">
          <a class="pc-head-link dropdown-toggle arrow-none me-0" data-bs-toggle="dropdown" href="#" role="button" aria-haspopup="false" aria-expanded="false">
            <i class="ph-duotone ph-bell"></i>
            <span class="badge bg-success pc-h-badge">3</span>
          </a>
          <div class="dropdown-menu dropdown-notification dropdown-menu-end pc-h-dropdown">
            <div class="dropdown-header d-flex align-items-center justify-content-between">
              <h5 class="m-0">Notifications</h5>
              <ul class="list-inline ms-auto mb-0">
                <li class="list-inline-item">
                  <a href="../application/mail.html" class="avtar avtar-s btn-link-hover-primary">
                    <i class="ti ti-link f-18"></i>
                  </a>
                </li>
              </ul>
            </div>
            <div class="dropdown-body text-wrap header-notification-scroll position-relative" style="max-height: calc(100vh - 235px)">
              <ul class="list-group list-group-flush">
                <li class="list-group-item">
                  <p class="text-span">Today</p>
                  <div class="d-flex">
                    <div class="flex-shrink-0">
                      <img src="<?= Url::base(true) ?>/lightapp/assets/images/face1.jpg" alt="user-image" class="user-avtar avtar avtar-s" />
                    </div>
                    <div class="flex-grow-1 ms-3">
                      <div class="d-flex">
                        <div class="flex-grow-1 me-3 position-relative">
                          <h6 class="mb-0 text-truncate">Keefe Bond added new tags to 💪 Design system</h6>
                        </div>
                        <div class="flex-shrink-0">
                          <span class="text-sm">2 min ago</span>
                        </div>
                      </div>
                      <p class="position-relative mt-1 mb-2"><br /><span class="text-truncate">Lorem Ipsum has been the industry's standard dummy text ever since the 1500s.</span></p>
                      <span class="badge bg-light-primary border border-primary me-1 mt-1">web design</span>
                      <span class="badge bg-light-warning border border-warning me-1 mt-1">Dashobard</span>
                      <span class="badge bg-light-success border border-success me-1 mt-1">Design System</span>
                    </div>
                  </div>
                </li>
                <li class="list-group-item">
                  <div class="d-flex">
                    <div class="flex-shrink-0">
                      <div class="avtar avtar-s bg-light-primary">
                        <i class="ph-duotone ph-chats-teardrop f-18"></i>
                      </div>
                    </div>
                    <div class="flex-grow-1 ms-3">
                      <div class="d-flex">
                        <div class="flex-grow-1 me-3 position-relative">
                          <h6 class="mb-0 text-truncate">Message</h6>
                        </div>
                        <div class="flex-shrink-0">
                          <span class="text-sm">1 hour ago</span>
                        </div>
                      </div>
                      <p class="position-relative mt-1 mb-2"><br /><span class="text-truncate">Lorem Ipsum has been the industry's standard dummy text ever since the 1500s.</span></p>
                    </div>
                  </div>
                </li>
                <li class="list-group-item">
                  <p class="text-span">Yesterday</p>
                  <div class="d-flex">
                    <div class="flex-shrink-0">
                      <div class="avtar avtar-s bg-light-danger">
                        <i class="ph-duotone ph-user f-18"></i>
                      </div>
                    </div>
                    <div class="flex-grow-1 ms-3">
                      <div class="d-flex">
                        <div class="flex-grow-1 me-3 position-relative">
                          <h6 class="mb-0 text-truncate">Challenge invitation</h6>
                        </div>
                        <div class="flex-shrink-0">
                          <span class="text-sm">12 hour ago</span>
                        </div>
                      </div>
                      <p class="position-relative mt-1 mb-2"><br /><span class="text-truncate"><strong> Jonny aber </strong> invites to join the challenge</span></p>
                      <button class="btn btn-sm rounded-pill btn-outline-secondary me-2">Decline</button>
                      <button class="btn btn-sm rounded-pill btn-primary">Accept</button>
                    </div>
                  </div>
                </li>
                <li class="list-group-item">
                  <div class="d-flex">
                    <div class="flex-shrink-0">
                      <div class="avtar avtar-s bg-light-info">
                        <i class="ph-duotone ph-notebook f-18"></i>
                      </div>
                    </div>
                    <div class="flex-grow-1 ms-3">
                      <div class="d-flex">
                        <div class="flex-grow-1 me-3 position-relative">
                          <h6 class="mb-0 text-truncate">Forms</h6>
                        </div>
                        <div class="flex-shrink-0">
                          <span class="text-sm">2 hour ago</span>
                        </div>
                      </div>
                      <p class="position-relative mt-1 mb-2">Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard
                        dummy text ever since the 1500s.</p>
                    </div>
                  </div>
                </li>
                <li class="list-group-item">
                  <div class="d-flex">
                    <div class="flex-shrink-0">
                      <img src="<?= Url::base(true) ?>/lightapp/assets/images/user/avatar-2.jpg" alt="user-image" class="user-avtar avtar avtar-s" />
                    </div>
                    <div class="flex-grow-1 ms-3">
                      <div class="d-flex">
                        <div class="flex-grow-1 me-3 position-relative">
                          <h6 class="mb-0 text-truncate">Keefe Bond added new tags to 💪 Design system</h6>
                        </div>
                        <div class="flex-shrink-0">
                          <span class="text-sm">2 min ago</span>
                        </div>
                      </div>
                      <p class="position-relative mt-1 mb-2"><br /><span class="text-truncate">Lorem Ipsum has been the industry's standard dummy text ever since the 1500s.</span></p>
                      <button class="btn btn-sm rounded-pill btn-outline-secondary me-2">Decline</button>
                      <button class="btn btn-sm rounded-pill btn-primary">Accept</button>
                    </div>
                  </div>
                </li>
                <li class="list-group-item">
                  <div class="d-flex">
                    <div class="flex-shrink-0">
                      <div class="avtar avtar-s bg-light-success">
                        <i class="ph-duotone ph-shield-checkered f-18"></i>
                      </div>
                    </div>
                    <div class="flex-grow-1 ms-3">
                      <div class="d-flex">
                        <div class="flex-grow-1 me-3 position-relative">
                          <h6 class="mb-0 text-truncate">Security</h6>
                        </div>
                        <div class="flex-shrink-0">
                          <span class="text-sm">5 hour ago</span>
                        </div>
                      </div>
                      <p class="position-relative mt-1 mb-2">Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard
                        dummy text ever since the 1500s.</p>
                    </div>
                  </div>
                </li>
              </ul>
            </div>
            <div class="dropdown-footer">
              <div class="row g-3">
                <div class="col-6">
                  <div class="d-grid"><button class="btn btn-primary">Archive all</button></div>
                </div>
                <div class="col-6">
                  <div class="d-grid"><button class="btn btn-outline-secondary">Mark all as read</button></div>
                </div>
              </div>
            </div>
          </div>
        </li> -->
        <li class="dropdown pc-h-item header-user-profile">
          <a class="pc-head-link dropdown-toggle arrow-none me-0" data-bs-toggle="dropdown" href="#" role="button" aria-haspopup="false" data-bs-auto-close="outside" aria-expanded="false">
            <img src="<?= Url::base(true) ?>/lightapp/assets/images/face1.jpg" alt="user-image" class="user-avtar" />
          </a>
          <div class="dropdown-menu dropdown-user-profile dropdown-menu-end pc-h-dropdown">
            <div class="dropdown-header d-flex align-items-center justify-content-between">
              <h5 class="m-0">Profile</h5>
            </div>
            <div class="dropdown-body">
              <div class="profile-notification-scroll position-relative" style="max-height: calc(100vh - 225px)">
                <ul class="list-group list-group-flush w-100">
                  <li class="list-group-item">
                    <div class="d-flex align-items-center">
                      <div class="flex-shrink-0">
                        <img src="<?= Url::base(true) ?>/lightapp/assets/images/face1.jpg" alt="user-image" class="wid-50 rounded-circle" />
                      </div>
                      <div class="flex-grow-1 mx-3">
                        <h5 class="mb-0"><?= Yii::$app->user->isGuest ? '' : Yii::$app->user->identity->username ?></h5>
                        <a class="link-primary" href="mailto:<?= Yii::$app->user->identity->email ?>"><?= Yii::$app->user->isGuest ? '' : Yii::$app->user->identity->email ?></a>
                      </div>
                      <span class="badge bg-primary"><?php
                                                      if (!Yii::$app->user->isGuest) {
                                                        $authManager = Yii::$app->authManager;
                                                        $userId = Yii::$app->user->id;
                                                        $assignments = $authManager->getAssignments($userId);

                                                        $rolesPermissions = [];
                                                        foreach ($assignments as $assignment) {
                                                          $rolesPermissions[] = $assignment->roleName; // atau ->permissionName tergantung implementasi Anda
                                                        }

                                                        echo implode(', ', $rolesPermissions);
                                                      }
                                                      ?></span>
                    </div>
                  </li>
                  <!-- <li class="list-group-item">
                    <a href="#" class="dropdown-item">
                      <span class="d-flex align-items-center">
                        <i class="ph-duotone ph-key"></i>
                        <span>Change password</span>
                      </span>
                    </a>
                    <a href="#" class="dropdown-item">
                      <span class="d-flex align-items-center">
                        <i class="ph-duotone ph-envelope-simple"></i>
                        <span>Recently mail</span>
                      </span>
                      <div class="user-group">
                        <img src="<?= Url::base(true) ?>/lightapp/assets/images/face1.jpg" alt="user-image" class="avtar" />
                        <img src="<?= Url::base(true) ?>/lightapp/assets/images/face1.jpg" alt="user-image" class="avtar" />
                        <img src="<?= Url::base(true) ?>/lightapp/assets/images/face1.jpg" alt="user-image" class="avtar" />
                      </div>
                    </a>
                    <a href="#" class="dropdown-item">
                      <span class="d-flex align-items-center">
                        <i class="ph-duotone ph-calendar-blank"></i>
                        <span>Schedule meetings</span>
                      </span>
                    </a>
                  </li> -->
                  <!-- <li class="list-group-item">
                    <a href="#" class="dropdown-item">
                      <span class="d-flex align-items-center">
                        <i class="ph-duotone ph-heart"></i>
                        <span>Favorite</span>
                      </span>
                    </a>
                    <a href="#" class="dropdown-item">
                      <span class="d-flex align-items-center">
                        <i class="ph-duotone ph-arrow-circle-down"></i>
                        <span>Download</span>
                      </span>
                      <span class="avtar avtar-xs rounded-circle bg-danger text-white">10</span>
                    </a>
                  </li> -->
                  <!-- <li class="list-group-item">
                    <div class="dropdown-item">
                      <span class="d-flex align-items-center">
                        <i class="ph-duotone ph-globe-hemisphere-west"></i>
                        <span>Languages</span>
                      </span>
                      <span class="flex-shrink-0">
                        <select class="form-select bg-transparent form-select-sm border-0 shadow-none">
                          <option value="1">English</option>
                          <option value="2">Spain</option>
                          <option value="3">Arbic</option>
                        </select>
                      </span>
                    </div>
                    <a href="#" class="dropdown-item">
                      <span class="d-flex align-items-center">
                        <i class="ph-duotone ph-flag"></i>
                        <span>Country</span>
                      </span>
                    </a>
                    <div class="dropdown-item">
                      <span class="d-flex align-items-center">
                        <i class="ph-duotone ph-moon"></i>
                        <span>Dark mode</span>
                      </span>
                      <div class="form-check form-switch form-check-reverse m-0">
                        <input class="form-check-input f-18" id="dark-mode" type="checkbox" onclick="dark_mode()" role="switch" />
                      </div>
                    </div>
                  </li> -->
                  <!-- <li class="list-group-item">
                    <a href="#" class="dropdown-item">
                      <span class="d-flex align-items-center">
                        <i class="ph-duotone ph-user-circle"></i>
                        <span>Edit profile</span>
                      </span>
                    </a>
                    <a href="#" class="dropdown-item">
                      <span class="d-flex align-items-center">
                        <i class="ph-duotone ph-star text-warning"></i>
                        <span>Upgrade account</span>
                        <span class="badge bg-light-success border border-success ms-2">NEW</span>
                      </span>
                    </a>
                    <a href="#" class="dropdown-item">
                      <span class="d-flex align-items-center">
                        <i class="ph-duotone ph-bell"></i>
                        <span>Notifications</span>
                      </span>
                    </a>
                    <a href="#" class="dropdown-item">
                      <span class="d-flex align-items-center">
                        <i class="ph-duotone ph-gear-six"></i>
                        <span>Settings</span>
                      </span>
                    </a>
                  </li> -->
                  <li class="list-group-item">
                    <a href="<?= Url::to(['/site/portal']) ?>" class="dropdown-item">
                      <span class="d-flex align-items-center">
                        <i class="ph-duotone ph-plus-circle"></i>
                        <span>Kembali ke Portal</span>
                      </span>
                    </a>
                    <!-- Updated Switch Apps link -->
                    <!-- <a href="#" class="dropdown-item" id="switchAppBtn">
                      <span class="d-flex align-items-center">
                        <i class="ph-duotone ph-power"></i>
                        <span>Switch Apps</span>
                      </span>
                    </a> -->
                    <a href="<?= Url::to(['/site/index-main']) ?>" class="dropdown-item" data-method="post">
                      <span class="d-flex align-items-center">
                        <i class="ph-duotone ph-power"></i>
                        <span>Switch Dashboard</span>
                      </span>
                    </a>
                    <a href="<?= Url::to(['/site/logout']) ?>" class="dropdown-item" data-method="post">
                      <span class="d-flex align-items-center">
                        <i class="ph-duotone ph-power"></i>
                        <span>Logout</span>
                      </span>
                    </a>
                  </li>

                </ul>
              </div>
            </div>
          </div>
        </li>
      </ul>
    </div>
  </div>
</header>
<!-- [ Header ] end -->

<!-- Modal -->
<!-- Modal -->
<div class="modal fade" id="appSwitchModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-fullscreen">
    <div class="modal-content">
      <!-- No header -->
      <div class="modal-body" id="modalContent">
        <!-- Content will be loaded dynamically here -->
      </div>
    </div>
  </div>
</div>




<script>
  // Event listener for the "Switch Apps" button
  document.getElementById('switchAppBtn').addEventListener('click', function(e) {
    e.preventDefault(); // Prevent the default link behavior

    // Trigger the modal to show
    $('#appSwitchModal').modal('show');

    // Make an AJAX request to fetch the content of index-main
    $.ajax({
      url: '<?= Url::to(['/site/index-main']) ?>', // URL to fetch index-main
      type: 'GET',
      success: function(response) {
        // Place the returned content inside the modal body
        $('#modalContent').html(response);
      },
      error: function() {
        // Handle any errors here
        $('#modalContent').html('<p>Sorry, we could not load the app content at this time.</p>');
      }
    });
  });
</script>