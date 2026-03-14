<?php

namespace App\Exports\Reports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\BeforeSheet;
use Maatwebsite\Excel\Concerns\WithStartRow;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class StatisticalExport implements
    FromCollection,
    WithHeadings,
    WithMapping,
    WithStyles,
    ShouldAutoSize,
    WithEvents,
    WithStartRow
{
    use Exportable;

    /** @var Collection */
    protected Collection $rows;

    /** @var object filtros / contexto (ANTES era queryable) */
    protected object $queryable;

    /** @var bool */
    protected bool $byUser = false;

    public function __construct(Collection $rows, object $queryable)
    {
        $this->rows      = $rows;
        $this->queryable = $queryable;
        $this->byUser    = (bool) ($queryable->byuser ?? false);
    }

    /* ======================
       DATA
    ====================== */
    public function collection(): Collection
    {
        return $this->rows;
    }

    /* ======================
       HEADERS
    ====================== */
    public function headings(): array
    {
        $headers = [
            'DIA',
            'OFICINA',
        ];

        if ($this->byUser) {
            $headers[] = 'USUARIO';
        }

        return array_merge($headers, [
            'ALTA',
            'BAJA',
            'VERIFICACION',
            'MODIFICACION',
        ]);
    }

    /* ======================
       ROW MAPPING
    ====================== */
    public function map($row): array
    {
        $data = [
            $row->dia ?? '-',                 // blindaje
            $row->nombre_corto ?? '-',
        ];

        if ($this->byUser) {
            $data[] = $row->usuario ?? '-';
        }

        return array_merge($data, [
            $row->alta ?? '0',
            $row->baja ?? '0',
            $row->verificacion ?? '0',
            $row->modificacion ?? '0',
        ]);
    }

    /* ======================
       FILTROS ARRIBA (IGUAL QUE ANTES)
    ====================== */
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
                    if ($key === 'byuser') {
                        continue;
                    }

                    if ($key === 'date')  $key = 'FECHA';
                    if ($key === 'from')  $key = 'FECHA DE INICIO';
                    if ($key === 'to')    $key = 'FECHA DE FIN';
                    if ($key === 'month') $key = 'MES';
                    if ($key === 'year')  $key = 'AÑO';

                    $sheet->setCellValue(
                        "A{$row}",
                        strtoupper(str_replace('_', ' ', preg_replace('/^id_/', '', $key)))
                    );

                    $row++;
                }

                // línea en blanco como antes
                $sheet->setCellValue("A{$row}", '');
            },
        ];
    }

    /* ======================
       OFFSET (MISMA LÓGICA ORIGINAL)
    ====================== */
    public function startRow(): int
    {
        $count = count((array) $this->queryable);

        // no contar byuser porque no se imprime
        if (isset($this->queryable->byuser)) {
            $count--;
        }

        return $count + 3;
    }

    /* ======================
       STYLES (HEADER AZUL CORRECTO)
    ====================== */
    public function styles(Worksheet $sheet)
    {
        // header está UNA fila antes del startRow
        $headerRow = $this->startRow() ;

        return [
            $headerRow => [
                'font' => [
                    'bold' => true,
                    'color' => ['rgb' => 'FFFFFF'],
                ],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '0E9AFF'],
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical'   => Alignment::VERTICAL_CENTER,
                ],
            ],
        ];
    }
}
