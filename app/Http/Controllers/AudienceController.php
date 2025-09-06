<?php

namespace App\Http\Controllers; // Perhatikan namespace ini

// Pastikan menggunakan base Controller
use App\Models\Audience; // Import Model Audience
use App\Models\Conference; // Import Model Conference
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request; // Untuk upload file
use Illuminate\Support\Facades\Storage;
use App\Constants\Countries;

class AudienceController extends Controller
{
    /**
     * Display a listing of the resource (Menampilkan daftar audiences).
     */
    public function index()
    {
        // Ambil semua data audience, eager load relasi conference
        $audiences = Audience::with('conference')->get();
        $conferences = Conference::all();

        return view('home.audience.index', compact('audiences', 'conferences')); // View di folder 'audience/index.blade.php'
    }

    /**
     * Show the form for creating a new resource (Menampilkan form tambah audience).
     */
    public function create()
    {
        $conferences = Conference::all(); // Ambil daftar conference untuk dropdown

        return view('home.audience.create', compact('conferences')); // View di folder 'audience/create.blade.php'
    }

    public function regis()
    {
        $conferences = Conference::all(); // Ambil daftar conference untuk dropdown

        return view('registration', compact('conferences')); // View di folder 'audience/create.blade.php'
    }

    /**
     * Store a newly created resource in storage (Menyimpan audience baru).
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'conference_id' => 'required|exists:conferences,id',
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'paper_title' => 'nullable|string|max:255',
            'institution' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:audiences,email', // Unik di tabel audiences
            'phone_number' => 'nullable|string|max:20',
            'country' => 'required|string|max:255',
            'presentation_type' => 'required|in:online_author,onsite,participant_only',
            'paid_fee' => 'nullable|numeric|min:0',
            'payment_status' => 'required|in:pending_payment,paid,cancelled,refunded', // Validasi status
            'full_paper' => 'nullable|file|mimes:doc,docx|max:51200',
        ]);

        $fullPaperPath = null;
        if ($request->hasFile('full_paper')) {
            $fullPaperPath = $request->file('full_paper')->store('audience_full_papers', 'public');
        }
        $validatedData['full_paper_path'] = $fullPaperPath;

        Audience::create($validatedData);

        return redirect()->route('audience.index')->with('success', 'Audience berhasil ditambahkan!');
    }

    /**
     * Display the specified resource (Menampilkan detail audience).
     */
    public function show(Audience $audience) // Route Model Binding
    {
        $clearPhoneNumber = preg_replace('/[\s\-\(\)]/', '', $audience->phone_number);
        if (preg_match('/^0/', $clearPhoneNumber) && isset(Countries::LIST[$audience->country])) {
            $phoneNumber = preg_replace('/^0/', Countries::LIST[$audience->country]['code'], $clearPhoneNumber);
        } elseif (preg_match('/^\+/', $clearPhoneNumber)) {
            $phoneNumber = $clearPhoneNumber;
        } else {
            $phoneNumber =
                (isset(Countries::LIST[$audience->country]) ? Countries::LIST[$audience->country]['code'] : '') .
                $clearPhoneNumber;
        }
        $audience->phone_number = $phoneNumber;
        $audience->country_name = isset(Countries::LIST[$audience->country]) ? Countries::LIST[$audience->country]['name'] : $audience->country;
        return view('home.audience.show', compact('audience')); // View di folder 'audience/show.blade.php'
    }

    /**
     * Show the form for editing the specified resource (Menampilkan form edit audience).
     */
    public function edit(Audience $audience) // Route Model Binding
    {
        $conferences = Conference::all(); // Ambil daftar conference untuk dropdown

        return view('home.audience.edit', compact('audience', 'conferences')); // View di folder 'audience/edit.blade.php'
    }

    /**
     * Update the specified resource in storage (Memperbarui data audience).
     */
    public function update(Request $request, Audience $audience) // Route Model Binding
    {
        $validatedData = $request->validate([
            'id' => 'required|exists:audiences,id',
            'payment_status' => 'required|in:pending_payment,paid,cancelled,refunded',
            'full_paper' => 'nullable|file|mimes:doc,docx|max:51200',
        ], [
            'full_paper.mimes' => 'The full paper must be a file of type: doc, docx.',
            'full_paper.max' => 'The full paper may not be greater than 50MB.',
        ]);
        $fullPaperPath = null;
        if ($request->hasFile('full_paper')) {
            $fullPaperPath = $request->file('full_paper')->store('audience_full_papers', 'public');
        }
        if ($fullPaperPath) {
            $validatedData['full_paper_path'] = $fullPaperPath;
        }
        $update = $audience->update($validatedData);
        if (!$update) {
            return redirect()->back()->withInput()->withErrors(['error' => 'Failed to update audience. Please try again.']);
        } else {
            if ($validatedData['payment_status'] !== 'pending_payment') {
                $audience->sendPaymentConfirmationEmail();
            }
        }

        return redirect()->route('home.audience.index')->with('success', 'Audience berhasil diperbarui!');
    }

    /**
     * Remove the specified resource from storage (Menghapus audience).
     */
    public function destroy(Audience $audience) // Route Model Binding
    {
        // Hapus file full paper yang terkait jika ada
        // if ($audience->full_paper_path) {
        //     Storage::disk('public')->delete($audience->full_paper_path);
        // }

        // $audience->delete();

        $audience->delete(); // soft delete

        return redirect()->route('home.audience.index')->with('success', 'Audience berhasil dihapus!');
    }

    public function downloadCertificate(Audience $audience)
    {
        $conference = Conference::find($audience->conference_id);

        if (!$conference) {
            return redirect()->back()->withInput()->withErrors(['conference_id' => 'Selected conference does not exist.']);
        }

        $audience = Audience::where('email', $audience->email)
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

        return $pdf->stream("certificate-{$data['name']}.pdf");
    }
}
