<?php

namespace Database\Seeders;

use App\Models\JenisKertas;
use App\Models\KategoriProduk;
use App\Models\Produk;
use App\Models\UkuranKertas;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->seedUsers();
        $kategoriIds = $this->seedKategoris();
        $this->seedUkuranKertas();
        $this->seedJenisKertas();
        $this->seedProduks($kategoriIds);
    }

    private function seedUsers(): void
    {
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
            User::updateOrCreate(
                ['email' => $user['email']],
                $user
            );
        }
    }

    private function seedKategoris(): array
    {
        $kategoris = [
            ['nama' => 'Poster', 'slug' => 'poster', 'deskripsi' => 'Poster kualitas HD untuk kebutuhan promosi atau dekorasi.'],
            ['nama' => 'ID Card', 'slug' => 'id-card', 'deskripsi' => 'Cetak ID card berbahan PVC untuk kebutuhan identitas dan member card.'],
            ['nama' => 'Kartu Nama', 'slug' => 'kartu-nama', 'deskripsi' => 'Kartu nama profesional dengan bahan premium.'],
            ['nama' => 'Kartu Ucapan', 'slug' => 'kartu-ucapan', 'deskripsi' => 'Cetak kartu ucapan personal atau bisnis.'],
            ['nama' => 'Brosur', 'slug' => 'brosur', 'deskripsi' => 'Media promosi brosur dengan berbagai ukuran.'],
            ['nama' => 'Sticker', 'slug' => 'sticker', 'deskripsi' => 'Cetak stiker berkualitas dengan berbagai pilihan bahan dan potong.'],
        ];

        $kategoriIds = [];
        foreach ($kategoris as $kategori) {
            $model = KategoriProduk::updateOrCreate(
                ['slug' => $kategori['slug']],
                $kategori + ['aktif' => true]
            );
            $kategoriIds[$kategori['slug']] = $model->id;
        }

        return $kategoriIds;
    }

    private function seedUkuranKertas(): void
    {
        $ukurans = [
            ['nama' => 'A3', 'dimensi' => '297x420 mm'],
            ['nama' => 'A4', 'dimensi' => '210x297 mm'],
            ['nama' => 'A5', 'dimensi' => '148x210 mm'],
            ['nama' => 'A6', 'dimensi' => '105x148 mm'],
            ['nama' => 'Standard (54x86 mm)', 'dimensi' => '54x86 mm'],
            ['nama' => 'Custom', 'dimensi' => 'Sesuai kebutuhan'],
            ['nama' => 'A3+', 'dimensi' => '320x480 mm'],
        ];

        foreach ($ukurans as $ukuran) {
            UkuranKertas::updateOrCreate(
                ['nama' => $ukuran['nama']],
                $ukuran + [
                    'faktor_harga' => 1,
                    'faktor_waktu' => 1,
                    'aktif' => true,
                ]
            );
        }
    }

    private function seedJenisKertas(): void
    {
        $jenisKertas = [
            ['nama' => 'Vinyl', 'deskripsi' => 'Bahan stiker vinyl tahan air.'],
            ['nama' => 'Vinyl Transparan', 'deskripsi' => 'Bahan stiker vinyl transparan.'],
            ['nama' => 'Chromo Glossy', 'deskripsi' => 'Bahan stiker chromo glossy.'],
            ['nama' => 'Chromo HVS', 'deskripsi' => 'Bahan stiker chromo HVS.'],
            ['nama' => 'Artpaper 150 gsm', 'deskripsi' => 'Kertas artpaper 150 gsm.'],
            ['nama' => 'Artpaper 260 gsm', 'deskripsi' => 'Kertas artpaper 260 gsm.'],
            ['nama' => 'Artpaper 120 gsm', 'deskripsi' => 'Kertas artpaper 120 gsm.'],
            ['nama' => 'Linen', 'deskripsi' => 'Kertas linen bertekstur.'],
            ['nama' => 'Concord', 'deskripsi' => 'Kertas concord.'],
            ['nama' => 'Jasmine', 'deskripsi' => 'Kertas jasmine.'],
            ['nama' => 'PVC', 'deskripsi' => 'Bahan PVC untuk ID Card.'],
            ['nama' => 'Art Carton 260 gsm', 'deskripsi' => 'Kertas art carton 260 gsm.'],
        ];

        foreach ($jenisKertas as $jenis) {
            JenisKertas::updateOrCreate(
                ['nama' => $jenis['nama']],
                $jenis + [
                    'harga_tambahan' => 0,
                    'aktif' => true,
                ]
            );
        }
    }

    private function seedProduks(array $kategoriIds): void
    {
        $legacyStickerTransparan = Produk::where('slug', 'sticker-vinyl-transprant-a3')->first();
        if ($legacyStickerTransparan && ! Produk::where('slug', 'sticker-vinyl-transparan-a3')->exists()) {
            $legacyStickerTransparan->update(['slug' => 'sticker-vinyl-transparan-a3']);
        } elseif ($legacyStickerTransparan) {
            $legacyStickerTransparan->update(['aktif' => false]);
        }

        $produks = [
            [
                'kategori_slug' => 'sticker',
                'nama' => 'Sticker Vinyl Glossy A3+',
                'slug' => 'sticker-vinyl-glossy-a3',
                'deskripsi' => 'Cetak stiker bahan vinyl tahan air ukuran A3+ (32x48 cm).',
                'gambar' => 'produk/sticker-vinyl-a3.png',
                'harga_satuan' => 12000,
                'min_order' => 1,
                'is_finishing' => false,
                'is_cutting' => true,
                'estimasi_waktu_per_unit' => 5,
            ],
            [
                'kategori_slug' => 'sticker',
                'nama' => 'Sticker Chromo A3+',
                'slug' => 'sticker-chromo-a3',
                'deskripsi' => 'Cetak stiker bahan Chromo Glossy ukuran A3+ (32x48 cm).',
                'gambar' => 'produk/sticker-chromo-a3.png',
                'harga_satuan' => 10000,
                'min_order' => 1,
                'is_finishing' => false,
                'is_cutting' => true,
                'estimasi_waktu_per_unit' => 5,
            ],
            [
                'kategori_slug' => 'poster',
                'nama' => 'Poster Artpaper 260 A3',
                'slug' => 'poster-artpaper-260-a3',
                'deskripsi' => 'Poster dinding kertas tebal Artpaper 260 gsm ukuran A3.',
                'gambar' => 'produk/poster-a3.png',
                'harga_satuan' => 7000,
                'min_order' => 1,
                'is_finishing' => true,
                'is_cutting' => false,
                'estimasi_waktu_per_unit' => 3,
            ],
            [
                'kategori_slug' => 'poster',
                'nama' => 'Poster Artpaper 260 A4',
                'slug' => 'poster-artpaper-260-a4',
                'deskripsi' => 'Poster dinding kertas tebal Artpaper 260 gsm ukuran A4.',
                'gambar' => 'produk/poster-a4.png',
                'harga_satuan' => 4000,
                'min_order' => 1,
                'is_finishing' => true,
                'is_cutting' => false,
                'estimasi_waktu_per_unit' => 3,
            ],
            [
                'kategori_slug' => 'id-card',
                'nama' => 'ID Card 1 Muka',
                'slug' => 'id-card-1-muka',
                'deskripsi' => 'Cetak ID card bahan PVC 1 muka.',
                'gambar' => null,
                'harga_satuan' => 8000,
                'min_order' => 10,
                'is_finishing' => false,
                'is_cutting' => false,
                'estimasi_waktu_per_unit' => 2,
            ],
            [
                'kategori_slug' => 'kartu-nama',
                'nama' => 'Kartu Nama 1 Muka',
                'slug' => 'kartu-nama-1-muka',
                'deskripsi' => 'Kartu nama 1 muka bahan Art Carton 260 gsm. Harga per pcs.',
                'gambar' => 'produk/kartu-nama.png',
                'harga_satuan' => 500,
                'min_order' => 25,
                'is_finishing' => false,
                'is_cutting' => false,
                'estimasi_waktu_per_unit' => 2,
            ],
            [
                'kategori_slug' => 'kartu-ucapan',
                'nama' => 'Kartu Ucapan A3',
                'slug' => 'kartu-ucapan-a3',
                'deskripsi' => 'Cetak kartu ucapan custom bahan BW / Carton per lembar A3.',
                'gambar' => 'produk/kartu-ucapan.png',
                'harga_satuan' => 10000,
                'min_order' => 1,
                'is_finishing' => false,
                'is_cutting' => false,
                'estimasi_waktu_per_unit' => 2,
            ],
            [
                'kategori_slug' => 'brosur',
                'nama' => 'Cetak Brosur A4',
                'slug' => 'cetak-brosur-a4',
                'deskripsi' => 'Cetak brosur ukuran A4 bahan Artpaper 150 gsm.',
                'gambar' => 'produk/brosur-a4.png',
                'harga_satuan' => 1200,
                'min_order' => 500,
                'is_finishing' => false,
                'is_cutting' => false,
                'estimasi_waktu_per_unit' => 1,
            ],
            [
                'kategori_slug' => 'brosur',
                'nama' => 'Cetak Brosur A5',
                'slug' => 'cetak-brosur-a5',
                'deskripsi' => 'Cetak brosur ukuran A5 bahan Artpaper 150 gsm.',
                'gambar' => 'produk/brosur-a5.png',
                'harga_satuan' => 800,
                'min_order' => 500,
                'is_finishing' => false,
                'is_cutting' => false,
                'estimasi_waktu_per_unit' => 1,
            ],
            [
                'kategori_slug' => 'id-card',
                'nama' => 'ID Card 2 Muka',
                'slug' => 'id-card-2-muka',
                'deskripsi' => 'Cetak ID card bahan PVC 2 muka.',
                'gambar' => null,
                'harga_satuan' => 12000,
                'min_order' => 5,
                'is_finishing' => false,
                'is_cutting' => false,
                'estimasi_waktu_per_unit' => 2,
            ],
            [
                'kategori_slug' => 'sticker',
                'nama' => 'Sticker Vinyl Transparan A3+',
                'slug' => 'sticker-vinyl-transparan-a3',
                'deskripsi' => 'Cetak stiker bahan vinyl transparan ukuran A3+ (32x48 cm).',
                'gambar' => 'produk/sticker-vinyl-a3.png',
                'harga_satuan' => 12000,
                'min_order' => 1,
                'is_finishing' => true,
                'is_cutting' => true,
                'estimasi_waktu_per_unit' => 5,
            ],
            [
                'kategori_slug' => 'brosur',
                'nama' => 'Cetak Brosur A4 Bolak Balik',
                'slug' => 'cetak-brosur-a4-bolak-balik',
                'deskripsi' => 'Cetak brosur ukuran A4 bolak balik bahan Artpaper 150 gsm.',
                'gambar' => 'produk/brosur-a4.png',
                'harga_satuan' => 1600,
                'min_order' => 500,
                'is_finishing' => false,
                'is_cutting' => false,
                'estimasi_waktu_per_unit' => 1,
            ],
            [
                'kategori_slug' => 'brosur',
                'nama' => 'Cetak Brosur A5 Bolak Balik',
                'slug' => 'cetak-brosur-a5-bolak-balik',
                'deskripsi' => 'Cetak brosur ukuran A5 bolak balik bahan Artpaper 150 gsm.',
                'gambar' => 'produk/brosur-a5.png',
                'harga_satuan' => 1200,
                'min_order' => 500,
                'is_finishing' => false,
                'is_cutting' => false,
                'estimasi_waktu_per_unit' => 1,
            ],
            [
                'kategori_slug' => 'brosur',
                'nama' => 'Cetak Brosur A6',
                'slug' => 'cetak-brosur-a6',
                'deskripsi' => 'Cetak brosur ukuran A6 bahan Artpaper 150 gsm.',
                'gambar' => 'produk/brosur-a4.png',
                'harga_satuan' => 500,
                'min_order' => 500,
                'is_finishing' => false,
                'is_cutting' => false,
                'estimasi_waktu_per_unit' => 1,
            ],
            [
                'kategori_slug' => 'brosur',
                'nama' => 'Cetak Brosur A6 Bolak Balik',
                'slug' => 'cetak-brosur-a6-bolak-balik',
                'deskripsi' => 'Cetak brosur ukuran A6 bolak balik bahan Artpaper 150 gsm.',
                'gambar' => 'produk/brosur-a4.png',
                'harga_satuan' => 600,
                'min_order' => 500,
                'is_finishing' => false,
                'is_cutting' => false,
                'estimasi_waktu_per_unit' => 1,
            ],
            [
                'kategori_slug' => 'kartu-nama',
                'nama' => 'Kartu Nama 2 Muka',
                'slug' => 'kartu-nama-2-muka',
                'deskripsi' => 'Kartu nama 2 muka bahan Art Carton 260 gsm. Harga per pcs, minimal order 25 pcs.',
                'gambar' => 'produk/kartu-nama.png',
                'harga_satuan' => 800,
                'min_order' => 25,
                'is_finishing' => false,
                'is_cutting' => false,
                'estimasi_waktu_per_unit' => 2,
            ],
        ];

        foreach ($produks as $produk) {
            $kategoriSlug = $produk['kategori_slug'];
            unset($produk['kategori_slug']);

            Produk::updateOrCreate(
                ['slug' => $produk['slug']],
                $produk + [
                    'kategori_id' => $kategoriIds[$kategoriSlug],
                    'aktif' => true,
                ]
            );
        }
    }
}
