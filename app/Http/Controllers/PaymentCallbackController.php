<?php

namespace App\Http\Controllers;

use App\Models\Pesanan;
use App\Models\Transaksi;
use App\Services\MidtransService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PaymentCallbackController extends Controller
{
    public function __construct(protected MidtransService $midtransService)
    {
    }

    public function finishRedirect(Request $request)
    {
        $orderId = $request->query('order_id');

        if (!$orderId) {
            return redirect()->route('home');
        }

        try {
            $this->midtransService->syncTransactionStatus($orderId);
        } catch (\Exception $e) {
            Log::warning('MIDTRANS_FINISH_REDIRECT_SYNC_FAILED', [
                'order_id' => $orderId,
                'message' => $e->getMessage(),
            ]);
        }

        $baseOrderId = preg_replace('/-\d+$/', '', $orderId);
        $user = auth()->user();

        if (str_starts_with($baseOrderId, 'TRX-')) {
            $transaksi = Transaksi::with('pesanans')->where('nomor_transaksi', $baseOrderId)->first();
            $pesanan = $transaksi?->pesanans->sortBy('id')->first();

            if (!$pesanan) {
                return redirect()->route('home')->with('error', 'Pesanan pembayaran tidak ditemukan.');
            }

            if ($user && $user->role === 'kasir') {
                $routeParams = ['id' => $pesanan->id];
                if (($transaksi->pembayaran_status ?? $pesanan->pembayaran_status) === 'paid') {
                    $routeParams['show_feedback'] = 1;
                }

                return redirect()->route('kasir.pesanan.show', $routeParams);
            }

            $routeParams = ['id' => $pesanan->id];

            if (($transaksi->pembayaran_status ?? $pesanan->pembayaran_status) === 'paid') {
                $routeParams['show_feedback'] = 1;
            }

            return redirect()->route('pelanggan.pesanan.show', $routeParams);
        }

        $pesanan = Pesanan::with('transaksi')->where('nomor_pesanan', $baseOrderId)->first();

        if (!$pesanan) {
            return redirect()->route('home')->with('error', 'Pesanan pembayaran tidak ditemukan.');
        }

        if ($user && $user->role === 'kasir') {
            $routeParams = ['id' => $pesanan->id];
            if (($pesanan->transaksi->pembayaran_status ?? $pesanan->pembayaran_status) === 'paid') {
                $routeParams['show_feedback'] = 1;
            }

            return redirect()->route('kasir.pesanan.show', $routeParams);
        }

        $routeParams = ['id' => $pesanan->id];

        if (($pesanan->transaksi->pembayaran_status ?? $pesanan->pembayaran_status) === 'paid') {
            $routeParams['show_feedback'] = 1;
        }

        return redirect()->route('pelanggan.pesanan.show', $routeParams);
    }

    public function callback(Request $request)
    {
        if ($request->isMethod('get')) {
            return response()->json([
                'message' => 'Midtrans notification endpoint is reachable',
            ]);
        }

        $requiredFields = ['order_id', 'status_code', 'gross_amount', 'signature_key', 'transaction_status'];
        foreach ($requiredFields as $field) {
            if (!$request->filled($field)) {
                Log::info('MIDTRANS_CALLBACK_TEST: Payload tidak lengkap, dianggap test notification', [
                    'payload' => $request->all(),
                ]);

                return response()->json([
                    'message' => 'Midtrans notification endpoint is reachable',
                ]);
            }
        }

        $serverKey = config('midtrans.server_key');
        $hashed = hash("sha512", $request->order_id . $request->status_code . $request->gross_amount . $serverKey);

        if ($hashed !== $request->signature_key) {
            return response()->json(['message' => 'Invalid signature'], 403);
        }

        $orderId = $request->order_id;
        $baseOrderId = preg_replace('/-\d+$/', '', $orderId);
        $status = $request->transaction_status;
        $isTransaction = str_starts_with($baseOrderId, 'TRX-');

        if ($isTransaction) {
            $model = Transaksi::where('nomor_transaksi', $baseOrderId)->first();
        } else {
            $model = Pesanan::where('nomor_pesanan', $baseOrderId)->first();
        }

        if (!$model) {
            Log::info('MIDTRANS_CALLBACK_TEST: Order tidak ditemukan, kemungkinan payload test Midtrans', [
                'order_id' => $orderId,
                'base_order_id' => $baseOrderId,
                'payload' => $request->all(),
            ]);

            return response()->json([
                'message' => 'Callback received, order not found in local database',
            ]);
        }

        $this->midtransService->applyStatusUpdate($orderId, $status);

        Log::info('MIDTRANS_CALLBACK: Status pembayaran berhasil diperbarui', [
            'order_id' => $orderId,
            'base_order_id' => $baseOrderId,
            'transaction_status' => $status,
            'model' => $isTransaction ? 'transaksi' : 'pesanan',
            'model_id' => $model->id,
        ]);

        return response()->json(['message' => 'Callback handled successfully']);
    }
}
