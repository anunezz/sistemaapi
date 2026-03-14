<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte PDF</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
        }
        .table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .table th, .table td {
            border: 1px solid #ddd;
            padding: 6px;
            text-align: left;
        }
        h2 {
            margin: 4px 0;
        }
    </style>
</head>
<body>

    {{-- Encabezado --}}
    <table style="width: 100%;">
        <tr>
            <td colspan="2" style="text-align: center;">
                @php
                    $headerPath = public_path('img/relaciones_header.jpeg');
                @endphp
                @if (file_exists($headerPath))
                    <img src="{{ $headerPath }}" style="width: 100%; height: auto;">
                @endif
            </td>
        </tr>
        <tr>
            <td colspan="2" style="text-align: center;">
                <h2>Sistema de Impedimentos Administrativos y Judiciales</h2>
            </td>
        </tr>
    </table>

    <div class="header">
        <h2>Reporte Generado</h2>
        <p>Fecha de generación: {{ now()->format('d/m/Y H:i:s') }}</p>
    </div>

    {{-- Tabla principal --}}
    <table class="table">
        <thead>
            <tr>
                <th>Número de Impedimento</th>
                <th>Oficina</th>
                <th>Fecha Alta</th>
                <th>Motivo</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($report as $item)
                <tr>
                    <td>{{ $item->numero_impedimento ?? '' }}</td>
                    <td>{{ optional($item->cat_office)->nombre_corto ?? '' }}</td>
                    <td>
                        @php
                            try {
                                echo \Carbon\Carbon::parse($item->created_at)->format('d/m/Y H:i');
                            } catch (\Exception $e) {
                                echo $item->created_at; // muestra tal cual si no se puede parsear
                            }
                        @endphp
                    </td>

                    <td>{{ optional($item->cat_causal_impedimento)->causal_impedimento ?? '' }}</td>
                </tr>

                {{-- Subtabla de solicitudes --}}
                @if (!empty($item->requests) && $item->requests->count() > 0)
                    <tr>
                        <td colspan="4">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Solicitud</th>
                                        <th>Nombre Completo</th>
                                        <th>Tipo Solicitud</th>
                                        <th>Fecha Registro</th>
                                        <th>Estatus</th>
                                        <th>Observaciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($item->requests as $req)
                                        <tr>
                                            <td>{{ $req->id_solicitud ?? '' }}</td>
                                            <td>{{ $req->full_name ?? '' }}</td>
                                            <td>{{ optional($req->cat_type)->tipo_solicitud ?? '' }}</td>
                                            <td>{{ $req->fecha_registro ?? '' }}</td>
                                            <td>{{ optional($req->cat_status)->estatus_solicitud ?? '' }}</td>
                                            <td>{{ $req->observaciones ?? '' }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </td>
                    </tr>
                @endif
            @empty
                <tr>
                    <td colspan="4" style="text-align:center;">Sin resultados</td>
                </tr>
            @endforelse
        </tbody>
    </table>

</body>
</html>
