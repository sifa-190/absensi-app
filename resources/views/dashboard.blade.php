<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kipin — Dashboard Absensi</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&family=JetBrains+Mono:wght@400;600&display=swap" rel="stylesheet">
    <style>
        :root {
            --accent: #6366f1;
            --accent-light: rgba(99,102,241,0.15);
            --accent-muted: rgba(99,102,241,0.10);
            --green: #16a34a;
            --green-light: rgba(22,163,74,0.15);
            --amber: #d97706;
            --amber-light: rgba(217,119,6,0.15);
            --rose: #e11d48;
            --rose-light: rgba(225,29,72,0.15);
            --purple: #7c3aed;
            --purple-light: rgba(124,58,237,0.15);
            --teal: #0d9488;
            --sidebar-bg: #0f172a;
            --sidebar-border: rgba(255,255,255,0.08);

            --bg: #0f172a;
            --surface: #1e293b;
            --border: rgba(255,255,255,0.13);
            --border-md: rgba(255,255,255,0.22);
            --text: #f1f5f9;
            --text-2: #cbd5e1;
            --text-mute: #94a3b8;
            --text-light: #475569;

            --sidebar-w: 230px;
            --topbar-h: 56px;
            --radius: 12px;
            --radius-sm: 8px;
        }
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        html, body { height: 100%; }
        body {
            font-family: 'Inter', sans-serif;
            background: var(--bg);
            color: var(--text);
            min-height: 100vh;
            display: flex;
            overflow-x: hidden;
        }

        /* ═══════════════════════ LOADING ═══════════════════════ */
        .loading-overlay {
            display: none; position: fixed; inset: 0;
            background: rgba(244,246,251,0.94); backdrop-filter: blur(6px);
            z-index: 9999; flex-direction: column; align-items: center; justify-content: center; gap: 16px;
        }
        .loading-overlay.show { display: flex; }
        .spinner { width: 40px; height: 40px; border: 3px solid #e0e7ff; border-top-color: var(--accent); border-radius: 50%; animation: spin .7s linear infinite; }
        .loading-text { font-size: 13px; color: var(--text-mute); font-weight: 500; }

        /* ═══════════════════════ SIDEBAR ═══════════════════════ */
        .sidebar {
            width: var(--sidebar-w);
            height: 100vh;
            background: #0f172a;
            border-right: 2px solid rgba(99,102,241,0.85);
            box-shadow: 3px 0 3px rgba(99,102,241,0.8), 5px 0 50px rgba(99,102,241,0.4);
            display: flex;
            flex-direction: column;
            flex-shrink: 0;
            position: fixed;
            left: 0; top: 0;
            z-index: 100;
        }
        .sb-brand {
            padding: 20px 16px 16px;
            border-bottom: 1px solid rgba(255,255,255,0.08);
            display: flex; align-items: center; gap: 12px;
        }
        .sb-logo {
            width: 38px; height: 38px;
            background: var(--accent);
            border-radius: 10px;
            display: flex; align-items: center; justify-content: center;
            flex-shrink: 0;
        }
        .sb-logo img { width: 28px; height: 28px; object-fit: contain; }
        .sb-logo-fallback { font-size: 16px; font-weight: 600; color: #fff; }
        .sb-brand-text {}
        .sb-title { font-size: 14px; font-weight: 600; color: #f1f5f9; line-height: 1.2; }
        .sb-sub { font-size: 11px; color: #94a3b8; margin-top: 2px; }
        .sb-nav { flex: 1; overflow-y: auto; padding: 8px; }
        .sb-item.active {
         background: rgba(99,102,241,0.25);
         box-shadow: inset 3px 0 0 #818cf8, 0 1px 8px rgba(99,102,241,0.2);
        }
        .sb-section-label {
            font-size: 10px; font-weight: 600; color: #94a3b8;
            text-transform: uppercase; letter-spacing: .08em;
            padding: 12px 8px 5px;
        }
        .sb-item {
            display: flex; align-items: center; gap: 10px;
            padding: 9px 10px; border-radius: var(--radius-sm);
            cursor: pointer; transition: all .15s;
            margin-bottom: 1px; border: none; background: transparent;
            font-family: inherit; width: 100%; text-align: left;
        }
        .sb-item svg { width: 17px; height: 17px; stroke: var(--text-light); fill: none; stroke-width: 1.8; flex-shrink: 0; transition: stroke .15s; }
        .sb-item-label { font-size: 13px; font-weight: 500; color: #ffffff; transition: color .15s; flex: 1; }
        .sb-badge { font-size: 10px; background: var(--accent); color: #fff; padding: 1px 6px; border-radius: 20px; font-weight: 600; }
        .sb-item:hover { background: rgba(255,255,255,0.07); }
        .sb-item:hover svg { stroke: #e2e8f0; }
        .sb-item:hover .sb-item-label { color: #e2e8f0; }
        .sb-item.active { background: rgba(99,102,241,0.25); }
        .sb-item.active svg { stroke: #818cf8; }
        .sb-item.active .sb-item-label { color: #818cf8; font-weight: 600; }

        .sb-bottom {
        padding: 12px;
        border-top: 1px solid rgba(255,255,255,0.08);
        }
        .sb-user {
            display: flex; align-items: center; gap: 10px;
            padding: 8px; border-radius: var(--radius-sm);
        }
        .sb-avatar {
            width: 32px; height: 32px;
            border-radius: 8px; background: var(--accent-light);
            display: flex; align-items: center; justify-content: center;
            font-size: 12px; font-weight: 600; color: var(--accent); flex-shrink: 0;
        }
        .sb-uname { font-size: 12px; font-weight: 600; color: #f1f5f9; }
        .sb-urole { font-size: 11px; color: #94a3b8; }
        .sb-logout {
            margin-left: auto; background: none; border: none; cursor: pointer;
            padding: 5px; border-radius: 6px; color: var(--text-light); transition: all .15s;
        }
        .sb-logout:hover { background: var(--rose-light); color: var(--rose); }
        .sb-logout svg { width: 15px; height: 15px; stroke: currentColor; fill: none; stroke-width: 2; display: block; }

        /* ═══════════════════════ MAIN AREA ═══════════════════════ */
        .main-area {
            margin-left: var(--sidebar-w);
            flex: 1;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            min-width: 0;
            overflow-x: hidden;
        }

        /* ═══════════════════════ TOPBAR ═══════════════════════ */
        .topbar {
            min-height: var(--topbar-h);
            background: #1e293b;
            border-bottom: 1px solid rgba(255,255,255,0.07);
            display: flex; align-items: center; flex-wrap: wrap;
            padding: 8px 20px;
            gap: 10px;
            position: sticky; top: 0; z-index: 50;
            flex-shrink: 0;
        }
        .tb-breadcrumb { flex: 1; min-width: 120px; display: flex; align-items: center; gap: 6px; font-size: 13px; }
        .tb-bc-page { font-weight: 600; color: var(--text); white-space: nowrap; }
        .tb-bc-sep { color: var(--text-light); }
        .tb-bc-sub { color: var(--text-mute); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 200px; }
        .tb-right { display: flex; align-items: center; gap: 8px; flex-wrap: wrap; }
        .view-pills { display: flex; gap: 2px; background: #0f172a; border-radius: var(--radius-sm); padding: 3px; flex-wrap: nowrap; }
         .vp-btn {
        padding: 5px 12px; border-radius: 6px; font-size: 12px; font-weight: 500;
        border: none; background: transparent; color: #ffffff;
        cursor: pointer; font-family: inherit; transition: all .15s; white-space: nowrap;
        }
        .vp-btn.active { background: #6366f1; color: #ffffff; font-weight: 700; box-shadow: 0 2px 8px rgba(99,102,241,0.5); }
        .tb-period-selects { display: flex; gap: 6px; }
        .period-select {
            padding: 6px 28px 6px 10px; font-size: 12px; font-weight: 500;
            border: 1px solid rgba(255,255,255,0.12); border-radius: var(--radius-sm);
            background: #0f172a; color: #f1f5f9; font-family: inherit;
            outline: none; cursor: pointer; appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='10' height='10' viewBox='0 0 24 24' fill='none' stroke='%236b7280' stroke-width='2.5'%3E%3Cpolyline points='6 9 12 15 18 9'/%3E%3C/svg%3E");
            background-repeat: no-repeat; background-position: right 8px center;
        }
        .period-select:focus { border-color: var(--accent); }
        .tb-clock {
    font-family: 'JetBrains Mono', monospace; font-size: 12px;
    color: #ffffff; font-weight: 600; padding: 6px 14px;
    background: linear-gradient(135deg, #059669, #10b981);
    border-radius: var(--radius-sm);
    border: none;
    box-shadow: 0 2px 10px rgba(16,185,129,0.45);
    white-space: nowrap;
}
        .live-dot { display: inline-block; width: 6px; height: 6px; border-radius: 50%; background: #22c55e; margin-right: 6px; animation: pulse 2s infinite; vertical-align: middle; }

        /* ═══════════════════════ CONTENT ═══════════════════════ */
        .content {
            flex: 1; overflow-y: auto; overflow-x: hidden;
            padding: 20px;
            min-width: 0;
        }

        /* Prevent tables from blowing layout */
        .tbl-wrap { overflow-x: auto; max-width: 100%; }
        table { width: 100%; border-collapse: collapse; font-size: 13px; min-width: 480px; }

        /* ═══════════════════════ STAT CARDS ═══════════════════════ */
        .stat-grid { display: grid; grid-template-columns: repeat(5,1fr); gap: 12px; margin-bottom: 20px; }
      .stat-card {
    border-radius: var(--radius);
    padding: 16px;
    position: relative;
    overflow: hidden;
    transition: transform .2s, box-shadow .2s;
    box-shadow: 0 4px 16px rgba(0,0,0,0.4), inset 0 1px 0 rgba(255,255,255,0.15);
}
        .stat-card:hover { transform: translateY(-2px); box-shadow: 0 6px 20px rgba(0,0,0,0.07); }
        .sc-icon {
            width: 36px; height: 36px; border-radius: 10px;
            display: flex; align-items: center; justify-content: center; margin-bottom: 14px;
        }
        .sc-icon svg { width: 18px; height: 18px; fill: none; stroke-width: 1.8; }
        .sc-label { font-size: 11px; font-weight: 500; color: var(--text-mute); margin-bottom: 6px; }
        .sc-val { font-size: 28px; font-weight: 600; letter-spacing: -.03em; line-height: 1; }
        .sc-bar { position: absolute; bottom: 0; left: 0; right: 0; height: 3px; }
        /* Total — Biru Indigo */
.c-total { background: linear-gradient(135deg, #4f46e5, #6366f1); border: none; }
.c-total .sc-icon { background: rgba(255,255,255,0.2); }
.c-total .sc-icon svg { stroke: #fff; }
.c-total .sc-val { color: #fff; }
.c-total .sc-label { color: rgba(255,255,255,0.8); }
.c-total .sc-bar { background: rgba(255,255,255,0.3); }

/* Tepat Waktu — Hijau */
.c-tepat { background: linear-gradient(135deg, #15803d, #22c55e); border: none; }
.c-tepat .sc-icon { background: rgba(255,255,255,0.2); }
.c-tepat .sc-icon svg { stroke: #fff; }
.c-tepat .sc-val { color: #fff; }
.c-tepat .sc-label { color: rgba(255,255,255,0.8); }
.c-tepat .sc-bar { background: rgba(255,255,255,0.3); }

/* Terlambat — Amber/Orange */
.c-lambat { background: linear-gradient(135deg, #b45309, #f59e0b); border: none; }
.c-lambat .sc-icon { background: rgba(255,255,255,0.2); }
.c-lambat .sc-icon svg { stroke: #fff; }
.c-lambat .sc-val { color: #fff; }
.c-lambat .sc-label { color: rgba(255,255,255,0.8); }
.c-lambat .sc-bar { background: rgba(255,255,255,0.3); }

/* Pulang Cepat — Merah Rose */
.c-cepat { background: linear-gradient(135deg, #be123c, #f43f5e); border: none; }
.c-cepat .sc-icon { background: rgba(255,255,255,0.2); }
.c-cepat .sc-icon svg { stroke: #fff; }
.c-cepat .sc-val { color: #fff; }
.c-cepat .sc-label { color: rgba(255,255,255,0.8); }
.c-cepat .sc-bar { background: rgba(255,255,255,0.3); }

/* Tidak Masuk — Ungu */
.c-absent { background: linear-gradient(135deg, #6d28d9, #a855f7); border: none; }
.c-absent .sc-icon { background: rgba(255,255,255,0.2); }
.c-absent .sc-icon svg { stroke: #fff; }
.c-absent .sc-val { color: #fff; }
.c-absent .sc-label { color: rgba(255,255,255,0.8); }
.c-absent .sc-bar { background: rgba(255,255,255,0.3); }
        @keyframes statPop { 0%{transform:scale(.92);opacity:.4} 65%{transform:scale(1.04)} 100%{transform:scale(1);opacity:1} }
        .stat-val.pop { animation: statPop .28s ease; }

        /* ═══════════════════════ CARD ═══════════════════════ */
        .card {
    background: var(--surface);
    border: 1px solid var(--border-md);
    border-radius: var(--radius);
    margin-bottom: 16px;
    overflow: hidden;
    box-shadow: 0 2px 12px rgba(0,0,0,0.35), 0 0 0 1px rgba(255,255,255,0.05);
}
        .card-head {
            display: flex; align-items: center; gap: 10px;
            padding: 14px 18px; border-bottom: 1px solid var(--border);
            font-size: 13px; font-weight: 600; color: var(--text);
        }
        .card-head svg { width: 16px; height: 16px; stroke: var(--text-mute); fill: none; stroke-width: 1.8; }
        .card-chip {
            margin-left: auto; font-size: 11px; padding: 2px 10px;
            border-radius: 20px; background: var(--accent-light);
            color: var(--accent); border: 1px solid var(--accent-muted); font-weight: 600;
        }
        .card-body { padding: 16px 18px; }

        /* ═══════════════════════ ALERTS ═══════════════════════ */
        .alert-ok { background: var(--green-light); border: 1px solid #bbf7d0; color: #15803d; border-radius: var(--radius-sm); padding: 10px 14px; font-size: 13px; margin-bottom: 14px; font-weight: 500; }
        .alert-err { background: var(--rose-light); border: 1px solid #fecdd3; color: #be123c; border-radius: var(--radius-sm); padding: 10px 14px; font-size: 13px; margin-bottom: 14px; font-weight: 500; }

        /* ═══════════════════════ UPLOAD ═══════════════════════ */
        .upload-zone { display: flex; gap: 10px; align-items: center; flex-wrap: wrap; }
        .upload-input {
            flex: 1; min-width: 200px; padding: 9px 12px; font-size: 13px;
            border: 1.5px dashed rgba(99,102,241,0.3); border-radius: var(--radius-sm);
            background: var(--accent-light); color: var(--text); font-family: inherit;
        }
        .upload-input::file-selector-button {
            background: white; color: var(--accent); border: 1px solid rgba(99,102,241,0.25);
            padding: 4px 12px; border-radius: 6px; cursor: pointer; font-size: 12px;
            font-weight: 600; margin-right: 10px; font-family: inherit;
        }
        .btn-upload {
            padding: 9px 20px; font-size: 13px; font-weight: 600;
            background: var(--accent); color: #fff; border: none;
            border-radius: var(--radius-sm); cursor: pointer; font-family: inherit;
            display: flex; align-items: center; gap: 7px; white-space: nowrap;
            transition: background .15s;
        }
        .btn-upload:hover { background: #4f46e5; }
        .btn-upload svg { width: 14px; height: 14px; stroke: #fff; fill: none; stroke-width: 2.5; }

        /* ═══════════════════════ MAIN LAYOUT (CHART + TABLE) ═══════════════════════ */
        .dash-two-col { display: grid; grid-template-columns: 300px 1fr; gap: 16px; margin-bottom: 16px; }

        /* ═══════════════════════ DONUT ═══════════════════════ */
        .donut-inner { padding: 16px 18px; }
        .donut-canvas-wrap { position: relative; width: 180px; height: 180px; margin: 0 auto 16px; }
        .donut-center { position: absolute; top: 50%; left: 50%; transform: translate(-50%,-50%); text-align: center; }
        .donut-center-num { font-size: 26px; font-weight: 600; color: var(--text); line-height: 1; }
        .donut-center-lbl { font-size: 11px; color: var(--text-mute); margin-top: 2px; }
        .donut-legends { display: flex; flex-direction: column; gap: 7px; }
        .dl-item { display: flex; align-items: center; gap: 8px; padding: 8px 10px; border-radius: var(--radius-sm); cursor: pointer; transition: background .15s; }
        .dl-item:hover { background: rgba(255,255,255,0.05); }
        .dl-dot { width: 10px; height: 10px; border-radius: 3px; flex-shrink: 0; }
        .dl-label { font-size: 12px; color: #cbd5e1; flex: 1; }
        .dl-val { font-size: 13px; font-weight: 600; color: #f1f5f9; }
        .dl-pct { font-size: 10px; color: #94a3b8; margin-left: 3px; }

        /* ═══════════════════════ FILTER ═══════════════════════ */
        .filter-row { display: grid; grid-template-columns: 1fr 1fr 1fr auto; gap: 10px; align-items: end; }
        .f-group { display: flex; flex-direction: column; gap: 4px; }
        .f-label { font-size: 10px; font-weight: 600; color: var(--text-mute); text-transform: uppercase; letter-spacing: .07em; }
        .f-input {
            padding: 8px 10px; font-size: 13px; border: 1px solid rgba(255,255,255,0.10);
            border-radius: var(--radius-sm); background: #0f172a; color: #f1f5f9;
            font-family: inherit; outline: none; transition: border-color .15s;
        }
        .f-input:focus { border-color: var(--accent); }
        .btn-reset {
            padding: 8px 14px; font-size: 12px; font-weight: 500;
            background: var(--bg); color: var(--text-mute); border: 1px solid var(--border-md);
            border-radius: var(--radius-sm); cursor: pointer; font-family: inherit;
            white-space: nowrap; transition: all .15s;
        }
        .btn-reset:hover { border-color: var(--accent); color: var(--accent); background: var(--accent-light); }
        .btn-print {
            display: flex; align-items: center; gap: 7px; padding: 7px 16px;
            font-size: 12px; font-weight: 600; background: var(--bg); color: var(--text-2);
            border: 1px solid var(--border-md); border-radius: var(--radius-sm);
            cursor: pointer; font-family: inherit; transition: all .15s; white-space: nowrap;
        }
        .btn-print:hover { border-color: var(--accent); color: var(--accent); background: var(--accent-light); }
        .btn-print svg { width: 14px; height: 14px; stroke: currentColor; fill: none; stroke-width: 2; }

        /* ═══════════════════════ TABLE ═══════════════════════ */
        .karyawan-nav-strip {
            display: flex; align-items: center; gap: 6px;
            padding: 10px 18px; background: var(--bg); border-bottom: 1px solid var(--border);
            overflow-x: auto;
        }
        .karyawan-nav-label { font-size: 11px; font-weight: 600; color: var(--text-mute); white-space: nowrap; flex-shrink: 0; }
        .karyawan-nav-btn {
        padding: 4px 11px; border-radius: 20px; font-size: 11px; font-weight: 600;
        border: 1px solid rgba(255,255,255,0.15); background: rgba(255,255,255,0.06);
        color: #e2e8f0; cursor: pointer; font-family: inherit;
        white-space: nowrap; flex-shrink: 0; transition: all .15s;
        }
        .karyawan-nav-btn:hover { border-color: #818cf8; color: #fff; background: rgba(99,102,241,0.2); }
        .karyawan-nav-btn.active { background: var(--accent); color: #fff; border-color: var(--accent); font-weight: 700; box-shadow: 0 0 10px rgba(99,102,241,0.4); }

        .emp-table-header {
        display: flex; align-items: center; gap: 14px;
        padding: 14px 18px; background: linear-gradient(90deg, rgba(99,102,241,0.15) 0%, rgba(30,41,59,0.8) 100%);
        border-bottom: 1px solid rgba(99,102,241,0.2);
        border-left: 3px solid #6366f1;
        }
        .emp-table-avatar { width: 48px; height: 48px; border-radius: 12px; color: #fff; display: flex; align-items: center; justify-content: center; font-size: 16px; font-weight: 600; flex-shrink: 0; }
        .emp-table-name { font-size: 15px; font-weight: 600; color: var(--text); }
        .emp-table-meta { display: flex; align-items: center; gap: 8px; margin-top: 4px; flex-wrap: wrap; }
        .emp-table-pin { font-family: 'JetBrains Mono', monospace; font-size: 11px; font-weight: 600; background: var(--accent); color: #fff; padding: 2px 8px; border-radius: 5px; }
        .emp-table-days { font-size: 12px; color: var(--text-mute); }
        .emp-table-stats { display: flex; gap: 6px; flex-wrap: wrap; margin-left: auto; }
        .esp { display: flex; align-items: center; gap: 4px; padding: 4px 10px; border-radius: 20px; font-size: 11px; font-weight: 600; }
        .esp-tepat  { background: rgba(34,197,94,0.2);  color: #4ade80; border: 1px solid rgba(34,197,94,0.3); }
        .esp-lambat { background: rgba(245,158,11,0.2); color: #fbbf24; border: 1px solid rgba(245,158,11,0.3); }
        .esp-cepat  { background: rgba(244,63,94,0.2);  color: #fb7185; border: 1px solid rgba(244,63,94,0.3); }
        .esp-absent { background: rgba(168,85,247,0.2); color: #c084fc; border: 1px solid rgba(168,85,247,0.3); }

        .tbl-wrap { overflow-x: auto; }
        table { width: 100%; border-collapse: collapse; font-size: 13px; }
        thead tr { background: #0f172a; }
        th { padding: 10px 16px; text-align: left; font-size: 10px; font-weight: 700; color: #ffffff; border-bottom: 1px solid rgba(255,255,255,0.15); letter-spacing: .06em; text-transform: uppercase; }
        td { padding: 11px 16px; border-bottom: 1px solid rgba(255,255,255,0.08); vertical-align: middle; color: #ffffff; }
        tbody tr:last-child td { border-bottom: none; }
        tbody tr:hover td { background: rgba(99,102,241,0.08); }
        tbody tr.row-absent td { background: rgba(124,58,237,0.08); color: #cbd5e1; }
        /* ═══ Nama Hari badges ═══ */
        .date-day-badge { display: inline-flex; align-items: center; padding: 1px 8px; border-radius: 20px; font-size: 10px; font-weight: 600; letter-spacing: .04em; }
        .day-senin   { background: #eef2ff; color: #4338ca; border: 1px solid #c7d2fe; }
        .day-selasa  { background: #f0fdf4; color: #15803d; border: 1px solid #bbf7d0; }
        .day-rabu    { background: #fffbeb; color: #92400e; border: 1px solid #fde68a; }
        .day-kamis   { background: #fff1f2; color: #be123c; border: 1px solid #fecdd3; }
        .day-jumat   { background: #f5f3ff; color: #6d28d9; border: 1px solid #ddd6fe; }
        .day-sabtu   { background: #ecfeff; color: #0e7490; border: 1px solid #a5f3fc; }
        .day-minggu  { background: #fff7ed; color: #c2410c; border: 1px solid #fed7aa; }

        .emp-avatar-sm { width: 30px; height: 30px; border-radius: 8px; color: #fff; display: inline-flex; align-items: center; justify-content: center; font-size: 10px; font-weight: 600; flex-shrink: 0; }
        .emp-name-cell { display: flex; align-items: center; gap: 9px; }
        .emp-name-text { font-size: 13px; font-weight: 500; color: var(--text); }
        .emp-pin-text { font-family: 'JetBrains Mono', monospace; font-size: 10px; color: var(--text-mute); }

        .time-badge { display: inline-flex; align-items: center; gap: 5px; padding: 4px 9px; border-radius: 6px; font-family: 'JetBrains Mono', monospace; font-size: 12px; font-weight: 500; white-space: nowrap; }
        .time-badge svg { width: 11px; height: 11px; flex-shrink: 0; fill: none; stroke-width: 2; }
        .time-ok   { background: rgba(34,197,94,0.2); color: #4ade80; border: 1px solid rgba(34,197,94,0.3); } .time-ok svg { stroke: #4ade80; }
        .time-late { background: rgba(245,158,11,0.2); color: #fbbf24; border: 1px solid rgba(245,158,11,0.3); } .time-late svg { stroke: #fbbf24; }
        .time-early{ background: rgba(244,63,94,0.2); color: #fb7185; border: 1px solid rgba(244,63,94,0.3); } .time-early svg { stroke: #fb7185; }
        .time-none { font-size: 11px; color: var(--text-light); font-style: italic; }
        .badge-absent { display: inline-flex; align-items: center; gap: 5px; padding: 4px 9px; border-radius: 6px; background: rgba(168,85,247,0.2); color: #c084fc; border: 1px solid rgba(168,85,247,0.4); font-size: 12px; font-weight: 500; }
        .status-pill { font-size: 11px; font-weight: 600; padding: 3px 9px; border-radius: 20px; }
        .sp-tepat  { background: rgba(34,197,94,0.2);  color: #4ade80; border: 1px solid rgba(34,197,94,0.4); }
        .sp-lambat { background: rgba(245,158,11,0.2); color: #fbbf24; border: 1px solid rgba(245,158,11,0.4); }
        .sp-cepat  { background: rgba(244,63,94,0.2);  color: #fb7185; border: 1px solid rgba(244,63,94,0.4); }
        .sp-absent { background: rgba(168,85,247,0.2); color: #c084fc; border: 1px solid rgba(168,85,247,0.4); }

        /* ═══════════════════════ PAGINATION ═══════════════════════ */
        .pg-bar { display: flex; align-items: center; justify-content: space-between; padding: 10px 18px; flex-wrap: wrap; gap: 8px; border-top: 1px solid var(--border); background: var(--bg); }
        .pg-info { font-size: 12px; color: var(--text-mute); }
        .pg-btns { display: flex; gap: 3px; flex-wrap: wrap; }
        .pg-btn { min-width: 30px; height: 30px; padding: 0 6px; border: 1px solid var(--border-md); background: var(--surface); color: var(--text); border-radius: 7px; font-size: 12px; font-weight: 500; cursor: pointer; display: flex; align-items: center; justify-content: center; font-family: inherit; transition: all .15s; }
        .pg-btn:hover:not(:disabled) { border-color: var(--accent); color: var(--accent); }
        .pg-btn:disabled { opacity: .35; cursor: not-allowed; }
        .pg-btn.pg-active { background: var(--accent); color: #fff; border-color: var(--accent); font-weight: 600; }
        .pg-ellipsis { font-size: 13px; padding: 0 3px; color: var(--text-light); }

        /* ═══════════════════════ PERFORMA PAGE ═══════════════════════ */
        .page { display: none; } .page.active { display: block; }

        .emp-selector-grid { display: grid; grid-template-columns: repeat(auto-fill,minmax(200px,1fr)); gap: 10px; padding: 14px 18px; }
        .emp-card {
            background: var(--bg); border: 1.5px solid var(--border);
            border-radius: var(--radius); padding: 14px; cursor: pointer; transition: all .2s; position: relative; overflow: hidden;
        }
        .emp-card:hover { border-color: var(--accent); background: var(--surface); box-shadow: 0 4px 16px rgba(99,102,241,0.1); }
        .emp-card.selected { border-color: var(--accent); background: var(--surface); }
        .emp-card.selected::before { content: ''; position: absolute; top: 0; left: 0; right: 0; height: 3px; background: var(--accent); }
        .emp-card-badge { position: absolute; top: 10px; right: 10px; width: 18px; height: 18px; border-radius: 50%; background: var(--accent); display: none; align-items: center; justify-content: center; }
        .emp-card.selected .emp-card-badge { display: flex; }
        .emp-card-badge svg { width: 10px; height: 10px; stroke: #fff; fill: none; stroke-width: 3; }
        .emp-card-header { display: flex; align-items: center; gap: 10px; margin-bottom: 12px; }
        .emp-card-avatar { width: 38px; height: 38px; border-radius: 10px; color: #fff; display: flex; align-items: center; justify-content: center; font-size: 13px; font-weight: 600; flex-shrink: 0; }
        .emp-card-name { font-size: 13px; font-weight: 600; color: var(--text); overflow: hidden; text-overflow: ellipsis; white-space: nowrap; max-width: 120px; }
        .emp-card-pin { font-family: 'JetBrains Mono', monospace; font-size: 10px; color: var(--text-mute); margin-top: 2px; }
        .emp-card-stats { display: grid; grid-template-columns: repeat(4,1fr); gap: 4px; }
        .emp-card-stat { text-align: center; padding: 6px 2px; border-radius: 7px; background: rgba(255,255,255,0.7); border: 1px solid rgba(0,0,0,0.04); }
        .emp-card-stat-num { font-size: 14px; font-weight: 600; }
        .emp-card-stat-lbl { font-size: 8px; font-weight: 600; color: var(--text-mute); text-transform: uppercase; letter-spacing: .04em; margin-top: 1px; }

        .chart-panel { display: none; } .chart-panel.show { display: block; }
        .bar-chart-topbar { display: flex; align-items: center; gap: 12px; padding: 14px 18px; border-bottom: 1px solid var(--border); flex-wrap: wrap; }
        .bar-chart-emp-info { display: flex; align-items: center; gap: 12px; flex: 1; }
        .bar-chart-emp-avatar { width: 40px; height: 40px; border-radius: 10px; color: #fff; display: flex; align-items: center; justify-content: center; font-size: 14px; font-weight: 600; flex-shrink: 0; }
        .bar-chart-emp-name { font-size: 15px; font-weight: 600; color: var(--text); }
        .bar-chart-emp-pin { font-family: 'JetBrains Mono', monospace; font-size: 11px; color: var(--text-mute); margin-top: 2px; }
        .bar-mode-btns { display: flex; gap: 5px; }
        .bar-mode-btn { padding: 6px 14px; border-radius: var(--radius-sm); font-size: 11px; font-weight: 500; border: 1px solid var(--border-md); background: var(--bg); color: var(--text-mute); cursor: pointer; font-family: inherit; transition: all .15s; }
        .bar-mode-btn:hover { border-color: var(--accent); color: var(--accent); }
        .bar-mode-btn.active { background: var(--accent); color: #fff; border-color: var(--accent); }
        .bar-chart-body { padding: 16px 18px 12px; }
        .bar-chart-canvas-wrap { position: relative; height: 360px; }
        .chart-legend-custom { display: flex; flex-wrap: wrap; gap: 10px; padding: 8px 18px 12px; }
        .cl-item { display: flex; align-items: center; gap: 5px; font-size: 11px; color: var(--text-mute); }
        .cl-dot { width: 10px; height: 10px; border-radius: 3px; flex-shrink: 0; }
        .cl-line { width: 16px; height: 3px; border-radius: 2px; flex-shrink: 0; }

        .yearly-stat-strip { display: flex; gap: 8px; padding: 10px 18px 14px; flex-wrap: wrap; border-top: 1px solid var(--border); }
        .ys-pill { display: flex; align-items: center; gap: 10px; padding: 10px 14px; border-radius: var(--radius-sm); flex: 1; min-width: 100px; border: 1px solid transparent; }
        .ys-hadir { background: rgba(34,197,94,0.15); border-color: rgba(34,197,94,0.3); } .ys-hadir .ys-val { color: #4ade80; }
        .ys-lambat { background: rgba(245,158,11,0.15); border-color: rgba(245,158,11,0.3); } .ys-lambat .ys-val { color: #fbbf24; }
        .ys-cepat { background: rgba(244,63,94,0.15); border-color: rgba(244,63,94,0.3); } .ys-cepat .ys-val { color: #fb7185; }
        .ys-tepat { background: rgba(99,102,241,0.15); border-color: rgba(99,102,241,0.3); } .ys-tepat .ys-val { color: #818cf8; }
        .ys-absent { background: rgba(168,85,247,0.15); border-color: rgba(168,85,247,0.3); } .ys-absent .ys-val { color: #c084fc; }
        .ys-val { font-size: 20px; font-weight: 600; line-height: 1; }
        .ys-lbl { font-size: 11px; color: #94a3b8; margin-top: 2px; }
        .chart-select-hint { display: flex; flex-direction: column; align-items: center; justify-content: center; padding: 3rem 2rem; gap: 10px; }
        .csh-icon { width: 56px; height: 56px; border-radius: 14px; background: var(--accent-light); display: flex; align-items: center; justify-content: center; }
        .csh-icon svg { width: 26px; height: 26px; stroke: var(--accent); fill: none; stroke-width: 1.5; }
        .csh-text { font-size: 14px; font-weight: 500; color: var(--text-mute); }
        .csh-sub { font-size: 12px; color: var(--text-light); }

        /* ═══════════════════════ EMPTY ═══════════════════════ */
        .empty { text-align: center; padding: 3rem 1rem; }
        .empty-icon { font-size: 36px; margin-bottom: 12px; }
        .empty-text { font-size: 13px; font-weight: 500; color: var(--text-mute); }
        .empty-sub { font-size: 12px; color: var(--text-light); margin-top: 5px; }

        /* ═══════════════════════ UPLOAD PAGE ═══════════════════════ */
       .upload-page { max-width: 100%; }
        .file-info { font-size: 11px; color: var(--text-mute); margin-top: 8px; display: none; }

        /* ═══════════════════════ ANIMATIONS ═══════════════════════ */
        @keyframes spin { to { transform: rotate(360deg); } }
        @keyframes pulse { 0%,100%{opacity:1}50%{opacity:.3} }
        @keyframes rowIn { from{opacity:0;transform:translateY(5px)}to{opacity:1;transform:translateY(0)} }
        @keyframes fadeIn { from{opacity:0}to{opacity:1} }

        /* ═══════════════════════ PRINT ═══════════════════════ */
        @media print {
            /* Sembunyikan semua kecuali area cetak */
            body > * { display: none !important; }
            #printArea { display: block !important; }
            @page { margin: 1.5cm; size: A4; }
        }

        /* ═══════════════════════ RESPONSIVE ═══════════════════════ */

        /* Laptop besar (1280px+): sidebar penuh, 5 kolom stat */

        /* Laptop kecil/medium (1024px-1279px): sidebar lebih sempit */
        @media (max-width: 1279px) {
            :root { --sidebar-w: 200px; }
            .stat-grid { grid-template-columns: repeat(3,1fr); }
        }

        /* Tablet landscape / laptop kecil (900px-1023px): sidebar icon only */
        @media (max-width: 1023px) {
            :root { --sidebar-w: 60px; }
            .sb-brand { padding: 14px 10px; justify-content: center; }
            .sb-brand-text { display: none; }
            .sb-logo { margin-bottom: 0; }
            .sb-section-label { display: none; }
            .sb-item { justify-content: center; padding: 10px; margin-bottom: 2px; }
            .sb-item-label { display: none; }
            .sb-badge { display: none; }
            .sb-bottom { padding: 8px; }
            .sb-uname, .sb-urole { display: none; }
            .sb-user { justify-content: center; padding: 6px; }
            .sb-avatar { flex-shrink: 0; }
            .sb-logout { margin-left: 0; }
            .stat-grid { grid-template-columns: repeat(3,1fr); }
            .dash-two-col { grid-template-columns: 1fr; }
            .content { padding: 16px; }
        }

        /* Tablet portrait (768px-899px) */
        @media (max-width: 899px) {
            .stat-grid { grid-template-columns: repeat(2,1fr); }
            .filter-row { grid-template-columns: 1fr 1fr; }
            .tb-period-selects { display: none; }
        }

        /* Mobile + hamburger (max 767px): sidebar hidden, hamburger muncul */
        @media (max-width: 767px) {
            :root { --sidebar-w: 0px; }
            .sidebar {
                transform: translateX(-230px);
                width: 230px;
                transition: transform .25s ease;
                z-index: 200;
            }
            .sidebar.open { transform: translateX(0); box-shadow: 4px 0 24px rgba(0,0,0,0.12); }
            .sidebar.open .sb-brand-text,
            .sidebar.open .sb-section-label,
            .sidebar.open .sb-item-label,
            .sidebar.open .sb-badge,
            .sidebar.open .sb-uname,
            .sidebar.open .sb-urole { display: block; }
            .sidebar.open .sb-item { justify-content: flex-start; padding: 9px 10px; }
            .sidebar.open .sb-user { justify-content: flex-start; }
            .sidebar.open .sb-logout { margin-left: auto; }
            .main-area { margin-left: 0; }
            .hamburger { display: flex !important; }
            .stat-grid { grid-template-columns: repeat(2,1fr); }
            .filter-row { grid-template-columns: 1fr; }
            .topbar { padding: 8px 14px; }
            .content { padding: 12px; }
            .dash-two-col { grid-template-columns: 1fr; }
            .view-pills .vp-btn { padding: 4px 8px; font-size: 11px; }
            .tb-bc-sub { display: none; }
            .tb-bc-sep { display: none; }
        }

        /* Overlay saat sidebar mobile terbuka */
        .sidebar-overlay {
            display: none; position: fixed; inset: 0;
            background: rgba(0,0,0,0.3); z-index: 199;
        }
        .sidebar-overlay.show { display: block; }

        /* Hamburger button */
        .hamburger {
            display: none; align-items: center; justify-content: center;
            width: 36px; height: 36px; border: none; background: transparent;
            cursor: pointer; border-radius: var(--radius-sm); color: var(--text-mute);
            flex-shrink: 0; transition: background .15s;
        }
        .hamburger:hover { background: var(--bg); }
        .hamburger svg { width: 20px; height: 20px; stroke: currentColor; fill: none; stroke-width: 2; }
    </style>
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
        <!-- Hamburger (mobile only) -->
        <button class="hamburger" id="hamburgerBtn" onclick="toggleSidebar()" aria-label="Toggle menu">
            <svg viewBox="0 0 24 24"><line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="18" x2="21" y2="18"/></svg>
        </button>
        <div class="tb-breadcrumb">
            <span class="tb-bc-page" id="tbPageName">Dashboard</span>
            <span class="tb-bc-sep">/</span>
            <span class="tb-bc-sub" id="tbPageSub">Ringkasan</span>
        </div>
        <div class="tb-right">
            <!-- Period filter (hanya tampil di dashboard & data) -->
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
            <script>
                setTimeout(function(){
                    ['alertSuccess','alertError'].forEach(function(id){
                        var el=document.getElementById(id);
                        if(el){el.style.transition='opacity .6s';el.style.opacity='0';setTimeout(function(){el.remove();},600);}
                    });
                },8000);
            </script>

            <!-- STAT CARDS -->
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

            <!-- DONUT + SUMMARY -->
            <div class="dash-two-col">
                <!-- Donut -->
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

                <!-- Right: filter + table mini -->
                <div style="display:flex;flex-direction:column;gap:14px">

                    <!-- Quick stats table -->
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

            <!-- Filter -->
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
                <form action="{{ route('import.presensi') }}" method="POST" enctype="multipart/form-data" id="uploadForm" onsubmit="document.getElementById('loadingOverlay').classList.add('show')">
                    @csrf
                    <input type="file" id="fileInputHidden" name="file_csv[]" multiple accept=".csv" style="display:none" onchange="handleFileSelect(this)">

                    <!-- DROP ZONE -->
                    <div id="dropZone" onclick="document.getElementById('fileInputHidden').click()"
                        style="border: 2px dashed rgba(99,102,241,0.4); border-radius: 16px; padding: 48px 24px;
                               text-align: center; cursor: pointer; transition: all .25s;
                               background: rgba(99,102,241,0.05); margin-bottom: 20px; position: relative;">
                        <div id="dropIcon" style="width:64px;height:64px;border-radius:16px;background:rgba(99,102,241,0.15);display:flex;align-items:center;justify-content:center;margin:0 auto 16px;">
                            <svg viewBox="0 0 24 24" width="28" height="28" fill="none" stroke="#818cf8" stroke-width="1.8">
                                <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
                                <polyline points="17 8 12 3 7 8"/>
                                <line x1="12" y1="3" x2="12" y2="15"/>
                            </svg>
                        </div>
                        <div style="font-size:15px;font-weight:600;color:#e2e8f0;margin-bottom:6px">Drag & drop file CSV di sini</div>
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

        <!-- INFO CARD -->
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
</div>

        <div style="text-align:center;padding:1rem 0 .5rem;font-size:12px;color:var(--text-light)">
            &copy; {{ date('Y') }} <span style="color:var(--accent);font-weight:600">Kipin</span> &mdash; Sistem Monitoring Absensi &middot; Sakera
        </div>

    </div><!-- /content -->
</div><!-- /main-area -->

<!-- RAW DATA -->
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

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2.2.0/dist/chartjs-plugin-datalabels.min.js"></script>
<script>
/* ══ KONSTANTA ══ */
var NAMA_BULAN   = ['','Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
var NAMA_BULAN_S = ['','Jan','Feb','Mar','Apr','Mei','Jun','Jul','Ags','Sep','Okt','Nov','Des'];
var NAMA_HARI    = ['Minggu','Senin','Selasa','Rabu','Kamis','Jumat','Sabtu'];
var HARI_CSS     = ['day-minggu','day-senin','day-selasa','day-rabu','day-kamis','day-jumat','day-sabtu'];
var DONUT_COLORS = ['#16a34a','#d97706','#e11d48','#6366f1','#7c3aed'];
var DONUT_LABELS = ['Tepat Waktu','Terlambat','Pulang Cepat','Tepat Pulang','Tidak Masuk'];
var DONUT_BGS    = ['#dcfce7','#fef9c3','#ffe4e6','#eef2ff','#f3e8ff'];
var DONUT_TXT    = ['#15803d','#92400e','#be123c','#4338ca','#6d28d9'];
var BC = {
    hadir:  { hex:'#6366f1', bg:'rgba(99,102,241,0.70)',  border:'#6366f1', label:'Total Hadir',    type:'bar'  },
    tepat:  { hex:'#16a34a', bg:'rgba(22,163,74,0.70)',   border:'#16a34a', label:'Tepat Waktu',    type:'bar'  },
    absent: { hex:'#7c3aed', bg:'rgba(124,58,237,0.70)',  border:'#7c3aed', label:'Tidak Masuk',    type:'bar'  },
    lambat: { hex:'#d97706', bg:'rgba(217,119,6,0.80)',   border:'#d97706', label:'Terlambat',      type:'bar'  },
    cepat:  { hex:'#e11d48', bg:'rgba(225,29,72,0.80)',   border:'#e11d48', label:'Pulang Cepat',   type:'bar'  },
    tren:   { hex:'#0d9488', bg:'rgba(13,148,136,0.10)',  border:'#0d9488', label:'Tren Kehadiran', type:'line' }
};
var PALETTE = ['#6366f1','#0d9488','#d97706','#e11d48','#16a34a','#7c3aed','#0891b2','#dc2626','#65a30d','#9333ea','#2563eb','#ea580c','#059669','#be185d','#ca8a04'];
var MALE_NAMES = ['adi','agustinus','bhirawa','ryo','ridwan','imam'];
function isMale(nama){ return MALE_NAMES.indexOf(nama.toLowerCase().trim()) !== -1; }
function avatarHTML(nama, color, size, fontSize){
    var w=size||38;
    var svgMale='<svg viewBox="0 0 24 24" width="'+(w*0.62)+'" height="'+(w*0.62)+'" fill="white" xmlns="http://www.w3.org/2000/svg">'
        +'<circle cx="12" cy="7" r="4"/>'
        +'<path d="M4 21v-1a8 8 0 0 1 16 0v1"/>'
        +'</svg>';
    var svgFemale='<svg viewBox="0 0 24 24" width="'+(w*0.62)+'" height="'+(w*0.62)+'" fill="white" xmlns="http://www.w3.org/2000/svg">'
        +'<circle cx="12" cy="6" r="3.5"/>'
        +'<path d="M12 11c-4 0-7 2.5-7 5.5V21h14v-4.5c0-3-3-5.5-7-5.5z"/>'
        +'<path d="M9 11.5 Q12 14 15 11.5" stroke="rgba(255,255,255,0.4)" stroke-width="1" fill="none"/>'
        +'<line x1="10" y1="13" x2="9" y2="17" stroke="rgba(255,255,255,0.3)" stroke-width="1"/>'
        +'<line x1="14" y1="13" x2="15" y2="17" stroke="rgba(255,255,255,0.3)" stroke-width="1"/>'
        +'</svg>';
    var bg = isMale(nama) ? 'linear-gradient(135deg,#2563eb,#6366f1)' : 'linear-gradient(135deg,#be185d,#ec4899)';
    return '<div style="width:'+w+'px;height:'+w+'px;border-radius:10px;background:'+bg+';display:flex;align-items:center;justify-content:center;flex-shrink:0">'
        +(isMale(nama)?svgMale:svgFemale)
        +'</div>';
}
/* ══ PARSE RAW DATA ══ */
var RAW_DATA = [];
try { RAW_DATA = JSON.parse(document.getElementById('rawDataScript').textContent); } catch(e){}

/* ══ STATE ══ */
var viewMode         = 'bulanan';
var currentEmpPage   = 1;
var donutChart       = null;
var empBarChart      = null;
var selectedEmpPin   = null;
var selectedEmpColor = '#6366f1';
var barMode          = 'all';
var currentPage      = 'dashboard';

/* ══ SIDEBAR TOGGLE (mobile) ══ */
function toggleSidebar(){
    var sb=document.getElementById('sidebar');
    var ov=document.getElementById('sidebarOverlay');
    sb.classList.toggle('open');
    ov.classList.toggle('show');
}
function closeSidebarMobile(){
    if(window.innerWidth<=767){
        document.getElementById('sidebar').classList.remove('open');
        document.getElementById('sidebarOverlay').classList.remove('show');
    }
}

/* ══ CLOCK ══ */
function tick(){
    var d=new Date();
    document.getElementById('liveClock').textContent=d.toLocaleTimeString('id-ID',{hour:'2-digit',minute:'2-digit',second:'2-digit'});
}
tick(); setInterval(tick,1000);

function showFileName(input){}
function handleFileSelect(input){
    if(!input.files||!input.files.length) return;
    renderFileList(input.files);
}
var selectedFiles = [];

function renderFileList(files){
    for(var i=0;i<files.length;i++) selectedFiles.push(files[i]);
    updateFileListUI();
}

function removeFile(idx){
    selectedFiles.splice(idx,1);
    updateFileListUI();
}

function updateFileListUI(){
    var list=document.getElementById('fileList');
    var btn=document.getElementById('btnSubmitUpload');

    if(!selectedFiles.length){
        list.style.display='none';
        list.innerHTML='';
        btn.disabled=true;
        btn.style.opacity='0.4';
        btn.style.cursor='not-allowed';

        // reset input file juga
        var fi=document.getElementById('fileInputHidden');
        fi.value='';
        return;
    }

    var html='<div style="font-size:11px;font-weight:600;color:#64748b;text-transform:uppercase;letter-spacing:.07em;margin-bottom:8px">File dipilih ('+selectedFiles.length+')</div>';
    for(var i=0;i<selectedFiles.length;i++){
        var f=selectedFiles[i];
        var size=f.size>1024*1024?(f.size/1024/1024).toFixed(1)+' MB':(Math.round(f.size/1024))+' KB';
        html+='<div style="display:flex;align-items:center;gap:10px;padding:10px 14px;border-radius:8px;background:rgba(99,102,241,0.1);border:1px solid rgba(99,102,241,0.2);margin-bottom:6px">'
            +'<svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="#818cf8" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>'
            +'<span style="font-size:12px;font-weight:500;color:#e2e8f0;flex:1">'+f.name+'</span>'
            +'<span style="font-size:11px;color:#64748b;margin-right:6px">'+size+'</span>'
            +'<button onclick="removeFile('+i+')" type="button" '
            +'style="width:20px;height:20px;border-radius:50%;background:rgba(244,63,94,0.2);border:1px solid rgba(244,63,94,0.3);'
            +'display:flex;align-items:center;justify-content:center;cursor:pointer;flex-shrink:0;transition:all .15s;"'
            +'onmouseover="this.style.background=\'rgba(244,63,94,0.4)\'" onmouseout="this.style.background=\'rgba(244,63,94,0.2)\'">'
            +'<svg viewBox="0 0 24 24" width="10" height="10" fill="none" stroke="#fb7185" stroke-width="3"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>'
            +'</button>'
            +'</div>';
    }
    list.style.display='block';
    list.innerHTML=html;
    btn.disabled=false;
    btn.style.opacity='1';
    btn.style.cursor='pointer';

    // sync ke input file pakai DataTransfer
    var dt=new DataTransfer();
    selectedFiles.forEach(function(f){dt.items.add(f);});
    document.getElementById('fileInputHidden').files=dt.files;
}
// Drag & drop
document.addEventListener('DOMContentLoaded',function(){
    var dz=document.getElementById('dropZone');
    if(!dz) return;
    dz.addEventListener('dragover',function(e){
        e.preventDefault();
        dz.style.borderColor='#6366f1';
        dz.style.background='rgba(99,102,241,0.12)';
    });
    dz.addEventListener('dragleave',function(){
        dz.style.borderColor='rgba(99,102,241,0.4)';
        dz.style.background='rgba(99,102,241,0.05)';
    });
    dz.addEventListener('drop',function(e){
        e.preventDefault();
        dz.style.borderColor='rgba(99,102,241,0.4)';
        dz.style.background='rgba(99,102,241,0.05)';
        var fi=document.getElementById('fileInputHidden');
        fi.files=e.dataTransfer.files;
        renderFileList(e.dataTransfer.files);
    });
});

/* ══ INIT ══ */
(function init(){
    var now=new Date(),nowM=now.getMonth()+1,nowY=now.getFullYear();
    ['filterTahun','filterTahunPerf'].forEach(function(id){
        var sel=document.getElementById(id); if(!sel) return;
        var years={}; years[nowY]=true;
        RAW_DATA.forEach(function(r){years[r.tahun]=true;});
        Object.keys(years).sort(function(a,b){return b-a;}).forEach(function(y){
            var o=document.createElement('option'); o.value=y; o.textContent=y;
            if(parseInt(y)===nowY) o.selected=true; sel.appendChild(o);
        });
    });
    var bestMonth=nowM;
    if(RAW_DATA.length>0){
        var mc={};
        RAW_DATA.forEach(function(r){if(r.tahun===nowY)mc[r.bulan]=(mc[r.bulan]||0)+1;});
        var best=0;
        Object.keys(mc).forEach(function(m){if(mc[m]>best){best=mc[m];bestMonth=parseInt(m);}});
        if(best===0){RAW_DATA.forEach(function(r){mc[r.bulan]=(mc[r.bulan]||0)+1;});Object.keys(mc).forEach(function(m){if(mc[m]>best){best=mc[m];bestMonth=parseInt(m);}});}
    }
    document.getElementById('filterBulan').value=bestMonth;
    document.getElementById('filterBulanPerf').value=bestMonth;

    // badge count
    document.getElementById('sbDataBadge').textContent=RAW_DATA.length;

    initDonut();
    onPeriodChange();
})();

/* ══ PAGE SWITCH ══ */
function switchPage(p){
    closeSidebarMobile();
    currentPage=p;
    ['pageDashboard','pageData','pagePerforma','pageUpload'].forEach(function(id){
        document.getElementById(id).classList.remove('active');
    });
    ['sbDashboard','sbData','sbPerforma','sbUpload'].forEach(function(id){
        var el=document.getElementById(id); if(el) el.classList.remove('active');
    });
    var pid='page'+p.charAt(0).toUpperCase()+p.slice(1);
    var sid='sb'+p.charAt(0).toUpperCase()+p.slice(1);
    document.getElementById(pid).classList.add('active');
    var sbEl=document.getElementById(sid); if(sbEl) sbEl.classList.add('active');

    var names={dashboard:'Dashboard',data:'Data Presensi',performa:'Chart Performa',upload:'Upload CSV'};
    var subs={dashboard:'Ringkasan',data:'Tabel lengkap',performa:'Analisis karyawan',upload:'Import data CSV'};
    document.getElementById('tbPageName').textContent=names[p]||p;
    document.getElementById('tbPageSub').textContent=subs[p]||'';

    var showPeriod=(p==='dashboard'||p==='data');
    document.getElementById('topbarPeriod').style.display=showPeriod?'flex':'none';

    if(p==='performa') renderPerfPage();
    if(p==='data'){ applyFiltersAndRender(); }
}

/* ══ HELPER — nama hari ══ */
function getNamaHari(tglStr){
    var parts=tglStr.split('-');
    return NAMA_HARI[new Date(parseInt(parts[0]),parseInt(parts[1])-1,parseInt(parts[2])).getDay()];
}
function getHariCssClass(tglStr){
    var parts=tglStr.split('-');
    return HARI_CSS[new Date(parseInt(parts[0]),parseInt(parts[1])-1,parseInt(parts[2])).getDay()];
}

/* ══ DONUT ══ */
function initDonut(){
    var ctx=document.getElementById('donutChart').getContext('2d');
    donutChart=new Chart(ctx,{
        type:'doughnut',
        data:{labels:DONUT_LABELS,datasets:[{data:[0,0,0,0,0],backgroundColor:DONUT_COLORS,borderColor:'#ffffff',borderWidth:3,hoverOffset:10}]},
        options:{responsive:true,maintainAspectRatio:false,cutout:'72%',animation:{animateScale:true,duration:600},
            plugins:{legend:{display:false},tooltip:{callbacks:{label:function(c){var t=c.dataset.data.reduce(function(a,b){return a+b;},0);return ' '+c.label+': '+c.raw+' ('+(t>0?Math.round(c.raw/t*100):0)+'%)';}},padding:10,cornerRadius:8}},
            onHover:function(e,els){onDonutHover(els);}
        }
    });
}
function onDonutHover(els){
    var data=donutChart.data.datasets[0].data,total=data.reduce(function(a,b){return a+b;},0);
    var nE=document.getElementById('donutCenterNum'),lE=document.getElementById('donutCenterLbl');
    if(els.length){var i=els[0].index;nE.textContent=data[i];nE.style.color=DONUT_COLORS[i];lE.textContent=DONUT_LABELS[i];}
    else{nE.textContent=total;nE.style.color='var(--text)';lE.textContent='total';}
}
function updateDonut(vals){
    if(!donutChart) return;
    var total=vals.reduce(function(a,b){return a+b;},0);
    donutChart.data.datasets[0].data=vals; donutChart.update();
    document.getElementById('donutCenterNum').textContent=total;
    document.getElementById('donutCenterNum').style.color='var(--text)';
    document.getElementById('donutCenterLbl').textContent='total';
    renderLegend(vals,total);
}
function renderLegend(vals,total){
    var c=document.getElementById('donutLegends'); c.innerHTML='';
    DONUT_LABELS.forEach(function(lbl,i){
        var pct=total>0?Math.round(vals[i]/total*100):0;
        var div=document.createElement('div');
        div.className='dl-item';
        div.style.background='rgba(255,255,255,0.05)';
        div.innerHTML='<span class="dl-dot" style="background:'+DONUT_COLORS[i]+'"></span><span class="dl-label" style="color:#e2e8f0">'+lbl+'</span><span class="dl-val" style="color:'+DONUT_TXT[i]+'">'+vals[i]+'<span class="dl-pct" style="color:#94a3b8">('+pct+'%)</span></span>';
        div.setAttribute('onmouseover','hoverDonut('+i+')');
        div.setAttribute('onmouseout','resetDonut()');
        c.appendChild(div);
    });
}
function hoverDonut(i){
    if(!donutChart) return;
    var d=donutChart.data.datasets[0].data;
    donutChart.setActiveElements([{datasetIndex:0,index:i}]);
    donutChart.tooltip.setActiveElements([{datasetIndex:0,index:i}],{x:0,y:0});
    donutChart.update();
    document.getElementById('donutCenterNum').textContent=d[i];
    document.getElementById('donutCenterNum').style.color=DONUT_COLORS[i];
    document.getElementById('donutCenterLbl').textContent=DONUT_LABELS[i];
}
function resetDonut(){
    if(!donutChart) return;
    var d=donutChart.data.datasets[0].data,t=d.reduce(function(a,b){return a+b;},0);
    donutChart.setActiveElements([]);donutChart.tooltip.setActiveElements([],{x:0,y:0});donutChart.update();
    document.getElementById('donutCenterNum').textContent=t;
    document.getElementById('donutCenterNum').style.color='var(--text)';
    document.getElementById('donutCenterLbl').textContent='total';
}

/* ══ PERIOD ══ */
function setViewMode(mode){
    viewMode=mode;
    ['vpHarian','vpMingguan','vpBulanan','vpTahunan'].forEach(function(id){document.getElementById(id).classList.remove('active');});
    document.getElementById({harian:'vpHarian',mingguan:'vpMingguan',bulanan:'vpBulanan',tahunan:'vpTahunan'}[mode]).classList.add('active');
    document.getElementById('filterBulan').style.display =(mode!=='tahunan')?'':'none';
    document.getElementById('filterMinggu').style.display=(mode==='mingguan')?'':'none';
    document.getElementById('filterHari').style.display  =(mode==='harian')?'':'none';
    if(mode==='harian') populateHari(parseInt(document.getElementById('filterTahun').value),parseInt(document.getElementById('filterBulan').value));
    onPeriodChange();
}
function populateHari(tahun,bulan){
    var sel=document.getElementById('filterHari'),prev=sel.value; sel.innerHTML='';
    var days=new Date(tahun,bulan,0).getDate();
    for(var d=1;d<=days;d++){var o=document.createElement('option');o.value=d;o.textContent=d+' '+NAMA_BULAN[bulan];sel.appendChild(o);}
    if(prev&&parseInt(prev)<=days) sel.value=prev;
}
function getPeriod(){
    return {
        tahun:  parseInt(document.getElementById('filterTahun').value)  ||new Date().getFullYear(),
        bulan:  parseInt(document.getElementById('filterBulan').value)  ||new Date().getMonth()+1,
        minggu: parseInt(document.getElementById('filterMinggu').value) ||1,
        hari:   parseInt(document.getElementById('filterHari').value)   ||1
    };
}
function filterByPeriod(records,p){
    return records.filter(function(r){
        if(r.tahun!==p.tahun) return false;
        if(viewMode==='tahunan') return true;
        if(r.bulan!==p.bulan) return false;
        if(viewMode==='bulanan') return true;
        if(viewMode==='mingguan') return r.minggu===p.minggu;
        return r.hari===p.hari;
    });
}
function periodLabel(p){
    if(viewMode==='tahunan') return 'Tahun '+p.tahun;
    if(viewMode==='bulanan') return NAMA_BULAN[p.bulan]+' '+p.tahun;
    if(viewMode==='mingguan') return 'Minggu '+p.minggu+', '+NAMA_BULAN[p.bulan]+' '+p.tahun;
    var tgl=p.tahun+'-'+String(p.bulan).padStart(2,'0')+'-'+String(p.hari).padStart(2,'0');
    return getNamaHari(tgl)+', '+p.hari+' '+NAMA_BULAN[p.bulan]+' '+p.tahun;
}
function viewLabel(){return {harian:'Harian',mingguan:'Mingguan',bulanan:'Bulanan',tahunan:'Tahunan'}[viewMode];}

/* ══ MAIN PERIOD CHANGE ══ */
function onPeriodChange(){
    var p=getPeriod(),lbl=periodLabel(p);
    if(viewMode==='harian') populateHari(p.tahun,p.bulan);
    ['chartPeriodLabel','tablePeriodLabel'].forEach(function(id){
        var el=document.getElementById(id); if(el) el.textContent=lbl;
    });
    var subEl=document.getElementById('tbPageSub');
    if(subEl&&(currentPage==='dashboard'||currentPage==='data')) subEl.textContent=lbl;
    document.getElementById('chartViewChip').textContent=viewLabel();

    var pr=filterByPeriod(RAW_DATA,p);
    var cTepat=pr.filter(function(r){return r.isMasuk&&!r.terlambat;}).length;
    var cLambat=pr.filter(function(r){return r.terlambat;}).length;
    var cCepat=pr.filter(function(r){return r.pulangCepat;}).length;
    var cAbsent=hitungAbsen(pr,p);

    ['statTotal','statTepat','statLambat','statCepat','statAbsent'].forEach(function(id,i){
        var el=document.getElementById(id);
        el.textContent=[pr.length,cTepat,cLambat,cCepat,cAbsent][i];
        el.classList.remove('pop'); void el.offsetWidth; el.classList.add('pop');
    });

    var cPulangTepat=pr.filter(function(r){return !r.isMasuk&&!r.pulangCepat;}).length;
    updateDonut([cTepat,cLambat,cCepat,cPulangTepat,cAbsent]);

    // Quick summary table (dashboard)
    renderDashQuickTable(pr,p);

    currentEmpPage=1;
    if(currentPage==='data'){
        if(viewMode==='harian') renderDailyAllEmployees(pr,p);
        else renderTable(buildGrouped(pr,p));
    }
}

/* ══ DASHBOARD QUICK TABLE ══ */
function renderDashQuickTable(pr,p){
    var wrap=document.getElementById('dashQuickTable');
    var allK={};
    RAW_DATA.forEach(function(r){if(!allK[r.pin])allK[r.pin]=r.nama;});
    var emps=Object.keys(allK).map(function(pin){
        var empR=pr.filter(function(r){return r.pin===pin;});
        var masuk=empR.filter(function(r){return r.isMasuk;}).length;
        var lambat=empR.filter(function(r){return r.terlambat;}).length;
        return {pin:pin,nama:allK[pin],masuk:masuk,lambat:lambat};
    }).sort(function(a,b){return a.nama<b.nama?-1:1;}).slice(0,8);

    if(!emps.length){wrap.innerHTML='<div class="empty"><div class="empty-icon">📋</div><div class="empty-text">Tidak ada data</div></div>';return;}
    var html='<table style="width:100%;border-collapse:collapse;font-size:12px"><thead><tr style="background:var(--bg)"><th style="padding:9px 14px;text-align:left;font-size:10px;font-weight:600;color:var(--text-mute);border-bottom:1px solid var(--border);text-transform:uppercase;letter-spacing:.07em">Karyawan</th><th style="padding:9px 14px;text-align:center;font-size:10px;font-weight:600;color:var(--text-mute);border-bottom:1px solid var(--border);text-transform:uppercase;letter-spacing:.07em">Hadir</th><th style="padding:9px 14px;text-align:center;font-size:10px;font-weight:600;color:var(--text-mute);border-bottom:1px solid var(--border);text-transform:uppercase;letter-spacing:.07em">Lambat</th></tr></thead><tbody>';
    emps.forEach(function(emp,i){
        var color=PALETTE[i%PALETTE.length];
        html+='<tr style="border-bottom:1px solid rgba(0,0,0,0.04)"><td style="padding:10px 14px"><div style="display:flex;align-items:center;gap:8px"><div style="width:26px;height:26px;border-radius:7px;background:'+color+';display:flex;align-items:center;justify-content:center;font-size:9px;font-weight:600;color:#fff;flex-shrink:0">'+emp.nama.substring(0,2).toUpperCase()+'</div><span style="font-size:12px;font-weight:500;color:var(--text)">'+emp.nama+'</span></div></td><td style="padding:10px 14px;text-align:center"><span style="font-size:13px;font-weight:600;color:'+(emp.masuk>0?'#16a34a':'var(--text-mute)')+'">'+emp.masuk+'</span></td><td style="padding:10px 14px;text-align:center"><span style="font-size:13px;font-weight:600;color:'+(emp.lambat>0?'#d97706':'var(--text-mute)')+'">'+emp.lambat+'</span></td></tr>';
    });
    html+='</tbody></table>';
    if(Object.keys(allK).length>8){html+='<div style="text-align:center;padding:10px;font-size:12px;color:var(--accent);cursor:pointer;font-weight:500" onclick="switchPage(\'data\')">Lihat semua karyawan →</div>';}
    wrap.innerHTML=html;
}

/* ══ HITUNG ABSEN ══ */
function hitungAbsen(periodRecords,p){
    var dates={};
    periodRecords.forEach(function(r){dates[r.tanggal]=true;});
    var dArr=Object.keys(dates); if(!dArr.length) return 0;
    var allPins={};
    RAW_DATA.forEach(function(r){allPins[r.pin]=true;});
    var total=Object.keys(allPins).length,absen=0;
    dArr.forEach(function(dt){
        var hadir={};
        periodRecords.forEach(function(r){if(r.tanggal===dt&&r.isMasuk)hadir[r.pin]=true;});
        absen+=total-Object.keys(hadir).length;
    });
    return Math.max(0,absen);
}

/* ══ DAILY VIEW ══ */
function renderDailyAllEmployees(periodRecords,p){
    var allK={};
    RAW_DATA.forEach(function(r){if(!allK[r.pin])allK[r.pin]=r.nama;});
    var dayData={};
    Object.keys(allK).forEach(function(pin){dayData[pin]={pin:pin,nama:allK[pin],masuk:null,pulang:null,terlambat:false,pulangCepat:false,absent:true};});
    periodRecords.forEach(function(r){
        if(!dayData[r.pin])dayData[r.pin]={pin:r.pin,nama:r.nama,masuk:null,pulang:null,terlambat:false,pulangCepat:false,absent:true};
        var d=dayData[r.pin];
        if(r.isMasuk){d.absent=false;if(!d.masuk||r.waktu<d.masuk){d.masuk=r.waktu;d.terlambat=r.terlambat;}}
        else{if(!d.pulang||r.waktu>d.pulang){d.pulang=r.waktu;d.pulangCepat=r.pulangCepat;}}
    });
    var rows=Object.values(dayData).sort(function(a,b){return a.nama<b.nama?-1:1;});
    var tglStr=p.tahun+'-'+String(p.bulan).padStart(2,'0')+'-'+String(p.hari).padStart(2,'0');
    var namaHari=getNamaHari(tglStr);
    var hariCss=getHariCssClass(tglStr);
    document.getElementById('countChip').textContent=rows.length+' karyawan';
    document.getElementById('karyawanNavStrip').style.display='none';
    document.getElementById('empTableHeader').style.display='none';
    document.getElementById('tableHead').innerHTML='<tr><th>No</th><th>Karyawan</th><th>Jam Masuk</th><th>Jam Pulang</th><th>Status</th></tr>';
    var tbody=document.getElementById('tableBody'); tbody.innerHTML='';
    if(!rows.length){tbody.innerHTML='<tr><td colspan="5"><div class="empty"><div class="empty-icon">📋</div><div class="empty-text">Tidak ada data untuk hari ini</div></div></td></tr>';} 
    else {
        rows.forEach(function(emp,idx){
            var color=PALETTE[idx%PALETTE.length];
            var initials=emp.nama.substring(0,2).toUpperCase();
            var tr=document.createElement('tr');
            tr.style.animation='rowIn .2s '+(idx*0.02)+'s ease both';
            if(emp.absent) tr.className='row-absent';
            var noCell='<td style="color:var(--text-light);font-size:12px;font-family:\'JetBrains Mono\',monospace">'+String(idx+1).padStart(2,'0')+'</td>';
            var empCell='<td><div class="emp-name-cell"><div class="emp-avatar-sm" style="background:'+color+'">'+initials+'</div><div><div class="emp-name-text">'+emp.nama+'</div><div style="display:flex;align-items:center;gap:5px;margin-top:2px"><span class="emp-pin-text">PIN: '+emp.pin+'</span><span class="date-day-badge '+hariCss+'">'+namaHari+'</span></div></div></div></td>';
            if(emp.absent){
                tr.innerHTML=noCell+empCell+'<td><span class="badge-absent">Tidak Masuk</span></td><td><span class="time-none">—</span></td><td><span class="status-pill sp-absent">⚠ Absen</span></td>';
            } else {
                var jamMasuk=emp.masuk?(emp.terlambat?'<span class="time-badge time-late"><svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="9"/><path d="M12 8v4"/><circle cx="12" cy="16" r=".8" fill="#d97706"/></svg>'+emp.masuk+'</span>':'<span class="time-badge time-ok"><svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="9"/><path d="M9 12l2 2 4-4"/></svg>'+emp.masuk+'</span>'):'<span class="time-none">—</span>';
                var jamPulang=emp.pulang?(emp.pulangCepat?'<span class="time-badge time-early"><svg viewBox="0 0 24 24"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>'+emp.pulang+'</span>':'<span class="time-badge time-ok"><svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="9"/><path d="M9 12l2 2 4-4"/></svg>'+emp.pulang+'</span>'):'<span class="time-badge time-ok"><svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="9"/><path d="M9 12l2 2 4-4"/></svg>16:00:00</span>';
                var status=emp.terlambat?'<span class="status-pill sp-lambat">⚠ Terlambat</span>':emp.pulangCepat?'<span class="status-pill sp-cepat">↩ Pulang Cepat</span>':emp.masuk?'<span class="status-pill sp-tepat">✓ Tepat Waktu</span>':'<span class="time-none">—</span>';
                tr.innerHTML=noCell+empCell+'<td>'+jamMasuk+'</td><td>'+jamPulang+'</td><td>'+status+'</td>';
            }
            tbody.appendChild(tr);
        });
    }
    var jmlHadir=rows.filter(function(r){return !r.absent;}).length;
    var jmlLambat=rows.filter(function(r){return r.terlambat;}).length;
    var jmlAbsent=rows.filter(function(r){return r.absent;}).length;
    document.getElementById('pgInfo').innerHTML='📅 <strong>'+namaHari+', '+p.hari+' '+NAMA_BULAN[p.bulan]+' '+p.tahun+'</strong> &nbsp;| <span style="color:#16a34a;font-weight:600">✓ Hadir: '+jmlHadir+'</span> &nbsp;<span style="color:#d97706;font-weight:600">⚠ Lambat: '+jmlLambat+'</span> &nbsp;<span style="color:#7c3aed;font-weight:600">✕ Absen: '+jmlAbsent+'</span>';
    document.getElementById('pgBtns').innerHTML='';
}

/* ══ BUILD GROUPED ══ */
function buildGrouped(pr,p){
    var datesInPeriod={};
    pr.forEach(function(r){if(!datesInPeriod[r.tanggal])datesInPeriod[r.tanggal]=r.tanggalFmt;});
    var allDates=Object.keys(datesInPeriod).sort();
    var allK={};
    RAW_DATA.forEach(function(r){if(!allK[r.pin])allK[r.pin]=r.nama;});
    var kMap={};
    Object.keys(allK).forEach(function(pin){
        kMap[pin]={pin:pin,nama:allK[pin],days:{}};
        allDates.forEach(function(dt){kMap[pin].days[dt]={tanggal:dt,tanggalFmt:datesInPeriod[dt],masuk:null,pulang:null,terlambat:false,pulangCepat:false,absent:true};});
    });
    pr.forEach(function(r){
        if(!kMap[r.pin])kMap[r.pin]={pin:r.pin,nama:r.nama,days:{}};
        if(!kMap[r.pin].days[r.tanggal])kMap[r.pin].days[r.tanggal]={tanggal:r.tanggal,tanggalFmt:r.tanggalFmt,masuk:null,pulang:null,terlambat:false,pulangCepat:false,absent:true};
        var d=kMap[r.pin].days[r.tanggal];
        if(r.isMasuk){d.absent=false;if(!d.masuk||r.waktu<d.masuk){d.masuk=r.waktu;d.terlambat=r.terlambat;}}
        else{if(!d.pulang||r.waktu>d.pulang){d.pulang=r.waktu;d.pulangCepat=r.pulangCepat;}}
    });
    var result=[];
    Object.keys(kMap).forEach(function(pin){
        var k=kMap[pin];
        var days=Object.values(k.days).sort(function(a,b){return a.tanggal<b.tanggal?-1:1;});
        if(allDates.length>0) result.push({pin:k.pin,nama:k.nama,days:days});
    });
    return result.sort(function(a,b){return a.nama<b.nama?-1:1;});
}

/* ══ FILTER & RENDER ══ */
function applyFiltersAndRender(){
    var name = document.getElementById('searchName').value.toLowerCase().trim();
    var dateF = document.getElementById('filterDate').value;
    var ket = document.getElementById('filterKet').value;

    // Jika ada filter tanggal spesifik, paksa mode bulanan dan sesuaikan period
    var p = getPeriod();
    if (dateF) {
        var parts = dateF.split('-');
        // Override tahun & bulan dari filterTahun/filterBulan sesuai tanggal yang dipilih
        p.tahun = parseInt(parts[0]);
        p.bulan = parseInt(parts[1]);
        p.hari  = parseInt(parts[2]);
    }

    var pr = filterByPeriod(RAW_DATA, p);

    // Mode harian: filter per hari lalu tampilkan semua karyawan
    if (viewMode === 'harian') {
        // filter nama dulu kalau ada
        if (name) {
            pr = pr.filter(function(r){ return r.nama.toLowerCase().indexOf(name) !== -1; });
        }
        renderDailyAllEmployees(pr, p);
        return;
    }

    // Mode bulanan/mingguan/tahunan
    // Kalau ada filter tanggal, ambil semua data RAW tanpa batasan period dulu
    var baseRecords = dateF
        ? RAW_DATA.filter(function(r){ return r.tanggal === dateF; })
        : pr;

    var grouped = buildGrouped(baseRecords, p);

    // Filter nama
    if (name) {
        grouped = grouped.filter(function(k){
            return k.nama.toLowerCase().indexOf(name) !== -1;
        });
    }

    // Filter tanggal spesifik (sudah di-handle di baseRecords, tapi pastikan days juga terfilter)
    if (dateF) {
        grouped = grouped.map(function(k){
            return { pin:k.pin, nama:k.nama, days:k.days.filter(function(d){ return d.tanggal === dateF; }) };
        }).filter(function(k){ return k.days.length > 0; });
    }

    // Filter keterangan
    if (ket) {
        grouped = grouped.map(function(k){
            return { pin:k.pin, nama:k.nama, days:k.days.filter(function(d){
                if(ket==='terlambat') return d.terlambat;
                if(ket==='cepat')    return d.pulangCepat;
                if(ket==='tepat')    return d.masuk && !d.terlambat;
                if(ket==='absent')   return d.absent;
                return true;
            })};
        }).filter(function(k){ return k.days.length > 0; });
    }

    currentEmpPage = 1;
    renderTable(grouped);
}
function resetFilter(){
    document.getElementById('searchName').value='';
    document.getElementById('filterDate').value='';
    document.getElementById('filterKet').value='';
    applyFiltersAndRender();
}

/* ══ RENDER TABLE ══ */
function renderTable(grouped){
    document.getElementById('tableHead').innerHTML='<tr><th>No</th><th>Tanggal</th><th>Jam Masuk</th><th>Jam Pulang</th><th>Status</th></tr>';
    document.getElementById('karyawanNavStrip').style.display='';
    document.getElementById('empTableHeader').style.display='';
    var tbody=document.getElementById('tableBody'),pgBtns=document.getElementById('pgBtns'),pgInfo=document.getElementById('pgInfo');
    var navStrip=document.getElementById('karyawanNavStrip');
    tbody.innerHTML='';
    var total=grouped.length;
    if(currentEmpPage>total) currentEmpPage=Math.max(1,total);
    var totalDays=grouped.reduce(function(s,k){return s+k.days.length;},0);
    document.getElementById('countChip').textContent=total+' karyawan · '+totalDays+' hari';
    navStrip.innerHTML='<span class="karyawan-nav-label">Karyawan:</span>';
    grouped.forEach(function(k,idx){
        var btn=document.createElement('button');
        btn.className='karyawan-nav-btn'+(idx+1===currentEmpPage?' active':'');
        btn.textContent=k.nama.split(' ')[0]; btn.title=k.nama;
        btn.onclick=(function(i){return function(){currentEmpPage=i+1;renderTable(grouped);};})(idx);
        navStrip.appendChild(btn);
    });
    if(!total){
        resetEmpHeader();
        tbody.innerHTML='<tr><td colspan="5"><div class="empty"><div class="empty-icon">🔍</div><div class="empty-text">Tidak ada data</div><div class="empty-sub">Pilih periode lain atau ubah filter</div></div></td></tr>';
        pgInfo.textContent='Tidak ada data'; pgBtns.innerHTML=''; return;
    }
    var k=grouped[currentEmpPage-1];
    updateEmpHeader(k,currentEmpPage-1);
    k.days.forEach(function(d,idx){
        var tr=document.createElement('tr');
        tr.style.animation='rowIn .2s '+(idx*0.02)+'s ease both';
        var noCell='<td style="color:var(--text-light);font-size:12px;font-family:\'JetBrains Mono\',monospace">'+String(idx+1).padStart(2,'0')+'</td>';
        if(d.absent){
            tr.className='row-absent';
            tr.innerHTML=noCell+'<td style="font-size:12px;color:var(--text-mute)">'+d.tanggalFmt+'</td><td><span class="badge-absent">Tidak Masuk</span></td><td><span class="time-none">—</span></td><td><span class="status-pill sp-absent">⚠ Absen</span></td>';
        } else {
            var jamMasuk=d.masuk?(d.terlambat?'<span class="time-badge time-late"><svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="9"/><path d="M12 8v4"/><circle cx="12" cy="16" r=".8" fill="#d97706"/></svg>'+d.masuk+'</span>':'<span class="time-badge time-ok"><svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="9"/><path d="M9 12l2 2 4-4"/></svg>'+d.masuk+'</span>'):'<span class="time-none">—</span>';
            var jamPulang=d.pulang?(d.pulangCepat?'<span class="time-badge time-early"><svg viewBox="0 0 24 24"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>'+d.pulang+'</span>':'<span class="time-badge time-ok"><svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="9"/><path d="M9 12l2 2 4-4"/></svg>'+d.pulang+'</span>'):'<span class="time-badge time-ok"><svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="9"/><path d="M9 12l2 2 4-4"/></svg>16:00:00</span>';
            var status=d.terlambat?'<span class="status-pill sp-lambat">⚠ Terlambat</span>':d.pulangCepat?'<span class="status-pill sp-cepat">↩ Pulang Cepat</span>':d.masuk?'<span class="status-pill sp-tepat">✓ Tepat Waktu</span>':'<span class="time-none">—</span>';
            tr.innerHTML=noCell+'<td style="font-size:12px;color:var(--text-mute)">'+d.tanggalFmt+'</td><td>'+jamMasuk+'</td><td>'+jamPulang+'</td><td>'+status+'</td>';
        }
        tbody.appendChild(tr);
    });
    pgInfo.textContent='Karyawan '+currentEmpPage+' dari '+total;
    pgBtns.innerHTML='';
    function mkBtn(label,page,disabled,active){var b=document.createElement('button');b.className='pg-btn'+(active?' pg-active':'');b.innerHTML=label;b.disabled=disabled;b.onclick=function(){currentEmpPage=page;renderTable(grouped);};pgBtns.appendChild(b);}
    function mkE(){var s=document.createElement('span');s.className='pg-ellipsis';s.textContent='…';pgBtns.appendChild(s);}
    mkBtn('&#8592;',currentEmpPage-1,currentEmpPage===1,false);
    var pages=[];
    if(total<=7){for(var i=1;i<=total;i++) pages.push(i);}
    else{pages.push(1);if(currentEmpPage>3)pages.push('…');var lo=Math.max(2,currentEmpPage-1),hi=Math.min(total-1,currentEmpPage+1);for(var j=lo;j<=hi;j++)pages.push(j);if(currentEmpPage<total-2)pages.push('…');pages.push(total);}
    pages.forEach(function(pg){if(pg==='…')mkE();else mkBtn(pg,pg,false,pg===currentEmpPage);});
    mkBtn('&#8594;',currentEmpPage+1,currentEmpPage===total,false);
}

function updateEmpHeader(k,colorIdx){
    var color=PALETTE[colorIdx%PALETTE.length];
    var av=document.getElementById('empTableAvatar');
    av.textContent=k.nama.substring(0,2).toUpperCase(); av.style.background=color;
    document.getElementById('empTableName').textContent=k.nama;
    document.getElementById('empTablePin').textContent='PIN: '+k.pin;
    document.getElementById('empTableDays').textContent=k.days.length+' hari';
    var tepat=0,lambat=0,cepat=0,absent=0;
    k.days.forEach(function(d){if(d.absent)absent++;else{if(d.terlambat)lambat++;else if(d.masuk)tepat++;if(d.pulangCepat)cepat++;}});
    var html='';
    if(tepat)  html+='<div class="esp esp-tepat">✓ '+tepat+' Tepat</div>';
    if(lambat) html+='<div class="esp esp-lambat">⚠ '+lambat+' Lambat</div>';
    if(cepat)  html+='<div class="esp esp-cepat">↩ '+cepat+' Cepat</div>';
    if(absent) html+='<div class="esp esp-absent">✕ '+absent+' Absen</div>';
    document.getElementById('empTableStats').innerHTML=html;
}
function resetEmpHeader(){
    document.getElementById('empTableAvatar').textContent='??';
    document.getElementById('empTableAvatar').style.background='var(--accent)';
    ['empTableName','empTablePin'].forEach(function(id){document.getElementById(id).textContent='—';});
    document.getElementById('empTableDays').textContent='0 hari';
    document.getElementById('empTableStats').innerHTML='';
}

/* ══ PERFORMA ══ */
function getEmpsForPeriod(tahun,bulan){
    var allPins={};
    RAW_DATA.filter(function(r){return r.tahun===tahun;}).forEach(function(r){if(!allPins[r.pin])allPins[r.pin]={pin:r.pin,nama:r.nama};});
    var relevant=RAW_DATA.filter(function(r){if(r.tahun!==tahun)return false;if(bulan>0&&r.bulan!==bulan)return false;return true;});
    var workDates={};
    relevant.forEach(function(r){workDates[r.tanggal]=true;});
    var totalWD=Object.keys(workDates).length;
    var pinStats={};
    relevant.forEach(function(r){
        if(!pinStats[r.pin])pinStats[r.pin]={dateSet:{}};
        if(!pinStats[r.pin].dateSet[r.tanggal])pinStats[r.pin].dateSet[r.tanggal]={masuk:false,terlambat:false,pulangCepat:false};
        if(r.isMasuk){pinStats[r.pin].dateSet[r.tanggal].masuk=true;if(r.terlambat)pinStats[r.pin].dateSet[r.tanggal].terlambat=true;}
        else{if(r.pulangCepat)pinStats[r.pin].dateSet[r.tanggal].pulangCepat=true;}
    });
    return Object.keys(allPins).map(function(pin){
        var info=allPins[pin],sd=pinStats[pin]?Object.values(pinStats[pin].dateSet):[];
        var hadir=sd.filter(function(d){return d.masuk;}).length;
        return {pin:info.pin,nama:info.nama,hadir:hadir,lambat:sd.filter(function(d){return d.terlambat;}).length,cepat:sd.filter(function(d){return d.pulangCepat;}).length,absent:Math.max(0,totalWD-hadir),totalWD:totalWD};
    }).sort(function(a,b){return a.nama<b.nama?-1:1;});
}

function renderPerfPage(){
    var tahun=parseInt(document.getElementById('filterTahunPerf').value);
    var bulan=parseInt(document.getElementById('filterBulanPerf').value);
    document.getElementById('perfPeriodHint').textContent=bulan===0?'📅 Seluruh tahun '+tahun:'📅 '+NAMA_BULAN[bulan]+' '+tahun;
    var emps=getEmpsForPeriod(tahun,bulan);
    document.getElementById('empCountChip').textContent=emps.length+' karyawan';
    renderEmpSelector(emps,tahun,bulan);
    if(selectedEmpPin){
        var emp=emps.find(function(e){return e.pin===selectedEmpPin;});
        if(emp)buildBarChart(selectedEmpPin,emp.nama,selectedEmpColor,tahun,bulan);
        else{document.getElementById('empChartCard').classList.remove('show');document.getElementById('empChartHint').style.display='';selectedEmpPin=null;}
    }
}

function renderEmpSelector(emps,tahun,bulan){
    var grid=document.getElementById('empSelectorGrid'); grid.innerHTML='';
    if(!emps.length){grid.innerHTML='<div class="empty"><div class="empty-icon">📊</div><div class="empty-text">Tidak ada data</div></div>';return;}
    emps.forEach(function(emp,i){
        var color=PALETTE[i%PALETTE.length];
        var card=document.createElement('div');
        card.className='emp-card'+(emp.pin===selectedEmpPin?' selected':'');
    card.innerHTML = '<div class="emp-card-badge"><svg viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg></div>'
    + '<div class="emp-card-header">'
    + avatarHTML(emp.nama, color, 38, 13)
    + '<div style="min-width:0"><div class="emp-card-name" title="'+emp.nama+'">'+emp.nama+'</div>'
    + '<div class="emp-card-pin">PIN: '+emp.pin+'</div></div></div>'
    + '<div class="emp-card-stats">'
    + '<div class="emp-card-stat" style="background:rgba(99,102,241,0.15);border:1px solid rgba(99,102,241,0.25)"><div class="emp-card-stat-num" style="color:#818cf8">'+emp.hadir+'</div><div class="emp-card-stat-lbl" style="color:#94a3b8">Hadir</div></div>'
    + '<div class="emp-card-stat" style="background:rgba(245,158,11,0.15);border:1px solid rgba(245,158,11,0.25)"><div class="emp-card-stat-num" style="color:#fbbf24">'+emp.lambat+'</div><div class="emp-card-stat-lbl" style="color:#94a3b8">Lambat</div></div>'
    + '<div class="emp-card-stat" style="background:rgba(244,63,94,0.15);border:1px solid rgba(244,63,94,0.25)"><div class="emp-card-stat-num" style="color:#fb7185">'+emp.cepat+'</div><div class="emp-card-stat-lbl" style="color:#94a3b8">Cepat</div></div>'
    + '<div class="emp-card-stat" style="background:rgba(168,85,247,0.15);border:1px solid rgba(168,85,247,0.25)"><div class="emp-card-stat-num" style="color:#c084fc">'+emp.absent+'</div><div class="emp-card-stat-lbl" style="color:#94a3b8">Absen</div></div>'
    + '</div>';
        (function(pin,nama,col,t,b,el){
            el.onclick=function(){
                selectedEmpPin=pin;selectedEmpColor=col;
                document.querySelectorAll('.emp-card').forEach(function(c){c.classList.remove('selected');});
                el.classList.add('selected');
                buildBarChart(pin,nama,col,t,b);
            };
        })(emp.pin,emp.nama,color,tahun,bulan,card);
        grid.appendChild(card);
    });
}

function getMonthlyData(pin,tahun){
    var mMap={},mWD={};
    RAW_DATA.filter(function(r){return r.tahun===tahun;}).forEach(function(r){if(!mWD[r.bulan])mWD[r.bulan]={};mWD[r.bulan][r.tanggal]=true;});
    RAW_DATA.filter(function(r){return r.pin===pin&&r.tahun===tahun;}).forEach(function(r){
        if(!mMap[r.bulan])mMap[r.bulan]={};
        if(!mMap[r.bulan][r.tanggal])mMap[r.bulan][r.tanggal]={masuk:null,pulang:null,terlambat:false,pulangCepat:false};
        var d=mMap[r.bulan][r.tanggal];
        if(r.isMasuk){if(!d.masuk||r.waktu<d.masuk){d.masuk=r.waktu;d.terlambat=r.terlambat;}}
        else{if(!d.pulang||r.waktu>d.pulang){d.pulang=r.waktu;d.pulangCepat=r.pulangCepat;}}
    });
    var result=[];
    for(var m=1;m<=12;m++){
        if(!mWD[m])continue;
        var days=mMap[m]?Object.values(mMap[m]):[];
        var hadir=days.filter(function(d){return d.masuk;}).length;
        result.push({bulan:m,label:NAMA_BULAN_S[m],hadir:hadir,lambat:days.filter(function(d){return d.terlambat;}).length,cepat:days.filter(function(d){return d.pulangCepat;}).length,tepat:days.filter(function(d){return d.masuk&&!d.terlambat;}).length,absent:Math.max(0,Object.keys(mWD[m]).length-hadir)});
    }
    return result;
}

function getDailyData(pin,tahun,bulan){
    var dMap={},allDates={};
    RAW_DATA.filter(function(r){return r.tahun===tahun&&r.bulan===bulan;}).forEach(function(r){allDates[r.tanggal]=r.tanggalFmt;});
    RAW_DATA.filter(function(r){return r.pin===pin&&r.tahun===tahun&&r.bulan===bulan;}).forEach(function(r){
        if(!dMap[r.tanggal])dMap[r.tanggal]={masuk:null,pulang:null,terlambat:false,pulangCepat:false};
        var d=dMap[r.tanggal];
        if(r.isMasuk){if(!d.masuk||r.waktu<d.masuk){d.masuk=r.waktu;d.terlambat=r.terlambat;}}
        else{if(!d.pulang||r.waktu>d.pulang){d.pulang=r.waktu;d.pulangCepat=r.pulangCepat;}}
    });
    return Object.keys(allDates).sort().map(function(dt){
        var d=dMap[dt]||{masuk:null,terlambat:false,pulangCepat:false};
        return {tanggal:dt,label:String(parseInt(dt.split('-')[2])),hadir:d.masuk?1:0,lambat:d.terlambat?1:0,cepat:d.pulangCepat?1:0,tepat:(d.masuk&&!d.terlambat)?1:0,absent:d.masuk?0:1};
    });
}

function setBarMode(mode){
    barMode=mode;
    ['btnModeAll','btnModeHadir','btnModeLate'].forEach(function(id){document.getElementById(id).classList.remove('active');});
    document.getElementById({all:'btnModeAll',hadir:'btnModeHadir',late:'btnModeLate'}[mode]).classList.add('active');
    if(selectedEmpPin){
        var tahun=parseInt(document.getElementById('filterTahunPerf').value);
        var bulan=parseInt(document.getElementById('filterBulanPerf').value);
        buildBarChart(selectedEmpPin,document.getElementById('bcName').textContent,selectedEmpColor,tahun,bulan);
    }
}

function buildCustomLegend(activeKeys){
    var c=document.getElementById('chartLegendCustom'); c.innerHTML='';
    activeKeys.forEach(function(key){
        var cfg=BC[key];
        var item=document.createElement('div');item.className='cl-item';
        var ind=cfg.type==='line'?'<div class="cl-line" style="background:'+cfg.hex+'"></div>':'<div class="cl-dot" style="background:'+cfg.bg+';border:2px solid '+cfg.border+'"></div>';
        item.innerHTML=ind+'<span>'+cfg.label+'</span>';
        c.appendChild(item);
    });
}

function buildBarChart(pin,nama,color,tahun,bulan){
    var isDaily=bulan>0;
    var data=isDaily?getDailyData(pin,tahun,bulan):getMonthlyData(pin,tahun);
    document.getElementById('empChartCard').classList.add('show');
    document.getElementById('empChartHint').style.display='none';
  var av=document.getElementById('bcAvatar');
    av.innerHTML=''; 
    var bgColor=isMale(nama)?'linear-gradient(135deg,#2563eb,#6366f1)':'linear-gradient(135deg,#be185d,#ec4899)';
    av.style.background=bgColor;
    av.innerHTML='<svg viewBox="0 0 24 24" width="22" height="22" fill="white"><path d="M12 12c2.7 0 4.8-2.1 4.8-4.8S14.7 2.4 12 2.4 7.2 4.5 7.2 7.2 9.3 12 12 12zm0 2.4c-3.2 0-9.6 1.6-9.6 4.8v2.4h19.2v-2.4c0-3.2-6.4-4.8-9.6-4.8z"/></svg>';
    document.getElementById('bcName').textContent=nama;
    document.getElementById('bcPin').textContent='PIN: '+pin+' · '+(isDaily?NAMA_BULAN[bulan]+' '+tahun:'Tahun '+tahun);
    var yH=0,yL=0,yE=0,yT=0,yA=0;
    data.forEach(function(m){yH+=m.hadir;yL+=m.lambat;yE+=m.cepat;yT+=m.tepat;yA+=m.absent;});
    renderYearlyStat(yH,yL,yE,yT,yA,isDaily?NAMA_BULAN[bulan]+' '+tahun:'Tahun '+tahun);
    if(!data.length){if(empBarChart){empBarChart.destroy();empBarChart=null;}return;}
    if(empBarChart){empBarChart.destroy();empBarChart=null;}
    var ctx=document.getElementById('empBarChart').getContext('2d');
    var datasets=[],activeKeys=[];
    if(barMode==='all'||barMode==='hadir'){
   datasets.push({label:'Total Hadir',data:data.map(function(m){return m.hadir;}),backgroundColor:'rgba(99,102,241,0.85)',borderColor:'#6366f1',borderWidth:1.5,borderRadius:6,borderSkipped:false,barPercentage:isDaily?0.5:0.8,categoryPercentage:isDaily?0.6:0.9});activeKeys.push('hadir');
    datasets.push({label:'Tepat Waktu',data:data.map(function(m){return m.tepat;}),backgroundColor:'rgba(34,197,94,0.85)',borderColor:'#22c55e',borderWidth:1.5,borderRadius:6,borderSkipped:false,barPercentage:isDaily?0.5:0.8,categoryPercentage:isDaily?0.6:0.9});activeKeys.push('tepat');
    datasets.push({label:'Tidak Masuk',data:data.map(function(m){return m.absent;}),backgroundColor:'rgba(168,85,247,0.85)',borderColor:'#a855f7',borderWidth:1.5,borderRadius:6,borderSkipped:false,barPercentage:isDaily?0.5:0.8,categoryPercentage:isDaily?0.6:0.9});activeKeys.push('absent');
}
    if(barMode==='all'||barMode==='late'){
    datasets.push({label:'Terlambat',data:data.map(function(m){return m.lambat;}),backgroundColor:'rgba(245,158,11,0.85)',borderColor:'#f59e0b',borderWidth:1.5,borderRadius:6,borderSkipped:false,barPercentage:isDaily?0.5:0.8,categoryPercentage:isDaily?0.6:0.9});activeKeys.push('lambat');
        datasets.push({label:'Pulang Cepat',data:data.map(function(m){return m.cepat;}),backgroundColor:'rgba(244,63,94,0.85)',borderColor:'#f43f5e',borderWidth:1.5,borderRadius:6,borderSkipped:false,barPercentage:isDaily?0.5:0.8,categoryPercentage:isDaily?0.6:0.9});activeKeys.push('cepat');
}
    buildCustomLegend(activeKeys);
empBarChart=new Chart(ctx,{
    type: isDaily ? 'bar' : 'bar',
    data:{labels:data.map(function(m){return m.label;}),datasets:datasets},
    options:{
        responsive:true,maintainAspectRatio:false,
        interaction:{mode:'index',intersect:false},
        plugins:{
            legend:{display:false},
                tooltip:{callbacks:{title:function(items){var m=data[items[0].dataIndex];return isDaily?m.tanggal:NAMA_BULAN[m.bulan]+' '+tahun;},label:function(c){return ' '+c.dataset.label+': '+c.raw+' hari';}},padding:12,cornerRadius:10,backgroundColor:'rgba(17,24,39,0.92)',titleColor:'#c7d2fe',bodyColor:'#e0e7ff',borderColor:'rgba(99,102,241,0.3)',borderWidth:1}
            },
         scales:{
                x:{grid:{display:false},ticks:{font:{family:'Inter',size:11,weight:'500'},color:'#64748b'},border:{display:false}},
                y:{beginAtZero:true,grid:{color:'rgba(255,255,255,0.05)'},ticks:{font:{family:'JetBrains Mono',size:10},color:'#64748b',stepSize:1,callback:function(v){return Number.isInteger(v)?v:'';}},border:{display:false}}
        },
            animation:{duration:600,easing:'easeInOutQuart'}
        }
    });
    setTimeout(function(){document.getElementById('empChartCard').scrollIntoView({behavior:'smooth',block:'nearest'});},80);
}

function renderYearlyStat(h,l,e,t,a,label){
    document.getElementById('yearlyStatStrip').innerHTML=
        (label?'<div style="width:100%;font-size:11px;font-weight:600;color:var(--text-mute);text-transform:uppercase;letter-spacing:.06em;padding-bottom:6px">Ringkasan '+label+'</div>':'')+
        ys('ys-hadir',h,'Total Hadir')+ys('ys-tepat',t,'Tepat Waktu')+ys('ys-lambat',l,'Terlambat')+ys('ys-cepat',e,'Pulang Cepat')+ys('ys-absent',a,'Tidak Masuk');
}
function ys(cls,val,lbl){
    var colors={
        'ys-hadir':'#4ade80','ys-lambat':'#fbbf24','ys-cepat':'#fb7185','ys-tepat':'#818cf8','ys-absent':'#c084fc'
    };
    var bgs={
        'ys-hadir':'rgba(34,197,94,0.15)','ys-lambat':'rgba(245,158,11,0.15)','ys-cepat':'rgba(244,63,94,0.15)','ys-tepat':'rgba(99,102,241,0.15)','ys-absent':'rgba(168,85,247,0.15)'
    };
    var borders={
        'ys-hadir':'rgba(34,197,94,0.3)','ys-lambat':'rgba(245,158,11,0.3)','ys-cepat':'rgba(244,63,94,0.3)','ys-tepat':'rgba(99,102,241,0.3)','ys-absent':'rgba(168,85,247,0.3)'
    };
    return '<div class="ys-pill" style="background:'+bgs[cls]+';border:1px solid '+borders[cls]+'"><div><div class="ys-val" style="color:'+colors[cls]+'">'+val+'</div><div class="ys-lbl" style="color:#94a3b8">'+lbl+'</div></div></div>';
}

function cetakLaporan() {
    var p = getPeriod();
    var lbl = periodLabel(p);
    var pr = filterByPeriod(RAW_DATA, p);

    // Bangun data yang akan dicetak
    var grouped = [];
    if (viewMode === 'harian') {
        // Mode harian: satu tabel semua karyawan
        var allK = {};
        RAW_DATA.forEach(function(r){ if(!allK[r.pin]) allK[r.pin]=r.nama; });
        var dayData = {};
        Object.keys(allK).forEach(function(pin){
            dayData[pin]={pin:pin,nama:allK[pin],masuk:null,pulang:null,terlambat:false,pulangCepat:false,absent:true};
        });
        pr.forEach(function(r){
            var d=dayData[r.pin];
            if(!d) return;
            if(r.isMasuk){d.absent=false;if(!d.masuk||r.waktu<d.masuk){d.masuk=r.waktu;d.terlambat=r.terlambat;}}
            else{if(!d.pulang||r.waktu>d.pulang){d.pulang=r.waktu;d.pulangCepat=r.pulangCepat;}}
        });
        grouped = Object.values(dayData).sort(function(a,b){return a.nama<b.nama?-1:1;});
    } else {
        grouped = buildGrouped(pr, p);
    }

    // Susun HTML tabel untuk dicetak
    var rowsHtml = '';
    var no = 1;

    if (viewMode === 'harian') {
        grouped.forEach(function(emp) {
            var statusTxt = emp.absent ? 'Tidak Masuk'
                : emp.terlambat ? 'Terlambat'
                : emp.pulangCepat ? 'Pulang Cepat'
                : 'Tepat Waktu';
            var masukTxt = emp.masuk || (emp.absent ? '—' : '—');
            var pulangTxt = emp.pulang || (emp.absent ? '—' : '16:00:00');
            rowsHtml += '<tr>'
                + '<td>'+(no++)+'</td>'
                + '<td>'+emp.nama+'</td>'
                + '<td>'+emp.pin+'</td>'
                + '<td>'+masukTxt+'</td>'
                + '<td>'+pulangTxt+'</td>'
                + '<td>'+statusTxt+'</td>'
                + '</tr>';
        });
    } else {
        grouped.forEach(function(k) {
            k.days.forEach(function(d) {
                var statusTxt = d.absent ? 'Tidak Masuk'
                    : d.terlambat ? 'Terlambat'
                    : d.pulangCepat ? 'Pulang Cepat'
                    : d.masuk ? 'Tepat Waktu' : '—';
                var masukTxt = d.masuk || '—';
                var pulangTxt = d.pulang || (d.masuk ? '16:00:00' : '—');
                rowsHtml += '<tr>'
                    + '<td>'+(no++)+'</td>'
                    + '<td>'+d.tanggalFmt+'</td>'
                    + '<td>'+k.nama+'</td>'
                    + '<td>'+k.pin+'</td>'
                    + '<td>'+masukTxt+'</td>'
                    + '<td>'+pulangTxt+'</td>'
                    + '<td>'+statusTxt+'</td>'
                    + '</tr>';
            });
        });
    }

    // Hitung ringkasan
    var total = pr.length;
    var tepat  = pr.filter(function(r){return r.isMasuk&&!r.terlambat;}).length;
    var lambat = pr.filter(function(r){return r.terlambat;}).length;
    var cepat  = pr.filter(function(r){return r.pulangCepat;}).length;
    var absent = hitungAbsen(pr, p);

    var headerCols = viewMode === 'harian'
        ? '<th>No</th><th>Nama Karyawan</th><th>PIN</th><th>Jam Masuk</th><th>Jam Pulang</th><th>Status</th>'
        : '<th>No</th><th>Tanggal</th><th>Nama Karyawan</th><th>PIN</th><th>Jam Masuk</th><th>Jam Pulang</th><th>Status</th>';

    var printHtml = '<!DOCTYPE html><html lang="id"><head>'
        + '<meta charset="UTF-8">'
        + '<title>Laporan Absensi — '+lbl+'</title>'
        + '<style>'
        + 'body{font-family:Arial,sans-serif;font-size:12px;color:#111;margin:0;padding:0}'
        + '.header{text-align:center;margin-bottom:20px;border-bottom:2px solid #333;padding-bottom:12px}'
        + '.header h2{font-size:16px;margin:0 0 4px}'
        + '.header p{font-size:12px;color:#555;margin:2px 0}'
        + '.summary{display:flex;gap:10px;margin-bottom:16px;flex-wrap:wrap}'
        + '.sum-box{border:1px solid #ddd;border-radius:6px;padding:8px 14px;text-align:center;min-width:90px}'
        + '.sum-num{font-size:18px;font-weight:700;margin-bottom:2px}'
        + '.sum-lbl{font-size:10px;color:#666;text-transform:uppercase;letter-spacing:.06em}'
        + 'table{width:100%;border-collapse:collapse;font-size:11px}'
        + 'th{background:#f0f0f0;padding:7px 10px;text-align:left;border:1px solid #ccc;font-size:10px;text-transform:uppercase;letter-spacing:.06em}'
        + 'td{padding:7px 10px;border:1px solid #e0e0e0;vertical-align:middle}'
        + 'tr:nth-child(even) td{background:#fafafa}'
        + '.ok{color:#16a34a;font-weight:600}'
        + '.late{color:#d97706;font-weight:600}'
        + '.early{color:#e11d48;font-weight:600}'
        + '.abs{color:#7c3aed;font-weight:600}'
        + '.footer{text-align:center;margin-top:20px;font-size:10px;color:#999;border-top:1px solid #eee;padding-top:10px}'
        + '@page{size:A4;margin:1.5cm}'
        + '@media print{body{-webkit-print-color-adjust:exact;print-color-adjust:exact}}'
        + '</style></head><body>'
        + '<div class="header">'
        + '<h2>Laporan Absensi Karyawan</h2>'
        + '<p>Periode: <strong>'+lbl+'</strong></p>'
        + '<p>Dicetak pada: '+new Date().toLocaleDateString('id-ID',{weekday:'long',day:'numeric',month:'long',year:'numeric'})+' pukul '+new Date().toLocaleTimeString('id-ID')+'</p>'
        + '</div>'
        + '<div class="summary">'
        + '<div class="sum-box"><div class="sum-num" style="color:#6366f1">'+total+'</div><div class="sum-lbl">Total Absensi</div></div>'
        + '<div class="sum-box"><div class="sum-num" style="color:#16a34a">'+tepat+'</div><div class="sum-lbl">Tepat Waktu</div></div>'
        + '<div class="sum-box"><div class="sum-num" style="color:#d97706">'+lambat+'</div><div class="sum-lbl">Terlambat</div></div>'
        + '<div class="sum-box"><div class="sum-num" style="color:#e11d48">'+cepat+'</div><div class="sum-lbl">Pulang Cepat</div></div>'
        + '<div class="sum-box"><div class="sum-num" style="color:#7c3aed">'+absent+'</div><div class="sum-lbl">Tidak Masuk</div></div>'
        + '</div>'
        + '<table><thead><tr>'+headerCols+'</tr></thead><tbody>'
        + (rowsHtml || '<tr><td colspan="7" style="text-align:center;padding:20px;color:#999">Tidak ada data</td></tr>')
        + '</tbody></table>'
        + '<div class="footer">Kipin — Sistem Monitoring Absensi &middot; Total '+(no-1)+' baris data</div>'
        + '</body></html>';

    // Buka jendela baru dan langsung print
    var win = window.open('', '_blank', 'width=900,height=700');
    win.document.write(printHtml);
    win.document.close();
    win.focus();
    setTimeout(function(){ win.print(); }, 400);
}
function onFilterDateChange() {
    var dateF = document.getElementById('filterDate').value;
    if (dateF) {
        var parts = dateF.split('-');
        var y = parseInt(parts[0]);
        var m = parseInt(parts[1]);
        var d = parseInt(parts[2]);

        // Sync dropdown Tahun
        var selY = document.getElementById('filterTahun');
        for (var i = 0; i < selY.options.length; i++) {
            if (parseInt(selY.options[i].value) === y) {
                selY.selectedIndex = i;
                break;
            }
        }

        // Sync dropdown Bulan
        document.getElementById('filterBulan').value = m;

        // Kalau mode harian, sync juga dropdown Hari
        if (viewMode === 'harian') {
            populateHari(y, m);
            document.getElementById('filterHari').value = d;
        }

        // Update semua label periode di topbar & tabel
        onPeriodChange();
    }

    applyFiltersAndRender();
}
</script>
</body>
</html>
