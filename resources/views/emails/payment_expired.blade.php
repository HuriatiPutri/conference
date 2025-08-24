<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Payment Expired – {{ $initial }}</title>
</head>
<body style="font-family: Arial, sans-serif; color: #333; line-height: 1.6;">
    <h2 style="color: #6c757d;">Payment Expired</h2>

    <p>Dear {{ $name }},</p>

    <p>
        We regret to inform you that the payment for your registration to 
        <strong>{{ $conference_name }} {{ $year }}</strong> has <strong>expired</strong>.
    </p>

    <h4>Registration Details</h4>
    <ul>
        <li><strong>Registration Number:</strong> {{ $registration_number }}</li>
        <li><strong>Amount Due:</strong> IDR {{ $amount }}</li>
        <li><strong>Payment Method:</strong> {{ $payment_method }}</li>
        <li><strong>Status:</strong> Expired</li>
    </ul>

    <p>
        Since the payment was not completed within the required timeframe, your registration is not confirmed.
    </p>

    <p>
        If you still wish to attend, please proceed with a new registration and complete the payment 
        within the given deadline to secure your spot.
    </p>

    <hr>

    <p>We appreciate your interest in <strong>{{ $initial }}</strong> and hope to see you in {{ $place }}.</p>

    <p>Best regards,<br>
    {{ $initial }} Organizing Committee<br>
</body>
</html>
