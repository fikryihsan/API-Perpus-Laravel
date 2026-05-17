<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Buku extends Model {
    protected $table = 'buku';
    protected $primaryKey = 'id_buku';
    public $timestamps = false;
    protected $fillable = ['judul', 'penulis', 'stok'];
}
