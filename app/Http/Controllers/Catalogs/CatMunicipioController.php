<?php

namespace App\Http\Controllers\Catalogs;

use App\Http\Controllers\Controller;
use App\Http\Requests\Catalogs\CatMunicipioRequest;
use App\Http\Resources\Catalogs\CatMunicipioResource;
use App\Services\Catalogs\CatMunicipioService;
use App\Traits\BinnacleTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CatMunicipioController extends Controller
{
    use BinnacleTrait;
    protected $service;

    public function __construct()
    {
        $this->service = app(CatMunicipioService::class);
    }

    public function index(Request $request)
    {
        try {
            $catalogs = $this->service->getAll($request);

            return response()->json([
                'success' => true,
                'data' => new CatMunicipioResource($catalogs),
            ], 200);
        } catch (\Exception $e) {
            return $this->handleError($e);
        }
    }

    public function store(CatMunicipioRequest $request)
    {
            $catalog = $this->service->create($request->only('id_entidad_federativa', 'id_pais', 'municipio'));
            $this->guardarMovimiento(Auth::user()->id,10,3,'Se creó el municipio con ID: '.$catalog->id_municipio.', nombre: "'.$catalog->municipio.'", en el catalogo: Municipio');
            return response()->json([
                'success' => true,
                'data' => new CatMunicipioResource($catalog),
            ], 200);
    }

    public function update(CatMunicipioRequest $request, $id)
    {
        try {
            $catalog = $this->service->update($id, $request->only('id_entidad_federativa', 'id_pais', 'municipio', 'bol_eliminado'));

            if ($catalog) {
                $this->guardarMovimiento(Auth::user()->id,10,2,'Se actualizó el municipio con ID: '.$catalog->id_municipio.', nombre: "'.$catalog->municipio.'", en el catalogo: Municipio');
                return response()->json([
                    'success' => true,
                    'data' => new CatMunicipioResource($catalog),
                ], 200);
            }

            return response()->json([
                'success' => false,
                'message' => 'Registro no encontrado.',
            ], 404);
        } catch (\Exception $e) {
            return $this->handleError($e);
        }
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
