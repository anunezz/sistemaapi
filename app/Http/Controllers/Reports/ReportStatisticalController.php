<?php

namespace App\Http\Controllers\Reports;
use App\Http\Controllers\Controller;
use App\Services\Reports\StatisticalService;
use App\Traits\PermissionsTrait;
use Illuminate\Http\Request;

class ReportStatisticalController extends Controller
{
    use PermissionsTrait;
    protected $service;

    public function __construct()
    {
        $this->service = app(StatisticalService::class);
    }

    public function getReport(Request $request)
{
    try {
        if (!$this->hasPermission('reports')) {
            return $this->responseDenied();
        }

        $result = $this->service->getPersonaSolicitud($request);

        // Excel o PDF → regresar tal cual (BinaryResponse)
        if ($request->filled('action') && in_array($request->action, ['export', 'print'])) {
            return $result;
        }

        // Listado normal → JSON
        return response()->json([
            'success' => true,
            'data' => $result,
        ], 200);

    } catch (\Throwable $e) {
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
