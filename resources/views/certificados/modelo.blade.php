@php
    $bg = $evento->certificado_bg_path ? asset('storage/'.$evento->certificado_bg_path) : null;
    $x  = $evento->certificado_nome_x ?? 400;
    $y  = $evento->certificado_nome_y ?? 300;
    $fs = $evento->certificado_font_size ?? 48;
    $color = $evento->certificado_text_color ?? '#000000';
@endphp
<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="utf-8">
<title>Certificado</title>
<style>
  @page { margin: 0; }
  body { margin: 0; font-family: DejaVu Sans, sans-serif; }
  .wrap {
    position: relative;
    width: 297mm;  /* A4 landscape */
    height: 210mm;
  }
  .bg {
    position: absolute; left:0; top:0; width:100%; height:100%;
    background: {{ $bg ? "url('".$bg."')" : '#fff' }} no-repeat center center;
    background-size: cover;
  }
  .nome {
    position: absolute;
    left: {{ (int)$x }}px;
    top:  {{ (int)$y }}px;
    font-size: {{ (int)$fs }}px;
    color: {{ $color }};
    font-weight: 700;
  }
</style>
</head>
<body>
  <div class="wrap">
    <div class="bg"></div>
    <div class="nome">{{ $nome }}</div>
  </div>
</body>
</html>
