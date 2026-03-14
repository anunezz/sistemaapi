<?php

namespace App\Http\Controllers\Catalogs;

use App\Http\Controllers\Controller;
use App\Http\Requests\Catalogs\CatSubCausalImpedimentoRequest;
use App\Http\Resources\Catalogs\CatSubCausalImpedimentoResource;
use App\Services\Catalogs\CatSubCausalImpedimentoService;
use App\Traits\BinnacleTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CatSubCausalImpedimentoController extends Controller
{
    use BinnacleTrait;
    protected $service;

    public function __construct()
    {
        $this->service = app(CatSubCausalImpedimentoService::class);
    }

    public function index(Request $request)
    {
        try {
            $catalogs = $this->service->getAll($request);

            return response()->json([
                'success' => true,
                'data' => new CatSubCausalImpedimentoResource($catalogs),
            ], 200);
        } catch (\Exception $e) {
            return $this->handleError($e);
        }
    }

    public function store(CatSubCausalImpedimentoRequest $request)
    {
            $catalog = $this->service->create($request->only('id_causal_impedimento', 'subcausal_impedimento'));
            $this->guardarMovimiento(Auth::user()->id,10,3,'Se creó el subcausal con ID: '.$catalog->id_subcausal_impedimento.', nombre: "'.$catalog->subcausal_impedimento.'", en el catalogo: Subcausales');
            return response()->json([
                'success' => true,
                'data' => new CatSubCausalImpedimentoResource($catalog),
            ], 200);
    }

    public function update(CatSubCausalImpedimentoRequest $request, $id)
    {
            $catalog = $this->service->update($id, $request->only('id_causal_impedimento', 'subcausal_impedimento', 'bol_eliminado'));
            $this->guardarMovimiento(Auth::user()->id,10,2,'Se actualizó el subcausal con ID: '.$catalog->id_subcausal_impedimento.', nombre: "'.$catalog->subcausal_impedimento.'", en el catalogo: Subcausales');
            if ($catalog) {
                return response()->json([
                    'success' => true,
                    'data' => new CatSubCausalImpedimentoResource($catalog),
                ], 200);
            }

            return response()->json([
                'success' => false,
                'message' => 'Registro no encontrado.',
            ], 404);
    }

    public function destroy($id)
    {
        try {
            $deleted = $this->service->delete($id);

            if ($deleted) {
                return response()->json(['success' => true], 200);
            }

            return response()->json([
                'success' => false,
                'message' => 'Registro no encontrado.',
            ], 404);
        } catch (\Exception $e) {
            return $this->handleError($e);
        }
    }

    private function handleError(\Exception $e)
    {
        return response()->json([
            'success' => false,
            'message' => $e->getMessage(),
        ], 500);
    }
}
