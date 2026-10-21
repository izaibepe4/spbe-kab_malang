<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Akun Disetujui</title>
</head>

<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 20px;">
    <div style="background: linear-gradient(135deg, #102A66 0%, #1C2B78 100%); padding: 30px; text-align: center; border-radius: 10px 10px 0 0;">
        <h1 style="color: #fff; margin: 0; font-size: 24px;">SPBE Kabupaten Malang</h1>
    </div>

    <div style="background: #f9f9f9; padding: 30px; border-radius: 0 0 10px 10px; border: 1px solid #e0e0e0;">
        <h2 style="color: #102A66; margin-top: 0;">✅ Akun Anda Telah Disetujui!</h2>

        <p>Yth. <strong>{{ $user->name }}</strong>,</p>

        <p>Selamat! Akun Anda untuk Sistem Informasi SPBE Kabupaten Malang telah <strong>disetujui</strong> oleh administrator.</p>

        <div style="background: #fff; border: 1px solid #e0e0e0; padding: 15px; margin: 20px 0; border-radius: 4px;">
            <p style="margin: 0 0 10px 0; color: #666; font-size: 14px;"><strong>Informasi Akun:</strong></p>
            <p style="margin: 5px 0; font-size: 14px;">Nama: <strong>{{ $user->name }}</strong></p>
            <p style="margin: 5px 0; font-size: 14px;">Email: <strong>{{ $user->email }}</strong></p>
            <p style="margin: 5px 0; font-size: 14px;">OPD: <strong>{{ $user->nama_opd }}</strong></p>
            <p style="margin: 5px 0; font-size: 14px;">Status: <strong style="color: #28a745;">Aktif</strong></p>
        </div>

        <p>Anda sekarang dapat login dan mengakses sistem untuk mengelola data layanan OPD Anda.</p>

        <p style="margin: 30px 0;">
            <a href="{{ url('/login') }}"
                style="background-color: #FFA84A; color: #fff; padding: 12px 30px; text-decoration: none; border-radius: 5px; display: inline-block; font-weight: bold;">
                Login Sekarang
            </a>
        </p>

        <div style="background: #d1ecf1; border-left: 4px solid #17a2b8; padding: 15px; margin: 20px 0; border-radius: 4px;">
            <p style="margin: 0 0 10px 0; color: #0c5460; font-size: 14px;"><strong>Langkah Selanjutnya:</strong></p>
            <ol style="margin: 0; padding-left: 20px; color: #0c5460; font-size: 14px;">
                <li>Login menggunakan email dan password yang Anda daftarkan</li>
                <li>Lengkapi profil OPD Anda</li>
                <li>Mulai mengelola data layanan</li>
            </ol>
        </div>

        <p style="color: #666; font-size: 14px; margin-top: 30px;">
            Jika Anda mengalami kesulitan atau memiliki pertanyaan, silakan hubungi administrator sistem.
        </p>

        <hr style="border: none; border-top: 1px solid #e0e0e0; margin: 30px 0;">

        <p style="color: #999; font-size: 12px; margin: 0;">
            Email ini dikirim secara otomatis. Mohon jangan membalas email ini.
        </p>
    </div>
</body>

</html>