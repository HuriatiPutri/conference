<?php

namespace App\Http\Controllers;

use App\Models\JoivRegistration;
use App\Models\JoivRegistrationFee;
use App\Models\InvoiceHistory;
use App\Services\PayPalService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class JoivRegistrationController extends Controller
{
    /**
     * Show JOIV registration form
     */
    public function index(): Response
    {   
        $registrationFeeIDR = JoivRegistrationFee::getCurrentFeeAmount('IDR');
        $registrationFeeUSD = JoivRegistrationFee::getCurrentFeeAmount('USD');
        return Inertia::render('Joiv/Registration/Index', [
            'registrationFeeIDR' => $registrationFeeIDR,
            'registrationFeeUSD' => $registrationFeeUSD,
        ]);
    }

    /**
     * Store JOIV registration data
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email_address' => 'required|email|max:255',
            'phone_number' => 'required|string|max:20|regex:/^[0-9]+$/',
            'institution' => 'required|string|max:255',
            'country' => 'required|string|max:2',
            'paper_id' => 'nullable|string|max:255',
            'paper_title' => 'required|string|max:255',
            'full_paper' => 'required|file|mimes:pdf,doc,docx|max:51200',
        ], [
            'email_address.unique' => 'This email has already been registered.',
            'phone_number.regex' => 'Phone number must contain only numbers without spaces or other characters.',
            'full_paper.required' => 'Full paper is required.',
            'full_paper.mimes' => 'The full paper must be a file of type: pdf, doc, docx.',
            'full_paper.max' => 'The full paper may not be greater than 50MB.',
        ]);

        // Handle file upload
        $fullPaperPath = null;
        if ($request->hasFile('full_paper')) {
            $file = $request->file('full_paper');
            $filename = time() . '_' . Str::slug($validatedData['paper_title']) . '.' . $file->getClientOriginalExtension();
            $fullPaperPath = $file->storeAs('joiv_papers', $filename, 'public');
        }

        // Generate unique public ID
        $publicId = 'JOIV-' . strtoupper(Str::random(10));

        // Determine currency based on country (Indonesia = IDR, others = USD)
        $currency = $validatedData['country'] === 'ID' ? 'IDR' : 'USD';
        
        // Get current registration fee from database based on currency
        $paidFee = JoivRegistrationFee::getCurrentFeeAmount($currency);

        // Create registration record
        $registration = JoivRegistration::create([
            'first_name' => $validatedData['first_name'],
            'last_name' => $validatedData['last_name'],
            'email_address' => $validatedData['email_address'],
            'phone_number' => $validatedData['phone_number'],
            'institution' => $validatedData['institution'],
            'country' => $validatedData['country'],
            'paper_id' => $validatedData['paper_id'],
            'paper_title' => $validatedData['paper_title'],
            'full_paper_path' => $fullPaperPath,
            'paid_fee' => $paidFee,
            'currency' => $currency,
            'public_id' => $publicId,
            'payment_status' => 'pending_payment',
        ]);

        // Store registration ID in session for payment process
        $sessionKey = 'registration_' . $registration->id;
        session([
            $sessionKey => [
                'first_name' => $validatedData['first_name'],
                'last_name' => $validatedData['last_name'],
                'paper_id' => $validatedData['paper_id'] ?? null,
                'paper_title' => $validatedData['paper_title'] ?? null,
                'institution' => $validatedData['institution'],
                'email' => $validatedData['email_address'],
                'phone_number' => $validatedData['phone_number'],
                'country' => $validatedData['country'],
                'paid_fee' => $paidFee,
                'full_paper_path' => $fullPaperPath,
            ]
        ]);

        return redirect()->route('joiv.payment', ['registration' => $registration->public_id]);
    }

    /**
     * Show registration details
     */

    public function details(JoivRegistration $registration): Response
    {
        return Inertia::render('Joiv/Registration/Details', [
            'registration' => $registration,
        ]);
    }

    /**
     * Show payment page
     */
    public function payment(JoivRegistration $registration): Response
    {
        if ($registration->payment_status !== 'pending_payment') {
            return Inertia::render('Joiv/Payment/Complete', [
                'registration' => $registration,
            ]);
        }

        return Inertia::render('Joiv/Payment/Index', [
            'registration' => $registration,
        ]);
    }

    /**
     * Process payment selection
     */
    public function processPayment(Request $request, JoivRegistration $registration)
    {
        if ($registration->payment_status !== 'pending_payment') {
            return redirect()->route('joiv.payment', ['registration' => $registration->public_id])
                ->with('error', 'Payment already processed.');
        }

        //if payment method already set and is payment_gateway, redirect to paypal
        if ($registration->payment_method === 'payment_gateway') {
            return $this->initiatePayPalPayment($registration);
        }

        $validatedData = $request->validate([
            'payment_method' => 'required|in:transfer_bank,payment_gateway',
            'payment_proof' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:10240'
        ]);

        // Validate payment proof for bank transfer
        if ($validatedData['payment_method'] === 'transfer_bank') {
            $request->validate([
                'payment_proof' => 'required|file|mimes:jpg,jpeg,png,pdf|max:10240'
            ], [
                'payment_proof.required' => 'Bukti pembayaran wajib diupload untuk metode transfer bank.',
            ]);
        }

        // Handle PayPal payment
        if ($validatedData['payment_method'] === 'payment_gateway') {
            //update registration to payment_gateway and status pending
            $registration->update([
                'payment_method' => 'payment_gateway',
                'payment_status' => 'pending_payment',
            ]);
            //redirect to registration detail page with message
            return redirect()->route('joiv.registration.details', ['registration' => $registration->public_id])
                ->with('success', 'Please proceed to PayPal payment gateway from your registration details page.');
        }

        // Handle Bank Transfer
        return $this->processBankTransfer($request, $registration, $validatedData);
    }

    /**
     * Process bank transfer payment
     */
    private function processBankTransfer(Request $request, JoivRegistration $registration, array $validatedData)
    {
        // Handle payment proof upload
        $paymentProofPath = null;
        if ($request->hasFile('payment_proof')) {
            $file = $request->file('payment_proof');
            $filename = time() . '_proof_' . $registration->public_id . '.' . $file->getClientOriginalExtension();
            $paymentProofPath = $file->storeAs('payment_proofs', $filename, 'public');
        }

        // Update registration
        $registration->update([
            'payment_method' => 'transfer_bank',
            'payment_proof_path' => $paymentProofPath,
            'payment_status' => 'pending_payment', // Admin needs to verify
        ]);

        return redirect()->route('joiv.payment.complete', ['registration' => $registration->public_id])
            ->with('success', 'Registration submitted. Please wait for payment verification.');
    }

    /**
     * Initiate PayPal payment
     */
    private function initiatePayPalPayment(JoivRegistration $registration){
      try{
        //cari invoice pending via polymorphic relation
        $invoiceHistory = $registration->invoices()
          ->where('status', 'pending')
          ->where('payment_gateway', 'paypal')
          ->latest()
          ->first();

        if($invoiceHistory && $invoiceHistory->transaction_id){
          $paymentId = $invoiceHistory->transaction_id;

          \Log::info('Using existing PayPal payment', [
                'payment_id' => $paymentId,
                'registration_id' => $registration->id,
                'invoice_id' => $invoiceHistory->id
            ]);
          
          $approvalUrl = data_get($invoiceHistory->gateway_response, 'approval_url');
        }else{
            $amount = $registration->paid_fee;
            $currency = 'USD';
            $description = 'JOIV Article Registration - ' . $registration->paper_title;
            $returnUrl = route('joiv.paypal.success', $registration->public_id);
            $cancelUrl = route('joiv.paypal.cancel', $registration->public_id);

            $paymentResult = app(PayPalService::class)
                ->createPayment($amount, $currency, $description, $returnUrl, $cancelUrl);

            $paymentId = $paymentResult['payment_id'];

            $invoicePayload = [
                'transaction_id' => $paymentId,
                'payment_gateway' => 'paypal',
                'payment_method' => 'payment_gateway',
                'amount' => $amount,
                'currency' => $currency,
                'status' => 'pending',
                'description' => $description,
                'return_url' => $returnUrl,
                'cancel_url' => $cancelUrl,
                'gateway_response' => $paymentResult,
                'payment_initiated_at' => now(),
            ];

            // update jika sudah ada, create jika belum
            $invoiceHistory = $invoiceHistory
                ? tap($invoiceHistory)->update($invoicePayload)
                : $registration->invoices()->create($invoicePayload);

            $approvalUrl = $paymentResult['approval_url'];
        }
        // simpan session
        session([
            'paypal_payment_id' => $paymentId,
            'invoice_history_id' => $invoiceHistory->id,
            'registration_id' => $registration->id
        ]);

        // redirect external (Inertia)
        return response('', 409)
            ->header('X-Inertia-Location', $approvalUrl);
      }catch(\Throwable $e){
        \Log::error('PayPal payment creation failed for existing registration', [
                'error' => $e->getMessage(),
                'registration_id' => $registration->id,
                'trace' => $e->getTraceAsString()
            ]);
            // Provide more specific error messages based on the error type
            $errorMessage = 'Payment processing failed. Please try again.';
            
            if (strpos($e->getMessage(), 'credentials') !== false) {
                $errorMessage = 'Payment service configuration error. Please contact support.';
            } elseif (strpos($e->getMessage(), 'access token') !== false) {
                $errorMessage = 'Unable to connect to payment service. Please try again later.';
            } elseif (strpos($e->getMessage(), 'network') !== false || strpos($e->getMessage(), 'timeout') !== false) {
                $errorMessage = 'Network error occurred. Please check your connection and try again.';
            }

            return redirect()->back()->withErrors([
                'payment_method' => $errorMessage
            ]);
      }
    }

    /**
     * Handle PayPal success callback
     */
    public function paypalSuccess(Request $request, JoivRegistration $registration)
    {
        $token = $request->query('token');
        $paymentId = $request->get('paymentId');
        $payerId = $request->get('PayerID');

        $sessionPaymentId = session('paypal_payment_id');
        $invoiceHistoryId = session('invoice_history_id');

        \Log::info('PayPal Success Callback', [
            'payment_id' => $paymentId,
            'payer_id' => $payerId,
            'invoice_history_id' => $invoiceHistoryId
        ]);

        if (!$token) {
            return redirect()->route('joiv.payment', $registration->public_id)
                ->with('error', 'Payment token not found.');
        }

        if (!$paymentId || !$payerId || $paymentId !== $sessionPaymentId) {
            InvoiceHistory::whereKey($invoiceHistoryId)->update([
                'status' => 'failed',
                'execution_response' => ['error' => 'Invalid PayPal payment data']
            ]);

            return redirect()->route('joiv.registration.details', $registration->public_id)
                ->with('error', 'Invalid PayPal payment data.');
        }

        try {
            $invoiceHistory = InvoiceHistory::findOrFail($invoiceHistoryId);

            $paymentDetails = app(PayPalService::class)
                ->executePayment($paymentId, $payerId);

            \Log::info('PayPal Execution Response', [
                'state' => $paymentDetails['state'] ?? null
            ]);

            $invoiceHistory->update([
                'payer_id' => $payerId,
                'execution_response' => $paymentDetails,
                'payment_completed_at' => now(),
            ]);

            if (($paymentDetails['state'] ?? null) !== 'approved') {
                $invoiceHistory->update([
                    'status' => 'failed',
                    'execution_response' => array_merge(
                        $paymentDetails,
                        ['error' => 'Payment not approved']
                    )
                ]);

                return redirect()->route('joiv.payment', $registration->public_id)
                    ->with('error', 'Payment was not completed.');
            }

            // 🔗 pastikan reference benar
            $reference = $invoiceHistory->reference;

            if (!$reference || !$reference->is($registration)) {
                throw new \Exception('Payment reference mismatch.');
            }

            // ✅ update business entity
            $registration->update([
                'payment_status' => 'paid',
            ]);

            // ✅ update invoice
            $invoiceHistory->update([
                'status' => 'completed'
            ]);

            session()->forget([
                'paypal_payment_id',
                'invoice_history_id'
            ]);

            return redirect()->route('joiv.payment.complete', $registration->public_id)
                ->with('success', 'Payment successful!');

        } catch (\Throwable $e) {
            InvoiceHistory::whereKey($invoiceHistoryId)->update([
                'status' => 'failed',
                'execution_response' => ['error' => $e->getMessage()]
            ]);

            \Log::error('PayPal Success Error', [
                'payment_id' => $paymentId,
                'error' => $e->getMessage()
            ]);

            return redirect()->route('joiv.payment', $registration->public_id)
                ->with('error', 'Payment verification failed.');
        }
    }

    /**
     * Handle PayPal cancel callback
     */
    public function paypalCancel(JoivRegistration $registration)
    {
        session()->forget(['paypal_payment_id', 'joiv_registration_id']);

        return redirect()->route('joiv.payment', ['registration' => $registration->public_id])
            ->with('error', 'Payment was cancelled.');
    }

    /**
     * Show payment complete page
     */
    public function paymentComplete(JoivRegistration $registration): Response
    {
        return Inertia::render('Joiv/Payment/Complete', [
            'registration' => $registration,
        ]);
    }
}
