<?php

/** @var \yii\web\View $this */
/** @var string $content */

use common\widgets\Alert;
use frontend\assets\AppAsset;
use yii\bootstrap5\Breadcrumbs;
use yii\bootstrap5\Html;
use yii\bootstrap5\Nav;
use yii\bootstrap5\NavBar;
use yii\helpers\Url;

AppAsset::register($this);
?>

<script>
    document.getElementById("toggle-password").addEventListener("click", function() {
        var passwordInput = document.getElementById("password-input");
        var icon = this.querySelector(".eye-icon");

        if (passwordInput.type === "password") {
            passwordInput.type = "text";
            icon.innerHTML = '🙈 <small class="text-muted">Hide Password</small>'; // Menggunakan innerHTML
        } else {
            passwordInput.type = "password";
            icon.innerHTML = '👁️ <small class="text-muted">Show Password</small>';
        }
    });
</script>


<script>
    // Tampilkan loader branded saat form login dikirim
    document.addEventListener("DOMContentLoaded", function() {
        var form = document.querySelector(".eskf-form");
        var overlay = document.getElementById("overlaySpinner");

        if (form && overlay) {
            form.addEventListener("submit", function() {
                setLoaderText("Memverifikasi akun Anda", "Mohon tunggu sebentar...");
                overlay.classList.remove("is-success");
                overlay.style.display = "flex";
            });
        }
    });

    function setLoaderText(title, sub) {
        var t = document.getElementById("loaderTitle");
        var s = document.getElementById("loaderSub");
        if (t) t.textContent = title;
        if (s) s.textContent = sub;
    }

    // Loader sukses (animasi centang) lalu alihkan
    function showSuccessLoader(url) {
        var overlay = document.getElementById("overlaySpinner");
        if (!overlay) {
            window.location.href = url;
            return;
        }
        overlay.classList.add("is-success");
        setLoaderText("Login berhasil!", "Mengalihkan ke dashboard...");
        overlay.style.display = "flex";
        setTimeout(function() {
            window.location.href = url;
        }, 1800);
    }

    let redirectUrl = "";

    function showModal(message, isSuccess = false, url = '') {
        document.getElementById("overlaySpinner").style.display = "none"; // Sembunyikan spinner
        document.getElementById("modalMessage").innerText = message;
        document.getElementById("modalNotif").style.display = "flex";

        const modalIcon = document.getElementById("modalIcon");
        const modalHeader = document.getElementById("modalHeader");

        if (isSuccess) {
            modalIcon.innerHTML = "✅";
            modalIcon.classList.add("success-icon");
            modalHeader.innerText = "Berhasil!";
            redirectUrl = url;
            document.getElementById("redirectMessage").style.display = "block";

            let countdown = 5;
            const interval = setInterval(() => {
                countdown--;
                document.getElementById("countdown").innerText = countdown;
                if (countdown <= 0) {
                    clearInterval(interval);
                    window.location.href = redirectUrl;
                }
            }, 1000);
        } else {
            modalIcon.innerHTML = "❌";
            modalIcon.classList.add("error-icon");
            modalHeader.innerText = "Gagal!";
        }
    }

    function closeModal() {
        document.getElementById("modalNotif").style.display = "none";
        if (redirectUrl) {
            window.location.href = redirectUrl;
        }
    }

    <?php if (Yii::$app->session->hasFlash('error')): ?>
        showModal("<?= Yii::$app->session->getFlash('error') ?>");
    <?php elseif (Yii::$app->session->hasFlash('success')): ?>
        showSuccessLoader("<?= Url::to(['/site/index-main']) ?>");
    <?php endif; ?>
</script>