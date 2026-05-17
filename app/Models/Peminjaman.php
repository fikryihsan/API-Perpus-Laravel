<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Peminjaman extends Model {
    protected $table = 'peminjaman';
    protected $primaryKey = 'id_pinjam';
    public $timestamps = false;
    protected $fillable = ['id_anggota', 'tgl_pinjam', 'tgl_kembali', 'status'];
}