<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <link rel="shortcut icon" href="{{ asset('favicon.ico') }}" />
    <title>IMPEDIMENTOS-API</title>
    <link href="https://framework-gb.cdn.gob.mx/assets/styles/main.css" rel="stylesheet">
</head>

<body>
<div class="container-gbmx">
    <div class="info-box">
        <h3>IMPEDIMENTOS-API</h3>
        <small><b>V {{\Config::get('app.version')}}</b></small>
    </div>
</div>
<script src="https://framework-gb.cdn.gob.mx/gobmx.js"></script>
</body>

</html>
<style>
    .container-gbmx {
        display: flex;
        justify-content: center;
        align-items: center;
        height: 50vh;
        background-color: #f3f4f6;
    }

    .info-box {
        text-align: center;
        padding: 20px;
        background: #ffffff;
        border-radius: 8px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }
</style>
