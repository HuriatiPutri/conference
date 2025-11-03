<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Journal Acceptance Letter</title>
    <style>
        body {
            font-family: 'Times New Roman', serif;
            line-height: 1.4;
            margin: 0;
            padding: 40px;
            color: #333;
            font-size: 11pt;
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
        }

        .joiv-logo {
            font-size: 48px;
            font-weight: bold;
            color: #333;
            text-align: right;
            font-family: Arial, sans-serif;
        }

        .journal-name {
            background-color: #ffa500;
            color: black;
            padding: 8px 20px;
            font-size: 11pt;
            font-weight: bold;
            text-transform: uppercase;
            margin: 10px 0;
        }

        .date {
            text-align: right;
            margin: 30px 0 40px 0;
            font-size: 11pt;
        }

        .recipient {
            margin: 30px 0;
            font-size: 11pt;
        }

        .subject {
            /* margin: 20px 0; */
            font-size: 11pt;
        }

        .content {
            text-align: justify;
            /* margin: 20px 0; */
            font-size: 11pt;
            line-height: 1.4;
        }

        .manuscript-table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
            border: 1px solid #333;
        }

        .manuscript-table td {
            border: 1px solid #333;
            padding: 8px;
            font-size: 10pt;
        }

        .manuscript-table .label {
            width: 80px;
            text-align: center;
            font-weight: bold;
            background-color: #f5f5f5;
        }

        .signature-section {
            margin-top: 30px;
        }

        .signature-left {
            float: left;
            width: 50%;
        }

        .signature-right {
            float: right;
            width: 50%;
            text-align: right;
        }

        .signature-box {
            margin-top: 40px;
        }

        .signature-line {
            border-top: 1px solid #000;
            width: 200px;
            margin: 40px auto 10px auto;
        }

        .contact-info {
            font-size: 10pt;
            margin-top: 10px;
        }

        .clearfix::after {
            content: "";
            display: table;
            clear: both;
        }

        .scopus-info {
            float: right;
            width: 200px;
            text-align: center;
            margin-top: 20px;
        }

        .scopus-badge {
            background-color: #ff6b35;
            color: white;
            padding: 5px 10px;
            font-size: 12pt;
            font-weight: bold;
            margin: 5px 0;
        }

        .citation-score {
            font-size: 24pt;
            font-weight: bold;
            color: #333;
        }

        .footer-logo {
            text-align: center;
            margin-top: 40px;
        }

        .sotvi-logo {
            color: #0066cc;
            font-size: 14pt;
            font-weight: bold;
        }
    </style>
</head>

<body>
    <div class="header">
        <div class="joiv-logo">
            <img src="{{ $data['joiv_logo_path'] }}" alt="JOIV Logo" style="height: 60px;" />
        </div>
        <div class="journal-name">
            INTERNATIONAL OURNAL ON INFORMATICS VISUALIZATION
        </div>
    </div>

    <div class="date">
        {{ $data['number_of_letter'] }}<br>
        {{ $data['issue_date'] }}
    </div>

    <div class="recipient">
        Dear {{ $data['participant_name'] }}<br>
        {{ $data['institution'] }}
    </div>

    <div class="subject">
        RE: JOURNAL ACCEPTANCE LETTER
    </div>

    <div class="content">
        We are happy to inform you that the International Journal on Informatics Visualization
        (JOIV) has been indexed in Scopus. The Scientific committee of JOIV agrees that the
        following manuscript is <strong>accepted</strong> for publication in JOIV
        <strong>{{ $data['joiv_volume'] }}</strong>
        <table class="manuscript-table">
            <tr>
                <td class="label">Title</td>
                <td>{{ $data['paper_title'] }}</td>
            </tr>
            <tr>
                <td class="label">Authors</td>
                <td>{{ $data['authors'] }}</td>
            </tr>
        </table>

        <p>
            Thank you for your contribution the International Journal on Informatics Visualization
            (JOIV) and we look forward to receiving further submissions from you.
        </p>
    </div>

    <div class="signature-section clearfix">
        <div class="signature-left">
            <p style="margin: 0;">Sincerely</p>
            <img src="{{ $data['signature_path'] }}" alt="Editor Signature" style="width: 220px; margin-top: 20px;" />
        </div>

        <div class="signature-right">
            <img src="{{ $data['scopus_analitic_path'] }}" alt="Scopus Analytic" style="width: 180px;" />
        </div>
    </div>

    <div class="footer-logo">
        <img src="{{ $data['sotvi_logo_path'] }}" alt="SOTVI Logo" style="height: 40px;" />
    </div>
</body>

</html>
