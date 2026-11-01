<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DummyDataSeeder extends Seeder
{
    private $faker;
    private string $password;

    public function run(): void
    {
        if (DB::table('users')->where('email', 'pemohon@example.com')->exists()) {
            $this->command?->info('Data dummy sudah ada, dilewati.');
            return;
        }
        $this->faker = \Faker\Factory::create('id_ID');
        $this->faker->seed(2026);
        $this->password = Hash::make('P@ssw0rd');

        DB::transaction(function () {
            $sektor = $this->seedKategoriSektor();
            $verifikator = $this->seedVerifikator();
            $pemohon = $this->seedPemohon(150);
            $jenisIzin = $this->seedJenisIzin($sektor, $verifikator);
            $permohonan = $this->seedPermohonan($pemohon, $jenisIzin, 450);
            $this->seedSurvey($jenisIzin, $permohonan, 300);
            $this->seedTamu(180);
            $this->seedOtp($pemohon);
        });
        $this->command?->info('Data dummy berhasil dibuat.');
    }

    private function ts(Carbon $date): array
    {
        return ['created_at' => $date, 'updated_at' => $date];
    }

    private function randomDate(int $monthsBack): Carbon
    {
        return Carbon::now()->subDays(rand(0, $monthsBack * 30))->setTime(rand(7, 16), rand(0, 59), rand(0, 59));
    }

    private function seedKategoriSektor(): array
    {
        $data = [
            'Perizinan Berusaha' => ['Perdagangan', 'Perindustrian', 'Pariwisata', 'Pertanian', 'Perikanan'],
            'Perizinan Non Berusaha' => ['Kesehatan', 'Pendidikan', 'Sosial'],
            'Perizinan Bangunan' => ['Pekerjaan Umum', 'Tata Ruang'],
            'Perizinan Lingkungan' => ['Lingkungan Hidup', 'Kehutanan'],
            'Perizinan Transportasi' => ['Perhubungan', 'Komunikasi dan Informatika'],
        ];
        $sektor = [];
        foreach ($data as $kategori => $list) {
            $kid = DB::table('kategori_izins')->insertGetId(['nama' => $kategori] + $this->ts(now()));
            foreach ($list as $nama) {
                $sektor[] = ['id' => DB::table('sektor_izins')->insertGetId(['kategori_izin_id' => $kid, 'nama' => $nama] + $this->ts(now())), 'kategori_id' => $kid];
            }
        }
        return $sektor;
    }

    private function seedVerifikator(): array
    {
        $list = [
            ['Ni Made Ayu Lestari', 'fo@example.com', '0'],
            ['I Gede Wira Pratama', 'opd@example.com', '1'],
            ['Ni Ketut Sri Wahyuni', 'bo@example.com', '2'],
            ['I Wayan Agus Saputra', 'jf@example.com', '4'],
            ['Drs. I Nyoman Sudarma, M.Si', 'ttd@example.com', '3'],
            ['Ni Luh Putu Ariani', 'opd2@example.com', '1'],
        ];
        $result = [];
        foreach ($list as [$nama, $email, $jenis]) {
            $result[$jenis][] = DB::table('users')->insertGetId([
                'role_id' => 2, 'name' => $nama, 'email' => $email, 'telepon' => '08' . rand(1100000000, 1399999999),
                'nik' => '5102' . rand(100000000000, 999999999999), 'alamat' => 'Kantor DPMPTSP Kabupaten Tabanan',
                'jenis_kelamin' => str_starts_with($nama, 'Ni') ? 0 : 1, 'is_filled_data_register' => true,
                'email_verified_at' => now(), 'password' => $this->password,
            ] + $this->ts(Carbon::now()->subYears(2)));
        }
        return $result;
    }

    private function seedPemohon(int $count): array
    {
        $desa = ['Kediri', 'Marga', 'Penebel', 'Baturiti', 'Kerambitan', 'Selemadeg', 'Pupuan', 'Tabanan', 'Dauh Peken', 'Delod Peken', 'Banjar Anyar', 'Beraban'];
        $ids = [];
        for ($i = 0; $i < $count; $i++) {
            $gender = rand(0, 1);
            $email = $i === 0 ? 'pemohon@example.com' : Str::slug($this->faker->unique()->userName(), '.') . $i . '@example.com';
            $ids[] = DB::table('users')->insertGetId([
                'role_id' => 3,
                'name' => $i === 0 ? 'I Made Pemohon Contoh' : $this->faker->name($gender ? 'male' : 'female'),
                'email' => $email, 'telepon' => '08' . rand(1100000000, 9999999999),
                'nik' => '5102' . rand(100000000000, 999999999999),
                'alamat' => 'Jl. ' . $this->faker->streetName() . ' No. ' . rand(1, 120) . ', ' . $desa[array_rand($desa)] . ', Tabanan, Bali',
                'jenis_kelamin' => $gender, 'is_filled_data_register' => true, 'email_verified_at' => now(),
                'password' => $this->password,
            ] + $this->ts($this->randomDate(26)));
        }
        return $ids;
    }

    private function seedJenisIzin(array $sektor, array $verifikator): array
    {
        $jenis = [
            ['Izin Usaha Mikro dan Kecil (IUMK)', 'Izin bagi pelaku usaha mikro dan kecil di wilayah Kabupaten Tabanan.'],
            ['Persetujuan Bangunan Gedung (PBG)', 'Perizinan untuk membangun, mengubah, atau merawat bangunan gedung.'],
            ['Izin Praktik Dokter', 'Surat izin praktik bagi dokter umum dan dokter spesialis.'],
            ['Izin Praktik Bidan', 'Surat izin praktik bagi tenaga kebidanan.'],
            ['Izin Operasional Sekolah', 'Izin penyelenggaraan satuan pendidikan anak usia dini dan dasar.'],
            ['Tanda Daftar Usaha Pariwisata (TDUP)', 'Pendaftaran usaha pariwisata seperti villa, homestay, dan restoran.'],
            ['Izin Lingkungan (UKL-UPL)', 'Persetujuan lingkungan untuk kegiatan usaha yang berdampak lingkungan.'],
            ['Izin Trayek Angkutan', 'Izin penyelenggaraan angkutan orang dalam trayek.'],
        ];
        $forms = [
            ['Nama Usaha / Kegiatan', 'text', 'NAMA_USAHA'], ['Alamat Lokasi', 'text', 'ALAMAT_LOKASI'],
            ['Luas Lahan (m2)', 'number', 'LUAS_LAHAN'], ['Tanggal Mulai Kegiatan', 'date', 'TANGGAL_MULAI'],
        ];
        $berkas = ['KTP Pemohon', 'NPWP', 'Surat Pernyataan', 'Denah Lokasi', 'Foto Lokasi'];
        $result = [];
        foreach ($jenis as $index => [$nama, $desk]) {
            $s = $sektor[$index % count($sektor)];
            $id = DB::table('jenis_izins')->insertGetId([
                'ulid' => (string) Str::ulid(), 'nama' => $nama, 'deskripsi' => $desk, 'template_surat' => null,
                'sektor_izin_id' => $s['id'], 'kategori_izin_id' => $s['kategori_id'], 'is_pas_foto_required' => in_array($index, [2, 3]),
            ] + $this->ts(Carbon::now()->subYears(2)));
            foreach ($forms as $u => [$label, $tipe, $kode]) {
                DB::table('form_jenis_izins')->insert(['jenis_izin_id' => $id, 'label' => $label, 'tipe' => $tipe, 'kode_isian' => $kode . '_' . ($index + 1), 'urutan' => $u + 1] + $this->ts(now()));
            }
            foreach ($berkas as $u => $b) {
                DB::table('berkas_jenis_izins')->insert(['jenis_izin_id' => $id, 'nama' => $b, 'is_required' => $u < 3, 'urutan' => $u + 1] + $this->ts(now()));
            }
            DB::table('kelengkapan_jenis_izins')->insert(['jenis_izin_id' => $id, 'label' => 'Nomor Rekomendasi Teknis', 'tipe' => 'text', 'kode_isian' => 'NO_REKOM_' . ($index + 1), 'urutan' => 1] + $this->ts(now()));
            $alur = [['0', $verifikator['0'][0]], ['1', $verifikator['1'][$index % 2]], ['2', $verifikator['2'][0]], ['4', $verifikator['4'][0]], ['3', $verifikator['3'][0]]];
            foreach ($alur as $u => [$jv, $vid]) {
                DB::table('alur_jenis_izins')->insert(['jenis_izin_id' => $id, 'verifikator_id' => $vid, 'jenis_verifikator' => $jv, 'urutan' => $u + 1] + $this->ts(now()));
            }
            $result[] = ['id' => $id, 'nama' => $nama, 'deskripsi' => $desk, 'forms' => $forms, 'berkas' => $berkas, 'alur' => $alur, 'index' => $index + 1, 'pas_foto' => in_array($index, [2, 3])];
        }
        return $result;
    }

    private function seedPermohonan(array $pemohon, array $jenisIzin, int $count): array
    {
        $statuses = array_merge(array_fill(0, 45, 'selesai'), array_fill(0, 18, 'verifikasi'), array_fill(0, 15, 'permohonan_baru'),
            array_fill(0, 9, 'revisi'), array_fill(0, 6, 'verifikasi_ulang'), array_fill(0, 4, 'pending'), array_fill(0, 3, 'expired'));
        $result = [];
        for ($i = 0; $i < $count; $i++) {
            $userId = $i < 8 ? $pemohon[0] : $pemohon[array_rand($pemohon)];
            $user = DB::table('users')->find($userId);
            $j = $jenisIzin[array_rand($jenisIzin)];
            $status = $statuses[array_rand($statuses)];
            $date = $this->randomDate(24);
            if (Carbon::parse($user->created_at)->gt($date)) {
                $date = Carbon::parse($user->created_at)->addDays(rand(1, 20));
                if ($date->isFuture()) $date = Carbon::now()->subHours(rand(1, 48));
            }
            $id = DB::table('permohonans')->insertGetId([
                'user_id' => $userId, 'jenis_izin_id' => $j['id'], 'nama_jenis_izin' => $j['nama'], 'deskripsi_jenis_izin' => $j['deskripsi'],
                'nama' => $user->name, 'nik' => $user->nik, 'npwp' => rand(10, 99) . '.' . rand(100, 999) . '.' . rand(100, 999) . '.' . rand(1, 9) . '-' . rand(100, 999) . '.000',
                'tempat_lahir' => $this->faker->randomElement(['Tabanan', 'Denpasar', 'Badung', 'Gianyar', 'Buleleng', 'Jembrana', 'Klungkung']),
                'is_ttd' => $status === 'selesai', 'status' => $status, 'is_expired' => $status === 'expired',
                'is_legacy_data' => false, 'pengajuan_at' => $status === 'pending' ? null : $date,
                'is_pas_foto_required' => $j['pas_foto'],
            ] + $this->ts($date));
            DB::table('permohonans')->where('id', $id)->update(['nomor_registrasi' => 'BLL/' . $date->timestamp . '/' . $userId . '/' . $j['id'] . '/' . $id]);

            $values = [$this->faker->company(), 'Jl. ' . $this->faker->streetName() . ', Tabanan', (string) rand(50, 2500), $date->copy()->addDays(rand(10, 60))->toDateString()];
            foreach ($j['forms'] as $u => [$label, $tipe, $kode]) {
                DB::table('form_permohonans')->insert(['permohonan_id' => $id, 'label' => $label, 'tipe' => $tipe, 'kode_isian' => $kode . '_' . $j['index'], 'value' => $values[$u], 'urutan' => $u + 1] + $this->ts($date));
            }
            foreach ($j['berkas'] as $u => $b) {
                $revisi = $status === 'revisi' && $u === 1;
                DB::table('berkas_permohonans')->insert(['permohonan_id' => $id, 'nama' => $b, 'is_required' => $u < 3, 'urutan' => $u + 1, 'filepath' => null,
                    'is_revisi' => $revisi, 'catatan' => $revisi ? 'Dokumen kurang jelas, mohon unggah ulang.' : null] + $this->ts($date));
            }
            DB::table('kelengkapan_permohonans')->insert(['permohonan_id' => $id, 'label' => 'Nomor Rekomendasi Teknis', 'tipe' => 'text', 'kode_isian' => 'NO_REKOM_' . $j['index'],
                'value' => $status === 'selesai' ? 'REK/' . rand(100, 999) . '/DPMPTSP/' . $date->year : null, 'urutan' => 1] + $this->ts($date));
            $done = match ($status) { 'selesai' => 5, 'verifikasi' => rand(1, 4), 'revisi', 'verifikasi_ulang' => rand(0, 2), default => 0 };
            foreach ($j['alur'] as $u => [$jv, $vid]) {
                DB::table('alur_permohonans')->insert(['permohonan_id' => $id, 'verifikator_id' => $vid, 'jenis_verifikator' => $jv, 'urutan' => $u + 1, 'is_done' => $u < $done]
                    + $this->ts($date->copy()->addDays($u)));
            }
            $result[] = ['id' => $id, 'jenis_izin_id' => $j['id'], 'status' => $status, 'user' => $user];
        }
        return $result;
    }

    private function seedSurvey(array $jenisIzin, array $permohonan, int $count): void
    {
        $pertanyaan = DB::table('kuesioner_pertanyaans')->whereNull('group_layanan_skm_id')->whereNull('deleted_at')->pluck('id')->all();
        if (!$pertanyaan) {
            $list = [
                'Bagaimana pendapat Saudara tentang kesesuaian persyaratan pelayanan dengan jenis pelayanannya?',
                'Bagaimana pemahaman Saudara tentang kemudahan prosedur pelayanan di unit ini?',
                'Bagaimana pendapat Saudara tentang kecepatan waktu dalam memberikan pelayanan?',
                'Bagaimana pendapat Saudara tentang kewajaran biaya/tarif dalam pelayanan?',
                'Bagaimana pendapat Saudara tentang kesesuaian produk pelayanan dengan yang diberikan?',
                'Bagaimana pendapat Saudara tentang kompetensi/kemampuan petugas dalam pelayanan?',
                'Bagaimana pendapat Saudara tentang perilaku petugas dalam pelayanan terkait kesopanan dan keramahan?',
                'Bagaimana pendapat Saudara tentang kualitas penanganan pengaduan pengguna layanan?',
                'Bagaimana pendapat Saudara tentang kualitas sarana dan prasarana?',
            ];
            foreach ($list as $p) {
                $pid = DB::table('kuesioner_pertanyaans')->insertGetId(['pertanyaan' => $p] + $this->ts(now()));
                foreach (['Tidak Baik', 'Kurang Baik', 'Baik', 'Sangat Baik'] as $point => $opsi) {
                    DB::table('kuesioner_opsis')->insert(['kuesioner_pertanyaan_id' => $pid, 'opsi' => $opsi, 'point' => $point + 1] + $this->ts(now()));
                }
                $pertanyaan[] = $pid;
            }
        }
        $opsi = DB::table('kuesioner_opsis')->whereIn('kuesioner_pertanyaan_id', $pertanyaan)->get()->groupBy('kuesioner_pertanyaan_id');
        $teks = DB::table('kuesioner_pertanyaans')->whereIn('id', $pertanyaan)->pluck('pertanyaan', 'id');
        $selesai = array_values(array_filter($permohonan, fn ($p) => $p['status'] === 'selesai'));
        $pendidikan = ['SD', 'SMP', 'SMA', 'D3', 'S1', 'S2'];
        $pekerjaan = ['PNS', 'TNI/POLRI', 'Pegawai Swasta', 'Wiraswasta', 'Petani', 'Pelajar/Mahasiswa', 'Lainnya'];
        for ($i = 0; $i < $count; $i++) {
            $p = $selesai ? $selesai[$i % count($selesai)] : null;
            $date = $this->randomDate(9);
            $kid = DB::table('kuesioners')->insertGetId([
                'permohonan_id' => $i < count($selesai) ? $p['id'] : null,
                'jenis_izin_id' => $p ? $p['jenis_izin_id'] : $jenisIzin[array_rand($jenisIzin)]['id'],
                'jenis_kelamin' => rand(0, 1), 'pendidikan' => $pendidikan[array_rand($pendidikan)], 'pekerjaan' => $pekerjaan[array_rand($pekerjaan)],
                'is_from_skm' => false, 'nama' => $p ? $p['user']->name : $this->faker->name(), 'nomor_telepon' => '08' . rand(1100000000, 9999999999),
                'email' => $p ? $p['user']->email : $this->faker->safeEmail(),
            ] + $this->ts($date));
            foreach ($pertanyaan as $pid) {
                $r = rand(1, 100); $idx = $r <= 1 ? 0 : ($r <= 5 ? 1 : ($r <= 40 ? 2 : 3));
                $o = $opsi[$pid][$idx] ?? $opsi[$pid]->last();
                DB::table('kuesioner_jawabans')->insert(['kuesioner_id' => $kid, 'kuesioner_opsi_id' => $o->id, 'kuesioner_pertanyaan_id' => $pid,
                    'pertanyaan' => Str::limit($teks[$pid], 250, ''), 'opsi' => $o->opsi, 'point' => $o->point] + $this->ts($date));
            }
        }
    }

    private function seedTamu(int $count): void
    {
        for ($i = 0; $i < $count; $i++) {
            DB::table('tamus')->insert(['nama' => $this->faker->name(), 'email' => $this->faker->safeEmail(), 'telepon' => '08' . rand(1100000000, 9999999999),
                'alamat' => $this->faker->randomElement(['Kediri', 'Marga', 'Penebel', 'Baturiti', 'Kerambitan', 'Tabanan']) . ', Tabanan'] + $this->ts($this->randomDate(12)));
        }
    }

    private function seedOtp(array $pemohon): void
    {
        $emails = DB::table('users')->whereIn('id', $pemohon)->pluck('email')->all();
        for ($i = 0; $i < 120; $i++) {
            DB::table('otp_successes')->insert(['email' => $emails[array_rand($emails)], 'otp' => (string) rand(100000, 999999)] + $this->ts($this->randomDate(12)));
        }
        for ($i = 0; $i < 35; $i++) {
            DB::table('otp_faileds')->insert(['email' => $emails[array_rand($emails)], 'otp' => (string) rand(100000, 999999),
                'exception' => $this->faker->randomElement(['Kode OTP tidak valid', 'Kode OTP telah kedaluwarsa', 'Batas percobaan OTP terlampaui'])] + $this->ts($this->randomDate(12)));
        }
    }
}
