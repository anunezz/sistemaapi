<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\ShouldAutoSize; 

class ExportTransacciones implements FromView, WithStyles, ShouldAutoSize 
{
    protected $transacciones;

    public function __construct($transacciones)
    {
        $this->transacciones = $transacciones;
    }

    public function view(): View
    {
        return view('exports.transacciones', [
            'transacciones' => $this->transacciones
        ]);
    }

    public function styles(Worksheet $sheet)
    {
        // Estilo encabezados (primera fila)
        $sheet->getStyle('A1:E1')->applyFromArray([
            'font' => [
                'bold' => true,
                'size' => 12,
            ],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => [
                    'argb' => 'FFEFEFEF', // Gris claro
                ],
            ],
        ]);

        // Zebra: filas pares con fondo gris más tenue
        $totalRows = count($this->transacciones) + 1; // +1 por encabezado
        for ($row = 2; $row <= $totalRows; $row++) {
            if ($row % 2 === 0) {
                $sheet->getStyle("A{$row}:E{$row}")->applyFromArray([
                    'fill' => [
                        'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                        'startColor' => [
                            'argb' => 'FFF9F9F9', // Gris aún más claro
                        ],
                    ],
                ]);
            }
        }

        return [];
    }
}
