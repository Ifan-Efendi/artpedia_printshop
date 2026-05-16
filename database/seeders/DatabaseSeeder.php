<?php

namespace Database\Seeders;

use App\Models\KategoriProduk;
use App\Models\UkuranKertas;
use App\Models\JenisKertas;
use App\Models\Produk;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // 1. Akun Demo
        $users = [
            [
                'name' => 'Kasir Artpedia',
                'email' => 'kasirartpedia@gmail.com',
                'password' => Hash::make('kasir123'),
                'role' => 'kasir',
                'telepon' => '081234567890',
            ],
            [
                'name' => 'Operator Produksi',
                'email' => 'operatorproduksi@gmail.com',
                'password' => Hash::make('operator123'),
                'role' => 'operator_produksi',
                'telepon' => '081234567891',
            ],
            [
                'name' => 'Ifan',
                'email' => 'ifan@gmail.com',
                'password' => Hash::make('ifan123'),
                'role' => 'pelanggan',
                'telepon' => '081234567892',
            ],
        ];

        foreach ($users as $user) {
            User::create($user);
        }

        // 2. Kategori Produk
        $kategoris = [
            ['nama' => 'Poster', 'slug' => 'poster', 'deskripsi' => 'Poster kualitas HD untuk kebutuhan promosi atau dekorasi.'],
            ['nama' => 'ID Card', 'slug' => 'id-card', 'deskripsi' => 'Cetak ID card berbahan PVC untuk kebutuhan identitas dan member card.'],
            ['nama' => 'Kartu Nama', 'slug' => 'kartu-nama', 'deskripsi' => 'Kartu nama profesional dengan bahan premium.'],
            ['nama' => 'Kartu Ucapan', 'slug' => 'kartu-ucapan', 'deskripsi' => 'Cetak kartu ucapan personal atau bisnis.'],
            ['nama' => 'Brosur', 'slug' => 'brosur', 'deskripsi' => 'Media promosi brosur dengan berbagai ukuran.'],
            ['nama' => 'Sticker', 'slug' => 'sticker', 'deskripsi' => 'Cetak stiker berkualitas dengan berbagai pilihan bahan dan potong.'],
        ];

        foreach ($kategoris as $kat) {
            KategoriProduk::create($kat);
        }

        // ... (Ukuran & Jenis Kertas tetap) ...
        // (Saya asumsikan bagian Ukuran & Jenis Kertas sudah ada di atas baris 97)

        // 5. Produk Aktif
        $produks = [
            [
                'kategori_id' => 6,
                'nama' => 'Sticker Vinyl Glossy A3+',
                'slug' => 'sticker-vinyl-a3',
                'deskripsi' => 'Cetak stiker bahan vinyl tahan air ukuran A3+ (32x48 cm).',
                'harga_satuan' => 11000,
                'min_order' => 1,
                'is_finishing' => false,
                'is_cutting' => true,
                'estimasi_waktu_per_unit' => 5,
            ],
            [
                'kategori_id' => 6,
                'nama' => 'Sticker Chromo A3+',
                'slug' => 'sticker-chromo-a3',
                'deskripsi' => 'Cetak stiker bahan Chromo Glossy ukuran A3+ (32x48 cm).',
                'harga_satuan' => 8000,
                'min_order' => 1,
                'is_finishing' => false,
                'is_cutting' => true,
                'estimasi_waktu_per_unit' => 5,
            ],
            [
                'kategori_id' => 1,
                'nama' => 'Poster Artpaper 260 A3',
                'slug' => 'poster-artpaper-260-a3',
                'deskripsi' => 'Poster dinding kertas tebal Artpaper 260gsm ukuran A3.',
                'harga_satuan' => 7000,
                'min_order' => 1,
                'is_finishing' => true,
                'estimasi_waktu_per_unit' => 3,
            ],
            [
                'kategori_id' => 1,
                'nama' => 'Poster Artpaper 260 A4',
                'slug' => 'poster-artpaper-260-a4',
                'deskripsi' => 'Poster dinding kertas tebal Artpaper 260gsm ukuran A4.',
                'harga_satuan' => 4000,
                'min_order' => 1,
                'is_finishing' => true,
                'estimasi_waktu_per_unit' => 3,
            ],
            [
                'kategori_id' => 3,
                'nama' => 'Kartu Nama 1 Muka',
                'slug' => 'kartu-nama-1-muka',
                'deskripsi' => 'Kartu nama 1 muka bahan Art Carton 260 gsm. Harga per pcs.',
                'harga_satuan' => 480,
                'min_order' => 1,
                'is_finishing' => true,
                'is_cutting' => false,
                'estimasi_waktu_per_unit' => 2,
            ],
            [
                'kategori_id' => 4,
                'nama' => 'Kartu Ucapan A3',
                'slug' => 'kartu-ucapan-a3',
                'deskripsi' => 'Cetak kartu ucapan custom bahan BW / Carton per lembar A3.',
                'harga_satuan' => 7000,
                'min_order' => 1,
                'is_finishing' => false,
                'is_cutting' => false,
                'estimasi_waktu_per_unit' => 2,
            ],
            [
                'kategori_id' => 5,
                'nama' => 'Cetak Brosur A4',
                'slug' => 'cetak-brosur-a4',
                'deskripsi' => 'Cetak brosur ukuran A4 bahan Artpaper 150gsm',
                'harga_satuan' => 1200,
                'min_order' => 500,
                'is_finishing' => false,
                'is_cutting' => false,
                'estimasi_waktu_per_unit' => 1,
            ],
            [
                'kategori_id' => 5,
                'nama' => 'Cetak Brosur A5',
                'slug' => 'cetak-brosur-a5',
                'deskripsi' => 'Cetak brosur ukuran A5 bahan Artpaper 150gsm',
                'harga_satuan' => 800,
                'min_order' => 500,
                'is_finishing' => false,
                'is_cutting' => false,
                'estimasi_waktu_per_unit' => 1,
            ],
            [
                'kategori_id' => 2,
                'nama' => 'ID Card 1 Muka',
                'slug' => 'id-card-1-muka',
                'deskripsi' => 'Cetak ID card bahan PVC 1 muka.',
                'harga_satuan' => 8000,
                'min_order' => 1,
                'is_finishing' => false,
                'is_cutting' => false,
                'estimasi_waktu_per_unit' => 2,
            ],
            [
                'kategori_id' => 2,
                'nama' => 'ID Card',
                'slug' => 'id-card',
                'deskripsi' => 'Cetak ID card bahan PVC.',
                'harga_satuan' => 4500,
                'min_order' => 1,
                'is_finishing' => false,
                'is_cutting' => false,
                'estimasi_waktu_per_unit' => 2,
            ],
            [
                'kategori_id' => 5,
                'nama' => 'Cetak Brosur A4 Bolak - Balik',
                'slug' => 'cetak-brosur-a4-bolak-balik',
                'deskripsi' => 'cetak brosur ukuran A4 bolak balik bahan artpaper 150gsm',
                'harga_satuan' => 1600,
                'min_order' => 500,
                'is_finishing' => false,
                'is_cutting' => false,
                'estimasi_waktu_per_unit' => 1,
            ],
            [
                'kategori_id' => 5,
                'nama' => 'Cetak Brosur A5 Bolak Balik',
                'slug' => 'cetak-brosur-a5-bolak-balik',
                'deskripsi' => 'cetak brosur ukuran A5 bolak balik bahan artpaper 150gsm',
                'harga_satuan' => 1200,
                'min_order' => 500,
                'is_finishing' => false,
                'is_cutting' => false,
                'estimasi_waktu_per_unit' => 1,
            ],
            [
                'kategori_id' => 5,
                'nama' => 'Cetak Brosur A6',
                'slug' => 'cetak-brosur-a6',
                'deskripsi' => 'Cetak brosur ukuran A6 bahan artpaper 150gsm',
                'harga_satuan' => 500,
                'min_order' => 500,
                'is_finishing' => false,
                'is_cutting' => false,
                'estimasi_waktu_per_unit' => 1,
            ],
            [
                'kategori_id' => 5,
                'nama' => 'Cetak Brosur A6 Bolak Balik',
                'slug' => 'cetak-brosur-a6-bolak-balik',
                'deskripsi' => 'Cetak brosur ukuran A6 bolak balik bahan artpaper 150gsm',
                'harga_satuan' => 600,
                'min_order' => 500,
                'is_finishing' => false,
                'is_cutting' => false,
                'estimasi_waktu_per_unit' => 1,
            ],
        ];

        foreach ($produks as $p) {
            Produk::create($p);
        }
    }
}
