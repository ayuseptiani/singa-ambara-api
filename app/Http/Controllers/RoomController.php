<?php

namespace App\Http\Controllers;

use App\Models\Room;
use Illuminate\Http\Request;

class RoomController extends Controller
{
    // Ambil semua data kamar
    public function index()
    {
        return response()->json(Room::all());
    }

    // Ambil detail 1 kamar berdasarkan slug (contoh: /rooms/panji)
    public function show($slug)
    {
        $room = Room::where('slug', $slug)->first();

        if (!$room) {
            return response()->json(['message' => 'Kamar tidak ditemukan'], 404);
        }

        return response()->json($room);
    }
}