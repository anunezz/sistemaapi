<?php

namespace App\Http\Controllers\Catalogs;

use App\Http\Controllers\Controller;
use App\Http\Resources\Catalogs\CatTipoSolicitudResource;
use App\Services\Catalogs\CatTipoSolicitudService;
use Illuminate\Http\Request;

class CatTipoSolicitudController extends Controller
{
    protected $service;

    public function __construct()
    {
        $this->service = app(CatTipoSolicitudService::class);
    }

    public function index(Request $request)
    {
        try {
            $catalogs = $this->service->getAll($request);

            return response()->json([
                'success' => true,
                'data' => new CatTipoSolicitudResource($catalogs),
            ], 200);
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
