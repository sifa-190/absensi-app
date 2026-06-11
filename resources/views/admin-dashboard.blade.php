<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kipin — Dashboard Absensi</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&family=JetBrains+Mono:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/admin/admin-dashboard.css') }}">
</head>
<body>

<div class="loading-overlay" id="loadingOverlay">
    <div class="spinner"></div>
    <div class="loading-text">Memproses data absensi...</div>
</div>

<div class="sidebar-overlay" id="sidebarOverlay" onclick="toggleSidebar()"></div>

<!-- ═══ SIDEBAR ═══ -->
<aside class="sidebar" id="sidebar">
    <div class="sb-brand">
        <div class="sb-logo">
            <img src="{{ asset('images/kipin.png') }}" alt="Kipin" onerror="this.style.display='none';this.nextElementSibling.style.display='flex'">
            <span class="sb-logo-fallback" style="display:none">K</span>
        </div>
        <div class="sb-brand-text">
            <div class="sb-title">Kipin Absensi</div>
            <div class="sb-sub">Monitoring real-time</div>
        </div>
    </div>

    <nav class="sb-nav">
        <div class="sb-section-label">Menu Utama</div>
        <button class="sb-item active" id="sbDashboard" onclick="switchPage('dashboard')">
            <svg viewBox="0 0 24 24"><rect x="3" y="3" width="7" height="7" rx="1.5"/><rect x="14" y="3" width="7" height="7" rx="1.5"/><rect x="3" y="14" width="7" height="7" rx="1.5"/><rect x="14" y="14" width="7" height="7" rx="1.5"/></svg>
            <span class="sb-item-label">Dashboard</span>
        </button>
        <button class="sb-item" id="sbData" onclick="switchPage('data')">
            <svg viewBox="0 0 24 24"><rect x="3" y="3" width="18" height="18" rx="2"/><path d="M3 9h18M9 21V9"/></svg>
            <span class="sb-item-label">Data Presensi</span>
            <span class="sb-badge" id="sbDataBadge">—</span>
        </button>
        <button class="sb-item" id="sbPerforma" onclick="switchPage('performa')">
            <svg viewBox="0 0 24 24"><rect x="2" y="13" width="4" height="9" rx="1"/><rect x="9" y="9" width="4" height="13" rx="1"/><rect x="16" y="5" width="4" height="17" rx="1"/><path d="M4 6l4-3 4 3 4-4"/></svg>
            <span class="sb-item-label">Chart Performa</span>
        </button>

        <div class="sb-section-label" style="margin-top:8px">Manajemen</div>
        <button class="sb-item" id="sbUpload" onclick="switchPage('upload')">
            <svg viewBox="0 0 24 24"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
            <span class="sb-item-label">Upload CSV</span>
        </button>
    </nav>

    <div class="sb-bottom">
        <div class="sb-user">
            <div class="sb-avatar">AD</div>
            <div>
                <div class="sb-uname">Admin</div>
                <div class="sb-urole">Administrator</div>
            </div>
            <form action="{{ route('logout') }}" method="POST" style="margin-left:auto">
                @csrf
                <button type="submit" class="sb-logout" title="Logout">
                    <svg viewBox="0 0 24 24"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
                </button>
            </form>
        </div>
    </div>
</aside>

<!-- ═══ MAIN AREA ═══ -->
<div class="main-area">

    <!-- TOPBAR -->
    <header class="topbar">
        <button class="hamburger" id="hamburgerBtn" onclick="toggleSidebar()" aria-label="Toggle menu">
            <svg viewBox="0 0 24 24"><line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="18" x2="21" y2="18"/></svg>
        </button>
        <div class="tb-breadcrumb">
            <span class="tb-bc-page" id="tbPageName">Dashboard</span>
            <span class="tb-bc-sep">/</span>
            <span class="tb-bc-sub" id="tbPageSub">Ringkasan</span>
        </div>
        <div class="tb-right">
            <div id="topbarPeriod" style="display:flex;align-items:center;gap:8px">
                <div class="view-pills">
                    <button class="vp-btn" id="vpHarian"   onclick="setViewMode('harian')">Harian</button>
                    <button class="vp-btn" id="vpMingguan" onclick="setViewMode('mingguan')">Mingguan</button>
                    <button class="vp-btn active" id="vpBulanan"  onclick="setViewMode('bulanan')">Bulanan</button>
                    <button class="vp-btn" id="vpTahunan"  onclick="setViewMode('tahunan')">Tahunan</button>
                </div>
                <div class="tb-period-selects">
                    <select class="period-select" id="filterTahun" onchange="onPeriodChange()"></select>
                    <select class="period-select" id="filterBulan" onchange="onPeriodChange()">
                        <option value="1">Januari</option><option value="2">Februari</option><option value="3">Maret</option>
                        <option value="4">April</option><option value="5">Mei</option><option value="6">Juni</option>
                        <option value="7">Juli</option><option value="8">Agustus</option><option value="9">September</option>
                        <option value="10">Oktober</option><option value="11">November</option><option value="12">Desember</option>
                    </select>
                    <select class="period-select" id="filterMinggu" onchange="onPeriodChange()" style="display:none">
                        <option value="1">Minggu 1 (1–7)</option><option value="2">Minggu 2 (8–14)</option>
                        <option value="3">Minggu 3 (15–21)</option><option value="4">Minggu 4 (22–28)</option><option value="5">Minggu 5 (29–31)</option>
                    </select>
                    <select class="period-select" id="filterHari" onchange="onPeriodChange()" style="display:none"></select>
                </div>
            </div>
            {{-- JAM: id harus "liveClock" --}}
            <div class="tb-clock"><span class="live-dot"></span><span id="liveClock">--:--:--</span></div>
        </div>
    </header>

    <!-- CONTENT AREA -->
    <div class="content">

        <!-- ═══ PAGE: DASHBOARD ═══ -->
        <div class="page active" id="pageDashboard">

            @if(session('success'))
            <div class="alert-ok" id="alertSuccess">{!! session('success') !!}</div>
            @endif
            @if(session('error'))
            <div class="alert-err" id="alertError">{!! session('error') !!}</div>
            @endif

            <div class="stat-grid">
                <div class="stat-card c-total">
                    <div class="sc-icon"><svg viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="18" rx="2"/><path d="M16 2v4M8 2v4M3 10h18"/></svg></div>
                    <div class="sc-label">Total Absensi</div>
                    <div class="sc-val" id="statTotal">0</div>
                    <div class="sc-bar"></div>
                </div>
                <div class="stat-card c-tepat">
                    <div class="sc-icon"><svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="9"/><path d="M9 12l2 2 4-4"/></svg></div>
                    <div class="sc-label">Tepat Waktu</div>
                    <div class="sc-val" id="statTepat">0</div>
                    <div class="sc-bar"></div>
                </div>
                <div class="stat-card c-lambat">
                    <div class="sc-icon"><svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="9"/><path d="M12 8v4"/><circle cx="12" cy="16" r=".5" fill="currentColor"/></svg></div>
                    <div class="sc-label">Terlambat</div>
                    <div class="sc-val" id="statLambat">0</div>
                    <div class="sc-bar"></div>
                </div>
                <div class="stat-card c-cepat">
                    <div class="sc-icon"><svg viewBox="0 0 24 24"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg></div>
                    <div class="sc-label">Pulang Cepat</div>
                    <div class="sc-val" id="statCepat">0</div>
                    <div class="sc-bar"></div>
                </div>
                <div class="stat-card c-absent">
                    <div class="sc-icon"><svg viewBox="0 0 24 24"><circle cx="12" cy="8" r="4"/><path d="M4 20c0-4 3.58-7 8-7s8 3 8 7"/><line x1="17" y1="11" x2="23" y2="11"/></svg></div>
                    <div class="sc-label">Tidak Masuk</div>
                    <div class="sc-val" id="statAbsent">0</div>
                    <div class="sc-bar"></div>
                </div>
            </div>

            <div class="dash-two-col">
                <div class="card">
                    <div class="card-head">
                        <svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="9"/><path d="M12 3a9 9 0 0 1 9 9h-9z"/></svg>
                        Distribusi Status
                        <span class="card-chip" id="chartViewChip">Bulanan</span>
                    </div>
                    <div class="donut-inner">
                        <div class="donut-canvas-wrap">
                            <canvas id="donutChart"></canvas>
                            <div class="donut-center">
                                <div class="donut-center-num" id="donutCenterNum">0</div>
                                <div class="donut-center-lbl" id="donutCenterLbl">total</div>
                            </div>
                        </div>
                        <div class="donut-legends" id="donutLegends"></div>
                    </div>
                </div>

                <div style="display:flex;flex-direction:column;gap:14px">
                    <div class="card" style="flex:1">
                        <div class="card-head">
                            <svg viewBox="0 0 24 24"><rect x="3" y="3" width="18" height="18" rx="2"/><path d="M3 9h18M9 21V9"/></svg>
                            Ringkasan Karyawan — <span id="chartPeriodLabel" style="color:var(--accent);margin-left:4px">—</span>
                        </div>
                        <div id="dashQuickTable" style="padding:0">
                            <div class="empty"><div class="empty-icon">📊</div><div class="empty-text">Pilih periode untuk melihat data</div></div>
                        </div>
                    </div>
                </div>
            </div>

        </div><!-- /pageDashboard -->

        <!-- ═══ PAGE: DATA PRESENSI ═══ -->
        <div class="page" id="pageData">
            <div class="card" style="margin-bottom:16px">
                <div class="card-head">
                    <svg viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                    Filter &amp; Pencarian
                </div>
                <div class="card-body">
                    <div class="filter-row">
                        <div class="f-group">
                            <label class="f-label">Cari Nama</label>
                            <input class="f-input" type="text" id="searchName" placeholder="Nama karyawan..." oninput="applyFiltersAndRender()">
                        </div>
                        <div class="f-group">
                            <label class="f-label">Tanggal</label>
                            <input class="f-input" type="date" id="filterDate" onchange="onFilterDateChange()">
                        </div>
                        <div class="f-group">
                            <label class="f-label">Keterangan</label>
                            <select class="f-input" id="filterKet" onchange="applyFiltersAndRender()">
                                <option value="">Semua</option>
                                <option value="tepat">Tepat Waktu</option>
                                <option value="terlambat">Terlambat</option>
                                <option value="cepat">Pulang Cepat</option>
                                <option value="absent">Tidak Masuk</option>
                            </select>
                        </div>
                        <div><button class="btn-reset" onclick="resetFilter()">↺ Reset</button></div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-head">
                    <svg viewBox="0 0 24 24"><rect x="3" y="3" width="18" height="18" rx="2"/><path d="M3 9h18M9 21V9"/></svg>
                    Data Presensi — <span id="tablePeriodLabel" style="color:var(--accent);margin-left:4px">—</span>
                    <span class="card-chip" id="countChip">0 data</span>
                    <button class="btn-print" onclick="cetakLaporan()" style="margin-left:8px">
                        <svg viewBox="0 0 24 24"><polyline points="6 9 6 2 18 2 18 9"/><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/><rect x="6" y="14" width="12" height="8"/></svg>
                        Cetak
                    </button>
                </div>
                <div class="karyawan-nav-strip" id="karyawanNavStrip" style="display:none">
                    <span class="karyawan-nav-label">Karyawan:</span>
                </div>
                <div class="emp-table-header" id="empTableHeader" style="display:none">
                    <div class="emp-table-avatar" id="empTableAvatar">??</div>
                    <div class="emp-table-info">
                        <div class="emp-table-name" id="empTableName">—</div>
                        <div class="emp-table-meta">
                            <span class="emp-table-pin" id="empTablePin">—</span>
                            <span class="emp-table-days" id="empTableDays">0 hari</span>
                        </div>
                    </div>
                    <div class="emp-table-stats" id="empTableStats"></div>
                </div>
                <div class="tbl-wrap" id="tableWrap">
                    <table id="mainTable">
                        <thead id="tableHead"><tr><th>No</th><th>Tanggal</th><th>Jam Masuk</th><th>Jam Pulang</th><th>Status</th></tr></thead>
                        <tbody id="tableBody"></tbody>
                    </table>
                </div>
                <div class="pg-bar" id="pgBar">
                    <div class="pg-info" id="pgInfo">—</div>
                    <div class="pg-btns" id="pgBtns"></div>
                </div>
            </div>
        </div><!-- /pageData -->

        <!-- ═══ PAGE: PERFORMA ═══ -->
        <div class="page" id="pagePerforma">
            <div style="display:flex;align-items:center;gap:10px;margin-bottom:14px;flex-wrap:wrap">
                <select class="period-select" id="filterTahunPerf" onchange="renderPerfPage()"></select>
                <select class="period-select" id="filterBulanPerf" onchange="renderPerfPage()">
                    <option value="0">Semua Bulan</option>
                    <option value="1">Januari</option><option value="2">Februari</option><option value="3">Maret</option>
                    <option value="4">April</option><option value="5">Mei</option><option value="6">Juni</option>
                    <option value="7">Juli</option><option value="8">Agustus</option><option value="9">September</option>
                    <option value="10">Oktober</option><option value="11">November</option><option value="12">Desember</option>
                </select>
                <span style="font-size:12px;color:var(--text-mute);margin-left:auto" id="perfPeriodHint"></span>
            </div>

            <div class="card">
                <div class="card-head">
                    <svg viewBox="0 0 24 24"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                    Pilih Karyawan
                    <span class="card-chip" id="empCountChip">0 karyawan</span>
                    <span style="font-size:11px;color:var(--text-mute);margin-left:4px">← klik untuk lihat chart</span>
                </div>
                <div class="emp-selector-grid" id="empSelectorGrid"></div>
            </div>

            <div class="card chart-panel" id="empChartCard">
                <div class="bar-chart-topbar">
                    <div class="bar-chart-emp-info">
                        <div class="bar-chart-emp-avatar" id="bcAvatar">??</div>
                        <div><div class="bar-chart-emp-name" id="bcName">—</div><div class="bar-chart-emp-pin" id="bcPin">—</div></div>
                    </div>
                    <div class="bar-mode-btns">
                        <button class="bar-mode-btn active" id="btnModeAll"   onclick="setBarMode('all')">Semua Data</button>
                        <button class="bar-mode-btn"        id="btnModeHadir" onclick="setBarMode('hadir')">Kehadiran</button>
                        <button class="bar-mode-btn"        id="btnModeLate"  onclick="setBarMode('late')">Keterlambatan</button>
                    </div>
                </div>
                <div class="chart-legend-custom" id="chartLegendCustom"></div>
                <div class="bar-chart-body"><div class="bar-chart-canvas-wrap"><canvas id="empBarChart"></canvas></div></div>
                <div class="yearly-stat-strip" id="yearlyStatStrip"></div>
            </div>

            <div class="card" id="empChartHint">
                <div class="chart-select-hint">
                    <div class="csh-icon"><svg viewBox="0 0 24 24"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/></svg></div>
                    <div class="csh-text">Pilih karyawan untuk melihat chart performa</div>
                    <div class="csh-sub">Data kehadiran & keterlambatan per bulan / per hari</div>
                </div>
            </div>
        </div><!-- /pagePerforma -->

        <!-- ═══ PAGE: UPLOAD ═══ -->
        <div class="page" id="pageUpload">
            <div class="upload-page">
                <div class="card">
                    <div class="card-head">
                        <svg viewBox="0 0 24 24"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
                        Upload Data Revo <span class="card-chip">CSV</span>
                    </div>
                    <div class="card-body">
                        {{-- action sesuaikan dengan route Laravel kamu --}}
                        <form action="{{ route('import.presensi') }}" method="POST" enctype="multipart/form-data" id="uploadForm">
                            @csrf
                            {{-- Input file hidden, name="file_csv[]" --}}
                            <input type="file" id="fileInputHidden" name="file_csv[]" multiple accept=".csv"
                                   style="display:none" onchange="handleFileSelect(this)">

                            <!-- DROP ZONE -->
                            <div id="dropZone" onclick="document.getElementById('fileInputHidden').click()"
                                style="border:2px dashed rgba(99,102,241,0.4);border-radius:16px;padding:48px 24px;
                                       text-align:center;cursor:pointer;transition:all .25s;
                                       background:rgba(99,102,241,0.05);margin-bottom:20px">
                                <div style="width:64px;height:64px;border-radius:16px;background:rgba(99,102,241,0.15);display:flex;align-items:center;justify-content:center;margin:0 auto 16px">
                                    <svg viewBox="0 0 24 24" width="28" height="28" fill="none" stroke="#818cf8" stroke-width="1.8">
                                        <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
                                        <polyline points="17 8 12 3 7 8"/>
                                        <line x1="12" y1="3" x2="12" y2="15"/>
                                    </svg>
                                </div>
                                <div style="font-size:15px;font-weight:600;color:#e2e8f0;margin-bottom:6px">Drag &amp; drop file CSV di sini</div>
                                <div style="font-size:12px;color:#64748b;margin-bottom:16px">atau klik untuk pilih file dari komputer</div>
                                <div style="display:inline-flex;align-items:center;gap:6px;padding:6px 16px;border-radius:20px;background:rgba(99,102,241,0.15);border:1px solid rgba(99,102,241,0.3);font-size:11px;font-weight:600;color:#818cf8">
                                    <svg viewBox="0 0 24 24" width="12" height="12" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                                    Format: .CSV
                                </div>
                            </div>

                            <!-- FILE LIST -->
                            <div id="fileList" style="display:none;margin-bottom:16px"></div>

                            <!-- SUBMIT -->
                            <button class="btn-upload" type="submit" id="btnSubmitUpload" disabled
                                style="width:100%;justify-content:center;padding:12px;font-size:14px;opacity:0.4;cursor:not-allowed">
                                <svg viewBox="0 0 24 24"><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/><path d="M5 19h14"/></svg>
                                Upload &amp; Proses
                            </button>
                        </form>
                    </div>
                </div>

                <div class="card" style="margin-top:16px">
                    <div class="card-head">
                        <svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                        Panduan Upload
                    </div>
                    <div class="card-body" style="display:flex;flex-direction:column;gap:10px">
                        <div style="display:flex;align-items:center;gap:10px;padding:10px;border-radius:8px;background:rgba(34,197,94,0.08);border:1px solid rgba(34,197,94,0.2)">
                            <span style="font-size:16px">✅</span>
                            <span style="font-size:12px;color:#94a3b8">File harus format <strong style="color:#4ade80">.CSV</strong> hasil ekspor mesin absensi Revo</span>
                        </div>
                        <div style="display:flex;align-items:center;gap:10px;padding:10px;border-radius:8px;background:rgba(99,102,241,0.08);border:1px solid rgba(99,102,241,0.2)">
                            <span style="font-size:16px">📂</span>
                            <span style="font-size:12px;color:#94a3b8">Bisa upload <strong style="color:#818cf8">beberapa file sekaligus</strong> dalam satu kali proses</span>
                        </div>
                        <div style="display:flex;align-items:center;gap:10px;padding:10px;border-radius:8px;background:rgba(245,158,11,0.08);border:1px solid rgba(245,158,11,0.2)">
                            <span style="font-size:16px">⚠️</span>
                            <span style="font-size:12px;color:#94a3b8">Data duplikat akan <strong style="color:#fbbf24">otomatis diabaikan</strong> oleh sistem</span>
                        </div>
                    </div>
                </div>
            </div>
        </div><!-- /pageUpload -->

        <div style="text-align:center;padding:1rem 0 .5rem;font-size:12px;color:var(--text-light)">
            &copy; {{ date('Y') }} <span style="color:var(--accent);font-weight:600">Kipin</span> &mdash; Sistem Monitoring Absensi &middot; Sakera
        </div>

    </div><!-- /content -->
</div><!-- /main-area -->

<!-- ═══ SCRIPT (urutan penting!) ═══ -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2.2.0/dist/chartjs-plugin-datalabels.min.js"></script>

<!-- Raw Data dari PHP -->
<script id="rawDataScript" type="application/json">
[
@forelse($data as $i => $d)
@php
    $waktu       = \Carbon\Carbon::parse($d->waktu_absensi);
    $isMasuk     = str_contains(strtolower($d->status_mesin), 'masuk');
    $terlambat   = $isMasuk  && $waktu->format('H:i:s') > '08:30:00';
    $pulangCepat = !$isMasuk && $waktu->format('H:i:s') < '15:30:00';
    $hariKe      = (int)$waktu->format('j');
    $mingguKe    = (int)ceil($hariKe / 7);
@endphp
{
    "pin":          "{{ $d->karyawan->id_mesin }}",
    "nama":         "{{ addslashes($d->karyawan->nama) }}",
    "tanggal":      "{{ $waktu->format('Y-m-d') }}",
    "tanggalFmt":   "{{ $waktu->translatedFormat('d M Y') }}",
    "waktu":        "{{ $waktu->format('H:i:s') }}",
    "bulan":        {{ (int)$waktu->format('n') }},
    "tahun":        {{ (int)$waktu->format('Y') }},
    "hari":         {{ $hariKe }},
    "minggu":       {{ $mingguKe }},
    "isMasuk":      {{ $isMasuk ? 'true' : 'false' }},
    "terlambat":    {{ $terlambat ? 'true' : 'false' }},
    "pulangCepat":  {{ $pulangCepat ? 'true' : 'false' }}
}{{ !$loop->last ? ',' : '' }}
@empty
@endforelse
]
</script>

<!-- JS Eksternal Admin -->
<script src="{{ asset('js/admin/admin-dashboard.js') }}"></script>

</body>
</html>