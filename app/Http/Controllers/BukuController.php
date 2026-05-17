<?php
namespace App\Http\Controllers;
use App\Models\Buku;
use Illuminate\Http\Request;

class BukuController extends Controller {
    public function index() { 
        return response()->json(Buku::all(), 200); 
    }

    // Tampilkan Detail Satu Buku Berdasarkan ID
    public function show($id)
    {
    $buku = Buku::find($id);
    
    // Jika buku ditemukan, kirim datanya
    if ($buku) {
        return response()->json($buku, 200);
    }
    
    // Jika tidak ketemu, kirim pesan error 404
    return response()->json([
        'message' => 'Buku tidak ditemukan'
    ], 404);
    }

    public function store(Request $request) {
        $buku = Buku::create($request->all());
        return response()->json(['message' => 'Berhasil tambah', 'data' => $buku], 201);
    }

    public function update(Request $request, $id) {
        $buku = Buku::find($id);
        if (!$buku) return response()->json(['message' => 'Gak ada'], 404);
        $buku->update($request->all());
        return response()->json($buku, 200);
    }

    public function destroy($id) {
        $buku = Buku::find($id);
        if (!$buku) return response()->json(['message' => 'Gak ada'], 404);
        $buku->delete();
        return response()->json(['message' => 'Terhapus'], 200);
    }
}