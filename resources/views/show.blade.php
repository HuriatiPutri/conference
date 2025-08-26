@extends('layouts.app')
@section('title', 'Conference Participant Registration Details')

<script src="https://app.midtrans.com/snap/snap.js" data-client-key="{{ config('midtrans.client_key') }}"></script>

@section('content')

  <div class="container mt-4">
    <div class="card">
      <div class="card-header">
        Conference Registration Details
        <strong>{{ $conference->name }} ({{ $conference->year }})</strong>
        {{ $conference->city }}, {{ $conference->country }}
      </div>
      <div class="card-body">
        <table class="table-bordered table">
          <tr>
            <th>Name</th>
            <td>{{ $audience->first_name }} {{ $audience->last_name }}</td>
          </tr>
          <tr>
            <th>Email</th>
            <td>{{ $audience->email }}</td>
          </tr>
          <tr>
            <th>Phone Number</th>
            <td>{{ $audience->phone_number }}</td>
          </tr>
          <tr>
            <th>Type of Participation</th>
            <td>{{ $audience->getPresentationTypeText() }}</td>
          </tr>
          <tr>
            <th>Payment Method</th>
            <td>{{ $audience->getPaymentMethodText() }}</td>
          </tr>
          <tr>
            <th>Payment Status</th>
            <td>{!! $audience->getPaymentStatusText() !!}</td>
          </tr>
          <tr>
            <th>Fee Paid</th>
            <td>{{ $audience->country === 'ID' ? 'Rp' : 'USD' }}{{ number_format($audience->paid_fee) }}</td>
          </tr>
          <tr>
            <th>Conference</th>
            <td>{{ $audience->conference->name }}</td>
          </tr>
          <tr>
            <th>Registration Date</th>
            <td>{{ $audience->created_at->format('d F Y H:i') }}</td>
          </tr>
          <tr>
            <th>File Full Paper</th>
            <td>
              @if ($audience->full_paper)
                <a href="{{ asset('storage/' . $audience->full_paper) }}" target="_blank">View Full Paper</a>
              @else
                No full paper file uploaded.
              @endif
            </td>
          </tr>
          <tr>
            <th>Notes</th>
            <td>{{ $audience->note }}</td>
          </tr>
          @if ($audience->payment_status === 'pending_payment')
            <tr>
              <td colspan="2">
                @if ($audience->payment_method === 'payment_gateway')
                  @if ($expiresAt)
                    <p id="status">Complete Payment Before:</p> <span id="countdown" class="badge badge-danger"></span>
                    <div id="countdown-timer">
                      <span class="badge badge-info" id="hour">00</span>:<span class="badge badge-info"
                        id="minute">00</span>:<span class="badge badge-info" id="second">00</span>
                    </div>
                    </p>
                  @endif
                  <div id="container-payment">
                    @if ($audience->country === 'ID')
                      <button id="pay-button" class="btn btn-primary"
                        {{ $paymentMethod === 'paypal' ? 'disabled' : '' }}>Pay Now with Virtual Account (VA)</button>
                    @endif
                    <button id="paypal-pay-button" class="btn btn-primary"
                      {{ $paymentMethod === 'midtrans' ? 'disabled' : '' }}>Pay with PayPal / Credit Card</button>
                  </div>
                @else
                  Please transfer to the specified account.
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

    function payWithMidtrans() {
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
            onSuccess: function(result) {
              console.log('success', result);
              window.location.reload();
            },
            onPending: function(result) {
              console.log('pending', result);
              window.location.reload();
            },
            onError: function(result) {
              console.log('error', result);
              alert("An error occurred during the payment process.");
              window.location.reload();
            },
            onClose: function() {
              alert('You closed the popup without completing the payment.');
              window.location.reload();
            }
          });
        })
        .catch(error => console.error('Error:', error));
    }

    function payWithPayPal() {
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
          // Pastikan respons mengandung paypalToken
          console.log(data);
          if (data.success === false) {
            alert(data.error);
            return;
          } else {
            let paypalToken = data.paypal_token;
            // window.location.href = data.redirect_url; // Redirect ke PayPal
          }
        })
        .catch(error => console.error('Error:', error));
    }
    $('#pay-button').click(function() {
      payWithMidtrans();
    });

    $('#paypal-pay-button').click(function() {
      payWithPayPal();
    });
  </script>
@endsection
