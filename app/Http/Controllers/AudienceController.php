<?php

namespace App\Http\Controllers; // Perhatikan namespace ini

use App\Http\Controllers\Controller; // Pastikan menggunakan base Controller
use App\Models\Audience; // Import Model Audience
use App\Models\Conference; // Import Model Conference
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage; // Untuk upload file

class AudienceController extends Controller
{
    /**
     * Display a listing of the resource (Menampilkan daftar audiences).
     */
    public function index()
    {
        // Ambil semua data audience, eager load relasi conference
        $audiences = Audience::with('conference')->get();
        return view('home.audience.index', compact('audiences')); // View di folder 'audience/index.blade.php'
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
            'full_paper' => 'nullable|file|mimes:doc,docx|max:5120',
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
            'conference_id' => 'required|exists:conferences,id',
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'paper_title' => 'nullable|string|max:255',
            'institution' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:audiences,email,' . $audience->id, // Email unik, kecuali untuk audience ini sendiri
            'phone_number' => 'nullable|string|max:20',
            'country' => 'required|string|max:255',
            'presentation_type' => 'required|in:online_author,onsite,participant_only',
            'paid_fee' => 'nullable|numeric|min:0',
            'payment_status' => 'required|in:pending_payment,paid,cancelled,refunded',
            'full_paper' => 'nullable|file|mimes:doc,docx|max:5120',
        ]);

        // Tangani upload/penggantian file full paper
        if ($request->hasFile('full_paper')) {
            // Hapus file lama jika ada
            if ($audience->full_paper_path) {
                Storage::disk('public')->delete($audience->full_paper_path);
            }
            $fullPaperPath = $request->file('full_paper')->store('audience_full_papers', 'public');
            $validatedData['full_paper_path'] = $fullPaperPath;
        } elseif ($request->has('remove_full_paper') && $request->remove_full_paper == '1') {
            // Jika checkbox remove_full_paper dicentang
            if ($audience->full_paper_path) {
                Storage::disk('public')->delete($audience->full_paper_path);
            }
            $validatedData['full_paper_path'] = null;
        } else {
            // Pertahankan path lama jika tidak ada upload baru dan tidak dihapus
            $validatedData['full_paper_path'] = $audience->full_paper_path;
        }

        $audience->update($validatedData);

        return redirect()->route('home.audience.index')->with('success', 'Audience berhasil diperbarui!');
    }

    /**
     * Remove the specified resource from storage (Menghapus audience).
     */
    public function destroy(Audience $audience) // Route Model Binding
    {
        // Hapus file full paper yang terkait jika ada
        if ($audience->full_paper_path) {
            Storage::disk('public')->delete($audience->full_paper_path);
        }

        $audience->delete();

        return redirect()->route('home.audience.index')->with('success', 'Audience berhasil dihapus!');
    }
}