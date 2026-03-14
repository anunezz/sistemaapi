<?php

namespace App\Models;

use App\Casts\CleanText;
use Illuminate\Database\Eloquent\Model;
use App\Models\Catalogs\ImCatGeneralGenero;
use Carbon\Carbon;

class ImPersonaBitacora extends Model
{
    
    protected $table = 'im_persona_bitacora';
    protected $primaryKey = 'id_persona_bitacora';
    protected $fillable = [
        'id_persona_bitacora',
        'id_persona',
        'id_persona_consolidada',
        'nombres',
        'primer_apellido',
        'segundo_apellido',
        'correo_electronico',
        'fecha_nacimiento',
        'curp',
        'id_genero',
        'entidad_federativa_nacimiento',
        'id_pais_nacimiento',
        'id_entidad_federativa_nacimiento',
        'id_municipio_nacimiento',
        'bol_eliminado',
        'id_usuario_alta',
        'id_usuario_modificacion',
        'nombres_padre',
        'primer_apellido_padre',
        'segundo_apellido_padre',
        'nombres_madre',
        'primer_apellido_madre',
        'segundo_apellido_madre',
    ];

    protected $appends = ['full_name','expand','format_fecha_nacimiento'];

    protected function casts(): array
    {
        return [
            'persona_consolidada' => CleanText::class,
            'nombres' => CleanText::class,
            'primer_apellido' => CleanText::class,
            'segundo_apellido' => CleanText::class,
            'correo_electronico' => CleanText::class,
            'genero' => CleanText::class,
            'entidad_federativa_nacimiento' => CleanText::class,
            'nombres_padre' => CleanText::class,
            'primer_apellido_padre' => CleanText::class,
            'segundo_apellido_padre' => CleanText::class,
            'nombres_madre' => CleanText::class,
            'primer_apellido_madre' => CleanText::class,
            'segundo_apellido_madre' => CleanText::class,
        ];
    }

    public function getFullNameAttribute(): string
    {
        return "$this->nombres $this->primer_apellido $this->segundo_apellido";
    }

    public function getFormatFechaNacimientoAttribute()
    {
        if (!$this->fecha_nacimiento) {
            return null;
        }

        return Carbon::parse($this->fecha_nacimiento)->format('d-m-Y');
    }

    public function getExpandAttribute()
    {
        return false;
    }

    function people_fathers(){
        return $this->hasOne(ImPersonaPadre::class,'id_persona','id_persona');
    }

    function impediment(){
        return $this->hasOne(ImImpedimento::class,'id_persona','id_persona');
    }

    function genre() {
        return $this->hasOne(ImCatGeneralGenero::class,'id','id_genero');
    }

    public function scopeSearch($query,$request){
        return $query->when(isset($request->curp) && $request->curp !== null, function ($query) use ($request) {
            return $query->where('curp',$request->curp);
        })
        ->where('nombres',$request->nombres)
        ->where('primer_apellido',$request->primer_apellido)
        ->orderBy('created_at','DESC');
    }

}
