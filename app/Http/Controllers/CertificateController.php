<?php

namespace App\Http\Controllers;

use App\Models\Audience;
use App\Models\Conference;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class CertificateController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $conferences = Conference::all();

        return view('certificate.index', compact('conferences'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $conference = Conference::find($request->conference_id);

        if (!$conference) {
            return redirect()->back()->withInput()->withErrors(['conference_id' => 'Selected conference does not exist.']);
        }

        if ($conference->certificate_template_path === null || $conference->certificate_template_position === null) {
            return redirect()->back()->withInput()
            ->withErrors(['conference_id' => 'Certificate template is not available for the selected conference. Please contact the administrator.']);
        }

        $audience = Audience::where('email', $request->email)
            ->where('conference_id', $conference->id)
            ->first();

        if (!$audience) {
            return redirect()->back()->withInput()->withErrors(['email' => 'This email is not registered for the selected conference.']);
        }

        $keynoteIsExist = $audience ? $audience->keynote()->exists() : false;
        $parallelSessionIsExist = $audience ? $audience->parallelSession()->exists() : false;
        if (!$keynoteIsExist && !$parallelSessionIsExist) {
            return redirect()->back()->withInput()->withErrors(['email' => 'This email has not participated in any sessions for the selected conference.']);
        }

        // Jika validasi berhasil, arahkan ke rute untuk mengunduh sertifikat
        $positions = json_decode($conference->certificate_template_position, true);
        $layout = json_decode($positions['positions'], true);
        $background = storage_path('app/public/'.$conference->certificate_template_path);

        $data = [
            'name' => $audience->first_name.' '.$audience->last_name,
            'conference' => $conference->name,
            'date' => $conference->date,
        ];

        $pdf = Pdf::loadView('certificate.template', compact('data', 'layout', 'background'))
                  ->setPaper('A5', 'landscape');

        return $pdf->download("certificate-{$data['name']}.pdf");

        return redirect()->route('certificate.index');
    }
}
