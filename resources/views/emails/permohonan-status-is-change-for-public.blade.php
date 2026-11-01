<x-mail::message>
<h2 style="text-align: center;color:#181818;font-weight: bold;font-size:22px;margin: 10px 0;">Status Usulan Anda Diperbaharui</h2>
<p style="text-align: center;color:#181818;margin-bottom:0;">Jenis Izin: {{ $permohonan->nama_jenis_izin }}</p>
<p style="text-align: center;color:#181818;margin-bottom:0;">Nomor Registrasi: <strong style="color: #181818;">{{ $permohonan->nomor_registrasi }}</strong></p>
<h1 style="text-align: center;color:#AA0000;font-weight: bold;font-size:32px;margin: 10px 0;">{{ $status }}</h1>
<x-mail::button :url=$route_url
                color="red">
    Lihat Usulan
</x-mail::button>
</x-mail::message>
