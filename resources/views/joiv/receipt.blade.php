<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Payment Receipt - JOIV Registration</title>
    <style>
        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 12px;
            line-height: 1.6;
            color: #333;
            margin: 40px;
        }

        .header {
            text-align: center;
            margin-bottom: 40px;
            border-bottom: 2px solid #1a73e8;
            padding-bottom: 20px;
        }

        .header h1 {
            color: #1a73e8;
            margin: 0;
            font-size: 24px;
        }

        .header p {
            margin: 5px 0;
            color: #666;
        }

        .receipt-info {
            margin-bottom: 30px;
        }

        .receipt-info table {
            width: 100%;
            border-collapse: collapse;
        }

        .receipt-info td {
            padding: 8px;
            border-bottom: 1px solid #eee;
        }

        .receipt-info td:first-child {
            font-weight: bold;
            width: 200px;
        }

        .amount-section {
            background: #f8f9fa;
            padding: 20px;
            margin: 30px 0;
            border-radius: 5px;
            text-align: center;
        }

        .amount-section h3 {
            margin: 0 0 10px 0;
            color: #1a73e8;
        }

        .amount {
            font-size: 32px;
            font-weight: bold;
            color: #1a73e8;
        }

        .footer {
            margin-top: 50px;
            padding-top: 20px;
            border-top: 1px solid #eee;
            text-align: center;
            color: #666;
            font-size: 10px;
        }

        .status-badge {
            display: inline-block;
            padding: 5px 15px;
            background: #34c759;
            color: white;
            border-radius: 15px;
            font-weight: bold;
        }
    </style>
</head>

<body>
    <div class="header">
        <h1>Payment Receipt</h1>
        <p>JOIV Article Registration</p>
        <p>International Journal on Informatics Visualization</p>
    </div>

    <div class="receipt-info">
        <table>
            <tr>
                <td>Receipt ID:</td>
                <td>{{ $registration->public_id }}</td>
            </tr>
            <tr>
                <td>Date:</td>
                <td>{{ $registration->created_at->format('F d, Y H:i:s') }}</td>
            </tr>
            <tr>
                <td>Name:</td>
                <td>{{ $registration->first_name }} {{ $registration->last_name }}</td>
            </tr>
            <tr>
                <td>Email:</td>
                <td>{{ $registration->email_address }}</td>
            </tr>
            <tr>
                <td>Institution:</td>
                <td>{{ $registration->institution }}</td>
            </tr>
            <tr>
                <td>Country:</td>
                <td>{{ $registration->country }}</td>
            </tr>
            <tr>
                <td>Paper ID:</td>
                <td>{{ $registration->paper_id ?? '-' }}</td>
            </tr>
            <tr>
                <td>Paper Title:</td>
                <td>{{ $registration->paper_title }}</td>
            </tr>
            <tr>
                <td>Payment Method:</td>
                <td>{{ $registration->getPaymentMethodText() }}</td>
            </tr>
            <tr>
                <td>Payment Status:</td>
                <td><span class="status-badge">{{ strtoupper($registration->getPaymentStatusText()) }}</span></td>
            </tr>
        </table>
    </div>

    <div class="amount-section">
        <h3>Total Amount Paid</h3>
        <div class="amount">${{ number_format($registration->paid_fee, 2) }} USD</div>
    </div>

    <div class="footer">
        <p>This is a computer-generated receipt and does not require a signature.</p>
        <p>For inquiries, please contact us at support@joiv.org</p>
        <p>Generated on {{ now()->format('F d, Y H:i:s') }}</p>
    </div>
</body>

</html>
