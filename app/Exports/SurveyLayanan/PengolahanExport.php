<?php

namespace App\Exports\SurveyLayanan;

use App\Models\GroupLayananSkm;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class PengolahanExport implements FromView, ShouldAutoSize, WithTitle, WithEvents
{
    protected ?GroupLayananSkm $groupLayananSkm;
    protected $start;
    protected $end;
    protected $laporan_survey_layanan;

    public function __construct(?GroupLayananSkm $groupLayananSkm, $start, $end, $laporan_survey_layanan)
    {
        $this->groupLayananSkm = $groupLayananSkm;
        $this->start = $start;
        $this->end = $end;
        $this->laporan_survey_layanan = $laporan_survey_layanan;
    }

    public function view(): View
    {
        return view('exports.laporan.survey-layanan.pengolahan', [
            'groupLayananSkm' => $this->groupLayananSkm,
            'tanggal_awal' => $this->start,
            'tanggal_akhir' => $this->end,
            'laporan_survey_layanan' => $this->laporan_survey_layanan,
        ]);
    }

    public function title(): string
    {
        return 'Pengolahan Data';
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();

                $total_unsur = count($this->laporan_survey_layanan['data']['nrrUnsur']) ?? 4;
                // Merge cells for the header

                // Apply styles to the merged cell
                $sheet->getStyle('A1:' . \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($total_unsur+1) . '1')->applyFromArray([
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
                $sheet->getStyle('A2:' . \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($total_unsur+1) . '2')->applyFromArray([
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

                $sheet->getStyle('A4:' . \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex(count($this->laporan_survey_layanan['data']['nrrUnsur']) + 1) . '5')->applyFromArray([
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

                for ($i = 6; $i <= $this->laporan_survey_layanan['data']['kuesioners']->count() + 8; $i++) {
                    $sheet->getStyle('A' . $i . ':' . \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex(count($this->laporan_survey_layanan['data']['nrrUnsur']) + 1) . $i)
                        ->applyFromArray([
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
            }
        ];
    }
}
