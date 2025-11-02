<?php

namespace App\Http\Controllers;

use App\Models\Audience;
use App\Models\Conference;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;
use Barryvdh\DomPDF\Facade\Pdf;

class CertificateController extends Controller
{
    /**
     * Show certificate download form or process download
     */
    public function downloadOrShow(Request $request)
    {
        // If no parameters provided, show the form
        if (!$request->has('conference_id') || !$request->has('email')) {
            return $this->create();
        }

        try {
            // If parameters provided, process the download
            return $this->download($request);
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Validation failed, redirect back to form with errors and input
            return redirect('/certificate/download')
                ->withErrors($e->errors())
                ->withInput($request->only(['conference_id', 'email']));
        }
    }

    /**
     * Show certificate download form
     */
    public function create(): Response
    {
        $conferences = Conference::whereNull('deleted_at')
                                ->orderBy('name')
                                ->get(['id', 'name', 'initial', 'date', 'city', 'country']);

        return Inertia::render('Certificate/Download', [
            'conferences' => $conferences,
            'errors' => session('errors') ? session('errors')->getBag('default')->getMessages() : [],
            'oldInput' => old()
        ]);
    }

    /**
     * Process certificate download request
     */
    public function download(Request $request)
    {
        $validatedData = $request->validate([
            'conference_id' => 'required|integer|exists:conferences,id',
            'email' => 'required|email|max:255',
        ]);

        // Find conference by ID
        $conference = Conference::where('id', $validatedData['conference_id'])
                               ->whereNull('deleted_at')
                               ->first();
        
        if (!$conference) {
            return redirect('/certificate/download')
                ->withErrors(['conference_id' => 'Conference not found.'])
                ->withInput($request->only(['conference_id', 'email']));
        }

        // Find audience by email and conference
        $audience = Audience::where('email', $validatedData['email'])
                           ->where('conference_id', $conference->id)
                           ->whereNull('deleted_at')
                           ->first();

        if (!$audience) {
            return redirect('/certificate/download')
                ->withErrors(['email' => 'Email not found in this conference registration.'])
                ->withInput($request->only(['conference_id', 'email']));
        }

        // Check if audience is submitted keynote or parallel session presenter
        if (!$audience->key_notes()->exists() && !$audience->parallel_sessions()->exists()) {
            return redirect('/certificate/download')
                ->withErrors(['email' => 'Certificate is only available for keynote or parallel session presenters.'])
                ->withInput($request->only(['conference_id', 'email']));
        }

        // Check if audience has paid
        if ($audience->payment_status !== 'paid') {
            return redirect('/certificate/download')
                ->withErrors(['email' => 'Certificate is only available for participants with paid status.'])
                ->withInput($request->only(['conference_id', 'email']));
        }

        // Load conference with template
        $audience->load('conference');
        $conference = $audience->conference;
        
        // Validate certificate template exists
        if (!$conference || !$conference->certificate_template_path || !$conference->certificate_template_position) {
            return redirect('/certificate/download')
                ->withErrors(['conference_id' => 'Certificate template has not been set up for this conference.'])
                ->withInput($request->only(['conference_id', 'email']));
        }
        
        // Parse position data
        try {
            $positionData = json_decode($conference->certificate_template_position, true);
            if (!isset($positionData['positions'])) {
                throw new \Exception('Invalid position data format');
            }
            $positions = json_decode($positionData['positions'], true);
        } catch (\Exception $e) {
            return redirect('/certificate/download')
                ->withErrors(['conference_id' => 'Certificate template position data is invalid: ' . $e->getMessage()])
                ->withInput($request->only(['conference_id', 'email']));
        }
        
        // Template image path
        $templatePath = storage_path('app/public/' . $conference->certificate_template_path);
        
        if (!file_exists($templatePath)) {
            return redirect('/certificate/download')
                ->withErrors(['conference_id' => 'Certificate template file not found.'])
                ->withInput($request->only(['conference_id', 'email']));
        }
        
        // Encode image to base64 for HTML use
        try {
            $templateBase64 = base64_encode(file_get_contents($templatePath));
            $templateMimeType = mime_content_type($templatePath);
        } catch (\Exception $e) {
            return redirect('/certificate/download')
                ->withErrors(['conference_id' => 'Failed to read certificate template image: ' . $e->getMessage()])
                ->withInput($request->only(['conference_id', 'email']));
        }
        
        dd($audience->parallel_sessions->first()->name_of_presenter);
        // Certificate data
        $certificateData = [
            'participant_name' => $audience->parallel_sessions->first()->name_of_presenter,
            'paper_title' => $audience->parallel_sessions->first()->paper_title,
            'conference_name' => $conference->name,
            'conference_year' => $conference->year ?? date('Y'),
            'template_base64' => $templateBase64,
            'template_mime' => $templateMimeType,
            'positions' => $positions
        ];
        
        try {
            // Generate PDF
            $pdf = Pdf::loadView('certificates.template', $certificateData);
            $pdf->setPaper('A4', 'landscape');
            $pdf->setOptions([
                'isHtml5ParserEnabled' => true,
                'isPhpEnabled' => true,
                'dpi' => 150,
                'defaultFont' => 'Arial'
            ]);
            
            // Filename
            $filename = 'Certificate_' . str_replace([' ', '.', ','], '_', $audience->first_name . '_' . $audience->last_name) . '_' . $conference->initial . '.pdf';
            
            return $pdf->download($filename);
        } catch (\Exception $e) {
            \Log::error('Certificate PDF generation failed: ' . $e->getMessage(), [
                'audience_id' => $audience->id,
                'conference_id' => $conference->id,
                'error' => $e->getTraceAsString()
            ]);
            
            return redirect('/certificate/download')
                ->withErrors(['conference_id' => 'Failed to generate certificate PDF. Please try again or contact support.'])
                ->withInput($request->only(['conference_id', 'email']));
        }
    }
}
