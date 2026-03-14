<?php

namespace App\Http\Controllers\Catalogs;

use App\Http\Controllers\Controller;
use App\Http\Requests\Catalogs\CatEstatusSolicitudRequest;
use App\Http\Resources\Catalogs\CatEstatusSolicitudResource;
use App\Services\Catalogs\CatEstatusSolicitudService;
use App\Traits\BinnacleTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CatEstatusSolicitudController extends Controller
{
    use BinnacleTrait;
    protected $service;

    public function __construct()
    {
        $this->service = app(CatEstatusSolicitudService::class);
    }

    public function index(Request $request)
    {
        try {
            $catalogs = $this->service->getAll($request);

            return response()->json([
                'success' => true,
                'data' => new CatEstatusSolicitudResource($catalogs),
            ], 200);
        } catch (\Exception $e) {
            return $this->handleError($e);
        }
    }

    public function store(CatEstatusSolicitudRequest $request)
    {
            $catalog = $this->service->create($request->only('estatus_solicitud'));
            $this->guardarMovimiento(Auth::user()->id,10,3,'Se creó el estatus con ID: '.$catalog->id_estatus_solicitud.', nombre: "'.$catalog->estatus_solicitud.'", en el catalogo: Estatus');
            return response()->json([
                'success' => true,
                'data' => new CatEstatusSolicitudResource($catalog),
            ], 200);
    }

    public function update(CatEstatusSolicitudRequest $request, $id)
    {
            $catalog = $this->service->update($id, $request->only('estatus_solicitud', 'bol_eliminado'));

            if ($catalog) {
                $this->guardarMovimiento(Auth::user()->id,10,2,'Se actualizó el estatus con ID: '.$catalog->id_estatus_solicitud.', nombre: "'.$catalog->estatus_solicitud.'", en el catalogo: Estatus');
                return response()->json([
                    'success' => true,
                    'data' => new CatEstatusSolicitudResource($catalog),
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
