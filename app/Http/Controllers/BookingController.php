<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Room; 
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class BookingController extends Controller
{
    public function store(Request $request)
    {
        Log::info('Mencoba booking:', $request->all());

        // 1. Validasi Input
        $request->validate([
            'room_id' => 'required|exists:rooms,id',
            'check_in' => 'required|date|after_or_equal:today',
            'check_out' => 'required|date|after:check_in',
            'total_price' => 'required|integer',
            'guest_name' => 'required|string',
            'phone_number' => 'required|string',
            'payment_method' => 'required|string',
            
            // 🔥 UPDATE BARU: Validasi Jumlah Kamar
            'quantity' => 'required|integer|min:1', 
        ]);

        // -----------------------------------------------------------
        // 🔥 LOGIKA BARU: MULTI-QUANTITY CHECK
        // -----------------------------------------------------------
        
        $room = Room::findOrFail($request->room_id);

        // Hitung total kamar yang SUDAH terpesan (SUM Quantity)
        // Kalau ada 1 booking tapi isinya 3 kamar, maka dihitung 3 unit terpakai.
        $bookedQty = Booking::where('room_id', $request->room_id)
            ->where(function ($query) {
                // Booking dianggap aktif jika statusnya:
                $query->where('status', 'confirmed')
                      ->orWhere('status', 'paid')
                      ->orWhere('status', 'checked_in');
            })
            ->where(function ($q) use ($request) {
                // Logika Matematika Overlap Tanggal:
                $q->where('check_in', '<', $request->check_out)
                  ->where('check_out', '>', $request->check_in);
            })
            ->sum('quantity'); // 👈 PENTING: Gunakan SUM, bukan COUNT lagi

        // Sisa Stok Real
        $sisaStok = $room->total_units - $bookedQty;

        // Cek apakah sisa stok cukup untuk permintaan user
        // Misal sisa 2, user minta 3 -> TOLAK
        if ($sisaStok < $request->quantity) {
            return response()->json([
                'message' => "Maaf, sisa kamar di tanggal ini hanya {$sisaStok} unit. Anda meminta {$request->quantity} unit."
            ], 400);
        }
        
        // -----------------------------------------------------------
        // END PROTEKSI BACKEND
        // -----------------------------------------------------------

        $user = $request->user();
        $checkIn = Carbon::parse($request->check_in);
        $checkOut = Carbon::parse($request->check_out);
        $totalDays = $checkIn->diffInDays($checkOut);

        try {
            // 2. Simpan ke Database
            $booking = Booking::create([
                'user_id' => $user->id,
                'room_id' => $request->room_id,
                'guest_name' => $request->guest_name, 
                'phone_number' => $request->phone_number,
                'check_in' => $request->check_in,
                'check_out' => $request->check_out,
                'total_days' => $totalDays,
                'total_price' => $request->total_price, // Harga ini sudah dikali qty di frontend
                
                // 🔥 UPDATE BARU: Simpan Quantity ke Database
                'quantity' => $request->quantity,       
                
                'payment_method' => $request->payment_method,
                'payment_status' => 'paid',
                'status' => 'confirmed'
            ]);

            return response()->json(['message' => 'Booking berhasil!', 'data' => $booking], 201);

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