<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Srmklive\PayPal\Services\PayPal as PayPalClient;
use App\Models\InvoiceHistory;
use App\Models\Audience;
use App\Helpers\CurrencyHelper;

class PaypalController extends Controller
{
    public function createTransaction(Request $request)
{
    // Validasi input jika diperlukan
    $request->validate([
        'audience_id' => 'required|exists:audiences,id',
    ]);

    // Cek apakah invoice sudah ada, jika sudah kembalikan error
    $invoiceHistory = InvoiceHistory::where('audience_id', $request->input('audience_id'))->first();

    if ($invoiceHistory) {
        // Invoice sudah ada, kembalikan error via JSON
        return response()->json(['redirect_url' => $invoiceHistory->redirect_url], 200);
    }

    // Ambil data audience berdasarkan ID
    $audience = \App\Models\Audience::findOrFail($request->input('audience_id'));

    // Buat order ID unik untuk transaksi
    $order_id = 'ORDER-' . uniqid();
    // Konversi jumlah dari IDR ke USD, karena PayPal menggunakan USD
    $amount = CurrencyHelper::idrToUsd($audience->paid_fee, 16375);
    $provider = new PayPalClient;
    $provider->setApiCredentials(config('paypal'));
    $provider->getAccessToken();

    $response = $provider->createOrder([
        "intent" => "CAPTURE",
        "purchase_units" => [
            [
                "reference_id" => $order_id,
                "amount" => [
                    "currency_code" => "USD",
                    "value" => $amount
                ]
            ],
        ],
        "application_context" => [
            "return_url" => route('paypal.success'),
            "cancel_url" => route('paypal.cancel'),
        ]
    ]);

    
    if (isset($response['id']) && $response['status'] === 'CREATED') {
        // Simpan order ID dan informasi invoice lainnya ke database
        $redirect_url = null;
        
        foreach ($response['links'] as $link) {
            if ($link['rel'] === 'approve') {
                $redirect_url = $link['href'];
                break;
            }
        }

        InvoiceHistory::create([
            'id' => $order_id,
            'audience_id' => $audience->id,
            'amount' => $audience->paid_fee,
            'snap_token' => $response['id'], // Tidak digunakan untuk PayPal
            'redirect_url' => $redirect_url,
            'payment_method' => 'paypal',
            'expired_at' => now()->addHours(24)->timestamp, // Simpan expired dalam integer (unix timestamp)
            'status' => 'pending',
        ]);
        if ($redirect_url) {
            return response()->json(['redirect_url' => $redirect_url]);
        } else {
            return response()->json(['error' => 'Approve URL tidak ditemukan.'], 500);
        }
    }

    return response()->json(['error' => 'Terjadi kesalahan saat membuat order.'], 500);
}

    public function captureTransaction(Request $request)
    {
        $provider = new PayPalClient;
        $provider->setApiCredentials(config('paypal'));
        $provider->getAccessToken();

        $response = $provider->capturePaymentOrder($request->token);
        $invoiceHistory = InvoiceHistory::where('snap_token', $request->token)->first();

        
        if (isset($response['status']) && $response['status'] === 'COMPLETED') {
            // Simpan ke database jika perlu

            InvoiceHistory::where('snap_token', $request->token)->update([
                'status' => 'paid',
            ]);

            if($invoiceHistory){
                Audience::where('id', $invoiceHistory->audience_id)->update([
                    'payment_status' => 'paid' ?? 'unknown',
                ]);
            }

            return redirect()->route('registration.show', $invoiceHistory->audience_id)->with('success', 'Pembayaran berhasil!');
        }

        return redirect()->route('registration.show', $invoiceHistory->audience_id)->with('error', 'Pembayaran gagal.');
    }

    public function cancelTransaction(Request $request)
    {
        // Logika untuk menangani pembatalan transaksi
        
        $invoiceHistory = InvoiceHistory::where('snap_token', $request['token'])->first();

        return redirect()->route('registration.show', $invoiceHistory->audience_id)->with('error', 'Transaksi dibatalkan oleh pengguna.');
    }
}
