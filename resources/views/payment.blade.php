<!DOCTYPE html>
<html>
<head>
    <title>Bayar Sekarang</title>
    <script src="https://app.sandbox.midtrans.com/snap/snap.js" 
            data-client-key="{{ config('midtrans.client_key') }}"></script>
</head>
<body>
    <h2>Menyiapkan pembayaran...</h2>

    <script type="text/javascript">
        window.onload = function () {
            snap.pay('{{ $snapToken }}', {
                onSuccess: function(result){
                    console.log('success', result);
                    window.location.href = "/payment/success";
                },
                onPending: function(result){
                    console.log('pending', result);
                    window.location.href = "/payment/pending";
                },
                onError: function(result){
                    console.log('error', result);
                    alert("Terjadi kesalahan saat pembayaran.");
                },
                onClose: function(){
                    alert('Kamu menutup popup tanpa menyelesaikan pembayaran');
                    window.location.href = "/paymnet/unfinish";
                }
            });
        }
    </script>
</body>
</html>
