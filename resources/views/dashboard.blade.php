<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kipin — Dashboard Absensi</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;600&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg:        #f0f4ff;
            --bg2:       #e8eeff;
            --bg3:       #ffffff;
            --card:      #ffffff;
            --border:    rgba(99,102,241,0.12);
            --border2:   rgba(99,102,241,0.25);
            --accent:    #6366f1;
            --accent2:   #4f46e5;
            --accent3:   #818cf8;
            --accentbg:  #eef2ff;
            --teal:      #0d9488;
            --tealbg:    #ccfbf1;
            --amber:     #d97706;
            --amberbg:   #fef3c7;
            --rose:      #e11d48;
            --rosebg:    #ffe4e6;
            --green:     #16a34a;
            --greenbg:   #dcfce7;
            --text:      #1e1b4b;
            --text2:     #4338ca;
            --text3:     #6366f1;
            --textmute:  #64748b;
            --textlight: #94a3b8;
            --radius:    16px;
            --radius-sm: 10px;
        }

        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            background: var(--bg);
            min-height: 100vh;
            font-family: 'Plus Jakarta Sans', sans-serif;
            color: var(--text);
            overflow-x: hidden;
        }

        body::before {
            content: '';
            position: fixed; inset: 0;
            background-image:
                linear-gradient(rgba(99,102,241,0.04) 1px, transparent 1px),
                linear-gradient(90deg, rgba(99,102,241,0.04) 1px, transparent 1px);
            background-size: 36px 36px;
            pointer-events: none; z-index: 0;
        }

        .glow-blob { position: fixed; border-radius: 50%; filter: blur(80px); pointer-events: none; z-index: 0; }
        .glow-1 { width: 600px; height: 600px; background: rgba(99,102,241,0.10); top: -160px; left: -120px; }
        .glow-2 { width: 500px; height: 500px; background: rgba(13,148,136,0.07); bottom: 0; right: -100px; }
        .glow-3 { width: 300px; height: 300px; background: rgba(225,29,72,0.05); top: 40%; left: 50%; }

        /* LOADING */
        .loading-overlay {
            display: none; position: fixed; inset: 0;
            background: rgba(240,244,255,0.92); backdrop-filter: blur(8px);
            z-index: 9999; flex-direction: column;
            align-items: center; justify-content: center; gap: 20px;
        }
        .loading-overlay.show { display: flex; }
        .spinner { width: 48px; height: 48px; border: 3px solid #e0e7ff; border-top-color: var(--accent); border-radius: 50%; animation: spin .7s linear infinite; }
        .loading-text { font-size: 14px; color: var(--textmute); font-weight: 600; }
        .loading-bar-wrap { width: 180px; height: 3px; background: #e0e7ff; border-radius: 99px; overflow: hidden; }
        .loading-bar { height: 100%; width: 40%; background: linear-gradient(90deg, var(--accent), var(--teal)); border-radius: 99px; animation: barSlide 1.4s ease-in-out infinite; }

        /* WRAP */
        .wrap { position: relative; z-index: 1; max-width: 1240px; margin: 0 auto; padding: 1.5rem 1.5rem 3rem; }

        /* TOPBAR */
        .topbar {
            display: flex; align-items: center; gap: 18px;
            background: linear-gradient(135deg, #6366f1 0%, #4f46e5 50%, #0d9488 100%);
            border-radius: 20px; padding: 1.1rem 1.6rem;
            margin-bottom: 1.5rem; animation: slideDown .5s ease;
            position: relative; overflow: hidden;
            box-shadow: 0 8px 32px rgba(99,102,241,0.28), 0 2px 8px rgba(99,102,241,0.15);
        }
        .topbar::before {
            content: ''; position: absolute; inset: 0;
            background: radial-gradient(ellipse at 80% 50%, rgba(255,255,255,0.08) 0%, transparent 60%);
            pointer-events: none;
        }

        .logo-wrap {
            width: 68px; height: 68px; border-radius: 18px;
            background: rgba(255,255,255,0.18);
            border: 2px solid rgba(255,255,255,0.35);
            display: flex; align-items: center; justify-content: center;
            flex-shrink: 0; overflow: hidden; backdrop-filter: blur(6px);
            box-shadow: 0 6px 24px rgba(0,0,0,0.15), inset 0 1px 0 rgba(255,255,255,0.25);
        }
        .logo-wrap img { width: 54px; height: 54px; object-fit: contain; }
        .logo-fallback { width: 36px; height: 36px; fill: #fff; display: none; }

        .topbar-info { flex: 1; }
        .topbar-title { font-size: 19px; font-weight: 800; color: #fff; letter-spacing: -.03em; line-height: 1; }
        .topbar-sub { font-size: 12px; color: rgba(255,255,255,0.75); margin-top: 5px; display: flex; align-items: center; gap: 7px; }
        .live-dot { width: 7px; height: 7px; border-radius: 50%; background: #a3e635; box-shadow: 0 0 6px #a3e635; animation: pulse 2s infinite; }

        .topbar-right { text-align: right; flex-shrink: 0; }
        .clock { font-family: 'JetBrains Mono', monospace; font-size: 24px; font-weight: 600; color: #fff; letter-spacing: -.02em; line-height: 1; }
        .date-label { font-size: 11px; color: rgba(255,255,255,0.7); margin-top: 4px; }

        /* STAT GRID */
        .stat-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 14px; margin-bottom: 1.5rem; }
        .stat-card {
            background: var(--card); border: 1px solid var(--border);
            border-radius: var(--radius); padding: 1.3rem 1.4rem;
            position: relative; overflow: hidden;
            animation: fadeUp .5s ease both;
            box-shadow: 0 2px 12px rgba(99,102,241,0.06);
            transition: transform .2s, box-shadow .2s;
        }
        .stat-card:hover { transform: translateY(-3px); box-shadow: 0 8px 28px rgba(99,102,241,0.13); }
        .stat-card::before { content: ''; position: absolute; top: 0; left: 0; right: 0; height: 3px; }
        .c-total::before  { background: linear-gradient(90deg, #6366f1, #818cf8); }
        .c-tepat::before  { background: linear-gradient(90deg, #16a34a, #4ade80); }
        .c-lambat::before { background: linear-gradient(90deg, #d97706, #fbbf24); }
        .c-cepat::before  { background: linear-gradient(90deg, #e11d48, #fb7185); }
        .stat-card:nth-child(1) { animation-delay: .05s; }
        .stat-card:nth-child(2) { animation-delay: .10s; }
        .stat-card:nth-child(3) { animation-delay: .15s; }
        .stat-card:nth-child(4) { animation-delay: .20s; }

        .stat-icon { width: 40px; height: 40px; border-radius: 12px; display: flex; align-items: center; justify-content: center; margin-bottom: 14px; }
        .stat-icon svg { width: 20px; height: 20px; }
        .icon-total  { background: var(--accentbg); } .icon-total svg  { stroke: var(--accent); }
        .icon-tepat  { background: var(--greenbg);  } .icon-tepat svg  { stroke: var(--green);  }
        .icon-lambat { background: var(--amberbg);  } .icon-lambat svg { stroke: var(--amber);  }
        .icon-cepat  { background: var(--rosebg);   } .icon-cepat svg  { stroke: var(--rose);   }

        .stat-label { font-size: 11px; font-weight: 700; color: var(--textmute); text-transform: uppercase; letter-spacing: .07em; margin-bottom: 5px; }
        .stat-val { font-size: 34px; font-weight: 800; letter-spacing: -.04em; line-height: 1; }
        .v-total { color: var(--accent); } .v-tepat { color: var(--green); }
        .v-lambat { color: var(--amber); } .v-cepat  { color: var(--rose);  }

        /* CARD */
        .card { background: var(--card); border: 1px solid var(--border); border-radius: var(--radius); margin-bottom: 1.25rem; overflow: hidden; animation: fadeUp .5s ease both; box-shadow: 0 2px 12px rgba(99,102,241,0.06); }
        .card-head { display: flex; align-items: center; gap: 10px; padding: .9rem 1.4rem; border-bottom: 1px solid var(--border); font-size: 13px; font-weight: 700; color: var(--text); background: linear-gradient(90deg, #fafbff 0%, #fff 100%); }
        .card-head-icon { width: 30px; height: 30px; border-radius: 9px; background: var(--accentbg); display: flex; align-items: center; justify-content: center; }
        .card-head-icon svg { width: 15px; height: 15px; stroke: var(--accent); }
        .chip { font-size: 10px; padding: 3px 10px; border-radius: 20px; background: var(--accentbg); color: var(--accent); border: 1px solid rgba(99,102,241,0.2); font-weight: 700; letter-spacing: .04em; margin-left: auto; }
        .card-body { padding: 1.1rem 1.4rem; }

        /* ALERTS */
        .alert-ok  { background: var(--greenbg); border: 1px solid #bbf7d0; color: #15803d; border-radius: var(--radius-sm); padding: 10px 14px; font-size: 13px; margin-bottom: 1rem; animation: fadeUp .4s ease; font-weight: 600; }
        .alert-err { background: var(--rosebg);  border: 1px solid #fecdd3; color: #be123c; border-radius: var(--radius-sm); padding: 10px 14px; font-size: 13px; margin-bottom: 1rem; animation: fadeUp .4s ease; font-weight: 600; }

        /* UPLOAD */
        .upload-zone { display: flex; gap: 12px; align-items: center; flex-wrap: wrap; }
        .upload-input { flex: 1; min-width: 200px; padding: 10px 14px; font-size: 13px; border: 1.5px dashed rgba(99,102,241,0.3); border-radius: 10px; background: var(--accentbg); color: var(--text); font-family: inherit; transition: border-color .2s; }
        .upload-input:hover { border-color: var(--accent); }
        .upload-input::file-selector-button { background: white; color: var(--accent); border: 1px solid rgba(99,102,241,0.3); padding: 4px 12px; border-radius: 6px; cursor: pointer; font-size: 12px; font-weight: 700; margin-right: 10px; font-family: inherit; }
        .file-info { font-size: 11px; color: var(--textmute); margin-top: 8px; display: none; }
        .btn-upload { padding: 10px 24px; font-size: 13px; font-weight: 700; background: linear-gradient(135deg, var(--accent), var(--accent2)); color: #fff; border: none; border-radius: 10px; cursor: pointer; font-family: inherit; display: flex; align-items: center; gap: 8px; transition: opacity .2s, transform .1s; white-space: nowrap; box-shadow: 0 4px 14px rgba(99,102,241,0.35); }
        .btn-upload:hover { opacity: .88; } .btn-upload:active { transform: scale(.97); }
        .btn-upload svg { width: 14px; height: 14px; stroke: #fff; }

        /* FILTER */
        .filter-row { display: grid; grid-template-columns: 1fr 1fr 1fr auto; gap: 12px; align-items: end; }
        .f-group { display: flex; flex-direction: column; gap: 5px; }
        .f-label { font-size: 10px; font-weight: 700; color: var(--textmute); text-transform: uppercase; letter-spacing: .07em; }
        .f-input { padding: 9px 12px; font-size: 13px; border: 1.5px solid rgba(99,102,241,0.15); border-radius: var(--radius-sm); background: #fafbff; color: var(--text); font-family: inherit; outline: none; transition: border-color .2s, box-shadow .2s; }
        .f-input:focus { border-color: var(--accent); box-shadow: 0 0 0 3px rgba(99,102,241,0.1); }
        .f-input option { background: #fff; }
        .btn-reset { padding: 9px 16px; font-size: 12px; font-weight: 600; background: #f1f5f9; color: var(--textmute); border: 1.5px solid #e2e8f0; border-radius: var(--radius-sm); cursor: pointer; font-family: inherit; white-space: nowrap; transition: all .2s; }
        .btn-reset:hover { border-color: var(--accent); color: var(--accent); background: var(--accentbg); }

        /* RESULT BAR */
        .result-bar { padding: 8px 1.4rem; font-size: 12px; color: var(--textmute); background: #fafbff; border-bottom: 1px solid var(--border); display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 8px; }
        .result-bar strong { color: var(--accent); font-weight: 700; }

        /* PER PAGE SELECT */
        .per-page-wrap { display: flex; align-items: center; gap: 7px; font-size: 12px; color: var(--textmute); }
        .per-page-sel { font-size: 12px; border: 1.5px solid rgba(99,102,241,0.15); border-radius: 7px; padding: 3px 8px; background: #fff; color: var(--text); font-family: inherit; cursor: pointer; outline: none; transition: border-color .2s; }
        .per-page-sel:focus { border-color: var(--accent); }

        /* TABLE */
        .tbl-wrap { overflow-x: auto; }
        table { width: 100%; border-collapse: collapse; font-size: 13px; }
        thead tr { background: #f8faff; }
        th { padding: 11px 16px; text-align: left; font-size: 10px; font-weight: 700; color: var(--textmute); border-bottom: 1.5px solid rgba(99,102,241,0.1); text-transform: uppercase; letter-spacing: .07em; white-space: nowrap; }
        td { padding: 13px 16px; border-bottom: 1px solid rgba(99,102,241,0.06); vertical-align: middle; color: var(--text); }
        tbody tr:last-child td { border-bottom: none; }
        tbody tr:hover td { background: #f5f7ff; transition: background .15s; }

        .avatar { width: 34px; height: 34px; border-radius: 50%; background: linear-gradient(135deg, var(--accent), var(--teal)); color: #fff; display: inline-flex; align-items: center; justify-content: center; font-size: 11px; font-weight: 800; margin-right: 10px; flex-shrink: 0; letter-spacing: -.02em; box-shadow: 0 2px 8px rgba(99,102,241,0.25); }
        .nama-cell { display: flex; align-items: center; }

        .pin { font-family: 'JetBrains Mono', monospace; font-size: 11px; font-weight: 600; background: var(--accentbg); color: var(--accent); padding: 3px 9px; border-radius: 6px; border: 1px solid rgba(99,102,241,0.18); }

        .badge { display: inline-flex; align-items: center; gap: 5px; padding: 4px 11px; border-radius: 20px; font-size: 11px; font-weight: 700; white-space: nowrap; }
        .badge::before { content: ''; width: 5px; height: 5px; border-radius: 50%; }
        .b-masuk     { background: var(--greenbg); color: #15803d; border: 1px solid #bbf7d0; } .b-masuk::before     { background: #16a34a; }
        .b-pulang    { background: var(--rosebg);  color: #be123c; border: 1px solid #fecdd3; } .b-pulang::before    { background: #e11d48; }
        .b-terlambat { background: var(--amberbg); color: #92400e; border: 1px solid #fde68a; } .b-terlambat::before { background: #d97706; }
        .b-cepat     { background: var(--rosebg);  color: #be123c; border: 1px solid #fecdd3; } .b-cepat::before     { background: #e11d48; }
        .b-tepat     { background: var(--accentbg); color: var(--accent2); border: 1px solid rgba(99,102,241,0.2); } .b-tepat::before { background: var(--accent); }

        /* DONUT CHART */
        .donut-wrap {
            display: flex; flex-wrap: wrap; gap: 28px;
            align-items: center; justify-content: center;
            padding: 1.4rem;
        }
        .donut-canvas-wrap {
            position: relative; width: 210px; height: 210px; flex-shrink: 0;
        }
        .donut-center {
            position: absolute; top: 50%; left: 50%;
            transform: translate(-50%, -50%);
            text-align: center; pointer-events: none;
        }
        .donut-center-num { font-size: 30px; font-weight: 800; color: var(--text); letter-spacing: -.04em; line-height: 1; }
        .donut-center-lbl { font-size: 11px; color: var(--textmute); margin-top: 3px; font-weight: 600; }
        .donut-legends { display: flex; flex-direction: column; gap: 10px; flex: 1; min-width: 200px; }
        .donut-legend-item {
            display: flex; align-items: center; gap: 12px;
            padding: 10px 14px; border-radius: 12px;
            border: 1px solid rgba(99,102,241,0.08);
            cursor: pointer; transition: background .15s, transform .15s;
        }
        .donut-legend-item:hover { transform: translateX(3px); }
        .donut-legend-dot { width: 12px; height: 12px; border-radius: 4px; flex-shrink: 0; }
        .donut-legend-label { font-size: 13px; font-weight: 600; color: var(--text); flex: 1; }
        .donut-legend-val { font-size: 13px; font-weight: 700; }
        .donut-legend-pct { font-size: 11px; color: var(--textmute); font-weight: 500; margin-left: 3px; }

        /* PRINT BUTTON */
        .btn-print {
            display: flex; align-items: center; gap: 8px;
            padding: 8px 20px; font-size: 12px; font-weight: 700;
            background: linear-gradient(135deg, #6366f1, #4f46e5);
            color: #fff; border: none; border-radius: 10px;
            cursor: pointer; font-family: inherit;
            box-shadow: 0 4px 14px rgba(99,102,241,0.30);
            transition: opacity .2s, transform .1s;
            white-space: nowrap; margin-left: 8px;
        }
        .btn-print:hover { opacity: .88; }
        .btn-print:active { transform: scale(.97); }
        .btn-print svg { width: 14px; height: 14px; stroke: #fff; }

        /* PAGINATION */
        .pg-bar {
            display: flex; align-items: center; justify-content: space-between;
            padding: 12px 1.4rem; flex-wrap: wrap; gap: 10px;
            border-top: 1px solid rgba(99,102,241,0.08); background: #fafbff;
        }
        .pg-info { font-size: 12px; color: var(--textmute); font-weight: 500; }
        .pg-btns { display: flex; gap: 4px; align-items: center; flex-wrap: wrap; }
        .pg-btn {
            min-width: 32px; height: 32px; padding: 0 8px;
            border: 1.5px solid rgba(99,102,241,0.15);
            background: #fff; color: var(--text);
            border-radius: 8px; font-size: 12px; font-weight: 600;
            cursor: pointer; display: flex; align-items: center; justify-content: center;
            font-family: inherit; transition: all .15s; line-height: 1;
        }
        .pg-btn:hover:not(:disabled) { border-color: var(--accent); color: var(--accent); background: var(--accentbg); }
        .pg-btn:disabled { opacity: .35; cursor: not-allowed; }
        .pg-btn.pg-active { background: var(--accent); color: #fff; border-color: var(--accent); font-weight: 700; box-shadow: 0 2px 8px rgba(99,102,241,0.3); }
        .pg-ellipsis { font-size: 13px; padding: 0 4px; color: var(--textlight); line-height: 32px; }

        .empty { text-align: center; padding: 3.5rem 1rem; color: var(--textlight); }
        .empty-icon { font-size: 40px; margin-bottom: 14px; }
        .empty-text { font-size: 14px; font-weight: 700; color: var(--textmute); }
        .empty-sub  { font-size: 12px; margin-top: 6px; }

        .footer { text-align: center; padding: 1.5rem 0 .5rem; font-size: 12px; color: var(--textmute); animation: fadeUp .5s .4s ease both; }
        .footer span { color: var(--accent); font-weight: 700; }

        /* ── PRINT STYLES ────────────────────────────────── */
        @media print {
            .glow-blob, .loading-overlay { display: none !important; }
            body { background: #fff !important; }
            body::before { display: none !important; }
            .wrap { max-width: 100%; padding: 0 12px; }

            /* Sembunyikan elemen non-esensial */
            .topbar form,
            .card:has(.upload-input),
            .filter-row,
            .pg-bar,
            .result-bar,
            .footer,
            .btn-print,
            .btn-upload,
            .btn-reset { display: none !important; }

            /* Card tampil rapi */
            .card { box-shadow: none !important; border: 1px solid #ddd !important; page-break-inside: avoid; }

            /* Topbar tetap tampil tapi sederhana */
            .topbar {
                background: #6366f1 !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
                box-shadow: none !important;
                border-radius: 12px !important;
                margin-bottom: 12px !important;
            }

            /* Stat grid tetap tampil */
            .stat-card { box-shadow: none !important; }
            .stat-card::before { -webkit-print-color-adjust: exact; print-color-adjust: exact; }

            /* Badge warna tetap tampil */
            .badge, .pin, .avatar {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            /* Donut chart area */
            .donut-canvas-wrap canvas { max-width: 180px; max-height: 180px; }

            /* Semua baris tabel tampil saat print */
            .tbl-row { display: table-row !important; }

            @page {
                margin: 1.5cm;
                size: A4;
            }
        }

        @keyframes slideDown { from { opacity: 0; transform: translateY(-14px); } to { opacity: 1; transform: translateY(0); } }
        @keyframes fadeUp    { from { opacity: 0; transform: translateY(10px);  } to { opacity: 1; transform: translateY(0); } }
        @keyframes pulse     { 0%, 100% { opacity: 1; transform: scale(1); } 50% { opacity: .4; transform: scale(.5); } }
        @keyframes spin      { to { transform: rotate(360deg); } }
        @keyframes barSlide  { 0% { transform: translateX(-200%); } 100% { transform: translateX(400%); } }
        @keyframes rowIn     { from { opacity: 0; transform: translateY(6px); } to { opacity: 1; transform: translateY(0); } }

        @media (max-width: 900px) { .stat-grid { grid-template-columns: repeat(2, 1fr); } }
        @media (max-width: 600px) {
            .filter-row { grid-template-columns: 1fr; }
            .topbar { flex-wrap: wrap; }
            .topbar-right { display: none; }
            .wrap { padding: 1rem 1rem 2rem; }
            .donut-wrap { flex-direction: column; }
        }
    </style>
</head>
<body>

<div class="glow-blob glow-1"></div>
<div class="glow-blob glow-2"></div>
<div class="glow-blob glow-3"></div>

<div class="loading-overlay" id="loadingOverlay">
    <div class="spinner"></div>
    <div class="loading-text">Memproses data absensi...</div>
    <div class="loading-bar-wrap"><div class="loading-bar"></div></div>
</div>

<div class="wrap">

    <!-- TOPBAR -->
    <div class="topbar">
        <div class="logo-wrap">
            <img src="{{ asset('images/kipin.png') }}"
                 alt="Kipin Logo"
                 onerror="this.style.display='none'; this.nextElementSibling.style.display='block'">
            <svg class="logo-fallback" viewBox="0 0 24 24" fill="none" stroke-width="2">
                <path d="M17 12h-5v5h5v-5zM16 1v2H8V1H6v2H5c-1.11 0-1.99.9-1.99 2L3 19c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2h-1V1h-2zm3 18H5V8h14v11z" fill="currentColor"/>
            </svg>
        </div>

        <div class="topbar-info">
            <div class="topbar-title">Dashboard Monitoring Absensi</div>
            <div class="topbar-sub">
                <span class="live-dot"></span>
                Kipin &mdash; Data Real-time
            </div>
        </div>

        <div class="topbar-right" style="display:flex; align-items:center; gap:14px;">
            <div>
                <div class="clock" id="liveClock"></div>
                <div class="date-label" id="liveDate"></div>
            </div>
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" style="
                    display: flex; align-items: center; gap: 7px;
                    padding: 8px 16px;
                    background: rgba(255,255,255,0.15);
                    border: 1.5px solid rgba(255,255,255,0.3);
                    border-radius: 10px;
                    color: #fff;
                    font-size: 13px;
                    font-weight: 700;
                    font-family: inherit;
                    cursor: pointer;
                    backdrop-filter: blur(6px);
                    transition: background .2s;
                "
                onmouseover="this.style.background='rgba(255,255,255,0.25)'"
                onmouseout="this.style.background='rgba(255,255,255,0.15)'"
                >
                    <svg width="15" height="15" fill="none" stroke="#fff" stroke-width="2.2" viewBox="0 0 24 24">
                        <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/>
                        <polyline points="16 17 21 12 16 7"/>
                        <line x1="21" y1="12" x2="9" y2="12"/>
                    </svg>
                    Logout
                </button>
            </form>
        </div>
    </div>

    <!-- STAT CARDS -->
    <div class="stat-grid">
        @php
            $cTepat  = $data->filter(function($d){
                $w = \Carbon\Carbon::parse($d->waktu_absensi);
                $isMasuk = str_contains(strtolower($d->status_mesin), 'masuk');
                return $isMasuk && $w->format('H:i:s') <= '08:30:00';
            })->count();
            $cLambat = $data->filter(function($d){
                $w = \Carbon\Carbon::parse($d->waktu_absensi);
                $isMasuk = str_contains(strtolower($d->status_mesin), 'masuk');
                return $isMasuk && $w->format('H:i:s') > '08:30:00';
            })->count();
            $cCepat  = $data->filter(function($d){
                $w = \Carbon\Carbon::parse($d->waktu_absensi);
                $isMasuk = str_contains(strtolower($d->status_mesin), 'masuk');
                return !$isMasuk && $w->format('H:i:s') < '15:30:00';
            })->count();
            $cPulang = $data->filter(function($d){
                return !str_contains(strtolower($d->status_mesin), 'masuk');
            })->count();
            $totalData = $data->count();
        @endphp

        <div class="stat-card c-total">
            <div class="stat-icon icon-total">
                <svg fill="none" stroke-width="1.8" viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="18" rx="2"/><path d="M16 2v4M8 2v4M3 10h18"/></svg>
            </div>
            <div class="stat-label">Total Data</div>
            <div class="stat-val v-total">{{ $totalData }}</div>
        </div>
        <div class="stat-card c-tepat">
            <div class="stat-icon icon-tepat">
                <svg fill="none" stroke-width="1.8" viewBox="0 0 24 24"><circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 3"/></svg>
            </div>
            <div class="stat-label">Tepat Waktu</div>
            <div class="stat-val v-tepat">{{ $cTepat }}</div>
        </div>
        <div class="stat-card c-lambat">
            <div class="stat-icon icon-lambat">
                <svg fill="none" stroke-width="1.8" viewBox="0 0 24 24"><circle cx="12" cy="12" r="9"/><path d="M12 8v4"/><circle cx="12" cy="16" r=".5" fill="currentColor"/></svg>
            </div>
            <div class="stat-label">Terlambat</div>
            <div class="stat-val v-lambat">{{ $cLambat }}</div>
        </div>
        <div class="stat-card c-cepat">
            <div class="stat-icon icon-cepat">
                <svg fill="none" stroke-width="1.8" viewBox="0 0 24 24"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
            </div>
            <div class="stat-label">Pulang Cepat</div>
            <div class="stat-val v-cepat">{{ $cCepat }}</div>
        </div>
    </div>

    @if(session('success'))
        <div class="alert-ok">&#10003; {{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert-err">&#10005; {{ session('error') }}</div>
    @endif

    <!-- UPLOAD -->
    <div class="card">
        <div class="card-head">
            <div class="card-head-icon">
                <svg fill="none" stroke-width="2" viewBox="0 0 24 24"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
            </div>
            Upload Data Revo
            <span class="chip">CSV</span>
        </div>
        <div class="card-body">
            <form action="{{ route('import.presensi') }}" method="POST" enctype="multipart/form-data"
                  onsubmit="document.getElementById('loadingOverlay').classList.add('show')">
                @csrf
                <div class="upload-zone">
                    <input type="file" class="upload-input" name="file_csv" required onchange="showFileName(this)">
                    <button class="btn-upload" type="submit">
                        <svg fill="none" stroke-width="2.5" viewBox="0 0 24 24"><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/><path d="M5 19h14"/></svg>
                        Upload &amp; Proses
                    </button>
                </div>
                <div class="file-info" id="fileInfo"></div>
            </form>
        </div>
    </div>

    <!-- GRAFIK DONUT -->
    <div class="card" style="animation-delay:.25s">
        <div class="card-head">
            <div class="card-head-icon">
                <svg fill="none" stroke-width="2" viewBox="0 0 24 24">
                    <circle cx="12" cy="12" r="9"/>
                    <path d="M12 3a9 9 0 0 1 9 9h-9z"/>
                </svg>
            </div>
            Grafik Ringkasan Absensi
            <span class="chip">Donut</span>

            <!-- TOMBOL PRINT PDF -->
            <button class="btn-print" onclick="printDashboard()">
                <svg fill="none" stroke-width="2.2" viewBox="0 0 24 24">
                    <polyline points="6 9 6 2 18 2 18 9"/>
                    <path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/>
                    <rect x="6" y="14" width="12" height="8"/>
                </svg>
                Print / Simpan PDF
            </button>
        </div>

        <div class="donut-wrap">
            <!-- Canvas -->
            <div class="donut-canvas-wrap">
                <canvas id="donutChart"
                        role="img"
                        aria-label="Grafik donut ringkasan absensi: Tepat Waktu {{ $cTepat }}, Terlambat {{ $cLambat }}, Pulang Cepat {{ $cCepat }}, Absensi Pulang {{ $cPulang }}">
                    Tepat Waktu: {{ $cTepat }}, Terlambat: {{ $cLambat }}, Pulang Cepat: {{ $cCepat }}, Absensi Pulang: {{ $cPulang }}
                </canvas>
                <div class="donut-center">
                    <div class="donut-center-num" id="donutCenterNum">{{ $totalData }}</div>
                    <div class="donut-center-lbl" id="donutCenterLbl">total</div>
                </div>
            </div>

            <!-- Legend -->
            <div class="donut-legends">
                @php
                    $legendItems = [
                        ['label' => 'Tepat Waktu',    'val' => $cTepat,  'color' => '#16a34a', 'bg' => '#dcfce7', 'valColor' => '#15803d'],
                        ['label' => 'Terlambat',       'val' => $cLambat, 'color' => '#d97706', 'bg' => '#fef3c7', 'valColor' => '#92400e'],
                        ['label' => 'Pulang Cepat',    'val' => $cCepat,  'color' => '#e11d48', 'bg' => '#ffe4e6', 'valColor' => '#be123c'],
                        ['label' => 'Absensi Pulang',  'val' => $cPulang, 'color' => '#6366f1', 'bg' => '#eef2ff', 'valColor' => '#4338ca'],
                    ];
                @endphp
                @foreach($legendItems as $idx => $item)
                <div class="donut-legend-item"
                        style="background: {{ $item['bg'] }}; border-color: {{ $item['color'] }}22;"
                        onmouseover="highlightDonut({{ $idx }})"
                        onmouseout="resetDonut()">
                        <span class="donut-legend-dot" style="background: {{ $item['color'] }};"></span>
                        <span class="donut-legend-label">{{ $item['label'] }}</span>
                        <span class="donut-legend-val" style="color: {{ $item['valColor'] }};">
                        {{ $item['val'] }}
                        @if($totalData > 0)
                            <span class="donut-legend-pct">({{ round($item['val'] / $totalData * 100) }}%)</span>
                        @endif
                    </span>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- FILTER -->
    <div class="card">
        <div class="card-head">
            <div class="card-head-icon">
                <svg fill="none" stroke-width="2" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
            </div>
            Filter &amp; Pencarian
        </div>
        <div class="card-body">
            <div class="filter-row">
                <div class="f-group">
                    <label class="f-label">Cari Nama</label>
                    <input class="f-input" type="text" id="searchName" placeholder="Nama karyawan..." oninput="filterTable()">
                </div>
                <div class="f-group">
                    <label class="f-label">Tanggal</label>
                    <input class="f-input" type="date" id="filterDate" onchange="filterTable()">
                </div>
                <div class="f-group">
                    <label class="f-label">Keterangan</label>
                    <select class="f-input" id="filterKet" onchange="filterTable()">
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

    <!-- TABLE -->
    <div class="card">
        <div class="card-head">
            <div class="card-head-icon">
                <svg fill="none" stroke-width="2" viewBox="0 0 24 24"><rect x="3" y="3" width="18" height="18" rx="2"/><path d="M3 9h18M9 21V9"/></svg>
            </div>
            Data Presensi
            <span class="chip" id="countChip">{{ $totalData }} data</span>
        </div>

        <!-- RESULT BAR -->
        <div class="result-bar">
            <span>
                Menampilkan <strong id="shownFrom">1</strong>–<strong id="shownTo">30</strong>
                dari <strong id="shownTotal">{{ $totalData }}</strong> data
            </span>
            <div class="per-page-wrap">
                Per halaman:
                <select class="per-page-sel" id="perPageSel" onchange="changePerPage()">
                    <option value="10">10</option>
                    <option value="20">20</option>
                    <option value="30" selected>30</option>
                    <option value="50">50</option>
                    <option value="100">100</option>
                </select>
            </div>
        </div>

        <div class="tbl-wrap">
            <table>
                <thead>
                    <tr>
                        <th>No</th><th>PIN</th><th>Nama Karyawan</th>
                        <th>Tanggal</th><th>Waktu</th><th>Status</th><th>Keterangan</th>
                    </tr>
                </thead>
                <tbody id="tableBody">
                    @forelse($data as $i => $d)
                    @php
                        $waktu       = \Carbon\Carbon::parse($d->waktu_absensi);
                        $isMasuk     = str_contains(strtolower($d->status_mesin), 'masuk');
                        $terlambat   = $isMasuk  && $waktu->format('H:i:s') > '08:30:00';
                        $pulangCepat = !$isMasuk && $waktu->format('H:i:s') < '15:30:00';
                        $ketClass    = $terlambat ? 'terlambat' : ($pulangCepat ? 'cepat' : 'tepat');
                    @endphp
                    <tr class="tbl-row"
                        data-index="{{ $i + 1 }}"
                        data-nama="{{ strtolower($d->karyawan->nama) }}"
                        data-tanggal="{{ $waktu->format('Y-m-d') }}"
                        data-ket="{{ $ketClass }}">
                        <td style="color:var(--textlight);font-size:12px;font-family:'JetBrains Mono',monospace" class="td-no">{{ str_pad($i+1,2,'0',STR_PAD_LEFT) }}</td>
                        <td><span class="pin">{{ $d->karyawan->id_mesin }}</span></td>
                        <td>
                            <div class="nama-cell">
                                <div class="avatar">{{ strtoupper(substr($d->karyawan->nama,0,2)) }}</div>
                                <span style="font-weight:600">{{ $d->karyawan->nama }}</span>
                            </div>
                        </td>
                        <td style="font-size:12px;color:var(--textmute)">{{ $waktu->translatedFormat('d M Y') }}</td>
                        <td style="font-family:'JetBrains Mono',monospace;font-size:12px;color:var(--textmute)">{{ $waktu->format('H:i:s') }}</td>
                        <td>
                            @if($isMasuk)
                                <span class="badge b-masuk">Absensi Masuk</span>
                            @else
                                <span class="badge b-pulang">Absensi Pulang</span>
                            @endif
                        </td>
                        <td>
                            @if($terlambat)
                                <span class="badge b-terlambat">Terlambat</span>
                            @elseif($pulangCepat)
                                <span class="badge b-cepat">Pulang Cepat</span>
                            @else
                                <span class="badge b-tepat">Tepat Waktu</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr id="emptyInitialRow">
                        <td colspan="7">
                            <div class="empty">
                                <div class="empty-icon">&#128203;</div>
                                <div class="empty-text">Belum ada data tersedia</div>
                                <div class="empty-sub">Upload file CSV untuk memulai</div>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- PAGINATION BAR -->
        <div class="pg-bar">
            <div class="pg-info" id="pgInfo">Halaman 1 dari 1</div>
            <div class="pg-btns" id="pgBtns"></div>
        </div>
    </div>

    <div class="footer">
        &copy; {{ date('Y') }} <span>Kipin</span> &mdash; Sistem Monitoring Absensi &middot; Sakera
    </div>

</div>

<!-- Chart.js CDN -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>

<script>
    /* ── CLOCK ─────────────────────────────────────────── */
    function tick() {
        var d = new Date();
        document.getElementById('liveClock').textContent = d.toLocaleTimeString('id-ID',{hour:'2-digit',minute:'2-digit',second:'2-digit'});
        document.getElementById('liveDate').textContent  = d.toLocaleDateString('id-ID',{weekday:'long',day:'numeric',month:'long',year:'numeric'});
    }
    tick(); setInterval(tick, 1000);

    /* ── FILE NAME ─────────────────────────────────────── */
    function showFileName(input) {
        var el = document.getElementById('fileInfo');
        if (input.files && input.files[0]) {
            var f = input.files[0];
            el.style.display = 'block';
            el.innerHTML = '&#128196; <strong style="color:var(--accent)">' + f.name + '</strong> <span>(' + Math.round(f.size/1024) + ' KB) — siap diproses</span>';
        } else { el.style.display = 'none'; }
    }

    /* ── DONUT CHART ───────────────────────────────────── */
    var donutData   = [{{ $cTepat }}, {{ $cLambat }}, {{ $cCepat }}, {{ $cPulang }}];
    var donutLabels = ['Tepat Waktu', 'Terlambat', 'Pulang Cepat', 'Absensi Pulang'];
    var donutColors = ['#16a34a', '#d97706', '#e11d48', '#6366f1'];
    var totalDonut  = {{ $totalData }};

    var donutChart = new Chart(document.getElementById('donutChart'), {
        type: 'doughnut',
        data: {
            labels: donutLabels,
            datasets: [{
                data: donutData,
                backgroundColor: donutColors,
                borderColor: '#ffffff',
                borderWidth: 4,
                hoverOffset: 10,
                hoverBorderWidth: 4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            cutout: '70%',
            animation: { animateScale: true, duration: 900, easing: 'easeInOutQuart' },
            plugins: {
                legend: { display: false },
                tooltip: {
                    callbacks: {
                        label: function(ctx) {
                            var pct = totalDonut > 0 ? Math.round(ctx.raw / totalDonut * 100) : 0;
                            return '  ' + ctx.label + ': ' + ctx.raw + ' (' + pct + '%)';
                        }
                    },
                    padding: 10,
                    cornerRadius: 10,
                    titleFont: { size: 13, weight: 'bold' },
                    bodyFont: { size: 12 }
                }
            },
            onHover: function(e, els) {
                var numEl = document.getElementById('donutCenterNum');
                var lblEl = document.getElementById('donutCenterLbl');
                if (els.length) {
                    var idx = els[0].index;
                    numEl.textContent = donutData[idx];
                    lblEl.textContent = donutLabels[idx];
                    numEl.style.color = donutColors[idx];
                } else {
                    numEl.textContent = totalDonut;
                    lblEl.textContent = 'total';
                    numEl.style.color = 'var(--text)';
                }
            }
        }
    });

    function highlightDonut(idx) {
        donutChart.setActiveElements([{ datasetIndex: 0, index: idx }]);
        donutChart.tooltip.setActiveElements([{ datasetIndex: 0, index: idx }], { x: 0, y: 0 });
        donutChart.update();
        document.getElementById('donutCenterNum').textContent = donutData[idx];
        document.getElementById('donutCenterNum').style.color = donutColors[idx];
        document.getElementById('donutCenterLbl').textContent = donutLabels[idx];
    }

    function resetDonut() {
        donutChart.setActiveElements([]);
        donutChart.tooltip.setActiveElements([], { x: 0, y: 0 });
        donutChart.update();
        document.getElementById('donutCenterNum').textContent = totalDonut;
        document.getElementById('donutCenterNum').style.color = 'var(--text)';
        document.getElementById('donutCenterLbl').textContent = 'total';
    }

    /* ── PRINT PDF ─────────────────────────────────────── */
    function printDashboard() {
        /* Saat print, tampilkan semua baris tabel */
        var allRows = document.querySelectorAll('.tbl-row');
        var prevDisplay = [];
        allRows.forEach(function(r, i) {
            prevDisplay[i] = r.style.display;
            r.style.display = '';
        });

        window.print();

        /* Kembalikan state setelah print */
        setTimeout(function() {
            allRows.forEach(function(r, i) {
                r.style.display = prevDisplay[i];
            });
        }, 500);
    }

    /* ── PAGINATION ENGINE ─────────────────────────────── */
    var PER_PAGE    = 30;
    var currentPage = 1;
    var filteredRows = [];

    function getFilteredRows() {
        var name = document.getElementById('searchName').value.toLowerCase().trim();
        var date = document.getElementById('filterDate').value;
        var ket  = document.getElementById('filterKet').value;
        return Array.from(document.querySelectorAll('.tbl-row')).filter(function(tr) {
            return (!name || tr.dataset.nama.includes(name))
                && (!date || tr.dataset.tanggal === date)
                && (!ket  || tr.dataset.ket === ket);
        });
    }

    function renderPage() {
        filteredRows = getFilteredRows();
        var total = filteredRows.length;
        var tp    = Math.max(1, Math.ceil(total / PER_PAGE));
        if (currentPage > tp) currentPage = tp;

        /* Sembunyikan semua baris */
        Array.from(document.querySelectorAll('.tbl-row')).forEach(function(tr) {
            tr.style.display = 'none';
        });

        /* Tampilkan slice halaman ini */
        var start = (currentPage - 1) * PER_PAGE;
        var end   = Math.min(start + PER_PAGE, total);
        filteredRows.slice(start, end).forEach(function(tr, idx) {
            tr.style.display = '';
            tr.style.animation = 'rowIn .3s ' + (idx * 0.025) + 's ease both';
            var noCell = tr.querySelector('.td-no');
            if (noCell) {
                var globalNo = start + idx + 1;
                noCell.textContent = String(globalNo).padStart(2, '0');
            }
        });

        /* Update result bar */
        var from = total === 0 ? 0 : start + 1;
        var to   = end;
        document.getElementById('shownFrom').textContent  = from;
        document.getElementById('shownTo').textContent    = to;
        document.getElementById('shownTotal').textContent = total;
        document.getElementById('countChip').textContent  = total + ' data';

        /* Empty state filter */
        var emptyRow = document.getElementById('emptyFilterRow');
        if (total === 0 && document.querySelectorAll('.tbl-row').length > 0) {
            if (!emptyRow) {
                var tr = document.createElement('tr');
                tr.id  = 'emptyFilterRow';
                tr.innerHTML = '<td colspan="7"><div class="empty"><div class="empty-icon">&#128269;</div><div class="empty-text">Data tidak ditemukan</div><div class="empty-sub">Coba ubah kata kunci atau filter</div></div></td>';
                document.getElementById('tableBody').appendChild(tr);
            }
        } else if (emptyRow) {
            emptyRow.remove();
        }

        renderPagination(tp, total);
    }

    function renderPagination(tp, total) {
        var wrap = document.getElementById('pgBtns');
        wrap.innerHTML = '';
        document.getElementById('pgInfo').textContent =
            total === 0 ? 'Tidak ada data' : 'Halaman ' + currentPage + ' dari ' + tp;
        if (total === 0) return;

        function mkBtn(label, page, disabled, active) {
            var b = document.createElement('button');
            b.className = 'pg-btn' + (active ? ' pg-active' : '');
            b.innerHTML = label;
            b.disabled  = disabled;
            b.onclick   = function() { currentPage = page; renderPage(); };
            wrap.appendChild(b);
        }

        function mkEllipsis() {
            var s = document.createElement('span');
            s.className   = 'pg-ellipsis';
            s.textContent = '…';
            wrap.appendChild(s);
        }

        mkBtn('&#8592;', currentPage - 1, currentPage === 1, false);

        var pages = [];
        if (tp <= 7) {
            for (var i = 1; i <= tp; i++) pages.push(i);
        } else {
            pages.push(1);
            if (currentPage > 3) pages.push('…');
            var lo = Math.max(2, currentPage - 1);
            var hi = Math.min(tp - 1, currentPage + 1);
            for (var i = lo; i <= hi; i++) pages.push(i);
            if (currentPage < tp - 2) pages.push('…');
            pages.push(tp);
        }

        pages.forEach(function(p) {
            if (p === '…') { mkEllipsis(); }
            else { mkBtn(p, p, false, p === currentPage); }
        });

        mkBtn('&#8594;', currentPage + 1, currentPage === tp, false);
    }

    /* ── FILTER ────────────────────────────────────────── */
    function filterTable() {
        currentPage = 1;
        renderPage();
    }

    function resetFilter() {
        document.getElementById('searchName').value = '';
        document.getElementById('filterDate').value = '';
        document.getElementById('filterKet').value  = '';
        filterTable();
    }

    /* ── PER PAGE ──────────────────────────────────────── */
    function changePerPage() {
        PER_PAGE    = parseInt(document.getElementById('perPageSel').value);
        currentPage = 1;
        renderPage();
    }

    /* ── INIT ──────────────────────────────────────────── */
    renderPage();
</script>
</body>
</html>