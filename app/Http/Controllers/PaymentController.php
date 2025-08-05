<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Midtrans\Snap;
use Midtrans\Config;
use App\Models\Audience;
use App\Models\InvoiceHistory;

class PaymentController extends Controller
{
    public function __construct()
    {
        Config::$serverKey = config('midtrans.server_key');
        Config::$isProduction = config('midtrans.is_production');
        Config::$isSanitized = true;
        Config::$is3ds = true;
    }

    public function getSnapToken(Request $request)
    {
        // Validasi input jika diperlukan
        $request->validate([
            'audience_id' => 'required|exists:audiences,id',
        ]);

        $invoiceHistory = InvoiceHistory::where('audience_id', $request->input('audience_id'))->first();
        if ($invoiceHistory) {
            // Jika sudah ada riwayat pembayaran, kembalikan token yang sudah ada
            return response()->json([
                'snap_token' => $invoiceHistory->snap_token,
                'audience_id' => $invoiceHistory->audience_id,
            ]);
        }else{
            // Jika belum ada, buat riwayat pembayaran baru
            // Ambil data peserta berdasarkan ID
            $audience = Audience::findOrFail($request->input('audience_id'));

            // Buat order ID unik untuk transaksi
            $order_id = 'ORDER-' . uniqid();
            // Siapkan parameter untuk Snap Token
            $params = [
                'transaction_details' => [
                    'order_id' => $order_id,
                    'gross_amount' => $audience->paid_fee, // Gunakan biaya yang sudah dibayar
                ],
                'customer_details' => [
                    'first_name' => $audience->first_name,
                    'last_name' => $audience->last_name,
                    'email' => $audience->email,
                    'phone' => $audience->phone_number,
                ],
                'expiry' => [
                    'start_time' => date("Y-m-d H:i:s T"), // Format harus sesuai dengan dokumentasi Midtrans
                    'unit'       => 'hour',
                    'duration'   => 24, // expired dalam 60 menit, misalnya
                ],
            ];

            // Dapatkan Snap Token dari Midtrans
            $snapToken = \Midtrans\Snap::getSnapToken($params);

            InvoiceHistory::create([
                'id' => $order_id,
                'audience_id' => $audience->id,
                'amount' => $audience->paid_fee,
                'snap_token' => $snapToken,
                'payment_method' => 'midtrans', // Metode pembayaran yang digunakan
                'expired_at' => now()->addHours(24)->timestamp, // Set waktu expired 24 jam dari sekarang
                'status' => 'pending', // Status awal adalah pending
            ]);

            return response()->json([
                'snap_token' => $snapToken,
                'audience_id' => $audience->id, // Kembalikan ID peserta untuk referensi di frontend
            ]);
        }
    }

    public function handleNotification(Request $request){

        $notif = new \Midtrans\Notification();
        $transaction = $notif->transaction_status;
        $orderId = $notif->order_id;

        $invoiceHistory = InvoiceHistory::where('id', $orderId)->first();

        InvoiceHistory::where('id', $orderId)->update([
            'status' => $transaction,
        ]);

        $mappingStatus = [
            'capture' => 'paid',
            'settlement' => 'paid',
            'pending' => 'pending_payment',
            'deny' => 'failed',
            'expire' => 'expired',
            'cancel' => 'cancelled',
        ];
        Audience::where('id', $invoiceHistory->audience_id)->update([
            'payment_status' => $mappingStatus[$transaction] ?? 'unknown',
        ]);
        return response()->json(['message' => 'Notification handled'], 200);
    }

}
