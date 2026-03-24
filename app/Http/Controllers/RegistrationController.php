<?php

namespace App\Http\Controllers;

use App\Models\Audience;
use App\Models\Conference;
use App\Models\InvoiceHistory;
use App\Services\PayPalService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class RegistrationController extends Controller
{
    /**
     * Show registration form for a conference
     */
    public function create(Conference $conference): Response
    {
        $isConferenceOpen = now()->between($conference->registration_start_date, $conference->registration_end_date);
        if(!$isConferenceOpen) {
            return Inertia::render('Registration/Closed', [
                'conference' => $conference->only([
                    'id', 'public_id', 'name', 'initial', 'date', 'city', 'country',
                    'registration_start_date', 'registration_end_date'
                ])
            ]);
        }
        return Inertia::render('Registration/Create', [
            'conference' => $conference->only([
                'id', 'public_id', 'name', 'initial', 'date', 'city', 'country',
                'online_fee', 'online_fee_usd', 'onsite_fee', 'onsite_fee_usd',
                'participant_fee', 'participant_fee_usd'
            ])
        ]);
    }

    /**
     * Store registration data in session (Step 1)
     */
    public function store(Request $request, Conference $conference)
    {
        // Basic validation rules
        $rules = [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'institution' => 'required|string|max:255',
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('audiences')->where(function ($query) use ($conference) {
                    return $query->where('conference_id', $conference->id)->whereNull('deleted_at');
                }),
            ],
            'phone_number' => 'required|string|max:20|regex:/^[0-9]+$/',
            'country' => 'required|string|max:2',
            'presentation_type' => 'required|in:online_author,onsite,participant_only',
        ];

        // Conditional validation for paper title and full paper
        if ($request->presentation_type !== 'participant_only') {
            $rules['paper_title'] = 'required|string|max:255';
            $rules['full_paper'] = 'required|file|mimes:pdf,doc,docx|max:51200';
        } else {
            $rules['paper_title'] = 'nullable|string|max:255';
            $rules['full_paper'] = 'nullable|file|mimes:pdf,doc,docx|max:51200';
        }

        $validatedData = $request->validate($rules, [
            'email.unique' => 'This email has already been registered for this conference.',
            'phone_number.regex' => 'Phone number must contain only numbers without spaces or other characters.',
            'full_paper.required' => 'Full paper is required for authors and presenters.',
            'full_paper.mimes' => 'The full paper must be a file of type: pdf, doc, docx.',
            'full_paper.max' => 'The full paper may not be greater than 50MB.',
            'paper_title.required' => 'Paper title is required for authors and presenters.',
        ]);

        // Handle file upload temporarily
        $fullPaperPath = null;
        if ($request->hasFile('full_paper')) {
            $fullPaperPath = $request->file('full_paper')->store('temp_papers', 'public');
        }

        // Calculate fee based on country and presentation type
        $paidFee = $this->calculateFee($conference, $validatedData['country'], $validatedData['presentation_type']);

        // Store registration data in session
        $sessionKey = 'registration_' . $conference->public_id;
        session([
            $sessionKey => [
                'conference_id' => $conference->id,
                'first_name' => $validatedData['first_name'],
                'last_name' => $validatedData['last_name'],
                'paper_title' => $validatedData['paper_title'] ?? null,
                'institution' => $validatedData['institution'],
                'email' => $validatedData['email'],
                'phone_number' => $validatedData['phone_number'],
                'country' => $validatedData['country'],
                'presentation_type' => $validatedData['presentation_type'],
                'paid_fee' => $paidFee,
                'full_paper_path' => $fullPaperPath,
            ]
        ]);

        return redirect()->route('registration.payment', [
            'conference' => $conference->public_id
        ]);
    }

    /**
     * Show payment step (Step 2)
     */
    public function payment(Conference $conference)
    {
        $sessionKey = 'registration_' . $conference->public_id;
        $registrationData = session($sessionKey);

        if (!$registrationData) {
            return redirect()->route('registration.create', $conference->public_id)
                ->with('error', 'Registration data not found. Please start registration again.');
        }

        return Inertia::render('Registration/Payment', [
            'conference' => $conference->only([
                'id', 'public_id', 'name', 'initial'
            ]),
            'registrationData' => $registrationData
        ]);
    }

    /**
     * Process payment selection and save to database
     */
    public function processPayment(Request $request, Conference $conference)
    {
        $sessionKey = 'registration_' . $conference->public_id;
        $registrationData = session($sessionKey);

        // Check if this is from the new PaymentDetails flow
        if ($request->has('audience_id')) {
            // Handle payment for existing audience record (from PaymentDetails page)
            return $this->processExistingAudiencePayment($request, $conference);
        }

        if (!$registrationData) {
            return redirect()->route('registration.create', $conference->public_id)
                ->with('error', 'Registration data not found. Please start registration again.');
        }

        $validatedData = $request->validate([
            'payment_method' => 'required|in:transfer_bank,payment_gateway',
            'payment_proof' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:10240'
        ]);

        // Additional validation: payment_proof is required for bank transfer
        if ($validatedData['payment_method'] === 'transfer_bank') {
            $request->validate([
                'payment_proof' => 'required|file|mimes:jpg,jpeg,png,pdf|max:10240'
            ], [
                'payment_proof.required' => 'Bukti pembayaran wajib diupload untuk metode transfer bank.',
                'payment_proof.file' => 'Bukti pembayaran harus berupa file.',
                'payment_proof.mimes' => 'Bukti pembayaran harus berformat: jpg, jpeg, png, atau pdf.',
                'payment_proof.max' => 'Ukuran bukti pembayaran maksimal 10MB.'
            ]);
        }

        // Handle PayPal payment - New flow: save data first, then redirect to details
        if ($validatedData['payment_method'] === 'payment_gateway') {
            // send email confirmation
            return $this->initiatePayPalPaymentNewFlow($conference, $registrationData);
        }

        // Handle Bank Transfer
        return $this->processBankTransfer($request, $conference, $registrationData, $validatedData);
    }

    /**
     * Process PayPal payment for existing audience record (from PaymentDetails page)
     */
    private function processExistingAudiencePayment(Request $request, Conference $conference)
    {
        $validatedData = $request->validate([
            'audience_id' => 'required|string',
            'payment_method' => 'required|in:payment_gateway'
        ]);

        // Find the audience record
        $audience = Audience::where('public_id', $validatedData['audience_id'])
            ->where('conference_id', $conference->id)
            ->where('payment_status', 'pending_payment')
            ->first();

        if (!$audience) {
            return redirect()->route('registration.create', $conference->public_id)
                ->with('error', 'Registration record not found or payment already processed.');
        }

        // Convert audience data to registration format for PayPal processing
        $registrationData = [
            'conference_id' => $audience->conference_id,
            'first_name' => $audience->first_name,
            'last_name' => $audience->last_name,
            'email' => $audience->email,
            'phone_number' => $audience->phone_number,
            'country' => $audience->country,
            'institution' => $audience->institution,
            'presentation_type' => $audience->presentation_type,
            'paper_title' => $audience->paper_title,
            'paid_fee' => $audience->paid_fee,
            'full_paper_path' => $audience->full_paper_path,
        ];

        // Use the existing PayPal flow but with the audience record
        return $this->initiatePayPalPaymentForExistingAudience($conference, $registrationData, $audience);
    }

    /**
     * Initiate PayPal payment
     */
    private function initiatePayPalPayment(Conference $conference, array $registrationData)
    {
        try {
            // Check if there's already a pending PayPal payment for this session
            $sessionKey = 'registration_' . $conference->public_id;
            $existingPaymentId = session('paypal_payment_id');
            $existingInvoiceHistoryId = session('invoice_history_id');
            
            // Check for existing pending payment for this registration data
            $existingInvoice = InvoiceHistory::where('conference_id', $registrationData['conference_id'])
                ->where('status', 'pending')
                ->where('payment_gateway', 'paypal')
                ->where('amount', $registrationData['paid_fee'])
                ->where('created_at', '>=', now()->subHours(2)) // Only check recent payments (2 hours)
                ->orderBy('created_at', 'desc')
                ->first();
            
            // If there's an existing pending payment, reuse it instead of creating new one
            if ($existingInvoice && $existingInvoice->transaction_id) {
                \Log::info('Reusing existing pending PayPal payment', [
                    'existing_transaction_id' => $existingInvoice->transaction_id,
                    'invoice_history_id' => $existingInvoice->id
                ]);
                
                // Update session with existing payment data
                session([
                    'paypal_payment_id' => $existingInvoice->transaction_id,
                    'invoice_history_id' => $existingInvoice->id,
                ]);
                
                // Get approval URL from stored gateway response
                $gatewayResponse = $existingInvoice->gateway_response;
                if ($gatewayResponse && isset($gatewayResponse['approval_url'])) {
                    return response('', 409)
                        ->header('X-Inertia-Location', $gatewayResponse['approval_url']);
                }
                
                // If no approval URL in stored response, fall through to create new payment
            }
            
            // Always use PayPal service (sandbox or live based on config)
            $paypalService = new PayPalService();
            
            $amount = $registrationData['paid_fee'];
            $currency = 'USD'; // PayPal mostly uses USD
            $description = "Registration for {$conference->name} - {$registrationData['first_name']} {$registrationData['last_name']}";
            
            $returnUrl = route('registration.paypal.return', $conference->public_id);
            $cancelUrl = route('registration.paypal.cancel', $conference->public_id);
            
            // Debug: Log the URLs being used
            \Log::info('PayPal Payment Creation', [
                'return_url' => $returnUrl,
                'cancel_url' => $cancelUrl,
                'amount' => $amount,
                'currency' => $currency
            ]);
            
            $paymentResult = $paypalService->createPayment($amount, $currency, $description, $returnUrl, $cancelUrl);
            
            // Debug: Log the PayPal response
            \Log::info('PayPal Payment Result', $paymentResult);
            
            // Create or update invoice history record for tracking
            if ($existingInvoice) {
                // Update existing invoice with new payment data
                $existingInvoice->update([
                    'transaction_id' => $paymentResult['payment_id'],
                    'gateway_response' => $paymentResult,
                    'payment_initiated_at' => now(),
                ]);
                $invoiceHistory = $existingInvoice;
                
                \Log::info('Updated existing invoice history record', [
                    'invoice_history_id' => $invoiceHistory->id,
                    'transaction_id' => $paymentResult['payment_id']
                ]);
            } else {
                // Create new invoice history record
                $invoiceHistory = InvoiceHistory::create([
                    'audience_id' => null, // Will be set when audience is created after payment
                    'conference_id' => $registrationData['conference_id'],
                    'payment_gateway' => 'paypal',
                    'payment_method' => 'payment_gateway',
                    'transaction_id' => $paymentResult['payment_id'],
                    'amount' => $amount,
                    'currency' => $currency,
                    'status' => 'pending',
                    'description' => $description,
                    'return_url' => $returnUrl,
                    'cancel_url' => $cancelUrl,
                    'gateway_response' => $paymentResult,
                    'payment_initiated_at' => now(),
                ]);
                
                \Log::info('Created new invoice history record', [
                    'invoice_history_id' => $invoiceHistory->id,
                    'transaction_id' => $paymentResult['payment_id']
                ]);
            }
            
            // Store payment ID and invoice history ID in session for later verification
            session([
                'paypal_payment_id' => $paymentResult['payment_id'],
                'invoice_history_id' => $invoiceHistory->id,
            ]);
            
            // Debug: Check if approval_url exists
            if (!isset($paymentResult['approval_url']) || empty($paymentResult['approval_url'])) {
                throw new \Exception('PayPal approval URL not found in response');
            }
            
            // Debug: Log the URL we're trying to redirect to
            \Log::info('Redirecting to PayPal URL', ['url' => $paymentResult['approval_url']]);
            
            $approvalUrl = $paymentResult['approval_url'];
            
            // For Inertia, use location header for external redirects
            return response('', 409)
                ->header('X-Inertia-Location', $approvalUrl);
            
        } catch (\Exception $e) {
            // Log the full error
            \Log::error('PayPal Payment Error', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            // For now, if PayPal fails, redirect back with clear instructions
            return redirect()->back()->with('error', 
                'PayPal payment tidak dapat diinisialisasi. Kemungkinan masalah: ' . 
                '1) Credentials PayPal tidak valid, ' .
                '2) Perlu setup PayPal Sandbox account yang benar. ' .
                'Silakan gunakan Transfer Bank atau hubungi administrator. ' .
                'Error: ' . $e->getMessage()
            );
        }
    }

    /**
     * Process bank transfer payment
     */
    private function processBankTransfer(Request $request, Conference $conference, array $registrationData, array $validatedData)
    {
        // Handle payment proof upload
        $paymentProofPath = null;
        if ($request->hasFile('payment_proof')) {
            $paymentProofPath = $request->file('payment_proof')->store('payment_proofs', 'public');
        }

        // Create audience record with pending status
        $audience = $this->createAudienceRecord(
            $conference, 
            $registrationData, 
            $validatedData['payment_method'], 
            $paymentProofPath, 
            'pending_payment'
        );

        // send email confirmation

        return redirect()->route('registration.success', [
            'conference' => $conference->public_id,
            'audience' => $audience->public_id
        ]);
    }

    /**
     * Create audience record in database
     */
    private function createAudienceRecord(Conference $conference, array $registrationData, string $paymentMethod, ?string $paymentProofPath = null, string $paymentStatus = 'pending')
    {
        // Move temp paper to permanent location
        $finalPaperPath = null;
        if ($registrationData['full_paper_path']) {
            $tempPath = storage_path('app/public/' . $registrationData['full_paper_path']);
            if (file_exists($tempPath)) {
                $filename = basename($registrationData['full_paper_path']);
                $finalPaperPath = 'audience_full_papers/' . $filename;
                $finalPath = storage_path('app/public/' . $finalPaperPath);
                
                // Create directory if it doesn't exist
                $dir = dirname($finalPath);
                if (!is_dir($dir)) {
                    mkdir($dir, 0755, true);
                }
                
                rename($tempPath, $finalPath);
            }
        }

        // Create audience record in database
        $audience = Audience::create([
            'public_id' => uniqid(),
            'conference_id' => $registrationData['conference_id'],
            'first_name' => $registrationData['first_name'],
            'last_name' => $registrationData['last_name'],
            'paper_title' => $registrationData['paper_title'],
            'institution' => $registrationData['institution'],
            'email' => $registrationData['email'],
            'phone_number' => $registrationData['phone_number'],
            'country' => $registrationData['country'],
            'presentation_type' => $registrationData['presentation_type'],
            'paid_fee' => $registrationData['paid_fee'],
            'payment_method' => $paymentMethod,
            'payment_proof_path' => $paymentProofPath,
            'full_paper_path' => $finalPaperPath,
            'payment_status' => $paymentStatus
        ]);

        // Clear session data
        $sessionKey = 'registration_' . $conference->public_id;
        session()->forget($sessionKey);

        return $audience;
    }

    /**
     * Handle PayPal payment return (success)
     */
    public function paypalReturn(Request $request, Conference $conference)
    {
        $paymentId = $request->get('paymentId');
        $payerId = $request->get('PayerID');
        $sessionPaymentId = session('paypal_payment_id');
        $invoiceHistoryId = session('invoice_history_id');

        \Log::info('PayPal Return Debug', [
            'request_payment_id' => $paymentId,
            'session_payment_id' => $sessionPaymentId,
            'invoice_history_id' => $invoiceHistoryId,
        ]);

        if (!$paymentId || !$payerId || $paymentId !== $sessionPaymentId) {
            if ($invoiceHistoryId) {
                InvoiceHistory::whereKey($invoiceHistoryId)->update([
                    'status' => 'failed',
                    'execution_response' => ['error' => 'Invalid PayPal payment data']
                ]);
            }

            return redirect()->route('registration.create', $conference->public_id)
                ->with('error', 'Invalid PayPal payment data.');
        }

        try {
            $invoiceHistory = InvoiceHistory::findOrFail($invoiceHistoryId);

            $paymentDetails = app(PayPalService::class)
                ->executePayment($paymentId, $payerId);

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

                return redirect()->route('registration.create', $conference->public_id)
                    ->with('error', 'PayPal payment was not approved.');
            }

            // 🔗 ambil model terkait dari polymorphic relation
            $reference = $invoiceHistory->reference;

            if (!$reference) {
                throw new \Exception('Payment reference not found.');
            }

            // ✅ Update business entity berdasarkan tipe reference
            if ($reference instanceof Audience) {
                $reference->update(['payment_status' => 'paid']);
                $reference->sendPaymentConfirmationEmail();
                $audience = $reference;

            } elseif ($reference instanceof JoivRegistration) {
                $reference->update(['payment_status' => 'paid']);
                $audience = null;

            } else {
                throw new \Exception('Unsupported payment reference type: ' . get_class($reference));
            }

            $invoiceHistory->update(['status' => 'completed']);

            session()->forget([
                'paypal_payment_id',
                'invoice_history_id'
            ]);

            \Log::info('PayPal Payment Completed', [
                'payment_id' => $paymentId,
                'invoice_id' => $invoiceHistory->id,
                'reference_type' => get_class($reference),
                'reference_id' => $reference->id
            ]);

            if ($audience) {
                return redirect()->route('registration.success', [
                    'conference' => $conference->public_id,
                    'audience' => $audience->public_id
                ]);
            }

            return redirect()->route('joiv.registration.details', $reference->public_id);

        } catch (\Throwable $e) {
            if ($invoiceHistoryId) {
                InvoiceHistory::whereKey($invoiceHistoryId)->update([
                    'status' => 'failed',
                    'execution_response' => ['error' => $e->getMessage()]
                ]);
            }

            \Log::error('PayPal Execution Error', [
                'payment_id' => $paymentId,
                'error' => $e->getMessage()
            ]);

            $errorMessage = match (true) {
                str_contains($e->getMessage(), 'credentials') => 'Payment service configuration error.',
                str_contains($e->getMessage(), 'access token') => 'Unable to connect to payment service.',
                str_contains($e->getMessage(), 'network'),
                str_contains($e->getMessage(), 'timeout') => 'Network error occurred.',
                str_contains($e->getMessage(), 'PAYMENT_ALREADY_DONE') => 'This payment has already been processed.',
                str_contains($e->getMessage(), 'INVALID_PAYMENT_ID') => 'Invalid payment reference.',
                default => 'Failed to process PayPal payment.',
            };

            return redirect()->route('registration.create', $conference->public_id)
                ->with('error', $errorMessage);
        }
    }

    /**
     * Handle PayPal payment cancel
     */
    public function paypalCancel(Conference $conference)
    {
        $invoiceHistoryId = session('invoice_history_id');
        
        // Update invoice history as cancelled
        if ($invoiceHistoryId) {
            $invoiceHistory = InvoiceHistory::find($invoiceHistoryId);
            if ($invoiceHistory) {
                $invoiceHistory->update([
                    'status' => 'cancelled',
                    'execution_response' => ['error' => 'Payment cancelled by user']
                ]);
            }
        }
        
        // Clear PayPal session data
        session()->forget(['paypal_payment_id', 'invoice_history_id']);

        return redirect()->route('registration.payment', $conference->public_id)
            ->with('error', 'PayPal payment was cancelled. Please try again or choose a different payment method.');
    }

    /**
     * Show success page
     */
    public function success(Conference $conference, Audience $audience)
    {
        // Verify that this audience belongs to the conference
        if ($audience->conference_id !== $conference->id) {
            abort(404);
        }

        return Inertia::render('Registration/Success', [
            'audience' => $audience->only([
                'public_id', 'first_name', 'last_name', 'email',
                'payment_method', 'payment_status', 'paid_fee', 'country'
            ]),
            'conference' => $audience->conference->only([
                'name', 'initial', 'date'
            ])
        ]);
    }

    /**
     * Calculate registration fee
     */
    private function calculateFee(Conference $conference, string $country, string $presentationType): float
    {
        $isIndonesia = $country === 'ID';
        
        switch ($presentationType) {
            case 'online_author':
                return $isIndonesia ? $conference->online_fee : $conference->online_fee_usd;
            case 'onsite':
                return $isIndonesia ? $conference->onsite_fee : $conference->onsite_fee_usd;
            case 'participant_only':
                return $isIndonesia ? $conference->participant_fee : $conference->participant_fee_usd;
            default:
                return 0;
        }
    }

    /**
     * New PayPal flow: Save audience data first, then redirect to payment details
     */
    private function initiatePayPalPaymentNewFlow(Conference $conference, array $registrationData)
    {
        try {
            // Create the audience record with pending payment status
            $audience = $this->createAudienceRecord($conference, $registrationData, 'payment_gateway', null, 'pending_payment');
            
            // Clear session data since we've saved to database
            $sessionKey = 'registration_' . $conference->public_id;
            session()->forget($sessionKey);
            
            // send email confirmation
            $audience->sendEmail();
            // Redirect to payment details page
            return redirect()->route('registration.details', [
                'conference' => $conference->public_id,
                'audience' => $audience->public_id
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Failed to save audience data for PayPal payment', [
                'error' => $e->getMessage(),
                'registration_data' => $registrationData,
                'trace' => $e->getTraceAsString()
            ]);

            // Provide more specific error messages
            $errorMessage = 'Failed to process registration. Please try again.';
            
            if (strpos($e->getMessage(), 'Duplicate') !== false || strpos($e->getMessage(), 'unique') !== false) {
                $errorMessage = 'This email is already registered for this conference. Please check your registration status.';
            } elseif (strpos($e->getMessage(), 'validation') !== false) {
                $errorMessage = 'Please check your registration information and try again.';
            } elseif (strpos($e->getMessage(), 'database') !== false || strpos($e->getMessage(), 'connection') !== false) {
                $errorMessage = 'Database connection error. Please try again later.';
            }

            return redirect()->back()->withErrors([
                'payment_method' => $errorMessage
            ]);
        }
    }

    /**
     * Show payment details page (order summary and payment processing)
     */
    public function paymentDetails(Conference $conference, Audience $audience): Response
    {
        // Verify that this audience belongs to the conference
        if ($audience->conference_id !== $conference->id) {
            abort(404);
        }

        return Inertia::render('Registration/PaymentDetails', [
            'conference' => $conference->only([
                'id', 'public_id', 'name', 'initial', 'date', 'city', 'country'
            ]),
            'audience' => $audience->only([
                'id', 'public_id', 'first_name', 'last_name', 'email', 'institution',
                'paper_title', 'presentation_type', 'paid_fee', 'payment_status',
                'payment_method', 'country'
            ])
        ]);
    }

    /**
     * Initiate PayPal payment for existing audience record
     */
    private function initiatePayPalPaymentForExistingAudience(Conference $conference, array $registrationData, Audience $audience)
    {
        try {
            // 🔎 cari invoice pending milik audience (scoped by polymorphic relation)
        $invoiceHistory = $audience->invoices()
            ->where('status', 'pending')
            ->where('payment_gateway', 'paypal')
            ->latest()
            ->first();

        if ($invoiceHistory && $invoiceHistory->transaction_id) {
            $paymentId = $invoiceHistory->transaction_id;

            \Log::info('Using existing PayPal payment', [
                'payment_id' => $paymentId,
                'audience_id' => $audience->id,
                'invoice_id' => $invoiceHistory->id
            ]);

            $approvalUrl = data_get($invoiceHistory->gateway_response, 'approval_url');

        } else {
            // 🧾 build payment request
            $amount = $registrationData['paid_fee'];
            $currency = 'USD';
            $description = "Registration for {$conference->name} - {$registrationData['first_name']} {$registrationData['last_name']}";
            $returnUrl = route('registration.paypal.return', $conference->public_id);
            $cancelUrl = route('registration.paypal.cancel', $conference->public_id);

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

            // 🔄 update jika ada invoice lama tanpa transaction_id, else create baru
            $invoiceHistory = $invoiceHistory
                ? tap($invoiceHistory)->update($invoicePayload)
                : $audience->invoices()->create($invoicePayload);

            $approvalUrl = $paymentResult['approval_url'];

            \Log::info('Created PayPal payment for audience', [
                'payment_id' => $paymentId,
                'audience_id' => $audience->id,
                'invoice_id' => $invoiceHistory->id
            ]);
        }

        // 💾 simpan session untuk return handler
        session([
            'paypal_payment_id' => $paymentId,
            'invoice_history_id' => $invoiceHistory->id,
            'audience_id' => $audience->id
        ]);

        // 🌐 external redirect (Inertia compatible)
        return response('', 409)
            ->header('X-Inertia-Location', $approvalUrl);

        } catch (\Exception $e) {
            \Log::error('PayPal payment creation failed for existing audience', [
                'error' => $e->getMessage(),
                'audience_id' => $audience->id,
                'conference_id' => $conference->id,
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
}
