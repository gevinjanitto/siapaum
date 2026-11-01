<?php

namespace App\Exports\SurveyLayanan;

use App\Models\GroupLayananSkm;
use App\Services\LaporanService;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use App\Exports\SurveyLayanan\PengolahanExport;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class LaporanExport implements FromView, ShouldAutoSize, WithEvents, WithTitle, WithMultipleSheets
{
    protected ?GroupLayananSkm $groupLayananSkm;
    protected $start;
    protected $end;
    protected $laporan_survey_layanan;

    public function __construct(?GroupLayananSkm $groupLayananSkm, $start, $end)
    {
        $this->groupLayananSkm = $groupLayananSkm;
        $this->start = $start;
        $this->end = $end;

        $laporan_service = new LaporanService();
        $this->laporan_survey_layanan = $laporan_service->laporanSurvey([
            'tanggal_awal' => $this->start,
            'tanggal_akhir' => $this->end,
        ], $this->groupLayananSkm);
    }
    public function view(): View
    {
        return view('exports.laporan.survey-layanan.laporan', [
            'groupLayananSkm' => $this->groupLayananSkm,
            'tanggal_awal' => $this->start,
            'tanggal_akhir' => $this->end,
            'laporan_survey_layanan' => $this->laporan_survey_layanan,
        ]);
    }

    public function title(): string
    {
        return 'Laporan';
    }

    public function sheets(): array
    {
        return [
            $this,
            new PengolahanExport($this->groupLayananSkm, $this->start, $this->end, $this->laporan_survey_layanan),
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();

                // Merge cells for the header
                $sheet->mergeCells('A1:D1');

                // Apply styles to the merged cell
                $sheet->getStyle('A1:D1')->applyFromArray([
                    'alignment' => [
                        'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                        'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                        'wrapText' => true,
                    ],
                    'font' => [
                        'bold' => true,
                    ],
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                            'color' => ['argb' => '000000'],
                        ],
                    ],
                ]);

                // Set row height to auto
                $sheet->getRowDimension(1)->setRowHeight(80);

                // row 2
                $sheet->getStyle('A2:D2')->applyFromArray([
                    'font' => [
                        'bold' => true,
                    ],
                    'alignment' => [
                        'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                        'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                    ],
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                            'color' => ['argb' => '000000'],
                        ],
                    ],
                ]);

                $row = 4;

                // JENIS KELAMIN
                $sheet->getStyle('A' . $row . ':D' . $row)->applyFromArray([
                    'font' => [
                        'bold' => true,
                    ],
                ]);
                $sheet->getStyle('A' . ++$row . ':D' . ++$row)->applyFromArray([
                    'alignment' => [
                        'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                        'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                    ],
                ]);
                $row++;
                $sheet->getStyle('A' . $row . ':D' . $row)->applyFromArray([
                    'font' => [
                        'bold' => true,
                    ],
                ]);
                for ($i = 0; $i < $this->laporan_survey_layanan['statistik']->get('jenis_kelamin')->count() + 2; $i++) {
                    $sheet->getStyle('A' . $row . ':D' . $row)->applyFromArray([
                        'alignment' => [
                            'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                            'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                        ],
                        'borders' => [
                            'allBorders' => [
                                'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                                'color' => ['argb' => '000000'],
                            ],
                        ],
                    ]);

                    $row++;
                }
                $row++;

                // Pendidikan Terakhir
                $sheet->getStyle('A' . $row . ':D' . $row)->applyFromArray([
                    'font' => [
                        'bold' => true,
                    ],
                ]);
                $sheet->getStyle('A' . ++$row . ':D' . ++$row)->applyFromArray([
                    'alignment' => [
                        'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                        'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                    ],
                ]);
                $row++;
                $sheet->getStyle('A' . $row . ':D' . $row)->applyFromArray([
                    'font' => [
                        'bold' => true,
                    ],
                ]);
                for ($i = 0; $i < $this->laporan_survey_layanan['statistik']->get('pendidikan_terakhir')->count() + 2; $i++) {
                    $sheet->getStyle('A' . $row . ':D' . $row)->applyFromArray([
                        'alignment' => [
                            'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                            'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                        ],
                        'borders' => [
                            'allBorders' => [
                                'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                                'color' => ['argb' => '000000'],
                            ],
                        ],
                    ]);

                    $row++;
                }
                $row++;

                // Jenis Pekerjaan
                $sheet->getStyle('A' . $row . ':D' . $row)->applyFromArray([
                    'font' => [
                        'bold' => true,
                    ],
                ]);
                $sheet->getStyle('A' . ++$row . ':D' . ++$row)->applyFromArray([
                    'alignment' => [
                        'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                        'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                    ],
                ]);
                $row++;
                $sheet->getStyle('A' . $row . ':D' . $row)->applyFromArray([
                    'font' => [
                        'bold' => true,
                    ],
                ]);
                for ($i = 0; $i < $this->laporan_survey_layanan['statistik']->get('jenis_pekerjaan')->count() + 2; $i++) {
                    $sheet->getStyle('A' . $row . ':D' . $row)->applyFromArray([
                        'alignment' => [
                            'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                            'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                        ],
                        'borders' => [
                            'allBorders' => [
                                'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                                'color' => ['argb' => '000000'],
                            ],
                        ],
                    ]);

                    $row++;
                }

                // Kesimpulan
                $row = $row + 2;
                $sheet->getStyle('A' . $row . ':D' . $row)->applyFromArray([
                    'font' => [
                        'bold' => true,
                    ],
                    'alignment' => [
                        'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                        'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                    ],
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                            'color' => ['argb' => '000000'],
                        ],
                    ],
                ]);
                $row++;
                $sheet->getStyle('A' . $row . ':D' . $row)->applyFromArray([
                    'font' => [
                        'bold' => true,
                        'size' => 64,
                    ],
                    'alignment' => [
                        'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                        'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                    ],
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
