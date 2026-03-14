@php
use Carbon\Carbon;
$timezone = config('app.timezone');
config(['app.timezone' => 'America/Mexico_City']);
@endphp
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <title>IMPEDIMENTOS</title>
    <style type="text/css">
        .header {
            background: #A42341;
            padding: 10px;
        }

        .cintillo {
            background: #b69262;
            padding: 2px;
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
        }

        .container {
            overflow: auto;
            /* Para contener correctamente las imágenes */
        }

        .left {
            float: left;
            margin-right: 10px;
            /* Espacio entre la imagen y el contenido adyacente */
        }

        .right {
            float: right;
            margin-left: 10px;
            /* Espacio entre la imagen y el contenido adyacente */
        }

        a, a:active {
            background-color: transparent;
            /* Fondo transparente */
            color: rgb(105, 28, 50) !important;
            /* Color del texto */
            border: 1px solid rgb(105, 28, 50);
            /* Borde de 1px sólido con el mismo color */
            padding: 8px 15px;
            /* Espaciado interno para que el borde no esté pegado al texto */
            text-decoration: none;
            /* Elimina el subrayado predeterminado de los enlaces */
            display: inline-block;
            /* Permite aplicar padding y border correctamente */
        }

        /* Opcional: Estilo al pasar el mouse por encima (hover) */
        a:hover {
            background-color: rgb(105, 28, 50);
            /* Cambia el fondo al color del texto */
            color: white !important;
            /* Cambia el texto a blanco */
            cursor: pointer;
            /* Indica que es un elemento interactivo */
        }
    </style>

</head>

<body>

    <div class="header">
        <div class="container" style="padding:0px;">
            <img class="left" width="35%" src="{{ $message->embed(public_path().'/img/relaciones_header.jpeg') }}" alt="relaciones_header">
        </div>
    </div>
    <div class="cuerpo">
        <div class="contenido" style="text-align: center;">
            <p>Estimado(a), <b>{{ $user->full_name ?? '' }}</b></p>
            <br />
            <p>Tu contraseña generada es: <b>{{$password}}</b></p>
            <br />
            <p>Para <b>Iniciar Sesión</b> da clic en el siguiente botón</p>
            <a href="{{$loginFront}}" target="_blank" rel="noopener noreferrer">Iniciar Sesión</a>
        </div>

        <div class="footer">
            <br>
            <div style="text-align:center; margin: 2%;">
                <img src="{{ $message->embed( public_path('img/relaciones_footer.jpg')) }}" width="27%">
            </div>
        </div>
    </div>
</body>

</html>
