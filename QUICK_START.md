# 🚀 QUICK START GUIDE - FITUR BARU JAWATRIP

## ⚡ INSTALASI CEPAT (5 MENIT)

### Step 1: Import Database
1. Buka **phpMyAdmin**
2. Pilih database `jawatrip`
3. Klik tab **Import**
4. Upload file: `databasenya/update_fitur_baru.sql`
5. Klik **Go**

✅ **4 tabel baru akan dibuat:**
- `reviews` (untuk rating & review)
- `wishlist` (untuk favorit)
- `promo_diskon` (untuk promo/diskon)
- `chat_messages` (untuk chat history)

---

### Step 2: Aktivasi Fitur di File Existing

#### A. Tambahkan Chat Widget (Semua Halaman User)

Edit file: `index.php`, `wisata.php`, dll

**Tambahkan sebelum `</body>`:**
```php
<?php include 'includes/chat_widget.php'; ?>
```

#### B. Tambahkan Notifikasi Bell (Navbar)

Edit file: `includes/navbar.php`

**Tambahkan di area navbar (sebelum profile/login button):**
```php
<?php include 'includes/notification_widget.php'; ?>
```

#### C. Tambahkan Review Widget (Halaman Detail Paket)

Edit file: `wisatamalang.php`

**Tambahkan sebelum `<?php include 'includes/footer.php'; ?>`:**
```php
<?php include 'includes/review_widget.php'; ?>
```

#### D. Tambahkan Wishlist Button

Edit file yang tampilkan list paket (misalnya `wisata.php`)

**Di bagian atas (setelah header):**
```php
<?php include 'includes/wishlist_button.php'; ?>
```

**Di card paket, tambah button:**
```html
<button class="wishlist-btn" onclick="toggleWishlist(<?= $paket['id_paket'] ?>, this)">
    <i class="bi bi-heart-fill"></i>
</button>
```

---

### Step 3: Tambahkan Menu Link Baru

#### Untuk User (includes/navbar.php atau dashboard_home.php)
```html
<a href="explore.php" class="nav-link">🔍 Explore</a>
<a href="wishlist.php" class="nav-link">❤️ Favorit</a>
```

#### Untuk Admin (admin/sidebar_admin.php atau navbar admin)
```html
<a href="analytics.php" class="nav-link">📊 Analytics</a>
<a href="promo_management.php" class="nav-link">🎁 Promo</a>
```

---

## 🎯 TESTING FITUR

### 1. Test Review System
1. Buka halaman detail paket: `wisatamalang.php?id=1`
2. Scroll ke bawah, cari section **Review & Rating**
3. Klik **"Tulis Review"**
4. Pilih rating bintang
5. Tulis review
6. Submit

### 2. Test Wishlist
1. Buka `wisata.php` atau halaman list paket
2. Klik icon **❤️ heart** di card paket
3. Icon berubah warna merah = berhasil
4. Buka `wishlist.php` untuk lihat daftar favorit

### 3. Test Live Chat
1. Scroll ke bawah halaman manapun
2. Klik icon **chat bubble** biru di kanan bawah
3. Ketik pesan atau klik quick reply
4. Bot akan auto-reply

### 4. Test Filter & Search
1. Buka `explore.php`
2. Pilih filter lokasi, durasi, harga, kategori
3. Klik **"Terapkan Filter"**
4. Hasil akan tampil sesuai filter

### 5. Test Notifikasi
1. Lihat icon **🔔 bell** di navbar
2. Jika ada badge merah = ada notif baru
3. Klik bell untuk lihat dropdown notifikasi

### 6. Test Admin Analytics (Admin Only)
1. Login sebagai admin
2. Buka `admin/analytics.php`
3. Lihat grafik dan statistik

### 7. Test Manajemen Promo (Admin Only)
1. Login sebagai admin
2. Buka `admin/promo_management.php`
3. Klik **"Tambah Promo Baru"**
4. Isi form promo
5. Submit

---

## 📱 SCREENSHOT LOKASI FITUR

```
┌─────────────────────────────────────┐
│  NAVBAR                             │
│  [Logo] [Menu] [🔔Notif] [Profile] │ ← Notifikasi Widget
└─────────────────────────────────────┘

┌─────────────────────────────────────┐
│  HOMEPAGE / ANY PAGE                │
│                                     │
│  [Content here...]                  │
│                                     │
│                      ┌───┐          │
│                      │💬 │          │ ← Live Chat Widget
│                      └───┘          │
└─────────────────────────────────────┘

┌─────────────────────────────────────┐
│  WISATAMALANG.PHP (Detail Paket)    │
│  [Hero Section]                     │
│  [Itinerary]                        │
│  [Facilities]                       │
│                                     │
│  ⭐ REVIEW & RATING                 │ ← Review Widget
│  [Rating Overview] [Reviews List]   │
└─────────────────────────────────────┘

┌─────────────────────────────────────┐
│  EXPLORE.PHP (Filter Page)          │
│  [Search Bar]                       │
│  [Lokasi] [Durasi] [Harga] Filters │ ← Advanced Filter
│  [Results Grid]                     │
└─────────────────────────────────────┘

┌─────────────────────────────────────┐
│  WISATA.PHP (Package List)          │
│  ┌─────────┐  ┌─────────┐          │
│  │ Paket 1 │  │ Paket 2 │          │
│  │ ❤️      │  │ ❤️      │          │ ← Wishlist Button
│  └─────────┘  └─────────┘          │
└─────────────────────────────────────┘

┌─────────────────────────────────────┐
│  ADMIN/ANALYTICS.PHP                │
│  [Stat Cards]                       │
│  [Revenue Chart] [Status Chart]     │ ← Admin Analytics
│  [Top Destinations Table]           │
└─────────────────────────────────────┘

┌─────────────────────────────────────┐
│  ADMIN/PROMO_MANAGEMENT.PHP         │
│  ┌──────────┐  ┌──────────┐        │
│  │ PROMO 1  │  │ PROMO 2  │        │ ← Promo Management
│  │ Timer: 5d│  │ Timer: 2h│        │
│  └──────────┘  └──────────┘        │
└─────────────────────────────────────┘
```

---

## 🎨 CUSTOMIZATION

### Ubah Warna Theme
Edit file CSS atau style tag di masing-masing widget:

```css
/* Ganti gradient primary */
background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);

/* Menjadi: */
background: linear-gradient(135deg, #YOUR_COLOR1 0%, #YOUR_COLOR2 100%);
```

### Ubah Auto-Reply Chat Bot
Edit file: `includes/chat_widget.php`

Cari function `getBotResponse()` dan tambah kondisi baru:

```javascript
function getBotResponse(message) {
    const msg = message.toLowerCase();
    
    if (msg.includes('custom_keyword')) {
        return 'Custom response here!';
    }
    // ... existing code
}
```

---

## ⚙️ KONFIGURASI

### Ubah Interval Auto-Refresh Notifikasi
Default: 30 detik

Edit `includes/notification_widget.php`:
```javascript
// Ubah dari 30000 (30s) ke nilai lain (ms)
setInterval(updateNotificationCount, 60000); // 60 detik
```

### Ubah Jumlah Notifikasi yang Ditampilkan
Edit `notification_handler.php`:
```php
// Ubah LIMIT 10 menjadi angka lain
ORDER BY p.tanggal_pesan DESC
LIMIT 20  // Tampilkan 20 notifikasi
```

---

## 🔧 TROUBLESHOOTING UMUM

### ❌ Error: "Table 'reviews' doesn't exist"
**Solusi:** Import `update_fitur_baru.sql` di phpMyAdmin

### ❌ Wishlist button tidak berfungsi (tidak ada response)
**Solusi:** 
1. Pastikan user sudah login
2. Cek file `wishlist_handler.php` ada dan bisa diakses
3. Buka browser Console (F12) untuk lihat error

### ❌ Chat widget tidak muncul
**Solusi:** 
1. Pastikan `<?php include 'includes/chat_widget.php'; ?>` ditambahkan
2. Cek apakah file ada di folder `includes/`
3. Clear browser cache (Ctrl+F5)

### ❌ Chart tidak tampil di Analytics
**Solusi:** 
1. Pastikan ada koneksi internet (Chart.js load dari CDN)
2. Atau download Chart.js dan simpan lokal

### ❌ Filter tidak mengembalikan hasil
**Solusi:**
1. Cek apakah ada data paket di database
2. Pastikan field yang difilter sesuai dengan struktur database
3. Cek `filter_handler.php` untuk debugging

---

## 📊 DATABASE STRUCTURE

### Tabel `reviews`
```sql
- id (PK)
- id_paket (FK ke paket_wisata)
- id_user (FK ke users)
- rating (1-5)
- review_text
- created_at
```

### Tabel `wishlist`
```sql
- id (PK)
- id_user (FK)
- id_paket (FK)
- created_at
```

### Tabel `promo_diskon`
```sql
- id (PK)
- kode_promo (UNIQUE)
- nama_promo
- jenis_diskon (percentage/fixed)
- nilai_diskon
- min_transaksi
- max_diskon
- kuota
- terpakai
- tanggal_mulai
- tanggal_selesai
- status (active/inactive)
- created_at
```

---

## 🎉 SELESAI!

Semua fitur sudah siap digunakan! 

**Next Steps:**
1. ✅ Test semua fitur
2. ✅ Customize sesuai kebutuhan
3. ✅ Deploy ke production
4. ✅ Monitor & optimize

**Butuh bantuan?** Lihat `DOKUMENTASI_FITUR_BARU.md` untuk detail lengkap!

---

**Enjoy your enhanced JawaTrip website! 🚀**
