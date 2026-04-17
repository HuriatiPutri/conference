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
                'payment_proof.file' => 'Bukti pembayaran harus berupa file.',
                'payment_proof.mimes' => 'Bukti pembayaran harus berformat: jpg, jpeg, png, atau pdf.',
                'payment_proof.max' => 'Ukuran bukti pembayaran maksimal 10MB.',
            ]);
        }

        // Handle PayPal payment - save method first, then initiate PayPal
        if ($validatedData['payment_method'] === 'payment_gateway') {
            // Update payment method on registration record
            $registration->update([
                'payment_method' => 'payment_gateway',
            ]);

            // Directly initiate PayPal payment (same as RegistrationController new flow)
            return $this->initiatePayPalPayment($registration);
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
    private function initiatePayPalPayment(JoivRegistration $registration)
    {
        try {
            // Check if there is already a pending PayPal invoice for this registration
            $existingInvoice = InvoiceHistory::where('joiv_registration_id', $registration->id)
                ->where('status', 'pending')
                ->where('payment_gateway', 'paypal')
                ->first();

            $paymentResult = null;
            $invoiceHistory = null;

            if ($existingInvoice && $existingInvoice->transaction_id) {
                // Reuse existing pending payment
                $paymentId = $existingInvoice->transaction_id;
                $invoiceHistory = $existingInvoice;

                \Log::info('Reusing existing pending PayPal payment', [
                    'payment_id' => $paymentId,
                    'registration_id' => $registration->id,
                    'invoice_id' => $existingInvoice->id,
                ]);
            } else {
                // Create new PayPal payment
                $payPalService = new PayPalService();

                $amount = $registration->paid_fee;
                $currency = 'USD'; // PayPal uses USD
                $description = 'JOIV Article Registration - ' . $registration->paper_title;
                $returnUrl = route('joiv.paypal.success', $registration->public_id);
                $cancelUrl = route('joiv.paypal.cancel', $registration->public_id);

                \Log::info('PayPal Payment Creation', [
                    'return_url' => $returnUrl,
                    'cancel_url' => $cancelUrl,
                    'amount' => $amount,
                    'currency' => $currency,
                ]);

                $paymentResult = $payPalService->createPayment($amount, $currency, $description, $returnUrl, $cancelUrl);
                $paymentId = $paymentResult['payment_id'];

                \Log::info('PayPal Payment Result', $paymentResult);

                if ($existingInvoice) {
                    // Update existing invoice that had no transaction_id yet
                    $existingInvoice->update([
                        'transaction_id' => $paymentId,
                        'amount' => $amount,
                        'currency' => $currency,
                        'gateway_response' => $paymentResult,
                        'payment_initiated_at' => now(),
                    ]);
                    $invoiceHistory = $existingInvoice;
                } else {
                    // Create new invoice history record
                    $invoiceHistory = InvoiceHistory::create([
                        'joiv_registration_id' => $registration->id,
                        'payment_gateway' => 'paypal',
                        'payment_method' => 'payment_gateway',
                        'transaction_id' => $paymentId,
                        'amount' => $amount,
                        'currency' => $currency,
                        'status' => 'pending',
                        'description' => $description,
                        'return_url' => $returnUrl,
                        'cancel_url' => $cancelUrl,
                        'gateway_response' => $paymentResult,
                        'payment_initiated_at' => now(),
                    ]);
                }

                \Log::info('Created/Updated invoice history for PayPal', [
                    'invoice_id' => $invoiceHistory->id,
                    'transaction_id' => $paymentId,
                    'registration_id' => $registration->id,
                ]);
            }

            // Store payment details in session for return handling
            session([
                'paypal_payment_id' => $paymentId,
                'invoice_history_id' => $invoiceHistory->id,
                'joiv_registration_id' => $registration->id,
            ]);

            // Get approval URL
            if ($existingInvoice && $existingInvoice->gateway_response && isset($existingInvoice->gateway_response['approval_url'])) {
                $approvalUrl = $existingInvoice->gateway_response['approval_url'];
            } else {
                if (!isset($paymentResult['approval_url']) || empty($paymentResult['approval_url'])) {
                    throw new \Exception('PayPal approval URL not found in response');
                }
                $approvalUrl = $paymentResult['approval_url'];
            }

            \Log::info('Redirecting to PayPal URL', ['url' => $approvalUrl]);

            // For Inertia, use location header for external redirects
            return response('', 409)
                ->header('X-Inertia-Location', $approvalUrl);

        } catch (\Exception $e) {
            \Log::error('PayPal payment creation failed for JOIV registration', [
                'error' => $e->getMessage(),
                'registration_id' => $registration->id,
                'trace' => $e->getTraceAsString(),
            ]);

            // Provide specific error messages
            $errorMessage = 'Payment processing failed. Please try again.';

            if (strpos($e->getMessage(), 'credentials') !== false) {
                $errorMessage = 'Payment service configuration error. Please contact support.';
            } elseif (strpos($e->getMessage(), 'access token') !== false) {
                $errorMessage = 'Unable to connect to payment service. Please try again later.';
            } elseif (strpos($e->getMessage(), 'network') !== false || strpos($e->getMessage(), 'timeout') !== false) {
                $errorMessage = 'Network error occurred. Please check your connection and try again.';
            }

            return redirect()->back()->withErrors([
                'payment_method' => $errorMessage,
            ]);
        }
    }

    /**
     * Handle PayPal success callback
     */
    public function paypalSuccess(Request $request, JoivRegistration $registration)
    {
        $paymentId = $request->get('paymentId');
        $payerId = $request->get('PayerID');
        $token = $request->query('token');
        $sessionPaymentId = session('paypal_payment_id');
        $invoiceHistoryId = session('invoice_history_id');

        \Log::info('PayPal Return Debug Info', [
            'request_payment_id' => $paymentId,
            'request_payer_id' => $payerId,
            'token' => $token,
            'session_payment_id' => $sessionPaymentId,
            'session_invoice_history_id' => $invoiceHistoryId,
            'all_request_params' => $request->all(),
        ]);

        if (!$paymentId || !$payerId || $paymentId !== $sessionPaymentId) {
            \Log::error('PayPal Return Validation Failed', [
                'has_payment_id' => !empty($paymentId),
                'has_payer_id' => !empty($payerId),
                'payment_ids_match' => $paymentId === $sessionPaymentId,
                'request_payment_id' => $paymentId,
                'session_payment_id' => $sessionPaymentId,
            ]);

            // Mark invoice as failed if it exists
            if ($invoiceHistoryId) {
                $invoiceHistory = InvoiceHistory::find($invoiceHistoryId);
                if ($invoiceHistory) {
                    $invoiceHistory->update([
                        'status' => 'failed',
                        'execution_response' => ['error' => 'Invalid PayPal payment data'],
                    ]);
                }
            }

            return redirect()->route('joiv.registration.details', ['registration' => $registration->public_id])
                ->with('error', 'Invalid PayPal payment data. Please try again.');
        }

        try {
            $paypalService = new PayPalService();
            $paymentDetails = $paypalService->executePayment($paymentId, $payerId);

            \Log::info('PayPal Payment Execution Response', [
                'payment_details' => $paymentDetails,
                'payment_state' => $paymentDetails['state'] ?? 'unknown',
            ]);

            // Update invoice history with execution response
            $invoiceHistory = null;
            if ($invoiceHistoryId) {
                $invoiceHistory = InvoiceHistory::find($invoiceHistoryId);
                if ($invoiceHistory) {
                    $invoiceHistory->update([
                        'payer_id' => $payerId,
                        'execution_response' => $paymentDetails,
                        'payment_completed_at' => now(),
                    ]);
                }
            }

            if ($paymentDetails['state'] === 'approved') {
                // Update registration to paid
                $registration->update([
                    'payment_status' => 'paid',
                ]);

                // Mark invoice as completed
                if ($invoiceHistory) {
                    $invoiceHistory->update([
                        'status' => 'completed',
                        'paid_at' => now(),
                    ]);
                }

                // Clear session
                session()->forget(['paypal_payment_id', 'invoice_history_id', 'joiv_registration_id']);

                \Log::info('JOIV PayPal Payment Completed Successfully', [
                    'payment_id' => $paymentId,
                    'payer_id' => $payerId,
                    'registration_id' => $registration->id,
                    'invoice_history_id' => $invoiceHistory ? $invoiceHistory->id : null,
                ]);

                return redirect()->route('joiv.payment.complete', ['registration' => $registration->public_id])
                    ->with('success', 'Payment successful!');
            }

            // Payment not approved
            if ($invoiceHistory) {
                $invoiceHistory->update([
                    'status' => 'failed',
                    'execution_response' => array_merge($paymentDetails, ['error' => 'Payment not approved']),
                ]);
            }

            \Log::error('PayPal payment was not approved. Registration: ' . $registration->public_id);

            return redirect()->route('joiv.payment', ['registration' => $registration->public_id])
                ->with('error', 'PayPal payment was not approved. Please try again.');

        } catch (\Exception $e) {
            // Mark invoice as failed
            if ($invoiceHistoryId) {
                $invoiceHistoryRecord = InvoiceHistory::find($invoiceHistoryId);
                if ($invoiceHistoryRecord) {
                    $invoiceHistoryRecord->update([
                        'status' => 'failed',
                        'execution_response' => ['error' => $e->getMessage()],
                    ]);
                }
            }

            \Log::error('PayPal capture error', [
                'payment_id' => $paymentId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            // Specific error messages
            $errorMessage = 'Failed to process PayPal payment. Please try again.';

            if (strpos($e->getMessage(), 'credentials') !== false) {
                $errorMessage = 'Payment service configuration error. Please contact support.';
            } elseif (strpos($e->getMessage(), 'access token') !== false) {
                $errorMessage = 'Unable to connect to payment service. Please try again later.';
            } elseif (strpos($e->getMessage(), 'network') !== false || strpos($e->getMessage(), 'timeout') !== false) {
                $errorMessage = 'Network error occurred. Please check your connection and try again.';
            } elseif (strpos($e->getMessage(), 'PAYMENT_ALREADY_DONE') !== false) {
                $errorMessage = 'This payment has already been processed.';
            } elseif (strpos($e->getMessage(), 'INVALID_PAYMENT_ID') !== false) {
                $errorMessage = 'Invalid payment reference. Please start a new payment.';
            }

            return redirect()->route('joiv.payment', ['registration' => $registration->public_id])
                ->with('error', $errorMessage);
        }
    }

    /**
     * Handle PayPal cancel callback
     */
    public function paypalCancel(JoivRegistration $registration)
    {
        $invoiceHistoryId = session('invoice_history_id');

        // Mark invoice as cancelled
        if ($invoiceHistoryId) {
            $invoiceHistory = InvoiceHistory::find($invoiceHistoryId);
            if ($invoiceHistory) {
                $invoiceHistory->update([
                    'status' => 'cancelled',
                    'execution_response' => ['error' => 'Payment cancelled by user'],
                ]);
            }
        }

        session()->forget(['paypal_payment_id', 'invoice_history_id', 'joiv_registration_id']);

        return redirect()->route('joiv.payment', ['registration' => $registration->public_id])
            ->with('error', 'PayPal payment was cancelled. Please try again or choose a different payment method.');
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
