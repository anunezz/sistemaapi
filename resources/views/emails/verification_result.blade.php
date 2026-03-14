@php
    $timezone = config('app.timezone');
    config(['app.timezone' => 'America/Mexico_City']);
@endphp
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>IMPEDIMENTOS</title>
    <style type="text/css">
        .header{ background:#A42341; padding:10px; }
        .cintillo{ background:#b69262; padding:2px; }
        .cuerpo{ background:#f9f9f9; padding:50px; }
        .contenido{ background:#f9f9f9; padding:10px; }
        .footer{ background:#f9f9f9; }
        .break-word{ word-break: break-all; }
        p{ padding: 2px; }
        .container { overflow: auto; }
        .left { float:left; margin-right:10px; }
        .right { float:right; margin-left:10px; }
    </style>
</head>
<body>
<div class="header">
    <div class="container" style="padding:0px;">
        <img class="left" width="35%" src="{{ $message->embed(public_path().'/img/relaciones_header.jpeg') }}" alt="relaciones_header">
    </div>
</div>

<div class="cuerpo">
    <div class="contenido">
        
        <div style="text-align:center;">
            <h2 style="font-size:18px; margin:0 0 8px;">{{ $title }}</h2>
            <p style="margin:0;">{{ $line }}</p>
        </div>

        <div style="margin-top:20px; text-align:justify;">
            {!! $cuerpo_correo !!}
        </div>
        
    </div>

    <div class="footer">
        <br>
        <div style="text-align:center; margin: 2%;">
            <img src="{{ $message->embed(public_path('img/relaciones_footer.jpg')) }}" width="27%">
        </div>
    </div>
</div>
</body>
</html>
