<?php

namespace App\Http\Controllers;

use App\Models\Catalogs\ImCatSubCausalImpedimento;
use App\Models\Catalogs\ImPlantillas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Transaccion;
use Illuminate\Support\Facades\Auth;

class ImPlantillaController extends Controller
{
    private string $id_user;

    public function __construct()
    {
        $this->id_user = Auth::id();
    }

public function index(Request $request)
{
    try {
        $data = $request->all();

        $params = $request->input('params', []);

        $page = $params['page'] ?? 1;
        $rowsPerPage = $params['rowsPerPage'] ?? 10;

        return response()->json([
            'success' => true,
            'Results' => [
                'Plantillas' => ImPlantillas::with('cat_subcausal.cat_causal_impedimento')->orderBy('created_at', 'desc')
                    ->paginate($rowsPerPage, ['*'], 'page', $page)
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
public function getPlantillaById(Request $request)
{
    try {
        $data = $request->all();

        $params = $request->input('params', []);
        $page = $params['page'] ?? 1;
        $rowsPerPage = $params['rowsPerPage'] ?? 10;

        $id = $request->input('id');  // capturamos el id si viene

        if ($id) {
            // Traer sólo la plantilla por id con sus relaciones
            $plantilla = ImPlantillas::with('cat_subcausal.cat_causal_impedimento')
            ->find($id);

            if (!$plantilla) {
                return response()->json([
                    'success' => false,
                    'message' => "No se encontró la plantilla con id $id"
                ], 404);
            }

            return response()->json([
                'success' => true,
                'Results' => [
                    'Plantilla' => $plantilla
                ],
            ], 200);
        } else {
            // Traer paginación normal
            return response()->json([
                'success' => true,
                'Results' => [
                    'Plantillas' => ImPlantillas::with('cat_subcausal.cat_causal_impedimento')
                    ->orderBy('created_at', 'desc')
                    ->paginate($rowsPerPage, ['*'], 'page', $page)
                ],
            ], 200);
        }

    } catch (\Throwable $th) {
        return response()->json([
            'success' => false,
            'message' => 'Error al obtener los registros',
            'error' => $th->getMessage()
        ], 500);
    }
}


public function store(Request $request)
{
    DB::beginTransaction();
    try {
        $data = $request->all();

        // Verificar si ya existe plantilla para esta subcausal
        $existe = ImPlantillas::where('id_subcausal_impedimento', $data['id_subcausal_impedimento'])->exists();

        if ($existe) {
            return response()->json(['error' => 'Ya existe una plantilla para esta subcausal'], 422);
        }
        ImPlantillas::create([
            'plantilla' => $data['plantilla'],
            'id_subcausal_impedimento' => $data['id_subcausal_impedimento'],
            'id_causal_impedimento' => $data['id_causal_impedimento'],
        ]);

        $ImCatSubCausalImpedimento = ImCatSubCausalImpedimento::find($data['id_subcausal_impedimento']);

        Transaccion::create([
            "user_id" => $this->id_user,
            "cat_transaction_type_id" => 3,
            "cat_module_id" => 6,
            "action"=> "Se creo una plantilla del Subcausal $ImCatSubCausalImpedimento->subcausal_impedimento"
        ]);

        DB::commit();
        return response()->json(['mensaje' => 'Plantilla registrada con éxito']);

    } catch (\Throwable $e) {
        DB::rollBack();
        return response()->json(['error' => 'Error al guardar la transacción', 'errormessage' => $e], 500);
    }

}

public function update(Request $request, $id)
{
    DB::beginTransaction();
    try {
        $data = $request->all();

        $existe = ImPlantillas::where('id_subcausal_impedimento', $data['id_subcausal_impedimento'])
        ->where('id_plantilla', '!=', $id)  // Excluir el registro actual
        ->exists();

        if ($existe) {
            return response()->json(['error' => 'Ya existe una plantilla para esta subcausal'], 422);
        }

        $plantilla = ImPlantillas::findOrFail($id);
        $plantilla->plantilla = $data['plantilla'];
        $plantilla->save();

        $ImCatSubCausalImpedimento = ImCatSubCausalImpedimento::find($data['id_subcausal_impedimento']);

        Transaccion::create([
            "user_id" => $this->id_user,
            "cat_transaction_type_id" => 2,
            "cat_module_id" => 6,
            "action"=> "Se creo una plantilla del Subcausal $ImCatSubCausalImpedimento->subcausal_impedimento"
        ]);

        DB::commit();

        return response()->json(['success' => true, 'mensaje' => 'Plantilla actualizada con éxito']);

    } catch (\Throwable $e) {
        DB::rollBack();
        return response()->json(['error' => 'Error al actualizar la transacción', 'errormessage' => $e->getMessage()], 500);
    }
}
public function delete(Request $request)
{
    DB::beginTransaction();
    try {
        $data = $request->all();

        $plantilla = ImPlantillas::findOrFail($data['id']);
        $plantilla->delete();

        $ImCatSubCausalImpedimento = ImCatSubCausalImpedimento::find(  $plantilla->id_subcausal_impedimento );

        Transaccion::create([
            "user_id" => $this->id_user,
            "cat_transaction_type_id" => 4,
            "cat_module_id" => 6,
            "action"=> "Se elimino una plantilla del Subcausal $ImCatSubCausalImpedimento->subcausal_impedimento"
        ]);

        DB::commit();

        return response()->json(['success' => true, 'mensaje' => 'Plantilla eliminada con éxito']);

    } catch (\Throwable $e) {
        DB::rollBack();
        return response()->json(['error' => 'Error al actualizar la transacción', 'errormessage' => $e->getMessage()], 500);
    }
}
public function get_cat_subcausal(Request $request)
{
    try {
        $idSubcausalEditando = $request->input('id_subcausal_editando');

        // Obtener el subcausal que estás editando (si hay id)
        $subcausaleditando = null;
        if ($idSubcausalEditando) {
            $subcausaleditando = ImCatSubCausalImpedimento::with('cat_causal_impedimento')
                ->where('id_subcausal_impedimento', $idSubcausalEditando)
                ->first();
        }

        // Obtener todos los subcausales que no tienen plantilla
        $sinPlantilla = ImCatSubCausalImpedimento::with('cat_causal_impedimento')
            ->whereDoesntHave('cat_plantilla')
            ->get();

        // Si el subcausal editando existe y no está en $sinPlantilla, agregarlo
        if ($subcausaleditando && !$sinPlantilla->contains('id_subcausal_impedimento', $idSubcausalEditando)) {
            $sinPlantilla->push($subcausaleditando);
        }

        // Ordenar la colección combinada por 'subcausal_impedimento'
        $catSubcausales = $sinPlantilla->sortBy('subcausal_impedimento')->values();

        return response()->json([
            'success' => true,
            'Results' => [
                'CatSubcausal' => $catSubcausales,
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


}
