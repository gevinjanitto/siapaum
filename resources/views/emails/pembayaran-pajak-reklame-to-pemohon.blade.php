<x-mail::message>
<p style="text-align: start;color:hsl(0, 0%, 9%);margin: 10px;"><strong>Halo, {{ $permohonan->user->name }}!</strong></p>
<p style="text-align: start;color:#181818;margin: 10px;">Kami informasikan bahwa SKPD Pajak Reklame untuk permohonan Izin Reklame dengan nomor registrasi <strong style="color: #181818;">{{ $permohonan->nomor_registrasi }}</strong> telah terbit. Untuk melihat detailnya, silakan login ke akun Anda pada aplikasi SIAP AUM</p>
<x-mail::button :url=$route_url
                color="red">
    Upload Bukti Bayar
</x-mail::button>
<p style="text-align: start;color:#181818;margin: 10px;">Harap diperhatikan bahwa batas akhir pembayaran SKPD sesuai dengan tanggal yang tertera pada berkas SKPD. Mohon segera melakukan pembayaran agar permohonan izin Anda dapat kami proses lebih lanjut</p>
<p style="text-align: start;color:#181818;margin: 10px;">Setelah menyelesaikan pembayaran, harap mengunggah file bukti pembayaran melalui aplikasi SIAP AUM. Pastikan file bukti bayar diunggah sesuai dengan nomor registrasi <strong style="color: #181818;">{{ $permohonan->nomor_registrasi }}</strong> yang tercantum pada SKPD</p>
</x-mail::message>
