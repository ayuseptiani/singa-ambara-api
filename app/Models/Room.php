<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Room extends Model
{
    use HasFactory;

    // Izinkan semua kolom diisi (Mass Assignment)
    protected $guarded = ['id'];

    // PENTING: Ini yang mengatasi error "Array to string conversion"
    // Kita memberitahu Laravel: "Tolong kolom 'facilities' otomatis ubah jadi Array saat diambil, dan jadi JSON saat disimpan."
   protected $casts = [
        'price' => 'integer',      // Memaksa harga jadi angka (integer)
        'facilities' => 'array',   // Memaksa fasilitas jadi Array asli (bukan string JSON)
    ];

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }
}