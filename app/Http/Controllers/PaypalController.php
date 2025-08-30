<?php

namespace App\Http\Controllers;

use App\Events\ActivityLogEvent;
use App\Helpers\CurrencyHelper;
use App\Models\Audience;
use App\Models\InvoiceHistory;
use Illuminate\Http\Request;
use Srmklive\PayPal\Services\PayPal as PayPalClient;

class PaypalController extends Controller
{
    public function handle(Request $request)
    {
        $event = $request->all();

        if ($event['event_type'] === 'PAYMENT.CAPTURE.COMPLETED') {
            $captureId = $event['resource']['id'];

            // Cari payment di DB
            $audience = Audience::where('paypal_capture_id', $captureId)->first();

            if ($audience) {
                $audience->update(['payment_status' => 'paid']);
            }
        }

        event(new ActivityLogEvent('INFO', 'PayPal Webhook Received', $event));

        return response()->json(['status' => 'ok', 'message' => 'Webhook received', 'data' => $event], 200);
    }

    public function createTransaction(Request $request)
    {
        try {
            // Validasi input jika diperlukan
            $request->validate([
                'audience_id' => 'required|exists:audiences,id',
            ]);

            // Cek apakah invoice sudah ada, jika sudah kembalikan error
            $invoiceHistory = InvoiceHistory::where('audience_id', $request->input('audience_id'))->first();

            if ($invoiceHistory) {
                // Invoice sudah ada, kembalikan error via JSON
                return response()->json(['success' => true, 'redirect_url' => $invoiceHistory->redirect_url]);
            }

            // Ambil data audience berdasarkan ID
            $audience = Audience::findOrFail($request->input('audience_id'));

            // Buat order ID unik untuk transaksi
            $order_id = 'ORDER-'.uniqid();
            // Konversi jumlah dari IDR ke USD, karena PayPal menggunakan USD
            $amount = $audience->country === 'ID' ? CurrencyHelper::idrToUsd($audience->paid_fee, 16375) : $audience->paid_fee;
            $provider = new PayPalClient();
            $provider->setApiCredentials(config('paypal'));
            $provider->getAccessToken();

            $response = $provider->createOrder([
                'intent' => 'CAPTURE',
                'purchase_units' => [
                    [
                        'reference_id' => $order_id,
                        'amount' => [
                            'currency_code' => 'USD',
                            'value' => $amount,
                        ],
                    ],
                ],
                'application_context' => [
                    'return_url' => route('paypal.success'),
                    'cancel_url' => route('paypal.cancel'),
                ],
            ]);

            if (isset($response['id'])) {
                event(new ActivityLogEvent(
                    'INFO',
                    'PayPal Create Order Response',
                    $response
                ));
            }
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
                    return response()->json(['success' => true, 'redirect_url' => $redirect_url]);
                } else {
                    return response()->json(['success' => false, 'error' => 'Approve URL tidak ditemukan.'], 500);
                }
            }

            event(new ActivityLogEvent('ERROR', 'PayPal Create Order Failed', $response));

            return response()->json(['
        success' => false,
                'error' => 'An error occurred during the payment process.',
                'details' => $response,
            ], 500);
        } catch (\Exception $e) {
            event(new ActivityLogEvent('ERROR', 'PayPal Create Order Exception', $e->getMessage()));

            return response()->json(['success' => false, 'error' => 'Exception: '.$e->getMessage()], 500);
        }
    }

    public function captureTransaction(Request $request)
    {
        $provider = new PayPalClient();
        $provider->setApiCredentials(config('paypal'));
        $provider->getAccessToken();

        $response = $provider->capturePaymentOrder($request->token);
        $invoiceHistory = InvoiceHistory::where('snap_token', $request->token)->first();

        if (isset($response['status'])) {
            event(new ActivityLogListener($response['status'], 'PayPal Capture Order Response', $response));
        }
        if (isset($response['status']) && $response['status'] === 'COMPLETED') {
            // Simpan ke database jika perlu

            InvoiceHistory::where('snap_token', $request->token)->update([
                'status' => 'paid',
            ]);

            if ($invoiceHistory) {
                Audience::where('id', $invoiceHistory->audience_id)->update([
                    'payment_status' => 'paid' ?? 'unknown',
                ]);

                // Kirim email konfirmasi pembayaran
                $invoiceHistory->sendEmail();
            }

            return redirect()->route('registration.show', $invoiceHistory->audience->public_id)->with('success', 'Pembayaran berhasil!');
        }

        event(new ActivityLogListener('ERROR', 'PayPal Capture Order Failed', $response));

        return redirect()->route('registration.show', $invoiceHistory->audience->public_id)->with('error', 'Pembayaran gagal.');
    }

    public function cancelTransaction(Request $request)
    {
        // Cari histori invoice berdasarkan snap_token
        $invoiceHistory = InvoiceHistory::where('snap_token', $request->input('token'))->first();

        // Jika tidak ditemukan, redirect dengan pesan error
        if (!$invoiceHistory) {
            return redirect()->route('home')->with('error', 'Data transaksi tidak ditemukan.');
        }

        // Jika audience tidak ditemukan (jaga-jaga)
        if (!$invoiceHistory->audience) {
            return redirect()->route('home')->with('error', 'Data peserta tidak ditemukan.');
        }

        // Jika semua data ditemukan, lanjut redirect ke halaman registrasi dengan pesan error
        return redirect()
            ->route('registration.show', $invoiceHistory->audience->public_id)
            ->with('error', 'Transaksi dibatalkan oleh pengguna.');
    }

    public function checkPaypalOrder($orderId)
    {
        $provider = new PayPalClient();
        $provider->setApiCredentials(config('paypal'));
        $provider->getAccessToken();

        $order = $provider->showCapturedPaymentDetails($orderId);

        // Contoh: $order['status'] bisa "COMPLETED", "CREATED", "APPROVED"
        if ($order['status'] === 'COMPLETED') {
            InvoiceHistory::where('capture_id', $orderId)->update([
                'status' => 'paid',
            ]);

            $invoiceHistory = InvoiceHistory::where('capture_id', $orderId)->first();

            if ($invoiceHistory) {
                Audience::where('id', $invoiceHistory->audience_id)->update([
                    'payment_status' => 'paid' ?? 'unknown',
                ]);

                // Kirim email konfirmasi pembayaran
                $invoiceHistory->sendEmail();
            }
            event(new ActivityLogEvent('SUCCESS', 'PayPal Order Completed', $order));
        }

        return response()->json($order);
    }
}
