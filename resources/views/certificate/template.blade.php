<!DOCTYPE html>
<html>

<head>
  <meta charset="UTF-8">
  <style>
    @page {
      size: A4 landscape;
      margin: 0cm;
    }

    body {
      margin: 0;
      padding: 0;
      font-family: DejaVu Sans, sans-serif;
      width: 297mm;
      height: 210mm;
    }

    .certificate {
      position: relative;
      width: 297mm;
      /* A4 landscape width */
      height: 210mm;
      /* A4 landscape height */
      background: url("{{ $background }}") no-repeat center center;
      background-size: cover;
      overflow: hidden;
    }

    .field {
      position: absolute;
      font-weight: bold;
      word-wrap: break-word;
      overflow-wrap: break-word;
      white-space: nowrap;
      transform: translateX(-50%);
      text-align: center
    }

    /* .text-center { text-align: center; } */
    .text-left {
      text-align: left;
    }

    .text-right {
      text-align: right;
    }
  </style>
</head>

<body>
  <div class="certificate">
    @foreach ($layout as $field => $style)
      @php
        $fontSizeMm = ($style['size'] ?? 24) * 0.26;
      @endphp

      <div class="field"
        style="left: {{ $style['x'] }}; 
                        top: {{ $style['y'] }}; 
                        font-size: {{ $fontSizeMm }}mm; 
                        background: 'red';
                        text-align: 'center';
                        width: {{ $style['width'] ?? 400 }}px;
                        height: auto;
                        white-space: normal;
                        overflow-wrap: anywhere;
                        word-break: break-word;
                        color: {{ $style['color'] ?? '#000000' }};">
        {{ $data[$field] ?? '' }}
      </div>
    @endforeach
  </div>
</body>

</html>
