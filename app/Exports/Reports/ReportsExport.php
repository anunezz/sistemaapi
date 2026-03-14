<?php

namespace App\Exports\Reports;

use App\Models\ImSolicitud;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\BeforeSheet;
use Maatwebsite\Excel\Concerns\WithStartRow;

class ReportsExport implements FromQuery, WithHeadings, WithMapping, WithStyles, ShouldAutoSize, WithEvents, WithStartRow
{
    use Exportable;

    protected $queryable;

    function __construct($queryable) {
        $this->queryable = $queryable;
    }

    public function query()
    {
        return ImSolicitud::with('cat_office', 'cat_status', 'cat_causal_impedimento')->search($this->queryable);
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

    public function headings(): array
    {
        return [
            'ID SOLICITUD',
            'NOMBRE',
            'OFICINA',
            'FECHA DE ALTA',
            'OBSERVACIONES',
            'MOTIVO',
        ];
    }

    public function map($invoice): array
    {
        return [
            $invoice->id_solicitud,
            $invoice->full_name,
            $invoice->cat_office->nombre_corto,
            $invoice->created_at,
            $invoice->observaciones,
            $invoice->cat_causal_impedimento?->causal_impedimento,
        ];
    }

    public function registerEvents(): array
    {
        return [
            BeforeSheet::class => function (BeforeSheet $event) {
                $sheet = $event->sheet;

                $row = 1;
                $queryable = $this->queryable;
                $sheet->setCellValue("A{$row}", 'FILTROS APLICADOS');
                $sheet->getStyle("A{$row}")->getFont()->setBold(true);
                $row++;

                foreach ($queryable as $key => $value) {
                    if($key === 'date') $key = 'FECHA';
                    if($key === 'from') $key = 'FECHA DE INICIO';
                    if($key === 'to') $key = 'FECHA DE FIN';
                    if($key === 'type_report'){
                        if($value == 1){
                            $key = 'REPORTE SOLICITUDES';
                        }
                    };

                    $sheet->setCellValue("A{$row}", strtoupper(str_replace('_', ' ', preg_replace('/^id_/', '', $key))));
                    //$sheet->setCellValue("B{$row}", is_array($value) ? '' : $value);//implode(', ', $value) : $value);
                    $row++;
                }

                $sheet->setCellValue("A{$row}", '');
            },
        ];
    }
}
