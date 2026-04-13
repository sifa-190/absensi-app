<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Imports\PresensiImport;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\PresensiMentah; // TAMBAHAN

class AttendanceController extends Controller
{
    // 🔥 TAMPILKAN DATA KE DASHBOARD
        public function index()
        {
            $data = PresensiMentah::all(); // 🔥 ambil dari presensi_mentah
            return view('dashboard', compact('data'));
        }

    // 🔥 IMPORT CSV
    public function importCsv(Request $request) 
    {
        $request->validate([
            'file_csv' => 'required|mimes:csv,txt',
        ]);

        if ($request->hasFile('file_csv')) {
            Excel::import(new PresensiImport, $request->file('file_csv'));

            // 🔥 balik ke dashboard + refresh data
            return redirect('/')->with('success', 'Data berhasil diimpor!');
        }

        return back()->with('error', 'Gagal mengunggah file.');
    }
}