<?php

namespace App\Models;

use App\Casts\CleanText;
use App\Models\Catalogs\ImCatOficina;
use App\Models\Catalogs\ImCatPrioridades;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use App\Models\Catalogs\ImCatCausalImpedimento;
use App\Traits\SearchableTrait;

class ImSolicitudBitacora extends Model
{

    use SearchableTrait;
    protected $table = 'im_solicitud_bitacora';

    protected $primaryKey = 'id_bitacora';

    protected $fillable = [
        'id_solicitud',
        'fecha_registro',
        'id_tipo_solicitud',
        'id_estatus_solicitud',
        'id_estatus_verificacion',
        'cad_oficina',
        'id_oficina',
        'id_prioridad',
        'urgencia',
        'observaciones',
        'id_cita',
        'id_solicitud_suet',
        'nombres',
        'primer_apellido',
        'segundo_apellido',
        'persona_correo_electronico',
        'fecha_nacimiento',
        'curp',
        'causal_otro_descripcion',
        'id_genero',
        'entidad_federativa_nacimiento',
        'id_pais_nacimiento',
        'nombres_padre',
        'primer_apellido_padre',
        'segundo_apellido_padre',
        'nombres_madre',
        'primer_apellido_madre',
        'segundo_apellido_madre',
        'correo_electronico',
        'motivacion_acto_juridico',
        'causal_impedimento',
        'id_causal_impedimento',
        'id_subcausal_impedimento',
        'anexo_expediente_integrado',
        'anexo_dictamen_verificacion',
        'numero_pasaporte_cancelado',
        'otro_documento_soporte',
        'documentacion_complementaria',
        'resolucion_judicial',
        'denuncia_penal_ratificada',
        'dependencia',
        'nombre_dependencia',
        'bol_eliminado',
        'id_usuario_alta',
        'id_usuario_modificacion',
        'created_at',
        'updated_at',
        'tipo_solicitud',
        'estatus_solicitud',
        'prioridad',
        'nombres_identidad',
        'numero_documento',
        'primer_apellido_identidad',
        'segundo_apellido_identidad',
        'curp_identidad',
        'cuerpo_correo'
    ];

    protected $appends = ['hash_id', 'full_name', 'aux_update','fecha_registro_formatted','fecha_nacimiento_formatted'];

    protected function casts(): array
    {
        return [
            'tipo_solicitud' => CleanText::class,
            'estatus_solicitud' => CleanText::class,
            'prioridad' => CleanText::class,
            'observaciones' => CleanText::class,
            'nombres' => CleanText::class,
            'primer_apellido' => CleanText::class,
            'segundo_apellido' => CleanText::class,
            'persona_correo_electronico' => CleanText::class,
            'curp' => CleanText::class,
            'entidad_federativa_nacimiento' => CleanText::class,
            'nombres_padre' => CleanText::class,
            'primer_apellido_padre' => CleanText::class,
            'segundo_apellido_padre' => CleanText::class,
            'nombres_madre' => CleanText::class,
            'primer_apellido_madre' => CleanText::class,
            'segundo_apellido_madre' => CleanText::class,
            'correo_electronico' => CleanText::class,
            'nombres_identidad' => CleanText::class,
            'primer_apellido_identidad' => CleanText::class,
            'segundo_apellido_identidad' => CleanText::class,
            'curp_identidad' => CleanText::class,
            'anexo_expediente_integrado' => CleanText::class,
            'anexo_dictamen_verificacion' => CleanText::class,
            'numero_pasaporte_cancelado' => CleanText::class,
            'otro_documento_soporte' => CleanText::class,
            'documentacion_complementaria' => CleanText::class,
            'resolucion_judicial' => CleanText::class,
            'denuncia_penal_ratificada' => CleanText::class,
        ];
    }

    public function getHashIdAttribute()
    {
        return encrypt($this->id_solicitud);
    }

    public function getFechaRegistroFormattedAttribute()
    {
        if (!$this->fecha_registro) {
            return null;
        }

        return Carbon::parse($this->fecha_registro)->format('d-m-Y');
    }

    public function getFechaNacimientoFormattedAttribute()
    {
        if (!$this->fecha_nacimiento) {
            return null;
        }

        return Carbon::parse($this->fecha_nacimiento)->format('d-m-Y');
    }

    public function getAuxUpdateAttribute()
    {
        return ($this->id_subcausal_impedimento != null);
    }

    public function getFullNameAttribute(): string
    {
        return "$this->nombres $this->primer_apellido $this->segundo_apellido";
    }

    function cat_office()
    {
        return $this->hasOne(ImCatOficina::class, 'id_oficina', 'id_oficina');
    }

    function cat_status()
    {
        return $this->hasOne(ImCatStatusSolicitud::class, 'id_estatus_solicitud', 'id_estatus_solicitud');
    }

    function cat_causales()
    {
        return $this->hasOne(ImCatStatusSolicitud::class, 'id_estatus_solicitud', 'id_estatus_solicitud');
    }

    function cat_priority()
    {
        return $this->hasOne(ImCatPrioridades::class, 'id_prioridad', 'id_prioridad');
    }

    function cat_type()
    {
        return $this->hasOne(ImCatTipoSolicitud::class, 'id_tipo_solicitud', 'id_tipo_solicitud');
    }
    public function usuario_modificacion()
    {
        return $this->belongsTo(User::class, 'id_usuario_modificacion');
    }
    function documents()
    {
        return $this->hasMany(ImPersonaSolicitudDocumento::class, 'id_solicitud', 'id_solicitud');
    }

    function cat_causal_impedimento()
    {
        return $this->hasOne(ImCatCausalImpedimento::class, 'id_causal_impedimento', 'id_causal_impedimento');
    }

    public function scopeSearch($query, $filters)
    {


        return $query->where(function ($q) use ($filters) {
            $q->when(!empty($filters->id_solicitud), function ($query) use ($filters) {
                $query->where('im_solicitud_bitacora.id_solicitud', $filters->id_solicitud);
            });
            $q->when(!empty($filters->id_type), function ($query) use ($filters) {
                $query->whereIn('im_solicitud_bitacora.id_tipo_solicitud', [$filters->id_type]);
            });
            $q->when(!empty($filters->id_oficina), function ($query) use ($filters) {
                $query->whereIn('im_solicitud_bitacora.id_oficina', [$filters->id_oficina]);
            });

            $q->when(!empty($filters->id_estatus), function ($query) use ($filters) {
                $query->whereIn('im_solicitud_bitacora.id_estatus_solicitud', [$filters->id_estatus]);
            });

            $q->when(!empty($filters->id_causal), function ($query) use ($filters) {
                $query->whereIn('im_solicitud_bitacora.id_causal_impedimento', [$filters->id_causal]);
            });

            $q->when(!empty($filters->id_subcausal_impedimento), function ($query) use ($filters) {
                $query->whereIn('im_solicitud_bitacora.id_subcausal_impedimento', $filters->id_subcausal_impedimento);
            });

            $q->when(!empty($filters->name), function ($query) use ($filters) {
                $this->applySearchConcatenatedColumns($query, ['nombres', 'primer_apellido', 'segundo_apellido'], $filters->name, true);
            });

            $q->when(!empty($filters->from) && !empty($filters->to), function ($query) use ($filters) {

                $fechaInicio = Carbon::createFromFormat(
                    'd-m-Y',
                    $filters->from
                )->startOfDay();

                $fechaFin = Carbon::createFromFormat(
                    'd-m-Y',
                    $filters->to
                )->endOfDay();

                $query->whereBetween(
                    'im_solicitud_bitacora.fecha_registro',
                    [$fechaInicio, $fechaFin]
                );
            });


        });
    }
}

