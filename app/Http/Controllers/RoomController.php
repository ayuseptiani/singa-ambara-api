<?php

namespace App\Http\Controllers;

use App\Models\Room;
use Illuminate\Http\Request;

class RoomController extends Controller
{
    // Ambil semua kamar (Public)
    public function index()
    {
        return Room::all();
    }

    // Ambil detail 1 kamar (Public)
    public function show($slug)
    {
        return Room::where('slug', $slug)->firstOrFail();
    }

    // --- TAMBAHAN BARU: FUNGSI UPDATE (KHUSUS ADMIN) ---
    public function update(Request $request, $id)
    {
        // Cari kamar berdasarkan ID, kalau ga ada error 404
        $room = Room::findOrFail($id);

        // Validasi input dari Admin
        $request->validate([
            'price'       => 'numeric', // Harga harus angka
            'total_units' => 'integer', // Stok harus bulat
            'capacity'    => 'integer', // Kapasitas harus bulat
        ]);

        // Simpan perubahan ke database
        // Kita gunakan $request->only biar aman, cuma field ini yang boleh diganti
        $room->update($request->only([
            'price', 
            'total_units', 
            'capacity'
        ]));

        return response()->json([
            'message' => 'Data kamar berhasil diperbarui',
            'data'    => $room
        ]);
    }
}