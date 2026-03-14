<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>
    @if($ImSolicitud->id_tipo_solicitud == 1)
          SOLICITUD DE ALTA DE IMPEDIMENTO
      @elseif($ImSolicitud->id_tipo_solicitud == 2)
          SOLICITUD DE BAJA DE IMPEDIMENTO
      @elseif($ImSolicitud->id_tipo_solicitud == 3)
          SOLICITUD DE VERIFICACIÓN POR ALERTA DE IMPEDIMENTO
      @endif
  </title>
  <style>
    @page { margin: 24px 28px; }
    body { font-family: DejaVu Sans, Arial, Helvetica, sans-serif; font-size: 11px; color:#000; }

    /* Paleta del diseño */
    :root {
      --vino: #631634;
      --borde: #000000;
    }

    table { width: 100%; border-collapse: collapse;  table-layout: fixed;}
    td {
      vertical-align: top;
      word-wrap: break-word;
      overflow-wrap: break-word;
      white-space: normal;
    }
    .mb-8{ margin-bottom: 8px; }
    .mb-12{ margin-bottom: 12px; }

    .title-main{
      text-align:center; font-weight:bold; font-size:16px; margin:0 0 10px 0;
    }

    .header td{ vertical-align: top; }
    .logo-cell{ width:48%; }
    .logo{ height:58px; }
    .top-right{ width:52%; font-size:11px; }
    .line {
      border-bottom: 1px solid #000;
      min-height: 16px;            /* mantiene apariencia inicial */
      padding: 2px 0;              /* evita fixed height */
      display: block;              /* importante para que el contenido ocupe línea completa */
      white-space: normal;         /* permite wrap */
      overflow-wrap: break-word;   /* rompe palabras largas */
      word-break: break-word;      /* garantiza ruptura en algunos motores */
      line-height: 1.1;
      vertical-align: top;
    }


    .bar{ background:var(--vino); color:#fff; font-weight:bold; font-size:12px; padding:4px 6px; text-align:center; }

    .row td{ padding:0;}
    .label{ width:28%; padding:6px 8px; white-space:nowrap; }
    .box{ width:72%; border:1px solid var(--borde); height:20px; padding:4px 6px; }

    .box-sm { width: 35%; border:1px solid #000; height:20px; padding:4px 6px; }
    .box-md { width: 55%; border:1px solid #000; height:20px; padding:4px 6px; }


    .tri .label{ width:28%; }
    .tri .box{ width:72%; }

    .box-lg{ border:1px solid var(--borde); height:150px; padding:8px; vertical-align:top; line-height:1.35; }

    .subtitle {
      font-weight: 700;
    }

.box-inline {
  border:1px solid #000;
  min-height:20px;
  padding:4px 6px;
  /* display:block;        */
  width:auto;           /* Se expande */
  max-width:100%;       /* No se sale del contenedor */
  white-space: normal;
  word-break: break-word;
}




.box-check{
  display:inline-block; width:12px; height:12px;
  border:1px solid #000; margin-right:6px;
  vertical-align:middle; text-align:center; line-height:12px; font-size:10px;
}
  .input-inline {
    display: inline-block;
    min-width: 120px;
    min-height: 18px;
    padding: 2px 6px;
    margin-left: 4px;
    border: 1px solid #000;        /* ← ahora caja */
    line-height: 1.2;
    font-size: 12px;
    white-space: nowrap;           /* En una línea */
    overflow: hidden;
    text-overflow: ellipsis;
    vertical-align: middle;
  }


  .input-block {
    display: block;
    width: 100%;
    min-height: 22px;
    padding: 4px 0;
    margin-top: 4px;
    border: 1px solid #000;        /* ← caja visible */
    font-size: 12px;
    line-height: 1.3;
    white-space: normal;           /* Permite saltos de línea */
    overflow-wrap: break-word;
    word-break: break-word;
    box-sizing: border-box;
  }
.bar-gray{
  background:#d9d9d9; color:#000; font-weight:bold; font-size:12px;
  padding:4px 6px; text-align:center;
}
.sig-line{ border-top:1px solid #000; height:40px;  margin-top: 12px;}
.sig-caption{ font-size:10px; text-align:center; font-weight:bold; }

  </style>
</head>
<body>
@php
  // Derivados que ya usabas
  $oficina   = $ImSolicitud->nombre_dependencia ?: optional($ImSolicitud->cat_office)->cad_oficina ?: '';

  try {
    $fechaSolicitud = $ImSolicitud->fecha_registro
      ? \Carbon\Carbon::parse($ImSolicitud->fecha_registro)->format('d/m/Y')
      : (\Carbon\Carbon::parse($ImSolicitud->created_at ?? null)->format('d/m/Y') ?? '');
  } catch (\Exception $e) { $fechaSolicitud = $ImSolicitud->fecha_registro ?: ''; }

  $noCorreo = $ImSolicitud->numero_documento ?: '';
@endphp


  <h1 class="title-main">
    @if($ImSolicitud->id_tipo_solicitud == 1)
          SOLICITUD DE ALTA DE IMPEDIMENTO
      @elseif($ImSolicitud->id_tipo_solicitud == 2)
          SOLICITUD DE BAJA DE IMPEDIMENTO
      @elseif($ImSolicitud->id_tipo_solicitud == 3)
          SOLICITUD DE VERIFICACIÓN POR ALERTA DE IMPEDIMENTO
          @else
          SOLICITUD DE ALTA DE IMPEDIMENTO
          @endif
  </h1>


  <table class="header mb-12">
    <tr>
      <td class="logo-cell">
        <img src="{{ public_path('images/logoSRE2.jpg') }}" alt="Escudo" style="height: 80px;">
      </td>
      <td class="top-right">
        <table>
          <tr>
            <td style="text-align:right; padding-right:6px; width:38%;">FECHA:</td>
            <td><div class="line">{{ $fechaSolicitud }}</div></td>
          </tr>
          <tr>
            <td style="text-align:right; padding-right:6px; vertical-align: top;">OFICINA:</td>
            <td style="vertical-align: top;">
              <div class="line" style="display:block; white-space:normal; overflow-wrap:break-word; word-break:break-word;">
                {{ $oficina }}
              </div>
            </td>
          </tr>
          <tr>
            <td style="text-align:right; padding-right:6px;">No. DE CORREO:</td>
            <td><div class="line">{{ $noCorreo }}</div></td>
          </tr>
        </table>
      </td>
    </tr>
  </table>

  {{-- DATOS DE LA PERSONA --}}
  <div class="bar">DATOS DE LA PERSONA</div>
  <table class="mb-12">
    <tr class="row">
      <td class="label">NOMBRE(S)</td>
      <td class="box">{{ $ImSolicitud->nombres ?? '' }}</td>
    </tr>
    <tr class="row">
      <td class="label">APELLIDO PATERNO</td>
      <td class="box">{{ $ImSolicitud->primer_apellido ?? '' }}</td>
    </tr>
    <tr class="row">
      <td class="label">APELLIDO MATERNO</td>
      <td class="box">{{ $ImSolicitud->segundo_apellido ?? '' }}</td>
    </tr>
    <tr class="row">
      <td class="label">FECHA DE NACIMIENTO</td>
      <td class="box-inline">
        @php
          try {
            echo $ImSolicitud->fecha_nacimiento
              ? \Carbon\Carbon::parse($ImSolicitud->fecha_nacimiento)->format('d/m/Y')
              : '';
          } catch (\Exception $e) { echo $ImSolicitud->fecha_nacimiento ?: ''; }
        @endphp
      </td>
    </tr>
    <tr class="row">
      <td class="label">LUGAR DE NACIMIENTO</td>
      <td class="box-inline">
        {{ $ImSolicitud->entidad_federativa_nacimiento ?? '' }}
      </td>
    </tr>
  </table>


  <div class="bar">SI APLICA:</div>

  {{-- DATOS DEL PADRE --}}
  <table class="mb-12">
    <tr><td colspan="2" class="subtitle">DATOS DEL PADRE</td></tr>
    <tr class="row tri">
      <td class="label">NOMBRE(S)</td>
      <td class="box">{{ $ImSolicitud->nombres_padre ?? '' }}</td>
    </tr>
    <tr class="row tri">
      <td class="label">APELLIDO PATERNO</td>
      <td class="box">{{ $ImSolicitud->primer_apellido_padre ?? '' }}</td>
    </tr>
    <tr class="row tri">
      <td class="label">APELLIDO MATERNO</td>
      <td class="box">{{ $ImSolicitud->segundo_apellido_padre ?? '' }}</td>
    </tr>
  </table>

  {{-- DATOS DE LA MADRE --}}
  <table class="mb-12">
    <tr><td colspan="2" class="subtitle">DATOS DE LA MADRE</td></tr>
    <tr class="row tri">
      <td class="label">NOMBRE(S)</td>
      <td class="box">{{ $ImSolicitud->nombres_madre ?? '' }}</td>
    </tr>
    <tr class="row tri">
      <td class="label">APELLIDO PATERNO</td>
      <td class="box">{{ $ImSolicitud->primer_apellido_madre ?? '' }}</td>
    </tr>
    <tr class="row tri">
      <td class="label">APELLIDO MATERNO</td>
      <td class="box">{{ $ImSolicitud->segundo_apellido_madre ?? '' }}</td>
    </tr>
  </table>

  {{-- MOTIVACIÓN DEL ACTO JURÍDICO --}}
  <div class="bar">MOTIVACIÓN DEL ACTO JURÍDICO</div>
  <table class="mb-12">
    <tr>
      <td class="box-lg">
          @if (!empty(trim($ImSolicitud->motivacion_acto_juridico ?? '')))
              {!! html_entity_decode($ImSolicitud->motivacion_acto_juridico) !!}
          @endif
      </td>

    </tr>
  </table>

  {{-- NUEVA IDENTIDAD  --}}
  @if($ImSolicitud->id_tipo_solicitud == 4)
  <div class="bar">IDENTIDAD A LA QUE SE SOLICITA CREAR UN IMPEDIMENTO</div>
  <table class="mb-12">
    <tr><td colspan="2" class="subtitle">DATOS NUEVA IDENTIDAD</td></tr>
    <tr class="row">
      <td class="label">CURP</td>
      <td class="box">{{ $ImSolicitud->curp_identidad ?? '' }}</td>
    </tr>
    <tr class="row">
      <td class="label">NOMBRE(S)</td>
      <td class="box">{{ $ImSolicitud->nombres_identidad ?? '' }}</td>
    </tr>
    <tr class="row">
      <td class="label">APELLIDO PATERNO</td>
      <td class="box">{{ $ImSolicitud->primer_apellido_identidad ?? '' }}</td>
    </tr>
    <tr class="row">
      <td class="label">APELLIDO MATERNO</td>
      <td class="box">{{ $ImSolicitud->segundo_apellido_identidad ?? '' }}</td>
    </tr>
  </table>
  @endif
{{-- ANEXOS --}}
<div class="bar">ANEXOS</div>
  <table class="mb-12" style="border:1px solid #000;">
  @php
      // Helper para checar si existe un doc con cierto id_cat_anexos
      $has = fn(int $n) => $ImSolicitud->documents && $ImSolicitud->documents->contains('id_cat_anexos', $n);
  @endphp
    @if (in_array($ImSolicitud->id_tipo_solicitud, [1]))
      <tr>
        <td style="padding:6px; width:60%;">
          <span class="box-check">@if($has(1)) ✕ @endif</span>
          EXPEDIENTE INTEGRADO POR LA DELEGACIÓN
        </td>

        <td style="padding:6px; width:40%;">
          PASAPORTE CANCELADO NÚMERO
          <span class="input-inline">
            {{ optional($ImSolicitud->documents->firstWhere('id_cat_anexos', 3))->observaciones }}
          </span>
          <span class="box-check" style="margin-left:8px;">@if($has(3)) ✕ @endif</span>
        </td>
      </tr>

      <tr>
        <td colspan="2" style="padding:6px;">
          <span class="box-check">@if($has(2)) ✕ @endif</span>
          DICTAMEN DE VERIFICACIÓN POR LA AUTORIDAD(ES) COMPETENTE(S)
        </td>
      </tr>
      <tr>
        <td colspan="2" style="padding:6px;">
          <span class="box-check">@if($has(9)) ✕ @endif</span>
          PROBATORIO DE IDENTIDAD
        </td>
      </tr>
      <tr>
        <td colspan="2" style="padding:6px;">
          <span class="box-check">@if($has(12)) ✕ @endif</span>
          FOTO
        </td>
      </tr>
      <tr>
        <td colspan="2" style="padding:6px;">
          <span class="box-check">@if($has(13)) ✕ @endif</span>
          FORMATO DE SOLICITUD
        </td>
      </tr>

      <tr>
        <td colspan="2" style="padding:6px; vertical-align: top;">
          <span class="box-check">@if($has(4)) ✕ @endif</span>
          OTRO, ESPECIFICAR:
          <div class="input-block">
            {{ optional($ImSolicitud->documents->firstWhere('id_cat_anexos', 4))->observaciones }}
          </div>
        </td>
      </tr>


  @elseif(in_array($ImSolicitud->id_tipo_solicitud, [3]))
      <tr>
        <td style="padding:6px; width:60%;">
          <span class="box-check">@if($has(1)) ✕ @endif</span>
          EXPEDIENTE INTEGRADO POR LA DELEGACIÓN
        </td>
      </tr>
      <tr>
        <td colspan="2" style="padding:6px;">
          <span class="box-check">@if($has(12)) ✕ @endif</span>
          FOTO
        </td>
      </tr>
      <tr>
        <td colspan="2" style="padding:6px;">
          <span class="box-check">@if($has(13)) ✕ @endif</span>
          FORMATO DE SOLICITUD
        </td>
      </tr>

      <tr>
        <td colspan="2" style="padding:6px; vertical-align: top;">
          <span class="box-check">@if($has(4)) ✕ @endif</span>
          OTRO, ESPECIFICAR:
          <div class="input-block">
            {{ optional($ImSolicitud->documents->firstWhere('id_cat_anexos', 4))->observaciones }}
          </div>
        </td>
      </tr>

  @elseif (in_array($ImSolicitud->id_tipo_solicitud, haystack: [2]))
      <tr>
        <td style="padding:6px; width:60%;">
          <span class="box-check">@if($has(2)) ✕ @endif</span>
          DICTAMEN DE VERIFICACION POR AUTORIDADES(ES) COMPETENTE(S)
        </td>
        <td style="padding:6px; width:40%;">
          <span class="box-check">@if($has(5)) ✕ @endif</span>
          EN SU CASO, DOCUMENTACION COMPLEMENTARIA PRESENTADA
        </td>
      </tr>
      <tr>
        <td style="padding:6px; width:60%;">
          <span class="box-check">@if($has(7)) ✕ @endif</span>
          EN SU CASO, DENUNCIA PENAL RATIFICADA (POR SUPLANTACION DE IDENTIDAD)
        </td>
      </tr>
      <tr>
        <td style="padding:6px; width:60%;">
          <span class="box-check">@if($has(6)) ✕ @endif</span>
          EN SU CASO, RESOLUCION JUDICIAL
        </td>
        <td style="padding:6px; width:40%;">
          <span class="box-check">@if($has(1)) ✕ @endif</span>
          EXPEDIENTE INTEGRADO POR LA DELEGACIÓN
        </td>
      </tr>

      <tr>
        <td style="padding:6px; width:40%;">
          <span class="box-check">@if($has(12)) ✕ @endif</span>
          FOTO
        </td>
        <td style="padding:6px; width:60%;">
          <span class="box-check">@if($has(13)) ✕ @endif</span>
          FORMATO DE SOLICITUD
        </td>
      </tr>

      <tr>
        <td colspan="2" style="padding:6px; vertical-align: top;">
          <span class="box-check">@if($has(4)) ✕ @endif</span>
          OTRO, ESPECIFICAR:
          <div class="input-block">
            {{ optional($ImSolicitud->documents->firstWhere('id_cat_anexos', 4))->observaciones }}
          </div>
        </td>
      </tr>

  @elseif (in_array($ImSolicitud->id_tipo_solicitud, [4]))
      <tr>
      <td style="width:50%; padding:4px;">
        <span class="box-check">@if($has(8)) ✕ @endif</span>
        CURP ACTUALIZADA
      </td>
      <td style="width:50%; padding:4px;">
        <span class="box-check">@if($has(13)) ✕ @endif</span>
        FORMATO DE SOLICITUD
      </td>
    </tr>
    <tr>
      <td style="padding:4px;">
        <span class="box-check">@if($has(12)) ✕ @endif</span>
        FOTO
      </td>
      <td style="padding:4px;">
        <span class="box-check">@if($has(11)) ✕ @endif</span>
        PASAPORTE ANTERIOR
      </td>
    </tr>
    <tr>
      <td style="padding:4px;">
        <span class="box-check">@if($has(9)) ✕ @endif</span>
        PROBATORIO DE IDENTIDAD
      </td>
      <td style="padding:4px;">
        <span class="box-check">@if($has(10)) ✕ @endif</span>
        PROBATORIO DE NACIONALIDAD
      </td>
    </tr>

  @endif

  </table>

<div class="section-autorizacion" style="page-break-inside: avoid; margin-top: 10px;">

  {{-- AUTORIZACIÓN DE LA SOLICITUD --}}
  <div class="bar" style="
        margin-top:4px;
        margin-bottom:4px;
        padding:4px 0;
        font-size:11px;
        background-color:#6e0023;
        color:#fff;
        text-align:center;
        font-weight:bold;">
    AUTORIZACIÓN DE LA SOLICITUD
  </div>

  <table style="width:100%; border-collapse:collapse; margin:0; margin-top: 30px; padding:0; font-size:10px;">
    <tr>
      <td style="width:50%; padding:6px 10px; vertical-align:top;">
        <div class="sig-line" style="border-top:1px solid #000; height:30px; margin-bottom:2px;"></div>
        <div class="sig-caption" style="font-size:9px; text-align:center;">
            ELABORO <br>
            @if( optional($ImSolicitud->cat_user_elaboro)->full_name )
                {{ optional($ImSolicitud->cat_user_elaboro)->full_name }} - {{ optional($ImSolicitud->cat_user_elaboro)->puesto }}
            @endif
        </div>
      </td>
      <td style="width:50%; padding:6px 10px; vertical-align:top;">
        <div class="sig-line" style="border-top:1px solid #000; height:30px; margin-bottom:2px;"></div>
        <div class="sig-caption" style="font-size:9px; text-align:center;">
            REVISO <br>
            @if( optional($ImSolicitud->cat_user_reviso)->full_name )
                {{ optional($ImSolicitud->cat_user_reviso)->full_name }} - {{ optional($ImSolicitud->cat_user_reviso)->puesto }}
            @endif
        </div>
      </td>
    </tr>
  </table>

</div>

</body>
</html>
