<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Room;
use App\Models\Booking;

class AvailabilityController extends Controller
{
    public function check(Request $request)
    {
        // 1. Validasi Input
        $request->validate([
            'check_in' => 'required|date|after_or_equal:today',
            'check_out' => 'required|date|after:check_in',
            'adults' => 'integer|min:1',
            'children' => 'integer|min:0',
        ]);

        $checkIn = $request->check_in;
        $checkOut = $request->check_out;
        $totalGuests = $request->adults + $request->children;

        // 2. Ambil Semua Kamar
        $rooms = Room::all();
        $availableRooms = [];

        foreach ($rooms as $room) {
            // 3. Cek Kapasitas (Skip jika kamar kekecilan)
            // (Opsional: Matikan if ini jika ingin tetap menampilkan kamar meski overcapacity)
            if ($totalGuests > $room->capacity) {
                continue; 
            }

            // 4. Hitung Kamar yang TERBOOKING di tanggal tersebut
            // Logika: Cari booking yang statusnya SUDAH BAYAR (PAID) atau CONFIRMED
            // Dan tanggalnya bertabrakan (Overlap)
            $bookedCount = Booking::where('room_id', $room->id)
                ->where(function ($query) {
                    $query->where('status', 'PAID')
                          ->orWhere('status', 'CONFIRMED');
                })
                ->where(function ($q) use ($checkIn, $checkOut) {
                    // Logika Overlap Tanggal
                    $q->whereBetween('check_in', [$checkIn, $checkOut])
                      ->orWhereBetween('check_out', [$checkIn, $checkOut])
                      ->orWhere(function ($sub) use ($checkIn, $checkOut) {
                          $sub->where('check_in', '<', $checkIn)
                              ->where('check_out', '>', $checkOut);
                      });
                })
                ->count();

            // 5. Hitung Sisa Kamar
            $sisaKamar = $room->total_units - $bookedCount;

            // Jika masih ada sisa, masukkan ke daftar available
            if ($sisaKamar > 0) {
                $availableRooms[] = [
                    'id' => $room->id,
                    'name' => $room->name,
                    'slug' => $room->slug,
                    'image' => $room->image,
                    'price' => $room->price,
                    'capacity' => $room->capacity,
                    'total_units' => $room->total_units,
                    'available_qty' => $sisaKamar, // <--- Data Penting!
                ];
            }
        }

        return response()->json([
            'message' => 'Cek ketersediaan berhasil',
            'check_in' => $checkIn,
            'check_out' => $checkOut,
            'total_guests' => $totalGuests,
            'rooms' => $availableRooms
        ]);
    }
}