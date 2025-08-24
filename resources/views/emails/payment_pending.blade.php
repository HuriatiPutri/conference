<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Payment Pending – {{ $initial }}</title>
</head>
<body style="font-family: Arial, sans-serif; color: #333; line-height: 1.6;">
    <h2 style="color: #ffc107;">Payment Pending Confirmation</h2>

    <p>Dear {{ $name }},</p>

    <p>
        We have received your payment request for 
        <strong>{{ $conference_name }} {{ $year }}</strong>.
        However, your payment status is currently <strong>Pending</strong>.
    </p>

    <h4>Payment Details</h4>
    <ul>
        <li><strong>Registration Number:</strong> {{ $registration_number }}</li>
        <li><strong>Amount:</strong> IDR {{ $amount }}</li>
        <li><strong>Payment Date:</strong> {{ $payment_date }}</li>
        <li><strong>Payment Method:</strong> {{ $payment_method }}</li>
        <li><strong>Status:</strong> Pending Verification</li>
    </ul>

    <p>
        Once your payment has been successfully verified, you will receive another confirmation email, 
        and your registration will be officially confirmed.
    </p>

    <p>
        If you have already completed the payment, please allow some time for the verification process. 
        Should you have any questions or need assistance, feel free to contact our support team.
    </p>

    <hr>

    <p>Thank you for your patience and for joining <strong>{{ $initial }}</strong> in {{ $place }}.</p>

    <p>Best regards,<br>
    {{ $initial }} Organizing Committee<br>
</body>
</html>
