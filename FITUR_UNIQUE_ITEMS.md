# Fitur Unique Items - Panduan Lengkap

## Apa itu Unique Items?

Unique Items adalah fitur yang memungkinkan Anda membuat variasi dari item yang sama dengan nilai-nilai yang berbeda (seperti qty berbeda, berat berbeda, dll) dalam satu barcode yang sama. Setiap unique item dapat dicetak dengan label terpisah.

## Kapan Menggunakan Fitur Ini?

Gunakan fitur ini ketika:
- Anda memiliki item yang sama tetapi dengan qty berbeda di setiap box
- Ada box yang rusak/kurang sehingga qty-nya berbeda dari standar
- Perlu membuat label dengan qty custom untuk kebutuhan khusus
- Ingin mencetak label dengan nilai berbeda tanpa membuat item baru

## Contoh Kasus Nyata

### Kasus 1: Box Rusak
Item: Bracket Type A
- Qty standar per box: 100 pcs
- Box 1: 100 pcs (normal)
- Box 2: 95 pcs (5 pcs rusak)
- Box 3: 100 pcs (normal)

**Solusi:** Buat unique item dengan qty 95 untuk Box 2

### Kasus 2: Sisa Produksi
Item: Cover Panel B
- Qty standar per box: 50 pcs
- Produksi total: 275 pcs
- Box 1-5: 50 pcs (normal) = 250 pcs
- Box 6: 25 pcs (sisa)

**Solusi:** Buat unique item dengan qty 25 untuk Box 6

### Kasus 3: Berat Berbeda
Item: Material C
- Berat standar: 10 kg
- Batch khusus: 8 kg (material lebih ringan)

**Solusi:** Buat unique item dengan qty/berat 8 untuk batch khusus

## Cara Menggunakan

### 1. Akses Halaman Detail Barcode
- Buka menu "Barcode Barang"
- Klik "Lihat" pada item yang ingin ditambahkan unique item
- Scroll ke bawah hingga menemukan section "Unique Items"

### 2. Menambah Unique Item Baru
1. Klik tombol **"+ Tambah Unique Item"**
2. Form input akan muncul
3. Masukkan **Qty (pcs)** yang diinginkan
4. Klik **"Simpan"**
5. Unique item baru akan muncul di daftar

### 3. Mencetak Label Unique Item
1. Cari unique item yang ingin dicetak di daftar
2. Klik tombol **"Cetak Label"**
3. Label akan terbuka di tab baru
4. Gunakan fungsi print browser (Ctrl+P) untuk mencetak
5. Label akan menampilkan qty sesuai unique item

### 4. Mengedit Unique Item
1. Klik tombol **"Edit"** pada unique item yang ingin diubah
2. Form edit akan muncul
3. Ubah nilai qty
4. Klik **"Update"** untuk menyimpan
5. Klik **"Batal"** untuk membatalkan

### 5. Menghapus Unique Item
1. Klik tombol **"Hapus"** pada unique item
2. Konfirmasi penghapusan dengan klik "OK"
3. Unique item akan dihapus dari daftar

## Informasi Teknis

### Database
Tabel: `unique_items`
- `id` - ID unik
- `item_id` - Referensi ke item (nullable)
- `qty` - Quantity untuk unique item
- `created_at` - Waktu dibuat
- `updated_at` - Waktu diupdate

### Relasi
- Setiap unique item terhubung ke satu item
- Satu item dapat memiliki banyak unique items
- Jika item dihapus, unique items akan tetap ada dengan item_id = null

### Label
- Label unique item menggunakan format yang sama dengan label item biasa
- Barcode dan QR code tetap sama (menggunakan barcode item)
- Hanya qty yang berbeda sesuai dengan unique item

## Tips Penggunaan

1. **Beri Nama yang Jelas**: Meskipun saat ini hanya ada field qty, pastikan Anda mencatat di tempat lain jika ada informasi tambahan

2. **Cetak Segera**: Setelah membuat unique item, segera cetak labelnya agar tidak lupa

3. **Verifikasi Qty**: Pastikan qty yang diinput sudah benar sebelum mencetak label

4. **Gunakan Seperlunya**: Tidak perlu membuat unique item untuk setiap box jika qty-nya sama dengan standar

5. **Dokumentasi**: Catat alasan pembuatan unique item (misalnya: "box rusak", "sisa produksi", dll) di sistem lain jika diperlukan

## Troubleshooting

### Tombol "Tambah Unique Item" tidak muncul
- Pastikan Anda berada di halaman detail barcode barang
- Refresh halaman browser

### Form tidak muncul setelah klik tombol
- Cek console browser untuk error JavaScript
- Pastikan JavaScript tidak diblokir

### Label tidak tercetak dengan benar
- Pastikan printer sudah terkonfigurasi dengan benar
- Coba print preview terlebih dahulu
- Periksa setting ukuran kertas

### Unique item tidak tersimpan
- Pastikan qty diisi dengan angka positif
- Cek koneksi database
- Lihat error message yang muncul

## Pengembangan Selanjutnya

Fitur yang bisa ditambahkan di masa depan:
- Field tambahan: berat, dimensi, catatan
- Bulk create unique items
- Export/import unique items
- History perubahan unique items
- Notifikasi saat unique item dibuat/diubah

## Kontak Support

Jika ada pertanyaan atau masalah terkait fitur ini, silakan hubungi tim IT atau developer yang bertanggung jawab.
