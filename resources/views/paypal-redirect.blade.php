<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Redirecting to PayPal...</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            background-color: #f5f5f5;
        }

        .container {
            text-align: center;
            background: white;
            padding: 2rem;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            max-width: 400px;
        }

        .spinner {
            border: 4px solid #f3f3f3;
            border-top: 4px solid #0070f3;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            animation: spin 1s linear infinite;
            margin: 0 auto 1rem;
        }

        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }

        .paypal-button {
            background-color: #0070f3;
            color: white;
            padding: 12px 24px;
            border: none;
            border-radius: 6px;
            font-size: 16px;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            margin-top: 1rem;
        }

        .paypal-button:hover {
            background-color: #0051a5;
        }

        .debug-info {
            margin-top: 1rem;
            padding: 1rem;
            background: #f8f9fa;
            border-radius: 4px;
            font-size: 12px;
            color: #666;
            word-break: break-all;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="spinner"></div>
        <h2>Redirecting to PayPal...</h2>
        <p>You will be redirected to PayPal in a moment...</p>
        <p>If the redirect doesn't work automatically, click the button below:</p>

        <a href="{{ $paypal_url }}" class="paypal-button" target="_blank">
            Continue to PayPal
        </a>

        <div class="debug-info">
            <strong>Debug Info:</strong><br>
            Payment ID: {{ $payment_id }}<br>
            PayPal URL: {{ $paypal_url }}
        </div>
    </div>

    <script>
        // Auto redirect after 2 seconds
        setTimeout(function() {
            console.log('Redirecting to:', '{{ $paypal_url }}');
            window.location.href = '{{ $paypal_url }}';
        }, 2000);

        // Immediate redirect attempt
        try {
            window.location.href = '{{ $paypal_url }}';
        } catch (e) {
            console.error('Redirect failed:', e);
        }
    </script>
</body>

</html>
