<?php

namespace App\Services\Reports;

use App\Exports\Reports\StatisticalExport;
use App\Models\ImSolicitud;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class StatisticalService
{
    public function getPersonaSolicitud(Request $request)
    {
        // ======================
        // FILTROS (se quedan igual)
        // ======================
        $filters = collect($request->filters ?? [])
            ->reject(
                fn($value) =>
                is_null($value) || $value === '' || $value === [] || $value === false
            )
            ->toArray();

        $filters = (object) $filters;

        $noFilters = empty((array) $filters);
        $noSearch = !$request->filled('search');

        if (
            !$request->filled('action') &&
            $noFilters &&
            $noSearch
        ) {
            return response()->json([
                'success' => true,
                'data' => [
                    'data' => [],
                    'current_page' => 1,
                    'per_page' => (int) $request->get('rowsPerPage', 10),
                    'total' => 0,
                    'last_page' => 1,
                ]
            ]);
        }

        // ======================
        // FLAGS (se quedan igual)
        // ======================
        $userIds = collect($filters->id_user ?? [])->filter()->values()->all();
        $byUser = count($userIds) > 0;
        $filters->byuser = $byUser;
        $userFkColumn = 'im_solicitud.id_usuario_alta';

        $byMonth = !empty($filters->month) && !empty($filters->year);
        $monthInt = $byMonth ? (int) $filters->month : null;
        $yearInt = $byMonth ? (int) $filters->year : null;

        // ======================
        // SELECTS (SE QUEDAN IGUAL)
        // ======================
        $baseSelectDay = [
            DB::raw("DATE(im_solicitud.created_at) as dia"),
            'im_cat_oficina.nombre_corto',
            DB::raw("COUNT(*) as total"),
            DB::raw("SUM(CASE WHEN im_solicitud.id_tipo_solicitud = 1 THEN 1 ELSE 0 END) as alta"),
            DB::raw("SUM(CASE WHEN im_solicitud.id_tipo_solicitud = 2 THEN 1 ELSE 0 END) as baja"),
            DB::raw("SUM(CASE WHEN im_solicitud.id_tipo_solicitud = 3 THEN 1 ELSE 0 END) as verificacion"),
            DB::raw("SUM(CASE WHEN im_solicitud.id_tipo_solicitud = 4 THEN 1 ELSE 0 END) as modificacion"),
        ];
        if ($byUser) {
            $baseSelectDay[] = DB::raw("users.username as usuario");
        }

        $periodGroup = DB::raw("date_trunc('month', im_solicitud.created_at)");
        $periodLabel = DB::raw("to_char(date_trunc('month', im_solicitud.created_at), 'YYYY-MM') as periodo");

        $baseSelectMonth = [
            $periodLabel,
            'im_cat_oficina.nombre_corto',
            DB::raw("COUNT(*) as total"),
            DB::raw("SUM(CASE WHEN im_solicitud.id_tipo_solicitud = 1 THEN 1 ELSE 0 END) as alta"),
            DB::raw("SUM(CASE WHEN im_solicitud.id_tipo_solicitud = 2 THEN 1 ELSE 0 END) as baja"),
            DB::raw("SUM(CASE WHEN im_solicitud.id_tipo_solicitud = 3 THEN 1 ELSE 0 END) as verificacion"),
            DB::raw("SUM(CASE WHEN im_solicitud.id_tipo_solicitud = 4 THEN 1 ELSE 0 END) as modificacion"),
        ];
        if ($byUser) {
            $baseSelectMonth[] = DB::raw("users.username as usuario");
        }

        // ======================
        // QUERY BASE (LA MISMA QUE YA JALABA)
        // ======================
        if ($byMonth) {
            $query = ImSolicitud::query()
                ->select($baseSelectMonth)
                ->join('im_cat_oficina', 'im_solicitud.id_oficina', '=', 'im_cat_oficina.id_oficina')
                ->when(
                    $byUser,
                    fn($q) =>
                    $q->join('users', 'users.id', '=', DB::raw($userFkColumn))
                        ->whereIn(DB::raw($userFkColumn), $userIds)
                )
                ->search($filters)
                ->whereYear('im_solicitud.created_at', $yearInt)
                ->whereMonth('im_solicitud.created_at', $monthInt)
                ->groupBy($periodGroup, 'im_cat_oficina.nombre_corto')
                ->when($byUser, fn($q) => $q->groupBy('users.username'))
                ->orderBy($periodGroup)
                ->when($byUser, fn($q) => $q->orderBy('users.username'));
        } else {
            $query = ImSolicitud::query()
                ->select($baseSelectDay)
                ->join('im_cat_oficina', 'im_solicitud.id_oficina', '=', 'im_cat_oficina.id_oficina')
                ->when(
                    $byUser,
                    fn($q) =>
                    $q->join('users', 'users.id', '=', DB::raw($userFkColumn))
                        ->whereIn(DB::raw($userFkColumn), $userIds)
                )
                ->search($filters)
                ->groupBy(DB::raw("DATE(im_solicitud.created_at)"), 'im_cat_oficina.nombre_corto')
                ->when($byUser, fn($q) => $q->groupBy('users.username'))
                ->orderBy('dia')
                ->when($byUser, fn($q) => $q->orderBy('users.username'));
        }

        // ======================
        // SALIDAS
        // ======================

        if ($request->action === 'export') {
            return Excel::download(
                new StatisticalExport($query->get(), $filters),
                'Reporte.xlsx'
            );
        }

        if ($request->action === 'print') {
            return Pdf::loadView('reports.statistical_pdf', [
                'report' => $query->get(),
                'filters' => $filters

            ])->download('estadistico_solicitudes.pdf');
        }

        // LISTADO NORMAL (TABLA)
        return $query->paginate($request->get('rowsPerPage', 10));
    }
}
