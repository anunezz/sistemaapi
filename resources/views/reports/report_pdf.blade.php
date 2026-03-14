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

        /* ---- FIX PARA QUE NO SE DESFASE ---- */
        .table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            table-layout: fixed; /* NECESARIO EN DOMPDF */
        }

        .table th, .table td {
            border: 1px solid #ddd;
            padding: 6px;
            text-align: left;
            word-break: break-word; /* Corta palabras largas */
            white-space: normal;
            line-height: 1.2; /* Evita salto exagerado de línea */
        }

        /* Columnas cortas: ocupan lo mínimo */
        .col-id { width: 40px; }
        .col-ofi { width: 80px; }
        .col-fecha { width: 90px; }
        .col-motivo { width: 90px; }

        /* Observaciones toma el espacio sobrante */
        .col-obs { width: auto; }

        h2 {
            margin: 4px 0;
        }
    </style>
</head>

<body>
    {{-- Encabezado del PDF --}}
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
                <th class="col-id">ID Solicitud</th>
                <th>Nombre</th>
                <th class="col-ofi">Oficina</th>
                <th class="col-fecha">Fecha Alta</th>
                <th class="col-obs">Observaciones</th>
                <th class="col-motivo">Motivo</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($report as $item)
                <tr>
                    <td class="col-id">{{ $item->id_solicitud ?? '' }}</td>
                    <td>{{ $item->full_name ?? '' }}</td>
                    <td class="col-ofi">{{ optional($item->cat_office)->nombre_corto ?? '' }}</td>
                    <td class="col-fecha">
                        @php
                            try {
                                echo \Carbon\Carbon::parse($item->created_at)->format('d/m/Y H:i');
                            } catch (\Exception $e) {
                                echo $item->created_at;
                            }
                        @endphp
                    </td>

                    <td class="col-obs">{{ $item->observaciones ?? '' }}</td>
                    <td class="col-motivo">{{ optional($item->cat_causal_impedimento)->causal_impedimento ?? '' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" style="text-align:center;">Sin resultados disponibles</td>
                </tr>
            @endforelse
        </tbody>
    </table>

</body>
</html>
