<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Absensi Kipin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            background: linear-gradient(135deg, #1a0533 0%, #2d1b69 50%, #1a0533 100%);
            min-height: 100vh;
            font-family: 'Segoe UI', sans-serif;
        }

        .dash-wrap { padding: 2rem; max-width: 1200px; margin: 0 auto; }

        .topbar {
            background: rgba(255,255,255,0.08);
            backdrop-filter: blur(12px);
            border: 1px solid rgba(255,255,255,0.15);
            border-radius: 16px;
            padding: 1.2rem 1.5rem;
            display: flex;
            align-items: center;
            gap: 14px;
            margin-bottom: 1.5rem;
            animation: slideDown 0.5s ease;
        }
        .topbar-icon {
            width: 46px; height: 46px; border-radius: 12px;
            background: linear-gradient(135deg, #a855f7, #7c3aed);
            display: flex; align-items: center; justify-content: center;
            animation: float 3s ease-in-out infinite;
            flex-shrink: 0;
        }
        .topbar-icon svg { width: 24px; height: 24px; fill: #fff; }
        .topbar-title { font-size: 18px; font-weight: 700; color: #fff; margin: 0; }
        .topbar-sub { font-size: 12px; color: rgba(255,255,255,0.55); margin: 2px 0 0; }
        .pulse-dot {
            display: inline-block; width: 7px; height: 7px;
            border-radius: 50%; background: #a3e635;
            margin-right: 5px; animation: pulse 1.8s infinite;
        }

        .stat-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 14px;
            margin-bottom: 1.5rem;
        }
        .stat-card {
            background: rgba(255,255,255,0.07);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255,255,255,0.12);
            border-radius: 14px;
            padding: 1.1rem 1.25rem;
            animation: fadeUp 0.5s ease both;
        }
        .stat-card:nth-child(1) { animation-delay: 0.05s; }
        .stat-card:nth-child(2) { animation-delay: 0.10s; }
        .stat-card:nth-child(3) { animation-delay: 0.15s; }
        .stat-card:nth-child(4) { animation-delay: 0.20s; }
        .stat-label { font-size: 11px; color: rgba(255,255,255,0.5); margin-bottom: 6px; display: flex; align-items: center; gap: 5px; }
        .stat-val { font-size: 26px; font-weight: 700; color: #fff; }
        .stat-dot { width: 8px; height: 8px; border-radius: 50%; flex-shrink: 0; }
        .dot-purple { background: #c084fc; }
        .dot-green  { background: #a3e635; }
        .dot-amber  { background: #fbbf24; }
        .dot-red    { background: #f87171; }

        .card-custom {
            background: rgba(255,255,255,0.07);
            backdrop-filter: blur(12px);
            border: 1px solid rgba(255,255,255,0.12);
            border-radius: 16px;
            margin-bottom: 1.5rem;
            overflow: hidden;
            animation: fadeUp 0.5s ease both;
        }
        .card-header-custom {
            display: flex; align-items: center; gap: 10px;
            padding: 0.9rem 1.25rem;
            border-bottom: 1px solid rgba(255,255,255,0.08);
            font-size: 14px; font-weight: 600; color: #e9d5ff;
        }
        .header-badge {
            font-size: 10px; padding: 2px 8px; border-radius: 20px;
            background: rgba(168,85,247,0.3); color: #e9d5ff; font-weight: 600;
            border: 1px solid rgba(168,85,247,0.4);
        }
        .card-body-custom { padding: 1.25rem; }

        .upload-input {
            flex: 1; padding: 9px 13px; font-size: 13px;
            border: 1px solid rgba(255,255,255,0.2);
            border-radius: 10px;
            background: rgba(255,255,255,0.08);
            color: #fff;
        }
        .upload-input::file-selector-button {
            background: rgba(168,85,247,0.3);
            color: #e9d5ff;
            border: none;
            padding: 4px 12px;
            border-radius: 6px;
            cursor: pointer;
            font-size: 12px;
        }
        .btn-upload {
            padding: 9px 22px; font-size: 13px; font-weight: 600;
            background: linear-gradient(135deg, #a855f7, #7c3aed);
            color: #fff; border: none;
            border-radius: 10px; cursor: pointer;
            transition: opacity 0.2s, transform 0.1s;
            white-space: nowrap;
        }
        .btn-upload:hover { opacity: 0.85; }
        .btn-upload:active { transform: scale(0.97); }

        table { width: 100%; border-collapse: collapse; font-size: 13px; }
        thead tr { background: rgba(255,255,255,0.05); }
        th {
            padding: 10px 14px; text-align: left;
            font-weight: 600; font-size: 11px;
            color: rgba(255,255,255,0.45);
            border-bottom: 1px solid rgba(255,255,255,0.08);
            text-transform: uppercase; letter-spacing: 0.05em;
        }
        td {
            padding: 12px 14px; color: rgba(255,255,255,0.85);
            border-bottom: 1px solid rgba(255,255,255,0.06);
            vertical-align: middle;
        }
        tbody tr.row-anim { animation: fadeUp 0.4s ease both; }
        tbody tr:last-child td { border-bottom: none; }
        tbody tr:hover td {
            background: rgba(255,255,255,0.05);
            transition: background 0.15s;
        }

        .badge-custom {
            display: inline-flex; align-items: center; gap: 5px;
            padding: 4px 11px; border-radius: 20px;
            font-size: 11px; font-weight: 600;
        }
        .badge-masuk {
            background: rgba(163,230,53,0.15);
            color: #a3e635;
            border: 1px solid rgba(163,230,53,0.3);
        }
        .badge-pulang {
            background: rgba(248,113,113,0.15);
            color: #f87171;
            border: 1px solid rgba(248,113,113,0.3);
        }
        .badge-terlambat {
            background: rgba(251,191,36,0.15);
            color: #fbbf24;
            border: 1px solid rgba(251,191,36,0.3);
        }
        .badge-cepat {
            background: rgba(244,114,182,0.15);
            color: #f472b6;
            border: 1px solid rgba(244,114,182,0.3);
        }
        .badge-tepat {
            background: rgba(96,165,250,0.15);
            color: #60a5fa;
            border: 1px solid rgba(96,165,250,0.3);
        }

        .pin-chip {
            font-family: monospace; font-size: 12px;
            background: rgba(255,255,255,0.1);
            color: #c084fc;
            padding: 3px 8px; border-radius: 6px;
        }
        .avatar {
            width: 32px; height: 32px; border-radius: 50%;
            background: linear-gradient(135deg, #a855f7, #7c3aed);
            color: #fff;
            display: inline-flex; align-items: center; justify-content: center;
            font-size: 11px; font-weight: 700;
            margin-right: 8px; flex-shrink: 0;
        }
        .nama-cell { display: flex; align-items: center; }

        .alert-custom-success {
            background: rgba(163,230,53,0.1);
            border: 1px solid rgba(163,230,53,0.3);
            color: #a3e635;
            border-radius: 10px; padding: 10px 14px;
            font-size: 13px; margin-bottom: 1rem;
            animation: fadeUp 0.4s ease;
        }
        .alert-custom-danger {
            background: rgba(248,113,113,0.1);
            border: 1px solid rgba(248,113,113,0.3);
            color: #f87171;
            border-radius: 10px; padding: 10px 14px;
            font-size: 13px; margin-bottom: 1rem;
            animation: fadeUp 0.4s ease;
        }

        .footer-copy {
            text-align: center;
            padding: 1.5rem 0 1rem;
            font-size: 12px;
            color: rgba(255,255,255,0.3);
            animation: fadeUp 0.5s 0.5s ease both;
        }

        @keyframes slideDown {
            from { opacity: 0; transform: translateY(-16px); }
            to   { opacity: 1; transform: translateY(0); }
        }
        @keyframes fadeUp {
            from { opacity: 0; transform: translateY(12px); }
            to   { opacity: 1; transform: translateY(0); }
        }
        @keyframes pulse {
            0%, 100% { opacity: 1; transform: scale(1); }
            50%       { opacity: 0.3; transform: scale(0.6); }
        }
        @keyframes float {
            0%, 100% { transform: translateY(0); }
            50%       { transform: translateY(-5px); }
        }

        @media (max-width: 768px) {
            .stat-grid { grid-template-columns: repeat(2, 1fr); }
            .dash-wrap { padding: 1rem; }
        }
    </style>
</head>
<body>
<div class="dash-wrap">

    {{-- TOPBAR --}}
    <div class="topbar">
        <div class="topbar-icon">
            <svg viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-1 14H9V8h2v8zm4 0h-2V8h2v8z"/></svg>
        </div>
        <div>
            <p class="topbar-title">Dashboard Monitoring Absensi</p>
            <p class="topbar-sub"><span class="pulse-dot"></span>Kipin &mdash; Data real-time</p>
        </div>
    </div>

    {{-- STAT CARDS --}}
    <div class="stat-grid">
        <div class="stat-card">
            <div class="stat-label"><span class="stat-dot dot-purple"></span>Total Data</div>
            <div class="stat-val">{{ $data->count() }}</div>
        </div>
        <div class="stat-card">
            <div class="stat-label"><span class="stat-dot dot-green"></span>Tepat Waktu</div>
            <div class="stat-val">
                {{ $data->filter(function($d){
                    $w = \Carbon\Carbon::parse($d->waktu_absensi);
                    $isMasuk = str_contains(strtolower($d->status_mesin), 'masuk');
                    return $isMasuk && $w->format('H:i:s') <= '08:30:00';
                })->count() }}
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-label"><span class="stat-dot dot-amber"></span>Terlambat</div>
            <div class="stat-val">
                {{ $data->filter(function($d){
                    $w = \Carbon\Carbon::parse($d->waktu_absensi);
                    $isMasuk = str_contains(strtolower($d->status_mesin), 'masuk');
                    return $isMasuk && $w->format('H:i:s') > '08:30:00';
                })->count() }}
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-label"><span class="stat-dot dot-red"></span>Pulang Cepat</div>
            <div class="stat-val">
                {{ $data->filter(function($d){
                    $w = \Carbon\Carbon::parse($d->waktu_absensi);
                    $isMasuk = str_contains(strtolower($d->status_mesin), 'masuk');
                    return !$isMasuk && $w->format('H:i:s') < '15:30:00';
                })->count() }}
            </div>
        </div>
    </div>

    {{-- NOTIFIKASI --}}
    @if(session('success'))
    <div class="alert-custom-success">{{ session('success') }}</div>
    @endif

    @if(session('error'))
    <div class="alert-custom-danger">{{ session('error') }}</div>
    @endif

    {{-- FORM UPLOAD --}}
    <div class="card-custom">
        <div class="card-header-custom">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#c084fc" stroke-width="2">
                <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
                <polyline points="17 8 12 3 7 8"/>
                <line x1="12" y1="3" x2="12" y2="15"/>
            </svg>
            Upload Data Revo
            <span class="header-badge">CSV</span>
        </div>
        <div class="card-body-custom">
            <form action="{{ route('import.presensi') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="d-flex gap-2">
                    <input type="file" class="upload-input" name="file_csv" required>
                    <button class="btn-upload" type="submit">Upload &amp; Proses</button>
                </div>
            </form>
        </div>
    </div>

    {{-- TABEL --}}
    <div class="card-custom">
        <div class="card-header-custom">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#c084fc" stroke-width="2">
                <rect x="3" y="3" width="18" height="18" rx="2"/>
                <path d="M3 9h18M9 21V9"/>
            </svg>
            Data Presensi Mentah
        </div>
        <div style="overflow-x:auto;">
            <table>
                <thead>
                    <tr>
                        <th>PIN</th>
                        <th>Nama Karyawan</th>
                        <th>Waktu Absensi</th>
                        <th>Status</th>
                        <th>Keterangan</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($data as $i => $d)
                    @php
                        $waktu       = \Carbon\Carbon::parse($d->waktu_absensi);
                        $jamMasuk    = \Carbon\Carbon::createFromTime(8, 30);
                        $jamPulang   = \Carbon\Carbon::createFromTime(15, 30);
                        $isMasuk     = str_contains(strtolower($d->status_mesin), 'masuk');
                        $terlambat   = $isMasuk  && $waktu->format('H:i:s') > $jamMasuk->format('H:i:s');
                        $pulangCepat = !$isMasuk && $waktu->format('H:i:s') < $jamPulang->format('H:i:s');
                    @endphp
                    <tr class="row-anim" data-delay="{{ $i }}">
                        <td><span class="pin-chip">{{ $d->karyawan->id_mesin }}</span></td>
                        <td>
                            <div class="nama-cell">
                                <div class="avatar">{{ strtoupper(substr($d->karyawan->nama, 0, 2)) }}</div>
                                {{ $d->karyawan->nama }}
                            </div>
                        </td>
                        <td style="font-family:monospace; font-size:12px; color:rgba(255,255,255,0.5);">
                            {{ $d->waktu_absensi }}
                        </td>
                        <td>
                            @if($isMasuk)
                                <span class="badge-custom badge-masuk">Absensi Masuk</span>
                            @else
                                <span class="badge-custom badge-pulang">Absensi Pulang</span>
                            @endif
                        </td>
                        <td>
                            @if($terlambat)
                                <span class="badge-custom badge-terlambat">Terlambat</span>
                            @elseif($pulangCepat)
                                <span class="badge-custom badge-cepat">Pulang Cepat</span>
                            @else
                                <span class="badge-custom badge-tepat">Tepat Waktu</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" style="text-align:center; color:rgba(255,255,255,0.3); padding:3rem;">
                            Belum ada data tersedia
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- COPYRIGHT --}}
    <div class="footer-copy">
        &copy; {{ date('Y') }} Kipin &mdash; Sistem Monitoring Absensi. Sakera.
    </div>

</div>

<script>
    document.querySelectorAll('tr.row-anim').forEach(function(tr) {
        var delay = tr.getAttribute('data-delay');
        tr.style.animationDelay = (delay * 0.04) + 's';
    });
</script>

</body>
</html>