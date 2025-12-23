<!DOCTYPE html>
<html>
<head>
    <title>Verifikasi OTP</title>
</head>
<body style="font-family: Arial, sans-serif; text-align: center; padding: 20px;">
    
    <div style="max-width: 600px; margin: 0 auto; border: 1px solid #ddd; padding: 20px; border-radius: 10px;">
        <h2 style="color: #D4AF37;">Singa Ambara Suites</h2>
        <hr>
        <p>Halo,</p>
        <p>Terima kasih telah mendaftar. Gunakan kode OTP berikut untuk memverifikasi akun Anda:</p>
        
        <div style="background-color: #f4f4f4; padding: 15px; font-size: 24px; font-weight: bold; letter-spacing: 5px; margin: 20px 0;">
            {{ $otp }}
        </div>

        <p>Kode ini hanya berlaku selama 10 menit.</p>
        <p style="font-size: 12px; color: #888;">Jika Anda tidak merasa mendaftar, abaikan email ini.</p>
    </div>

</body>
</html>