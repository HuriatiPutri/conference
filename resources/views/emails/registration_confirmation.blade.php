<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Registration Confirmation – {{ $initial }}</title>
</head>
<body style="font-family: Arial, sans-serif; color: #333; line-height: 1.6;">
    <h2 style="color: #0066cc;">Registration Successful – {{ $initial }}</h2>

    <p>Dear {{ $name }},</p>

    <p>
        Thank you for registering as a participant in the upcoming 
        <strong>{{$conference_name}} {{$year}}</strong>,
        which will be held in <strong>{{ $place }}</strong>.
    </p>

    <p><strong>Your Registration Details:</strong></p>
    <ul>
        <li><strong>Full Name:</strong> {{ $name }}</li>
        <li><strong>Email:</strong> {{ $email }}</li>
        <li><strong>Registration Number:</strong> {{ $registration_number }}</li>
        <li><strong>Registration Date:</strong> {{ $registration_date }}</li>
    </ul>

    <p>
        To complete your registration and secure your participation, please proceed with the payment via the link below:
    </p>

    <p>
        <a href="{{ $payment_link }}" style="display: inline-block; padding: 12px 24px; background-color: #007bff; color: white; text-decoration: none; border-radius: 6px;">
            Proceed to Payment
        </a>
    </p>

    <p>If the button above doesn’t work, you can copy and paste this link into your browser:</p>

    <p><a href="{{ $payment_link }}">{{ $payment_link }}</a></p>

    <hr>

    <p>
        If you did not register for this conference, please ignore this email or contact the organizing committee.
    </p>

    <p>Warm regards,<br>
    – {{ $initial }} Organizing Committee<br>
</body>
</html>
