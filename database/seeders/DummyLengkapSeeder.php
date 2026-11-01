<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

// Melengkapi data dummy: reklame, validasi, log TTE, surat/berkas (file asli), activity log, OTP, token, dll.
class DummyLengkapSeeder extends Seeder
{
    private $faker;
    private array $files = [];

    public function run(): void
    {
        if (DB::table('registrasi_reklames')->exists()) {
            $this->command?->info('Data dummy lengkap sudah ada, dilewati.');
            return;
        }
        $this->faker = \Faker\Factory::create('id_ID');
        $this->faker->seed(2027);

        $this->prepareFiles();
        DB::transaction(function () {
            $this->seedTemplateSurat();
            $this->seedReklame();
            $this->attachSuratPermohonan();
            $this->seedValidasi();
            $this->seedLogTte();
            $this->seedActivityLog();
            $this->seedSkm();
            $this->seedOtpToken();
            $this->seedSystemTables();
        });
        $this->command?->info('Data dummy lengkap berhasil dibuat.');
    }

    private function ts(Carbon $d): array
    {
        return ['created_at' => $d, 'updated_at' => $d];
    }

    private function randomDate(int $monthsBack): Carbon
    {
        return Carbon::now()->subDays(rand(0, $monthsBack * 30))->setTime(rand(7, 16), rand(0, 59), rand(0, 59));
    }

    private function pdf(string $path, string $judul, array $baris): string
    {
        $pdf = new \FPDF();
        $pdf->AddPage();
        $pdf->SetFont('Arial', 'B', 13);
        $pdf->Cell(0, 7, 'PEMERINTAH KABUPATEN TABANAN', 0, 1, 'C');
        $pdf->SetFont('Arial', '', 10);
        $pdf->Cell(0, 6, 'DINAS PENANAMAN MODAL DAN PELAYANAN TERPADU SATU PINTU', 0, 1, 'C');
        $pdf->Cell(0, 5, 'Jl. Pahlawan No. 19 Tabanan, Bali', 0, 1, 'C');
        $pdf->Line(10, 30, 200, 30);
        $pdf->Ln(10);
        $pdf->SetFont('Arial', 'B', 12);
        $pdf->Cell(0, 8, strtoupper($judul), 0, 1, 'C');
        $pdf->Ln(4);
        $pdf->SetFont('Arial', '', 11);
        foreach ($baris as $b) {
            $pdf->MultiCell(0, 7, iconv('UTF-8', 'windows-1252//TRANSLIT', (string) $b));
        }
        $pdf->Ln(14);
        $pdf->Cell(0, 6, 'Tabanan, ' . Carbon::now()->locale('id')->isoFormat('D MMMM Y'), 0, 1, 'R');
        $pdf->Cell(0, 6, 'Kepala Dinas', 0, 1, 'R');
        $pdf->Ln(16);
        $pdf->SetFont('Arial', 'BU', 11);
        $pdf->Cell(0, 6, 'Drs. I Nyoman Sudarma, M.Si', 0, 1, 'R');
        $pdf->SetFont('Arial', 'I', 8);
        $pdf->Ln(10);
        $pdf->Cell(0, 5, 'Dokumen dummy - dibuat otomatis oleh seeder SIAP AUM untuk keperluan demo.', 0, 1, 'C');
        Storage::put($path, $pdf->Output('S'));
        return $path;
    }

    private function image(string $path, string $teks, array $rgb): string
    {
        if (function_exists('imagecreatetruecolor')) {
            $im = imagecreatetruecolor(640, 400);
            imagefill($im, 0, 0, imagecolorallocate($im, ...$rgb));
            $white = imagecolorallocate($im, 255, 255, 255);
            imagerectangle($im, 20, 20, 619, 379, $white);
            imagestring($im, 5, 40, 180, $teks, $white);
            imagestring($im, 3, 40, 210, 'SIAP AUM - Kabupaten Tabanan (dummy)', $white);
            ob_start();
            imagejpeg($im, null, 80);
            Storage::put($path, ob_get_clean());
            imagedestroy($im);
        } else {
            Storage::put($path, file_get_contents(public_path('assets/images/siap-aum/gate-ornament.jpg')));
        }
        return $path;
    }

    private function prepareFiles(): void
    {
        foreach (['KTP Pemohon', 'NPWP', 'Surat Pernyataan', 'Denah Lokasi', 'Foto Lokasi', 'Gambar Desain Reklame', 'Surat Izin Pemilik Lahan'] as $b) {
            $this->files['berkas'][$b] = $this->pdf('public/berkas_permohonan/dummy-' . Str::slug($b) . '.pdf', $b, ["Lampiran berkas: $b", 'Berkas ini merupakan contoh dokumen persyaratan permohonan izin.']);
        }
        $this->files['surat_kuasa'] = $this->pdf('public/surat_kuasa/dummy-surat-kuasa.pdf', 'Surat Kuasa', [
            'Yang bertanda tangan di bawah ini memberikan kuasa kepada penerima kuasa untuk mengurus permohonan perizinan pada DPMPTSP Kabupaten Tabanan.',
        ]);
        $this->files['rekomendasi'] = $this->pdf('public/permohonan/surat_rekomendasi/dummy-lampiran-teknis.pdf', 'Lampiran Teknis / Surat Rekomendasi', [
            'Berdasarkan hasil peninjauan lapangan, permohonan dinyatakan memenuhi persyaratan teknis dan direkomendasikan untuk diterbitkan izinnya.',
        ]);
        $this->files['pengantar'] = $this->pdf('public/permohonan/surat_permohonan_rekomendasi/dummy-surat-pengantar.pdf', 'Surat Pengantar Permohonan Rekomendasi', [
            'Bersama ini kami sampaikan permohonan rekomendasi teknis atas permohonan izin sebagaimana terlampir.',
        ]);
        $this->files['lampiran_sk'] = $this->pdf('public/permohonan/lampiran_sk/dummy-lampiran-sk.pdf', 'Lampiran Surat Keputusan', [
            'Lampiran ini merupakan bagian tidak terpisahkan dari Surat Keputusan izin yang diterbitkan.',
        ]);
        $this->files['skpd'] = $this->pdf('public/permohonan/reklame/skpd/dummy-skpd.pdf', 'Surat Ketetapan Pajak Daerah (SKPD) Reklame', [
            'Pajak reklame terutang sesuai ketetapan Badan Pendapatan Daerah Kabupaten Tabanan.',
        ]);
        $this->files['bukti_bayar'] = $this->image('public/permohonan/reklame/bukti_bayar/dummy-bukti-bayar.jpg', 'BUKTI PEMBAYARAN PAJAK REKLAME', [20, 110, 70]);
        $this->files['pas_foto'] = $this->image('public/pas_foto/dummy-pas-foto.jpg', 'PAS FOTO PEMOHON', [180, 30, 30]);
        $warna = [[225, 37, 27], [30, 64, 175], [217, 119, 6], [22, 101, 52], [91, 33, 182]];
        for ($i = 1; $i <= 5; $i++) {
            $this->files['reklame'][] = $this->image("public/reklame-images/dummy-reklame-$i.jpg", "REKLAME CONTOH #$i", $warna[$i - 1]);
        }
    }

    private function seedTemplateSurat(): void
    {
        $jenis = DB::table('jenis_izins')->whereNull('template_surat')->get();
        foreach ($jenis as $j) {
            $word = new \PhpOffice\PhpWord\PhpWord();
            $s = $word->addSection();
            $s->addText('PEMERINTAH KABUPATEN TABANAN', ['bold' => true, 'size' => 14], ['alignment' => 'center']);
            $s->addText('SURAT IZIN ${NAMA_JNS_IZIN}', ['bold' => true, 'size' => 12], ['alignment' => 'center']);
            $s->addText('Nomor: ${NO_REGISTRASI}', [], ['alignment' => 'center']);
            foreach (['Nama' => 'NAMA', 'NIK' => 'NIK', 'NPWP' => 'NPWP', 'Tempat Lahir' => 'TEMPAT_LAHIR'] as $label => $kode) {
                $s->addText($label . ' : ${' . $kode . '}');
            }
            $s->addText('${DESKRIPSI_JNS_IZIN}');
            $path = 'public/file_templateword/template-' . Str::slug($j->nama) . '.docx';
            Storage::makeDirectory('public/file_templateword');
            \PhpOffice\PhpWord\IOFactory::createWriter($word, 'Word2007')->save(storage_path('app/' . $path));
            DB::table('jenis_izins')->where('id', $j->id)->update(['template_surat' => $path]);
        }
    }

    private function seedReklame(): void
    {
        $verif = DB::table('alur_jenis_izins')->where('jenis_izin_id', DB::table('jenis_izins')->min('id'))->orderBy('urutan')->get(['verifikator_id', 'jenis_verifikator'])->toArray();
        $jenisId = 9;
        if (!DB::table('jenis_izins')->where('id', $jenisId)->exists()) {
            $now = Carbon::now()->subYears(2);
            $sektor = DB::table('sektor_izins')->first();
            DB::table('jenis_izins')->insert(['id' => $jenisId, 'ulid' => (string) Str::ulid(), 'nama' => 'Izin Penyelenggaraan Reklame',
                'deskripsi' => 'Izin pemasangan reklame (billboard, baliho, papan nama, dan sejenisnya) di wilayah Kabupaten Tabanan.',
                'template_surat' => DB::table('jenis_izins')->value('template_surat'), 'sektor_izin_id' => $sektor?->id, 'kategori_izin_id' => $sektor?->kategori_izin_id,
                'is_pas_foto_required' => false] + $this->ts($now));
        }
        $forms = [['Nama Perusahaan', 'text', 'NAMA_PERUSAHAAN'], ['HP/Telp', 'text', 'HP/TELP'], ['Alamat', 'text', 'ALAMAT'], ['Jenis Reklame', 'text', 'JENIS_REKLAME'],
            ['Area Pemasangan', 'text', 'AREA_PEMASANGAN'], ['Bunyi Reklame', 'text', 'BUNYI_REKLAME'], ['Lokasi Pemasangan', 'text', 'LOKASI_PEMASANGAN'],
            ['Ukuran (m2)', 'number', 'UKURAN'], ['Jumlah', 'number', 'JUMLAH'], ['Tanggal Mulai', 'date', 'TGL_MULAI'], ['Tanggal Akhir', 'date', 'TGL_AKHIR'], ['Lama Pemasangan (hari)', 'number', 'LAMA_PEMASANGAN']];
        $berkas = ['KTP Pemohon', 'NPWP', 'Gambar Desain Reklame', 'Surat Izin Pemilik Lahan'];
        $kelengkapan = [['Pajak Reklame', 'number', 'PAJAK_REKLAME'], ['Pajak Reklame Terbilang', 'text', 'PAJAK_REKLAME_TERBILANG'], ['Nomor SKPD', 'text', 'NO_SKPD']];
        if (!DB::table('form_jenis_izins')->where('jenis_izin_id', $jenisId)->exists()) {
            foreach ($forms as $u => [$l, $t, $k]) DB::table('form_jenis_izins')->insert(['jenis_izin_id' => $jenisId, 'label' => $l, 'tipe' => $t, 'kode_isian' => $k, 'urutan' => $u + 1] + $this->ts(now()));
            foreach ($berkas as $u => $b) DB::table('berkas_jenis_izins')->insert(['jenis_izin_id' => $jenisId, 'nama' => $b, 'is_required' => true, 'urutan' => $u + 1] + $this->ts(now()));
            foreach ($kelengkapan as $u => [$l, $t, $k]) DB::table('kelengkapan_jenis_izins')->insert(['jenis_izin_id' => $jenisId, 'label' => $l, 'tipe' => $t, 'kode_isian' => $k, 'urutan' => $u + 1] + $this->ts(now()));
            foreach ($verif as $u => $v) DB::table('alur_jenis_izins')->insert(['jenis_izin_id' => $jenisId, 'verifikator_id' => $v->verifikator_id, 'jenis_verifikator' => $v->jenis_verifikator, 'urutan' => $u + 1] + $this->ts(now()));
        }

        $jenisReklame = ['Billboard', 'Baliho', 'Papan', 'Papan Bercahaya', 'LED', 'Megatron/Videotron', 'Layar/Spanduk Umbul-umbul dan Sejenisnya', 'Tempel/Plat/Tembok'];
        $area = ['Pariwisata', 'Perkotaan', 'Lainnya'];
        $lokasi = ['Jl. Gatot Subroto, Tabanan', 'Jl. Raya Kediri', 'Simpang Pesiapan', 'Jl. Bypass Ir. Soekarno', 'Jl. Raya Tanah Lot', 'Jl. Raya Bedugul, Baturiti', 'Jl. Pahlawan, Tabanan', 'Pasar Kediri'];
        $statuses = ['selesai', 'selesai', 'selesai', 'verifikasi', 'verifikasi', 'permohonan_baru', 'revisi', 'pending'];
        $pemohon = DB::table('users')->where('role_id', 3)->inRandomOrder()->limit(30)->get();
        $no = 0;
        foreach ($pemohon as $i => $user) {
            $date = $this->randomDate(18);
            $perusahaan = $this->faker->company();
            $rid = DB::table('registrasi_reklames')->insertGetId(['user_id' => $user->id, 'nama' => $user->name, 'nik' => $user->nik,
                'npwp' => rand(10, 99) . '.' . rand(100, 999) . '.' . rand(100, 999) . '.' . rand(1, 9) . '-' . rand(100, 999) . '.000',
                'nama_perusahaan' => $perusahaan, 'alamat_perusahaan' => 'Jl. ' . $this->faker->streetName() . ' No. ' . rand(1, 99) . ', Tabanan', 'nomor_telepon' => $user->telepon] + $this->ts($date));
            $nomor = 'BLL/' . $date->timestamp . '/' . $user->id . '/9/' . $rid;
            DB::table('registrasi_reklames')->where('id', $rid)->update(['nomor_registrasi' => $nomor]);
            $reg = DB::table('registrasi_reklames')->find($rid);

            for ($r = 0; $r < ($i % 3 === 0 ? 2 : 1); $r++, $no++) {
                $mulai = $date->copy()->addDays(rand(7, 30));
                $akhir = $mulai->copy()->addDays([90, 180, 365][rand(0, 2)]);
                $values = ['NAMA_PERUSAHAAN' => $perusahaan, 'HP/TELP' => $user->telepon, 'ALAMAT' => $reg->alamat_perusahaan,
                    'JENIS_REKLAME' => $jenisReklame[array_rand($jenisReklame)], 'AREA_PEMASANGAN' => $area[array_rand($area)],
                    'BUNYI_REKLAME' => $this->faker->randomElement(['Promo Akhir Tahun', 'Grand Opening', 'Diskon 50%', 'Nikmati Kopi Asli Bali', 'Villa & Resort Tabanan', 'Servis Motor Murah']) . ' - ' . $perusahaan,
                    'LOKASI_PEMASANGAN' => $lokasi[array_rand($lokasi)], 'UKURAN' => (string) [4, 8, 12, 18, 24, 32][rand(0, 5)], 'JUMLAH' => (string) rand(1, 4),
                    'TGL_MULAI' => $mulai->format('d-m-Y'), 'TGL_AKHIR' => $akhir->format('d-m-Y'), 'LAMA_PEMASANGAN' => (string) $mulai->diffInDays($akhir)];
                $status = $no < 8 ? null : $statuses[array_rand($statuses)];
                $done = match ($status) { 'selesai' => 5, 'verifikasi' => rand(1, 3), 'revisi' => 1, default => 0 };
                $skpd = $done >= 2 || $status === 'selesai';
                $rkid = DB::table('reklames')->insertGetId(['registrasi_reklame_id' => $rid, 'image_filepath' => $this->files['reklame'][$no % 5], 'is_from_sireko' => $no % 7 === 0,
                    'skpd_filepath' => $skpd ? $this->files['skpd'] : null, 'bukti_bayar_filepath' => $skpd ? $this->files['bukti_bayar'] : null,
                    'bukti_bayar_user_id' => $skpd ? $user->id : null, 'is_bongkar' => $no % 11 === 0] + $this->ts($date));
                foreach ($forms as $u => [$l, $t, $k]) {
                    DB::table('form_reklames')->insert(['reklame_id' => $rkid, 'label' => $l, 'tipe' => $t, 'kode_isian' => $k, 'value' => $values[$k], 'urutan' => $u + 1] + $this->ts($date));
                }
                if (!$status) continue;

                $pid = DB::table('permohonans')->insertGetId(['user_id' => $user->id, 'jenis_izin_id' => $jenisId, 'nama_jenis_izin' => 'Izin Penyelenggaraan Reklame',
                    'deskripsi_jenis_izin' => 'Izin pemasangan reklame di wilayah Kabupaten Tabanan.', 'nomor_registrasi' => $nomor, 'nama' => $reg->nama, 'nik' => $reg->nik,
                    'npwp' => $reg->npwp, 'tempat_lahir' => '', 'is_ttd' => $status === 'selesai', 'status' => $status, 'is_expired' => false, 'is_legacy_data' => false,
                    'pengajuan_at' => $status === 'pending' ? null : $date, 'is_pas_foto_required' => false] + $this->ts($date));
                DB::table('reklames')->where('id', $rkid)->update(['permohonan_id' => $pid]);
                foreach ($forms as $u => [$l, $t, $k]) {
                    DB::table('form_permohonans')->insert(['permohonan_id' => $pid, 'label' => $l, 'tipe' => $t, 'kode_isian' => $k, 'value' => $values[$k], 'urutan' => $u + 1] + $this->ts($date));
                }
                foreach ($berkas as $u => $b) {
                    $rev = $status === 'revisi' && $u === 2;
                    DB::table('berkas_permohonans')->insert(['permohonan_id' => $pid, 'nama' => $b, 'is_required' => true, 'urutan' => $u + 1,
                        'filepath' => $status === 'pending' ? null : $this->files['berkas'][$b], 'is_revisi' => $rev, 'catatan' => $rev ? 'Desain reklame belum mencantumkan ukuran.' : null] + $this->ts($date));
                }
                $pajak = (int) $values['UKURAN'] * (int) $values['JUMLAH'] * rand(150, 400) * 1000;
                $kv = $skpd ? ['PAJAK_REKLAME' => (string) $pajak, 'PAJAK_REKLAME_TERBILANG' => $this->terbilang($pajak) . ' rupiah', 'NO_SKPD' => 'SKPD/' . rand(1000, 9999) . '/BAPENDA/' . $date->year] : [];
                foreach ($kelengkapan as $u => [$l, $t, $k]) {
                    DB::table('kelengkapan_permohonans')->insert(['permohonan_id' => $pid, 'label' => $l, 'tipe' => $t, 'kode_isian' => $k, 'value' => $kv[$k] ?? null, 'urutan' => $u + 1] + $this->ts($date));
                }
                foreach ($verif as $u => $v) {
                    DB::table('alur_permohonans')->insert(['permohonan_id' => $pid, 'verifikator_id' => $v->verifikator_id, 'jenis_verifikator' => $v->jenis_verifikator,
                        'urutan' => $u + 1, 'is_done' => $u < $done] + $this->ts($date->copy()->addDays($u)));
                }
                if ($skpd) {
                    DB::table('pembayaran_reklames')->insert(['permohonan_id' => $pid, 'nomor_skpd' => $kv['NO_SKPD'], 'is_lunas' => $status === 'selesai' || rand(0, 1),
                        'skpd_filepath' => $this->files['skpd'], 'nominal' => $pajak] + $this->ts($date->copy()->addDays(3)));
                }
            }
        }
    }

    private function terbilang(int $n): string
    {
        $h = ['', 'satu', 'dua', 'tiga', 'empat', 'lima', 'enam', 'tujuh', 'delapan', 'sembilan', 'sepuluh', 'sebelas'];
        if ($n < 12) return $h[$n];
        if ($n < 20) return $this->terbilang($n - 10) . ' belas';
        if ($n < 100) return trim($this->terbilang(intdiv($n, 10)) . ' puluh ' . $this->terbilang($n % 10));
        if ($n < 200) return trim('seratus ' . $this->terbilang($n - 100));
        if ($n < 1000) return trim($this->terbilang(intdiv($n, 100)) . ' ratus ' . $this->terbilang($n % 100));
        if ($n < 2000) return trim('seribu ' . $this->terbilang($n - 1000));
        if ($n < 1000000) return trim($this->terbilang(intdiv($n, 1000)) . ' ribu ' . $this->terbilang($n % 1000));
        return trim($this->terbilang(intdiv($n, 1000000)) . ' juta ' . $this->terbilang($n % 1000000));
    }

    private function attachSuratPermohonan(): void
    {
        DB::table('berkas_permohonans')->whereNull('filepath')->whereIn('permohonan_id', DB::table('permohonans')->where('status', '!=', 'pending')->select('id'))
            ->orderBy('id')->chunkById(500, function ($rows) {
                foreach ($rows as $b) {
                    DB::table('berkas_permohonans')->where('id', $b->id)->update(['filepath' => $this->files['berkas'][$b->nama] ?? $this->files['berkas']['Surat Pernyataan']]);
                }
            });
        DB::table('permohonans')->where('is_pas_foto_required', true)->where('status', '!=', 'pending')->update(['pas_foto_filepath' => $this->files['pas_foto']]);
        DB::statement('UPDATE permohonans SET surat_kuasa_filepath = ? WHERE status != ? AND MOD(id, 4) = 0', [$this->files['surat_kuasa'], 'pending']);

        $opdDone = DB::table('alur_permohonans')->where('jenis_verifikator', '1')->where('is_done', true)->pluck('permohonan_id');
        DB::table('permohonans')->whereIn('id', $opdDone)->where('jenis_izin_id', '!=', 9)
            ->update(['surat_rekomendasi_filepath' => $this->files['rekomendasi'], 'surat_permohonan_rekomendasi_filepath' => $this->files['pengantar']]);

        $bo = DB::table('alur_permohonans')->where('jenis_verifikator', '2')->where('is_done', true)->pluck('permohonan_id');
        $permohonan = DB::table('permohonans')->whereIn('id', $bo)->orWhere('status', 'selesai')->get();
        foreach ($permohonan as $p) {
            $form = DB::table('form_permohonans')->where('permohonan_id', $p->id)->orderBy('urutan')->get();
            $baris = ["Nomor Registrasi : {$p->nomor_registrasi}", "Nama Pemohon : {$p->nama}", "NIK : {$p->nik}", "NPWP : {$p->npwp}"];
            foreach ($form->take(6) as $f) $baris[] = "{$f->label} : {$f->value}";
            $baris[] = '';
            $baris[] = "Dengan ini diberikan {$p->nama_jenis_izin} kepada pemohon tersebut di atas, dengan ketentuan mematuhi seluruh peraturan perundang-undangan yang berlaku.";
            $path = $this->pdf('izin_terbit/dummy-izin-' . $p->id . '.pdf', 'Surat ' . $p->nama_jenis_izin, $baris);
            DB::table('permohonans')->where('id', $p->id)->update(['template_surat_filepath' => $path, 'lampiran_sk_filepath' => $p->id % 3 === 0 ? $this->files['lampiran_sk'] : null]);
        }
    }

    private function seedValidasi(): void
    {
        $catatan = ['Dokumen sudah sesuai.', 'Data valid dan lengkap.', 'Sesuai dengan dokumen asli.', null];
        DB::table('alur_permohonans')->join('permohonans', 'permohonans.id', '=', 'alur_permohonans.permohonan_id')
            ->where(fn ($q) => $q->where('alur_permohonans.is_done', true)->orWhere(fn ($q) => $q->where('permohonans.status', 'revisi')->where('alur_permohonans.urutan', 1)))
            ->whereIn('alur_permohonans.jenis_verifikator', ['0', '1'])
            ->select('alur_permohonans.*', 'permohonans.status as p_status')->orderBy('alur_permohonans.id')
            ->chunk(300, function ($alurs) use ($catatan) {
                foreach ($alurs as $a) {
                    $revisi = $a->p_status === 'revisi' && !$a->is_done;
                    DB::table('validasi_forms')->insert(['alur_permohonan_id' => $a->id, 'status' => $revisi ? 'revisi' : 'valid',
                        'catatan' => $revisi ? 'Mohon periksa kembali isian formulir.' : $catatan[array_rand($catatan)]] + $this->ts(Carbon::parse($a->updated_at)));
                    foreach (DB::table('berkas_permohonans')->where('permohonan_id', $a->permohonan_id)->get(['id', 'is_revisi', 'catatan']) as $b) {
                        $rev = $revisi && $b->is_revisi;
                        DB::table('validasi_berkas')->insert(['alur_permohonan_id' => $a->id, 'berkas_permohonan_id' => $b->id, 'status' => $rev ? 'revisi' : 'valid',
                            'catatan' => $rev ? $b->catatan : null] + $this->ts(Carbon::parse($a->updated_at)));
                    }
                }
            });
    }

    private function seedLogTte(): void
    {
        $ttd = DB::table('users')->where('email', 'ttd@example.com')->value('id') ?? DB::table('users')->where('role_id', 2)->value('id');
        foreach (DB::table('permohonans')->where('status', 'selesai')->get(['id', 'updated_at']) as $i => $p) {
            $d = Carbon::parse($p->updated_at)->addDays(5);
            if ($i % 6 === 0) {
                DB::table('log_ttes')->insert(['permohonan_id' => $p->id, 'verifikator_id' => $ttd, 'code' => '400',
                    'body' => json_encode(['error' => 'Passphrase anda salah', 'status_code' => 2031])] + $this->ts($d->copy()->subMinutes(5)));
            }
            DB::table('log_ttes')->insert(['permohonan_id' => $p->id, 'verifikator_id' => $ttd, 'code' => '200', 'body' => '-'] + $this->ts($d));
        }
    }

    private function seedActivityLog(): void
    {
        $label = ['0' => 'Front Office', '1' => 'OPD', '2' => 'Back Office', '4' => 'JF', '3' => null];
        $rows = [];
        $push = function ($desc, $pid, $causer, Carbon $d) use (&$rows) {
            $rows[] = ['log_name' => 'default', 'description' => $desc, 'subject_type' => 'App\\Models\\Permohonan', 'subject_id' => $pid, 'event' => null,
                'causer_type' => 'App\\Models\\User', 'causer_id' => $causer, 'properties' => '[]', 'batch_uuid' => null] + $this->ts($d);
        };
        $permohonan = DB::table('permohonans')->where('status', '!=', 'pending')->get(['id', 'user_id', 'status', 'jenis_izin_id', 'pengajuan_at', 'created_at']);
        $alur = DB::table('alur_permohonans')->where('is_done', true)->get()->groupBy('permohonan_id');
        foreach ($permohonan as $p) {
            $d = Carbon::parse($p->pengajuan_at ?? $p->created_at);
            $push('Pemohon membuat usulan', $p->id, $p->user_id, $d);
            foreach ($alur[$p->id] ?? [] as $a) {
                $t = Carbon::parse($a->updated_at)->addHours(rand(1, 20));
                if ($p->jenis_izin_id == 9 && $a->jenis_verifikator === '1') {
                    $push('Verifikator mengunggah SKPD Reklame', $p->id, $a->verifikator_id, $t->copy()->subHour());
                    $push('Pemohon mengunggah bukti bayar reklame', $p->id, $p->user_id, $t->copy()->subMinutes(30));
                }
                $push($label[$a->jenis_verifikator] ? 'Disetujui verifikator ' . $label[$a->jenis_verifikator] : 'Izin terbit telah ditandatangani', $p->id, $a->verifikator_id, $t);
            }
            if (in_array($p->status, ['revisi', 'verifikasi_ulang'])) {
                $fo = DB::table('alur_permohonans')->where('permohonan_id', $p->id)->orderBy('urutan')->value('verifikator_id');
                $push('Verifikator meminta revisi', $p->id, $fo, $d->copy()->addDays(2));
                if ($p->status === 'verifikasi_ulang') $push('Pemohon melakukan perbaikan revisi', $p->id, $p->user_id, $d->copy()->addDays(4));
            }
            if (count($rows) > 400) { DB::table('activity_log')->insert($rows); $rows = []; }
        }
        if ($rows) DB::table('activity_log')->insert($rows);
    }

    private function seedSkm(): void
    {
        $layanan = DB::table('layanan_skms')->get(['id', 'group_layanan_skm_id']);
        if ($layanan->isEmpty()) return;
        $nama = ['Pelayanan Perizinan Berusaha', 'Pelayanan Perizinan Non Berusaha', 'Pelayanan Pengaduan', 'Pelayanan Konsultasi', 'Pelayanan Informasi Investasi'];
        foreach (DB::table('group_layanan_skms')->orderBy('id')->get() as $i => $g) {
            DB::table('group_layanan_skms')->where('id', $g->id)->update(['image_filepath' => $g->image_filepath ?? $this->copyPublic("group_layanan_skm/dummy-group-$i.jpg", $i)]);
            if (!DB::table('kuesioner_pertanyaans')->where('group_layanan_skm_id', $g->id)->exists()) {
                foreach (['Bagaimana kemudahan prosedur pada ' . ($nama[$i % 5]) . '?', 'Bagaimana kecepatan petugas dalam melayani?', 'Bagaimana keramahan petugas?', 'Bagaimana kenyamanan ruang pelayanan?'] as $p) {
                    $pid = DB::table('kuesioner_pertanyaans')->insertGetId(['pertanyaan' => $p, 'group_layanan_skm_id' => $g->id] + $this->ts(now()));
                    foreach (['Tidak Baik', 'Kurang Baik', 'Baik', 'Sangat Baik'] as $point => $o) {
                        DB::table('kuesioner_opsis')->insert(['kuesioner_pertanyaan_id' => $pid, 'opsi' => $o, 'point' => $point + 1] + $this->ts(now()));
                    }
                }
            }
        }
        $pendidikan = ['SD', 'SMP', 'SMA', 'D3', 'S1', 'S2'];
        $pekerjaan = ['PNS', 'TNI/POLRI', 'Pegawai Swasta', 'Wiraswasta', 'Petani', 'Pelajar/Mahasiswa', 'Lainnya'];
        for ($i = 0; $i < 60; $i++) {
            $l = $layanan[$i % $layanan->count()];
            $date = $this->randomDate(8);
            $kid = DB::table('kuesioners')->insertGetId(['permohonan_id' => null, 'jenis_izin_id' => null, 'layanan_skm_id' => $l->id, 'is_from_skm' => true,
                'jenis_kelamin' => rand(0, 1), 'pendidikan' => $pendidikan[array_rand($pendidikan)], 'pekerjaan' => $pekerjaan[array_rand($pekerjaan)],
                'nama' => $this->faker->name(), 'nomor_telepon' => '08' . rand(1100000000, 9999999999), 'email' => $this->faker->safeEmail()] + $this->ts($date));
            foreach (DB::table('kuesioner_pertanyaans')->where('group_layanan_skm_id', $l->group_layanan_skm_id)->whereNull('deleted_at')->get() as $p) {
                $o = DB::table('kuesioner_opsis')->where('kuesioner_pertanyaan_id', $p->id)->orderBy('point')->skip(rand(1, 3))->first();
                DB::table('kuesioner_jawabans')->insert(['kuesioner_id' => $kid, 'kuesioner_opsi_id' => $o->id, 'kuesioner_pertanyaan_id' => $p->id,
                    'pertanyaan' => Str::limit($p->pertanyaan, 250, ''), 'opsi' => $o->opsi, 'point' => $o->point] + $this->ts($date));
            }
        }
    }

    private function copyPublic(string $path, int $i): string
    {
        $src = ['figma-raw-4.webp', 'gate-ornament.jpg', 'figma-raw-2.webp'][$i % 3];
        $file = public_path('assets/images/siap-aum/' . $src);
        if (is_file($file)) {
            Storage::disk('public')->put($path, file_get_contents($file));
            return $path;
        }
        $this->image('public/' . $path, 'LAYANAN SKM', [225, 37, 27]);
        return $path;
    }

    private function seedOtpToken(): void
    {
        $users = DB::table('users')->where('role_id', 3)->limit(40)->get(['id', 'email']);
        foreach ($users as $i => $u) {
            $d = $this->randomDate(6);
            DB::table('otps')->insert(['user_id' => $i % 2 ? $u->id : null, 'email' => $u->email, 'token_name' => $i % 3 ? 'register' : 'forgot-password',
                'otp_token' => Hash::make((string) rand(100000, 999999)), 'max_attempt' => 5, 'attempt' => rand(0, 3), 'is_valid' => false,
                'ip_address' => '10.10.' . rand(0, 255) . '.' . rand(1, 254), 'expired_at' => $d->copy()->addMinutes(10)] + $this->ts($d));
            if ($i < 20) {
                DB::table('password_reset_tokens')->insert(['email' => $u->email, 'token' => Hash::make(Str::random(60)), 'created_at' => Carbon::now()->subDays(rand(2, 60))]);
            }
        }
        $apiNames = ['SIREKO Bapenda', 'OSS RBA Integrasi', 'Dashboard Bupati', 'Satu Data Tabanan', 'SIMBG PUPR', 'Dinas Kesehatan', 'Dinas Pendidikan', 'Dinas Perhubungan',
            'Disparda Tabanan', 'DLH Tabanan', 'Bappeda Litbang', 'Inspektorat', 'Diskominfo', 'Kecamatan Kediri', 'Kecamatan Marga', 'Kecamatan Penebel', 'Kecamatan Baturiti',
            'Kecamatan Kerambitan', 'Kecamatan Selemadeg', 'Kecamatan Pupuan'];
        foreach ($apiNames as $i => $n) {
            $slug = Str::slug($n);
            if (DB::table('open_api_users')->where('username', $slug)->exists()) continue;
            $id = DB::table('open_api_users')->insertGetId(['name' => $n, 'username' => $slug, 'email' => $slug . '@tabanankab.go.id', 'password' => Hash::make('P@ssw0rd')] + $this->ts(Carbon::now()->subMonths(rand(1, 18))));
            DB::table('personal_access_tokens')->insert(['tokenable_type' => 'App\\Models\\OpenApiUser', 'tokenable_id' => $id, 'name' => 'api-token',
                'token' => hash('sha256', Str::random(40)), 'abilities' => '["*"]', 'last_used_at' => Carbon::now()->subHours(rand(1, 300)), 'expires_at' => null] + $this->ts(Carbon::now()->subMonths(1)));
        }
    }

    private function seedSystemTables(): void
    {
        for ($i = 0; $i < 20; $i++) {
            $d = $this->randomDate(3);
            $job = $i % 2 ? 'App\\Jobs\\SendUsulanNeedVerifikasiNotifcationJob' : 'App\\Jobs\\UpdatePermohonanRevisiToExpiredJob';
            DB::table('failed_jobs')->insert(['uuid' => (string) Str::uuid(), 'connection' => 'database', 'queue' => 'default',
                'payload' => json_encode(['uuid' => (string) Str::uuid(), 'displayName' => $job, 'job' => 'Illuminate\\Queue\\CallQueuedHandler@call', 'maxTries' => 3, 'data' => ['commandName' => $job]]),
                'exception' => 'Symfony\\Component\\Mailer\\Exception\\TransportException: Connection could not be established with host "mailpit:1025" (dummy)', 'failed_at' => $d]);
            DB::table('jobs')->insert(['queue' => 'dummy-arsip', 'payload' => json_encode(['uuid' => (string) Str::uuid(), 'displayName' => $job, 'data' => ['commandName' => $job]]),
                'attempts' => 0, 'reserved_at' => null, 'available_at' => $d->timestamp, 'created_at' => $d->timestamp]);
        }
        if (!\Illuminate\Support\Facades\Schema::hasTable('telescope_entries')) return;
        $types = ['request', 'query', 'job', 'mail', 'log', 'exception'];
        for ($i = 0; $i < 40; $i++) {
            $uuid = (string) Str::uuid();
            $type = $types[$i % count($types)];
            DB::table('telescope_entries')->insert(['uuid' => $uuid, 'batch_id' => (string) Str::uuid(), 'family_hash' => null, 'should_display_on_index' => true, 'type' => $type,
                'content' => json_encode(['method' => 'GET', 'uri' => '/dashboard', 'response_status' => 200, 'duration' => rand(40, 900), 'message' => "Contoh entri $type (dummy)"]),
                'created_at' => $this->randomDate(1)]);
            DB::table('telescope_entries_tags')->insert(['entry_uuid' => $uuid, 'tag' => 'dummy-' . $type]);
        }
        foreach (['dummy-request', 'dummy-job', 'dummy-exception'] as $tag) {
            DB::table('telescope_monitoring')->insertOrIgnore(['tag' => $tag]);
        }
    }
}
