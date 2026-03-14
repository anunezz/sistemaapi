<?php

namespace App\Models;

use App\Casts\CleanText;
use App\Models\Catalogs\ImCatGeneralGenero;
use App\Models\Catalogs\ImCatOficina;
use App\Models\Catalogs\ImCatSubCausalImpedimento;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class ImImpedimentoBitacora extends Model
{
    protected $table = 'im_impedimento_bitacora';
    protected $primaryKey = 'id_impedimento_bitacora';
    protected $fillable = [
        'id_impedimento_bitacora',
        'id_impedimento',
        'id_oficina',
        'correo_electronico',
        'numero_documento',
        'id_estatus_impedimento',
        'cad_oficina',
        'motivacion_acto_juridico',
        'id_causal_impedimento',
        'causal_otro_descripcion',
        'id_subcausal_impedimento',
        'anexo_expediente_integrado',
        'anexo_dictamen_verificacion',
        'numero_pasaporte_cancelado',
        'otro_documento_soporte',
        'observaciones',
        'numero_impedimento',
        'dependencia',
        'nombre_dependencia',
        'fecha_autorizacion',
        'id_usuario_elaboro',
        'id_usuario_reviso',
        'id_usuario_autorizo',
        'id_usuario_altas',
        'bol_eliminado',
        'id_usuario_alta',
        'id_usuario_modificacion',
        'created_at_impedimento',
        'updated_at_impedimento',
        //PERSONA
        'id_persona',
        'id_persona_consolidada',
        'nombres',
        'primer_apellido',
        'segundo_apellido',
        'fecha_nacimiento',
        'curp',
        'id_genero',
        'entidad_federativa_nacimiento',
        'id_pais_nacimiento',
        'id_entidad_federativa_nacimiento',
        'id_municipio_nacimiento',
        'bol_eliminado_persona',
        'created_at_persona',
        'updated_at_persona',
        //PADRES
        'nombres_padre',
        'primer_apellido_padre',
        'segundo_apellido_padre',
        'nombres_madre',
        'primer_apellido_madre',
        'segundo_apellido_madre',
        'bol_eliminado_padres',
        'created_at_padres',
        'updated_at_padres',
        //BAJA
        'id_secuencial_baja',
        'fecha_elaboracion',
        'id_estatus_impedimento_baja',
        'fechado',
        'contenido',
        'motivo_levantamiento',
        'descrpción_levantamiento',
        'anexo_expediente_integrado_baja',
        'anexo_dictamen_verificacion_baja',
        'documentacion_complementaria',
        'resolucion_juficial',
        'denuncia_penal_ratificada',
        'otro_documento_baja',
        'id_usuario_alta_baja',
        'id_usuario_modificacion_baja',
        'is_active',
        'bol_eliminado_bajas',
        'created_at_bajas',
        'updated_at_bajas',

        'estatus_solicitud',
        'tipo_solicitud',
        'id_tipo_solicitud',
        'causal_impedimento',
        'subcausal_impedimento',
        'usuario_alta',
        'usuario_modificacion'
    ];

    protected function casts(): array
    {
        return [
            'correo_electronico' => CleanText::class,
            'numero_documento' => CleanText::class,
            'cad_oficina' => CleanText::class,
            'motivacion_acto_juridico' => CleanText::class,
            'causal_otro_descripcion' => CleanText::class,
            'anexo_expediente_integrado' => CleanText::class,
            'anexo_dictamen_verificacion' => CleanText::class,
            'numero_pasaporte_cancelado' => CleanText::class,
            'otro_documento_soporte' => CleanText::class,
            'observaciones' => CleanText::class,
            'numero_impedimento' => CleanText::class,
            'nombre_dependencia' => CleanText::class,

            'nombres' => CleanText::class,
            'primer_apellido' => CleanText::class,
            'segundo_apellido' => CleanText::class,
            'curp' => CleanText::class,
            'entidad_federativa_nacimiento' => CleanText::class,

            'nombres_padre' => CleanText::class,
            'primer_apellido_padre' => CleanText::class,
            'segundo_apellido_padre' => CleanText::class,

            'nombres_madre' => CleanText::class,
            'primer_apellido_madre' => CleanText::class,
            'segundo_apellido_madre' => CleanText::class,

            'contenido' => CleanText::class,
            'motivo_levantamiento' => CleanText::class,
            'descrpción_levantamiento' => CleanText::class,
            'anexo_expediente_integrado_baja' => CleanText::class,
            'anexo_dictamen_verificacion_baja' => CleanText::class,
            'documentacion_complementaria' => CleanText::class,
            'resolucion_juficial' => CleanText::class,
            'denuncia_penal_ratificada' => CleanText::class,
            'otro_documento_baja' => CleanText::class,

            'estatus_solicitud' => CleanText::class,
            'tipo_solicitud' => CleanText::class,
            'causal_impedimento' => CleanText::class,
            'subcausal_impedimento' => CleanText::class,
            'usuario_alta' => CleanText::class,
            'usuario_modificacion' => CleanText::class,
        ];
    }

    protected $appends = ['full_name'];

    public function getFullNameAttribute(): string
    {
        return "$this->nombres $this->primer_apellido $this->segundo_apellido";
    }

    public function getCreatedAtImpedimentoAttribute($value)
    {
        return $value
            ? Carbon::parse($value)->format('d-m-Y H:i:s')
            : null;
    }

    public function getFechaNacimientoAttribute($value)
    {
        return $value
            ? Carbon::parse($value)->format('d-m-Y')
            : null;
    }

    public function getFechaRegistroAttribute()
    {
        if (!$this->created_at) {
            return null;
        }

        return Carbon::parse($this->created_at)->format('d-m-Y H:i:s');
    }

    public function cat_office()
    {
        return $this->hasOne(ImCatOficina::class, 'id_oficina', 'id_oficina');
    }

    /** Usuario que autorizó */
    public function usuarioAlta()
    {
        return $this->belongsTo(User::class, 'id_usuario_alta', 'id');
    }

    /** Usuario que autorizó */
    public function usuarioModificacion()
    {
        return $this->belongsTo(User::class, 'id_usuario_modificacion', 'id');
    }

    public function requests()
    {
        return $this->belongsToMany(
            ImSolicitud::class,
            'im_impedimentos_solicitudes',
            'id_solicitud',
            'id_impedimento'
        )
            ->with(['cat_type', 'cat_status'])
            ->distinct();
    }

    function cat_causal_impedimento()
    {
        return $this->hasOne(ImCatCausalImpedimento::class, 'id_causal_impedimento', 'id_causal_impedimento');
    }

    function cat_type()
    {
        return $this->hasOne(ImCatTipoSolicitud::class, 'id_tipo_solicitud', 'id_tipo_solicitud');
    }

    function people()
    {
        return $this->hasOne(ImPersona::class, 'id_persona', 'id_persona');
    }

    public function cat_subcausal_impedimento()
    {
        return $this->hasOne(ImCatSubCausalImpedimento::class, 'id_causal_impedimento', 'id_causal_impedimento');
    }

    function low()
    {
        return $this->hasOne(ImImpedimentoBaja::class, 'id_impedimento', 'id_impedimento');
    }
    public function persona_bitacora()
    {
        return $this->hasOne(ImPersonaBitacora::class, 'id_persona', 'id_persona');
    }

    function cat_status()
    {
        return $this->belongsTo(ImCatStatusSolicitud::class, 'id_estatus_impedimento', 'id_estatus_solicitud');
    }

    function cat_genero()
    {
        return $this->belongsTo(ImCatGeneralGenero::class, 'id_genero','id');
    }

    function cat_causal()
    {
        return $this->hasOne(ImCatCausalImpedimento::class, 'id_causal_impedimento', 'id_causal_impedimento');
    }

    function cat_subcausal()
    {
        return $this->hasOne(ImCatSubcausalImpedimento::class, 'id_subcausal_impedimento', 'id_subcausal_impedimento');
    }

    /** Usuario que elaboró */
    public function usuarioElaboro()
    {
        return $this->belongsTo(User::class, 'id_usuario_elaboro', 'id');
    }

    /** Usuario que revisó */
    public function usuarioReviso()
    {
        return $this->belongsTo(User::class, 'id_usuario_reviso', 'id');
    }

    /** Usuario que autorizó */
    public function usuarioAutorizo()
    {
        return $this->belongsTo(User::class, 'id_usuario_autorizo', 'id');
    }

    /** Usuario que dio de alta */
    public function usuarioAltas()
    {
        return $this->belongsTo(User::class, 'id_usuario_altas', 'id');
    }

    /** Usuario que dio de alta la baja */
    public function usuarioAltaBaja()
    {
        return $this->belongsTo(User::class,'id_usuario_alta_baja','id');
    }

    /** Usuario que modificó la baja */
    public function usuarioModificacionBaja()
    {
        return $this->belongsTo(User::class, 'id_usuario_modificacion_baja', 'id');
    }

    public function getCreatedAtAttribute($value)
    {
        return Carbon::parse($value)->format('d/m/Y H:i');
    }

    public function getUpdatedAtAttribute($value)
    {
        return Carbon::parse($value)->format('d/m/Y H:i');
    }

    protected function applySearchConcatenatedColumns($query, array $columns, $value, $useLike = false)
    {
        $query->where(function ($q) use ($columns, $value, $useLike) {
            $cleanValue = preg_replace('/\s+/', ' ', trim($value));
            foreach ($columns as $column) {
                if ($useLike) {
                    $q->orWhereRaw("unaccent(lower({$column})) LIKE unaccent(lower(?))", ['%' . strtolower($cleanValue) . '%']);
                } else {
                    $q->orWhereRaw("unaccent(lower({$column})) = unaccent(lower(?))", [strtolower($cleanValue)]);
                }
            }
        });
    }


    public function scopeSearch($query, $filters)
    {
        return $query->where(function ($q) use ($filters) {
            $q->when(!empty($filters->id_impedimento), function ($query) use ($filters) {
                $query->where('id_impedimento', $filters->id_impedimento);
            });
            $q->when(!empty($filters->numero_impedimento), function ($query) use ($filters) {
                $query->where('numero_impedimento', 'like', '%' . $filters->numero_impedimento . '%');
            });

            $q->when($filters->id_type !== null, function ($query) use ($filters) {
                if ($filters->id_type == 999) {
                    $query->whereNull('id_tipo_solicitud');
                } else {
                    $query->where('id_tipo_solicitud', $filters->id_type);
                }
            });


            $q->when(!empty($filters->id_oficina), function ($query) use ($filters) {
                $query->whereIn('im_impedimento_bitacora.id_oficina', [$filters->id_oficina]);
            });

            $q->when($filters->id_estatus !== null, function ($query) use ($filters) {
                $query->where('im_impedimento_bitacora.id_estatus_impedimento', $filters->id_estatus);
            });


            $q->when(!empty($filters->id_causal), function ($query) use ($filters) {
                $query->whereIn('im_impedimento_bitacora.id_causal_impedimento', [$filters->id_causal]);
            });

                $q->when(!empty($filters->id_subcausal_impedimento), function ($query) use ($filters) {
                    $query->whereIn('im_impedimento_bitacora.id_subcausal_impedimento', $filters->id_subcausal_impedimento);
                });



            $q->when(!empty($filters->curp), function ($query) use ($filters) {
                $query->where('curp', 'ILIKE', '%' . $filters->curp . '%');

            });


            $q->when(!empty($filters->name), function ($query) use ($filters) {
                $this->applySearchConcatenatedColumns($query, [
                    'nombres',
                    'primer_apellido',
                    'segundo_apellido'
                ], $filters->name, true);
            });


            $q->when(!empty($filters->from) && !empty($filters->to), function ($query) use ($filters) {
                $fechaInicio = Carbon::createFromFormat('d-m-Y', $filters->from, 'America/Mexico_City')
                    ->startOfDay()
                    ->timezone('UTC');

                $fechaFin = Carbon::createFromFormat('d-m-Y', $filters->to, 'America/Mexico_City')
                    ->endOfDay()
                    ->timezone('UTC');
                $query->whereBetween('created_at', [$fechaInicio, $fechaFin]);
            });
        });
    }
}
