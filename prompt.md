# Requirements
- Backend = Laravel
- Frontend = Vitejs

# CATATAN
## CATATAN 1
Projek ini adalah projek web untuk generate barcode untuk barang dan perusahaan, selain itu web ini juga bisa scan barcode hasil dibuat untuk menampilkan informasi barang dan perusahaan. Jadi barcode dalam web ini ada 2 jenis untuk barcode barang dan barcode perusahaan.

## CATATAN 2

1.Keterangan yang di label yg diprint  
Customer
 part name
part number
 model
 Berat
Qty
inspector name
tgl produksi
tgl expired
 code


2.Didalam Barcode 1 tiap barang 
a.Keterangan yang di label 
Customer
 part name
part number
 model
 Berat
Qty
inspector name
tgl produksi
tgl expired
 code
Posisi rak  dibagian tingkat 

b.Barang masuk dari checker atau finishing 
nomor transfer slip
 tanggal terima barang fg ke gudang
 jumlah box

3.Barcode ke 2 untuk melihat semua barang setiap perusahan :
Nama perusahaan
Nama setiap barang 
Jumlah barang 
Posisi rak dan tingkat 




Barcode + label
Ukuran material
Jenis bahan material pilih : 
a.SPCC
b.SESE
Quantity manterial
No surat jalan material
Tanggal terima masuk ke gudang

## CATATAN 3
terdapat autentikasi dasar seperti register, login, dan logout dengan breeze

# Flowchart pada file `barcode01.drawio`

# To Do

1. Buatkan desain database ERD dari projek dengan dicatat ke file `ERD.md` dan buatkan todo lebih detail pada `TODO.md`
2. Install semua package yang dibutuhkan
3. buatkan 1 kode shell terminal linux untuk generate semua file-file/kode-kode migration, seeder, model, dan controller yang disimpan di file `first.sh`
--> Migration
--> Seeder
--> Model
--> Controller
2. Buatkan kode migration untuk membuat sesuai ERD dari poin 1
3. Buatkan kode seeder sesuai migration
4. Buatkan kode model-model
5. Buatkan kode controller
6. buatkan kode view dengan vitejs