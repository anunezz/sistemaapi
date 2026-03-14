<?php

namespace App\Http\Controllers;

use App\Models\Catalogs\ImCatCausalImpedimento;
use App\Models\Catalogs\ImCatEstatusSolicitud;
use App\Models\Catalogs\ImCatOficina;
use App\Models\ImCatTipoSolicitud;
use App\Models\ImSolicitudBitacora;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ApplicationTransactionController extends Controller
{
        /**
     * Display a listing of the resource.
     */
public function index(Request $request){
        DB::beginTransaction();
        try{
                $params = $request->input('params', default: []);
                $page = $params['page'] ?? 1;
                $rowsPerPage = $params['rowsPerPage'] ?? 10;

            if($request->wantsJson()){
                $filters = (object)$request->input(key: 'filters');
                return response()->json([
                    'success' => true,
                    'Results' => [
                        "ImSolicitud" => ImSolicitudBitacora::with([
                            "cat_priority",
                            "cat_office",
                            "cat_status",
                            "cat_causales",
                            "cat_type",
                            "cat_causal_impedimento",
                            "usuario_modificacion"
                        ])
                        // ->whereIn('id_estatus_solicitud',[10,30])
                        ->orderByDesc('urgencia')
                        ->orderBy('updated_at', 'desc')
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

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
        public function store(Request $request)
        {
            // DB::beginTransaction();
            // try {            
            //     $data = $request->all();
            //     $action = $data['action'] ?? null;
            //     $moduleId = $data['moduleId'] ?? null;
            //     $typeTransactionId = $data['typeTransactionId'] ?? null;
                
            //     if (!$moduleId || !$typeTransactionId) {
            //         return response()->json(['error' => 'Datos incompletos'], 400);
            //     }
                
            //     $transaccion = Transaccion::create([
            //         'user_id' => Auth::id(),
            //         'cat_transaction_type_id' => $typeTransactionId,
            //         'action' => $action, 
            //         'cat_module_id' => $moduleId, 
            //     ]);
            //     DB::commit();
            // return response()->json(['mensaje' => 'Transacción registrada con éxito']);

        // } catch (\Throwable $e) {
        //     DB::rollBack();
        //     return response()->json(['error' => 'Error al guardar la transacción', 'errormessage' => $e], 500);
        // }

        }

        public function get_cats_application_binnacle() {
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
