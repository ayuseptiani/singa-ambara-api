<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Carbon\Carbon; // Penting untuk set waktu verifikasi

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // 1. Buat Akun RESEPSIONIS (Agar tombol di Navbar Frontend muncul)
        // Frontend Anda mengecek: userName === "Resepsionis"
        User::create([
            'name' => 'Resepsionis',
            'email' => 'admin@hotel.com',
            'password' => bcrypt('admin123'),
            'role' => 'admin', // Role Admin
            'email_verified_at' => Carbon::now(), // LANGSUNG VERIFIED (Bypass OTP)
            'otp_code' => null,
            'otp_expires_at' => null,
        ]);

        // 2. Buat Akun SUPER OWNER (Opsional - untuk cadangan)
        User::create([
            'name' => 'Owner',
            'email' => 'owner@hotel.com',
            'password' => bcrypt('owner123'),
            'role' => 'super_admin',
            'email_verified_at' => Carbon::now(), // LANGSUNG VERIFIED
            'otp_code' => null,
            'otp_expires_at' => null,
        ]);
    }
}