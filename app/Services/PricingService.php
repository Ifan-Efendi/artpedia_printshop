<?php

namespace App\Services;

use App\Models\JenisKertas;
use App\Models\Produk;
use App\Models\UkuranKertas;

class PricingService
{
    public function resolveSpecs(Produk $produk, $ukuranId = null, $jenisId = null): array
    {
        $ukuran = $ukuranId ? UkuranKertas::find($ukuranId) : null;
        $jenis = $jenisId ? JenisKertas::find($jenisId) : null;

        return [
            $ukuran ?: $this->defaultUkuran($produk),
            $jenis ?: $this->defaultJenis($produk),
        ];
    }

    public function calculate(Produk $produk, string $ukuran, string $bahan, $finishing, ?string $potong, int $jumlah = 1): float
    {
        $family = $this->resolveProductFamily($produk);
        $finishing = $this->normalizeFinishing($finishing);
        $price = (float) $produk->harga_satuan;

        if ($family === 'sticker') {
            if ($potong === 'Kiss Cut') {
                $price += 4000;
            } elseif ($potong === 'Die Cut') {
                $price += 8000;
            }
        } elseif ($family === 'kartu-nama') {
            foreach ($finishing as $item) {
                if ($item === 'Glossy' || $item === 'Doff') {
                    $finishingCost = ceil($jumlah / 25) * 4000;
                    $price += ($finishingCost / max($jumlah, 1));
                }
            }

            return $price;
        }

        foreach ($finishing as $item) {
            if ($item === 'Glossy' || $item === 'Doff') {
                $price += 4000;
            }
        }

        return $price;
    }

    public function normalizeFinishing($finishing): array
    {
        if (is_array($finishing)) {
            return array_values(array_filter($finishing));
        }

        if (is_string($finishing) && trim($finishing) !== '') {
            return [trim($finishing)];
        }

        return [];
    }

    public function finishingLabel($finishing): string
    {
        $items = $this->normalizeFinishing($finishing);

        return !empty($items) ? implode(', ', $items) : 'Tidak Pakai';
    }

    private function defaultUkuran(Produk $produk): UkuranKertas
    {
        $text = strtolower(trim(($produk->nama ?? '') . ' ' . ($produk->slug ?? '') . ' ' . ($produk->deskripsi ?? '')));

        foreach (['A3+', 'A3', 'A4', 'A5', 'A6'] as $size) {
            if (str_contains($text, strtolower($size))) {
                $match = UkuranKertas::where('nama', $size)->first();
                if ($match) {
                    return $match;
                }
            }
        }

        if (str_contains($text, 'id-card') || str_contains($text, 'id card') || str_contains($text, 'kartu-nama') || str_contains($text, 'kartu nama')) {
            $match = UkuranKertas::where('nama', 'like', '%Standard%')->first();
            if ($match) {
                return $match;
            }
        }

        return UkuranKertas::firstOrFail();
    }

    private function defaultJenis(Produk $produk): JenisKertas
    {
        $text = strtolower(trim(($produk->nama ?? '') . ' ' . ($produk->slug ?? '') . ' ' . ($produk->deskripsi ?? '')));

        $rules = [
            'pvc' => 'PVC',
            'art carton 260' => 'Art Carton 260 gsm',
            'vinyl transparan' => 'Vinyl Transparan',
            'vinyl' => 'Vinyl',
            'chromo' => 'Chromo Glossy',
            'artpaper 260' => 'Artpaper 260 gsm',
            'artpaper 150' => 'Artpaper 150 gsm',
            'artpaper 120' => 'Artpaper 120 gsm',
            'kartu ucapan' => 'Linen',
            'kartu-ucapan' => 'Linen',
            'id card' => 'PVC',
            'id-card' => 'PVC',
            'kartu nama' => 'Art Carton 260 gsm',
            'kartu-nama' => 'Art Carton 260 gsm',
            'brosur' => 'Artpaper 150 gsm',
            'flyer' => 'Artpaper 150 gsm',
        ];

        foreach ($rules as $needle => $jenisNama) {
            if (str_contains($text, $needle)) {
                $match = JenisKertas::where('nama', $jenisNama)->first();
                if ($match) {
                    return $match;
                }
            }
        }

        return JenisKertas::firstOrFail();
    }

    private function resolveProductFamily(Produk $produk): ?string
    {
        $text = strtolower(trim(
            ($produk->kategori->slug ?? '') . ' ' .
            ($produk->kategori->nama ?? '') . ' ' .
            ($produk->slug ?? '') . ' ' .
            ($produk->nama ?? '') . ' ' .
            ($produk->deskripsi ?? '')
        ));

        if (str_contains($text, 'sticker')) {
            return 'sticker';
        }

        if (str_contains($text, 'kartu nama') || str_contains($text, 'kartu-nama')) {
            return 'kartu-nama';
        }

        if (str_contains($text, 'kartu ucapan') || str_contains($text, 'kartu-ucapan')) {
            return 'kartu-ucapan';
        }

        if (str_contains($text, 'brosur') || str_contains($text, 'flyer')) {
            return 'brosur';
        }

        if (str_contains($text, 'poster')) {
            return 'poster';
        }

        if (str_contains($text, 'id card') || str_contains($text, 'id-card')) {
            return 'id-card';
        }

        return null;
    }
}
