<?php

namespace App\Models;

use App\Casts\CleanText;
use App\Models\Catalogs\ImCatCausalImpedimento;
use App\Models\Catalogs\ImCatOficina;
use Illuminate\Database\Eloquent\Model;
use App\Models\Catalogs\ImCatGeneralGenero;
use App\Models\Catalogs\ImCatPrioridades;
use App\Models\Catalogs\ImCatSubCausalImpedimento;
use App\Models\Catalogs\ImCatEstatusVerificacion;
use Carbon\Carbon;
use App\Models\User;

class ImSolicitud extends Model
{
    protected $table = 'im_solicitud';         // Nombre de la tabla
    protected $primaryKey = 'id_solicitud';
    protected $fillable = [
        'fecha_registro',
        'id_tipo_solicitud',
        'id_estatus_solicitud',
        'id_estatus_verificacion',
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
        'id_causal_impedimento',
        'causal_otro_descripcion',
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
        'nombres_identidad',
        'primer_apellido_identidad',
        'segundo_apellido_identidad',
        'curp_identidad',
        'fecha_autorizacion',
        'id_usuario_elaboro',
        'id_usuario_reviso',
        'id_usuario_autorizo',
        'id_usuario_altas',
        'bol_eliminado',
        'id_usuario_alta',
        'id_usuario_modificacion',
        'numero_documento',
        'cuerpo_correo',
        'backup_anterior',
        'id_impedimento',
        'verificacion_impedimentos'
    ];

    protected $appends = ['hash_id', 'full_name', 'aux_update','fecha_registro_formatted'];
    protected function casts(): array
    {
        return [
            'observaciones' => CleanText::class,
            'nombres' => CleanText::class,
            'primer_apellido' => CleanText::class,
            'segundo_apellido' => CleanText::class,
            'persona_correo_electronico' => CleanText::class,
            'curp' => CleanText::class,
            'nombres_padre' => CleanText::class,
            'primer_apellido_padre' => CleanText::class,
            'segundo_apellido_padre' => CleanText::class,
            'nombres_madre' => CleanText::class,
            'primer_apellido_madre' => CleanText::class,
            'segundo_apellido_madre' => CleanText::class,
            'correo_electronico' => CleanText::class,
            'causal_otro_descripcion' => CleanText::class,
            'anexo_expediente_integrado' => CleanText::class,
            'anexo_dictamen_verificacion' => CleanText::class,
            'numero_pasaporte_cancelado' => CleanText::class,
            'otro_documento_soporte' => CleanText::class,
            'documentacion_complementaria' => CleanText::class,
            'resolucion_judicial' => CleanText::class,
            'denuncia_penal_ratificada' => CleanText::class,
            'nombres_identidad' => CleanText::class,
            'primer_apellido_identidad' => CleanText::class,
            'segundo_apellido_identidad' => CleanText::class,
            'curp_identidad' => CleanText::class,
            'verificacion_impedimentos' => 'array',
        ];
    }

    public function getFechaRegistroFormattedAttribute()
    {
        if (!$this->fecha_registro) {
            return null;
        }

        return Carbon::parse($this->fecha_registro)->format('d-m-Y');
    }

    public function getHashIdAttribute()
    {
        return  encrypt($this->id_solicitud);
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

    function cat_status_verificacion()
    {
        return $this->hasOne(ImCatEstatusVerificacion::class, 'id_estatus_verificacion', 'id_estatus_verificacion');
    }

    function cat_priority()
    {
        return $this->hasOne(ImCatPrioridades::class, 'id_prioridad', 'id_prioridad');
    }

    function cat_type()
    {
        return $this->hasOne(ImCatTipoSolicitud::class, 'id_tipo_solicitud', 'id_tipo_solicitud');
    }

    function documents()
    {
        return $this->hasMany(ImSolicitudDocumento::class, 'id_solicitud', 'id_solicitud');
    }

    function cat_causal_impedimento()
    {
        return $this->hasOne(ImCatCausalImpedimento::class, 'id_causal_impedimento', 'id_causal_impedimento');
    }

    public function cat_subcausal_impedimento()
    {
        return $this->hasOne(ImCatSubCausalImpedimento::class, 'id_causal_impedimento', 'id_causal_impedimento');
    }

    public function causales()
    {
        return $this->hasMany(ImSolicitudCausal::class, 'solicitud_id', 'id_solicitud');
    }

    public function asignaciones()
    {
        return $this->hasMany(ImAsignacionSolicitudes::class, 'id_solicitud', 'id_solicitud');
    }

    public function asignacion()
    {
        return $this->hasOne(ImAsignacionSolicitudes::class, 'id_solicitud', 'id_solicitud');
    }

    public function impediment()
    {
        return $this->belongsToMany(
            ImImpedimento::class,
            'im_impedimentos_solicitudes',
            'id_solicitud',
            'id_impedimento',
        )->distinct()->limit(1);
    }

    public function impedimento()
    {
        return $this->hasOne(ImImpedimento::class, 'id_impedimento', 'id_impedimento');
    }

    public function cat_user_elaboro()
    {
        return $this->hasOne(User::class, 'id','id_usuario_elaboro');
    }

    public function cat_user_reviso()
    {
        return $this->hasOne(User::class,'id','id_usuario_reviso');
    }

    public function cat_user_autorizo()
    {
        return $this->hasOne(User::class, 'id_usuario_elaboro', 'id');
    }

    public function cat_user_altas()
    {
        return $this->hasOne(User::class, 'id_usuario_elaboro', 'id');
    }

    public function getCreatedAtAttribute($value)
    {
        return Carbon::parse($value)->format('d/m/Y H:i');
    }

    public function getUpdatedAtAttribute($value)
    {
        return Carbon::parse($value)->format('d/m/Y H:i');
    }

    public function scopeSearch($query, $filters)
    {
        return $query->where(function ($q) use ($filters) {

            $q->when(!empty($filters->id_solicitud), function ($query) use ($filters) {
                $query->where('id_solicitud', $filters->id_solicitud);
            });
            $q->when(!empty($filters->id_oficina), function ($query) use ($filters) {
                $query->whereIn('im_solicitud.id_oficina', $filters->id_oficina);
            });

            $q->when(!empty($filters->id_estatus_solicitud), function ($query) use ($filters) {
                $query->whereIn('im_solicitud.id_estatus_solicitud', $filters->id_estatus_solicitud);
            });

            $q->when(!empty($filters->id_causal_impedimento), function ($query) use ($filters) {
                $query->whereIn('im_solicitud.id_causal_impedimento', $filters->id_causal_impedimento);
            });

            $q->when(!empty($filters->id_causal_subimpedimento), function ($query) use ($filters) {
                $query->whereIn('im_solicitud.id_subcausal_impedimento', $filters->id_causal_subimpedimento);
            });

            $q->when(!empty($filters->id_usuario_assigned_requests), function ($query) use ($filters) {
                $query->whereHas('asignaciones', function ($q) use ($filters) {
                $q->where('id_usuario', $filters->id_usuario_assigned_requests);
                });
            });
            $q->when(!empty($filters->nombres), function ($query) use ($filters) {
                $query->whereRaw("unaccent(nombres) ilike unaccent('%" . $filters->nombres . "%')");
            });

            $q->when(!empty($filters->primer_apellido), function ($query) use ($filters) {
                $query->whereRaw("unaccent(primer_apellido) ilike unaccent('%" . $filters->primer_apellido . "%')");
            });

            $q->when(!empty($filters->segundo_apellido), function ($query) use ($filters) {
                $query->whereRaw("unaccent(segundo_apellido) ilike unaccent('%" . $filters->segundo_apellido . "%')");
            });
            $q->when(!empty($filters->curp), function ($query) use ($filters) {
                $query->whereRaw("unaccent(im_solicitud.curp) ILIKE unaccent(?)", ['%' . $filters->curp . '%']);
            });

            $q->when(!empty($filters->from) && !empty($filters->to), function ($query) use ($filters) {
                $fechaInicio = \Carbon\Carbon::createFromFormat('d-m-Y', $filters->from)->startOfDay();
                $fechaFin = \Carbon\Carbon::createFromFormat('d-m-Y', $filters->to)->endOfDay();
                //  si ambas fechas son iguales
                if ($fechaInicio->equalTo($fechaFin)) {
                    $fechaInicio->startOfDay();
                    $fechaFin->endOfDay();
                }
                $query->whereBetween('im_solicitud.created_at', [$fechaInicio, $fechaFin]);
            });

            /*$q->when(!empty($filters->date), function ($query) use ($filters) {
                [$year, $month] = explode('-', $filters->date);

                if ($year && $month) {
                    $inicioMes = \Carbon\Carbon::create($year, $month, 1)->startOfMonth();
                    $finMes = \Carbon\Carbon::create($year, $month, 1)->endOfMonth();

                    $query->whereBetween('im_solicitud.created_at', [$inicioMes, $finMes]);
                }
            });*/

            $q->when(!empty($filters->numero_impedimento), function ($query) use ($filters) {
                $query->whereHas('impediment', function ($subQuery) use ($filters) {
                $subQuery->where('im_impedimento.id_impedimento', $filters->numero_impedimento);
                });
            });


            $q->when(!empty($filters->id_tipo_solicitud), function ($query) use ($filters) {
                $query->whereHas('cat_type', function ($subQuery) use ($filters) {
                    $subQuery->whereIn('im_solicitud.id_tipo_solicitud', $filters->id_tipo_solicitud);
                });
            });
        });
    }

     // Nuevo para verificaciones
    public function solicitudCausales()
    {
        return $this->hasMany(ImSolicitudCausal::class, 'solicitud_id');
    }

    public function usesMultiCausales(): bool
    {
        return (int)$this->id_tipo_solicitud === 3;
    }

    public function scopeRestrictByPerfilOficina($query)
    {
        $perfil = auth()->user()->usuarioPerfil;

        if ($perfil && !$perfil->bol_eliminado && in_array($perfil->id_perfil, [1,2])) {
            $query->where('id_oficina', auth()->user()->id_oficina);
        }

        return $query;
    }

    public function scopeOnlyAssignedRequests($query)
    {
        return $query->whereHas('asignaciones');
    }
    public function scopeOnlyAssignedRequestsByUser($query)
    {
        return $query->whereHas('asignaciones', function ($q) {
                    $q->where('id_usuario', auth()->id());
                });
    }



    public function scopeOnlyAvailableRequests($query)
    {
        return $query->whereNotIn('id_solicitud', function ($subquery) {
            $subquery->select('id_solicitud')
            ->from('im_asignacion_solicitudes');
        });
    }

    public $snapshotRelationsMap = [
    'cat_type' => 'tipo_solicitud',
    'cat_status' => 'estatus_solicitud',
    'cat_priority' => 'prioridad',
    'cat_causal_impedimento' => 'causal_impedimento',
    'cat_office' => 'cad_oficina',
];
}
