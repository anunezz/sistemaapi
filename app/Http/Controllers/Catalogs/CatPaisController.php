<?php

namespace App\Http\Controllers\Catalogs;

use App\Http\Controllers\Controller;
use App\Http\Requests\Catalogs\CatPaisRequest;
use App\Http\Resources\Catalogs\CatPaisResource;
use App\Services\Catalogs\CatPaisService;
use App\Traits\BinnacleTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CatPaisController extends Controller
{
    use BinnacleTrait;
    protected $service;

    public function __construct()
    {
        $this->service = app(CatPaisService::class);
    }

    public function index(Request $request)
    {
        try {
            $catalogs = $this->service->getAll($request);

            return response()->json([
                'success' => true,
                'data' => new CatPaisResource($catalogs),
            ], 200);
        } catch (\Exception $e) {
            return $this->handleError($e);
        }
    }

    public function store(CatPaisRequest $request)
    {
            $catalog = $this->service->create($request->only('idalpha2', 'idalpha3', 'cad_nombre_es'));
            $this->guardarMovimiento(Auth::user()->id,10,3,'Se creó el país con ID: '.$catalog->id_pais.', nombre: "'.$catalog->pais.'", en el catalogo: País');
            return response()->json([
                'success' => true,
                'data' => new CatPaisResource($catalog),
            ], 200);
    }

    public function update(CatPaisRequest $request, $id)
    {
            $catalog = $this->service->update($id, $request->only('idalpha2', 'idalpha3', 'cad_nombre_es', 'bol_eliminado'));

            if ($catalog) {
                $this->guardarMovimiento(Auth::user()->id,10,2,'Se actualizó el país con ID: '.$catalog->id_pais.', nombre: "'.$catalog->pais.'", en el catalogo: País');
                return response()->json([
                    'success' => true,
                    'data' => new CatPaisResource($catalog),
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
