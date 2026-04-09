<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Karyawan; // WAJIB TAMBAH INI

class PresensiMentah extends Model
{
    use HasFactory;

    protected $table = 'presensi_mentah';

    protected $fillable = [
        'karyawan_id', 
        'waktu_absensi', 
        'status_mesin'
    ];

    // 🔥 RELASI KE KARYAWAN
    public function karyawan()
    {
        return $this->belongsTo(Karyawan::class);
    }
}