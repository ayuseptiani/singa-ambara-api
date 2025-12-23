<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class AdminUserController extends Controller
{
    public function index(Request $request)
    {
        // Cek apakah yang request adalah Super Admin
        if ($request->user()->role !== 'super_admin') {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        // Ambil semua data kecuali diri sendiri
        $users = User::where('id', '!=', auth()->id())->orderBy('role', 'asc')->get();
        return response()->json($users);
    }

    public function updateRole(Request $request, $id)
    {
        if ($request->user()->role !== 'super_admin') {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $targetUser = User::findOrFail($id);
        
        // Logic Toggle: Jika Admin -> jadi User, Jika User -> jadi Admin
        $targetUser->role = ($targetUser->role === 'admin') ? 'user' : 'admin';
        $targetUser->save();

        return response()->json(['message' => "Role user berhasil diubah menjadi {$targetUser->role}"]);
    }

    public function destroy(Request $request, $id)
    {
        if ($request->user()->role !== 'super_admin') {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        User::findOrFail($id)->delete();
        return response()->json(['message' => 'User berhasil dihapus']);
    }
}