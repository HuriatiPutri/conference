<div>
    <img src="{{ $data['conference_cover'] }}" alt="Conference Cover" style="width: 100%; max-width: 800px;" />
    <div style="margin-top: 20px; margin-bottom: 20px; text-align:right">
        <span>{{ $data['payment_date'] }}</span><br>
        <span>{{ $data['invoice_id'] }}</span>
    </div>
    <h2 style="text-align: center">PAYMENT RECEIPT</h2>
    <p>The Editorial Office of <strong>{{ $data['conference_name'] }}</strong> confirms receipt of the following
        payment for the registration fee:
    </p>
    <table style="width: 100%; border-collapse: collapse;">
        <tbody>
            <tr>
                <th style="text-align: left; padding: 8px;" width="30%">Receipt from</th>
                <td style="padding: 8px;">: {{ $data['name'] }}</td>
            </tr>
            <tr>
                <th style="text-align: left; padding: 8px;">Address</th>
                <td style="padding: 8px;">: {{ $data['address'] }}</td>
            </tr>
            <tr>
                <th style="text-align: left; padding: 8px;">Publication fee for article</th>
                <td style="padding: 8px;">: {{ $data['paper_title'] }}</td>
            </tr>
            <tr>
                <th style="text-align: left; padding: 8px;">Registration Fee</th>
                <td style="padding: 8px;">: {{ $data['amount'] }}</td>
            </tr>
        </tbody>
    </table>

    <img src="{{ $data['signature'] }}" alt="Signature" style="width: 150px; margin-top: 40px; display: block;" />
</div>
