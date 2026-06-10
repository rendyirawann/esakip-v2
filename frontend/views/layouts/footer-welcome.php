<?php

use yii\helpers\Url;

?>

<script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/gsap.min.js" integrity="sha512-7eHRwcbYkK4d9g/6tD/mhkf++eoTHwpNM9woBxtPUBWm67zeAfFC+HrdoE2GanKeocly/VxeLvIqwvCdk7qScg==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Animasi masuk (entrance) — logo pop + teks reveal ke atas
        var tl = gsap.timeline();
        tl.from('#svg', { scale: 0.5, opacity: 0, y: -30, rotation: -10, duration: 1, ease: 'back.out(1.7)' })
            .from('.logo-name h1', { y: 40, opacity: 0, duration: 0.8, ease: 'power3.out' }, '-=0.35')
            .from('.logo-name p', { y: 22, opacity: 0, duration: 0.6, ease: 'power2.out' }, '-=0.45')
            .from('.logo-name small', { y: 16, opacity: 0, duration: 0.5, ease: 'power2.out' }, '-=0.4')
            .from('.welcome-dots', { opacity: 0, duration: 0.5 }, '-=0.2')
            // Float lembut setelah masuk
            .to('#svg', { y: -10, duration: 1.4, ease: 'sine.inOut', yoyo: true, repeat: -1 }, '-=0.1');

        // Fade out keseluruhan lalu alihkan ke pilihan dashboard
        gsap.to('.loading-page', {
            opacity: 0,
            duration: 1,
            delay: 3.8,
            ease: 'power2.inOut',
            onComplete: function() {
                window.location.href = '<?= Url::to(['site/index-main']) ?>';
            }
        });
    });
</script>
