<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use Illuminate\Http\Request;

class AdminBookingController extends Controller
{
    // 1. LIHAT SEMUA BOOKING (Untuk Tabel Admin)
    public function index()
    {
        // Ambil semua booking, sertakan data User dan Kamarnya
        $bookings = Booking::with(['user', 'room']) 
                    ->orderBy('created_at', 'desc') // Yang terbaru paling atas
                    ->get();

        return response()->json($bookings);
    }

    // 2. UPDATE STATUS (Check-In / Check-Out / Cancel)
    public function updateStatus(Request $request, $id)
    {
        $booking = Booking::findOrFail($id);
        
        // Validasi status yang boleh masuk
        $request->validate([
            'status' => 'required|in:confirmed,checked_in,checked_out,cancelled'
        ]);

        $booking->status = $request->status;
        
        // Logika bisnis: Jika tamu Check-In, otomatis anggap sudah LUNAS
        if ($request->status == 'checked_in') {
            $booking->payment_status = 'paid';
        }

        $booking->save();

        return response()->json([
            'message' => 'Status berhasil diupdate!', 
            'data' => $booking
        ]);
    }
}