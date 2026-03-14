<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Boleta de Elaboración de Arraigo</title>
  <style>
    body {
      font-family: Arial, sans-serif;
    }

    .title {
      font-size: 16px;
      margin: 0;
    }

    /* table {
      border-collapse: collapse;
      }
      th {
        border: 1px solid;
        background-color: #bceeff;
      border-bottom: none;
      font-weight: normal;
      font-size: 12px;
    } */
    /* td {
      padding: 6px;
      vertical-align: top;
      border: 1px solid;
      border-top: none;
    } */

    .num_arraigo_container{
      width: 100%;
      margin-top: 45px;
    }
    .datos_personales_container {
      width: 100%;
      width: 100%;
      margin: 0;
      margin-bottom: 20px;
      font-size: 12px;
    }

    .datos_personales_wrap {
      display: table;
      width: 100%;
      margin: 0;
    }
  .foto-img {
    width: 150px;
    height: 214px;
    object-fit: cover;  /* Hace que la imagen cubra el área sin distorsión, recortando */
    object-position: center center; /* Centra la imagen en el recorte */
    display: block;
  }


    .table-datos-personales {
      width: 100%;
      display: table-cell;
      vertical-align: top;

    }
    .table-datos-personales td{
      width: 100%;
    }

    .table-datos-personales th {
      font-weight: normal;
      font-size: 12px;
      background-color: #bceeff;
    }

    .td-bold {
      font-weight: bold;
      font-size: 25px
    }
  </style>
</head>
<body>
  <!-- HEADER -->
  <div class="center" style="width: 100%;">
  <table style="width: 100%; border-collapse: collapse;">
    <tr>
      <td style="text-align: center; white-space: nowrap;">
        <img src="{{ public_path('images/eagleSRE.svg') }}" alt="Escudo" style="height: 80px;">
        <p style="margin: 0; font-size: 10px; white-space: nowrap;">SECRETARÍA DE RELACIONES EXTERIORES</p>
      </td>

      <td style="text-align: center; font-size: 12px;">
        <p style="margin: 0;">DIRECCIÓN GENERAL DE DELEGACIONES</p>
        <p style="margin: 0;">DIRECCIÓN DE NORMATIVIDAD</p>
        <p style="margin: 0;">BOLETA DE ELABORACIÓN DE ARRAIGO O IMPEDIMENTO ADMINISTRATIVO</p>
      </td>
    </tr>
  </table>
</div>

  <!-- END-HEADER -->

  <!-- NUMERO-ARRAIGO -->
  <div class="num_arraigo_container">
    <table style="border-collapse: collapse;border: 1px solid; margin: 0 auto;">
      <tr>
        <th style="border: 1px solid; font-weight: normal; background-color: #bceeff;">
          NUMERO DE IMPEDIMENTO
        </th>
      </tr>
      <tr>
        <td class="td-bold" style="text-align: center;">
          {{ $ImImpedimento->numero_impedimento ? $ImImpedimento->numero_impedimento : '' }}
        </td>
      </tr>
    </table>
  </div>
    <!-- END-NUMERO-ARRAIGO -->

<div class="datos_personales_container">
  <p class="title">DATOS PERSONALES</p>
  <table class="datos_personales_wrap">
  <tr>

@php
    $imagenMostrada = false;
@endphp

@foreach ($ImImpedimento->documents as $doc)
    @if ($doc->id_cat_anexos == 12 && !empty($doc->url_documento))
        <img src="data:image/png;base64,{{ $doc->url_documento }}" alt="Imagen" class="foto-img">
        @php $imagenMostrada = true; @endphp
        @break
    @endif
@endforeach

@if (!$imagenMostrada)
    <p style="text-align: center; font-size:16px; font-weight: normal;">Imagen no disponible</p>
@endif




    <td class="table-datos-personales">
      <table style="width: 100%; border-collapse: collapse;" border="1">
        <tr style="width: 100&;">
          <th colspan="2" style="width: 100%;">NOMBRE(S)</th>
        </tr>
        <tr>
          <td colspan="2" style="width: 100%;">{{ optional($ImImpedimento->people)->nombres ?? '' }}</td>
        </tr>

        <tr>
          <th style="width: 50%;">APELLIDO PATERNO</th>
          <th style="width: 50%;">APELLIDO MATERNO</th>
        </tr>
        <tr>
          <td style="width: 50%;">{{ optional($ImImpedimento->people)->primer_apellido ?? '' }}</td>
          <td style="width: 50%;">{{ optional($ImImpedimento->people)->segundo_apellido ?? '' }}</td>
        </tr>
        <tr style="width: 100&;">
          <th colspan="2" style="width: 100%;"> FECHA DE NACIMIENTO</th>
        </tr>
        <tr>
          <td colspan="2" style="width: 100%;">{{ optional($ImImpedimento->people)->format_fecha_nacimiento ?? '' }}</td>
        </tr>

        <tr style="width: 100&;">
          <th colspan="2" style="width: 100%;">LUGAR DE NACIMIENTO</th>
        </tr>
        <tr>
          <td colspan="2" style="width: 100%;">
            {{ $ImImpedimento->people->entidad_federativa_nacimiento ?? '' }}
        </td>

        </tr>

        <tr style="width: 100&;">
          <th colspan="2" style="width: 100%;">NOMBRE DEL PADRE</th>
        </tr>
        <tr>
          <td colspan="2" style="width: 100%;">
            @php
              $padre = optional($ImImpedimento->fathers);
              $nombrePadre = trim("{$padre->nombres_padre} {$padre->primer_apellido_padre} {$padre->segundo_apellido_padre}");
            @endphp
            {{ $nombrePadre !== '' ? $nombrePadre : '' }}
          </td>
        </tr>

        <tr style="width: 100&;">
          <th colspan="2" style="width: 100%;">NOMBRE DE LA MADRE</th>
        </tr>
        <tr>
          <td colspan="2" style="width: 100%;">
            @php
              $madre = optional($ImImpedimento->fathers);
              $nombreMadre = trim("{$madre->nombres_madre} {$madre->primer_apellido_madre} {$madre->segundo_apellido_madre}");
            @endphp
            {{ $nombreMadre !== '' ? $nombreMadre : '' }}
          </td>
        </tr>
      </table>
    </td>
  </tr>
</table>
</div>
<div class="datos_personales_container">
  <p class="title">INSTITUCION QUE REMITE LA DOCUMENTACION</p>
  <table class="datos_personales_wrap">
  <tr>
    <td class="table-datos-personales">
      <table style="width: 100%; border-collapse: collapse;" border="1">
        <tr>
          <th style="width: 25%;">DOCUMENTO</th>
          <th style="width: 50%;">ORIGEN</th>
          <th style="width: 25%;">SOLICITA IMPEDIMENTO</th>
        </tr>
        <tr>

          <td style="width:25%;">{{ $ImImpedimento->numero_documento ? $ImImpedimento->numero_documento : '' }}</td>
          <td style="width: 25%;">
              {{ !blank($ImImpedimento->nombre_dependencia)
                  ? $ImImpedimento->nombre_dependencia
                  : optional($ImImpedimento->cat_office)->cad_oficina }}
          </td>

          <td>SI</td>
        </tr>

        <tr style="width: 100&;">
          <th style="width: 25%;"> FECHADO</th>
          <th colspan="2" style="width: 75%;"> CONTENIDO</th>
        </tr>
        <tr>
          <td style="width: 25%;">{{ $ImImpedimento->format_created_at ? $ImImpedimento->format_created_at : ''}}</td>
          <td colspan="2" style="width: 75%;">
          {{ $ImImpedimento->documents->map(fn($doc) => $doc->cat_anexo?->nombre)
            ->filter()
            ->implode(', ') ?: '' }}
          </td>
        </tr>
      </table>
    </td>
  </tr>
</table>
</div>

<div class="datos_personales_container">
  <p class="title">MOTIVO DEL ARRAIGO JUDICIAL O IMPEDIMENTO ADMINISTRATIVO</p>
  <table class="datos_personales_wrap">
  <tr>
    <td class="table-datos-personales">
      <table style="width: 100%; border-collapse: collapse;" border="1">
        <tr>

          <th style="width: 100%;">&nbsp;</th>
        </tr>

        <tr>
            <td style="width: 100%;">
                @if ($ImImpedimento->motivacion_acto_juridico)
                    {!! html_entity_decode($ImImpedimento->motivacion_acto_juridico) !!}
                @endif
            </td>

        </tr>

      </table>
    </td>
  </tr>
</table>
</div>

<div class="datos_personales_container">
  <p class="title">CAMPOS DE CONTROL</p>
  <table style="width: 100%; border-collapse: separate; border-spacing: 10px; border: 1px solid #000;">
  <tr>
  <!-- Firma 1 -->
  <td style="width: 16.66%; height: 60px; vertical-align: bottom;">
    <div style="border-bottom: 1px solid #000; height: 40px;">
    </div>
    <p style="text-align: center; font-size: 10px; margin: 0; margin-top: 2px;">
        ELABORO <br>
        <span>{!! optional($ImImpedimento->usuarioElaboro)->full_name ? optional($ImImpedimento->usuarioElaboro)->full_name : "<br/>" !!}</span>
    </p>
  </td>

  <!-- Firma 2 -->
  <td style="width: 16.66%; height: 60px; vertical-align: bottom;">
    <div style="border-bottom: 1px solid #000; height: 40px;">

    </div>
    <p style="text-align: center; font-size: 10px; margin: 0; margin-top: 2px;">
        REVISO <br>
        <span>{!! optional($ImImpedimento->usuarioReviso)->full_name ? optional($ImImpedimento->usuarioReviso)->full_name : "<br/>" !!}</span>
    </p>
  </td>

  <!-- Firma 3 -->
  <td style="width: 16.66%; height: 60px; vertical-align: bottom;">
    <div style="border-bottom: 1px solid #000; height: 40px;">
    </div>
    <p style="text-align: center; font-size: 10px; margin: 0; margin-top: 2px;">
        AUTORIZO <br>
        <span>{!! optional($ImImpedimento->usuarioAutorizo)->full_name ? optional($ImImpedimento->usuarioAutorizo)->full_name : "<br/>" !!}</span>
    </p>
  </td>

  <!-- Firma 4 -->
  <td style="width: 16.66%; height: 60px; vertical-align: bottom;">
    <div style="border-bottom: 1px solid #000; height: 40px;">
    </div>
  <p style="text-align: center; font-size: 10px; margin: 0; margin-top: 2px;">
    @if ($ImImpedimento->low)
        BAJA <br>

        @if ($ImImpedimento->low != null && $ImImpedimento->low->id_estatus_impedimento_baja == 150)
            <span>{!! optional($ImImpedimento->low->cat_user_alta)->full_name ? optional($ImImpedimento->low->cat_user_alta)->full_name : "<br/>" !!}</span>
        @else
            <br>
        @endif

    @else
        ALTA <br>
        <span>{!! optional($ImImpedimento->usuarioAltas)->full_name ? optional($ImImpedimento->usuarioAltas)->full_name : "<br/>" !!}</span>
    @endif
</p>
  </td>

  <!-- Caja 5 -->
  <td style="width: 16.66%; vertical-align: bottom;">
    <table style="width: 100%; border-collapse: collapse;" border="1">
      <tr>
        <th style="font-size: 12px; font-weight: normal; text-align: center; background-color: #bceeff;">NÚM. IMPEDIMENTO</th>
      </tr>
      <tr>
        <td style="font-size: 12px; text-align: center; font-weight: bold;">{{ $ImImpedimento->numero_impedimento }}</td>
      </tr>
    </table>
  </td>

  <!-- Caja 6 -->
  <td style="width: 16.66%; vertical-align: bottom;">
    <table style="width: 100%; border-collapse: collapse;" border="1">
      <tr>
        <th style="font-size: 12px; font-weight: normal; text-align: center; background-color: #bceeff;">FECHA DE AUTORIZACION</th>
      </tr>
      <tr>
        <td style="font-size: 12px; text-align: center; font-weight: bold;">{{ $ImImpedimento->fecha_autorizacion_format ? $ImImpedimento->fecha_autorizacion_format : '' }}</td>
      </tr>
    </table>
  </td>
</tr>
  </table>
</div>





</body>
</html>
