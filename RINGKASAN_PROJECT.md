# Ringkasan Project Artpedia Printshop

Tanggal catatan: 11 Mei 2026

File ini dibuat sebagai pengingat percakapan dan progres project supaya pengembangan berikutnya bisa langsung nyambung.

## Gambaran Project

Project ini adalah sistem informasi pemesanan percetakan untuk Artpedia Printshop berbasis Laravel.

Role utama:
- Pelanggan
- Kasir
- Operator Produksi

Fitur utama:
- katalog produk percetakan
- pemesanan pelanggan
- pemesanan langsung oleh kasir
- keranjang multi-item
- pembayaran cash dan cashless/Midtrans
- tracking status pesanan
- antrian produksi dengan algoritma SJF
- dashboard kasir dan produksi
- riwayat pesanan/produksi

## Integrasi Midtrans

Yang sudah dikerjakan:
- konfigurasi Midtrans sandbox sudah disesuaikan lewat `.env`
- endpoint callback Midtrans dibuat/dirapikan
- payment finish redirect diarahkan balik ke sistem
- status `settlement` / `capture` disinkronkan menjadi pembayaran berhasil
- setelah pembayaran berhasil, pesanan masuk ke `dalam_antrian`
- polling status pembayaran ditambahkan supaya halaman tidak perlu refresh manual
- pelanggan dan kasir sama-sama bisa cek status pembayaran otomatis
- teks `Midtrans` pada beberapa UI diganti menjadi istilah umum seperti `Cashless`

URL penting untuk Midtrans sandbox:
- Notification URL: `/api/midtrans/callback`
- Finish Redirect URL: `/payment/finish`
- Unfinish Redirect URL: `/payment/finish`
- Error Redirect URL: `/payment/finish`

Catatan:
- untuk test lokal end-to-end dengan Midtrans tetap butuh URL publik seperti ngrok
- jika nanti pindah hosting production, ganti `APP_URL`, key Midtrans production, dan URL dashboard Midtrans

## Alur Pesanan Pelanggan

Yang sudah dikerjakan:
- halaman checkout pelanggan perantara dihilangkan
- dari keranjang pelanggan, tombol `Checkout` langsung membuat pesanan dan transaksi
- setelah checkout, pelanggan diarahkan ke detail/status pembayaran
- pelanggan bisa checkout lebih dari 1 item sekaligus
- semua item dalam sekali checkout memakai nomor pesanan yang sama
- detail pesanan pelanggan sudah menampilkan semua item dalam satu nomor pesanan
- ringkasan pembayaran pelanggan memakai total gabungan
- file desain tampil per item
- tombol `Kosongkan` keranjang pelanggan sudah ditambahkan
- tombol bukti pembayaran lama sudah dihapus karena pembayaran cashless lewat Midtrans

Status/tracking:
- `Menunggu Pembayaran`
- `Pembayaran Berhasil`
- `Dalam Antrian`
- `Produksi Selesai`

Catatan:
- step `Dalam Antrian` di tracking sudah dibuat hijau ketika status pesanan memang `dalam_antrian`

## Alur Pesanan Kasir

Yang sudah dikerjakan:
- alur kasir dibuat mirip pelanggan
- kasir tambah item ke keranjang
- halaman checkout kasir perantara dihilangkan
- dari keranjang kasir langsung mengisi:
  - nama pelanggan
  - nomor WA
  - email opsional
  - metode pembayaran `Cash` atau `Cashless`
- tombol utama tetap `Checkout`
- kasir wajib upload file desain/cetak
- opsi `foto produk menyusul` dihapus

Multi-item:
- checkout kasir bisa lebih dari 1 item
- item dalam satu checkout memakai nomor pesanan yang sama
- detail pesanan kasir sudah menampilkan semua item dalam grup nomor pesanan
- total pembayaran kasir memakai total grup
- file desain tampil per item

Pembayaran kasir:
- cash langsung dianggap `Pembayaran Berhasil` dan masuk antrian
- cashless memakai Midtrans/Snap
- setelah cash dibuat, muncul popup `Pesanan Berhasil Dibuat`
- setelah cashless berhasil, muncul popup `Pembayaran Berhasil`
- popup punya tombol:
  - `Dashboard`
  - `Lihat Antrian`
- popup punya tombol `X` untuk close dan tetap berada di halaman detail

Data pemesan:
- urutan tampilan sudah dibuat:
  - nama
  - nomor WA
  - email
- kalau email tidak diisi, tampil `-`
- email dummy lama seperti `nomorhp@artpedia.com` disembunyikan menjadi `-` di tampilan

## Keranjang

Pelanggan:
- bisa tambah banyak item
- bisa checkout multi-item
- bisa kosongkan keranjang
- tampilan qty dibuat tanpa kotak dan tanpa kata unit di kolom qty
- produk di keranjang memakai gambar produk jika ada

Kasir:
- bisa tambah banyak item
- bisa kosongkan keranjang
- form data pelanggan dan metode pembayaran ada langsung di halaman keranjang
- checkout multi-item sudah memakai satu nomor pesanan grup

## Data Produk, Harga, dan Spesifikasi

Yang sudah dikerjakan:
- data master kategori dan produk aktif sempat diaudit
- beberapa inkonsistensi produk/kategori diperbaiki
- `min_order` dipaksa di frontend dan backend
- unit bisnis produk disamakan memakai:
  - `lembar`
  - `pcs`
  - `box`
- opsi finishing/cutting sekarang mengikuti flag produk, bukan hardcoded
- backend juga menyaring finishing/cutting agar tidak tersimpan jika produk tidak mendukung

Catatan penting:
- eksperimen tambahan bahan seperti Concord/Linen/Jasmine/Copenhagen sempat dibahas lalu di-undo karena bertentangan dengan menu kelola produk
- jangan menambahkan variasi bahan baru langsung di kode tanpa menyelaraskan dengan fitur Kelola Produk

## Produksi dan Algoritma SJF

Yang sudah dikerjakan:
- antrian produksi memakai algoritma SJF berdasarkan estimasi waktu pengerjaan paling singkat
- urutan antrian:
  - `estimasi_waktu ASC`
  - `dikonfirmasi_at ASC`
  - `id ASC`
- antrian dihitung per item pesanan/produk, bukan per pelanggan
- satu operator hanya boleh punya satu pesanan `diproses`
- tombol `Kerjakan` hanya aktif untuk urutan pertama
- item selain urutan pertama tombolnya disabled
- attempt memulai item selain urutan pertama dibuat silent/no notification
- dashboard produksi dibuat realtime polling agar antrian baru muncul tanpa refresh manual

Teks SJF yang pernah disepakati:
`Pesanan diurutkan otomatis, dan yang waktu pengerjaannya paling singkat akan dikerjakan lebih dulu.`

## Tampilan Produksi

Yang sudah dikerjakan:
- label `TERPENDEK` diganti menjadi `URUTAN 1`
- tombol `Detail`, `Kerjakan`, dan `Selesai` dibuat konsisten ukurannya
- dashboard produksi dirapikan:
  - label prioritas dihapus
  - ukuran teks produk sedang diproses diperkecil
- detail produksi:
  - `Riwayat Verifikasi` diganti menjadi `Status Pembayaran`
  - `Pembayaran Valid` diganti menjadi `Pembayaran Berhasil`
  - teks `Diverifikasi oleh Kasir Artpedia` dihilangkan
  - breadcrumb atas seperti `Dashboard / Antrian / ART-...` dihapus
  - label `Instruksi Khusus` diganti menjadi `Catatan Pembeli`

## Landing Page

Yang sudah terjadi:
- sempat beberapa kali dicoba untuk membuat landing page seperti contoh hero besar dengan produk showcase
- hasil percobaan dianggap belum rapi oleh user
- perubahan tersebut sudah di-undo
- landing page dikembalikan ke versi awal yang lebih simpel

Catatan untuk lanjut:
- user ingin landing page mirip contoh dengan:
  - judul besar `Artpedia Printshop`
  - tagline handwriting tipis
  - maskot di kanan
  - produk percetakan di bawah seperti panggung/showcase
  - section `Mengapa Memilih Kami?`
- jangan eksekusi desain landing page besar tanpa persetujuan eksplisit
- pendekatan yang lebih aman nanti:
  - kerjakan hero saja dulu
  - siapkan aset maskot dan produk yang benar-benar bersih
  - uji desktop/mobile sebelum lanjut section bawah

## Deploy dan Environment

Yang sudah dikerjakan:
- file sisa Railway/Nixpacks sudah dihapus:
  - `nixpacks.toml`

Catatan:
- project sekarang difokuskan untuk lokal/XAMPP dan kemungkinan hosting lain
- untuk localhost, asset sudah diperbaiki agar tidak dipaksa HTTPS/ngrok saat `APP_ENV=local`
- ngrok masih diperlukan jika ingin test callback Midtrans dari lokal

## File Penting yang Sering Tersentuh

Controller:
- `app/Http/Controllers/CartController.php`
- `app/Http/Controllers/KasirController.php`
- `app/Http/Controllers/PesananController.php`
- `app/Http/Controllers/ProduksiController.php`
- `app/Http/Controllers/PaymentCallbackController.php`

Service:
- `app/Services/MidtransService.php`
- `app/Services/PricingService.php`

Model:
- `app/Models/Pesanan.php`
- `app/Models/Produk.php`

View utama:
- `resources/views/welcome.blade.php`
- `resources/views/pelanggan/cart/index.blade.php`
- `resources/views/pelanggan/pesanan/show.blade.php`
- `resources/views/kasir/cart/index.blade.php`
- `resources/views/kasir/pesanan/show.blade.php`
- `resources/views/produksi/antrian.blade.php`
- `resources/views/produksi/dashboard.blade.php`
- `resources/views/produksi/show.blade.php`

## Hal yang Perlu Hati-hati

- Jangan mengubah alur pembayaran Midtrans tanpa test transaksi baru.
- Jangan mengembalikan unique constraint `nomor_pesanan`, karena multi-item checkout memakai nomor pesanan yang sama.
- Jangan menambahkan variasi produk/bahan langsung di kode kalau belum sinkron dengan menu Kelola Produk.
- Jangan menghapus file desain/temp tanpa memastikan tidak ada session/order aktif yang masih butuh.
- Jangan melakukan `git reset --hard` atau revert besar karena project sudah banyak perubahan manual.

## Rekomendasi Next Step

- Test ulang alur pelanggan:
  - tambah 2 item
  - checkout
  - bayar cashless
  - pastikan semua item masuk detail dan antrian

- Test ulang alur kasir:
  - tambah 2 item
  - checkout cash
  - cek popup
  - cek antrian produksi
  - ulangi cashless

- Test produksi:
  - pastikan hanya urutan pertama bisa dikerjakan
  - pastikan operator tidak bisa mengambil 2 job
  - pastikan dashboard produksi realtime

- Landing page:
  - diskusikan ulang konsep visual sebelum implementasi
  - mulai dari hero kecil dulu, jangan rombak total
