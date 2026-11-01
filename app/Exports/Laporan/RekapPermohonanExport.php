<?php

namespace App\Exports\Laporan;

use Illuminate\Support\Carbon;
use App\Enums\StatusPermohonanEnum;
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

class RekapPermohonanExport implements FromCollection, WithMapping, ShouldAutoSize, WithHeadings, WithStyles, WithEvents
{
    public $data;

    public function __construct($data)
    {
        $this->data = $data;
    }
    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return $this->data;
    }

    public function registerEvents(): array
    {
        return [
            // Handle by a closure.
            BeforeSheet::class => function (BeforeSheet $event) {
                $event->sheet
                    ->getPageSetup()->setPaperSize(9)
                    ->setOrientation(PageSetup::ORIENTATION_LANDSCAPE);
            },
        ];
    }

    public function headings(): array
    {
        return $this->getHeader();
    }

    public function styles($sheet)
    {
        $numColumns = count($this->getHeader());
        $lastColumnLetter = Coordinate::stringFromColumnIndex($numColumns);
        $cellRange = 'A1:' . $lastColumnLetter . $sheet->getHighestRow();
        $sheet->getStyle($cellRange)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

        return [
            1 => ['font' => ['bold' => true]],
        ];
    }

    public function map($permohonan): array
    {
        return [
            $permohonan->nama,
            $permohonan->jenisIzin->nama,
            $permohonan->nomor_registrasi,
            Carbon::parse($permohonan->pengajuan_at)->isoFormat('D MMMM Y'),
            $permohonan->user->email,
            $permohonan->memohon_untuk,
            $permohonan->user->telepon,
            $permohonan->nik,
            $permohonan->formPermohonan->where('kode_isian', 'NO_STR')->first()?->value,
            $permohonan->formPermohonan->where('kode_isian', 'ALAMAT')->first()?->value,
            $permohonan->formPermohonan->where('kode_isian', 'PRAKTIK_KE')->first()?->value,
            $permohonan->formPermohonan->where('kode_isian', 'LOKASI')->first()?->value,
            $permohonan->kelengkapanPermohonan->where('kode_isian', 'NO_SK')->first()?->value,
            $this->parseDate($permohonan->kelengkapanPermohonan->where('kode_isian', 'TGL_SK')->first()?->value),
            $this->parseDate($permohonan->kelengkapanPermohonan->where('kode_isian', 'TGL_BERLAKU_SK')->first()?->value),
            $permohonan->kelengkapanPermohonan->where('kode_isian', 'NO_REKOMENDASI')->first()?->value,
            StatusPermohonanEnum::tryFrom($permohonan->status)->deskripsi()
        ];
    }

    /**
     * Attempt to parse a date value. If parsing fails, return the original value.
     *
     * @param string|null $value
     * @return string|null
     */
    private function parseDate($value)
    {
        if (!$value) {
            return null;
        }

        try {
            return Carbon::parse($value)->isoFormat('D MMMM Y');
        } catch (\Exception $e) {
            return $value;
        }
    }

    private function getHeader()
    {
        return [
            "Nama Pemohon",
            "Jenis Izin",
            "Nomor Registrasi",
            "Tanggal Pengajuan",
            "Email Pemohon",
            "Memohon Untuk",
            "No Telepon",
            "NIK",
            "Nomor STR - NO_STR",
            "Alamat Pemohon/Pemilik - ALAMAT",
            "Praktik Ke - PRAKTIK_KE",
            "Alamat Praktik/Usaha/Penelitian - LOKASI",
            "Nomor Surat Keputusan - NO_SK",
            "Tanggal Ditetapkan Surat Keputusan - TGL_SK",
            "Tanggal Berlaku Surat Keputusan Sampai - TGL_BERLAKU_SK",
            "Nomor Lampiran Teknis dari Dinas - NO_REKOMENDASI",
            "Status"
        ];
    }
}
