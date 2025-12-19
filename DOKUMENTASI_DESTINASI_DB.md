# Database Destinasi - Panduan Implementasi

## 📋 Struktur Database Baru

### 1. Tabel `destinasi_wisata`
Menyimpan daftar semua destinasi wisata yang tersedia.

```sql
- id_destinasi (PK, Auto Increment)
- nama_destinasi (VARCHAR 255)
- gambar (VARCHAR 255) - nama file gambar
- deskripsi_destinasi (TEXT)
- created_at (TIMESTAMP)
```

### 2. Tabel `paket_destinasi` (Junction Table)
Menghubungkan paket wisata dengan destinasi yang dikunjungi.

```sql
- id (PK, Auto Increment)
- id_paket (FK → paket_wisata.id_paket)
- id_destinasi (FK → destinasi_wisata.id_destinasi)
- urutan (INT) - urutan kunjungan destinasi
- created_at (TIMESTAMP)
```

## 🚀 Langkah Implementasi

### Step 1: Import SQL File
1. Buka **phpMyAdmin** → Database `jawatrip`
2. Klik tab **SQL** atau **Import**
3. Upload/Copy-paste file berikut secara berurutan:
   - `add_destinasi_table.sql` → Buat tabel dan data destinasi
   - `sample_paket_destinasi.sql` → Hubungkan paket dengan destinasi

### Step 2: Verifikasi Data
```sql
-- Check destinasi
SELECT * FROM destinasi_wisata;

-- Check paket destinasi
SELECT pd.*, dw.nama_destinasi 
FROM paket_destinasi pd 
JOIN destinasi_wisata dw ON pd.id_destinasi = dw.id_destinasi 
ORDER BY pd.id_paket, pd.urutan;
```

### Step 3: Update Nama Paket (Opsional)
Sesuaikan nama paket di database dengan paket yang sebenarnya:

```sql
SELECT id_paket, nama_paket FROM paket_wisata;
```

Kemudian update relasi `paket_destinasi` dengan id_paket yang benar.

## 📝 Menambah Destinasi Baru

### Tambah Destinasi:
```sql
INSERT INTO `destinasi_wisata` (`nama_destinasi`, `gambar`, `deskripsi_destinasi`) 
VALUES ('Nama Destinasi', 'nama_file.webp', 'Deskripsi destinasi...');
```

### Hubungkan ke Paket:
```sql
INSERT INTO `paket_destinasi` (`id_paket`, `id_destinasi`, `urutan`) 
VALUES (1, 13, 1);
-- id_paket = 1 (paket yang dituju)
-- id_destinasi = 13 (destinasi baru)
-- urutan = 1 (urutan kunjungan)
```

## 🎯 Fitur yang Sudah Diimplementasikan

✅ **wisatamalang.php** - Otomatis query destinasi dari database  
✅ **Galeri Destinasi** - Menampilkan nama + deskripsi singkat  
✅ **Urutan Destinasi** - Sesuai dengan field `urutan` di database  
✅ **Fleksibel** - Ganti destinasi cukup update database

## 🖼️ Folder Gambar
Pastikan file gambar destinasi sudah ada di folder `/img/`

Contoh struktur:
```
img/
  ├── jtp1.webp
  ├── jtp2.webp
  ├── angkut.webp
  ├── batu.webp
  ├── cobanrondo1.webp
  ├── kawahijen.webp
  ├── bromo.webp
  ├── tumpaksewu.webp
  └── ... (file gambar lainnya)
```

## 💡 Tips

1. **Urutan Destinasi**: Gunakan field `urutan` untuk mengatur urutan kunjungan
2. **Deskripsi**: Gunakan deskripsi singkat (< 100 karakter) untuk gallery
3. **Gambar**: Pastikan format .webp atau .jpg untuk performa optimal
4. **Konsistensi**: Gunakan format nama file yang konsisten (lowercase, no spaces)
