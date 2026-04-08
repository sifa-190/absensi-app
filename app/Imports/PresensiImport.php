<?php

namespace App\Imports;

use App\Models\PresensiMentah;
use Maatwebsite\Excel\Concerns\ToModel;

class PresensiImport implements ToModel
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
public function model(array $row)
{
    return new PresensiMentah([
       'karyawan_id'   => $row[0], // Ambil dari kolom ID (0)
       'waktu_absensi' => $row[2], // Ambil dari kolom Waktu (2)
       'status_mesin'  => $row[8], // Ambil dari kolom Status (8)
    ]);
}
}
