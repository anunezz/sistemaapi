<?php

namespace App\Models;

use App\Casts\CleanText;
use App\Models\Catalogs\ImCatEntidadFederativa;
use App\Models\Catalogs\ImCatMunicipio;
use App\Models\Catalogs\ImCatPais;
use Illuminate\Database\Eloquent\Model;
use App\Models\Catalogs\ImCatGeneralGenero;
use Carbon\Carbon;

class ImPersona extends Model
{
    protected $table = 'im_persona';
    protected $primaryKey = 'id_persona';
    protected $fillable = [
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
        'id_municipio_nacimiento',
        'bol_eliminado',
        'id_usuario_alta',
        'id_usuario_modificacion'
    ];

    protected function casts(): array
    {
        return [
            'nombres' => CleanText::class,
            'primer_apellido' => CleanText::class,
            'segundo_apellido' => CleanText::class,
            'correo_electronico' => CleanText::class,
            'curp' => CleanText::class,
            'entidad_federativa_nacimiento' => CleanText::class,
        ];
    }

    protected $appends = ['full_name','expand','format_fecha_nacimiento'];

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

    public function entidadFederativa()
    {
        return $this->belongsTo(ImCatEntidadFederativa::class, 'entidad_federativa_nacimiento', 'id_entidad_federativa');
    }

    // Relación con catálogo de País
    public function paisNacimiento()
    {
        return $this->belongsTo(ImCatPais::class, 'id_pais_nacimiento', 'id_cat_pais');
    }

    // Relación con catálogo de Municipio
    public function municipioNacimiento()
    {
        return $this->belongsTo(ImCatMunicipio::class, 'id_municipio_nacimiento', ownerKey: 'id_municipio');
    }
    public function scopeSearch($query, $request) {
        // Campos obligatorios
        $query->where('bol_eliminado', false)
              ->where('nombres', $request->nombres)
              ->where('primer_apellido', $request->primer_apellido);

        // Campos opcionales (pero con coincidencia si existen)
        $query->when(!empty($request->segundo_apellido), function ($query) use ($request) {
            $query->orWhere('segundo_apellido', $request->segundo_apellido);
        });

        $query->when(!empty($request->fecha_nacimiento), function ($query) use ($request) {
            $query->orWhere('fecha_nacimiento', $request->fecha_nacimiento);
        });

        $query->when(!empty($request->entidad_federativa_nacimiento), function ($query) use ($request) {
            $query->orWhere('entidad_federativa_nacimiento', $request->entidad_federativa_nacimiento);
        });

        // Opcional: filtrar por curp si existe
        $query->when(isset($request->curp) && $request->curp !== null, function ($query) use ($request) {
            $query->where('curp', $request->curp);
        });

        // Ordenamiento
        return $query->orderBy('created_at', 'DESC');
    }

    public function scopeSearchService($query, $request) {
        $nombre  = $this->normalizeText($request->nombres ?? null);
        $paterno = $this->normalizeText($request->primer_apellido ?? null);
        $materno = $this->normalizeText($request->segundo_apellido ?? null);
        $entidad_federativa_nacimiento = $this->normalizeText($request->entidad_federativa_nacimiento ?? null);
        $curp = $this->normalizeText($request->curp ?? null);

        $query->where('bol_eliminado', false)
        ->whereRaw(
            "regexp_replace(upper(trim(nombres)), '\s+', ' ', 'g') = ?",
            [$nombre]
        )
        ->whereRaw(
            "regexp_replace(upper(trim(primer_apellido)), '\s+', ' ', 'g') = ?",
            [$paterno]
        );

        $query->when(!empty($materno), function ($query) use ($materno) {
            $query->whereRaw(
                "regexp_replace(upper(trim(segundo_apellido)), '\s+', ' ', 'g') = ?",
                [$materno]
            );
        });

        $query->when(!empty($request->fecha_nacimiento), function ($query) use ($request) {
            //dd($request->fecha_nacimiento);
            $query->whereDate('fecha_nacimiento', $request->fecha_nacimiento);
        });

        $query->when(!empty($entidad_federativa_nacimiento), function ($query) use ($entidad_federativa_nacimiento) {
            $query->whereRaw(
                "regexp_replace(upper(trim(entidad_federativa_nacimiento)), '\s+', ' ', 'g') = ?",
                [$entidad_federativa_nacimiento]
            );
        });

        $query->when(isset($curp) && $curp, function ($query) use ($curp) {
            // dd('prueba si entro curp',$curp);
            $query->orWhereRaw(
                "regexp_replace(upper(trim(curp)), '\\s+', ' ', 'g') = ?",
                [$curp]
            );
        });

        // dd(args: 'no entro', $curp);
        return $query->orderBy('created_at', 'DESC');
    }

    protected function normalizeText(?string $value): ?string
    {
        if ($value === null) {
            return null;
        }

        // Quita espacios al inicio/fin
        $value = trim($value);

        // Convierte espacios repetidos en uno solo
        $value = preg_replace('/\s+/', ' ', $value);

        // Pasa todo a mayúsculas (para comparar sin distinguir mayúsculas/minúsculas)
        return mb_strtoupper($value, 'UTF-8');
    }


}
