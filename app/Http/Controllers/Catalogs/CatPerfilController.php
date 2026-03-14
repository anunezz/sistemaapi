<?php

namespace App\Http\Controllers\Catalogs;

use App\Http\Controllers\Controller;
use App\Http\Requests\Catalogs\CatPerfilRequest;
use App\Http\Resources\Catalogs\CatPerfilResource;
use App\Services\Catalogs\CatPerfilService;
use App\Traits\BinnacleTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CatPerfilController extends Controller
{
    use BinnacleTrait;
    protected $service;

    public function __construct()
    {
        $this->service = app(CatPerfilService::class);
    }

    public function index(Request $request)
    {
        try {
            $catalogs = $this->service->getAll($request);

            return response()->json([
                'success' => true,
                'data' => new CatPerfilResource($catalogs),
            ], 200);
        } catch (\Exception $e) {
            return $this->handleError($e);
        }
    }

    public function store(CatPerfilRequest $request)
    {
            $catalog = $this->service->create($request->only('perfil'));
            $this->guardarMovimiento(Auth::user()->id,10,3,'Se creó el perfil con ID: '.$catalog->id_perfil.', nombre: "'.$catalog->perfil.'", en el catalogo: Perfil');
            return response()->json([
                'success' => true,
                'data' => new CatPerfilResource($catalog),
            ], 200);
    }

    public function update(CatPerfilRequest $request, $id)
    {
            $catalog = $this->service->update($id, $request->only('perfil', 'bol_eliminado'));

            if ($catalog) {
                $this->guardarMovimiento(Auth::user()->id,10,2,'Se actualizó el perfil con ID: '.$catalog->id_perfil.', nombre: "'.$catalog->perfil.'", en el catalogo: Perfil');
                return response()->json([
                    'success' => true,
                    'data' => new CatPerfilResource($catalog),
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
