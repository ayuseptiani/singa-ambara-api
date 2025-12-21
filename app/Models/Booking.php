<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    use HasFactory;

    protected $guarded = ['id']; // Izinkan semua kolom diisi

    // Relasi: Booking milik User
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relasi: Booking milik Room
    public function room()
    {
        return $this->belongsTo(Room::class);
    }
}