<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 20px;">
    <div style="background: linear-gradient(135deg, #102A66 0%, #1C2B78 100%); padding: 30px; text-align: center; border-radius: 10px 10px 0 0;">
        <h1 style="color: #fff; margin: 0; font-size: 24px;">SPBE Kabupaten Malang</h1>
    </div>
    
    <div style="background: #f9f9f9; padding: 30px; border-radius: 0 0 10px 10px; border: 1px solid #e0e0e0;">
        <h2 style="color: #102A66; margin-top: 0;">Reset Password</h2>
        
        <p>Halo,</p>
        
        <p>Anda menerima email ini karena kami menerima permintaan reset password untuk akun Anda.</p>
        
        <p style="margin: 30px 0;">
            <a href="{{ $url }}" 
               style="background-color: #FFA84A; color: #fff; padding: 12px 30px; text-decoration: none; border-radius: 5px; display: inline-block; font-weight: bold;">
                Reset Password
            </a>
        </p>
        
        <p style="color: #666; font-size: 14px;">
            Atau copy dan paste link berikut ke browser Anda:<br>
            <a href="{{ $url }}" style="color: #FFA84A; word-break: break-all;">{{ $url }}</a>
        </p>
        
        <div style="background: #fff3cd; border-left: 4px solid #ffc107; padding: 15px; margin: 20px 0; border-radius: 4px;">
            <p style="margin: 0; color: #856404; font-size: 14px;">
                <strong>⚠️ Penting:</strong> Link ini hanya berlaku selama <strong>10 menit</strong>. Jika Anda tidak meminta reset password, abaikan email ini.
            </p>
        </div>
        
        <p style="color: #666; font-size: 14px; margin-top: 30px;">
            Jika tombol tidak berfungsi, copy link di atas dan paste di browser Anda.
        </p>
        
        <hr style="border: none; border-top: 1px solid #e0e0e0; margin: 30px 0;">
        
        <p style="color: #999; font-size: 12px; margin: 0;">
            Email ini dikirim secara otomatis. Mohon jangan membalas email ini.
        </p>
    </div>
</body>
</html>




