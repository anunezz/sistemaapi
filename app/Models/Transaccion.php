<?php

namespace App\Models;

use App\Casts\CleanText;
use Illuminate\Database\Eloquent\Model;

class Transaccion extends Model
{
    protected $fillable = ['user_id','cat_transaction_type_id','action', 'cat_module_id', 'parameters'];
    protected $appends = ['hash_id'];
    protected $table = 'im_transacciones';

    protected function casts(): array
    {
        return [
            ' action' => CleanText::class,

        ];
    }
    // Ecritpar el ID
    public function getHashIdAttribute()
    {
        return encrypt($this->id);
    }

    function user(){
        return $this->belongsTo(User::class);
    }

 public function cat_tipos_transaccion()
{
    return $this->belongsTo(CatTiposTransaccion::class, 'cat_transaction_type_id', 'id');
}

public function cat_modulo()
{
    return $this->belongsTo(CatModulo::class, 'cat_module_id', 'id');
}

}
