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
            ['nama' => 'Sticker', 'slug' => 'sticker', 'deskripsi' => 'Cetak stiker berkualitas dengan berbagai pilihan bahan dan potong.'],
            ['nama' => 'Poster', 'slug' => 'poster', 'deskripsi' => 'Poster kualitas HD untuk kebutuhan promosi atau dekorasi.'],
            ['nama' => 'Kartu Nama', 'slug' => 'kartu-nama', 'deskripsi' => 'Kartu nama profesional dengan bahan premium.'],
            ['nama' => 'Kartu Ucapan', 'slug' => 'kartu-ucapan', 'deskripsi' => 'Cetak kartu ucapan personal atau bisnis.'],
            ['nama' => 'Brosur', 'slug' => 'brosur', 'deskripsi' => 'Media promosi brosur dengan berbagai ukuran.'],
        ];

        foreach ($kategoris as $kat) {
            KategoriProduk::create($kat);
        }

        // 3. Ukuran Kertas
        $ukurans = [
            ['nama' => 'A3', 'dimensi' => '297x420mm', 'faktor_harga' => 1.00, 'faktor_waktu' => 1.00],
            ['nama' => 'A4', 'dimensi' => '210x297mm', 'faktor_harga' => 1.00, 'faktor_waktu' => 1.00],
            ['nama' => 'A5', 'dimensi' => '148x210mm', 'faktor_harga' => 1.00, 'faktor_waktu' => 1.00],
            ['nama' => 'A6', 'dimensi' => '105x148mm', 'faktor_harga' => 1.00, 'faktor_waktu' => 1.00],
            ['nama' => 'Standard (54x86 mm)', 'dimensi' => '54x86mm', 'faktor_harga' => 1.00, 'faktor_waktu' => 1.00],
            ['nama' => 'Custom', 'dimensi' => 'Custom', 'faktor_harga' => 1.00, 'faktor_waktu' => 1.00],
        ];

        foreach ($ukurans as $uk) {
            UkuranKertas::create($uk);
        }

        // 4. Jenis Kertas / Bahan
        $jenis = [
            ['nama' => 'Vinyl', 'harga_tambahan' => 0],
            ['nama' => 'Vinyl Transparan', 'harga_tambahan' => 0],
            ['nama' => 'Chromo Glossy', 'harga_tambahan' => 0],
            ['nama' => 'Chromo HVS', 'harga_tambahan' => 0],
            ['nama' => 'Artpaper 150 gsm', 'harga_tambahan' => 0],
            ['nama' => 'Artpaper 260 gsm', 'harga_tambahan' => 0],
            ['nama' => 'Artpaper 120 gsm', 'harga_tambahan' => 0],
            ['nama' => 'Linen', 'harga_tambahan' => 0],
            ['nama' => 'Concord', 'harga_tambahan' => 0],
            ['nama' => 'Jasmine', 'harga_tambahan' => 0],
        ];

        foreach ($jenis as $j) {
            JenisKertas::create($j);
        }

        // 5. Produk (Specific Variants)
        $produks = [
            // Sticker (Cat ID: 1)
            [
                'kategori_id' => 1,
                'nama' => 'Sticker Vinyl A3',
                'slug' => 'sticker-vinyl-a3',
                'deskripsi' => 'Cetak stiker bahan Vinyl tahan air ukuran A3 (32x48cm). Termasuk potong.',
                'harga_satuan' => 11000,
                'min_order' => 1,
                'estimasi_waktu_per_unit' => 5,
            ],
            [
                'kategori_id' => 1,
                'nama' => 'Sticker Chromo A3',
                'slug' => 'sticker-chromo-a3',
                'deskripsi' => 'Cetak stiker bahan kertas Chromo mengkilap ukuran A3 (32x48cm).',
                'harga_satuan' => 8000,
                'min_order' => 1,
                'estimasi_waktu_per_unit' => 5,
            ],
            
            // Poster (Cat ID: 2)
            [
                'kategori_id' => 2,
                'nama' => 'Poster Artpaper 260 A3',
                'slug' => 'poster-ap260-a3',
                'deskripsi' => 'Poster dinding kertas tebal Artpaper 260gsm ukuran A3.',
                'harga_satuan' => 7000,
                'min_order' => 1,
                'estimasi_waktu_per_unit' => 3,
            ],
            [
                'kategori_id' => 2,
                'nama' => 'Poster Artpaper 260 A4',
                'slug' => 'poster-ap260-a4',
                'deskripsi' => 'Poster dinding kertas tebal Artpaper 260gsm ukuran A4.',
                'harga_satuan' => 4000,
                'min_order' => 1,
                'estimasi_waktu_per_unit' => 3,
            ],
             [
                'kategori_id' => 2,
                'nama' => 'Poster Artpaper 150 A3',
                'slug' => 'poster-ap150-a3',
                'deskripsi' => 'Poster ekonomis kertas Artpaper 150gsm ukuran A3.',
                'harga_satuan' => 5000,
                'min_order' => 1,
                'estimasi_waktu_per_unit' => 3,
            ],

            // Kartu Nama (Cat ID: 3)
            [
                'kategori_id' => 3,
                'nama' => 'Kartu Nama Standard',
                'slug' => 'kartu-nama-standard',
                'deskripsi' => 'Kartu nama 1 muka bahan Art Carton 260gsm (Box isi 100).',
                'harga_satuan' => 40000, // Per box logic might need adjustment but keeping consistent with system
                'min_order' => 1, // Logic change: 1 box? Or keeping 100 pcs logic? keeping system consistent for now
                'estimasi_waktu_per_unit' => 60,
            ],

            // Kartu Ucapan (Cat ID: 4)
            [
                'kategori_id' => 4,
                'nama' => 'Kartu Ucapan A3',
                'slug' => 'kartu-ucapan-a3',
                'deskripsi' => 'Cetak kartu ucapan custom bahan BW / Carton per lembar A3.',
                'harga_satuan' => 7000,
                'min_order' => 1,
                'estimasi_waktu_per_unit' => 2,
            ],

            // Brosur (Cat ID: 5)
            [
                'kategori_id' => 5,
                'nama' => 'Brosur A4',
                'slug' => 'brosur-a4',
                'deskripsi' => 'Cetak brosur promosi ukuran A4 bahan Artpaper 120gsm.',
                'harga_satuan' => 4000,
                'min_order' => 1,
                'estimasi_waktu_per_unit' => 1,
            ],
            [
                'kategori_id' => 5,
                'nama' => 'Brosur A5',
                'slug' => 'brosur-a5',
                'deskripsi' => 'Cetak brosur promosi hemat ukuran A5 bahan Artpaper 120gsm.',
                'harga_satuan' => 2500,
                'min_order' => 2,
                'estimasi_waktu_per_unit' => 1,
            ],
        ];

        foreach ($produks as $p) {
            Produk::create($p);
        }
    }
}
