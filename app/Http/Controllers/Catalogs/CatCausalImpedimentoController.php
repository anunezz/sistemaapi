<?php

namespace App\Http\Controllers\Catalogs;

use App\Http\Controllers\Controller;
use App\Http\Requests\Catalogs\CatCausalImpedimentoRequest;
use App\Http\Resources\Catalogs\CatCausalImpedimentoResource;
use App\Services\Catalogs\CatCausalImpedimentoService;
use App\Traits\BinnacleTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CatCausalImpedimentoController extends Controller
{
    use BinnacleTrait;
    protected $service;

    public function __construct()
    {
        $this->service = app(CatCausalImpedimentoService::class);
    }

    public function index(Request $request)
    {
        try {
            $catalogs = $this->service->getAll($request);

            return response()->json([
                'success' => true,
                'data' => new CatCausalImpedimentoResource($catalogs),
            ], 200);
        } catch (\Exception $e) {
            return $this->handleError($e);
        }
    }

    public function store(CatCausalImpedimentoRequest $request)
    {  
            $catalog = $this->service->create($request->only('causal_impedimento'));
            $this->guardarMovimiento(Auth::user()->id,10,3,'Se creó la causal con ID: '.$catalog->id_causal_impedimento.', nombre: "'.$catalog->causal_impedimento.'", en el catalogo: Causales');
            return response()->json([
                'success' => true,
                'data' => new CatCausalImpedimentoResource($catalog),
            ], 200);
    }

    public function update(CatCausalImpedimentoRequest $request, $id)
    {
            $catalog = $this->service->update($id, $request->only('causal_impedimento', 'bol_eliminado', 'validate_high'));

            if ($catalog) {
                $this->guardarMovimiento(Auth::user()->id,10,2,'Se actualizó la causal con ID: '.$catalog->id_causal_impedimento.', nombre: "'.$catalog->causal_impedimento.'", en el catalogo: Causales');
                return response()->json([
                    'success' => true,
                    'data' => new CatCausalImpedimentoResource($catalog),
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
