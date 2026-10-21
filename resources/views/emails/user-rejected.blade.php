<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pendaftaran Ditolak</title>
</head>

<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 20px;">
    <div style="background: linear-gradient(135deg, #102A66 0%, #1C2B78 100%); padding: 30px; text-align: center; border-radius: 10px 10px 0 0;">
        <h1 style="color: #fff; margin: 0; font-size: 24px;">SPBE Kabupaten Malang</h1>
    </div>

    <div style="background: #f9f9f9; padding: 30px; border-radius: 0 0 10px 10px; border: 1px solid #e0e0e0;">
        <h2 style="color: #102A66; margin-top: 0;">Pendaftaran Akun Ditolak</h2>

        <p>Yth. <strong>{{ $user->name }}</strong>,</p>

        <p>Kami informasikan bahwa pendaftaran akun Anda untuk Sistem Informasi SPBE Kabupaten Malang <strong>tidak dapat disetujui</strong> oleh administrator.</p>

        <div style="background: #fff; border: 1px solid #e0e0e0; padding: 15px; margin: 20px 0; border-radius: 4px;">
            <p style="margin: 0 0 10px 0; color: #666; font-size: 14px;"><strong>Informasi Pendaftaran:</strong></p>
            <p style="margin: 5px 0; font-size: 14px;">Nama: <strong>{{ $user->name }}</strong></p>
            <p style="margin: 5px 0; font-size: 14px;">Email: <strong>{{ $user->email }}</strong></p>
            <p style="margin: 5px 0; font-size: 14px;">OPD: <strong>{{ $user->nama_opd }}</strong></p>
            <p style="margin: 5px 0; font-size: 14px;">Status: <strong style="color: #dc3545;">Ditolak</strong></p>
        </div>

        @if($reason)
        <div style="background: #fff3cd; border-left: 4px solid #ffc107; padding: 15px; margin: 20px 0; border-radius: 4px;">
            <p style="margin: 0 0 5px 0; color: #856404; font-size: 14px;"><strong>Alasan Penolakan:</strong></p>
            <p style="margin: 0; color: #856404; font-size: 14px;">{{ $reason }}</p>
        </div>
        @endif

        <div style="background: #d1ecf1; border-left: 4px solid #17a2b8; padding: 15px; margin: 20px 0; border-radius: 4px;">
            <p style="margin: 0 0 10px 0; color: #0c5460; font-size: 14px;"><strong>Langkah Selanjutnya:</strong></p>
            <ul style="margin: 0; padding-left: 20px; color: #0c5460; font-size: 14px;">
                <li>Periksa kembali informasi yang Anda berikan</li>
                <li>Pastikan Anda terdaftar sebagai pegawai OPD yang bersangkutan</li>
                <li>Hubungi administrator untuk klarifikasi lebih lanjut</li>
            </ul>
        </div>

        <p style="color: #666; font-size: 14px;">
            Untuk informasi lebih lanjut atau bantuan, silakan hubungi:<br>
            <strong>Dinas Komunikasi dan Informatika Kabupaten Malang</strong><br>
            Email: <a href="mailto:{{ config('mail.from.address') }}" style="color: #FFA84A;">{{ config('mail.from.address') }}</a>
        </p>

        <hr style="border: none; border-top: 1px solid #e0e0e0; margin: 30px 0;">

        <p style="color: #999; font-size: 12px; margin: 0;">
            Email ini dikirim secara otomatis. Mohon jangan membalas email ini.
        </p>
    </div>
</body>

</html>