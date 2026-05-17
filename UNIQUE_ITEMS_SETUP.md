# Setup Fitur Unique Items

## Deskripsi
Fitur Unique Items memungkinkan Anda untuk membuat variasi dari item yang sama dengan nilai berbeda (misalnya qty berbeda, berat berbeda, dll) namun tetap menggunakan barcode yang sama. Setiap unique item dapat dicetak dengan label terpisah.

## Langkah-langkah Setup

### 1. Jalankan Migrasi Database
Jalankan perintah berikut untuk membuat tabel `unique_items`:

```bash
php artisan migrate
```

Migrasi akan membuat tabel dengan struktur:
- `id` - Primary key
- `item_id` - Foreign key ke tabel items (nullable, cascade on delete)
- `qty` - Quantity untuk unique item ini
- `timestamps` - Created at & Updated at

### 2. Cara Menggunakan

#### Di Halaman Detail Barcode Barang:

1. **Menambah Unique Item:**
   - Klik tombol "+ Tambah Unique Item"
   - Masukkan qty yang diinginkan
   - Klik "Simpan"

2. **Mencetak Label Unique Item:**
   - Setiap unique item memiliki tombol "Cetak Label"
   - Label akan menggunakan format yang sama dengan label item biasa
   - Qty pada label akan sesuai dengan qty unique item

3. **Mengedit Unique Item:**
   - Klik tombol "Edit" pada unique item yang ingin diubah
   - Ubah qty
   - Klik "Update"

4. **Menghapus Unique Item:**
   - Klik tombol "Hapus" pada unique item
   - Konfirmasi penghapusan

## Contoh Penggunaan

Misalnya Anda memiliki item dengan:
- Part Name: "Bracket A"
- Static Qty: 100 pcs
- Qty Sub Pack: 10 pcs

Namun ada beberapa box dengan qty berbeda:
- Box 1: 8 pcs (rusak 2 pcs)
- Box 2: 12 pcs (tambahan)
- Box 3: 5 pcs (sisa)

Anda dapat membuat 3 unique items dengan qty masing-masing 8, 12, dan 5. Setiap unique item dapat dicetak labelnya dengan qty yang sesuai.

## File yang Dimodifikasi/Dibuat

1. **Model:**
   - `app/Models/UniqueItem.php` (baru)
   - `app/Models/Item.php` (ditambahkan relasi uniqueItems)

2. **Controller:**
   - `app/Http/Controllers/ItemBarcodeController.php` (ditambahkan 4 method baru)

3. **Routes:**
   - `routes/web.php` (ditambahkan 4 route baru)

4. **Views:**
   - `resources/views/item-barcodes/show.blade.php` (ditambahkan section unique items)

5. **Migration:**
   - `database/migrations/2026_05_04_000003_add_unique_items_table.php`

## Routes yang Ditambahkan

```php
POST   /item-barcodes/{itemBarcode}/unique-items                    - Tambah unique item
PATCH  /item-barcodes/{itemBarcode}/unique-items/{uniqueItem}       - Update unique item
DELETE /item-barcodes/{itemBarcode}/unique-items/{uniqueItem}       - Hapus unique item
GET    /item-barcodes/{itemBarcode}/unique-items/{uniqueItem}/print - Cetak label unique item
```

## Catatan

- Unique items terhubung ke item melalui foreign key dengan `nullOnDelete()`, sehingga jika item dihapus, unique items akan tetap ada dengan `item_id = null`
- Setiap unique item dapat dicetak labelnya secara terpisah dengan qty yang sesuai
- UI untuk unique items hanya muncul di halaman detail barcode barang (tidak muncul saat print)
