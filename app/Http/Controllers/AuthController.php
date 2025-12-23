<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;   // <--- Penting untuk Transaksi
use Illuminate\Support\Facades\Mail; // <--- Penting untuk Email
use Carbon\Carbon;
use App\Mail\VerificationEmail;
use App\Mail\ResetPasswordEmail;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    // 1. REGISTER USER BARU
    public function register(Request $request)
    {
        // 1. Validasi Input
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        // 2. Mulai Transaksi Database
        // Ini kuncinya: Kita kunci database dulu, jangan simpan permanen.
        DB::beginTransaction();

        try {
            $otp = rand(100000, 999999);

            // 3. Simpan User (Masih dalam memori sementara)
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role' => 'user', 
                'otp_code' => $otp,
                'otp_expires_at' => Carbon::now()->addMinutes(10),
                'email_verified_at' => null,
            ]);

            \Log::info('Mencoba kirim email dari: ' . config('mail.from.address'));
            \Log::info('Username Login SMTP: ' . config('mail.mailers.smtp.username'));

            // 4. Coba Kirim Email
            // Jika ini gagal, dia akan loncat ke blok 'catch'
            // Mail::raw("Hello {$user->name}, your OTP code is: {$otp}", function ($message) use ($user){
            //     $message->to($user->email)
            //             ->subject('Kode Verifikasi Singa Ambara');
            // });
            Mail::to($user->email)->send(new VerificationEmail($otp));

            // 5. Jika email berhasil terkirim, baru simpan User secara permanen
            DB::commit();

            return response()->json([
                'message' => 'Registrasi berhasil. Silahkan cek email Anda untuk kode OTP.',
                'user' => $user->email,
            ], 201);

        } catch (\Exception $e) {
            // 6. Jika ada error (Email gagal, Koneksi putus, dll)
            // BATALKAN penyimpanan User. Database kembali bersih.
            DB::rollBack();

            return response()->json([
                'message' => 'Gagal registrasi: ' . $e->getMessage()
            ], 500);
        }
    }

    // ... (Fungsi verifyOtp, login, logout, userProfile biarkan tetap sama seperti sebelumnya) ...
    public function verifyOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'otp' => 'required|string|size:6',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return response()->json(['message' => 'User tidak ditemukan'], 404);
        }

        if ($user->otp_code !== $request->otp) {
            return response()->json(['message' => 'Kode OTP salah'], 400);
        }

        if (Carbon::now()->greaterThan($user->otp_expires_at)) {
            return response()->json(['message' => 'Kode OTP sudah kadaluarsa'], 400);
        }

        $user->email_verified_at = Carbon::now();
        $user->otp_code = null;
        $user->save();

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Verifikasi berhasil!',
            'access_token' => $token,
            'token_type' => 'Bearer',
            'user' => $user
        ]);
    }

    // 2. LOGIN USER
    public function login(Request $request)
    {
        // Validasi input
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        // 1. Cari User berdasarkan Email
        $user = User::where('email', $request->email)->first();

        // 2. Cek apakah User ada DAN Password cocok
        // Kita pakai Hash::check untuk membandingkan password teks dengan hash di DB
        if (! $user || ! Hash::check($request->password, $user->password)) {
            return response()->json([
                'message' => 'Login gagal. Email atau password salah.'
            ], 401);
        }

        // 3. Hapus token lama (Opsional - agar tidak menumpuk token di DB)
        // $user->tokens()->delete(); 

        // 4. Buat token baru
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Login berhasil',
            'user' => $user,
            'access_token' => $token,
            'token_type' => 'Bearer',
            'role' => $user->role 
        ]);
    }

// ... di dalam class AuthController ...

    // A. KIRIM LINK RESET
    public function forgotPassword(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $user = User::where('email', $request->email)->first();
        if (!$user) {
            return response()->json(['message' => 'Email tidak ditemukan'], 404);
        }

        // 1. Buat Token Random
        $token = Str::random(60);

        // 2. Simpan ke tabel password_reset_tokens
        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $request->email],
            [
                'email' => $request->email,
                'token' => $token, // Token tidak perlu di-hash untuk kesederhanaan saat ini
                'created_at' => Carbon::now()
            ]
        );

        // 3. Kirim Email
        Mail::to($request->email)->send(new ResetPasswordEmail($token, $request->email));

        return response()->json(['message' => 'Link reset password telah dikirim ke email Anda.']);
    }

    // B. PROSES UBAH PASSWORD
    public function resetPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'token' => 'required',
            'password' => 'required|min:8|confirmed' // Password baru + Konfirmasi
        ]);

        // 1. Cek Token di Database
        $resetRecord = DB::table('password_reset_tokens')
            ->where('email', $request->email)
            ->where('token', $request->token)
            ->first();

        if (!$resetRecord) {
            return response()->json(['message' => 'Token tidak valid atau email salah.'], 400);
        }

        // 2. Update Password User
        $user = User::where('email', $request->email)->first();
        $user->update(['password' => Hash::make($request->password)]);

        // 3. Hapus Token agar tidak bisa dipakai lagi
        DB::table('password_reset_tokens')->where('email', $request->email)->delete();

        return response()->json(['message' => 'Password berhasil diubah. Silahkan login.']);
    }



    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|string|min:8|confirmed', // butuh new_password_confirmation
        ]);

        $user = $request->user(); // Ambil user yg sedang login

        // 1. Cek apakah password lama benar
        if (!Hash::check($request->current_password, $user->password)) {
            return response()->json([
                'message' => 'Password saat ini salah.'
            ], 400);
        }

        // 2. Update Password Baru
        $user->update([
            'password' => Hash::make($request->new_password)
        ]);

        return response()->json([
            'message' => 'Password berhasil diperbarui!'
        ]);
    }

    // 3. LOGOUT USER
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json(['message' => 'Logout berhasil']);
    }

    public function userProfile(Request $request)
    {
        return response()->json($request->user());
    }
}