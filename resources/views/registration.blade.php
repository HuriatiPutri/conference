<?php
use App\Constants\Countries;

?>

@extends('layouts.app')
@section('title', 'Detail Pendaftaran Peserta Konferensi')

<link href="https://getbootstrap.com/docs/4.1/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://getbootstrap.com/docs/4.1/examples/starter-template/starter-template.css" rel="stylesheet">
@section('content')

  <main role="main" class="container">
    <div class="container mt-4">
      <div class="card">
        <div class="card-header">
          Conference Registration Form
          <strong>{{ $conference->name }} ({{ $conference->year }})</strong>
          {{ $conference->city }}, {{ $conference->country }}
        </div>


        <div class="row">
          <div class="col-md-8 offset-md-2">
            @if (session('success'))
              <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
              </div>
            @endif
          </div>
          <div class="card-body">

            {{-- Form Pendaftaran --}}
            <form action="{{ route('registration.store', $conference->public_id) }}" method="POST"
              enctype="multipart/form-data">
              @csrf

              {{-- Hidden input untuk conference_id --}}
              <input type="hidden" name="conference_id" value="{{ $conference->id }}">

              <div class="row mb-3">
                <div class="col-md-6">
                  <label for="first_name" class="form-label">First Name <span class="text-danger">*</span></label>
                  <input type="text" class="form-control @error('first_name') is-invalid @enderror" id="first_name"
                    name="first_name" value="{{ old('first_name') }}" required>
                  @error('first_name')
                    <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
                </div>
                <div class="col-md-6">
                  <label for="last_name" class="form-label">Last Name <span class="text-danger">*</span></label>
                  <input type="text" class="form-control @error('last_name') is-invalid @enderror" id="last_name"
                    name="last_name" value="{{ old('last_name') }}" required>
                  @error('last_name')
                    <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
                </div>
              </div>

              <div class="mb-3">
                <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                <input type="email" class="form-control @error('email') is-invalid @enderror" id="email"
                  name="email" value="{{ old('email') }}" required>
                @error('email')
                  <div class="invalid-feedback">{{ $message }}</div>
                @enderror
              </div>

              <div class="mb-3">
                <label for="phone_number" class="form-label">Phone Number / WhatsApp</label>
                <input type="text" class="form-control @error('phone_number') is-invalid @enderror" id="phone_number"
                  name="phone_number" value="{{ old('phone_number') }}">
                @error('phone_number')
                  <div class="invalid-feedback">{{ $message }}</div>
                @enderror
              </div>

              <div class="mb-3">
                <label for="country" class="form-label fw-bold">Country <span class="text-danger">*</span></label>
                <select class="form-control @error('country') is-invalid @enderror" id="country"
                  name="country" required>
                  <option value="" selected>Choose Country</option>
                  @foreach (Countries::LIST as $code => $country)
                    <option value="{{ $code }}" {{ old('country') == $code ? 'selected' : '' }}>
                      {{ $country['name'] }} ({{ $country['code'] }})
                    </option>
                  @endforeach
                </select>
              </div>

              <div class="mb-3">
                <label for="institution" class="form-label">Institution / Affiliation <span
                    class="text-danger">*</span></label>
                <input type="text" class="form-control @error('institution') is-invalid @enderror" id="institution"
                  name="institution" value="{{ old('institution') }}" required>
                @error('institution')
                  <div class="invalid-feedback">{{ $message }}</div>
                @enderror
              </div>



              <div class="mb-3">
                <label class="form-label">Type of Participation <span class="text-danger">*</span></label>
                <div id="presentation-types-wrapper">
                  <div class="form-check">
                    <input class="form-check-input presentation-type-radio" type="radio" name="presentation_type"
                      id="online_author" value="online_author"
                      {{ old('presentation_type') == 'online_author' ? 'checked' : '' }} required>
                    <label class="form-check-label" for="online_author">
                      Online (author/presenter) <span class="text-muted small-info" id="online-fee-display"></span>
                    </label>
                  </div>
                  <div class="form-check">
                    <input class="form-check-input presentation-type-radio" type="radio" name="presentation_type"
                      id="onsite" value="onsite" {{ old('presentation_type') == 'onsite' ? 'checked' : '' }}>
                    <label class="form-check-label" for="onsite">
                      Onsite <span class="text-muted small-info" id="onsite-fee-display"></span>
                    </label>
                  </div>
                  <div class="form-check">
                    <input class="form-check-input presentation-type-radio" type="radio" name="presentation_type"
                      id="participant_only" value="participant_only"
                      {{ old('presentation_type') == 'participant_only' ? 'checked' : '' }}>
                    <label class="form-check-label" for="participant_only">
                      Participant Only <span class="text-muted small-info" id="participant-fee-display"></span>
                    </label>
                  </div>
                </div>
                @error('presentation_type')
                  <div class="text-danger small mt-1">{{ $message }}</div>
                @enderror
              </div>

              <div id="paper-title-and-file-section"
                class="{{ in_array(old('presentation_type'), ['online_author', 'onsite']) ? '' : 'd-none' }}">
                <div class="mb-3">
                  <label for="paper_title" class="form-label">Paper Title <span class="text-danger">*</span></label>
                  <input type="text" class="form-control @error('paper_title') is-invalid @enderror"
                    id="paper_title" name="paper_title" value="{{ old('paper_title') }}">
                  @error('paper_title')
                    <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
                </div>

                <div class="mb-3">
                  <label for="full_paper" class="form-label">Upload Full Paper (Doc/Docx)</label>
                  <input type="file" class="form-control @error('full_paper') is-invalid @enderror" id="full_paper"
                    name="full_paper" accept=".doc,.docx">
                  <div class="form-text">Maximum file size: 5MB. Only .doc and .docx formats are allowed.</div>
                  @error('full_paper')
                    <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
                </div>
              </div>

              <div class="mb-3">
                <label for="paid_fee" class="form-label">Fee Paid</label>
                <div class="input-group">
                  <span class="input-group-text">Rp</span>
                  <input type="number" step="0.01" class="form-control @error('paid_fee') is-invalid @enderror"
                    id="paid_fee" name="paid_fee" value="{{ old('paid_fee', 0) }}" readonly>
                </div>
                <div class="form-text">The fee will be automatically updated according to the selection above.</div>
                @error('paid_fee')
                  <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror
              </div>

              <h5 class="mb-3 mt-4">Payment Method <span class="text-danger">*</span></h5>
              <div class="mb-3">
                <div class="form-check">
                  <input class="form-check-input payment-method-radio" type="radio" name="payment_method"
                    id="transfer_bank" value="transfer_bank"
                    {{ old('payment_method') == 'transfer_bank' ? 'checked' : '' }} required>
                  <label class="form-check-label" for="transfer_bank">
                    Bank Transfer
                  </label>
                </div>
                <div id="bank-transfer-info">
                  <div class="card" style="width: 30rem;">
                    <div class="card-body">
                      <h5 class="card-title"><strong>Bank Transfer Information:</strong></h5>
                      <span><strong>Bank Negara Indonesia (BNI)</strong></span><br />
                      <span>Alde Alanda <strong>0310526940</strong>
                        <button type="button" id="btn-copy" class="btn btn-primary btn-sm"><i
                            class="far fa-copy"></i></button>
                      </span>
                      <div class="alert alert-success" role="alert" id="copy-alert" style="display: none;">
                        Account number has been copied to clipboard.
                      </div>
                    </div>
                  </div>
                  <div class="mb-3" id="payment-proof-upload">
                    <label for="payment_proof" class="form-label">Upload Bukti Pembayaran <span
                        class="text-danger">*</span></label>
                    <input class="form-control @error('payment_proof') is-invalid @enderror" type="file"
                      id="payment_proof" name="payment_proof" accept="image/*">
                    <div class="form-text">Hanya file gambar (JPG, PNG) maksimal 2MB.</div>
                    @error('payment_proof')
                      <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                  </div>
                </div>
                <div class="form-check mt-2">
                  <input class="form-check-input payment-method-radio" type="radio" name="payment_method"
                    id="payment_gateway" value="payment_gateway"
                    {{ old('payment_method') == 'payment_gateway' ? 'checked' : '' }} required>
                  <label class="form-check-label" for="payment_gateway">
                    Virtual account, visa, Mastercard, etc.
                  </label>
                </div>
                @error('payment_method')
                  <div class="text-danger small mt-1">{{ $message }}</div>
                @enderror
              </div>


              <button type="submit" class="btn btn-primary mt-3"> Submit </button>

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
        $('#participant-fee-display').text(' (Rp ' + conferenceFees.participant.toLocaleString('id-ID') +
          ')');
      }

      function paperTitleRequired() {

        var paperTitleWrapper = $('#paper-title-wrapper');
        var selectedPresentationType = $('input[name="presentation_type"]:checked').val();
        var paperTitleField = $('#paper_title');

        // Tampilkan atau sembunyikan field Paper Title berdasarkan pilihan
        if (selectedPresentationType === 'online_author' || selectedPresentationType === 'onsite') {
          $('#paper-title-and-file-section').removeClass('d-none');
        } else {
          $('#paper-title-and-file-section').addClass('d-none');
          paperTitleField.val(''); // Kosongkan nilai jika disembunyikan
        }

        if (selectedPresentationType === 'online_author' || selectedPresentationType === 'onsite') {
          paperTitleField.attr('required', true);
        } else {
          paperTitleField.removeAttr('required');
        }
      }

      // --- Logika untuk Pilihan Pembayaran ---
      function togglePaymentProof() {
        var selectedMethod = $('input[name="payment_method"]:checked').val();
        var paymentProofUpload = $('#payment-proof-upload');
        if (selectedMethod === 'transfer_bank') {
          $('#bank-transfer-info').removeClass('d-none');
          $('#payment-proof-upload').show();
          $('#payment_proof').attr('required', true);
        } else {
          $('#bank-transfer-info').addClass('d-none');
          $('#payment-proof-upload').hide();
          $('#payment_proof').removeAttr('required');
        }
      }

      function copyToClipboard(text) {
        navigator.clipboard.writeText(text).then(function() {
          // Tampilkan alert sukses
          $('#copy-alert').show();
          console.log('Text copied to clipboard');
          // Sembunyikan alert setelah 2 detik
          setTimeout(function() {
            $('#copy-alert').hide();
          }, 1000);
        }, function(err) {
          console.error('Could not copy text: ', err);
        });
      }

      // Panggil fungsi ketika radio button Tipe Partisipasi berubah
      $('.presentation-type-radio').change(function() {
        updatePaidFee();
        paperTitleRequired();
      });

      // Event listener untuk tombol salin
      $('#btn-copy').click(function() {
        var accountNumber = "0310526940"; // Ganti dengan nomor rekening yang ingin disalin
        copyToClipboard(accountNumber);
      });


      // Panggil fungsi ketika radio button Metode Pembayaran berubah
      $('.payment-method-radio').change(function() {
        togglePaymentProof();
      });

      // Panggil fungsi saat halaman dimuat (untuk mengisi nilai awal jika ada old() value)
      updateFeeDisplays(); // Tampilkan biaya di samping radio button
      updatePaidFee(); // Isi field biaya dibayar
      togglePaymentProof(); // Tampilkan/sembunyikan bukti pembayaran sesuai old value

      // Jika ada old('paid_fee') yang disimpan dari validasi gagal, pertahankan nilainya
      var oldPaidFee = "{{ old('paid_fee') }}";
      if (oldPaidFee) {
        $('#paid_fee').val(oldPaidFee);
      }
    });
  </script>
@endsection
