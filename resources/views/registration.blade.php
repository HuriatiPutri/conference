<?php
use App\Constants\Countries;

?>

@extends('layouts.app')
@section('title', 'Detail Pendaftaran Peserta Konferensi')

<link href="https://getbootstrap.com/docs/4.1/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://getbootstrap.com/docs/4.1/examples/starter-template/starter-template.css" rel="stylesheet">
@section('content')

        <nav class="navbar navbar-expand-md navbar-dark bg-dark fixed-top">
            <a class="navbar-brand" href="#">SOTVI</a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarsExampleDefault" aria-controls="navbarsExampleDefault" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarsExampleDefault">
                <ul class="navbar-nav mr-auto">
                    <li class="nav-item active">
                        <a class="nav-link" href="#"> Conference Management System <span class="sr-only">(current)</span></a>
                    </li>
                </ul>
            </div>
        </nav>

        <main role="main" class="container">

            <div class="container mt-4">
                <div class="card">
                    <div class="card-header">
                        Formulir Pendaftaran Conference

                        <strong>{{ $conference->name }} ({{ $conference->year }})</strong>

                        {{ $conference->city }}, {{ $conference->country }}
                    </div>


                    <div class="row">
                        <div class="col-md-8 offset-md-2">
                            @if(session('success'))
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                {{ session('success') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                            @endif
                        </div>
                        <div class="card-body">
                            <form action="{{ route('registration.store', $conference->public_id) }}" method="POST" enctype="multipart/form-data">
                                @csrf

                                {{-- Hidden input untuk conference_id --}}
                                <input type="hidden" name="conference_id" value="{{ $conference->id }}">

                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label for="first_name" class="form-label">Nama Depan <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control @error('first_name') is-invalid @enderror" id="first_name" name="first_name" value="{{ old('first_name') }}" required>
                                        @error('first_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    </div>
                                    <div class="col-md-6">
                                        <label for="last_name" class="form-label">Nama Belakang <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control @error('last_name') is-invalid @enderror" id="last_name" name="last_name" value="{{ old('last_name') }}" required>
                                        @error('last_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                                    <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email') }}" required>
                                    @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="phone_number" class="form-label">Nomor Telepon/WhatsApp</label>
                                    <input type="text" class="form-control @error('phone_number') is-invalid @enderror" id="phone_number" name="phone_number" value="{{ old('phone_number') }}">
                                    @error('phone_number') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="country" class="form-label fw-bold">Country <span class="text-danger">*</span></label>
                                    <select class="form-control rounded-0 @error('country') is-invalid @enderror" id="country" name="country" required>
                                        <option value="" selected>Choose Country</option>
                                        @foreach(Countries::LIST as $code => $country)
                                            <option value="{{ $code }}" {{ old('country') == $code ? 'selected' : '' }}>
                                                {{ $country['name'] }} ({{ $country['code'] }})
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="mb-3">
                                    <label for="institution" class="form-label">Institusi/Afiliasi <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('institution') is-invalid @enderror" id="institution" name="institution" value="{{ old('institution') }}" required>
                                    @error('institution') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="paper_title" class="form-label">Judul Paper</label>
                                    <input type="text" class="form-control @error('paper_title') is-invalid @enderror" id="paper_title" name="paper_title" value="{{ old('paper_title') }}">
                                    <div class="form-text">Opsional jika tidak berencana presentasi paper.</div>
                                    @error('paper_title') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Tipe Partisipasi <span class="text-danger">*</span></label>
                                    <div id="presentation-types-wrapper">
                                        <div class="form-check">
                                            <input class="form-check-input presentation-type-radio" type="radio" name="presentation_type" id="online_author" value="online_author" {{ old('presentation_type') == 'online_author' ? 'checked' : '' }} required>
                                            <label class="form-check-label" for="online_author">
                                                Online (author/presenter) <span class="text-muted small-info" id="online-fee-display"></span>
                                            </label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input presentation-type-radio" type="radio" name="presentation_type" id="onsite" value="onsite" {{ old('presentation_type') == 'onsite' ? 'checked' : '' }}>
                                            <label class="form-check-label" for="onsite">
                                                Onsite <span class="text-muted small-info" id="onsite-fee-display"></span>
                                            </label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input presentation-type-radio" type="radio" name="presentation_type" id="participant_only" value="participant_only" {{ old('presentation_type') == 'participant_only' ? 'checked' : '' }}>
                                            <label class="form-check-label" for="participant_only">
                                                Participant Only <span class="text-muted small-info" id="participant-fee-display"></span>
                                            </label>
                                        </div>
                                    </div>
                                    @error('presentation_type') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="paid_fee" class="form-label">Biaya Dibayar</label>
                                    <div class="input-group">
                                        <span class="input-group-text">Rp</span>
                                        <input type="number" step="0.01" class="form-control @error('paid_fee') is-invalid @enderror" id="paid_fee" name="paid_fee" value="{{ old('paid_fee', 0) }}" readonly>
                                    </div>
                                    <div class="form-text">Biaya akan otomatis terisi berdasarkan pilihan di atas.</div>
                                    @error('paid_fee') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                                </div>

                                {{-- payment_status tidak perlu di form, karena diisi default di controller --}}

                                <div class="mb-3">
                                    <label for="full_paper" class="form-label">Upload Full Paper (Doc/Docx)</label>
                                    <input type="file" class="form-control @error('full_paper') is-invalid @enderror" id="full_paper" name="full_paper" accept=".doc,.docx">
                                    <div class="form-text">Maksimal 5MB. Hanya format .doc dan .docx yang diperbolehkan.</div>
                                    @error('full_paper') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                                
                                <h5 class="mt-4 mb-3">Metode Pembayaran <span class="text-danger">*</span></h5>
                                        <div class="mb-3">
                                            <div class="form-check">
                                                <input class="form-check-input payment-method-radio" type="radio" name="payment_method" id="transfer_bank" value="transfer_bank" {{ old('payment_method') == 'transfer_bank' ? 'checked' : '' }} required>
                                                <label class="form-check-label" for="transfer_bank">
                                                    Transfer Bank
                                                </label>
                                            </div>
                                            <div id="bank-transfer-info" class="mt-2 {{ old('payment_method') == 'transfer_bank' ? '' : 'd-none' }}">
                                                <p class="mb-1"><strong>Informasi Transfer Bank:</strong></p>
                                                <p class="mb-1">Bank Name: <strong>Bank Negara Indonesia (BNI)</strong></p>
                                                <p class="mb-1">Account Number: <strong>0310526940</strong></p>
                                                <p class="mb-1">Account Holder: <strong>Alde Alanda</strong></p>
                                                <div class="mb-3" id="payment-proof-upload">
                                            <label for="payment_proof" class="form-label">Upload Bukti Pembayaran <span class="text-danger">*</span></label>
                                            <input class="form-control @error('payment_proof') is-invalid @enderror" type="file" id="payment_proof" name="payment_proof" accept="image/*">
                                            <div class="form-text">Hanya file gambar (JPG, PNG) maksimal 2MB.</div>
                                            @error('payment_proof') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                        </div>
                                            </div>
                                            <div class="form-check mt-2">
                                                <input class="form-check-input payment-method-radio" type="radio" name="payment_method" id="payment_gateway" value="payment_gateway" {{ old('payment_method') == 'payment_gateway' ? 'checked' : '' }} required>
                                                <label class="form-check-label" for="payment_gateway">
                                                    Payment Gateway (Visa, Mastercard, dll.)
                                                </label>
                                            </div>
                                            @error('payment_method') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                                        </div>
                                        
                                
                                <button type="submit" class="btn btn-primary mt-3"><i class="fas fa-save mr-1"></i> Daftar Sekarang</button>

                            </form>

                        </div>

                    </div>
                </div>

            </div>



        </main><!-- /.container -->
@endsection

@section('scripts')
        <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script>
        <script src="https://getbootstrap.com/docs/4.1/assets/js/vendor/popper.min.js"></script>
        <script src="https://getbootstrap.com/docs/4.1/dist/js/bootstrap.min.js"></script>


        {{-- <script src="{{ asset('js/custom.js') }}"></script> --}}

        <script>
           $(document).ready(function() {
            // Data biaya konferensi dari objek $conference yang dikirim dari controller
            var conferenceFees = {
                online: parseFloat("{{ $conference->online_fee ?? 0 }}"),
                onsite: parseFloat("{{ $conference->onsite_fee ?? 0 }}"),
                participant: parseFloat("{{ $conference->participant_fee ?? 0 }}")
            };

            // Fungsi untuk mengupdate biaya dibayar
            function updatePaidFee() {
                var selectedPresentationType = $('input[name="presentation_type"]:checked').val();
                var paidFeeField = $('#paid_fee');
                var fee = 0;

                if (selectedPresentationType === 'online_author') {
                    fee = conferenceFees.online;
                } else if (selectedPresentationType === 'onsite') {
                    fee = conferenceFees.onsite;
                } else if (selectedPresentationType === 'participant_only') {
                    fee = conferenceFees.participant;
                }
                
                paidFeeField.val(fee.toFixed(2)); // Mengisi field dengan 2 angka di belakang koma
            }

            // Fungsi untuk mengupdate tampilan biaya di samping radio button
            function updateFeeDisplays() {
                // Tampilkan biaya di samping radio button
                $('#online-fee-display').text(' (Rp ' + conferenceFees.online.toLocaleString('id-ID') + ')');
                $('#onsite-fee-display').text(' (Rp ' + conferenceFees.onsite.toLocaleString('id-ID') + ')');
                $('#participant-fee-display').text(' (Rp ' + conferenceFees.participant.toLocaleString('id-ID') + ')');
            }

            // --- Logika untuk Pilihan Pembayaran ---
            function togglePaymentProof() {
                var selectedMethod = $('input[name="payment_method"]:checked').val();
                if (selectedMethod === 'transfer_bank') {
                    $('#bank-transfer-info').removeClass('d-none');
                    $('#payment-proof-upload').show();
                } else {
                    $('#bank-transfer-info').addClass('d-none');
                    $('#payment-proof-upload').hide();
                }
            }

            // Panggil fungsi ketika radio button Tipe Partisipasi berubah
            $('.presentation-type-radio').change(function() {
                updatePaidFee();
            });

            // Panggil fungsi ketika radio button Metode Pembayaran berubah
            $('.payment-method-radio').change(function() {
                togglePaymentProof();
            });

            // Panggil fungsi saat halaman dimuat (untuk mengisi nilai awal jika ada old() value)
            updateFeeDisplays(); // Tampilkan biaya di samping radio button
            updatePaidFee();     // Isi field biaya dibayar
            togglePaymentProof(); // Tampilkan/sembunyikan bukti pembayaran sesuai old value

            // Jika ada old('paid_fee') yang disimpan dari validasi gagal, pertahankan nilainya
            var oldPaidFee = "{{ old('paid_fee') }}";
            if (oldPaidFee) {
                $('#paid_fee').val(oldPaidFee);
            }
        });
    </script>
    @endsection