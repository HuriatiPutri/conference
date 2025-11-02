<?php

namespace App\Http\Controllers;

use App\Http\Requests\AudienceStoreRequest;
use App\Http\Requests\AudienceUpdateRequest;
use App\Http\Resources\AudienceCollection;
use App\Http\Resources\AudienceResource;
use App\Models\Audience;
use App\Models\Conference;
use App\Exports\AudienceExport;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use Inertia\Inertia;
use Inertia\Response;

class AudiencesController extends Controller
{
    public function index(): Response
    {
        $filters = Request::only('conference_id', 'payment_method', 'payment_status');
        $perPage = Request::input('per_page', 15); // Default 50, bisa diubah via parameter
        
        // Build query with filters
        $query = Audience::query()
            ->with(['conference', 'key_notes', 'parallel_sessions'])
            ->whereHas('conference');

        // Apply filters
        if (!empty($filters['conference_id'])) {
            $query->where('conference_id', $filters['conference_id']);
        }

        if (!empty($filters['payment_method'])) {
            $query->where('payment_method', $filters['payment_method']);
        }

        if (!empty($filters['payment_status'])) {
            $query->where('payment_status', $filters['payment_status']);
        }

        // Get filtered audiences for pagination
        $audiences = $query->orderBy('id', 'desc')->paginate($perPage)->appends(Request::all());

        // Get summary counts with same filters
        $summaryQuery = Audience::query()->whereHas('conference');
        
        if (!empty($filters['conference_id'])) {
            $summaryQuery->where('conference_id', $filters['conference_id']);
        }

        if (!empty($filters['payment_method'])) {
            $summaryQuery->where('payment_method', $filters['payment_method']);
        }

        $summary = [
            'paid' => (clone $summaryQuery)->where('payment_status', 'paid')->count(),
            'pending' => (clone $summaryQuery)->where('payment_status', 'pending_payment')->count(),
            'cancelled' => (clone $summaryQuery)->where('payment_status', 'cancelled')->count(),
            'refunded' => (clone $summaryQuery)->where('payment_status', 'refunded')->count(),
        ];

        // Get all conferences for filter dropdown
        $conferences = Conference::whereNull('deleted_at')
            ->orderBy('name')
            ->get(['id', 'name']);

        return Inertia::render('Admin/Audiences/Index', [
            'filters' => $filters,
            'audiences' => new AudienceCollection($audiences),
            'summary' => $summary,
            'conferences' => $conferences,
        ]);
    }

    public function export()
    {
        $filters = Request::only('conference_id', 'payment_method', 'payment_status');
        
        // Generate filename with current date and filters
        $filename = 'audiences_export_' . now()->format('Y-m-d_H-i-s');
        
        if (!empty($filters['conference_id'])) {
            $conference = Conference::find($filters['conference_id']);
            if ($conference) {
                $filename .= '_' . str_replace([' ', '/', '\\'], '_', $conference->name);
            }
        }
        
        $filename .= '.xlsx';
        
        return Excel::download(new AudienceExport($filters), $filename);
    }

    public function show(Audience $audience): Response
    {
        return Inertia::render('Admin/Audiences/Show', [
            'audience' => new AudienceResource($audience->loadMissing(['conference', 'key_notes','parallel_sessions'])),
        ]);
    }

    public function edit(Audience $audience): Response
    {
        return Inertia::render('Admin/Audiences/Edit', [
            'audience' => new AudienceResource($audience),
        ]);
    }

    public function update(Audience $audience, AudienceUpdateRequest $request): RedirectResponse
    {
        $audience->update(
            $request->validated()
        );

        return Redirect::back()->with('success', 'Audience updated.');
    }

    public function destroy(Audience $audience): RedirectResponse
    {
        $audience->delete();

        return Redirect::back()->with('success', 'Audience deleted.');
    }

    public function restore(Audience $audience): RedirectResponse
    {
        $audience->restore();

        return Redirect::back()->with('success', 'Audience restored.');
    }

    public function download(Audience $audience)
    {
        // Load conference dengan template sertifikat
        $audience->load('conference');
        $conference = $audience->conference;
        
        // Validasi apakah ada template sertifikat
        if (!$conference || !$conference->certificate_template_path || !$conference->certificate_template_position) {
            return redirect()->back()->withErrors(['error' => 'Template sertifikat belum diatur untuk konferensi ini.']);
        }
        
        // Parse position data
        try {
            $positionData = json_decode($conference->certificate_template_position, true);
            if (!isset($positionData['positions'])) {
                throw new \Exception('Format data posisi tidak valid');
            }
            $positions = json_decode($positionData['positions'], true);
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => 'Data posisi template sertifikat tidak valid: ' . $e->getMessage()]);
        }
        
        // Path template image
        $templatePath = storage_path('app/public/' . $conference->certificate_template_path);
        
        if (!file_exists($templatePath)) {
            return redirect()->back()->withErrors(['error' => 'File template sertifikat tidak ditemukan di: ' . $templatePath]);
        }
        
        // Encode image to base64 untuk digunakan di HTML
        $templateBase64 = base64_encode(file_get_contents($templatePath));
        $templateMimeType = mime_content_type($templatePath);
        $background = storage_path('app/public/'.$conference->certificate_template_path);
        // Data untuk sertifikat
        $certificateData = [
            'participant_name' => $audience->parallel_sessions->first()->name_of_presenter,
            'paper_title' => $audience->parallel_sessions->first()->paper_title,
            'conference_name' => $conference->name,
            'conference_year' => $conference->year,
            'template_base64' => $templateBase64,
            'template_mime' => $templateMimeType,
            'background' => $background,
            'positions' => $positions
        ];
        
        try {
            // Generate PDF
            $pdf = Pdf::loadView('certificates.template', $certificateData);
            $pdf->setPaper('A4', 'landscape');
            
            // Preview filename
            $filename = 'Certificate_' . str_replace([' ', '.', ','], '_', $audience->first_name . '_' . $audience->last_name) . '_' . $conference->initial . '.pdf';
            
            return $pdf->stream($filename);
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => 'Gagal membuat sertifikat PDF: ' . $e->getMessage()]);
        }
    }

    public function updatePaymentStatus(Audience $audience)
    {
        $validated = request()->validate([
            'payment_status' => 'required|in:pending_payment,cancelled,paid,refunded'
        ]);

        // Only allow updating payment status for transfer_bank payments
        if ($audience->payment_method !== 'transfer_bank') {
            return response()->json(['error' => 'Payment status can only be updated for transfer bank payments'], 400);
        }

        $audience->update([
            'payment_status' => $validated['payment_status']
        ]);

        if($validated['payment_status'] !== 'pending_payment') {
        $audience->sendPaymentConfirmationEmail();
        }

        return redirect()->back()->with('success', 'Payment status updated successfully');
    }

    public function downloadReceipt(Audience $audience)
    {
        // Check if payment is completed
        if ($audience->payment_status !== 'paid') {
            return redirect()->back()->withErrors(['error' => 'Receipt can only be downloaded for paid registrations']);
        }

        $audience->load('conference');
        $conference = $audience->conference;

        if (!$conference) {
            return redirect()->back()->withErrors(['error' => 'Conference not found']);
        }

        // Prepare data for receipt
        $data = [
            'name' => $audience->first_name . ' ' . $audience->last_name,
            'address' => $audience->institution . ', ' . $audience->country,
            'paper_title' => $audience->paper_title ?? 'N/A',
            'conference' => $conference->initial,
            'conference_name' => $conference->name,
            'conference_cover' => $conference->cover_poster_path ? storage_path('app/public/' . $conference->cover_poster_path) : null,
            'date' => $conference->date,
            'amount' => $audience->country === 'ID' ? 'Rp' . number_format($audience->paid_fee, 0, ',', '.') : '$' . number_format($audience->paid_fee, 2),
            'payment_method' => $audience->payment_method,
            'payment_date' => $audience->updated_at->format('d M Y H:i'),
            'invoice_id' => 'Ref. No.' . strtoupper($audience->public_id) . '/PAID/' . strtoupper($conference->initial) . '/2025',
            'signature' => storage_path('app/public/images/signature.png'),
        ];

        try {
            // Generate PDF
            $pdf = Pdf::loadView('receipt.index', compact('data'))
                      ->setPaper('A4', 'portrait');

            return $pdf->stream("receipt-{$data['name']}.pdf");
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => 'Failed to generate receipt: ' . $e->getMessage()]);
        }
    }
}
