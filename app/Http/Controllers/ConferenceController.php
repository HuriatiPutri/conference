<?php

namespace App\Http\Controllers;

use App\Models\Conference;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage; // Penting untuk mengelola file

class ConferenceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Mengambil semua data konferensi
        // Jika Anda menggunakan pagination di sisi server (misal dengan $conferences->links() di Blade),
        // gunakan: $conferences = Conference::paginate(10);
        $conferences = Conference::all(); // Mengambil semua data

        return view('home.conference.index', compact('conferences'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('home.conference.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // 1. Validasi Data
        $validatedData = $request->validate([
            'public_id' => 'required|unique:conferences,public_id',
            'name' => 'required|string|max:255',
            'initial' => 'nullable|string|max:255', // Initial bisa kosong
            'date' => 'nullable|date', // Tanggal bisa kosong, format YYYY-MM-DD
            'cover_poster' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048', // Validasi gambar, max 2MB (2048 KB)
            'city' => 'required|string|max:255',
            'country' => 'required|string|max:255',
            'year' => 'required|integer|min:' . (date('Y') - 10) . '|max:' . (date('Y') + 10),
            'online_fee' => 'required|numeric|min:0',
            'onsite_fee' => 'required|numeric|min:0',
            'participant_fee' => 'required|numeric|min:0',
        ]);

        $coverPosterPath = null;
        // 2. Tangani Upload File Cover Poster (jika ada)
        if ($request->hasFile('cover_poster')) {
            // Simpan file ke direktori 'public/conference_posters'
            // dan dapatkan path relatifnya
            $coverPosterPath = $request->file('cover_poster')->store('conference_posters', 'public');
            // Path yang disimpan akan seperti: 'conference_posters/gambar_unik.jpg'
        }

        // 3. Buat entri baru di database
        // Gabungkan path cover poster ke dalam data yang divalidasi
        $validatedData['public_id'] = uniqid();
        $validatedData['cover_poster_path'] = $coverPosterPath;

        Conference::create($validatedData);

        // 4. Redirect ke halaman daftar konferensi dengan pesan sukses
        return redirect()->route('conference.index')->with('success', 'Konferensi berhasil ditambahkan!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Conference $conference)
    {
        // Menampilkan detail satu konferensi
        return view('conference.show', compact('conference'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Conference $conference)
    {
        // Menampilkan form edit dengan data konferensi yang sudah ada
        return view('home.conference.edit', compact('conference'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Conference $conference)
    {
        // 1. Validasi Data
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'initial' => 'nullable|string|max:255',
            'date' => 'nullable|date',
            'cover_poster' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048', // Cover Poster tidak wajib diupdate
            'city' => 'required|string|max:255',
            'country' => 'required|string|max:255',
            'year' => 'required|integer|min:' . (date('Y') - 10) . '|max:' . (date('Y') + 10),
            'online_fee' => 'required|numeric|min:0',
            'onsite_fee' => 'required|numeric|min:0',
            'participant_fee' => 'required|numeric|min:0',
        ]);

        // 2. Tangani Upload File Cover Poster (jika ada perubahan)
        if ($request->hasFile('cover_poster')) {
            // Hapus cover poster lama jika ada
            if ($conference->cover_poster_path) {
                Storage::disk('public')->delete($conference->cover_poster_path);
            }
            // Simpan cover poster baru
            $coverPosterPath = $request->file('cover_poster')->store('conference_posters', 'public');
            $validatedData['cover_poster_path'] = $coverPosterPath;
        }
        // Jika tidak ada file baru diupload, dan field lama tidak dihapus, biarkan path lama tetap
        // Jika Anda ingin memungkinkan penghapusan poster tanpa upload baru, Anda perlu checkbox 'hapus poster'

        // 3. Update data konferensi di database
        $conference->update($validatedData);

        // 4. Redirect dengan pesan sukses
        return redirect()->route('conference.index')->with('success', 'Konferensi berhasil diperbarui!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Conference $conference)
    {
        // 1. Hapus Cover Poster yang terkait (jika ada)
        if ($conference->cover_poster_path) {
            Storage::disk('public')->delete($conference->cover_poster_path);
        }

        // 2. Hapus data konferensi dari database
        $conference->delete();

        // 3. Redirect dengan pesan sukses
        return redirect()->route('conference.index')->with('success', 'Konferensi berhasil dihapus!');
    }
}