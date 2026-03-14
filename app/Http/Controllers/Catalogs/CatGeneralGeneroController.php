<?php

namespace App\Http\Controllers\Catalogs;

use App\Http\Controllers\Controller;
use App\Http\Requests\Catalogs\CatGeneralGeneroRequest;
use App\Http\Resources\Catalogs\CatGeneralGeneroResource;
use App\Services\Catalogs\CatGeneralGeneroService;
use App\Traits\BinnacleTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CatGeneralGeneroController extends Controller
{
    use BinnacleTrait;
    protected $service;

    public function __construct()
    {
        $this->service = app(CatGeneralGeneroService::class);
    }

    public function index(Request $request)
    {
        try {
            $catalogs = $this->service->getAll($request);

            return response()->json([
                'success' => true,
                'data' => new CatGeneralGeneroResource($catalogs),
            ], 200);
        } catch (\Exception $e) {
            return $this->handleError($e);
        }
    }

    public function store(CatGeneralGeneroRequest $request)
    {
            $catalog = $this->service->create($request->only('id_genero', 'genero'));
            $this->guardarMovimiento(Auth::user()->id,10,3,'Se creó el estatus con ID: '.$catalog->id_genero.', nombre: "'.$catalog->genero.'", en el catalogo: General');
            return response()->json([
                'success' => true,
                'data' => new CatGeneralGeneroResource($catalog),
            ], 200);
    }

    public function update(CatGeneralGeneroRequest $request, $id)
    {
            $catalog = $this->service->update($id, $request->only('id_genero', 'genero', 'bol_eliminado'));

            if ($catalog) {
                 $this->guardarMovimiento(Auth::user()->id,10,2,'Se actualizó el estatus con ID: '.$catalog->id_genero.', nombre: "'.$catalog->genero.'", en el catalogo: General');
                return response()->json([
                    'success' => true,
                    'data' => new CatGeneralGeneroResource($catalog),
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
