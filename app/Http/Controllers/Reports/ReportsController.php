<?php

namespace App\Http\Controllers\Reports;
use App\Http\Controllers\Controller;
use App\Services\Reports\ReportsService;
use App\Traits\PermissionsTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReportsController extends Controller
{
    use PermissionsTrait;

    protected $service;


    public function __construct()
    {
        $this->service = app(ReportsService::class);
    }

    public function getReport(Request $request)
    {
        try {
            if(!$this->hasPermission('reports')){
                return $this->responseDenied();
            }

            $report = $this->service->getPersonaSolicitud($request);

            if ($request->filled("action") && $request->action == "export") {
                return $report;
            }

            if ($request->filled("action") && $request->action == "print") {
                return $report;
            }

            return response()->json([
                'success' => true,
                'data' => $report,
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
