@php
    use Carbon\Carbon;
    $timezone = config('app.timezone');
    config(['app.timezone' => 'America/Mexico_City']);
@endphp
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Restablecer Contraseña</title>
    <style type="text/css">
        .header {
            background: #A42341;
            padding: 10px;
        }

        .cuerpo {
            background: #f9f9f9;
            padding: 50px;
        }

        .contenido {
            background: #f9f9f9;
            padding: 10px;
        }

        .footer {
            background: #f9f9f9;
        }

        .break-word {
            word-break: break-all;
        }

        p {
            padding: 2px;
            font-size: 15px;
            color: #333;
        }

        .container {
            overflow: auto;
        }

        .left {
            float: left;
            margin-right: 10px;
        }

        .right {
            float: right;
            margin-left: 10px;
        }

        a.boton {
            background-color: #800000;
            border: 1px solid #800000;
            padding: 12px 24px;
            font-size: 16px;
            line-height: 1.5;
            color: #ffffff;
            text-decoration: none;
            display: inline-block;
            border-radius: 5px;
            font-weight: bold;
        }

        .centrado {
            text-align: center;
            margin: 20px 0;
        }
    </style>
</head>
<body>

<div class="header">
    <div class="container" style="padding:0px;">
        <img class="left" width="35%" src="{{ $message->embed(public_path().'/images/relaciones_header.jpeg') }}" alt="relaciones_header">
    </div>
</div>

<div class="cuerpo">
    <div class="contenido">
        <p>Estimado(a)</p>
        <p><b>{{ $user->name ?? 'Usuario' }}</b></p>

        <p>Recibiste este correo porque solicitaste restablecer tu contraseña.</p>

        <div class="centrado">
            <table role="presentation" border="0" cellpadding="0" cellspacing="0" style="margin: auto;">
                <tr>
                    <td align="center">
                        <a href="{{ $url }}" target="_blank" class="boton">
                            Restablecer Contraseña
                        </a>
                    </td>
                </tr>
            </table>
        </div>

        <p>Si no realizaste esta solicitud, puedes ignorar este mensaje.</p>

        <br>

        <p style="font-weight: bold;">Gracias,<br>IMPIDIMENTOS</p>

        <hr style="margin-top: 40px; border: none; border-top: 1px solid #dddddd;">

        <p style="font-size: 13px; color: #888888;">
            Si tienes problemas para hacer clic en el botón, copia y pega esta URL en tu navegador:<br>
            <a href="{{ $url }}" style="color: #800000;" class="break-word">{{ $url }}</a>
        </p>
    </div>

    <div class="footer">
        <br>
        <div style="text-align:center; margin: 2%;">
            <img src="{{ $message->embed(public_path('images/relaciones_footer.jpeg')) }}" width="27%">
        </div>
    </div>
</div>

</body>
</html>
