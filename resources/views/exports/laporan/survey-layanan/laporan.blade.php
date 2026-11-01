<table>
    <thead>
        <tr>
            <td colspan="4">
                INDEK KEPUASAN MASYARAKAT (IKM)<br>
                DINAS PENANAMAN MODAL DAN PELAYANAN TERPADU SATU PINTU<br>
                KABUPATEN TABANAN<br>
                PERIODE {{ $tanggal_awal }} - {{ $tanggal_akhir }}
            </td>
        </tr>
        <tr>
            <td colspan="4">
                Laporan Survey {{ $groupLayananSkm?->nama ?? 'SIAP AUM' }}
            </td>
        </tr>
    </thead>
    <tbody>
        {{-- Jenis Kelamin --}}
        <tr></tr>
        <tr>
            <td>1</td>
            <td colspan="3">Jenis Kelamin</td>
        </tr>
        <tr>
            <td colspan="4">Tabel 1</td>
        </tr>
        <tr>
            <td colspan="4">Responden Menurut Karakteristik Jenis Kelamin</td>
        </tr>
        <tr>
            <td>Nomor</td>
            <td>Jenis Kelamin</td>
            <td>Frekuensi (Orang)</td>
            <td>Prosentase (%)</td>
        </tr>
        @foreach ($laporan_survey_layanan['statistik']->get('jenis_kelamin') as $key => $value)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $key }}</td>
                <td>{{ $value }}</td>
                <td>{{ $laporan_survey_layanan['statistik']->get('jenis_kelamin_presentasi')->get($key) }}</td>
            </tr>
        @endforeach
        <tr>
            <td colspan="2">Total</td>
            <td>
                {{ $laporan_survey_layanan['statistik']->get('jenis_kelamin')->sum() }}
            </td>
            <td>
                {{ $laporan_survey_layanan['statistik']->get('jenis_kelamin_presentasi')->sum() }}
            </td>
        </tr>


        {{-- Pendidikan Terakhit --}}
        <tr></tr>
        <tr>
            <td>2</td>
            <td colspan="3">Pendidikan Terakhir</td>
        </tr>
        <tr>
            <td colspan="4">Tabel 2</td>
        </tr>
        <tr>
            <td colspan="4">Responden Menurut Karakteristik Pendidikan Terakhir</td>
        </tr>
        <tr>
            <td>Nomor</td>
            <td>Pendidikan Terakhir</td>
            <td>Frekuensi (Orang)</td>
            <td>Prosentase (%)</td>
        </tr>
        @foreach ($laporan_survey_layanan['statistik']->get('pendidikan_terakhir') as $key => $value)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $key }}</td>
                <td>{{ $value }}</td>
                <td>{{ $laporan_survey_layanan['statistik']->get('pendidikan_terakhir_presentasi')->get($key) }}</td>
            </tr>
        @endforeach
        <tr>
            <td colspan="2">Total</td>
            <td>
                {{ $laporan_survey_layanan['statistik']->get('pendidikan_terakhir')->sum() }}
            </td>
            <td>
                {{ $laporan_survey_layanan['statistik']->get('pendidikan_terakhir_presentasi')->sum() }}
            </td>
        </tr>

        {{-- Jenis Pekerjaan --}}
        <tr></tr>
        <tr>
            <td>3</td>
            <td colspan="3">Jenis Pekerjaan</td>
        </tr>
        <tr>
            <td colspan="4">Tabel 3</td>
        </tr>
        <tr>
            <td colspan="4">Responden Menurut Karakteristik Jenis Pekerjaan</td>
        </tr>
        <tr>
            <td>Nomor</td>
            <td>Jenis Pekerjaan</td>
            <td>Frekuensi (Orang)</td>
            <td>Prosentase (%)</td>
        </tr>
        @foreach ($laporan_survey_layanan['statistik']->get('jenis_pekerjaan') as $key => $value)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $key }}</td>
                <td>{{ $value }}</td>
                <td>{{ $laporan_survey_layanan['statistik']->get('jenis_pekerjaan_presentasi')->get($key) }}</td>
            </tr>
        @endforeach
        <tr>
            <td colspan="2">Total</td>
            <td>
                {{ $laporan_survey_layanan['statistik']->get('jenis_pekerjaan')->sum() }}
            </td>
            <td>
                {{ $laporan_survey_layanan['statistik']->get('jenis_pekerjaan_presentasi')->sum() }}
            </td>
        </tr>
        <tr></tr>
        <tr></tr>
        <tr>
           <td colspan="4">Kesimpulan Nilai Indeks Kepuasan Masyarakat</td>
        </tr>
        <tr>
            <td colspan="4">{{  number_format($laporan_survey_layanan['data']['persentaseNilaiIKM'], 2, ',', '.') }}</td>
        </tr>
    </tbody>
</table>
