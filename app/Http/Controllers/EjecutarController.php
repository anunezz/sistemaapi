<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Artisan;
use Exception;
use App\Models\ImCatAnexos;

class EjecutarController extends Controller
{

public function comandos(Request $request){
    DB::beginTransaction();
    try{
        Artisan::call('init');
        $output = Artisan::output();  // Si quieres obtener el resultado del comando
        
        return response()->json([
            'success' => true,
            'output' => $output
        ]);

    } catch (\Exception $e) {
        DB::rollBack();
        return response()->json([
            'success' => false,
            'message' => 'Error al mostrar información ' . $e->getMessage(),
            'line'    => $e->getline(),
            'code'    => $e->getCode(),
            'file'    => $e->getFile(),
        ], 300);
    }
}
}
