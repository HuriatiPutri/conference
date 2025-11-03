<?php

namespace App\Http\Controllers;

use App\Models\Conference;
use App\Models\Audience;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Barryvdh\DomPDF\Facade\Pdf;

class LettersOfApprovalController extends Controller
{
    /**
     * Display a listing of letters of approval.
     */
    public function index(Request $request): Response
    {
        $perPage = $request->input('per_page', 15); // Default 15, bisa diubah via parameter
        
        $query = Audience::with(['conference'])
            ->where('payment_status', 'paid')
            ->whereNotNull('paper_title');

        // Filter by conference if specified
        if ($request->filled('conference_id')) {
            $query->where('conference_id', $request->conference_id);
        }

        // Filter by search term
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('paper_title', 'like', "%{$search}%")
                  ->orWhere('institution', 'like', "%{$search}%");
            });
        }

        $audiences = $query->latest()->paginate($perPage)->appends($request->all());
        
        // Get conferences for filter dropdown
        $conferences = Conference::select('id', 'name', 'initial')
            ->whereNull('deleted_at')
            ->orderBy('name')
            ->get();

        // Summary statistics
        $summary = [
            'total_participants' => Audience::where('payment_status', 'paid')->count(),
            'total_papers' => Audience::where('payment_status', 'paid')->whereNotNull('paper_title')->count(),
            'total_conferences' => Conference::whereNull('deleted_at')->count(),
        ];

        return Inertia::render('Admin/LettersOfApproval/Index', [
            'audiences' => $audiences,
            'conferences' => $conferences,
            'filters' => $request->only(['conference_id', 'search']),
            'summary' => $summary,
        ]);
    }

    /**
     * Show the details of a specific participant for LoA.
     */
    public function show(Audience $audience): Response
    {
        $audience->load(['conference', 'key_notes', 'parallel_sessions']);

        // Check if participant is eligible for LoA
        if ($audience->payment_status !== 'paid') {
            abort(404, 'Participant not eligible for Letter of Approval');
        }

        return Inertia::render('Admin/LettersOfApproval/Show', [
            'audience' => $audience,
        ]);
    }

    /**
     * Show form to input authors and JOIV details before download.
     */
    public function downloadForm(Audience $audience): Response
    {
        // Check if participant is eligible
        if ($audience->payment_status !== 'paid') {
            abort(404, 'Participant not eligible for Letter of Approval');
        }

        $audience->load('conference');

        return Inertia::render('Admin/LettersOfApproval/DownloadForm', [
            'audience' => $audience->load('conference')->toArray(),
        ]);
    }

    /**
     * Update LoA authors and JOIV volume information.
     */
    public function updateLoaInfo(Request $request, Audience $audience)
    {
        // Validate input
        $request->validate([
            'authors' => 'required|string|max:500',
            'joiv_volume' => 'required|string|max:100',
        ]);

        // Check if participant is eligible
        if ($audience->payment_status !== 'paid') {
            abort(404, 'Participant not eligible for Letter of Approval');
        }

        // Update LoA information
        $audience->update([
            'loa_authors' => $request->authors,
            'loa_joiv_volume' => $request->joiv_volume,
            'loa_status' => 'approved', // Auto approve when info is complete
            'loa_approved_at' => now(),
        ]);

        return redirect()->back()->with('success', 'LoA information updated successfully. You can now download the letter.');
    }

    /**
     * Generate and download Letter of Approval PDF.
     */
    public function download(Request $request, Audience $audience)
    {
        // Check if participant is eligible
        if ($audience->payment_status !== 'paid') {
            abort(404, 'Participant not eligible for Letter of Approval');
        }

        // Check if LoA info is complete
        if (!$audience->loa_authors || !$audience->loa_joiv_volume) {
            return redirect()->route('letters-of-approval.download-form', $audience->id)
                           ->withErrors(['error' => 'Please fill in the authors and JOIV volume information first.']);
        }

        try {
            $audience->load('conference');
            
            $data = [
                'participant_name' => $audience->first_name . ' ' . $audience->last_name,
                'institution' => $audience->institution ?? 'Unknown Institution',
                'paper_title' => $audience->paper_title ?? 'Untitled Paper',
                'authors' => $audience->loa_authors,
                'joiv_volume' => $audience->loa_joiv_volume,
                'conference_name' => $audience->conference->name ?? 'Conference',
                'conference_initial' => $audience->conference->initial ?? 'CONF',
                'conference_date' => $audience->conference->date ?? now(),
                'conference_city' => $audience->conference->city ?? 'City',
                'conference_country' => $audience->conference->country ?? 'Country',
                'presentation_type' => $audience->presentation_type ?? 'presentation',
                'registration_number' => $audience->public_id ?? 'REG-' . $audience->id,
                'number_of_letter' => 'No: SOTVI/LoA/' . date('Y').'/' . ($audience->public_id),
                'issue_date' => $audience->loa_approved_at ? \Carbon\Carbon::parse($audience->loa_approved_at)->format('d F Y') : now()->format('d F Y'),
                'signature_path' => storage_path('app/public/images/loa_signature.png'),
                'joiv_logo_path' => storage_path('app/public/images/joiv_logo.png'),
                'sotvi_logo_path' => storage_path('app/public/images/sotvi_logo.png'),
                'scopus_analitic_path' => storage_path('app/public/images/scopus.png'),
            ];

            $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('letters-of-approval.template-clean', compact('data'))
                      ->setPaper('A4', 'portrait');

            $filename = "JOIV-Acceptance-Letter-{$audience->first_name}-{$audience->last_name}.pdf";

            return $pdf->stream($filename);
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => 'Failed to generate PDF: ' . $e->getMessage()]);
        }
    }

    /**
     * Bulk download multiple Letters of Approval as ZIP.
     */
    public function bulkDownload(Request $request)
    {
        $request->validate([
            'audience_ids' => 'required|array',
            'audience_ids.*' => 'exists:audiences,id'
        ]);

        $audiences = Audience::with('conference')
            ->whereIn('id', $request->audience_ids)
            ->where('payment_status', 'paid')
            ->get();

        if ($audiences->isEmpty()) {
            return redirect()->back()->with('error', 'No eligible participants found.');
        }

        // Create temporary directory for PDFs
        $tempDir = storage_path('app/temp/loa_bulk_' . uniqid());
        \File::makeDirectory($tempDir, 0755, true);

        $zipPath = storage_path('app/temp/Letters_of_Approval_' . date('Y-m-d_H-i-s') . '.zip');
        $zip = new \ZipArchive();
        
        if ($zip->open($zipPath, \ZipArchive::CREATE) !== TRUE) {
            return redirect()->back()->with('error', 'Could not create ZIP file.');
        }

        foreach ($audiences as $audience) {
            // Prepare data for each PDF
            $data = [
                'participant_name' => $audience->first_name . ' ' . $audience->last_name,
                'institution' => $audience->institution,
                'paper_title' => $audience->paper_title,
                'authors' => $audience->first_name . ' ' . $audience->last_name, // Default to participant name
                'joiv_volume' => 'Vol.10 No.6 November 2026', // Default volume
                'conference_name' => $audience->conference->name,
                'conference_initial' => $audience->conference->initial,
                'conference_date' => $audience->conference->date,
                'conference_city' => $audience->conference->city,
                'conference_country' => $audience->conference->country,
                'presentation_type' => $audience->presentation_type,
                'registration_number' => $audience->public_id,
                'issue_date' => now()->format('d F Y'),
                'signature_path' => storage_path('app/public/images/signature.png'),
            ];

            // Generate PDF
            $pdf = Pdf::loadView('letters-of-approval.template-clean', compact('data'))
                      ->setPaper('A4', 'portrait');

            $filename = "LoA-{$audience->conference->initial}-{$audience->first_name}-{$audience->last_name}.pdf";
            $pdfPath = $tempDir . '/' . $filename;
            
            file_put_contents($pdfPath, $pdf->output());
            $zip->addFile($pdfPath, $filename);
        }

        $zip->close();

        // Clean up temporary files
        \File::deleteDirectory($tempDir);

        return response()->download($zipPath)->deleteFileAfterSend(true);
    }

    /**
     * Update the approval status of a participant.
     */
    public function updateStatus(Request $request, Audience $audience)
    {
        $request->validate([
            'loa_status' => 'required|in:pending,approved,rejected',
            'loa_notes' => 'nullable|string|max:1000'
        ]);

        $audience->update([
            'loa_status' => $request->loa_status,
            'loa_notes' => $request->loa_notes,
            'loa_approved_at' => $request->loa_status === 'approved' ? now() : null,
        ]);

        return redirect()->back()->with('success', 'LoA status updated successfully.');
    }
}