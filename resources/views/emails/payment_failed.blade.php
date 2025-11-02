<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Payment Failed – {{ $initial }}</title>
</head>
<body style="font-family: Arial, sans-serif; color: #333; line-height: 1.6;">
    <h2 style="color: #dc3545;">Payment Failed</h2>

    <p>Dear {{ $name }},</p>

    <p>
        Unfortunately, we were unable to process your payment for 
        <strong>{{ $conference_name }} {{ $year }}</strong>.
    </p>

    <h4>Payment Attempt Details</h4>
    <ul>
        <li><strong>Registration Number:</strong> {{ $registration_number }}</li>
        <li><strong>Amount:</strong> IDR {{ $amount }}</li>
        <li><strong>Payment Date:</strong> {{ $payment_date }}</li>
        <li><strong>Payment Method:</strong> {{ $payment_method }}</li>
        <li><strong>Status:</strong> Failed</li>
    </ul>

    <p>
        Common reasons for payment failure include insufficient balance, incorrect payment details, 
        or issues with the payment provider.
    </p>

    <p>
        Please try making the payment again or use an alternative method. 
        If the issue persists, kindly contact our support team for further assistance.
    </p>

    <hr>

    <p>We appreciate your interest in joining <strong>{{ $initial }}</strong> in {{ $place }} 
    and hope to resolve this issue quickly so your registration can be confirmed.</p>

    <p>Best regards,<br>
    {{ $initial }} Organizing Committee<br>
</body>
</html>
