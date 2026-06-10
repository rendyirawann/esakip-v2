/*
 * eSakip — Sembunyikan tombol/tab yang mengandung teks "Perubahan".
 * Contoh: tombol toggle "Sasaran Renstra Perubahan", "Data Sasaran Renstra Perubahan",
 * "Uraian Visi Perubahan", dll.
 *
 * Hanya menyasar elemen INTERAKTIF (button / .btn / tab / .nav-link).
 * TIDAK menyentuh: judul (h1-h5), breadcrumb, header tabel ("Sebab Perubahan",
 * "Anggaran PK Perubahan"), label form, maupun menu di sidebar (.pc-sidebar).
 */
(function () {
  function hidePerubahanButtons() {
    var els = document.querySelectorAll('button, a.btn, .btn, .nav-link, [role="tab"]');
    Array.prototype.forEach.call(els, function (el) {
      // Lewati elemen di dalam sidebar (menu navigasi).
      if (el.closest && el.closest('.pc-sidebar')) {
        return;
      }
      var txt = (el.textContent || '').replace(/\s+/g, ' ').trim();
      if (/perubahan/i.test(txt)) {
        el.style.display = 'none';
        // Jika tombol berada dalam btn-group, biarkan tombol lain tetap rapi.
        el.setAttribute('aria-hidden', 'true');
      }
    });
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', hidePerubahanButtons);
  } else {
    hidePerubahanButtons();
  }
})();
