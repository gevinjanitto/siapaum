<x-mail::message>
<h2 style="text-align: center;color:#181818;font-weight: bold;font-size:22px;margin: 10px 0;">Verifikasi Email</h2>

<p style="text-align: start;color:#181818;margin: 10px;">Berikut adalah kode OTP untuk verifikasi email Anda. Jangan berikan kode ini kepada siapapun.</p>
<x-mail::panel>
    <strong>{{ $otp }}</strong>
</x-mail::panel>

<p style="text-align: start;color:#181818;margin: 10px;">Jika Anda tidak merasa melakukan tindakan ini, abaikan email ini</p>

</x-mail::message>
