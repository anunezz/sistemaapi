<?php

namespace App\Http\Controllers;

use App\Models\Catalogs\ImCatOficina;
use App\Models\Catalogs\ImCatPerfil;
use App\Models\Permission;
use Illuminate\Http\Request;

class CatalogController extends Controller
{
    public function index(Request $request){


        try{
            $catalogs = [];

            if($request->has('catPerfil') && $request->input('catPerfil') == true){
                $catalogs['cat_perfil'] = ImCatPerfil::where('bol_eliminado',false)->with('permissions')->orderBy('perfil','ASC')->get();
            }

            if($request->has('catOficina') && $request->input('catOficina') == true){
                $catalogs['cat_oficina'] = ImCatOficina::select('id_oficina as value','cad_oficina as label')->porTipoActivas(2)->orderBy('cad_oficina','ASC')->get();
            }

            if($request->has('permission') && $request->input('permission') == true){
                $catalogs['permission'] = Permission::orderBy('display_name','ASC')->get();
            }

            return response()->json([
				'data'=>$catalogs,
			], 200);


        }catch(\Exception $e){
            return response()->json([
				'error' => $e->getMessage(),
			], 400);
        }
        }
}
