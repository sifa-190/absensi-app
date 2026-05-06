<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Karyawan;

class PresensiMentah extends Model
{
    protected $table = 'presensi_mentah'; // ← pastikan ini ada

    protected $fillable = [
        'karyawan_id',
        'waktu_absensi',
        'status_mesin',
    ];

    public function karyawan()
    {
        return $this->belongsTo(Karyawan::class);
    }
}