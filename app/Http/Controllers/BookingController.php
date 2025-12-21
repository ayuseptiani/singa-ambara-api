<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class BookingController extends Controller
{
    public function store(Request $request)
    {
        Log::info('Mencoba booking:', $request->all());

        // 1. Validasi Input Tambahan
        $request->validate([
            'room_id' => 'required|exists:rooms,id',
            'check_in' => 'required|date',
            'check_out' => 'required|date|after:check_in',
            'total_price' => 'required|integer',
            'guest_name' => 'required|string',   // Inputan user (sesuai KTP)
            'phone_number' => 'required|string', // Inputan user
            'payment_method' => 'required|string', // QRIS, Transfer, dll
        ]);

        $user = $request->user();

        // Hitung durasi malam
        $checkIn = Carbon::parse($request->check_in);
        $checkOut = Carbon::parse($request->check_out);
        $totalDays = $checkIn->diffInDays($checkOut);

        try {
            // 2. Simpan ke Database
            $booking = Booking::create([
                'user_id' => $user->id,
                'room_id' => $request->room_id,
                
                // Data Tamu & Kontak
                'guest_name' => $request->guest_name, 
                'phone_number' => $request->phone_number,
                
                // Data Booking
                'check_in' => $request->check_in,
                'check_out' => $request->check_out,
                'total_days' => $totalDays,
                'total_price' => $request->total_price,
                
                // Data Pembayaran
                'payment_method' => $request->payment_method,
                'payment_status' => 'paid', // Kita anggap LUNAS karena simulasi sukses
                'status' => 'confirmed'
            ]);

            return response()->json([
                'message' => 'Booking berhasil!',
                'data' => $booking
            ], 201);

        } catch (\Exception $e) {
            Log::error('Gagal Simpan Booking: ' . $e->getMessage());
            return response()->json(['message' => 'Server Error: ' . $e->getMessage()], 500);
        }
    }

    public function index(Request $request)
    {
        $bookings = Booking::with('room')
                    ->where('user_id', $request->user()->id)
                    ->orderBy('created_at', 'desc')
                    ->get();

        return response()->json($bookings);
    }
}