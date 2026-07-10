# Panduan Deploy — Fitur EsakipStorage + Perbaikan PHP 8.3

Server: `bappedalitbang` (Ubuntu, Apache **mod_php 8.3**, app di `/var/www/html/esakip`).
Semua perintah dijalankan sebagai **root** (atau pakai `sudo`).

> Catatan: semua 7 aplikasi di `/var/www/html` berjalan di **PHP 8.3** (mod_php global, tanpa override per-vhost). Perbaikan 8.3 di bawah **aman & menguntungkan semua app** (hanya menambah ekstensi + membersihkan config error).

---

## FASE 0 — Backup (WAJIB sebelum mulai)
```bash
# Backup database (ganti user/pass sesuai server)
mysqldump -u root -p esakipprod > ~/esakipprod_$(date +%F).sql

# Backup folder aplikasi (opsional tapi disarankan)
tar czf ~/esakip_backup_$(date +%F).tgz -C /var/www/html esakip
```

---

## FASE 1 — Perbaiki PHP 8.3 (sekali untuk semua app)
Masalah: ada blok `extension=` manual di `php.ini` 8.3 + sejumlah ekstensi belum terpasang.

**1.1 Matikan blok extension manual (cli + fpm + apache2):**
```bash
for ini in /etc/php/8.3/cli/php.ini /etc/php/8.3/fpm/php.ini /etc/php/8.3/apache2/php.ini; do [ -f "$ini" ] && cp "$ini" "$ini.bak.$(date +%F)" && sed -i -E 's/^[[:space:]]*(zend_)?extension[[:space:]]*=/;&/' "$ini"; done
```

**1.2 Install ekstensi yang dibutuhkan aplikasi:**
```bash
apt update
apt install -y php8.3-curl php8.3-mbstring php8.3-xml php8.3-zip php8.3-gd php8.3-redis
```

**1.3 Verifikasi (harus TANPA Warning merah):**
```bash
php -v
php -m | grep -iE 'curl|mbstring|gd|zip|openssl|mysqli|pdo_mysql|redis|intl'
```

**1.4 Jika masih ada Warning** → blok manual ada di file lain (abaikan yang di `conf.d`):
```bash
grep -rnE '^[[:space:]]*(zend_)?extension[[:space:]]*=' /etc/php/8.3/ | grep -v '/conf.d/'
```
Comment baris yang muncul, lalu ulangi 1.3.

**1.5 Terapkan ke web:**
```bash
systemctl restart apache2
```

---

## FASE 2 — Deploy file aplikasi (kode)

### Jika pakai Git
```bash
cd /var/www/html/esakip
git pull
```
> File `*-local.php` tidak ikut git (lihat Fase 3).

### Jika upload manual (scp/sftp), file yang perlu di-upload:

**File BARU (9):**
- `frontend/controllers/StorageController.php`
- `frontend/models/StorageItem.php`
- `frontend/components/NextcloudService.php`
- `frontend/components/PdfCompressor.php`
- `frontend/views/storage/index.php`
- `frontend/views/storage/skpd-list.php`
- `frontend/views/layouts/navbar-storage.php`
- `frontend/views/layouts/main-storage.php`
- `console/migrations/m260610_030000_create_v2_storage_item.php`

**File .htaccess BARU (4) — proteksi config:**
- `common/config/.htaccess`
- `frontend/config/.htaccess`
- `backend/config/.htaccess`
- `console/config/.htaccess`

**File DIUBAH (2) — timpa yang lama:**
- `common/config/params.php`
- `frontend/views/site/index-main.php`

---

## FASE 3 — Konfigurasi rahasia (`params-local.php`)
File ini **gitignored** → set MANUAL di server. Edit `common/config/params-local.php`, pastikan ada blok:
```php
'nextcloud' => [
    'enabled'    => true,
    'baseUrl'    => 'https://nextcloud.deliserdangkab.go.id',
    'davUser'    => 'admin',
    'username'   => 'admin',
    'password'   => 'APP-PASSWORD-NEXTCLOUD',   // App Password, BUKAN password admin
    'baseFolder' => 'SAKIP-DELI SERDANG',
    'verifySsl'  => true,
],
```
> `gsBin` di server **dikosongkan** (sudah default di `params.php`) → auto-detect `gs` Linux.

---

## FASE 4 — Install Ghostscript (kompresi PDF lokal)
```bash
apt install -y ghostscript
gs --version        # mis. 10.0x.x
```
> Tanpa gs, fitur tetap jalan (file disimpan asli, tidak dikompres). NextCloud selalu menerima file ASLI.

---

## FASE 5 — Migration (buat tabel `v2_storage_item`)
```bash
cd /var/www/html/esakip
php yii migrate         # ketik: yes
```

---

## FASE 6 — Folder storage + permission (least privilege)
Web (mod_php) berjalan sebagai **www-data**, sedangkan app dimiliki `bappeda`. Beri akses tulis ke folder storage:
```bash
mkdir -p /var/www/html/esakip/frontend/storage/sakip
chown -R www-data:www-data /var/www/html/esakip/frontend/storage
chmod -R 750 /var/www/html/esakip/frontend/storage

# Kunci file kredensial
chown www-data:www-data /var/www/html/esakip/common/config/params-local.php
chmod 640 /var/www/html/esakip/common/config/params-local.php
```

---

## FASE 7 — Bersihkan cache & restart
```bash
cd /var/www/html/esakip
php yii cache/flush-all 2>/dev/null || true
rm -rf frontend/runtime/cache/* backend/runtime/cache/* 2>/dev/null || true
systemctl restart apache2
```

---

## FASE 8 — Uji fungsional
1. Login sebagai user **SKPD** → menu **Pilih Dashboard** → kartu **EsakipStorage**.
2. Buat folder (mis. `RENJA`) → **Unggah PDF**.
3. Cek status **✔ Tersinkron** dan kolom **Ukuran** (lebih kecil dari asli = kompresi jalan).
4. Cek di NextCloud: `SAKIP-DELI SERDANG / <kode> - <NAMA SKPD> / RENJA / file.pdf` (versi ASLI).
5. Login sebagai **superadmin/admin/developer** → harus bisa lihat **Semua SKPD**.

---

## Rollback (jika perlu)
```bash
# Kembalikan php.ini
cp /etc/php/8.3/cli/php.ini.bak.<tanggal> /etc/php/8.3/cli/php.ini   # dst untuk fpm/apache2

# Batalkan migration
cd /var/www/html/esakip && php yii migrate/down 1

# Restore DB
mysql -u root -p esakipprod < ~/esakipprod_<tanggal>.sql
```

---

### Checklist singkat
- [ ] Fase 0: backup DB + app
- [ ] Fase 1: `php -v` bersih (tanpa warning)
- [ ] Fase 2: file kode ter-deploy
- [ ] Fase 3: `params-local.php` berisi App Password
- [ ] Fase 4: `gs --version` jalan
- [ ] Fase 5: `php yii migrate` sukses
- [ ] Fase 6: permission storage + params-local
- [ ] Fase 8: upload PDF → Tersinkron + terkompres
