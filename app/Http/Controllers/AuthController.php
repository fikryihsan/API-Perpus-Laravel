<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    // 1. Fungsi Register 
    public function register(Request $request)
    {
        $request->validate([
            'username' => 'required|string|unique:users,name', // Validasi username unik
            'password' => 'required|string|min:6'
        ], [
            // Pesan error 
            'username.required' => 'Username wajib diisi ya!',
            'username.unique'   => 'Username ini sudah dipakai orang lain, mas!',
            'password.required' => 'Password-nya jangan lupa diisi.',
            'password.min'      => 'Password minimal harus 6 karakter.'
        ]);

        $user = User::create([
            'name' => $request->username,
            'email' => $request->username . '@perpus.com', // Email otomatis di background
            'password' => Hash::make($request->password)   // Enkripsi password
        ]);

        return response()->json([
            'message' => 'User baru berhasil didaftarkan!',
            'user' => [
                'username' => $user->name
            ]
        ], 201);
    }

    // 2. Fungsi Login (Menggunakan Username)
    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required',
            'password' => 'required'
        ]);

        // Cari user berdasarkan username (kolom name)
        $user = User::where('name', $request->username)->first();

        // Cek username dan kecocokan password
        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'message' => 'Username atau password salah!'
            ], 401);
        }

        // Bimsalabim! Terbitkan token
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Login sukses!',
            'access_token' => $token,
            'token_type' => 'Bearer'
        ], 200);
    }

    // 3. Fungsi Logout
    public function logout(Request $request)
    {
        // Hapus token yang sedang aktif digunakan
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Berhasil logout, token telah dihapus!'
        ], 200);
    }
}