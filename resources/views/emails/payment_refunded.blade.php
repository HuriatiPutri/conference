<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Your Payment Has Been Refunded – {{ $initial }}</title>
</head>
<body style="font-family: Arial, sans-serif; color: #333; line-height: 1.6;">
    <h2 style="color: #17a2b8;">Payment Refunded</h2>

    <p>Dear {{ $name }},</p>

    <p>
        We would like to inform you that your payment for the 
        <strong>{{ $conference_name }} {{ $year }}</strong>,
        has been <strong style="color:#17a2b8;">refunded</strong>.
    </p>

    <h4>Payment Details</h4>
    <ul>
        <li><strong>Registration Number:</strong> {{ $registration_number }}</li>
        <li><strong>Amount Paid:</strong> IDR {{ $amount }}</li>
        {{-- <li><strong>Payment Date:</strong> {{ $payment_date }}</li> --}}
        <li><strong>Payment Method:</strong> {{ $payment_method }}</li>
        <li><strong>Status:</strong> Refunded</li>
    </ul>

    <p>
        The refund has been processed successfully. Depending on your bank or payment provider, 
        it may take up to <strong>5–7 business days</strong> for the amount to appear in your account.
    </p>

    <p>
        If you have any questions regarding your refund, please don’t hesitate to contact our support team.
    </p>

    <hr>

    <p>We appreciate your understanding and hope to see you at <strong>{{ $initial }}</strong> in {{ $place }}.</p>

    <p>Best regards,<br>
    {{ $initial }} Organizing Committee<br>
</body>
</html>
