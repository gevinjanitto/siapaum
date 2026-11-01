<?php

namespace App\Exports\Laporan;

use App\Services\LaporanService;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class IzinTerbitExport implements FromView, ShouldAutoSize, WithEvents
{
    private $tahun, $data;
    public function __construct($tahun)
    {
        $this->tahun = $tahun;
    }
    public function view(): View
    {
        $laporan_service = new LaporanService();
        $this->data = $laporan_service->laporanIzinTerbitBulanan($this->tahun);
        return view('exports.laporan.permohonan-bulanan', [
            'tahun' => $this->tahun,
            'data' => $this->data,
        ]);
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();

                // Apply background color to the header row
                $sheet->getStyle('A1:O2')->applyFromArray([
                    'fill' => [
                        'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                        'color' => ['argb' => 'FFFF00'], // Yellow background
                    ],
                    'font' => [
                        'bold' => true,
                    ],
                    'alignment' => [
                        'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                    ],
                ]);

                $startRow = 3;
                foreach($this->data as $kategori){
                    $sheet->getStyle('A' . $startRow . ':O' . $startRow)->applyFromArray([
                        'fill' => [
                            'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                            'color' => ['argb' => 'FFFF00'], // Pink background
                        ],
                        'font' => [
                            'bold' => true,
                        ],
                        'alignment' => [
                            'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                        ],
                    ]);
                    $startRow++;

                    foreach($kategori->get('sektorIzin') as $sektor){
                        $sheet->getStyle('A' . $startRow . ':O' . $startRow)->applyFromArray([
                            // 'fill' => [
                            //     'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                            //     'color' => ['argb' => 'FFC0C0'], // Light red background
                            // ],
                            'font' => [
                                'bold' => true,
                            ],
                        ]);
                        $startRow++;
                        foreach($sektor->jenisIzin as $jenis){
                            $sheet->getStyle('A' . $startRow . ':O' . $startRow)->applyFromArray([
                                // 'fill' => [
                                //     'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                                //     'color' => ['argb' => 'FFC0C0'], // Light red background
                                // ],
                            ]);

                            $startRow++;
                        }
                        // jumlah
                        $sheet->getStyle('A' . $startRow . ':O' . $startRow)->applyFromArray([
                            'fill' => [
                                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                                'color' => ['argb' => 'FFC0C0'], // Light red background
                            ],
                            'font' => [
                                'bold' => true,
                            ],
                        ]);
                        $startRow++;
                    }
                }

                // Apply borders to all cells
                $sheet->getStyle('A1:O' . $sheet->getHighestRow())->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                            'color' => ['argb' => '000000'],
                        ],
                    ],
                ]);
            }
        ];
    }
}
