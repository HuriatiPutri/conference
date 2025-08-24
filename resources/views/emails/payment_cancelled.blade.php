<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Your Payment Status Has Been Cancelled – {{ $initial }}</title>
</head>
<body style="font-family: Arial, sans-serif; color: #333; line-height: 1.6;">
    <h2 style="color: #dc3545;">Payment Cancelled</h2>

    <p>Dear {{ $name }},</p>

    <p>
        We would like to inform you that your payment for the 
        <strong>{{ $conference_name }} {{ $year }}</strong>,
        has been <strong style="color:#dc3545;">cancelled</strong>.
    </p>

    <h4>Payment Details</h4>
    <ul>
        <li><strong>Registration Number:</strong> {{ $registration_number }}</li>
        <li><strong>Amount:</strong> IDR {{ $amount }}</li>
        <li><strong>Payment Method:</strong> {{ $payment_method }}</li>
        <li><strong>Status:</strong> Cancelled</li>
    </ul>

    <p>
        If this cancellation was not requested by you, please contact our support team immediately for further assistance.
    </p>

    <p>
        Should you have any other questions or require additional clarification, please don’t hesitate to get in touch with us.
    </p>

    <hr>

    <p>We appreciate your understanding and hope to see you at <strong>{{ $initial }}</strong> in {{ $place }}.</p>

    <p>Best regards,<br>
    {{ $initial }} Organizing Committee<br>
</body>
</html>
