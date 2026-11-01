<table>
    <thead>
        <tr>
            <th colspan="15"
                style="text-align: center; font-weight: bold">DATA PERIZINAN BERUSAHA DAN NONPERIZINAN TAHUN
                {{ $tahun }}</th>
        </tr>
        <tr>
            <th>NO</th>
            <th>JENIS IZIN</th>
            <th>Jan</th>
            <th>Feb</th>
            <th>Mar</th>
            <th>April</th>
            <th>Mei</th>
            <th>Juni</th>
            <th>Juli</th>
            <th>Agus</th>
            <th>Sept</th>
            <th>Okt</th>
            <th>Nop</th>
            <th>Des</th>
            <th>Total</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($data as $kategori)
            <tr>
                <td></td>
                <td>{{ $kategori->get('nama_kategori') }}</td>
            </tr>
            @foreach ($kategori->get('sektorIzin') as $sektor)
                <tr>
                    <td></td>
                    <td>{{ $sektor->nama }}</td>
                </tr>
                @foreach ($sektor->jenisIzin as $jenis)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $jenis->nama }}</td>
                        @foreach ($jenis->statistik as $statistik)
                            <td>{{ $statistik }}</td>
                        @endforeach
                        <td>{{ $jenis->statistik_total }}</td>
                    </tr>
                @endforeach
                <tr>
                    <td colspan="2">Jumlah</td>
                    @foreach ($sektor->statistik as $statistik)
                        <td>{{ $statistik }}</td>
                    @endforeach
                    <td>{{ $sektor->statistik_total }}</td>
                </tr>
            @endforeach
        @endforeach
    </tbody>
</table>
