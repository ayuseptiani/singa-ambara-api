<!DOCTYPE html>
<html>
<head><title>Reset Password</title></head>
<body style="font-family: Arial;">
    <h2>Permintaan Reset Password</h2>
    <p>Seseorang meminta untuk mereset password akun Singa Ambara Suites Anda.</p>
    <p>Klik tombol di bawah ini untuk mengganti password:</p>
    
    <a href="http://localhost:3000/reset-password?token={{ $token }}&email={{ $email }}" 
       style="background-color: #D4AF37; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;">
       Reset Password Saya
    </a>

    <p style="margin-top: 20px;">Atau copy link ini: <br>
    http://localhost:3000/reset-password?token={{ $token }}&email={{ $email }}
    </p>
</body>
</html>