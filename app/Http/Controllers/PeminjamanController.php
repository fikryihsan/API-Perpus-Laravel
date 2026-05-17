<?php
namespace App\Http\Controllers;

use App\Models\Peminjaman;
use App\Models\DetailPeminjaman;
use Illuminate\Http\Request;

class PeminjamanController extends Controller
{
    // 1. select all (banyak) -> Tampilkan semua data peminjaman utama
    public function index()
    {
        return response()->json(Peminjaman::all(), 200);
    }

    // 2. select 1 berd id transaksi -> Tampilkan 1 peminjaman utama berdasarkan ID
    public function show($id)
    {
        $pinjam = Peminjaman::find($id);
        if (!$pinjam) return response()->json(['message' => 'Transaksi tidak ditemukan'], 404);
        return response()->json($pinjam, 200);
    }

    // 3. insert transaksi -> Tambah data ke tabel peminjaman utama
    public function store(Request $request)
    {
        $pinjam = Peminjaman::create($request->all());
        return response()->json(['message' => 'Transaksi utama berhasil ditambahkan', 'data' => $pinjam], 201);
    }

    // 4. Fungsi untuk Update Status (Misal mengubah 'dipinjam' menjadi 'kembali')
public function update(Request $request, $id)
{
    $pinjam = Peminjaman::find($id);
    
    if (!$pinjam) {
        return response()->json(['message' => 'Data peminjaman tidak ditemukan'], 404);
    }

    // Mengupdate kolom status (dan tgl_kembali jika disertakan di body Postman)
    $pinjam->update($request->all());

    return response()->json([
        'message' => 'Status peminjaman berhasil diperbarui!',
        'data' => $pinjam
    ], 200);
}

    // 5. delete transaksi -> Hapus data peminjaman utama
    public function destroy($id)
    {
        $pinjam = Peminjaman::find($id);
        if (!$pinjam) return response()->json(['message' => 'Transaksi tidak ditemukan'], 404);
        $pinjam->delete();
        return response()->json(['message' => 'Transaksi utama berhasil dihapus'], 200);
    }

    // 6. Tampilkan semua isi tabel detail peminjaman
public function indexDetail()
{
    return response()->json(DetailPeminjaman::all(), 200);
}

    // 7. insert transaksi_detail berd id transaksi -> Tambah buku yang dipinjam ke tabel detail
    public function storeDetail(Request $request, $id_transaksi)
    {
        // Pastikan id_pinjam di body disamakan dengan ID transaksi di URL
        $data = $request->all();
        $data['id_pinjam'] = $id_transaksi;

        $detail = DetailPeminjaman::create($data);
        return response()->json(['message' => 'Detail transaksi berhasil ditambahkan', 'data' => $detail], 201);
    }

    // 8. delete transaksi_detail berd id transaksi & id transki detail (id_detail)
    public function destroyDetail($id_detail)
    {
    // Menggunakan query langsung berdasarkan primary key tabel detail_peminjaman yaitu id_detail
    $detail = DetailPeminjaman::where('id_detail', $id_detail)->first();

    if (!$detail) {
        return response()->json(['message' => 'Detail transaksi tidak ditemukan'], 404);
    }
    
    $detail->delete();
    return response()->json(['message' => 'Detail transaksi berhasil dihapus melalui ID Detail'], 200);
    }

    // 1. Fungsi Statistik tambahan kita kemarin (tetap dipertahankan)
    public function statistik()
    {
        return response()->json([
            'total_transaksi' => Peminjaman::count(),
            'sedang_dipinjam' => Peminjaman::where('status', 'dipinjam')->count(),
            'sudah_kembali' => Peminjaman::where('status', 'kembali')->count()
        ]);
    }

    // 2. Fungsi Statistik: Melihat siapa peminjam terbanyak dalam sebulan terakhir
public function peminjamTerbanyak()
{
    // Kita gabungkan tabel peminjaman dan detail_peminjaman untuk menghitung total buku asli yang dipinjam
    $topPeminjam = \DB::table('peminjaman')
        ->join('detail_peminjaman', 'peminjaman.id_pinjam', '=', 'detail_peminjaman.id_pinjam')
        ->select('peminjaman.id_anggota', \DB::raw('count(detail_peminjaman.id_buku) as total_buku_dipinjam'))
        ->where('peminjaman.tgl_pinjam', '>=', now()->subMonth())
        ->groupBy('peminjaman.id_anggota')
        ->orderBy('total_buku_dipinjam', 'desc')
        ->first(); // Ambil 1 anggota yang jumlah bukunya paling banyak

    if (!$topPeminjam) {
        return response()->json(['message' => 'Belum ada data transaksi detail dalam sebulan terakhir.'], 200);
    }

    return response()->json([
        'message' => 'Peminjam buku terbanyak bulan ini (Dihitung berdasarkan jumlah fisik buku)',
        'data' => [
            'id_anggota' => $topPeminjam->id_anggota,
            'total_buku_sebulan' => $topPeminjam->total_buku_dipinjam
        ]
    ], 200);
}

// 3. Fungsi Statistik: Melihat buku apa yang paling banyak dipinjam dalam sebulan terakhir
public function bukuTerbanyak()
{
    // Menggabungkan detail_peminjaman dengan peminjaman (untuk filter tanggal) dan buku (untuk ambil judul)
    $topBuku = \DB::table('detail_peminjaman')
        ->join('peminjaman', 'detail_peminjaman.id_pinjam', '=', 'peminjaman.id_pinjam')
        ->join('buku', 'detail_peminjaman.id_buku', '=', 'buku.id_buku')
        ->select('buku.id_buku', 'buku.judul', \DB::raw('count(detail_peminjaman.id_buku) as total_dipinjam'))
        ->where('peminjaman.tgl_pinjam', '>=', now()->subMonth())
        ->groupBy('buku.id_buku', 'buku.judul')
        ->orderBy('total_dipinjam', 'desc')
        ->first(); // Mengambil 1 buku yang paling tinggi angka pinjamnya

    if (!$topBuku) {
        return response()->json(['message' => 'Belum ada data peminjaman buku dalam sebulan terakhir.'], 200);
    }

    return response()->json([
        'message' => 'Buku yang paling banyak dipinjam bulan ini',
        'data' => [
            'id_buku' => $topBuku->id_buku,
            'judul_buku' => $topBuku->judul,
            'total_kali_dipinjam' => $topBuku->total_dipinjam
        ]
    ], 200);
}
}