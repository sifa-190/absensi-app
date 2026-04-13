<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Absensi Kipin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            background: #f0f4ff;
            min-height: 100vh;
            font-family: 'Segoe UI', sans-serif;
        }

        /* ─── LOADING OVERLAY ─── */
        .loading-overlay {
            display: none;
            position: fixed; inset: 0;
            background: rgba(255,255,255,0.9);
            z-index: 9999;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 18px;
        }
        .loading-overlay.show { display: flex; }
        .spinner {
            width: 60px; height: 60px;
            border: 5px solid #e0e7ff;
            border-top-color: #6366f1;
            border-radius: 50%;
            animation: spin .75s linear infinite;
        }
        .loading-card {
            background: #fff;
            border: 1px solid #e0e7ff;
            border-radius: 16px;
            padding: 2rem 2.5rem;
            text-align: center;
            box-shadow: 0 8px 32px rgba(99,102,241,0.12);
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 12px;
        }
        .loading-title { font-size: 15px; font-weight: 700; color: #6366f1; }
        .loading-sub   { font-size: 12px; color: #6b7280; }
        .loading-bar-wrap {
            width: 200px; height: 4px;
            background: #e0e7ff;
            border-radius: 99px;
            overflow: hidden;
        }
        .loading-bar {
            height: 100%;
            background: linear-gradient(90deg, #6366f1, #818cf8);
            border-radius: 99px;
            animation: barSlide 1.2s ease-in-out infinite;
        }

        /* ─── WRAP ─── */
        .dash-wrap { padding: 1.5rem; max-width: 1200px; margin: 0 auto; }

        /* ─── TOPBAR ─── */
        .topbar {
            background: linear-gradient(135deg, #6366f1 0%, #818cf8 100%);
            border-radius: 16px;
            padding: 1.2rem 1.5rem;
            display: flex;
            align-items: center;
            gap: 14px;
            margin-bottom: 1.5rem;
            box-shadow: 0 4px 24px rgba(99,102,241,0.22);
            animation: slideDown .5s ease;
        }
        .topbar-icon {
            width: 46px; height: 46px; border-radius: 12px;
            background: rgba(255,255,255,0.2);
            display: flex; align-items: center; justify-content: center;
            animation: float 3s ease-in-out infinite;
            flex-shrink: 0;
        }
        .topbar-icon svg { width: 24px; height: 24px; fill: #fff; }
        .topbar-title { font-size: 18px; font-weight: 700; color: #fff; margin: 0; }
        .topbar-sub   { font-size: 12px; color: rgba(255,255,255,0.75); margin: 2px 0 0; }
        .pulse-dot {
            display: inline-block; width: 7px; height: 7px;
            border-radius: 50%; background: #a3e635;
            margin-right: 5px; animation: pulse 1.8s infinite;
        }
        .topbar-clock { font-size: 20px; font-weight: 700; color: #fff; line-height: 1; }
        .topbar-date  { font-size: 11px; color: rgba(255,255,255,0.8); margin-top: 3px; }

        /* ─── STAT GRID ─── */
        .stat-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 14px;
            margin-bottom: 1.5rem;
        }
        .stat-card {
            background: #fff;
            border: 1px solid #e0e7ff;
            border-radius: 14px;
            padding: 1.1rem 1.25rem;
            box-shadow: 0 2px 8px rgba(99,102,241,0.07);
            animation: fadeUp .5s ease both;
        }
        .stat-card:nth-child(1) { animation-delay: .05s; }
        .stat-card:nth-child(2) { animation-delay: .10s; }
        .stat-card:nth-child(3) { animation-delay: .15s; }
        .stat-card:nth-child(4) { animation-delay: .20s; }
        .stat-label {
            font-size: 11px; color: #6b7280; margin-bottom: 6px;
            display: flex; align-items: center; gap: 5px;
            font-weight: 600; text-transform: uppercase; letter-spacing: .05em;
        }
        .stat-val  { font-size: 28px; font-weight: 700; }
        .v-total   { color: #6366f1; }
        .v-tepat   { color: #16a34a; }
        .v-lambat  { color: #b45309; }
        .v-cepat   { color: #dc2626; }
        .stat-dot  { width: 8px; height: 8px; border-radius: 50%; flex-shrink: 0; }
        .dot-purple { background: #6366f1; }
        .dot-green  { background: #16a34a; }
        .dot-amber  { background: #b45309; }
        .dot-red    { background: #dc2626; }

        /* ─── CARD ─── */
        .card-custom {
            background: #fff;
            border: 1px solid #e0e7ff;
            border-radius: 16px;
            margin-bottom: 1.5rem;
            overflow: hidden;
            box-shadow: 0 2px 12px rgba(99,102,241,0.07);
            animation: fadeUp .5s ease both;
        }
        .card-header-custom {
            display: flex; align-items: center; gap: 10px;
            padding: .9rem 1.25rem;
            border-bottom: 1px solid #e0e7ff;
            font-size: 14px; font-weight: 700; color: #1e1b4b;
        }
        .header-badge {
            font-size: 10px; padding: 2px 9px; border-radius: 20px;
            background: #ede9fe; color: #7c3aed; font-weight: 700;
            border: 1px solid #c4b5fd;
        }
        .card-body-custom { padding: 1.25rem; }

        /* ─── FILTER ─── */
        .filter-row {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr auto;
            gap: 12px;
            align-items: end;
        }
        .filter-group { display: flex; flex-direction: column; gap: 4px; }
        .filter-label {
            font-size: 11px; font-weight: 700; color: #6b7280;
            text-transform: uppercase; letter-spacing: .05em;
        }
        .filter-input {
            padding: 8px 12px; font-size: 13px;
            border: 1.5px solid #e0e7ff;
            border-radius: 9px;
            background: #f8f9ff;
            color: #1e1b4b;
            outline: none;
            transition: border-color .2s, box-shadow .2s;
        }
        .filter-input:focus {
            border-color: #6366f1;
            box-shadow: 0 0 0 3px rgba(99,102,241,0.1);
        }
        .btn-reset {
            padding: 8px 16px; font-size: 12px; font-weight: 600;
            background: #f1f5f9; color: #6b7280;
            border: 1.5px solid #e0e7ff;
            border-radius: 9px; cursor: pointer;
            transition: all .2s; white-space: nowrap;
        }
        .btn-reset:hover { background: #ede9fe; color: #6366f1; border-color: #a5b4fc; }

        /* ─── UPLOAD ─── */
        .upload-input {
            flex: 1; padding: 9px 13px; font-size: 13px;
            border: 1.5px dashed #a5b4fc;
            border-radius: 10px;
            background: #f5f3ff;
            color: #1e1b4b;
            transition: border-color .2s;
        }
        .upload-input:hover { border-color: #6366f1; }
        .upload-input::file-selector-button {
            background: #ede9fe; color: #7c3aed;
            border: none; padding: 4px 12px;
            border-radius: 6px; cursor: pointer;
            font-size: 12px; font-weight: 600; margin-right: 8px;
        }
        .btn-upload {
            padding: 9px 22px; font-size: 13px; font-weight: 700;
            background: linear-gradient(135deg, #6366f1, #818cf8);
            color: #fff; border: none;
            border-radius: 10px; cursor: pointer;
            transition: opacity .2s, transform .1s;
            white-space: nowrap;
            box-shadow: 0 2px 8px rgba(99,102,241,0.25);
            display: flex; align-items: center; gap: 8px;
        }
        .btn-upload:hover   { opacity: .88; }
        .btn-upload:active  { transform: scale(.97); }

        /* ─── RESULT INFO ─── */
        .result-info {
            font-size: 12px; color: #6b7280;
            padding: 6px 1.25rem;
            background: #f8f9ff;
            border-bottom: 1px solid #e0e7ff;
        }
        .result-info strong { color: #6366f1; }

        /* ─── TABLE ─── */
        table { width: 100%; border-collapse: collapse; font-size: 13px; }
        thead tr { background: #f5f3ff; }
        th {
            padding: 10px 14px; text-align: left;
            font-weight: 700; font-size: 11px; color: #6b7280;
            border-bottom: 1.5px solid #e0e7ff;
            text-transform: uppercase; letter-spacing: .05em;
        }
        td {
            padding: 11px 14px; color: #1e1b4b;
            border-bottom: 1px solid #f1f5f9;
            vertical-align: middle;
        }
        tbody tr { animation: fadeUp .35s ease both; }
        tbody tr:last-child td { border-bottom: none; }
        tbody tr:hover td { background: #f5f3ff; transition: background .15s; }

        /* ─── BADGES ─── */
        .badge-custom {
            display: inline-flex; align-items: center; gap: 5px;
            padding: 3px 10px; border-radius: 20px;
            font-size: 11px; font-weight: 700;
        }
        .badge-masuk     { background: #dcfce7; color: #16a34a; border: 1px solid #bbf7d0; }
        .badge-pulang    { background: #fee2e2; color: #dc2626; border: 1px solid #fecaca; }
        .badge-terlambat { background: #fef9c3; color: #b45309; border: 1px solid #fde68a; }
        .badge-cepat     { background: #fee2e2; color: #dc2626; border: 1px solid #fecaca; }
        .badge-tepat     { background: #dbeafe; color: #1d4ed8; border: 1px solid #bfdbfe; }

        /* ─── MISC ─── */
        .pin-chip {
            font-family: monospace; font-size: 12px;
            background: #ede9fe; color: #6366f1;
            padding: 3px 8px; border-radius: 6px; font-weight: 700;
        }
        .avatar {
            width: 32px; height: 32px; border-radius: 50%;
            background: linear-gradient(135deg, #6366f1, #818cf8);
            color: #fff;
            display: inline-flex; align-items: center; justify-content: center;
            font-size: 11px; font-weight: 700;
            margin-right: 8px; flex-shrink: 0;
        }
        .nama-cell { display: flex; align-items: center; }

        /* ─── ALERTS ─── */
        .alert-custom-success {
            background: #dcfce7; border: 1px solid #bbf7d0; color: #16a34a;
            border-radius: 10px; padding: 10px 14px; font-size: 13px;
            margin-bottom: 1rem; animation: fadeUp .4s ease;
        }
        .alert-custom-danger {
            background: #fee2e2; border: 1px solid #fecaca; color: #dc2626;
            border-radius: 10px; padding: 10px 14px; font-size: 13px;
            margin-bottom: 1rem; animation: fadeUp .4s ease;
        }

        /* ─── EMPTY STATE ─── */
        .empty-state { text-align: center; padding: 3rem; color: #9ca3af; }
        .empty-icon  { font-size: 40px; margin-bottom: 12px; opacity: .5; }
        .empty-text  { font-size: 14px; font-weight: 600; }
        .empty-sub   { font-size: 12px; margin-top: 4px; }

        /* ─── FOOTER ─── */
        .footer-copy {
            text-align: center; padding: 1.5rem 0 1rem;
            font-size: 12px; color: #9ca3af;
            animation: fadeUp .5s .5s ease both;
        }

        /* ─── KEYFRAMES ─── */
        @keyframes slideDown {
            from { opacity: 0; transform: translateY(-16px); }
            to   { opacity: 1; transform: translateY(0); }
        }
        @keyframes fadeUp {
            from { opacity: 0; transform: translateY(10px); }
            to   { opacity: 1; transform: translateY(0); }
        }
        @keyframes pulse {
            0%, 100% { opacity: 1; transform: scale(1); }
            50%       { opacity: .3; transform: scale(.6); }
        }
        @keyframes float {
            0%, 100% { transform: translateY(0); }
            50%       { transform: translateY(-5px); }
        }
        @keyframes spin { to { transform: rotate(360deg); } }
        @keyframes barSlide {
            0%   { transform: translateX(-100%); }
            50%  { transform: translateX(0%); }
            100% { transform: translateX(100%); }
        }

        /* ─── RESPONSIVE ─── */
        @media (max-width: 768px) {
            .stat-grid  { grid-template-columns: repeat(2, 1fr); }
            .filter-row { grid-template-columns: 1fr; }
            .dash-wrap  { padding: 1rem; }
        }
    </style>
</head>
<body>

{{-- ══ LOADING OVERLAY ══ --}}
<div class="loading-overlay" id="loadingOverlay">
    <div class="loading-card">
        <div class="spinner"></div>
        <div class="loading-title">Memproses Data Absensi...</div>
        <div class="loading-bar-wrap">
            <div class="loading-bar"></div>
        </div>
        <div class="loading-sub">Mohon tunggu, jangan tutup halaman ini</div>
    </div>
</div>

<div class="dash-wrap">

    {{-- ══ TOPBAR ══ --}}
    <div class="topbar">
        <div class="topbar-icon">
            <svg viewBox="0 0 24 24">
                <path d="M17 12h-5v5h5v-5zM16 1v2H8V1H6v2H5c-1.11 0-1.99.9-1.99 2L3 19c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2h-1V1h-2zm3 18H5V8h14v11z"/>
            </svg>
        </div>
        <div style="flex:1">
            <p class="topbar-title">Dashboard Monitoring Absensi</p>
            <p class="topbar-sub"><span class="pulse-dot"></span>Kipin &mdash; Data real-time</p>
        </div>
        <div style="text-align:right">
            <div class="topbar-clock" id="liveClock"></div>
            <div class="topbar-date"  id="liveDate"></div>
        </div>
    </div>

    {{-- ══ STAT CARDS ══ --}}
    <div class="stat-grid">
        <div class="stat-card">
            <div class="stat-label"><span class="stat-dot dot-purple"></span>Total Data</div>
            <div class="stat-val v-total">{{ $data->count() }}</div>
        </div>
        <div class="stat-card">
            <div class="stat-label"><span class="stat-dot dot-green"></span>Tepat Waktu</div>
            <div class="stat-val v-tepat">
                {{ $data->filter(function($d){
                    $w = \Carbon\Carbon::parse($d->waktu_absensi);
                    $isMasuk = str_contains(strtolower($d->status_mesin), 'masuk');
                    return $isMasuk && $w->format('H:i:s') <= '08:30:00';
                })->count() }}
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-label"><span class="stat-dot dot-amber"></span>Terlambat</div>
            <div class="stat-val v-lambat">
                {{ $data->filter(function($d){
                    $w = \Carbon\Carbon::parse($d->waktu_absensi);
                    $isMasuk = str_contains(strtolower($d->status_mesin), 'masuk');
                    return $isMasuk && $w->format('H:i:s') > '08:30:00';
                })->count() }}
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-label"><span class="stat-dot dot-red"></span>Pulang Cepat</div>
            <div class="stat-val v-cepat">
                {{ $data->filter(function($d){
                    $w = \Carbon\Carbon::parse($d->waktu_absensi);
                    $isMasuk = str_contains(strtolower($d->status_mesin), 'masuk');
                    return !$isMasuk && $w->format('H:i:s') < '15:30:00';
                })->count() }}
            </div>
        </div>
    </div>

    {{-- ══ NOTIFIKASI ══ --}}
    @if(session('success'))
        <div class="alert-custom-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert-custom-danger">{{ session('error') }}</div>
    @endif

    {{-- ══ FORM UPLOAD ══ --}}
    <div class="card-custom">
        <div class="card-header-custom">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#6366f1" stroke-width="2">
                <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
                <polyline points="17 8 12 3 7 8"/>
                <line x1="12" y1="3" x2="12" y2="15"/>
            </svg>
            Upload Data Revo
            <span class="header-badge">CSV</span>
        </div>
        <div class="card-body-custom">
            <form action="{{ route('import.presensi') }}" method="POST"
                  enctype="multipart/form-data"
                  onsubmit="showLoading()">
                @csrf
                <div class="d-flex gap-2 align-items-center flex-wrap">
                    <input type="file" class="upload-input" name="file_csv"
                           id="csvFileInput" required
                           onchange="showFileName(this)">
                    <button class="btn-upload" type="submit">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none"
                             stroke="currentColor" stroke-width="2.5">
                            <polyline points="17 8 12 3 7 8"/>
                            <line x1="12" y1="3" x2="12" y2="15"/>
                            <path d="M5 19h14"/>
                        </svg>
                        Upload &amp; Proses
                    </button>
                </div>
                <div id="fileInfo" style="margin-top:8px;font-size:12px;color:#6b7280;display:none"></div>
            </form>
        </div>
    </div>

    {{-- ══ FILTER & PENCARIAN ══ --}}
    <div class="card-custom">
        <div class="card-header-custom">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#6366f1" stroke-width="2">
                <circle cx="11" cy="11" r="8"/>
                <line x1="21" y1="21" x2="16.65" y2="16.65"/>
            </svg>
            Filter &amp; Pencarian
        </div>
        <div class="card-body-custom">
            <div class="filter-row">
                <div class="filter-group">
                    <label class="filter-label">Cari Nama Karyawan</label>
                    <input type="text" class="filter-input" id="searchName"
                           placeholder="Ketik nama karyawan..."
                           oninput="filterTable()">
                </div>
                <div class="filter-group">
                    <label class="filter-label">Filter Tanggal</label>
                    <input type="date" class="filter-input" id="filterDate"
                           onchange="filterTable()">
                </div>
                <div class="filter-group">
                    <label class="filter-label">Filter Keterangan</label>
                    <select class="filter-input" id="filterKet" onchange="filterTable()">
                        <option value="">Semua</option>
                        <option value="tepat">Tepat Waktu</option>
                        <option value="terlambat">Terlambat</option>
                        <option value="cepat">Pulang Cepat</option>
                    </select>
                </div>
                <div>
                    <button class="btn-reset" onclick="resetFilter()">&#8635; Reset</button>
                </div>
            </div>
        </div>
    </div>

    {{-- ══ TABEL ══ --}}
    <div class="card-custom">
        <div class="card-header-custom">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#6366f1" stroke-width="2">
                <rect x="3" y="3" width="18" height="18" rx="2"/>
                <path d="M3 9h18M9 21V9"/>
            </svg>
            Data Presensi
            <span class="header-badge" id="countBadge">{{ $data->count() }} data</span>
        </div>

        <div class="result-info">
            Menampilkan <strong id="shownCount">{{ $data->count() }}</strong>
            dari <strong>{{ $data->count() }}</strong> data
        </div>

        <div style="overflow-x:auto">
            <table>
                <thead>
                    <tr>
                        <th>No</th>
                        <th>PIN</th>
                        <th>Nama Karyawan</th>
                        <th>Tanggal</th>
                        <th>Waktu</th>
                        <th>Status</th>
                        <th>Keterangan</th>
                    </tr>
                </thead>
                <tbody id="tableBody">
                    @forelse($data as $i => $d)
                    @php
                        $waktu       = \Carbon\Carbon::parse($d->waktu_absensi);
                        $jamMasuk    = \Carbon\Carbon::createFromTime(8, 30);
                        $jamPulang   = \Carbon\Carbon::createFromTime(15, 30);
                        $isMasuk     = str_contains(strtolower($d->status_mesin), 'masuk');
                        $terlambat   = $isMasuk  && $waktu->format('H:i:s') > $jamMasuk->format('H:i:s');
                        $pulangCepat = !$isMasuk && $waktu->format('H:i:s') < $jamPulang->format('H:i:s');
                        $ketClass    = $terlambat ? 'terlambat' : ($pulangCepat ? 'cepat' : 'tepat');
                    @endphp
                    <tr class="row-anim tbl-row"
                        data-delay="{{ $i }}"
                        data-nama="{{ strtolower($d->karyawan->nama) }}"
                        data-tanggal="{{ $waktu->format('Y-m-d') }}"
                        data-ket="{{ $ketClass }}">

                        <td style="color:#9ca3af;font-size:12px">{{ $i + 1 }}</td>

                        <td><span class="pin-chip">{{ $d->karyawan->id_mesin }}</span></td>

                        <td>
                            <div class="nama-cell">
                                <div class="avatar">
                                    {{ strtoupper(substr($d->karyawan->nama, 0, 2)) }}
                                </div>
                                {{ $d->karyawan->nama }}
                            </div>
                        </td>

                        <td style="font-size:12px;color:#6b7280">
                            {{ $waktu->translatedFormat('d M Y') }}
                        </td>

                        <td style="font-family:monospace;font-size:12px;color:#6b7280">
                            {{ $waktu->format('H:i:s') }}
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
                        <td colspan="7">
                            <div class="empty-state">
                                <div class="empty-icon">📋</div>
                                <div class="empty-text">Belum ada data tersedia</div>
                                <div class="empty-sub">Upload file CSV untuk memulai</div>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- ══ FOOTER ══ --}}
    <div class="footer-copy">
        &copy; {{ date('Y') }} Kipin &mdash; Sistem Monitoring Absensi. Sakera.
    </div>

</div>

<script>
    /* ── Animasi delay tiap baris ── */
    document.querySelectorAll('tr.row-anim').forEach(function(tr) {
        tr.style.animationDelay = (tr.getAttribute('data-delay') * 0.04) + 's';
    });

    /* ── Jam & tanggal live ── */
    function updateClock() {
        var now = new Date();
        document.getElementById('liveClock').textContent =
            now.toLocaleTimeString('id-ID', {hour:'2-digit', minute:'2-digit', second:'2-digit'});
        document.getElementById('liveDate').textContent =
            now.toLocaleDateString('id-ID', {weekday:'long', day:'numeric', month:'long', year:'numeric'});
    }
    updateClock();
    setInterval(updateClock, 1000);

    /* ── Loading overlay saat form disubmit ── */
    function showLoading() {
        document.getElementById('loadingOverlay').classList.add('show');
    }

    /* ── Tampilkan nama file setelah dipilih ── */
    function showFileName(input) {
        var info = document.getElementById('fileInfo');
        if (input.files && input.files[0]) {
            var f = input.files[0];
            info.style.display = 'block';
            info.innerHTML = '📄 <strong>' + f.name + '</strong> (' +
                Math.round(f.size / 1024) + ' KB) — Siap diproses';
        } else {
            info.style.display = 'none';
        }
    }

    /* ── Filter & Pencarian (client-side) ── */
    var totalRows = document.querySelectorAll('.tbl-row').length;

    function filterTable() {
        var name  = document.getElementById('searchName').value.toLowerCase().trim();
        var date  = document.getElementById('filterDate').value;
        var ket   = document.getElementById('filterKet').value;
        var rows  = document.querySelectorAll('.tbl-row');
        var shown = 0;

        rows.forEach(function(tr) {
            var matchName = !name || tr.getAttribute('data-nama').includes(name);
            var matchDate = !date || tr.getAttribute('data-tanggal') === date;
            var matchKet  = !ket  || tr.getAttribute('data-ket') === ket;

            if (matchName && matchDate && matchKet) {
                tr.style.display = '';
                shown++;
            } else {
                tr.style.display = 'none';
            }
        });

        /* Update counter */
        document.getElementById('shownCount').textContent = shown;
        document.getElementById('countBadge').textContent = shown + ' data';

        /* Pesan kosong jika tidak ada hasil */
        var emptyRow = document.getElementById('emptyFilterRow');
        if (shown === 0 && rows.length > 0) {
            if (!emptyRow) {
                var tbody = document.getElementById('tableBody');
                var tr = document.createElement('tr');
                tr.id = 'emptyFilterRow';
                tr.innerHTML = '<td colspan="7"><div class="empty-state">' +
                    '<div class="empty-icon">🔍</div>' +
                    '<div class="empty-text">Tidak ada data ditemukan</div>' +
                    '<div class="empty-sub">Coba ubah kata kunci atau filter</div>' +
                    '</div></td>';
                tbody.appendChild(tr);
            }
        } else {
            if (emptyRow) emptyRow.remove();
        }
    }

    function resetFilter() {
        document.getElementById('searchName').value = '';
        document.getElementById('filterDate').value = '';
        document.getElementById('filterKet').value  = '';
        filterTable();
    }
</script>

</body>
</html>