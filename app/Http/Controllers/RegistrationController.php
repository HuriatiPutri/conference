<?php

namespace App\Http\Controllers;

use App\Models\Audience; // Model untuk menyimpan data pendaftar
use App\Models\Conference; // Model untuk mendapatkan detail konferensi
use App\Models\InvoiceHistory; // Model untuk menyimpan riwayat pembayaran
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage; // Untuk mengelola upload file
use Illuminate\Validation\Rule; // Digunakan untuk aturan validasi 'unique'
use Midtrans\Snap;
use Midtrans\Config;

class RegistrationController extends Controller
{
    public function __construct()
    {
        Config::$serverKey = config('midtrans.server_key');
        Config::$isProduction = config('midtrans.is_production');
        Config::$isSanitized = true;
        Config::$is3ds = true;
    }
    /**
     * Menampilkan formulir pendaftaran peserta untuk konferensi tertentu.
     * Conference ID diambil dari URL melalui Route Model Binding.
     *
     * @param  \App\Models\Conference  $conference  Instance Konferensi dari URL.
     * @return \Illuminate\View\View
     */
    public function create(Conference $conference)
    {
        // Variabel $conference sudah berisi objek Konferensi dari URL
        // View yang akan dimuat adalah 'resources/views/registrations/create.blade.php'
        return view('registration', [
            'conference' => $conference,
            'snapToken' => null,
            'audience_id' => null, // ID peserta akan diisi setelah pendaftaran berhasil
        ]);
        }

    /**
     * Menyimpan data pendaftaran peserta baru dari formulir publik.
     * Data akan disimpan ke tabel 'audiences'.
     *
     * @param  \Illuminate\Http\Request  $request  Objek permintaan HTTP yang berisi data form.
     * @param  \App\Models\Conference  $conference Instance Konferensi yang di-resolve dari URL.
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request, Conference $conference)
    {
        // 1. Validasi Data
        $validatedData = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'paper_title' => 'nullable|string|max:255', // Opsional
            'institution' => 'required|string|max:255',
            'email' => [
                'required',
                'email',
                'max:255',
                // Pastikan email unik di tabel 'audiences' untuk konferensi ini saja.
                Rule::unique('audiences')->where(function ($query) use ($conference) {
                    return $query->where('conference_id', $conference->id);
                }),
            ],
            'phone_number' => 'nullable|string|max:20',
            'country' => 'required|string|max:255',
            'presentation_type' => 'required|in:online_author,onsite,participant_only',
            'full_paper' => 'nullable|file|mimes:doc,docx|max:5120', // Upload file dokumen, max 5MB
            'payment_method' => 'required|in:transfer_bank,payment_gateway', // Metode pembayaran yang didukung
        ]);

        // 2. Tangani Upload File 'full_paper'
        $fullPaperPath = null;
        if ($request->hasFile('full_paper')) {
            $fullPaperPath = $request->file('full_paper')->store('audience_full_papers', 'public');
        }

        // 3. Hitung Biaya yang Dibayar
        $paidFee = 0;
        switch ($validatedData['presentation_type']) {
            case 'online_author':
                $paidFee = $conference->online_fee;
                break;
            case 'onsite':
                $paidFee = $conference->onsite_fee;
                break;
            case 'participant_only':
                $paidFee = $conference->participant_fee;
                break;
        }

        // 4. Simpan Data Pendaftaran ke Database (Tabel 'audiences')
        Audience::create([
            'public_id' => uniqid(), // Buat ID unik untuk peserta
            'conference_id' => $conference->id, // ID konferensi diambil dari objek Conference di URL
            'first_name' => $validatedData['first_name'],
            'last_name' => $validatedData['last_name'],
            'paper_title' => $validatedData['paper_title'],
            'institution' => $validatedData['institution'],
            'email' => $validatedData['email'],
            'phone_number' => $validatedData['phone_number'],
            'country' => $validatedData['country'],
            'presentation_type' => $validatedData['presentation_type'],
            'paid_fee' => $paidFee,
            'payment_status' => 'pending_payment', // Status pembayaran default 'pending_payment'
            'full_paper_path' => $fullPaperPath,
            'payment_method' => $validatedData['payment_method'], // Metode pembayaran default
        ]);


            // Ambil data Audience yang baru saja dibuat (alternatif: simpan hasil create ke variable)
            $audience = Audience::where('email', $validatedData['email'])
                                ->where('conference_id', $conference->id)
                                ->latest('id')->first();

        // 4.1. Kirim Email Konfirmasi Pendaftaran
        $audience->sendEmail();
        // 5. Redirect ke Halaman Formulir Pendaftaran dengan Pesan Sukses
        return redirect()->route('registration.show', $audience->public_id)->with('success', 'Pendaftaran Anda berhasil! Harap selesaikan pembayaran.');
    }

    /**
     * Menampilkan halaman detail pendaftaran peserta.
     * Halaman ini akan menampilkan informasi pendaftaran dan status pembayaran.
     */

    public function show($audience_id)
    {

        $audience = Audience::where('public_id', $audience_id)->firstOrFail();
        $invoiceHistory = InvoiceHistory::where('audience_id', $audience->id)->first();

        return view('show', [
            'audience' => $audience,
            'conference' => $audience->conference,
            'snapToken' => null, // Token Snap akan diisi saat pembayaran
            'audience_id' => $audience->id, // ID peserta untuk referensi
            'expiresAt' => $invoiceHistory ? $invoiceHistory->expired_at : 0, // Waktu kedaluwarsa token
            'paymentMethod' => $invoiceHistory->payment_method ?? null, 
        ]);
    }
}