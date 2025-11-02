<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Certificate - {{ $participant_name }}</title>
    <style>
        @page {
            margin: 0;
            size: A4 landscape;
        }

        body {
            margin: 0;
            padding: 0;
            font-family: 'Arial', sans-serif;
            position: relative;
            width: 297mm;
            height: 210mm;
            overflow: hidden;
        }

        .certificate-container {
            position: relative;
            width: 100%;
            height: 100%;
            background: url("data:{{ $template_mime }};base64,{{ $template_base64 }}") no-repeat center center;
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
        }

        .participant-name {
            position: absolute;
            left: {{ ($positions['name']['x'] / 838) * 100 }}%;
            top: {{ ($positions['name']['y'] / 595) * 100 }}%;
            font-size: {{ $positions['name']['size'] }}px;
            color: {{ $positions['name']['color'] }};
            text-align: {{ $positions['name']['align'] }};
            width: {{ ($positions['name']['width'] / 838) * 100 }}%;
            transform: translate(-50%, -50%);
            font-weight: bold;
            text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.1);
            font-family: Arial, sans-serif;
        }

        .paper-title {
            position: absolute;
            left: {{ ($positions['paper_title']['x'] / 838) * 100 }}%;
            top: {{ ($positions['paper_title']['y'] / 595) * 100 }}%;
            font-size: {{ $positions['paper_title']['size'] }}px;
            color: {{ $positions['paper_title']['color'] }};
            text-align: {{ $positions['paper_title']['align'] }};
            width: {{ ($positions['paper_title']['width'] / 838) * 100 }}%;
            transform: translate(-50%, -50%);
            text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.1);
            font-family: Arial, sans-serif;
        }
    </style>
</head>

<body>
    <div class="certificate-container">
        <!-- Text Overlays -->
        <div class="participant-name">
            {{ strtoupper($participant_name) }}
        </div>

        <div class="paper-title">
            {{ $paper_title }}
        </div>
    </div>
</body>

</html>
