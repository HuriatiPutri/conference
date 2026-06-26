<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Letter of Acceptance (LoA) – {{ $initial }}</title>
</head>

<body style="font-family: Arial, sans-serif; color: #333; line-height: 1.6;">
    <h2 style="color: #0066cc;">Letter of Acceptance (LoA)</h2>

    <p>Dear {{ $name }},</p>

    <p>
        We are happy to inform you that the International Journal on Informatics Visualization (JOIV) has been
        indexed in Scopus. The Scientific committee of JOIV agrees that the following manuscript is accepted
        for publication in JOIV <strong>{{ $joiv_volume }}</strong>.
    </p>

    <h4>Manuscript Details</h4>
    <ul>
        <li><strong>Title:</strong> {{ $paper_title }}</li>
        <li><strong>Authors:</strong> {{ $authors }}</li>
        <li><strong>Target Publication:</strong> JOIV {{ $joiv_volume }}</li>
        <li><strong>Registration Number:</strong> {{ $registration_number }}</li>
    </ul>

    <p>
        Your official Letter of Acceptance (LoA) has been generated and is attached to this email as a PDF file. Please
        keep this document for your records.
    </p>

    <hr style="border: 0; border-top: 1px solid #ccc; margin: 20px 0;">

    <p>We look forward to your participation in <strong>{{ $initial }}</strong> in {{ $place }}.</p>

    <p>Best regards,<br>
        {{ $initial }} Organizing Committee<br>
        <a href="{{ url('/') }}" style="color: #0066cc; text-decoration: none;">{{ url('/') }}</a>
    </p>
</body>

</html>