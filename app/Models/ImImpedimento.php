<?php

namespace App\Models;
//use App\Models\ImCatSubCausalImpedimento;
use App\Casts\CleanText;
use App\Models\Catalogs\ImCatCausalImpedimento;
use App\Models\Catalogs\ImCatSubCausalImpedimento;
use App\Models\Catalogs\ImCatOficina;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class ImImpedimento extends Model
{
    protected $table = 'im_impedimento';
    protected $primaryKey = 'id_impedimento';
    protected $fillable = [
        'id_impedimento',
        'id_persona',
        'id_oficina',
        'correo_electronico',
        'id_estatus_impedimento',
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
        'numero_documento',
        'backup_anterior'
    ];

    protected $appends = ['hash_id','expand','format_created_at','fecha_autorizacion_format'];

    protected function casts(): array
    {
        return [
            'anexo_expediente_integrado' => CleanText::class,
            'anexo_dictamen_verificacion' => CleanText::class,
            'numero_pasaporte_cancelado' => CleanText::class,
            'otro_documento_soporte' => CleanText::class,
            'causal_otro_descripcion' => CleanText::class,
            'observaciones' => CleanText::class,
            'numero_impedimento' => CleanText::class,
        ];
    }

    public function getHashIdAttribute()
    {
        return  encrypt($this->id_impedimento);
    }

    public function getFormatCreatedAtAttribute()
    {
        if (!$this->created_at) {
            return null;
        }

        return Carbon::parse($this->created_at)->format('d-m-Y H:i:s');
    }

    public function getFechaAutorizacionFormatAttribute()
    {
        if (!$this->fecha_autorizacion) {
            return null;
        }

        return Carbon::parse($this->fecha_autorizacion)->format('d-m-Y');
    }

    public function getExpandAttribute()
    {
        return false;
    }

    public function getNumeroImpedimentoAttribute()
    {
        return $this->id_impedimento;
    }

    public function cat_office()
    {
        return $this->hasOne(ImCatOficina::class, 'id_oficina', 'id_oficina');
    }

    public function requests()
{
    return $this->belongsToMany(
        ImSolicitud::class,
        'im_impedimentos_solicitudes',
        'id_impedimento', // este modelo
        'id_solicitud'    // modelo relacionado
    )
    ->with(['cat_type', 'cat_status'])
    ->distinct();
}


    public function requests2()
    {
        return $this->belongsToMany(
            ImSolicitud::class,
            'im_impedimentos_solicitudes',
            'id_impedimento',
            'id_solicitud'
        )
            ->distinct();
    }

    protected $casts = [
    'created_at' => 'datetime:d/m/Y H:i',
    'updated_at' => 'datetime:d/m/Y H:i',
];

    function cat_causal_impedimento()
    {
        return $this->hasOne(ImCatCausalImpedimento::class, 'id_causal_impedimento', 'id_causal_impedimento');
    }

    function people()
    {
        return $this->hasOne(ImPersona::class, 'id_persona', 'id_persona');
    }
    function fathers()
    {
        return $this->hasOne(ImPersonaPadre::class, 'id_persona', 'id_persona');
    }

    public function cat_subcausal_impedimento()
    {
        return $this->hasOne(ImCatSubCausalImpedimento::class, 'id_causal_impedimento', 'id_causal_impedimento');
    }

    function low(){
        return $this->hasOne(ImImpedimentoBaja::class,'id_impedimento','id_impedimento');
    }

    function lows(){
        return $this->hasOne(ImImpedimentoBaja::class,'id_impedimento','id_impedimento');
    }

    function cat_type()
    {
        return $this->hasOne(ImCatTipoSolicitud::class, 'id_tipo_solicitud', 'id_tipo_solicitud');
    }
    function cat_status()
    {
        return $this->belongsTo(ImCatStatusSolicitud::class, 'id_estatus_impedimento', 'id_estatus_solicitud');
    }

    function cat_causal()
    {
        return $this->hasOne(ImCatCausalImpedimento::class, 'id_causal_impedimento', 'id_causal_impedimento');
    }

    function cat_subcausal()
    {
        return $this->hasOne(ImCatSubcausalImpedimento::class, 'id_subcausal_impedimento', 'id_subcausal_impedimento');
    }

    // public function getCreatedAtAttribute($value)
    // {
    //     return Carbon::parse($value)->format('d/m/Y H:i');
    // }

    // public function getUpdatedAtAttribute($value)
    // {
    //     return Carbon::parse($value)->format('d/m/Y H:i');
    // }

    function documents(){
        return $this->hasMany(ImImpedimentoDocumento::class,'id_impedimento','id_impedimento');
    }

    public function cat_user_elaboro()
    {
        return $this->hasOne(User::class, 'id_usuario_elaboro', 'id');
    }

    public function cat_user_reviso()
    {
        return $this->hasOne(User::class, 'id_usuario_elaboro', 'id');
    }

     /** Usuario que elaboró */
    public function usuarioElaboro()
    {
        // FK en im_impedimento: id_usuario_elaboro -> users.id
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

    public function cat_user_autorizo()
    {
        return $this->hasOne(User::class, 'id_usuario_elaboro', 'id');
    }

    public function cat_user_altas()
    {
        return $this->hasOne(User::class, 'id_usuario_elaboro', 'id');
    }

    public function scopeSearch($query, $filters)
    {
        return $query->where(function ($q) use ($filters) {

            $q->when(!empty($filters->from) && !empty($filters->to), function ($query) use ($filters) {
                $fechaInicio = \Carbon\Carbon::createFromFormat('d-m-Y', $filters->from)->startOfDay();
                $fechaFin = \Carbon\Carbon::createFromFormat('d-m-Y', $filters->to)->endOfDay();
                $query->whereBetween('im_impedimento.created_at', [$fechaInicio, $fechaFin]);
            });

            $q->when(!empty($filters->numero_impedimento), function ($query) use ($filters) {
                $query->where('im_impedimento.id_impedimento', $filters->numero_impedimento);
            });
            $q->when(!empty($filters->id_oficina), function ($query) use ($filters) {
                $query->whereIn('im_impedimento.id_oficina', $filters->id_oficina);
            });
            $q->when(!empty($filters->id_causal_impedimento), function ($query) use ($filters) {
                $query->whereIn('im_impedimento.id_causal_impedimento', $filters->id_causal_impedimento);
            });

            $q->when(!empty($filters->id_estatus_solicitud), function ($query) use ($filters) {
                $ids = array_map('intval', (array) $filters->id_estatus_solicitud);

                // Filtro directo sobre el padre (Impedimento)
                $query->whereIn('id_estatus_impedimento', $ids);
            });



            $q->when(!empty($filters->nombres), function ($query) use ($filters) {
                $query->whereRelation('people', function ($q2) use ($filters) {
                    $q2->whereRaw("unaccent(nombres) ilike unaccent('%" . $filters->nombres . "%')");
                });
            });

            $q->when(!empty($filters->primer_apellido), function ($query) use ($filters) {
                $query->whereRelation('people', function ($q2) use ($filters) {
                    $q2->whereRaw("unaccent(primer_apellido) ilike unaccent('%" . $filters->primer_apellido . "%')");
                });
            });

            $q->when(!empty($filters->segundo_apellido), function ($query) use ($filters) {
                $query->whereRelation('people', function ($q2) use ($filters) {
                    $q2->whereRaw("unaccent(segundo_apellido) ilike unaccent('%" . $filters->segundo_apellido . "%')");
                });
            });
            $q->when(!empty($filters->curp), function ($query) use ($filters) {
                $query->whereRelation('people', function ($q2) use ($filters) {
                    $q2->whereRaw("unaccent(curp) ilike unaccent('%" . $filters->curp . "%')");
                });
            });

            $q->when(!empty($filters->id_tipo_solicitud), function ($query) use ($filters) {
                $query->whereHas('requests.cat_type', function ($sub) use ($filters) {
                    $sub->where('id_tipo_solicitud', $filters->id_tipo_solicitud);
                });
            });
        });
    }

    public $snapshotRelationsMap = [
        'cat_status' => 'estatus_solicitud',
        'cat_subcausal' => 'subcausal_impedimento',
        'cat_causal_impedimento' => 'causal_impedimento',
        'cat_office' => 'cad_oficina',
        'cat_type' => 'tipo_solicitud'
    ];

    public function getFirstSolicitudRelacion(string $relacion)
{
    return $this->requests->first()?->$relacion;
}

}
