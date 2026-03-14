<?php

namespace App\Models;

use App\Casts\CleanText;
use App\Models\Catalogs\ImCatPerfil;
use Illuminate\Database\Eloquent\Model;

class ImUsuarioPerfil extends Model
{
    protected $table = 'im_usuario_perfil';

     protected $primaryKey = 'id_usuario';

    public $incrementing = false;

    protected $fillable=['id_usuario','id_perfil','bol_eliminado','fec_alta','fec_modificacion','usuario_alta','usuario_modificacion'];

     protected function casts(): array
    {
        return [
            'usuario_alta' => CleanText::class,
            'usuario_modificacion' => CleanText::class,

        ];
    }

    const CREATED_AT = 'fec_alta';
    const UPDATED_AT = 'fec_modificacion';

    public function perfil(){
        return $this->belongsTo(ImCatPerfil::class,'id_perfil','id_perfil');
    }


}
