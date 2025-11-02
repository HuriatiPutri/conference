<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Payment Confirmation – {{ $initial }}</title>
</head>
<body style="font-family: Arial, sans-serif; color: #333; line-height: 1.6;">
    <h2 style="color: #28a745;">Payment Received Successfully</h2>

    <p>Dear {{ $name }},</p>

    <p>
        We are pleased to inform you that your payment for the 
        <strong>{{$conference_name}} {{$year}}</strong>,
        has been successfully received.
    </p>

    <h4>Payment Details</h4>
    <ul>
        <li><strong>Registration Number:</strong> {{ $registration_number }}</li>
        <li><strong>Amount Paid:</strong> IDR {{ $amount }}</li>
        {{-- <li><strong>Payment Date:</strong> {{ $payment_date }}</li> --}}
        <li><strong>Payment Method:</strong> {{ $payment_method }}</li>
    </ul>

    <p>
        Your registration is now confirmed. We will follow up with additional information regarding the conference schedule, venue, and check-in process closer to the event date.
    </p>

    <p>
        If you require an official invoice or have any further questions, please don't hesitate to contact us.
    </p>

    <hr>

    <p>We look forward to welcoming you to <strong>{{ $initial }}</strong> in {{ $place }}</p>

    <p>Best regards,<br>
    {{ $initial }} Organizing Committee<br>
</body>
</html>
