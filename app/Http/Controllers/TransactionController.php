<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ExportTransacciones; // Asegúrate de tener esta clase
use Illuminate\Support\Facades\DB;
use App\Models\Transaccion;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\CatTiposTransaccion;
use App\Models\CatModulo;
use Carbon\Carbon;

class TransactionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
public function index(Request $request)
{
    try {
        $data = $request->all();

        $params = $request->input('params', []);

        $page = $params['page'] ?? 1;
        $rowsPerPage = $params['rowsPerPage'] ?? 10;
        $transactions = Transaccion::with(['cat_tipos_transaccion', 'user', 'cat_modulo'])
                    ->orderBy('created_at', 'desc')
                    ->when(isset($data['id_user']) && $data['id_user'] !== null, function ($query) use ($data) {
                                $query->where('user_id',$data["id_user"]);
                            })
                        ->when(isset($data['id_movement']) && $data['id_movement'] !== null, function ($query) use ($data) {
                            return $query->where('cat_transaction_type_id',$data['id_movement']);
                        })
                        ->when(isset($data['id_module']) && $data['id_module'] !== null, function ($query) use ($data) {
                            return $query->where('cat_module_id',$data['id_module']);
                        })
                        ->when(isset($data['date']) && $data['date'] !== null,
                            function ($query) use ($data) {
                                if(is_array($data['date']) == false){
                                    $date = $data['date'];
                                    unset($data['date']);
                                    $data['date']['from'] = $date;
                                    $data['date']['to'] = $date;
                                }
                                $dateFrom = Carbon::parse($data['date']['from'])->format('Y-m-d');
                                $dateFrom = $dateFrom." 00:00:00";
                                $dateTo = Carbon::parse($data['date']['to'])->format('Y-m-d');
                                $dateTo = $dateTo." 23:59:59";
                                $query->whereBetween('created_at', [$dateFrom, $dateTo]);
                        })
                    ->paginate($rowsPerPage, ['*'], 'page', $page);

                    $userCount = (isset($data['id_user']) && $data['id_user'] !== null)
                    ? Transaccion::whereIn('user_id', is_array($data['id_user']) ? $data['id_user'] : [$data['id_user']])->count()
                    : null;

        return response()->json([
            'success' => true,
            'Results' => [
                'transactions' => $transactions,
                'userCount' => $userCount
            ],
        ], 200);
    } catch (\Throwable $th) {
        return response()->json([
            'success' => false,
            'message' => 'Error al obtener los registros',
            'error' => $th->getMessage()
        ], 500);
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
            DB::beginTransaction();
            try {
                $data = $request->all();
                $action = $data['action'] ?? null;
                $moduleId = $data['moduleId'] ?? null;
                $typeTransactionId = $data['typeTransactionId'] ?? null;

                if (!$moduleId || !$typeTransactionId) {
                    return response()->json(['error' => 'Datos incompletos'], 400);
                }

                $transaccion = Transaccion::create([
                    'user_id' => Auth::id(),
                    'cat_transaction_type_id' => $typeTransactionId,
                    'action' => $action,
                    'cat_module_id' => $moduleId,
                ]);
                DB::commit();
            return response()->json(['mensaje' => 'Transacción registrada con éxito']);

        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json(['error' => 'Error al guardar la transacción', 'errormessage' => $e], 500);
        }

        }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    public function get_cats_binnacle(Request $request)
    {
    try{
            return response()->json([
                    'success' => true,
                    'Results' => [
                        'catUsers' => User::where('usuario_directorio_activo', 1)->orderBy('username','asc')->get(),
                        'catTransactionTypes' => CatTiposTransaccion::orderBy('name','asc')->get(),
                        'catModules' => CatModulo::orderBy('name','asc')->get()
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

    public function get_export(Request $request)
{
    try {
        $data = $request->all();

        $transacciones = Transaccion::with(['cat_tipos_transaccion', 'user', 'cat_modulo'])
            ->orderBy('created_at', 'desc')
            ->when(isset($data['id_user']) && $data['id_user'] !== null, function ($query) use ($data) {
                $query->where('user_id', $data["id_user"]);
            })
            ->when(isset($data['id_movement']) && $data['id_movement'] !== null, function ($query) use ($data) {
                return $query->where('cat_transaction_type_id', $data['id_movement']);
            })
            ->when(isset($data['id_module']) && $data['id_module'] !== null, function ($query) use ($data) {
                return $query->where('cat_module_id', $data['id_module']);
            })
            ->when(isset($data['date']) && $data['date'] !== null, function ($query) use ($data) {
                if (!is_array($data['date'])) {
                    $date = $data['date'];
                    $data['date'] = [
                        'from' => $date,
                        'to' => $date,
                    ];
                }
                $dateFrom = Carbon::parse($data['date']['from'])->startOfDay();
                $dateTo = Carbon::parse($data['date']['to'])->endOfDay();

                $query->whereBetween('created_at', [$dateFrom, $dateTo]);
            })
            ->get(); // Sin paginación

        return Excel::download(
            new ExportTransacciones($transacciones), // <- Export class con lógica de formato
            'Bitacora.xlsx'
        );
    } catch (\Throwable $th) {
        return response()->json([
            'success' => false,
            'message' => 'Error al exportar: ' . $th->getMessage(),
        ], 500);
    }
}
}
