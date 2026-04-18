<?php

namespace App\Http\Controllers;

use App\Models\Membership;
use App\Models\Package;
use App\Models\InvoiceHistory;
use App\Models\Role;
use App\Models\User;
use App\Services\PayPalService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\ValidationException;

class MembershipRegistrationController extends Controller
{
    /**
     * Tampilkan form registrasi dan daftar package.
     */
    public function index()
    {
        $packages = Package::active()->get();
        return Inertia::render('Auth/RegisterMembership/index', [
            'packages' => $packages
        ]);
    }

    /**
     * Simpan data registrasi membership sementara (status pending).
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:memberships,email|unique:users,email',
            'phone_number' => 'required|string|max:20',
            'institution' => 'required|string|max:255',
            'country' => 'required|string|max:255',
            'package_id' => 'required|exists:packages,id',
        ]);

        $package = Package::findOrFail($validated['package_id']);

        $membership = Membership::create([
            ...$validated,
            'status' => 'pending',
            // start_date & end_date will be set upon activation
            'start_date' => now()->toDateString(),
            'end_date' => now()->addDays($package->duration)->toDateString(),
        ]);

        return redirect()->route('membership.payment', $membership->public_id);
    }

    /**
     * Tampilkan halaman pemilihan metode pembayaran.
     */
    public function payment(Membership $membership)
    {
        if ($membership->status === 'active') {
            return redirect()->route('login')->with('info', 'Membership is already active.');
        }

        $membership->load('package');

        return Inertia::render('Membership/Payment', [
            'membership' => $membership,
            'package' => $membership->package
        ]);
    }

    /**
     * Proses metode pembayaran yang dipilih.
     */
    public function processPayment(Request $request, Membership $membership)
    {
        if ($membership->status === 'active') {
            return redirect()->back()->with('error', 'Membership is already active.');
        }

        $request->validate([
            'payment_method' => 'required|in:transfer_bank,payment_gateway',
            'payment_proof' => 'nullable|required_if:payment_method,transfer_bank|file|mimes:jpeg,png,jpg,pdf|max:2048'
        ]);

        if ($request->payment_method === 'transfer_bank') {
            return $this->processBankTransfer($request, $membership);
        } else {
            return $this->initiatePayPalPayment($membership);
        }
    }

    /**
     * Proses Manual Bank Transfer
     */
    private function processBankTransfer(Request $request, Membership $membership)
    {
        DB::beginTransaction();
        try {
            $proofPath = $request->file('payment_proof')->store('payment_proofs', 'public');

            $package = $membership->package;
            $currency = $membership->country === 'ID' ? 'IDR' : 'USD';

            $invoice = $membership->invoices()->create([
                'payment_method' => 'transfer_bank',
                'status' => 'pending',
                'amount' => $package->price,
                'currency' => $currency,
                'payment_proof_path' => $proofPath,
                'description' => "Membership Payment for " . $package->name,
                'payment_initiated_at' => now(),
            ]);

            DB::commit();

            // Send email confirmation
            $membership->sendPaymentPendingEmail($package->price, $currency);

            return redirect()->route('membership.payment.complete', $membership->public_id);

        } catch (\Exception $e) {
            DB::rollBack();
            if (isset($proofPath)) {
                Storage::disk('public')->delete($proofPath);
            }
            return redirect()->back()->with('error', 'Failed to process bank transfer: ' . $e->getMessage());
        }
    }

    /**
     * Inisiasi pembayaran PayPal
     */
    private function initiatePayPalPayment(Membership $membership)
    {
        $package = $membership->package;
        $currency = 'USD'; // PayPal generally uses USD

        // Cek existing pending invoice untuk PayPal
        $invoiceHistory = $membership->invoices()
            ->where('payment_method', 'payment_gateway')
            ->where('payment_gateway', 'paypal')
            ->where('status', 'pending')
            ->latest()
            ->first();

        DB::beginTransaction();
        try {
            if (!$invoiceHistory) {
                $invoiceHistory = $membership->invoices()->create([
                    'payment_method' => 'payment_gateway',
                    'payment_gateway' => 'paypal',
                    'amount' => $package->price,
                    'currency' => $currency,
                    'description' => "Membership Payment for " . $package->name,
                    'status' => 'pending',
                    'payment_initiated_at' => now(),
                ]);
            } else {
                $invoiceHistory->update([
                    'payment_initiated_at' => now(),
                ]);
            }

            $returnUrl = route('membership.paypal.success', $membership->public_id);
            $cancelUrl = route('membership.paypal.cancel', $membership->public_id);

            $paypal = app(PayPalService::class);
            $paymentDetails = $paypal->createPayment(
                $package->price,
                $currency,
                "Membership Payment for {$membership->first_name} {$membership->last_name}",
                $returnUrl,
                $cancelUrl
            );

            $invoiceHistory->update([
                'transaction_id' => $paymentDetails['payment_id'],
                'gateway_response' => $paymentDetails,
            ]);

            session([
                'paypal_payment_id' => $paymentDetails['payment_id'],
                'invoice_history_id' => $invoiceHistory->id,
            ]);

            DB::commit();

            return Inertia::location($paymentDetails['approval_url']);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('PayPal Membership Payment Error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to initiate PayPal payment: ' . $e->getMessage());
        }
    }

    /**
     * Callback Success PayPal
     */
    public function paypalSuccess(Request $request, Membership $membership)
    {
        $paymentId = $request->get('paymentId');
        $payerId = $request->get('PayerID');
        $sessionPaymentId = session('paypal_payment_id');
        $invoiceHistoryId = session('invoice_history_id');

        if (!$paymentId || !$payerId || $paymentId !== $sessionPaymentId) {
            return redirect()->route('membership.payment', $membership->public_id)
                ->with('error', 'Invalid PayPal payment data.');
        }

        try {
            $invoiceHistory = InvoiceHistory::findOrFail($invoiceHistoryId);

            $paymentDetails = app(PayPalService::class)->executePayment($paymentId, $payerId);

            $invoiceHistory->update([
                'payer_id' => $payerId,
                'execution_response' => $paymentDetails,
                'payment_completed_at' => now(),
            ]);

            if (($paymentDetails['state'] ?? null) !== 'approved') {
                $invoiceHistory->update(['status' => 'failed']);
                return redirect()->route('membership.payment', $membership->public_id)
                    ->with('error', 'PayPal payment was not approved.');
            }

            // Payment sukses
            $invoiceHistory->update(['status' => 'completed']);
            $membership->activate();
            $membership->sendSetPasswordEmail();

            session()->forget(['paypal_payment_id', 'invoice_history_id']);

            return redirect()->route('membership.payment.complete', $membership->public_id)
                ->with('success', 'Payment successful!');

        } catch (\Exception $e) {
            \Log::error('PayPal Membership Execute Error: ' . $e->getMessage());
            return redirect()->route('membership.payment', $membership->public_id)
                ->with('error', 'Failed to process payment. Please contact support.');
        }
    }

    /**
     * Callback Cancel PayPal
     */
    public function paypalCancel(Membership $membership)
    {
        $invoiceHistoryId = session('invoice_history_id');

        if ($invoiceHistoryId) {
            InvoiceHistory::whereKey($invoiceHistoryId)->update([
                'status' => 'cancelled',
                'description' => 'User cancelled the PayPal payment'
            ]);
        }

        session()->forget(['paypal_payment_id', 'invoice_history_id']);

        return redirect()->route('membership.payment', $membership->public_id)
            ->with('error', 'Payment cancelled.');
    }

    /**
     * Halaman konfirmasi pembayaran
     */
    public function paymentComplete(Membership $membership)
    {
        return Inertia::render('Membership/PaymentComplete', [
            'membership' => $membership,
            'package' => $membership->package
        ]);
    }

    /**
     * Tampilkan form set password
     */
    public function setPassword(Request $request, $token)
    {
        return Inertia::render('Membership/SetPassword', [
            'token' => $token,
            'email' => $request->email,
        ]);
    }

    /**
     * Simpan password dan buat User
     */
    public function savePassword(Request $request, $token)
    {
        $request->validate([
            'email' => 'required|email|exists:memberships,email',
            'password' => 'required|confirmed|min:8',
        ]);

        $membership = Membership::where('email', $request->email)->firstOrFail();

        // Cari token di password_reset_tokens
        $record = DB::table('password_reset_tokens')->where('email', $request->email)->first();

        // Validasi token (Note: token di-hash di DB)
        if (!$record || !Hash::check($token, $record->token)) {
            throw ValidationException::withMessages([
                'email' => ['Invalid or expired token.'],
            ]);
        }

        DB::beginTransaction();
        try {
            // Buat User
            $user = User::create([
                'name' => $membership->first_name . ' ' . $membership->last_name,
                'email' => $membership->email,
                'password' => Hash::make($request->password),
            ]);
            $user->assignRole('user');

            // Link user_id
            $membership->update(['user_id' => $user->id]);

            // Hapus token
            DB::table('password_reset_tokens')->where('email', $request->email)->delete();

            DB::commit();

            // Auto login (opsional) atau redirect ke login
            return redirect()->route('login')->with('success', 'Password set successfully. Please login.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to create user account. Please try again.');
        }
    }
}
