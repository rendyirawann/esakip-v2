<?php
return [
    'adminEmail' => 'admin@example.com',
    'supportEmail' => 'support@example.com',
    'senderEmail' => 'noreply@example.com',
    'senderName' => 'Example.com mailer',
    'user.passwordResetTokenExpire' => 3600,
    'user.passwordMinLength' => 8,

    // ====== EsakipStorage (file manager + sinkron NextCloud) ======
    // Kredensial ASLI diisi di common/config/params-local.php (gitignored).
    'nextcloud' => [
        'enabled' => true,
        'baseUrl' => 'https://nextcloud.deliserdangkab.go.id', // tanpa trailing slash
        'davUser' => 'admin',  // pemilik akun -> /remote.php/dav/files/<davUser>/
        'username' => '',      // diisi di params-local
        'password' => '',      // diisi di params-local (disarankan App Password)
        'baseFolder' => 'SAKIP-DELI SERDANG',
        'verifySsl' => true,
    ],
    'sakipStorage' => [
        'localBase' => '@frontend/storage/sakip', // di luar webroot
        'maxSize' => 5242880,  // 5 MB
        'allowedExt' => ['pdf'],
        'allowedMime' => ['application/pdf'],
        // Kompresi salinan LOKAL saja (NextCloud tetap dapat file ASLI).
        'compress' => [
            'enabled' => true,
            'gsBin' => '',       // kosong = auto-detect dari PATH (gswin64c/gs). Pastikan folder bin gs ada di PATH & XAMPP di-restart.
            'level' => 'ebook',  // screen|ebook|printer|prepress (kecil -> besar)
        ],
    ],
];
