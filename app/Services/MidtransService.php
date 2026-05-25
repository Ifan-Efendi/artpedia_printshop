<?php

namespace App\Services;

use App\Models\Pesanan;
use App\Models\Transaksi;
use Illuminate\Support\Facades\Log;

class MidtransService
{
    private function init()
    {
        \Midtrans\Config::$serverKey = config('midtrans.server_key');
        \Midtrans\Config::$isProduction = config('midtrans.is_production');
        \Midtrans\Config::$isSanitized = config('midtrans.is_sanitized');
        \Midtrans\Config::$is3ds = config('midtrans.is_3ds');
        \Midtrans\Config::$curlOptions = [];
        \Midtrans\Config::$appendNotifUrl = config('midtrans.notification_url');
    }

    public function getSnapToken($model)
    {
        $this->init();

        $orderId = ($model instanceof Pesanan) ? $model->nomor_pesanan : $model->nomor_transaksi;
        
        // Tambahkan suffix unik agar tidak terjadi error duplicate order ID di Midtrans
        $uniqueOrderId = $orderId . '-' . time();
        $amount = (int) $model->total_harga;

        $params = [
            'transaction_details' => [
                'order_id' => $uniqueOrderId,
                'gross_amount' => $amount,
            ],
            'callbacks' => [
                'finish' => route('payment.finish'),
                'unfinish' => route('payment.finish'),
                'error' => route('payment.finish'),
            ],
            'customer_details' => [
                'first_name' => $model->user->name,
                'email' => $model->user->email,
                'phone' => $model->user->telepon,
            ],
            'item_details' => [
                [
                    'id' => $orderId,
                    'price' => $amount,
                    'quantity' => 1,
                    'name' => 'Pesanan Artpedia Print',
                ],
            ],
        ];

        try {
            return \Midtrans\Snap::getSnapToken($params);
        } catch (\Exception $e) {
            Log::error('MIDTRANS: Gagal mendapatkan token pembayaran', [
                'order_id' => $orderId,
                'message' => $e->getMessage(),
                'code' => $e->getCode()
            ]);
            throw $e;
        }
    }

    public function syncTransactionStatus(string $orderId)
    {
        $this->init();

        $statusResponse = \Midtrans\Transaction::status($orderId);
        $transactionStatus = $statusResponse->transaction_status ?? null;

        if ($transactionStatus) {
            $this->applyStatusUpdate($orderId, $transactionStatus);
        }

        return $statusResponse;
    }

    public function applyStatusUpdate(string $orderId, string $transactionStatus): void
    {
        $baseOrderId = preg_replace('/-\d+$/', '', $orderId);
        $isTransaction = str_starts_with($baseOrderId, 'TRX-');

        if ($isTransaction) {
            $model = Transaksi::with('pesanans')->where('nomor_transaksi', $baseOrderId)->first();
        } else {
            $model = Pesanan::where('nomor_pesanan', $baseOrderId)->first();
        }

        if (!$model) {
            Log::warning('MIDTRANS_SYNC: Order lokal tidak ditemukan', [
                'order_id' => $orderId,
                'base_order_id' => $baseOrderId,
                'transaction_status' => $transactionStatus,
            ]);
            return;
        }

        if (in_array($transactionStatus, ['capture', 'settlement'], true)) {
            if ($isTransaction) {
                $model->update([
                    'pembayaran_status' => 'paid',
                    'status' => 'valid',
                    'dikonfirmasi_at' => now(),
                ]);

                foreach ($model->pesanans as $pesanan) {
                    $pesanan->update([
                        'pembayaran_status' => 'paid',
                        'status' => 'dalam_antrian',
                        'dikonfirmasi_at' => now(),
                    ]);
                }
            } else {
                $model->update([
                    'pembayaran_status' => 'paid',
                    'status' => 'dalam_antrian',
                    'dikonfirmasi_at' => now(),
                ]);
            }

            return;
        }

        if ($transactionStatus === 'pending') {
            $model->update(['pembayaran_status' => 'pending']);
            return;
        }

        if (in_array($transactionStatus, ['deny', 'expire', 'cancel'], true)) {
            $model->update(['pembayaran_status' => 'failed']);
        }
    }
}
