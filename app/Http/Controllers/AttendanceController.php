<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PresensiMentah;
use App\Models\Karyawan;

class AttendanceController extends Controller
{
    // ══════════════════════════════════════════
    // TAMPILKAN DATA KE DASHBOARD
    // ══════════════════════════════════════════
    public function index()
    {
        $data = PresensiMentah::with('karyawan')->get();
        return view('dashboard', compact('data'));
    }

    // ══════════════════════════════════════════
    // IMPORT CSV — dengan normalisasi & cek duplikat
    // Menggantikan importCsv() yang lama
    // ══════════════════════════════════════════
    public function import(Request $request)
    {
        $request->validate([
            'file_csv'   => 'required|array|min:1|max:10',
            'file_csv.*' => 'required|file|mimes:csv,txt|max:10240',
        ], [
            'file_csv.required'   => 'Pilih minimal satu file CSV.',
            'file_csv.*.mimes'    => 'Setiap file harus berformat CSV.',
        ]);

        $totalInserted  = 0;
        $totalDuplicate = 0;
        $totalSkip      = 0;
        $fileReports    = [];

        foreach ($request->file('file_csv') as $file) {
            $fileName = $file->getClientOriginalName();
            $rows     = $this->parseCsv($file->getRealPath());

            if (empty($rows)) {
                $fileReports[] = "❌ {$fileName}: File kosong atau format tidak dikenali.";
                continue;
            }

            $inserted  = 0;
            $duplicate = 0;
            $skip      = 0;

            foreach ($rows as $row) {

                // ── NORMALISASI ──
                $normalized = $this->normalizeRow($row);

                if (!$normalized) {
                    $skip++;
                    continue;
                }

                $pin    = $normalized['pin'];
                $waktu  = $normalized['waktu'];   // format: Y-m-d H:i:s
                $status = $normalized['status'];  // "Absensi Masuk" / "Absensi Pulang"

                // ── CARI KARYAWAN ──
                $karyawan = Karyawan::where('id_mesin', $pin)->first();
                if (!$karyawan) {
                    $skip++;
                    continue;
                }

                // ── CEK DUPLIKAT ──
                $exists = PresensiMentah::where('karyawan_id',   $karyawan->id)
                                        ->where('waktu_absensi', $waktu)
                                        ->where('status_mesin',  $status)
                                        ->exists();

                if ($exists) {
                    $duplicate++;
                } else {
                    PresensiMentah::create([
                        'karyawan_id'   => $karyawan->id,
                        'waktu_absensi' => $waktu,
                        'status_mesin'  => $status,
                    ]);
                    $inserted++;
                }
            }

            $totalInserted  += $inserted;
            $totalDuplicate += $duplicate;
            $totalSkip      += $skip;

            // Laporan per file
            if ($inserted === 0 && $duplicate > 0) {
                $fileReports[] = "⚠️ {$fileName}: Semua {$duplicate} data sudah ada (duplikat).";
            } elseif ($inserted > 0 && $duplicate > 0) {
                $fileReports[] = "✅ {$fileName}: {$inserted} disimpan, {$duplicate} duplikat dilewati.";
            } elseif ($inserted > 0) {
                $fileReports[] = "✅ {$fileName}: {$inserted} data baru berhasil disimpan.";
            } else {
                $fileReports[] = "❌ {$fileName}: Tidak ada data valid (PIN tidak ditemukan / format salah).";
            }
        }

        $reportStr = implode(' | ', $fileReports);

        // ── RESPON ──
        if ($totalInserted === 0 && $totalDuplicate > 0) {
            return redirect()->back()->with('error',
                "Tidak ada data baru — {$totalDuplicate} data sudah ada di database. {$reportStr}"
            );
        }

        if ($totalInserted === 0) {
            return redirect()->back()->with('error',
                "Tidak ada data yang berhasil disimpan. Periksa format CSV atau data PIN karyawan. {$reportStr}"
            );
        }

        return redirect()->back()->with('success',
            "{$totalInserted} data baru disimpan" .
            ($totalDuplicate > 0 ? ", {$totalDuplicate} duplikat dilewati" : "") .
            ($totalSkip      > 0 ? ", {$totalSkip} baris dilewati (PIN tidak dikenal)" : "") .
            ". {$reportStr}"
        );
    }


    // ══════════════════════════════════════════════════════════
    // NORMALISASI BARIS CSV
    // Format A (mentah)  : ID,Nama,Waktu Absensi,Kantor,Departemen,
    //                      Posisi,Nama perangkat,SN perangkat,Status,Keterangan,Method
    // Format B (ringkas) : ID,Nama,Waktu Absensi,Status
    // ══════════════════════════════════════════════════════════
    private function normalizeRow(array $row): ?array
    {
        // Bersihkan key dari spasi tersembunyi
        $row = array_combine(
            array_map('trim', array_keys($row)),
            array_map('trim', array_values($row))
        );

        // ── PIN / ID ──
        $pin = $row['ID'] ?? $row['id'] ?? null;
        if (!$pin || !is_numeric(trim($pin))) return null;

        // ── WAKTU ABSENSI ──
        $waktuRaw = $row['Waktu Absensi'] ?? $row['waktu_absensi'] ?? null;
        if (!$waktuRaw) return null;

        $waktu = $this->parseWaktu(trim($waktuRaw));
        if (!$waktu) return null;

        // ── STATUS ──
        $status = $row['Status'] ?? null;
        if (!$status) return null;

        $statusLower = strtolower(trim($status));
        if (str_contains($statusLower, 'masuk')) {
            $status = 'Absensi Masuk';
        } elseif (str_contains($statusLower, 'pulang')) {
            $status = 'Absensi Pulang';
        } else {
            return null;
        }

        return [
            'pin'    => (string) trim($pin),
            'waktu'  => $waktu,
            'status' => $status,
        ];
    }


    // ══════════════════════════════════════════════════════════
    // KONVERSI FORMAT TANGGAL → selalu "Y-m-d H:i:s"
    // Terima:
    //   "15/01/2026 16:14:23"  → DD/MM/YYYY (format mesin mentah)
    //   "2026-01-15 16:14:23"  → YYYY-MM-DD (format sudah bersih)
    // ══════════════════════════════════════════════════════════
    private function parseWaktu(string $raw): ?string
    {
        $raw = trim($raw);

        // DD/MM/YYYY HH:mm:ss  ← format mentah dari mesin absensi
        if (preg_match('#^(\d{2})/(\d{2})/(\d{4})\s+(\d{2}:\d{2}:\d{2})$#', $raw, $m)) {
            return "{$m[3]}-{$m[2]}-{$m[1]} {$m[4]}";
        }

        // YYYY-MM-DD HH:mm:ss  ← sudah dinormalisasi
        if (preg_match('#^(\d{4})-(\d{2})-(\d{2})\s+(\d{2}:\d{2}:\d{2})$#', $raw, $m)) {
            return "{$m[1]}-{$m[2]}-{$m[3]} {$m[4]}";
        }

        // Fallback Carbon untuk format lain
        try {
            return \Carbon\Carbon::parse($raw)->format('Y-m-d H:i:s');
        } catch (\Exception $e) {
            return null;
        }
    }


    // ══════════════════════════════════════════════════════════
    // PARSE CSV → array of associative arrays
    // ══════════════════════════════════════════════════════════
    private function parseCsv(string $filePath): array
    {
        $rows    = [];
        $headers = [];

        if (($handle = fopen($filePath, 'r')) === false) return [];

        $lineNumber = 0;
        while (($line = fgetcsv($handle, 0, ',')) !== false) {
            if (empty(array_filter($line))) continue;

            if ($lineNumber === 0) {
                $line[0] = ltrim($line[0], "\xEF\xBB\xBF"); // hapus BOM UTF-8
                $headers = array_map('trim', $line);
            } else {
                if (count($headers) === count($line)) {
                    $rows[] = array_combine($headers, $line);
                }
            }
            $lineNumber++;
        }

        fclose($handle);
        return $rows;
    }
}