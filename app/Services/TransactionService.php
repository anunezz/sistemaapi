<?php

namespace App\Services;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class TransactionService
{

public static function guardarSnapshotBitacora($modeloOriginal, $bitacoraModelClass, $extra = [])
{
    $datos = $modeloOriginal->toArray();

    // Combinas con extra
    $datos = array_merge($datos, $extra);


    // Campos a convertir si tienen formato dd/mm/yyyy hh:mm
    foreach (['created_at', 'updated_at'] as $campoFecha) {
        if (isset($datos[$campoFecha]) && is_string($datos[$campoFecha])) {
            $fechaCarbon = Carbon::createFromFormat('d/m/Y H:i', $datos[$campoFecha]);
            if ($fechaCarbon) {
                $datos[$campoFecha] = $fechaCarbon->format('Y-m-d H:i:s');
            }
        }
    }

    $datos['id_' . $modeloOriginal->getTable()] = $modeloOriginal->getKey();
    $bitacoraRegistro = $bitacoraModelClass::create($datos);

    return $bitacoraRegistro;
}

}
