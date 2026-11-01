<table>
    <thead>
        <tr>
            <td
                colspan="{{ count($laporan_survey_layanan['data']['nrrUnsur']) ? count($laporan_survey_layanan['data']['nrrUnsur']) + 1 : 4 }}">
                INDEK KEPUASAN MASYARAKAT (IKM)<br>
                DINAS PENANAMAN MODAL DAN PELAYANAN TERPADU SATU PINTU<br>
                KABUPATEN TABANAN<br>
                PERIODE {{ $tanggal_awal }} - {{ $tanggal_akhir }}
            </td>
        </tr>
        <tr>
            <td colspan="{{ count($laporan_survey_layanan['data']['nrrUnsur']) ? count($laporan_survey_layanan['data']['nrrUnsur']) + 1 : 4 }}">
                Pengolahan Data Survey {{ $groupLayananSkm?->nama ?? 'SIAP AUM' }}
            </td>
        </tr>
    </thead>
    <tbody>
        <tr></tr>
        <tr>
            <th rowspan="2">NO RESP</th>
            <th colspan="{{ count($laporan_survey_layanan['data']['nrrUnsur']) }}">NILAI UNSUR PELAYANAN</th>
        </tr>
        <tr>
            @for ($i = 1; $i <= count($laporan_survey_layanan['data']['nrrUnsur']); $i++)
                <th>U{{ $i }}</th>
            @endfor
        </tr>
        @foreach ($laporan_survey_layanan['data']['kuesioners'] as $kuesioner)
            <tr>
                <td>{{ $loop->iteration }}</td>
                @foreach ($kuesioner->kuesioner_point as $point)
                    <td>{{ $point }}</td>
                @endforeach
            </tr>
        @endforeach
        <tr>
            <td>JML Nilai/Unsur</td>
            @foreach ($laporan_survey_layanan['data']['jmlNilaiUnsur'] as $jmlNilaiUnsur)
                <td>{{ number_format($jmlNilaiUnsur, 0, ',', '.') }}</td>
            @endforeach
        </tr>
        <tr>
            <td>NRR/Unsur</td>
            @foreach ($laporan_survey_layanan['data']['nrrUnsur'] as $nrrUnsur)
                <td>{{ number_format($nrrUnsur, 2, ',', '.') }}</td>
            @endforeach
        </tr>
        <tr>
            <td>NRR Tertbg/Unsur</td>
            @foreach ($laporan_survey_layanan['data']['nrrTertbgUnsur'] as $nrrTertbgUnsur)
                <td>{{ number_format($nrrTertbgUnsur, 2, ',', '.') }}</td>
            @endforeach
        </tr>
    </tbody>
</table>
