<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\JoivRegistration;
use App\Models\JoivRegistrationFee;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\JoivRegistrationExport;
use Inertia\Inertia;
use Inertia\Response;

class JoivArticleController extends Controller
{
    /**
     * Display a listing of JOIV registrations
     */
    public function index(): Response
    {
        $currentFee = JoivRegistrationFee::getCurrentFee();
        $filters = Request::only('country', 'institution', 'payment_status', 'search');
        $perPage = Request::input('per_page', 15);
        
        $query = JoivRegistration::query();

        // Apply filters
        if (!empty($filters['country'])) {
            $query->where('country', $filters['country']);
        }

        if (!empty($filters['institution'])) {
            $query->where('institution', 'ILIKE', "%{$filters['institution']}%");
        }

        if (!empty($filters['payment_status'])) {
            $query->where('payment_status', $filters['payment_status']);
        }

        // Apply search filter
        if (!empty($filters['search'])) {
            $searchTerm = $filters['search'];
            $query->where(function($q) use ($searchTerm) {
                $q->where('first_name', 'LIKE', "%{$searchTerm}%")
                  ->orWhere('last_name', 'LIKE', "%{$searchTerm}%")
                  ->orWhere('email_address', 'LIKE', "%{$searchTerm}%")
                  ->orWhere('phone_number', 'LIKE', "%{$searchTerm}%")
                  ->orWhere('institution', 'LIKE', "%{$searchTerm}%")
                  ->orWhere('paper_id', 'LIKE', "%{$searchTerm}%")
                  ->orWhere('paper_title', 'LIKE', "%{$searchTerm}%");
            });
        }

        $registrations = $query->with('loaVolume')
            ->orderBy('created_at', 'desc')
            ->paginate($perPage)
            ->appends(Request::all());

        // Get summary counts
        $summaryQuery = JoivRegistration::query();
        
        if (!empty($filters['country'])) {
            $summaryQuery->where('country', $filters['country']);
        }

        if (!empty($filters['institution'])) {
            $summaryQuery->where('institution', 'ILIKE', "%{$filters['institution']}%");
        }

        $summary = [
            'paid' => (clone $summaryQuery)->where('payment_status', 'paid')->count(),
            'pending' => (clone $summaryQuery)->where('payment_status', 'pending_payment')->count(),
            'cancelled' => (clone $summaryQuery)->where('payment_status', 'cancelled')->count(),
            'refunded' => (clone $summaryQuery)->where('payment_status', 'refunded')->count(),
        ];

        // Get unique countries and institutions for filters
        $countries = JoivRegistration::select('country')
            ->distinct()
            ->orderBy('country')
            ->pluck('country');

        return Inertia::render('Admin/JoivArticles/Index', [
            'filters' => $filters,
            'registrations' => $registrations,
            'summary' => $summary,
            'countries' => $countries,
            'currentFee' => $currentFee,
        ]);
    }

    /**
     * Display the specified registration
     */
    public function show(JoivRegistration $joivArticle): Response
    {
        $joivArticle->load(['creator', 'updater', 'invoiceHistories']);

        return Inertia::render('Admin/JoivArticles/Show', [
            'registration' => $joivArticle,
        ]);
    }

    /**
     * Show form to assign volume and authors before download LOA.
     */
    public function assignVolumeForm(JoivRegistration $joivArticle): Response
    {
        // Check if participant is eligible
        if ($joivArticle->payment_status !== 'paid') {
            abort(404, 'Participant not eligible for Letter of Approval');
        }

        $joivArticle->load(['loaVolume']);

        // Get all available LoA Volumes
        $loaVolumes = \App\Models\LoaVolume::select('id', 'volume')
            ->orderBy('volume')
            ->get();

        return Inertia::render('Admin/JoivArticles/AssignVolume', [
            'registration' => $joivArticle->toArray(),
            'loaVolumes' => $loaVolumes,
        ]);
    }

    /**
     * Update LoA authors and volume information.
     */
    public function updateLoaInfo(JoivRegistration $joivArticle, \Illuminate\Http\Request $request): RedirectResponse
    {
        // Validate input
        $request->validate([
            'authors' => 'required|string|max:500',
            'loa_volume_id' => 'required|exists:loa_volume,id',
        ]);

        // Check if participant is eligible
        if ($joivArticle->payment_status !== 'paid') {
            abort(404, 'Participant not eligible for Letter of Approval');
        }

        // Update LoA information
        $joivArticle->update([
            'loa_authors' => $request->authors,
            'loa_volume_id' => $request->loa_volume_id,
            'loa_approved_at' => now(),
            'updated_by' => Auth::id(),
        ]);

        return redirect()->back()->with('success', 'LoA information updated successfully. You can now download the letter.');
    }

    /**
     * Update payment status
     */
    public function updatePaymentStatus(JoivRegistration $joivArticle, Request $request): RedirectResponse
    {
        $validated = $request::validate([
            'payment_status' => 'required|in:pending_payment,paid,cancelled,refunded',
        ]);

        $joivArticle->update([
            'payment_status' => $validated['payment_status'],
            'updated_by' => Auth::id(),
        ]);

        return redirect()->back()->with('success', 'Payment status updated successfully.');
    }

    /**
     * Download full paper
     */
    public function downloadPaper(JoivRegistration $joivArticle)
    {
        if (!$joivArticle->full_paper_path || !Storage::disk('public')->exists($joivArticle->full_paper_path)) {
            return redirect()->back()->with('error', 'Full paper not found.');
        }

        return Storage::disk('public')->download($joivArticle->full_paper_path);
    }

    /**
     * Download payment proof
     */
    public function downloadPaymentProof(JoivRegistration $joivArticle)
    {
        if (!$joivArticle->payment_proof_path || !Storage::disk('public')->exists($joivArticle->payment_proof_path)) {
            return redirect()->back()->with('error', 'Payment proof not found.');
        }

        return Storage::disk('public')->download($joivArticle->payment_proof_path);
    }

    /**
     * Export registrations to Excel
     */
    public function export()
    {
        $filters = Request::only('country', 'institution', 'payment_status', 'search');
        
        $filename = 'joiv_registrations_export_' . now()->format('Y-m-d_H-i-s');
        
        if (!empty($filters['country'])) {
            $filename .= '_' . $filters['country'];
        }
        
        $filename .= '.xlsx';
        
        return Excel::download(new JoivRegistrationExport($filters), $filename);
    }

    /**
     * Download receipt PDF
     */
    public function downloadReceipt(JoivRegistration $joivArticle)
    {
        // Check if payment is completed
        if ($joivArticle->payment_status !== 'paid') {
            return redirect()->back()->withErrors(['error' => 'Receipt can only be downloaded for paid registrations']);
        }

        // Prepare data for receipt (same format as audience receipt)
        $data = [
            'name' => $joivArticle->first_name . ' ' . $joivArticle->last_name,
            'address' => $joivArticle->institution . ', ' . $joivArticle->country,
            'paper_title' => $joivArticle->paper_title ?? 'N/A',
            'conference' => 'JOIV',
            'conference_name' => 'JOIV: International Journal on Informatics Visualization',
            'conference_cover' => null,
            'date' => now()->format('Y'),
            'amount' => $joivArticle->country === 'ID' ? 'Rp' . number_format($joivArticle->paid_fee, 0, ',', '.') : '$' . number_format($joivArticle->paid_fee, 2),
            'payment_method' => $joivArticle->payment_method === 'transfer_bank' ? 'Bank Transfer' : 'Payment Gateway',
            'payment_date' => $joivArticle->updated_at->format('d M Y H:i'),
            'invoice_id' => 'Ref. No.' . strtoupper($joivArticle->public_id) . '/PAID/JOIV/' . now()->format('Y'),
            'signature' => storage_path('app/public/images/joiv-signature.png'),
        ];

        try {
            // Generate PDF using the same template as audience receipt
            $pdf = Pdf::loadView('receipt.joiv', compact('data'))
                      ->setPaper('A4', 'portrait');

            return $pdf->stream("receipt-{$data['name']}.pdf");
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => 'Failed to generate receipt: ' . $e->getMessage()]);
        }
    }

    /**
     * Download Letter of Approval for JOIV article
     */
    public function downloadLoa(JoivRegistration $joivArticle)
    {
        // Check if participant is eligible
        if ($joivArticle->payment_status !== 'paid') {
            abort(404, 'Participant not eligible for Letter of Approval');
        }

        // Check if LoA info is complete
        if (!$joivArticle->loa_authors || !$joivArticle->loa_volume_id) {
            return redirect()->back()->withErrors(['error' => 'Please fill in the authors and volume information first.']);
        }

        try {
            $joivArticle->load(['loaVolume']);
            
            $data = [
                'participant_name' => $joivArticle->first_name . ' ' . $joivArticle->last_name,
                'institution' => $joivArticle->institution ?? 'Unknown Institution',
                'paper_title' => $joivArticle->paper_title ?? 'Untitled Paper',
                'authors' => $joivArticle->loa_authors,
                'joiv_volume' => $joivArticle->loaVolume->volume ?? 'Volume Not Set',
                'conference_name' => 'Journal on Informatics Visualization',
                'conference_initial' => 'JOIV',
                'conference_date' => now(),
                'conference_city' => 'Online',
                'conference_country' => 'International',
                'presentation_type' => 'journal article',
                'registration_number' => $joivArticle->public_id ?? 'REG-' . $joivArticle->id,
                'number_of_letter' => 'No: SOTVI/LoA/' . date('Y').'/' . ($joivArticle->public_id),
                'issue_date' => $joivArticle->loa_approved_at ? \Carbon\Carbon::parse($joivArticle->loa_approved_at)->format('d F Y') : now()->format('d F Y'),
                'signature_path' => storage_path('app/public/images/loa_signature.png'),
                'joiv_logo_path' => storage_path('app/public/images/joiv_logo.png'),
                'sotvi_logo_path' => storage_path('app/public/images/sotvi_logo.png'),
                'scopus_analitic_path' => storage_path('app/public/images/scopus.png'),
            ];

            $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('letters-of-approval.template-clean', compact('data'))
                      ->setPaper('A4', 'portrait');

            $filename = "JOIV-Acceptance-Letter-{$joivArticle->first_name}-{$joivArticle->last_name}.pdf";

            return $pdf->stream($filename);
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => 'Failed to generate PDF: ' . $e->getMessage()]);
        }
    }

    /**
     * Delete registration
     */
    public function destroy(JoivRegistration $joivArticle): RedirectResponse
    {
        $joivArticle->delete();

        return redirect()->back()->with('success', 'Registration deleted successfully.');
    }

    /**
     * Restore deleted registration
     */
    public function restore(JoivRegistration $joivArticle): RedirectResponse
    {
        $joivArticle->restore();

        return redirect()->back()->with('success', 'Registration restored successfully.');
    }

    /**
     * Show fee settings page
     */
    public function feeSettings(): Response
    {
        $currentFee = JoivRegistrationFee::getCurrentFee();
        $feeHistory = JoivRegistrationFee::with('creator')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return Inertia::render('Admin/JoivArticles/FeeSettings', [
            'currentFee' => $currentFee,
            'feeHistory' => $feeHistory,
        ]);
    }

    /**
     * Update registration fee
     */
    public function updateFee(): RedirectResponse
    {
        $validated = Request::validate([
            'usd_amount' => 'required|numeric|min:0',
            'idr_amount' => 'required|numeric|min:0',
            'notes' => 'nullable|string|max:500',
        ]);

        JoivRegistrationFee::create([
            'usd_amount' => $validated['usd_amount'],
            'idr_amount' => $validated['idr_amount'],
            'notes' => $validated['notes'] ?? null,
            'created_by' => Auth::id(),
        ]);

        return redirect()->back()->with('success', 'Registration fee updated successfully.');
    }

    public function deleteFee(JoivRegistrationFee $fee): RedirectResponse
    {
        $fee->delete();
        return redirect()->back()->with('success', 'Registration fee record deleted successfully.');
    }
}
