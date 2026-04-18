<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Welcome - Set Your Password</title>
</head>
<body style="font-family: Arial, sans-serif; color: #333; line-height: 1.6;">
    <h2 style="color: #28a745;">Payment Verified. Welcome!</h2>

    <p>Dear {{ $name }},</p>

    <p>
        Thank you for purchasing the <strong>{{ $package_name }}</strong>. 
        Your payment has been successfully verified.
    </p>

    <p>
        To complete your registration and activate your account, please set your password by clicking the link below:
    </p>

    <p style="margin: 20px 0;">
        <a href="{{ $set_password_url }}" style="background-color: #007bff; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;">Set My Password</a>
    </p>

    <p>
        If the button above does not work, you can copy and paste the following URL into your browser: <br>
        <a href="{{ $set_password_url }}">{{ $set_password_url }}</a>
    </p>

    <p>
        If you have any questions, please don't hesitate to contact us.
    </p>

    <hr style="margin-top: 30px;">
    <p>Best regards,<br>
    SOTVI Administration</p>
</body>
</html>
