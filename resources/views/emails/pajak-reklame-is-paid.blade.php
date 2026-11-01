<x-mail::message>
<p style="text-align: start;color:hsl(0, 0%, 9%);margin: 10px;"><strong>Halo, {{ $verifikator->name }}!</strong></p>
<p style="text-align: start;color:#181818;margin: 10px;">Kami informasikan bahwa pemohon dengan nomor registrasi <strong style="color: #181818;">{{ $permohonan->nomor_registrasi }}</strong> telah menyelesaikan pembayaran pajak SKPD untuk permohonan izin reklame</p>
<x-mail::button :url="route('verifikator.verifikasi.show', $permohonan->id)"
                color="red">
    Verifikasi Pembayaran
</x-mail::button>
<p style="text-align: start;color:#181818;margin: 10px;">Mohon untuk segera melakukan proses verifikasi pembayaran pada aplikasi SIAP AUM agar permohonan tersebut dapat dilanjutkan ke tahap berikutnya</p>
</x-mail::message>
