<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Reporte PDF</title>
    <style>
        @page {
            margin: 80px 40px 60px 40px;
        }

        body {
            font-family: DejaVu Sans, Arial, sans-serif;
            font-size: 11px;
            color: #333;
        }

        header {
            position: fixed;
            top: -60px;
            left: 0;
            right: 0;
            text-align: center;
        }

        footer {
            position: fixed;
            bottom: -40px;
            left: 0;
            right: 0;
            text-align: center;
            font-size: 10px;
            color: #777;
        }

        .header {
            text-align: center;
            margin-bottom: 15px;
        }

        h2 {
            margin: 5px 0;
        }

        .table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        .table th,
        .table td {
            border: 0.6px solid #999;
            padding: 6px 8px;
            text-align: left;
            vertical-align: middle;
        }

        .table th {
            background-color: #f2f2f2;
            font-weight: bold;
        }

        .title {
            text-align: center;
            font-size: 14px;
            margin-bottom: 20px;
        }
    </style>
</head>

<body>

    <header>
        <img src="{{ public_path('img/relaciones_header.jpeg') }}" style="width: 100%; height: auto;">
    </header>

    <footer>
        Página <span class="pagenum"></span>
    </footer>

    <div class="title">
        <h2>Sistema de Impedimentos Administrativos y Judiciales</h2>
    </div>

    <div class="header">
        <h3>Reporte Generado</h3>
        <p>Fecha de generación: {{ \Carbon\Carbon::now()->format('d/m/Y H:i:s') }}</p>
    </div>

    <table class="table">
        <thead>
    <tr>
        <th>Día</th>
        <th>Oficina</th>

        @if (!empty($filters->byuser))
            <th>Usuario</th>
        @endif

        <th>Alta</th>
        <th>Baja</th>
        <th>Verificación</th>
        <th>Modificación</th>
    </tr>
</thead>

        <tbody>
    @foreach ($report as $item)
        <tr>
            <td>{{ $item->dia }}</td>
            <td>{{ $item->nombre_corto }}</td>

            @if (!empty($filters->byuser))
                <td>{{ $item->usuario ?? '-' }}</td>
            @endif

            <td>{{ $item->alta }}</td>
            <td>{{ $item->baja }}</td>
            <td>{{ $item->verificacion }}</td>
            <td>{{ $item->modificacion }}</td>
        </tr>
    @endforeach
</tbody>
    </table>

</body>

</html>
