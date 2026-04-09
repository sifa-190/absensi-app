<?php

namespace App\Imports;

use App\Models\Karyawan;
use App\Models\PresensiMentah;
use Maatwebsite\Excel\Concerns\ToModel;
use Carbon\Carbon; 

class PresensiImport implements ToModel
{
        public function model(array $row)
        {
            // skip header
            if ($row[0] == 'ID') return null;

            $karyawan = \App\Models\Karyawan::firstOrCreate(
                ['id_mesin' => $row[0]], 
                [
                    'nama' => $row[1],
                    'jabatan' => '-' // default
                ]
            );

            return new \App\Models\PresensiMentah([
                'karyawan_id' => $karyawan->id, 
                'waktu_absensi' => date('Y-m-d H:i:s', strtotime(str_replace('/', '-', $row[2]))),
                'status_mesin' => $row[8],
            ]);
        }
}