<?php

namespace App\Http\Controllers;

use App\Models\Catalogs\ImCatCausalImpedimento;
use App\Models\Catalogs\ImCatEstatusSolicitud;
use App\Models\Catalogs\ImCatOficina;
use App\Models\ImCatTipoSolicitud;
use App\Models\ImImpedimentoBitacora;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ImpedimentTransactionController extends Controller
{
    public function index(Request $request){
        DB::beginTransaction();
        try{
            if($request->wantsJson()){
                $filters = (object)$request->filters;

                $params = $request->input('params', default: []);
                $page = $params['page'] ?? 1;
                $rowsPerPage = $params['rowsPerPage'] ?? 10;

                //dd($filters);

                //dd(ImImpedimentoBitacora::search($filters)->get());

                return response()->json([
                    'success' => true,
                    'Results' => [
                        "ImSolicitud" => ImImpedimentoBitacora::with([
                        "cat_type",
                        "cat_office",
                        "cat_status",
                        "cat_causal_impedimento",
                        "cat_subcausal_impedimento",
                        "requests",
                        "low",
                        "persona_bitacora.genre",
                        "cat_genero",
                        // usuarios
                        "usuarioAltas",
                        "usuarioElaboro",
                        "usuarioReviso",
                        "usuarioAutorizo",
                        "usuarioAltaBaja",
                        "usuarioModificacionBaja"
                        ])
                        // ->whereIn('id_estatus_solicitud',[10,30])
                        ->orderBy('created_at', 'desc')
                        ->search($filters)
                        ->paginate($rowsPerPage, ['*'], 'page', $page)
                    ],
                ], 200);
            }else{
                DB::rollBack();
                return response()->view('errors.404', [], 404);
            }
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
        public function get_cats_impediment_binnacle() {
             try{
            return response()->json([
                    'success' => true,
                    'Results' => [
                        'catOffice' => ImCatOficina::porTipoActivas(2)->orderBy('cad_oficina','asc')->get(),
                        'catType' => ImCatTipoSolicitud::orderBy('tipo_solicitud','asc')->get(),
                        'catEstatus' => ImCatEstatusSolicitud::orderBy('estatus_solicitud','asc')->get(),
                        'catCausal' => ImCatCausalImpedimento::orderBy('causal_impedimento','asc')->get()
                        // WhereNotIn('id',[3,4,5,6,7])
                    ]
            ], 200);
        } catch (Exception $e) {
            return response()->json([
            'success' => false,
                'message' => 'Error al mostrar información ' . $e->getMessage(),
                'line'    => $e->getline(),
                'code'    => $e->getCode(),
                'file'    => $e->getFile(),
                'trace'   => $e->getTrace()
            ], 300);
        }
        }
}
