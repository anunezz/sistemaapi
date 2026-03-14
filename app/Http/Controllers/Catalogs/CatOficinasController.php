<?php

namespace App\Http\Controllers\Catalogs;

use App\Http\Controllers\Controller;
use App\Http\Requests\Catalogs\CatOficinasRequest;
use App\Http\Resources\Catalogs\CatOficinasResource;
use App\Services\Catalogs\CatOficinasService;
use App\Traits\BinnacleTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CatOficinasController extends Controller
{
    use BinnacleTrait;
    protected $service;

    public function __construct()
    {
        $this->service = app(CatOficinasService::class);
    }

    public function index(Request $request)
    {
        try {
            $catalogs = $this->service->getAll($request);

            return response()->json([
                'success' => true,
                'data' => new CatOficinasResource($catalogs),
            ], 200);
        } catch (\Exception $e) {
            return $this->handleError($e);
        }
    }

    public function store(CatOficinasRequest $request)
    {
            $catalog = $this->service->create($request->only('id_pais', 'cad_oficina', 'nombre_corto'));
            $this->guardarMovimiento(Auth::user()->id,10,3,'Se creó la oficina con ID: '.$catalog->id_oficina.', nombre: "'.$catalog->cad_oficina.'", en el catalogo: Oficina');
            return response()->json([
                'success' => true,
                'data' => new CatOficinasResource($catalog),
            ], 200);
    }

    public function update(CatOficinasRequest $request, $id)
    {
            $catalog = $this->service->update($id, $request->only('id_pais', 'cad_oficina', 'nombre_corto', 'bol_eliminado', 'id_oficina_suet','correo_electronico'));

            if ($catalog) {
                $this->guardarMovimiento(Auth::user()->id,10,2,'Se actualizó la oficina con ID: '.$catalog->id_oficina.', nombre: "'.$catalog->cad_oficina.'", en el catalogo: Oficina');
                return response()->json([
                    'success' => true,
                    'data' => new CatOficinasResource($catalog),
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
