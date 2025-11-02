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
            margin-bottom: 5px;
            font-family: Arial, sans-serif;
        }

        .joiv-logo .orange {
            color: #ff6b35;
        }

        .journal-name {
            background-color: #ffa500;
            color: white;
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
            font-weight: bold;
            margin: 20px 0;
            font-size: 11pt;
        }

        .content {
            text-align: justify;
            margin: 20px 0;
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
            margin-top: 60px;
        }

        .signature-left {
            float: left;
            width: 50%;
        }

        .signature-right {
            float: right;
            width: 45%;
            text-align: center;
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
            J<span class="orange">o</span>iv
        </div>
        <div class="journal-name">
            INTERNATIONAL JOURNAL ON INFORMATICS VISUALIZATION
        </div>
    </div>

    <div class="date">
        {{ $data['issue_date'] }}
    </div>

    <div class="recipient">
        Dear {{ $data['participant_name'] }}<br><br>
        {{ $data['institution'] }}
    </div>

    <div class="subject">
        <strong>RE: JOURNAL ACCEPTANCE LETTER</strong>
    </div>

    <div class="content">
        <p>
            We are happy to inform you that the International Journal on Informatics Visualization
            (JOIV) has been indexed in Scopus. The Scientific committee of JOIV agrees that the
            following manuscript is <strong>accepted</strong> for publication in JOIV
            <strong>{{ $data['joiv_volume'] ?? 'Vol.10 No.6 November 2026' }}</strong>
        </p>

        <table class="manuscript-table">
            <tr>
                <td class="label">Title</td>
                <td>{{ $data['paper_title'] }}</td>
            </tr>
            <tr>
                <td class="label">Authors</td>
                <td>{{ $data['authors'] ?? $data['participant_name'] }}</td>
            </tr>
        </table>

        <p>
            Thank you for your contribution the International Journal on Informatics Visualization
            (JOIV) and we look forward to receiving further submissions from you.
        </p>
    </div>

    <div class="signature-section clearfix">
        <div class="signature-left">
            <p>Sincerely</p>
            <div class="signature-box">
                <div class="signature-line"></div>
                <strong>Rahmat Hidayat</strong><br>
                Editor in Chief<br>
                International Journal on Informatics<br>
                Visualization<br>
                <div class="contact-info">
                    <strong>http://joiv.org</strong>
                </div>
            </div>
        </div>

        <div class="signature-right">
            <div class="scopus-info">
                <div style="border: 1px solid #ccc; padding: 10px; margin: 10px 0;">
                    <div style="font-size: 10pt; margin-bottom: 5px;">International Journal on</div>
                    <div style="font-size: 10pt; font-weight: bold;">Informatics Visualization</div>
                    <div class="scopus-badge">Q3</div>
                    <div style="font-size: 8pt; color: #666;">Information<br>Systems and<br>Management<br>best quartile
                    </div>
                    <div style="font-size: 8pt; color: #666; margin-top: 10px;">SJR 2024</div>
                    <div style="font-size: 12pt; font-weight: bold;">0.2</div>
                    <div style="background-color: #ff6b35; height: 10px; width: 50%; margin: 5px auto;"></div>
                    <div style="font-size: 8pt; color: #666;">powered by scimagojr.com</div>
                </div>
                <div class="citation-score">1.9</div>
                <div style="font-size: 10pt; color: #666;">2024<br>CiteScore</div>
                <div style="font-size: 8pt; color: #666; margin-top: 5px;">43rd percentile</div>
                <div style="font-size: 8pt; color: #666;">Powered by <span style="color: #ff6b35;">Scopus</span></div>
            </div>
        </div>
    </div>

    <div class="footer-logo">
        <div class="sotvi-logo">
            SOTVI
        </div>
        <div style="font-size: 8pt; color: #666;">Society of Visual Informatics</div>
    </div>
</body>

</html>
