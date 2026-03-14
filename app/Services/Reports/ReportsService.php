<?php

namespace App\Services\Reports;

use App\Exports\Reports\ImpedimentoExport;
use App\Exports\Reports\ReportsExport;
use App\Models\ImImpedimento;
use App\Models\ImSolicitud;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use Exception;


class ReportsService
{
public function getPersonaSolicitud(Request $request)
{
    try {
  
        Log::info('📩 Iniciando getPersonaSolicitud', [
            'action' => $request->action ?? null,
            'filters' => $request->filters ?? null
        ]);

        $filters = collect($request->filters)
            ->reject(function ($value) {
                return is_null($value) || $value === '' || $value === [] || $value === false;
            })
            ->toArray();

        $filters = (object)$filters;

        Log::debug('Filtros aplicados', (array)$filters);

        // ==========================================================
        //  EXPORT A EXCEL
        // ==========================================================
        if ($request->filled("action") && $request->action == "export") {
            Log::info('Generando Excel', ['type_report' => $filters->type_report ?? null]);

            if ($filters->type_report == 1) {
                return Excel::download(new ReportsExport($filters), 'Solicitud.xlsx');
            }

            if ($filters->type_report == 2) {
                return Excel::download(new ImpedimentoExport($filters), 'Impedimento.xlsx');
            }
        }

        // ==========================================================
        //  GENERACIÓN DE PDF
        // ==========================================================
        if ($request->filled("action") && $request->action == "print") {
            Log::info('Generando PDF', ['type_report' => $filters->type_report ?? null]);

            if ($filters->type_report == 1) {
                $report = ImSolicitud::with('cat_office', 'cat_status', 'cat_causal_impedimento')
                    ->search($filters)
                    ->get();

                if (!view()->exists('reports.report_pdf')) {
                    return response()->json(['message' => 'No se encontró la vista PDF'], 500);
                }

                $pdf = Pdf::loadView('reports.report_pdf', ['report' => $report]);

                return $pdf->download('Solicitud.pdf');
            }

            if ($filters->type_report == 2) {
                $report = ImImpedimento::with('requests', 'cat_office', 'cat_causal_impedimento')
                    ->search($filters)
                    ->get();

                if ($report->count() > 0) {
                }

                if (!view()->exists('reports.impediment_pdf')) {
                    return response()->json(['message' => 'No se encontró la vista PDF'], 500);
                }

                $pdf = Pdf::loadView('reports.impediment_pdf', ['report' => $report]);

                return $pdf->download('Impedimento.pdf');
            }

        }

        // ==========================================================
        //  LISTADO NORMAL
        // ==========================================================
        if ($filters->type_report == 1) {
            Log::info('Listando solicitudes sin exportar');
            return ImSolicitud::with('cat_office', 'cat_status', 'cat_causal_impedimento')
                ->search($filters)
                ->paginate($request->get('rowsPerPage', 10));
        }

        if ($filters->type_report == 2) {
            Log::info('Listando impedimentos sin exportar');
            return ImImpedimento::with('requests', 'cat_office', 'cat_status', 'cat_causal_impedimento')
                ->search($filters)
                ->paginate($request->get('rowsPerPage', 10));
        }

        Log::warning('⚠️ Acción no reconocida en getPersonaSolicitud', [
            'action' => $request->action ?? null,
            'filters' => $filters
        ]);

        return response()->json(['message' => 'Acción no válida'], 400);

    } catch (Exception $e) {

        Log::error('❌ Error en getPersonaSolicitud: '.$e->getMessage(), [
            'file' => $e->getFile(),
            'line' => $e->getLine(),
            'trace' => $e->getTraceAsString()
        ]);

        return response()->json([
            'success' => false,
            'message' => $e->getMessage()
        ], 500);
    }
}
}
