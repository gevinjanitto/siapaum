<x-mail::message>
<h2 style="text-align: center;color:#181818;font-weight: bold;font-size:22px;margin: 10px 0;">Jumlah Usulan yang Harus Ditindaklanjuti Hari Ini</h2>
<h1 style="text-align: center;color:#AA0000;font-weight: bold;font-size:48px;margin: 10px 0;">{{ $count }}</h1>
<x-mail::button :url="route('verifikator.verifikasi.index')"
                color="red">
    Lihat Untuk Menindaklanjuti Usulan
</x-mail::button>
</x-mail::message>
