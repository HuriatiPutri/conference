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
            'name' => 'required|string|max:255',
            'initial' => 'nullable|string|max:255', // Initial bisa kosong
            'date' => 'nullable|date', // Tanggal bisa kosong, format YYYY-MM-DD
            'cover_poster' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048', // Validasi gambar, max 2MB (2048 KB)
            'city' => 'required|string|max:255',
            'country' => 'required|string|max:255',
            'year' => 'required|integer|min:'.(date('Y') - 10).'|max:'.(date('Y') + 10),
            'online_fee' => 'required|numeric|min:0',
            'onsite_fee' => 'required|numeric|min:0',
            'participant_fee' => 'required|numeric|min:0',
            'online_fee_usd' => 'required|numeric|min:0',
            'onsite_fee_usd' => 'required|numeric|min:0',
            'participant_fee_usd' => 'required|numeric|min:0',
        ]);

        $validatedDataRoom = $request->validate([
            'room' => 'required|array|min:1',
            'room.*.room_name' => 'required|string|max:255',
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

        $conference = Conference::create($validatedData);

        // Simpan data rooms terkait konferensi
        foreach ($validatedDataRoom['room'] as $roomData) {
            $roomData['conference_id'] = $conference->id;
            $conference->rooms()->create($roomData);
        }

        // show error if room not added
        if ($conference->rooms()->count() == 0) {
            return redirect()->back()->withErrors(['rooms' => 'At least one room must be added.'])->withInput();
        }

        // 4. Redirect ke halaman daftar konferensi dengan pesan sukses
        return redirect()->route('conference.index')->with('success', 'Konferensi berhasil ditambahkan!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Conference $conference)
    {
        // Menampilkan detail satu konferensi
        return view('home.conference.show', compact('conference'));
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
            'year' => 'required|integer|min:'.(date('Y') - 10).'|max:'.(date('Y') + 10),
            'online_fee' => 'required|numeric|min:0',
            'onsite_fee' => 'required|numeric|min:0',
            'participant_fee' => 'required|numeric|min:0',
            'online_fee_usd' => 'required|numeric|min:0',
            'onsite_fee_usd' => 'required|numeric|min:0',
            'participant_fee_usd' => 'required|numeric|min:0',
        ]);

        $validatedDataRoom = $request->validate([
            'room' => 'required|array|min:1',
            'room.*.room_name' => 'required|string|max:255',
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

        // Update data rooms terkait konferensi
        // Hapus semua room lama dan tambahkan yang baru
        $conference->rooms()->delete();
        foreach ($validatedDataRoom['room'] as $roomData) {
            $roomData['conference_id'] = $conference->id;
            $conference->rooms()->create($roomData);
        }

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

    public function settingCertificate(Conference $conference)
    {
        $background = $conference->certificate_template_path ? Storage::url($conference->certificate_template_path) : null;

        $routeStore = $background ? route('conference.storeTemplatePosition', $conference->public_id) : route('conference.storeTemplate', $conference->public_id);

        $data = [
            'name' => 'Nama Peserta',
            'conference' => $conference->name,
            'date' => now()->format('d F Y'),
        ];

        $templatePosition = $conference->certificate_template_position ? json_decode($conference->certificate_template_position, true) : json_decode('{"positions":"{}"}', true);

        return view('home.conference.setting_certificate', [
            'conference' => $conference,
            'data' => $data,
            'background' => $background,
            'routeStore' => $routeStore,
            'positions' => json_decode($templatePosition['positions'], true) ?? null,
        ]);
    }

    public function storeTemplate(Request $request, Conference $conference)
    {
        // Validasi dan simpan template sertifikat yang diupload admin
        $validatedData = $request->validate([
            'template' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048', // Validasi gambar, max 2MB (2048 KB)
        ]);

        // Tangani Upload File Template Sertifikat
        if ($request->hasFile('template')) {
            // Simpan file ke direktori 'public/uploads'
            $templatePath = $request->file('template')->store('conference_certificate', 'public');
            // Path yang disimpan akan seperti: 'uploads/template_unik.jpg'

            // Simpan path template ke database (misal di kolom certificate_template_path di tabel conferences)
            $conference->certificate_template_path = $templatePath;
            $conference->save();
        }

        return redirect()->route('home.conference.setting-certificate', $conference->public_id)->with('success', 'Template sertifikat berhasil disimpan!');
    }

    public function storeTemplatePosition(Request $request, Conference $conference)
    {
        // Validasi dan simpan posisi elemen teks pada template sertifikat
        $validatedData = $request->validate([
            'positions' => 'required', // Simpan posisi sebagai string JSON
        ]);

        // Simpan posisi ke database (misal di kolom certificate_template_position di tabel conferences)
        $conference->certificate_template_position = json_encode($validatedData);
        $conference->save();

        return redirect()->route('home.conference.setting-certificate', $conference->public_id)->with('success', 'Posisi elemen sertifikat berhasil disimpan!');
    }
}
