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

        $registrations = $query->orderBy('created_at', 'desc')
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
        if ($joivArticle->payment_status !== 'paid') {
            return redirect()->back()->with('error', 'Receipt only available for paid registrations.');
        }

        $pdf = PDF::loadView('joiv.receipt', [
            'registration' => $joivArticle,
        ]);

        return $pdf->download('JOIV_Receipt_' . $joivArticle->public_id . '.pdf');
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
}
