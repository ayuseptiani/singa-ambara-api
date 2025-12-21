<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    // 1. REGISTER USER BARU
    public function register(Request $request)
    {
        // Validasi input
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
        ]);

        // Buat user di database
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'user', // Default role
        ]);

        // Buat token (Tiket masuk)
        // $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Registrasi berhasil',
            'user' => $user,
            // 'access_token' => $token,
            // 'token_type' => 'Bearer',
        ], 201);
    }

    // 2. LOGIN USER
    public function login(Request $request)
    {
        if (!Auth::attempt($request->only('email', 'password'))) {
            return response()->json([
                'message' => 'Login gagal. Cek email atau password Anda.'
            ], 401);
        }

        $user = User::where('email', $request['email'])->firstOrFail();
        
        // Buat token baru
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Login berhasil',
            'user' => $user,
            'access_token' => $token,
            'token_type' => 'Bearer',
            'role' => $user->role // Kirim role juga biar frontend tau
        ]);
    }

    // 3. LOGOUT (PERBAIKAN PENTING DI SINI)
    public function logout(Request $request)
    {
        // PERHATIKAN: Kita hanya menghapus "currentAccessToken" (Token saat ini).
        // Ini TIDAK MENGHAPUS USER dari database.
        // Hanya menghapus tiket masuknya saja.
        
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Logout berhasil'
        ]);
    }

    // 4. CEK USER PROFILE
    public function userProfile(Request $request)
    {
        return response()->json($request->user());
    }
}