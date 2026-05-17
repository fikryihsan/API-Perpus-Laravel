<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class DetailPeminjaman extends Model {
    protected $table = 'detail_peminjaman';
    // Karena tabel ini memakai gabungan FK atau ada ibfk_1 & ibfk_2, kita matikan incrementing key bawaan Laravel
    protected $primaryKey = 'id_detail'; 
    public $timestamps = false;
    protected $fillable = ['id_pinjam', 'id_buku'];
}