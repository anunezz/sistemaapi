<?php
namespace App\Exports\Reports;

use App\Models\ImImpedimento;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\BeforeSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\WithStartRow;

class ImpedimentoExport implements FromCollection, WithStyles, ShouldAutoSize, WithEvents, WithStartRow
{
    use Exportable;

    protected $queryable;

    public function __construct($queryable)
    {
        $this->queryable = $queryable;
    }

    public function collection()
    {
        $report = ImImpedimento::with(['requests.cat_type', 'requests.cat_status', 'cat_office', 'cat_causal_impedimento'])
            ->search($this->queryable)
            ->get();

        $rows = collect();

        // fila de encabezado Impedimeto
        $rows->push([
            'NUMERO DE IMPEDIMENTO',
            'OFICINA',
            'FECHA DE ALTA',
            'MOTIVO',
            '', '', '', ''
        ]);
        foreach ($report as $item) {

            // Impedimento
            $rows->push([
                $item->numero_impedimento,
                $item->cat_office->nombre_corto ?? '',
                $item->created_at,
                $item->cat_causal_impedimento->causal_impedimento ?? '',
                '', '', '', ''
            ]);

            if ($item->requests->isNotEmpty()) {
                // fila de encabezado de Solciitudes
                $rows->push(['', '', '', '', 'SOLICITUD', 'NOMBRE', 'TIPO SOLICITUD', 'FECHA REGISTRO', 'ESTATUS', 'OBSERVACIONES']);

                // Solciitudes
                foreach ($item->requests as $req) {
                    $rows->push([
                        '', '', '', '',
                        $req->id_solicitud,
                        $req->nombres . ' ' . $req->primer_apellido . ' ' . $req->segundo_apellido,
                        $req->cat_type->tipo_solicitud ?? '',
                        $req->fecha_registro,
                        $req->cat_status->estatus_solicitud ?? '',
                        $req->observaciones ?? ''
                    ]);
                }
            }

        }

        return $rows;
    }

    public function startRow(): int
    {
        return count((array) $this->queryable) + 3;
    }

    public function styles(Worksheet $sheet)
    {
        $headerRow = $this->startRow();
        return [
            $headerRow => [
                'font' => ['bold' => true],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '0e9aff'],
                ],
                'alignment' => [
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                ]
            ],
        ];
    }

    public function registerEvents(): array
    {
        return [
            BeforeSheet::class => function (BeforeSheet $event) {
                $sheet = $event->sheet;
                $row = 1;
                $sheet->setCellValue("A{$row}", 'FILTROS APLICADOS');
                $sheet->getStyle("A{$row}")->getFont()->setBold(true);
                $row++;

                foreach ($this->queryable as $key => $value) {
                    if ($key === 'date') $key = 'FECHA';
                    if($key === 'from') $key = 'FECHA DE INICIO';
                    if($key === 'to') $key = 'FECHA DE FIN';
                    if ($key === 'type_report' && $value == 2) {
                        $key = 'REPORTE IMPEDIMENTOS';
                    }
                    $sheet->setCellValue("A{$row}", strtoupper(str_replace('_', ' ', preg_replace('/^id_/', '', $key))));
                    $row++;
                }
                $sheet->setCellValue("A{$row}", '');
            }
        ];
    }
}
