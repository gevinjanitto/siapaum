<?php

namespace App\Exports\Laporan;

use Illuminate\Support\Carbon;
use Maatwebsite\Excel\Events\BeforeSheet;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Style\Border;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\PageSetup;

class BukuTamuExport implements FromCollection, WithMapping, ShouldAutoSize, WithHeadings, WithStyles, WithEvents
{
    public $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function collection()
    {
        return $this->data;
    }

    public function registerEvents(): array
    {
        return [
            BeforeSheet::class => function (BeforeSheet $event) {
                $event->sheet
                    ->getPageSetup()->setPaperSize(9)
                    ->setOrientation(PageSetup::ORIENTATION_LANDSCAPE);
            },
        ];
    }

    public function headings(): array
    {
        return [
            'Nama',
            'Email',
            'Nomor Telepon',
            'Alamat',
            'Tanggal Kunjungan',
        ];
    }

    public function styles($sheet)
    {
        $numColumns = count($this->headings());
        $lastColumnLetter = Coordinate::stringFromColumnIndex($numColumns);
        $cellRange = 'A1:' . $lastColumnLetter . $sheet->getHighestRow();
        $sheet->getStyle($cellRange)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

        return [
            1 => ['font' => ['bold' => true]],
        ];
    }

    public function map($tamu): array
    {
        return [
            $tamu->nama,
            $tamu->email,
            $tamu->telepon,
            $tamu->alamat,
            Carbon::parse($tamu->created_at)->setTimezone('GMT+8')->locale('id')->isoFormat('LL LTS'),
        ];
    }
}
