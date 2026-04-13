<?php

namespace App\Imports;

use App\Models\Karyawan;
use App\Models\PresensiMentah;
use Maatwebsite\Excel\Concerns\ToModel;

class PresensiImport implements ToModel
{
    public function model(array $row)
    {
        // skip header
        if ($row[0] == 'ID') return null;

        // 🔥 auto create / ambil karyawan
        $karyawan = Karyawan::firstOrCreate(
            ['id_mesin' => $row[0]],
            [
                'nama' => $row[1],
                'jabatan' => '-'
            ]
        );

        return new PresensiMentah([
            'karyawan_id' => $karyawan->id,
            'waktu_absensi' => $row[2], // sudah format benar
            'status_mesin' => $row[3],  // 🔥 FIX DI SINI
        ]);
    }
}