@extends('layouts.app')
@section('title', 'Detail Pendaftaran Peserta Konferensi')

<script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="{{ config('midtrans.client_key') }}"></script>

@section('content')

<div class="container mt-4">
    <div class="card">
        <div class="card-header">
        Detail Pendaftaran Peserta Konferensi 
            <strong>{{ $conference->name }} ({{ $conference->year }})</strong> 
            {{ $conference->city }}, {{ $conference->country }}
        </div>
        <div class="card-body">
            <table class="table table-bordered">
                <tr>
                    <th>Nama</th>
                    <td>{{ $audience->first_name }} {{ $audience->last_name }}</td>
                </tr>
                <tr>
                    <th>Email</th>
                    <td>{{ $audience->email }}</td>
                </tr>
                <tr>
                    <th>Nomor Telepon</th>
                    <td>{{ $audience->phone_number }}</td>
                </tr>
                <tr>
                    <th>Jenis Peserta</th>
                    <td>{{ $audience->getPresentationTypeText() }}</td>
                </tr>
                <tr>
                    <th>Metode Pembayaran</th>
                    <td>{{ $audience->getPaymentMethodText() }}</td>
                </tr>
                <tr>
                    <th>Status Pembayaran</th>
                    <td>{!! $audience->getPaymentStatusText() !!}</td>
                </tr>
                <tr>
                    <th>Jumlah Pembayaran</th>
                    <td>{{ $audience->paid_fee }}</td>
                </tr>
                <tr>
                    <th>Konferensi</th>
                    <td>{{ $audience->conference->name }}</td>
                </tr>
                <tr>
                    <th>Tanggal Pendaftaran</th>
                    <td>{{ $audience->created_at->format('d F Y H:i') }}</td>
                </tr>
                <tr>
                    <th>File Full Paper</th>
                    <td>
                        @if($audience->full_paper)
                            <a href="{{ asset('storage/' . $audience->full_paper) }}" target="_blank">Lihat Full Paper</a>
                        @else
                            Tidak ada file full paper yang diunggah.
                        @endif
                    </td>
                </tr>
                <tr>
                    <th>Catatan</th>
                    <td>{{ $audience->note }}</td>
                </tr>
                @if($audience->payment_status === 'pending_payment')
                <tr>
                    <td colspan="2">
                        @if($audience->payment_method === 'payment_gateway')
                            @if($expiresAt)
                                <p id="status">Selesaikan Pembayaran sebelum:</p> <span id="countdown" class="badge badge-danger"></span>
                                <div id="countdown-timer">
                                    <span class="badge badge-info" id="hour">00</span>:<span class="badge badge-info" id="minute">00</span>:<span class="badge badge-info" id="second">00</span>
                                </div>
                            </p>
                            @endif
                            <div id="container-payment">
                                <button id="pay-button" class="btn btn-primary" {{ $paymentMethod === 'paypal' ? 'disabled' : '' }}>Bayar sekarang dengan Midtrans</button>
                                <button id="paypal-pay-button" class="btn btn-primary"  {{ $paymentMethod === 'midtrans' ? 'disabled' : '' }}>Bayar dengan PayPal</button>
                            </div>
                        @else
                            Silakan transfer ke rekening yang telah ditentukan.
                        @endif
                    </td>
                </tr>
                @endif
            </table>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script type="text/javascript">
    var expiresAt = {{ $expiresAt }};
    var countDownDate = (expiresAt !== 0) ? new Date(expiresAt * 1000).getTime() : 0;
    var countdownFunction = setInterval(function() {
        var now = new Date().getTime();
        var distance = countDownDate - now;
        
        if (distance < 0) {
            clearInterval(countdownFunction);
            document.getElementById("countdown").innerHTML = "EXPIRED";
            document.getElementById("status").innerHTML = "Waktu pembayaran telah habis.";
            document.getElementById("countdown-timer").style.display = "none";
            document.getElementById("container-payment").style.display = "none";
        } else {
            var hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
            var minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
            var seconds = Math.floor((distance % (1000 * 60)) / 1000);
            document.getElementById("hour").innerHTML = String(hours).padStart(2, '0');
            document.getElementById("minute").innerHTML = String(minutes).padStart(2, '0');
            document.getElementById("second").innerHTML = String(seconds).padStart(2, '0');
        }
    }, 1000);

    document.getElementById('pay-button').addEventListener('click', function(){
        // Buat order Midtrans dengan request AJAX untuk mendapatkan snap token
        fetch("{{ route('payment.getSnapToken') }}", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": "{{ csrf_token() }}"
            },
            body: JSON.stringify({
                audience_id: "{{ $audience_id }}"
            })
        })
        .then(response => response.json())
        .then(data => {
            // Pastikan respons mengandung snapToken
            let snapToken = data.snap_token;
            snap.pay(snapToken, {
                onSuccess: function(result){
                    console.log('success', result);
                    window.location.reload();
                },
                onPending: function(result){
                    console.log('pending', result);
                    window.location.reload();
                },
                onError: function(result){
                    console.log('error', result);
                    alert("Terjadi kesalahan saat pembayaran.");
                    window.location.reload();
                },
                onClose: function(){
                    alert('Kamu menutup popup tanpa menyelesaikan pembayaran');
                    window.location.reload();
                }
            });
        })
        .catch(error => console.error('Error:', error));
    });

    document.getElementById('paypal-pay-button').addEventListener('click', function(){
        console.log('PayPal button clicked');
        // Buat order PayPal dengan request AJAX untuk mendapatkan token
        fetch("{{ route('paypal.pay') }}", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": "{{ csrf_token() }}"
            },
            body: JSON.stringify({
                audience_id: "{{ $audience_id }}"
            })
        })
        .then(response => response.json())
        .then(data => {
            console.log('PayPal response:', data);
            // Pastikan respons mengandung paypalToken
            let paypalToken = data.paypal_token;
            window.location.href = data.redirect_url; // Redirect ke PayPal
        })
        .catch(error => console.error('Error:', error));
    });
</script>
@endsection