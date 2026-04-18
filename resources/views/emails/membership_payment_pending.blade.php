<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Payment Pending Verification - Membership</title>
</head>
<body style="font-family: Arial, sans-serif; color: #333; line-height: 1.6;">
    <h2 style="color: #ffc107;">Payment Under Review</h2>

    <p>Dear {{ $name }},</p>

    <p>
        Thank you for submitting your payment proof for the <strong>{{ $package_name }}</strong> subscription. 
    </p>

    <h4>Payment Details</h4>
    <ul>
        <li><strong>Registration ID:</strong> {{ $public_id }}</li>
        <li><strong>Amount:</strong> {{ $currency }} {{ number_format($amount, 2) }}</li>
        <li><strong>Status:</strong> Pending Verification</li>
    </ul>

    <p>
        Our administration team will manually verify your transfer. Once verified, you will receive another email containing instructions to set up your password and access your account.
    </p>

    <p>
        Please allow up to 1-2 business days for the verification process.
    </p>

    <hr style="margin-top: 30px;">
    <p>Best regards,<br>
    SOTVI Administration</p>
</body>
</html>
