<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kipin — Monitoring Absensi</title>
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

        /* ═══ SIDEBAR ═══ */
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
            border-bottom: 1px solid rgba(255,255,255,0.15);
            display: flex; align-items: center; gap: 12px;
        }
        .sb-logo {
            width: 38px; height: 38px;
            background: var(--teal);
            border-radius: 10px;
            display: flex; align-items: center; justify-content: center;
            flex-shrink: 0;
        }
        .sb-logo img { width: 28px; height: 28px; object-fit: contain; }
        .sb-logo-fallback { font-size: 16px; font-weight: 600; color: #fff; }
        .sb-title { font-size: 14px; font-weight: 600; color: #ffffff; line-height: 1.2; }
        .sb-sub { font-size: 11px; color: #94a3b8; margin-top: 2px; }

        .sb-nav { flex: 1; overflow-y: auto; padding: 8px; }
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
        .sb-item svg { width: 17px; height: 17px; stroke: #ffffff; fill: none; stroke-width: 1.8; flex-shrink: 0; transition: stroke .15s; }
        .sb-item-label { font-size: 13px; font-weight: 500; color: #ffffff; transition: color .15s; flex: 1; }
        .sb-badge { font-size: 10px; background: var(--accent); color: #fff; padding: 1px 6px; border-radius: 20px; font-weight: 600; }
        .sb-item:hover { background: rgba(255,255,255,0.07); }
        .sb-item.active {
            background: rgba(99,102,241,0.25);
            box-shadow: inset 3px 0 0 #818cf8, 0 1px 8px rgba(99,102,241,0.2);
        }
        .sb-item.active svg { stroke: #818cf8; }
        .sb-item.active .sb-item-label { color: #818cf8; font-weight: 600; }

        /* View Only badge di sidebar */
        .sb-viewonly {
            margin: 10px 12px 0;
            display: flex; align-items: center; gap: 7px;
            background: rgba(16,185,129,0.15);
            border: 1px solid rgba(16,185,129,0.35);
            border-radius: 8px; padding: 8px 12px;
        }
        .sb-viewonly-dot { width: 7px; height: 7px; border-radius: 50%; background: #10b981; flex-shrink: 0; animation: pulse 2s infinite; }
        .sb-viewonly-txt { font-size: 11px; font-weight: 600; color: #10b981; }
        .sb-viewonly-ico { margin-left: auto; flex-shrink: 0; }
        .sb-viewonly-ico svg { width: 13px; height: 13px; stroke: #10b981; fill: none; stroke-width: 2; }

        .sb-bottom {
            padding: 12px;
            border-top: 1px solid rgba(255,255,255,0.15);
        }
        .sb-bottom-info {
            padding: 10px 12px;
            background: rgba(255,255,255,0.05);
            border-radius: var(--radius-sm);
            border: 1px solid rgba(255,255,255,0.08);
        }
        .sb-bottom-info-title { font-size: 11px; font-weight: 600; color: #ffffff; }
        .sb-bottom-info-sub { font-size: 10px; color: #94a3b8; margin-top: 2px; }

        /* ═══ MAIN AREA ═══ */
        .main-area {
            margin-left: var(--sidebar-w);
            flex: 1; display: flex; flex-direction: column;
            min-height: 100vh; min-width: 0; overflow-x: hidden;
        }

        /* ═══ TOPBAR ═══ */
        .topbar {
            min-height: var(--topbar-h);
            background: #1e293b;
            border-bottom: 1px solid rgba(255,255,255,0.1);
            display: flex; align-items: center; flex-wrap: wrap;
            padding: 8px 20px; gap: 10px;
            position: sticky; top: 0; z-index: 50; flex-shrink: 0;
        }
        .tb-breadcrumb { flex: 1; min-width: 120px; display: flex; align-items: center; gap: 6px; font-size: 13px; }
        .tb-bc-page { font-weight: 600; color: #ffffff; white-space: nowrap; }
        .tb-bc-sep { color: var(--text-light); }
        .tb-bc-sub { color: #94a3b8; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 200px; }
        .tb-right { display: flex; align-items: center; gap: 8px; flex-wrap: wrap; }
        .view-pills { display: flex; gap: 2px; background: #0f172a; border-radius: var(--radius-sm); padding: 3px; }
        .vp-btn {
            padding: 5px 12px; border-radius: 6px; font-size: 12px; font-weight: 500;
            border: none; background: transparent; color: #ffffff;
            cursor: pointer; font-family: inherit; transition: all .15s; white-space: nowrap;
        }
        .vp-btn.active { background: #6366f1; color: #ffffff; font-weight: 700; box-shadow: 0 2px 8px rgba(99,102,241,0.5); }
        .tb-period-selects { display: flex; gap: 6px; }
        .period-select {
            padding: 6px 28px 6px 10px; font-size: 12px; font-weight: 600;
            border: 1px solid rgba(255,255,255,0.35); border-radius: var(--radius-sm);
            background: #1e293b; color: #ffffff; font-family: inherit;
            outline: none; cursor: pointer; appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='10' height='10' viewBox='0 0 24 24' fill='none' stroke='%236b7280' stroke-width='2.5'%3E%3Cpolyline points='6 9 12 15 18 9'/%3E%3C/svg%3E");
            background-repeat: no-repeat; background-position: right 8px center;
        }
        .period-select:focus { border-color: var(--accent); }
        .tb-clock {
            font-family: 'JetBrains Mono', monospace; font-size: 12px;
            color: #ffffff; font-weight: 600; padding: 6px 14px;
            background: linear-gradient(135deg, #059669, #10b981);
            border-radius: var(--radius-sm); border: none;
            box-shadow: 0 2px 10px rgba(16,185,129,0.45); white-space: nowrap;
        }
        .live-dot { display: inline-block; width: 7px; height: 7px; border-radius: 50%; background: #ffffff; margin-right: 6px; animation: pulse 2s infinite; vertical-align: middle; box-shadow: 0 0 6px #fff; }

        /* View Only badge topbar */
        .tb-viewonly {
            display: flex; align-items: center; gap: 6px;
            padding: 5px 12px;
            background: rgba(16,185,129,0.15);
            border: 1px solid rgba(16,185,129,0.35);
            border-radius: var(--radius-sm); white-space: nowrap;
        }
        .tb-viewonly svg { width: 13px; height: 13px; stroke: #10b981; fill: none; stroke-width: 2; flex-shrink: 0; }
        .tb-viewonly span { font-size: 11px; font-weight: 600; color: #10b981; }

        /* ═══ CONTENT ═══ */
        .content { flex: 1; overflow-y: auto; overflow-x: hidden; padding: 20px; min-width: 0; }

        /* ═══ STAT CARDS ═══ */
        .stat-grid { display: grid; grid-template-columns: repeat(5,1fr); gap: 12px; margin-bottom: 20px; }
        .stat-card {
            border-radius: var(--radius); padding: 16px;
            position: relative; overflow: hidden;
            transition: transform .2s, box-shadow .2s;
            box-shadow: 0 4px 16px rgba(0,0,0,0.4), inset 0 1px 0 rgba(255,255,255,0.15);
        }
        .stat-card:hover { transform: translateY(-2px); box-shadow: 0 8px 28px rgba(0,0,0,0.5); }
        .sc-icon { width: 36px; height: 36px; border-radius: 10px; display: flex; align-items: center; justify-content: center; margin-bottom: 14px; }
        .sc-icon svg { width: 18px; height: 18px; fill: none; stroke-width: 1.8; }
        .sc-label { font-size: 11px; font-weight: 500; color: rgba(255,255,255,0.8); margin-bottom: 6px; }
        .sc-val { font-size: 28px; font-weight: 600; letter-spacing: -.03em; line-height: 1; color: #fff; }
        .sc-bar { position: absolute; bottom: 0; left: 0; right: 0; height: 3px; background: rgba(255,255,255,0.3); }
        .c-total  { background: linear-gradient(135deg, #4f46e5, #6366f1); } .c-total  .sc-icon { background: rgba(255,255,255,0.2); } .c-total  .sc-icon svg { stroke: #fff; }
        .c-tepat  { background: linear-gradient(135deg, #15803d, #22c55e); } .c-tepat  .sc-icon { background: rgba(255,255,255,0.2); } .c-tepat  .sc-icon svg { stroke: #fff; }
        .c-lambat { background: linear-gradient(135deg, #b45309, #f59e0b); } .c-lambat .sc-icon { background: rgba(255,255,255,0.2); } .c-lambat .sc-icon svg { stroke: #fff; }
        .c-cepat  { background: linear-gradient(135deg, #be123c, #f43f5e); } .c-cepat  .sc-icon { background: rgba(255,255,255,0.2); } .c-cepat  .sc-icon svg { stroke: #fff; }
        .c-absent { background: linear-gradient(135deg, #6d28d9, #a855f7); } .c-absent .sc-icon { background: rgba(255,255,255,0.2); } .c-absent .sc-icon svg { stroke: #fff; }

        /* ═══ CARD ═══ */
        .card {
            background: var(--surface);
            border: 1px solid var(--border-md);
            border-radius: var(--radius); margin-bottom: 16px; overflow: hidden;
            box-shadow: 0 2px 12px rgba(0,0,0,0.35), 0 0 0 1px rgba(255,255,255,0.05);
        }
        .card-head {
            display: flex; align-items: center; gap: 10px;
            padding: 14px 18px; border-bottom: 1px solid var(--border);
            font-size: 13px; font-weight: 600; color: #ffffff;
        }
        .card-head svg { width: 16px; height: 16px; stroke: #94a3b8; fill: none; stroke-width: 1.8; }
        .card-chip {
            margin-left: auto; font-size: 11px; padding: 2px 10px;
            border-radius: 20px; background: var(--accent-light);
            color: #818cf8; border: 1px solid rgba(99,102,241,0.3); font-weight: 600;
        }
        .card-body { padding: 16px 18px; }

        /* ═══ LAYOUT ═══ */
        .dash-two-col { display: grid; grid-template-columns: 300px 1fr; gap: 16px; margin-bottom: 16px; }

        /* ═══ DONUT ═══ */
        .donut-inner { padding: 16px 18px; }
        .donut-canvas-wrap { position: relative; width: 180px; height: 180px; margin: 0 auto 16px; }
        .donut-center { position: absolute; top: 50%; left: 50%; transform: translate(-50%,-50%); text-align: center; }
        .donut-center-num { font-size: 26px; font-weight: 600; color: #ffffff; line-height: 1; }
        .donut-center-lbl { font-size: 11px; color: #94a3b8; margin-top: 2px; }
        .donut-legends { display: flex; flex-direction: column; gap: 7px; }
        .dl-item { display: flex; align-items: center; gap: 8px; padding: 8px 10px; border-radius: var(--radius-sm); cursor: pointer; transition: background .15s; background: rgba(255,255,255,0.04); }
        .dl-item:hover { background: rgba(255,255,255,0.08); }
        .dl-dot { width: 10px; height: 10px; border-radius: 3px; flex-shrink: 0; }
        .dl-label { font-size: 12px; color: #e2e8f0; flex: 1; }
        .dl-val { font-size: 13px; font-weight: 600; color: #f1f5f9; }
        .dl-pct { font-size: 10px; color: #94a3b8; margin-left: 3px; }

        /* ═══ QUICK TABLE ═══ */
        .tbl-wrap { overflow-x: auto; max-width: 100%; }
        table { width: 100%; border-collapse: collapse; font-size: 13px; min-width: 480px; }
        thead tr { background: #0f172a; }
        th { padding: 10px 16px; text-align: left; font-size: 10px; font-weight: 700; color: #ffffff; border-bottom: 1px solid rgba(255,255,255,0.15); letter-spacing: .06em; text-transform: uppercase; white-space: nowrap; }
        td { padding: 11px 16px; border-bottom: 1px solid rgba(255,255,255,0.08); vertical-align: middle; color: #ffffff; }
        tbody tr:last-child td { border-bottom: none; }
        tbody tr:hover td { background: rgba(99,102,241,0.08); }
        tbody tr.row-absent td { background: rgba(124,58,237,0.08); }

        /* ═══ FILTER ═══ */
        .filter-row { display: grid; grid-template-columns: 1fr 1fr 1fr auto; gap: 10px; align-items: end; }
        .f-group { display: flex; flex-direction: column; gap: 4px; }
        .f-label { font-size: 10px; font-weight: 600; color: #94a3b8; text-transform: uppercase; letter-spacing: .07em; }
        .f-input {
            padding: 8px 10px; font-size: 13px; border: 1px solid rgba(255,255,255,0.12);
            border-radius: var(--radius-sm); background: #0f172a; color: #ffffff;
            font-family: inherit; outline: none; transition: border-color .15s;
        }
        .f-input:focus { border-color: var(--accent); }
        .btn-reset {
            padding: 8px 14px; font-size: 12px; font-weight: 500;
            background: var(--bg); color: #94a3b8; border: 1px solid var(--border-md);
            border-radius: var(--radius-sm); cursor: pointer; font-family: inherit; white-space: nowrap; transition: all .15s;
        }
        .btn-reset:hover { border-color: var(--accent); color: var(--accent); background: var(--accent-light); }
        .btn-print {
            display: flex; align-items: center; gap: 7px; padding: 7px 16px;
            font-size: 12px; font-weight: 600; background: var(--bg); color: #cbd5e1;
            border: 1px solid var(--border-md); border-radius: var(--radius-sm);
            cursor: pointer; font-family: inherit; transition: all .15s; white-space: nowrap;
        }
        .btn-print:hover { border-color: var(--accent); color: var(--accent); background: var(--accent-light); }
        .btn-print svg { width: 14px; height: 14px; stroke: currentColor; fill: none; stroke-width: 2; }

        /* ═══ TABLE EXTRAS ═══ */
        .karyawan-nav-strip {
            display: flex; align-items: center; gap: 6px;
            padding: 10px 18px; background: var(--bg); border-bottom: 1px solid var(--border); overflow-x: auto;
        }
        .karyawan-nav-label { font-size: 11px; font-weight: 600; color: #ffffff; white-space: nowrap; flex-shrink: 0; }
        .karyawan-nav-btn {
            padding: 4px 11px; border-radius: 20px; font-size: 11px; font-weight: 600;
            border: 1px solid rgba(255,255,255,0.15); background: rgba(255,255,255,0.06);
            color: #e2e8f0; cursor: pointer; font-family: inherit; white-space: nowrap; flex-shrink: 0; transition: all .15s;
        }
        .karyawan-nav-btn:hover { border-color: #818cf8; color: #fff; background: rgba(99,102,241,0.2); }
        .karyawan-nav-btn.active { background: var(--accent); color: #fff; border-color: var(--accent); font-weight: 700; box-shadow: 0 0 10px rgba(99,102,241,0.4); }

        .emp-table-header {
            display: flex; align-items: center; gap: 14px;
            padding: 14px 18px; background: linear-gradient(90deg, rgba(99,102,241,0.15) 0%, rgba(30,41,59,0.8) 100%);
            border-bottom: 1px solid rgba(99,102,241,0.2); border-left: 3px solid #6366f1;
        }
        .emp-table-avatar { width: 48px; height: 48px; border-radius: 12px; color: #fff; display: flex; align-items: center; justify-content: center; font-size: 16px; font-weight: 600; flex-shrink: 0; }
        .emp-table-name { font-size: 15px; font-weight: 700; color: #ffffff; }
        .emp-table-meta { display: flex; align-items: center; gap: 8px; margin-top: 4px; flex-wrap: wrap; }
        .emp-table-pin { font-family: 'JetBrains Mono', monospace; font-size: 11px; font-weight: 600; background: var(--accent); color: #fff; padding: 2px 8px; border-radius: 5px; }
        .emp-table-days { font-size: 12px; color: #ffffff; font-weight: 500; }
        .emp-table-stats { display: flex; gap: 6px; flex-wrap: wrap; margin-left: auto; }
        .esp { display: flex; align-items: center; gap: 4px; padding: 4px 10px; border-radius: 20px; font-size: 11px; font-weight: 700; }
        .esp-tepat  { background: rgba(34,197,94,0.2);  color: #4ade80; border: 1px solid rgba(34,197,94,0.3); }
        .esp-lambat { background: rgba(245,158,11,0.2); color: #fbbf24; border: 1px solid rgba(245,158,11,0.3); }
        .esp-cepat  { background: rgba(244,63,94,0.2);  color: #fb7185; border: 1px solid rgba(244,63,94,0.3); }
        .esp-absent { background: rgba(168,85,247,0.2); color: #c084fc; border: 1px solid rgba(168,85,247,0.3); }

        /* ═══ BADGES ═══ */
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
        .emp-name-text { font-size: 13px; font-weight: 600; color: #ffffff; }
        .emp-pin-text  { font-family: 'JetBrains Mono', monospace; font-size: 10px; color: #94a3b8; }

        .time-badge { display: inline-flex; align-items: center; gap: 5px; padding: 4px 9px; border-radius: 6px; font-family: 'JetBrains Mono', monospace; font-size: 12px; font-weight: 500; white-space: nowrap; }
        .time-badge svg { width: 11px; height: 11px; flex-shrink: 0; fill: none; stroke-width: 2; }
        .time-ok    { background: rgba(34,197,94,0.2);  color: #4ade80; border: 1px solid rgba(34,197,94,0.3);  } .time-ok    svg { stroke: #4ade80; }
        .time-late  { background: rgba(245,158,11,0.2); color: #fbbf24; border: 1px solid rgba(245,158,11,0.3); } .time-late  svg { stroke: #fbbf24; }
        .time-early { background: rgba(244,63,94,0.2);  color: #fb7185; border: 1px solid rgba(244,63,94,0.3);  } .time-early svg { stroke: #fb7185; }
        .time-none  { font-size: 11px; color: var(--text-light); font-style: italic; }
        .badge-absent { display: inline-flex; align-items: center; gap: 5px; padding: 4px 9px; border-radius: 6px; background: rgba(168,85,247,0.2); color: #c084fc; border: 1px solid rgba(168,85,247,0.4); font-size: 12px; font-weight: 500; }
        .status-pill { font-size: 11px; font-weight: 600; padding: 3px 9px; border-radius: 20px; }
        .sp-tepat  { background: rgba(34,197,94,0.2);  color: #4ade80; border: 1px solid rgba(34,197,94,0.4);  }
        .sp-lambat { background: rgba(245,158,11,0.2); color: #fbbf24; border: 1px solid rgba(245,158,11,0.4); }
        .sp-cepat  { background: rgba(244,63,94,0.2);  color: #fb7185; border: 1px solid rgba(244,63,94,0.4);  }
        .sp-absent { background: rgba(168,85,247,0.2); color: #c084fc; border: 1px solid rgba(168,85,247,0.4); }

        /* ═══ PAGINATION ═══ */
        .pg-bar { display: flex; align-items: center; justify-content: space-between; padding: 10px 18px; flex-wrap: wrap; gap: 8px; border-top: 1px solid var(--border); background: var(--bg); }
        .pg-info { font-size: 12px; color: #94a3b8; }
        .pg-btns { display: flex; gap: 3px; flex-wrap: wrap; }
        .pg-btn { min-width: 30px; height: 30px; padding: 0 6px; border: 1px solid var(--border-md); background: var(--surface); color: #ffffff; border-radius: 7px; font-size: 12px; font-weight: 500; cursor: pointer; display: flex; align-items: center; justify-content: center; font-family: inherit; transition: all .15s; }
        .pg-btn:hover:not(:disabled) { border-color: var(--accent); color: var(--accent); }
        .pg-btn:disabled { opacity: .35; cursor: not-allowed; }
        .pg-btn.pg-active { background: var(--accent); color: #fff; border-color: var(--accent); font-weight: 600; }
        .pg-ellipsis { font-size: 13px; padding: 0 3px; color: var(--text-light); }

        /* ═══ PERFORMA ═══ */
        .page { display: none; } .page.active { display: block; }
        .emp-selector-grid { display: grid; grid-template-columns: repeat(auto-fill,minmax(200px,1fr)); gap: 10px; padding: 14px 18px; }
        .emp-card { background: var(--bg); border: 1.5px solid var(--border); border-radius: var(--radius); padding: 14px; cursor: pointer; transition: all .2s; position: relative; overflow: hidden; }
        .emp-card:hover { border-color: var(--accent); background: var(--surface); box-shadow: 0 4px 16px rgba(99,102,241,0.1); }
        .emp-card.selected { border-color: var(--accent); background: var(--surface); }
        .emp-card.selected::before { content: ''; position: absolute; top: 0; left: 0; right: 0; height: 3px; background: var(--accent); }
        .emp-card-badge { position: absolute; top: 10px; right: 10px; width: 18px; height: 18px; border-radius: 50%; background: var(--accent); display: none; align-items: center; justify-content: center; }
        .emp-card.selected .emp-card-badge { display: flex; }
        .emp-card-badge svg { width: 10px; height: 10px; stroke: #fff; fill: none; stroke-width: 3; }
        .emp-card-header { display: flex; align-items: center; gap: 10px; margin-bottom: 12px; }
        .emp-card-avatar { width: 38px; height: 38px; border-radius: 10px; color: #fff; display: flex; align-items: center; justify-content: center; font-size: 13px; font-weight: 600; flex-shrink: 0; }
        .emp-card-name { font-size: 13px; font-weight: 600; color: #ffffff; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; max-width: 120px; }
        .emp-card-pin  { font-family: 'JetBrains Mono', monospace; font-size: 10px; color: #94a3b8; margin-top: 2px; }
        .emp-card-stats { display: grid; grid-template-columns: repeat(4,1fr); gap: 4px; }
        .emp-card-stat { text-align: center; padding: 6px 2px; border-radius: 7px; background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.08); }
        .emp-card-stat-num { font-size: 14px; font-weight: 600; }
        .emp-card-stat-lbl { font-size: 8px; font-weight: 600; color: #94a3b8; text-transform: uppercase; letter-spacing: .04em; margin-top: 1px; }

        .chart-panel { display: none; } .chart-panel.show { display: block; }
        .bar-chart-topbar { display: flex; align-items: center; gap: 12px; padding: 14px 18px; border-bottom: 1px solid var(--border); flex-wrap: wrap; }
        .bar-chart-emp-info { display: flex; align-items: center; gap: 12px; flex: 1; }
        .bar-chart-emp-avatar { width: 40px; height: 40px; border-radius: 10px; color: #fff; display: flex; align-items: center; justify-content: center; font-size: 14px; font-weight: 600; flex-shrink: 0; }
        .bar-chart-emp-name { font-size: 15px; font-weight: 600; color: #ffffff; }
        .bar-chart-emp-pin  { font-family: 'JetBrains Mono', monospace; font-size: 11px; color: #94a3b8; margin-top: 2px; }
        .bar-mode-btns { display: flex; gap: 5px; }
        .bar-mode-btn { padding: 6px 14px; border-radius: var(--radius-sm); font-size: 11px; font-weight: 500; border: 1px solid var(--border-md); background: var(--bg); color: #94a3b8; cursor: pointer; font-family: inherit; transition: all .15s; }
        .bar-mode-btn:hover { border-color: var(--accent); color: var(--accent); }
        .bar-mode-btn.active { background: var(--accent); color: #fff; border-color: var(--accent); }
        .bar-chart-body { padding: 16px 18px 12px; }
        .bar-chart-canvas-wrap { position: relative; height: 360px; }
        .chart-legend-custom { display: flex; flex-wrap: wrap; gap: 10px; padding: 8px 18px 12px; }
        .cl-item { display: flex; align-items: center; gap: 5px; font-size: 11px; color: #94a3b8; }
        .cl-dot  { width: 10px; height: 10px; border-radius: 3px; flex-shrink: 0; }
        .cl-line { width: 16px; height: 3px; border-radius: 2px; flex-shrink: 0; }

        .yearly-stat-strip { display: flex; gap: 8px; padding: 10px 18px 14px; flex-wrap: wrap; border-top: 1px solid var(--border); }
        .ys-pill { display: flex; align-items: center; gap: 10px; padding: 10px 14px; border-radius: var(--radius-sm); flex: 1; min-width: 100px; border: 1px solid transparent; }
        .ys-hadir  { background: rgba(34,197,94,0.15);  border-color: rgba(34,197,94,0.3);  } .ys-hadir  .ys-val { color: #4ade80; }
        .ys-lambat { background: rgba(245,158,11,0.15); border-color: rgba(245,158,11,0.3); } .ys-lambat .ys-val { color: #fbbf24; }
        .ys-cepat  { background: rgba(244,63,94,0.15);  border-color: rgba(244,63,94,0.3);  } .ys-cepat  .ys-val { color: #fb7185; }
        .ys-tepat  { background: rgba(99,102,241,0.15); border-color: rgba(99,102,241,0.3); } .ys-tepat  .ys-val { color: #818cf8; }
        .ys-absent { background: rgba(168,85,247,0.15); border-color: rgba(168,85,247,0.3); } .ys-absent .ys-val { color: #c084fc; }
        .ys-val { font-size: 20px; font-weight: 600; line-height: 1; }
        .ys-lbl { font-size: 11px; color: #94a3b8; margin-top: 2px; }

        .chart-select-hint { display: flex; flex-direction: column; align-items: center; justify-content: center; padding: 3rem 2rem; gap: 10px; }
        .csh-icon { width: 56px; height: 56px; border-radius: 14px; background: var(--accent-light); display: flex; align-items: center; justify-content: center; }
        .csh-icon svg { width: 26px; height: 26px; stroke: #818cf8; fill: none; stroke-width: 1.5; }
        .csh-text { font-size: 14px; font-weight: 500; color: #94a3b8; }
        .csh-sub  { font-size: 12px; color: var(--text-light); }

        /* ═══ EMPTY ═══ */
        .empty { text-align: center; padding: 3rem 1rem; }
        .empty-icon { font-size: 36px; margin-bottom: 12px; }
        .empty-text { font-size: 13px; font-weight: 500; color: #94a3b8; }
        .empty-sub  { font-size: 12px; color: var(--text-light); margin-top: 5px; }

        /* ═══ ANIMATIONS ═══ */
        @keyframes spin  { to { transform: rotate(360deg); } }
        @keyframes pulse { 0%,100%{opacity:1}50%{opacity:.3} }
        @keyframes rowIn { from{opacity:0;transform:translateY(5px)}to{opacity:1;transform:translateY(0)} }

        /* ═══ PRINT ═══ */
        @media print {
            body > * { display: none !important; }
            #printArea { display: block !important; }
            @page { margin: 1.5cm; size: A4; }
        }

        /* ═══ RESPONSIVE ═══ */
        @media (max-width: 1279px) { :root { --sidebar-w: 200px; } .stat-grid { grid-template-columns: repeat(3,1fr); } }
        @media (max-width: 1023px) {
            :root { --sidebar-w: 60px; }
            .sb-brand { padding: 14px 10px; justify-content: center; }
            .sb-brand-text, .sb-section-label, .sb-item-label, .sb-badge, .sb-viewonly-txt, .sb-viewonly-ico, .sb-bottom-info-sub { display: none; }
            .sb-logo { margin-bottom: 0; }
            .sb-viewonly { justify-content: center; padding: 8px; }
            .sb-item { justify-content: center; padding: 10px; }
            .sb-bottom { padding: 8px; }
            .sb-bottom-info { padding: 8px; text-align: center; }
            .stat-grid { grid-template-columns: repeat(3,1fr); }
            .dash-two-col { grid-template-columns: 1fr; }
            .content { padding: 16px; }
        }
        @media (max-width: 899px) {
            .stat-grid { grid-template-columns: repeat(2,1fr); }
            .filter-row { grid-template-columns: 1fr 1fr; }
            .tb-period-selects { display: none; }
        }
        @media (max-width: 767px) {
            :root { --sidebar-w: 0px; }
            .sidebar { transform: translateX(-230px); width: 230px; transition: transform .25s ease; z-index: 200; }
            .sidebar.open { transform: translateX(0); box-shadow: 4px 0 24px rgba(0,0,0,0.5); }
            .sidebar.open .sb-brand-text,
            .sidebar.open .sb-section-label,
            .sidebar.open .sb-item-label,
            .sidebar.open .sb-badge,
            .sidebar.open .sb-viewonly-txt,
            .sidebar.open .sb-viewonly-ico,
            .sidebar.open .sb-bottom-info-sub { display: block; }
            .sidebar.open .sb-viewonly { justify-content: flex-start; padding: 8px 12px; }
            .sidebar.open .sb-item { justify-content: flex-start; padding: 9px 10px; }
            .main-area { margin-left: 0; }
            .hamburger { display: flex !important; }
            .stat-grid { grid-template-columns: repeat(2,1fr); }
            .filter-row { grid-template-columns: 1fr; }
            .topbar { padding: 8px 14px; }
            .content { padding: 12px; }
            .dash-two-col { grid-template-columns: 1fr; }
            .tb-bc-sub, .tb-bc-sep, .tb-viewonly { display: none; }
        }
        .sidebar-overlay { display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 199; }
        .sidebar-overlay.show { display: block; }
        .hamburger {
            display: none; align-items: center; justify-content: center;
            width: 36px; height: 36px; border: none; background: transparent;
            cursor: pointer; border-radius: var(--radius-sm); color: #94a3b8; flex-shrink: 0; transition: background .15s;
        }
        .hamburger:hover { background: var(--bg); }
        .hamburger svg { width: 20px; height: 20px; stroke: currentColor; fill: none; stroke-width: 2; }
    </style>
</head>
<body>

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

    <!-- View Only badge -->
    <div class="sb-viewonly">
        <div class="sb-viewonly-dot"></div>
        <span class="sb-viewonly-txt">Tampilan Publik</span>
        <div class="sb-viewonly-ico">
            <svg viewBox="0 0 24 24"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
        </div>
    </div>

    <nav class="sb-nav">
        <div class="sb-section-label">Menu</div>
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
    </nav>

    <div class="sb-bottom">
        <div class="sb-bottom-info">
            <div class="sb-bottom-info-title">Read-only access</div>
            <div class="sb-bottom-info-sub">Data diperbarui otomatis</div>
        </div>
        <a href="{{ route('login') }}" style="display:flex;align-items:center;gap:10px;padding:9px 12px;border-radius:8px;background:rgba(225,29,72,0.12);border:1px solid rgba(225,29,72,0.3);text-decoration:none;transition:all .15s;margin-top:10px;" onmouseover="this.style.background='rgba(225,29,72,0.22)'" onmouseout="this.style.background='rgba(225,29,72,0.12)'">
    <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="#fb7185" stroke-width="1.8"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
    <span style="font-size:13px;font-weight:500;color:#fb7185;" class="sb-item-label">Logout</span>
</a>
    </div>
</aside>

<!-- ═══ MAIN AREA ═══ -->
<div class="main-area">

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
                    <button class="vp-btn active" id="vpBulanan" onclick="setViewMode('bulanan')">Bulanan</button>
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
            <div class="tb-viewonly">
                <svg viewBox="0 0 24 24"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                <span>View Only</span>
            </div>
            <div class="tb-clock"><span class="live-dot"></span><span id="liveClock">--:--:--</span></div>
        </div>
    </header>

    <div class="content">

        <!-- ═══ PAGE: DASHBOARD ═══ -->
        <div class="page active" id="pageDashboard">

            @if(session('success'))
            <div style="background:rgba(22,163,74,0.15);border:1px solid rgba(22,163,74,0.3);color:#4ade80;border-radius:var(--radius-sm);padding:10px 14px;font-size:13px;margin-bottom:14px;font-weight:500" id="alertSuccess">{!! session('success') !!}</div>
            @endif
            @if(session('error'))
            <div style="background:rgba(225,29,72,0.15);border:1px solid rgba(225,29,72,0.3);color:#fb7185;border-radius:var(--radius-sm);padding:10px 14px;font-size:13px;margin-bottom:14px;font-weight:500" id="alertError">{!! session('error') !!}</div>
            @endif
            <script>
                setTimeout(function(){
                    ['alertSuccess','alertError'].forEach(function(id){
                        var el=document.getElementById(id);
                        if(el){el.style.transition='opacity .6s';el.style.opacity='0';setTimeout(function(){el.remove();},600);}
                    });
                },8000);
            </script>

            <div class="stat-grid">
                <div class="stat-card c-total">
                    <div class="sc-icon"><svg viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="18" rx="2"/><path d="M16 2v4M8 2v4M3 10h18"/></svg></div>
                    <div class="sc-label">Total Absensi</div>
                    <div class="sc-val" id="statTotal">0</div><div class="sc-bar"></div>
                </div>
                <div class="stat-card c-tepat">
                    <div class="sc-icon"><svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="9"/><path d="M9 12l2 2 4-4"/></svg></div>
                    <div class="sc-label">Tepat Waktu</div>
                    <div class="sc-val" id="statTepat">0</div><div class="sc-bar"></div>
                </div>
                <div class="stat-card c-lambat">
                    <div class="sc-icon"><svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="9"/><path d="M12 8v4"/><circle cx="12" cy="16" r=".5" fill="currentColor"/></svg></div>
                    <div class="sc-label">Terlambat</div>
                    <div class="sc-val" id="statLambat">0</div><div class="sc-bar"></div>
                </div>
                <div class="stat-card c-cepat">
                    <div class="sc-icon"><svg viewBox="0 0 24 24"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg></div>
                    <div class="sc-label">Pulang Cepat</div>
                    <div class="sc-val" id="statCepat">0</div><div class="sc-bar"></div>
                </div>
                <div class="stat-card c-absent">
                    <div class="sc-icon"><svg viewBox="0 0 24 24"><circle cx="12" cy="8" r="4"/><path d="M4 20c0-4 3.58-7 8-7s8 3 8 7"/><line x1="17" y1="11" x2="23" y2="11"/></svg></div>
                    <div class="sc-label">Tidak Masuk</div>
                    <div class="sc-val" id="statAbsent">0</div><div class="sc-bar"></div>
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
                            Ringkasan Karyawan — <span id="chartPeriodLabel" style="color:#818cf8;margin-left:4px">—</span>
                        </div>
                        <div id="dashQuickTable" style="padding:0">
                            <div class="empty"><div class="empty-icon">📊</div><div class="empty-text">Pilih periode untuk melihat data</div></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

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
                    Data Presensi — <span id="tablePeriodLabel" style="color:#818cf8;margin-left:4px">—</span>
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
                <div class="tbl-wrap">
                    <table>
                        <thead id="tableHead"><tr><th>No</th><th>Tanggal</th><th>Jam Masuk</th><th>Jam Pulang</th><th>Status</th></tr></thead>
                        <tbody id="tableBody"></tbody>
                    </table>
                </div>
                <div class="pg-bar" id="pgBar">
                    <div class="pg-info" id="pgInfo">—</div>
                    <div class="pg-btns" id="pgBtns"></div>
                </div>
            </div>
        </div>

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
                <span style="font-size:12px;color:#94a3b8;margin-left:auto" id="perfPeriodHint"></span>
            </div>
            <div class="card">
                <div class="card-head">
                    <svg viewBox="0 0 24 24"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                    Pilih Karyawan
                    <span class="card-chip" id="empCountChip">0 karyawan</span>
                    <span style="font-size:11px;color:#94a3b8;margin-left:4px">← klik untuk lihat chart</span>
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
        </div>

        <div style="text-align:center;padding:1rem 0 .5rem;font-size:12px;color:#475569">
            &copy; {{ date('Y') }} <span style="color:#0d9488;font-weight:600">Kipin</span> &mdash; Sistem Monitoring Absensi &middot; Sakera &middot; <span style="color:#10b981;font-weight:600">Publik View</span>
        </div>

    </div>
</div>

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
<script>
/* ══ KONSTANTA ══ */
var NAMA_BULAN   = ['','Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
var NAMA_BULAN_S = ['','Jan','Feb','Mar','Apr','Mei','Jun','Jul','Ags','Sep','Okt','Nov','Des'];
var NAMA_HARI    = ['Minggu','Senin','Selasa','Rabu','Kamis','Jumat','Sabtu'];
var HARI_CSS     = ['day-minggu','day-senin','day-selasa','day-rabu','day-kamis','day-jumat','day-sabtu'];
var DONUT_COLORS = ['#16a34a','#d97706','#e11d48','#6366f1','#7c3aed'];
var DONUT_LABELS = ['Tepat Waktu','Terlambat','Pulang Cepat','Tepat Pulang','Tidak Masuk'];
var DONUT_TXT    = ['#4ade80','#fbbf24','#fb7185','#818cf8','#c084fc'];
var BC = {
    hadir:  { hex:'#6366f1', bg:'rgba(99,102,241,0.70)',  border:'#6366f1', label:'Total Hadir',    type:'bar'  },
    tepat:  { hex:'#16a34a', bg:'rgba(22,163,74,0.70)',   border:'#16a34a', label:'Tepat Waktu',    type:'bar'  },
    absent: { hex:'#7c3aed', bg:'rgba(124,58,237,0.70)',  border:'#7c3aed', label:'Tidak Masuk',    type:'bar'  },
    lambat: { hex:'#d97706', bg:'rgba(217,119,6,0.80)',   border:'#d97706', label:'Terlambat',      type:'bar'  },
    cepat:  { hex:'#e11d48', bg:'rgba(225,29,72,0.80)',   border:'#e11d48', label:'Pulang Cepat',   type:'bar'  },
    tren:   { hex:'#0d9488', bg:'rgba(13,148,136,0.10)',  border:'#0d9488', label:'Tren Kehadiran', type:'line' }
};
var PALETTE = ['#6366f1','#0d9488','#d97706','#e11d48','#16a34a','#7c3aed','#0891b2','#dc2626','#65a30d','#9333ea','#2563eb','#ea580c','#059669','#be185d','#ca8a04'];

var RAW_DATA = [];
try { RAW_DATA = JSON.parse(document.getElementById('rawDataScript').textContent); } catch(e){}

var viewMode       = 'bulanan';
var currentEmpPage = 1;
var donutChart     = null;
var empBarChart    = null;
var selectedEmpPin = null;
var selectedEmpColor = '#6366f1';
var barMode        = 'all';
var currentPage    = 'dashboard';

function toggleSidebar(){
    document.getElementById('sidebar').classList.toggle('open');
    document.getElementById('sidebarOverlay').classList.toggle('show');
}
function closeSidebarMobile(){ if(window.innerWidth<=767){ document.getElementById('sidebar').classList.remove('open'); document.getElementById('sidebarOverlay').classList.remove('show'); } }

function tick(){ document.getElementById('liveClock').textContent=new Date().toLocaleTimeString('id-ID',{hour:'2-digit',minute:'2-digit',second:'2-digit'}); }
tick(); setInterval(tick,1000);

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
        var best=0; Object.keys(mc).forEach(function(m){if(mc[m]>best){best=mc[m];bestMonth=parseInt(m);}});
        if(best===0){RAW_DATA.forEach(function(r){mc[r.bulan]=(mc[r.bulan]||0)+1;});Object.keys(mc).forEach(function(m){if(mc[m]>best){best=mc[m];bestMonth=parseInt(m);}});}
    }
    document.getElementById('filterBulan').value=bestMonth;
    document.getElementById('filterBulanPerf').value=bestMonth;
    document.getElementById('sbDataBadge').textContent=RAW_DATA.length;
    initDonut(); onPeriodChange();
})();

function switchPage(p){
    closeSidebarMobile(); currentPage=p;
    ['pageDashboard','pageData','pagePerforma'].forEach(function(id){document.getElementById(id).classList.remove('active');});
    ['sbDashboard','sbData','sbPerforma'].forEach(function(id){var el=document.getElementById(id);if(el)el.classList.remove('active');});
    document.getElementById('page'+p.charAt(0).toUpperCase()+p.slice(1)).classList.add('active');
    var sbEl=document.getElementById('sb'+p.charAt(0).toUpperCase()+p.slice(1)); if(sbEl) sbEl.classList.add('active');
    var names={dashboard:'Dashboard',data:'Data Presensi',performa:'Chart Performa'};
    var subs={dashboard:'Ringkasan absensi',data:'Tabel lengkap',performa:'Analisis karyawan'};
    document.getElementById('tbPageName').textContent=names[p]||p;
    document.getElementById('tbPageSub').textContent=subs[p]||'';
    document.getElementById('topbarPeriod').style.display=(p==='dashboard'||p==='data')?'flex':'none';
    if(p==='performa') renderPerfPage();
    if(p==='data') applyFiltersAndRender();
}

function getNamaHari(tglStr){ var pts=tglStr.split('-'); return NAMA_HARI[new Date(+pts[0],+pts[1]-1,+pts[2]).getDay()]; }
function getHariCssClass(tglStr){ var pts=tglStr.split('-'); return HARI_CSS[new Date(+pts[0],+pts[1]-1,+pts[2]).getDay()]; }

/* ══ DONUT ══ */
function initDonut(){
    var ctx=document.getElementById('donutChart').getContext('2d');
    donutChart=new Chart(ctx,{type:'doughnut',data:{labels:DONUT_LABELS,datasets:[{data:[0,0,0,0,0],backgroundColor:DONUT_COLORS,borderColor:'#1e293b',borderWidth:3,hoverOffset:10}]},
        options:{responsive:true,maintainAspectRatio:false,cutout:'72%',animation:{animateScale:true,duration:600},
            plugins:{legend:{display:false},tooltip:{callbacks:{label:function(c){var t=c.dataset.data.reduce(function(a,b){return a+b;},0);return ' '+c.label+': '+c.raw+' ('+(t>0?Math.round(c.raw/t*100):0)+'%)';}},padding:10,cornerRadius:8,backgroundColor:'rgba(15,23,42,0.95)',titleColor:'#e2e8f0',bodyColor:'#cbd5e1'}},
            onHover:function(e,els){onDonutHover(els);}}});
}
function onDonutHover(els){
    var d=donutChart.data.datasets[0].data,t=d.reduce(function(a,b){return a+b;},0);
    var nE=document.getElementById('donutCenterNum'),lE=document.getElementById('donutCenterLbl');
    if(els.length){nE.textContent=d[els[0].index];nE.style.color=DONUT_COLORS[els[0].index];lE.textContent=DONUT_LABELS[els[0].index];}
    else{nE.textContent=t;nE.style.color='#ffffff';lE.textContent='total';}
}
function updateDonut(vals){
    if(!donutChart) return;
    var total=vals.reduce(function(a,b){return a+b;},0);
    donutChart.data.datasets[0].data=vals; donutChart.update();
    document.getElementById('donutCenterNum').textContent=total;
    document.getElementById('donutCenterNum').style.color='#ffffff';
    document.getElementById('donutCenterLbl').textContent='total';
    renderLegend(vals,total);
}
function renderLegend(vals,total){
    var c=document.getElementById('donutLegends'); c.innerHTML='';
    DONUT_LABELS.forEach(function(lbl,i){
        var pct=total>0?Math.round(vals[i]/total*100):0;
        var div=document.createElement('div'); div.className='dl-item';
        div.innerHTML='<span class="dl-dot" style="background:'+DONUT_COLORS[i]+'"></span><span class="dl-label">'+lbl+'</span><span class="dl-val" style="color:'+DONUT_TXT[i]+'">'+vals[i]+'<span class="dl-pct">('+pct+'%)</span></span>';
        div.setAttribute('onmouseover','hoverDonut('+i+')'); div.setAttribute('onmouseout','resetDonut()');
        c.appendChild(div);
    });
}
function hoverDonut(i){if(!donutChart)return;var d=donutChart.data.datasets[0].data;donutChart.setActiveElements([{datasetIndex:0,index:i}]);donutChart.tooltip.setActiveElements([{datasetIndex:0,index:i}],{x:0,y:0});donutChart.update();document.getElementById('donutCenterNum').textContent=d[i];document.getElementById('donutCenterNum').style.color=DONUT_COLORS[i];document.getElementById('donutCenterLbl').textContent=DONUT_LABELS[i];}
function resetDonut(){if(!donutChart)return;var d=donutChart.data.datasets[0].data,t=d.reduce(function(a,b){return a+b;},0);donutChart.setActiveElements([]);donutChart.tooltip.setActiveElements([],{x:0,y:0});donutChart.update();document.getElementById('donutCenterNum').textContent=t;document.getElementById('donutCenterNum').style.color='#ffffff';document.getElementById('donutCenterLbl').textContent='total';}

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
    return{tahun:parseInt(document.getElementById('filterTahun').value)||new Date().getFullYear(),bulan:parseInt(document.getElementById('filterBulan').value)||new Date().getMonth()+1,minggu:parseInt(document.getElementById('filterMinggu').value)||1,hari:parseInt(document.getElementById('filterHari').value)||1};
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
function viewLabel(){return{harian:'Harian',mingguan:'Mingguan',bulanan:'Bulanan',tahunan:'Tahunan'}[viewMode];}

function onPeriodChange(){
    var p=getPeriod(),lbl=periodLabel(p);
    if(viewMode==='harian') populateHari(p.tahun,p.bulan);
    ['chartPeriodLabel','tablePeriodLabel'].forEach(function(id){var el=document.getElementById(id);if(el)el.textContent=lbl;});
    var subEl=document.getElementById('tbPageSub');
    if(subEl&&(currentPage==='dashboard'||currentPage==='data')) subEl.textContent=lbl;
    document.getElementById('chartViewChip').textContent=viewLabel();
    var pr=filterByPeriod(RAW_DATA,p);
    var cTepat=pr.filter(function(r){return r.isMasuk&&!r.terlambat;}).length;
    var cLambat=pr.filter(function(r){return r.terlambat;}).length;
    var cCepat=pr.filter(function(r){return r.pulangCepat;}).length;
    var cAbsent=hitungAbsen(pr,p);
    ['statTotal','statTepat','statLambat','statCepat','statAbsent'].forEach(function(id,i){
        var el=document.getElementById(id); el.textContent=[pr.length,cTepat,cLambat,cCepat,cAbsent][i];
    });
    var cPulangTepat=pr.filter(function(r){return !r.isMasuk&&!r.pulangCepat;}).length;
    updateDonut([cTepat,cLambat,cCepat,cPulangTepat,cAbsent]);
    renderDashQuickTable(pr,p);
    currentEmpPage=1;
    if(currentPage==='data'){
        if(viewMode==='harian') renderDailyAllEmployees(pr,p);
        else renderTable(buildGrouped(pr,p));
    }
}

function renderDashQuickTable(pr,p){
    var wrap=document.getElementById('dashQuickTable');
    var allK={}; RAW_DATA.forEach(function(r){if(!allK[r.pin])allK[r.pin]=r.nama;});
    var emps=Object.keys(allK).map(function(pin){
        var empR=pr.filter(function(r){return r.pin===pin;});
        return{pin:pin,nama:allK[pin],masuk:empR.filter(function(r){return r.isMasuk;}).length,lambat:empR.filter(function(r){return r.terlambat;}).length};
    }).sort(function(a,b){return a.nama<b.nama?-1:1;}).slice(0,8);
    if(!emps.length){wrap.innerHTML='<div class="empty"><div class="empty-icon">📋</div><div class="empty-text">Tidak ada data</div></div>';return;}
    var html='<table style="width:100%;border-collapse:collapse;font-size:12px"><thead><tr style="background:#0f172a"><th style="padding:9px 14px;text-align:left;font-size:10px;font-weight:700;color:#ffffff;border-bottom:1px solid rgba(255,255,255,0.1);text-transform:uppercase;letter-spacing:.07em">Karyawan</th><th style="padding:9px 14px;text-align:center;font-size:10px;font-weight:700;color:#ffffff;border-bottom:1px solid rgba(255,255,255,0.1);text-transform:uppercase;letter-spacing:.07em">Hadir</th><th style="padding:9px 14px;text-align:center;font-size:10px;font-weight:700;color:#ffffff;border-bottom:1px solid rgba(255,255,255,0.1);text-transform:uppercase;letter-spacing:.07em">Lambat</th></tr></thead><tbody>';
    emps.forEach(function(emp,i){
        var color=PALETTE[i%PALETTE.length];
        html+='<tr><td style="padding:10px 14px"><div style="display:flex;align-items:center;gap:8px"><div style="width:26px;height:26px;border-radius:7px;background:'+color+';display:flex;align-items:center;justify-content:center;font-size:9px;font-weight:600;color:#fff;flex-shrink:0">'+emp.nama.substring(0,2).toUpperCase()+'</div><span style="font-size:12px;font-weight:500;color:#ffffff">'+emp.nama+'</span></div></td><td style="padding:10px 14px;text-align:center"><span style="font-size:13px;font-weight:600;color:'+(emp.masuk>0?'#4ade80':'#475569')+'">'+emp.masuk+'</span></td><td style="padding:10px 14px;text-align:center"><span style="font-size:13px;font-weight:600;color:'+(emp.lambat>0?'#fbbf24':'#475569')+'">'+emp.lambat+'</span></td></tr>';
    });
    html+='</tbody></table>';
    if(Object.keys(allK).length>8) html+='<div style="text-align:center;padding:10px;font-size:12px;color:#818cf8;cursor:pointer;font-weight:500" onclick="switchPage(\'data\')">Lihat semua karyawan →</div>';
    wrap.innerHTML=html;
}

function hitungAbsen(periodRecords,p){
    var dates={}; periodRecords.forEach(function(r){dates[r.tanggal]=true;});
    var dArr=Object.keys(dates); if(!dArr.length) return 0;
    var allPins={}; RAW_DATA.forEach(function(r){allPins[r.pin]=true;});
    var total=Object.keys(allPins).length,absen=0;
    dArr.forEach(function(dt){var hadir={};periodRecords.forEach(function(r){if(r.tanggal===dt&&r.isMasuk)hadir[r.pin]=true;});absen+=total-Object.keys(hadir).length;});
    return Math.max(0,absen);
}

function renderDailyAllEmployees(periodRecords,p){
    var allK={}; RAW_DATA.forEach(function(r){if(!allK[r.pin])allK[r.pin]=r.nama;});
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
    var namaHari=getNamaHari(tglStr), hariCss=getHariCssClass(tglStr);
    document.getElementById('countChip').textContent=rows.length+' karyawan';
    document.getElementById('karyawanNavStrip').style.display='none';
    document.getElementById('empTableHeader').style.display='none';
    document.getElementById('tableHead').innerHTML='<tr><th>No</th><th>Karyawan</th><th>Jam Masuk</th><th>Jam Pulang</th><th>Status</th></tr>';
    var tbody=document.getElementById('tableBody'); tbody.innerHTML='';
    if(!rows.length){tbody.innerHTML='<tr><td colspan="5"><div class="empty"><div class="empty-icon">📋</div><div class="empty-text">Tidak ada data untuk hari ini</div></div></td></tr>';}
    else {
        rows.forEach(function(emp,idx){
            var color=PALETTE[idx%PALETTE.length], initials=emp.nama.substring(0,2).toUpperCase();
            var tr=document.createElement('tr'); tr.style.animation='rowIn .2s '+(idx*0.02)+'s ease both';
            if(emp.absent) tr.className='row-absent';
            var noCell='<td style="color:#475569;font-size:12px;font-family:\'JetBrains Mono\',monospace">'+String(idx+1).padStart(2,'0')+'</td>';
            var empCell='<td><div class="emp-name-cell"><div class="emp-avatar-sm" style="background:'+color+'">'+initials+'</div><div><div class="emp-name-text">'+emp.nama+'</div><div style="display:flex;align-items:center;gap:5px;margin-top:2px"><span class="emp-pin-text">PIN: '+emp.pin+'</span><span class="date-day-badge '+hariCss+'">'+namaHari+'</span></div></div></div></td>';
            if(emp.absent){
                tr.innerHTML=noCell+empCell+'<td><span class="badge-absent">Tidak Masuk</span></td><td><span class="time-none">—</span></td><td><span class="status-pill sp-absent">⚠ Absen</span></td>';
            } else {
                var jm=emp.masuk?(emp.terlambat?'<span class="time-badge time-late"><svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="9"/><path d="M12 8v4"/><circle cx="12" cy="16" r=".8" fill="#d97706"/></svg>'+emp.masuk+'</span>':'<span class="time-badge time-ok"><svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="9"/><path d="M9 12l2 2 4-4"/></svg>'+emp.masuk+'</span>'):'<span class="time-none">—</span>';
                var jp=emp.pulang?(emp.pulangCepat?'<span class="time-badge time-early"><svg viewBox="0 0 24 24"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>'+emp.pulang+'</span>':'<span class="time-badge time-ok"><svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="9"/><path d="M9 12l2 2 4-4"/></svg>'+emp.pulang+'</span>'):'<span class="time-badge time-ok"><svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="9"/><path d="M9 12l2 2 4-4"/></svg>16:00:00</span>';
                var st=emp.terlambat?'<span class="status-pill sp-lambat">⚠ Terlambat</span>':emp.pulangCepat?'<span class="status-pill sp-cepat">↩ Pulang Cepat</span>':emp.masuk?'<span class="status-pill sp-tepat">✓ Tepat Waktu</span>':'<span class="time-none">—</span>';
                tr.innerHTML=noCell+empCell+'<td>'+jm+'</td><td>'+jp+'</td><td>'+st+'</td>';
            }
            tbody.appendChild(tr);
        });
    }
    var jH=rows.filter(function(r){return !r.absent;}).length, jL=rows.filter(function(r){return r.terlambat;}).length, jA=rows.filter(function(r){return r.absent;}).length;
    document.getElementById('pgInfo').innerHTML='📅 <strong style="color:#fff">'+namaHari+', '+p.hari+' '+NAMA_BULAN[p.bulan]+' '+p.tahun+'</strong> &nbsp;| <span style="color:#4ade80;font-weight:600">✓ Hadir: '+jH+'</span> &nbsp;<span style="color:#fbbf24;font-weight:600">⚠ Lambat: '+jL+'</span> &nbsp;<span style="color:#c084fc;font-weight:600">✕ Absen: '+jA+'</span>';
    document.getElementById('pgBtns').innerHTML='';
}

function buildGrouped(pr,p){
    var datesInPeriod={}; pr.forEach(function(r){if(!datesInPeriod[r.tanggal])datesInPeriod[r.tanggal]=r.tanggalFmt;});
    var allDates=Object.keys(datesInPeriod).sort();
    var allK={}; RAW_DATA.forEach(function(r){if(!allK[r.pin])allK[r.pin]=r.nama;});
    var kMap={};
    Object.keys(allK).forEach(function(pin){kMap[pin]={pin:pin,nama:allK[pin],days:{}}; allDates.forEach(function(dt){kMap[pin].days[dt]={tanggal:dt,tanggalFmt:datesInPeriod[dt],masuk:null,pulang:null,terlambat:false,pulangCepat:false,absent:true};});});
    pr.forEach(function(r){
        if(!kMap[r.pin])kMap[r.pin]={pin:r.pin,nama:r.nama,days:{}};
        if(!kMap[r.pin].days[r.tanggal])kMap[r.pin].days[r.tanggal]={tanggal:r.tanggal,tanggalFmt:r.tanggalFmt,masuk:null,pulang:null,terlambat:false,pulangCepat:false,absent:true};
        var d=kMap[r.pin].days[r.tanggal];
        if(r.isMasuk){d.absent=false;if(!d.masuk||r.waktu<d.masuk){d.masuk=r.waktu;d.terlambat=r.terlambat;}}
        else{if(!d.pulang||r.waktu>d.pulang){d.pulang=r.waktu;d.pulangCepat=r.pulangCepat;}}
    });
    var result=[];
    Object.keys(kMap).forEach(function(pin){var k=kMap[pin]; var days=Object.values(k.days).sort(function(a,b){return a.tanggal<b.tanggal?-1:1;}); if(allDates.length>0) result.push({pin:k.pin,nama:k.nama,days:days});});
    return result.sort(function(a,b){return a.nama<b.nama?-1:1;});
}

function applyFiltersAndRender(){
    var name=document.getElementById('searchName').value.toLowerCase().trim();
    var dateF=document.getElementById('filterDate').value;
    var ket=document.getElementById('filterKet').value;
    var p=getPeriod();
    if(dateF){var pts=dateF.split('-');p.tahun=parseInt(pts[0]);p.bulan=parseInt(pts[1]);p.hari=parseInt(pts[2]);}
    var pr=filterByPeriod(RAW_DATA,p);
    if(viewMode==='harian'){if(name)pr=pr.filter(function(r){return r.nama.toLowerCase().indexOf(name)!==-1;});renderDailyAllEmployees(pr,p);return;}
    var baseRecords=dateF?RAW_DATA.filter(function(r){return r.tanggal===dateF;}):pr;
    var grouped=buildGrouped(baseRecords,p);
    if(name) grouped=grouped.filter(function(k){return k.nama.toLowerCase().indexOf(name)!==-1;});
    if(dateF) grouped=grouped.map(function(k){return{pin:k.pin,nama:k.nama,days:k.days.filter(function(d){return d.tanggal===dateF;})};}).filter(function(k){return k.days.length>0;});
    if(ket) grouped=grouped.map(function(k){return{pin:k.pin,nama:k.nama,days:k.days.filter(function(d){if(ket==='terlambat')return d.terlambat;if(ket==='cepat')return d.pulangCepat;if(ket==='tepat')return d.masuk&&!d.terlambat;if(ket==='absent')return d.absent;return true;})};}).filter(function(k){return k.days.length>0;});
    currentEmpPage=1; renderTable(grouped);
}

function resetFilter(){ document.getElementById('searchName').value=''; document.getElementById('filterDate').value=''; document.getElementById('filterKet').value=''; applyFiltersAndRender(); }

function onFilterDateChange(){
    var dateF=document.getElementById('filterDate').value;
    if(dateF){
        var pts=dateF.split('-'), y=parseInt(pts[0]), m=parseInt(pts[1]), d=parseInt(pts[2]);
        var selY=document.getElementById('filterTahun');
        for(var i=0;i<selY.options.length;i++){if(parseInt(selY.options[i].value)===y){selY.selectedIndex=i;break;}}
        document.getElementById('filterBulan').value=m;
        if(viewMode==='harian'){populateHari(y,m);document.getElementById('filterHari').value=d;}
        onPeriodChange();
    }
    applyFiltersAndRender();
}

function renderTable(grouped){
    document.getElementById('tableHead').innerHTML='<tr><th>No</th><th>Tanggal</th><th>Jam Masuk</th><th>Jam Pulang</th><th>Status</th></tr>';
    document.getElementById('karyawanNavStrip').style.display='';
    document.getElementById('empTableHeader').style.display='';
    var tbody=document.getElementById('tableBody'),pgBtns=document.getElementById('pgBtns'),pgInfo=document.getElementById('pgInfo');
    var navStrip=document.getElementById('karyawanNavStrip'); tbody.innerHTML='';
    var total=grouped.length; if(currentEmpPage>total) currentEmpPage=Math.max(1,total);
    var totalDays=grouped.reduce(function(s,k){return s+k.days.length;},0);
    document.getElementById('countChip').textContent=total+' karyawan · '+totalDays+' hari';
    navStrip.innerHTML='<span class="karyawan-nav-label">Karyawan:</span>';
    grouped.forEach(function(k,idx){
        var btn=document.createElement('button'); btn.className='karyawan-nav-btn'+(idx+1===currentEmpPage?' active':'');
        btn.textContent=k.nama.split(' ')[0]; btn.title=k.nama;
        btn.onclick=(function(i){return function(){currentEmpPage=i+1;renderTable(grouped);};})(idx);
        navStrip.appendChild(btn);
    });
    if(!total){resetEmpHeader();tbody.innerHTML='<tr><td colspan="5"><div class="empty"><div class="empty-icon">🔍</div><div class="empty-text">Tidak ada data</div><div class="empty-sub">Pilih periode lain atau ubah filter</div></div></td></tr>';pgInfo.textContent='Tidak ada data';pgBtns.innerHTML='';return;}
    var k=grouped[currentEmpPage-1]; updateEmpHeader(k,currentEmpPage-1);
    k.days.forEach(function(d,idx){
        var tr=document.createElement('tr'); tr.style.animation='rowIn .2s '+(idx*0.02)+'s ease both';
        var noCell='<td style="color:#475569;font-size:12px;font-family:\'JetBrains Mono\',monospace">'+String(idx+1).padStart(2,'0')+'</td>';
        if(d.absent){
            tr.className='row-absent';
            tr.innerHTML=noCell+'<td style="font-size:12px;color:#94a3b8">'+d.tanggalFmt+'</td><td><span class="badge-absent">Tidak Masuk</span></td><td><span class="time-none">—</span></td><td><span class="status-pill sp-absent">⚠ Absen</span></td>';
        } else {
            var jm=d.masuk?(d.terlambat?'<span class="time-badge time-late"><svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="9"/><path d="M12 8v4"/><circle cx="12" cy="16" r=".8" fill="#d97706"/></svg>'+d.masuk+'</span>':'<span class="time-badge time-ok"><svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="9"/><path d="M9 12l2 2 4-4"/></svg>'+d.masuk+'</span>'):'<span class="time-none">—</span>';
            var jp=d.pulang?(d.pulangCepat?'<span class="time-badge time-early"><svg viewBox="0 0 24 24"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>'+d.pulang+'</span>':'<span class="time-badge time-ok"><svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="9"/><path d="M9 12l2 2 4-4"/></svg>'+d.pulang+'</span>'):'<span class="time-badge time-ok"><svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="9"/><path d="M9 12l2 2 4-4"/></svg>16:00:00</span>';
            var st=d.terlambat?'<span class="status-pill sp-lambat">⚠ Terlambat</span>':d.pulangCepat?'<span class="status-pill sp-cepat">↩ Pulang Cepat</span>':d.masuk?'<span class="status-pill sp-tepat">✓ Tepat Waktu</span>':'<span class="time-none">—</span>';
            tr.innerHTML=noCell+'<td style="font-size:12px;color:#94a3b8">'+d.tanggalFmt+'</td><td>'+jm+'</td><td>'+jp+'</td><td>'+st+'</td>';
        }
        tbody.appendChild(tr);
    });
    pgInfo.textContent='Karyawan '+currentEmpPage+' dari '+total;
    pgBtns.innerHTML='';
    function mkBtn(label,page,disabled,active){var b=document.createElement('button');b.className='pg-btn'+(active?' pg-active':'');b.innerHTML=label;b.disabled=disabled;b.onclick=function(){currentEmpPage=page;renderTable(grouped);};pgBtns.appendChild(b);}
    function mkE(){var s=document.createElement('span');s.className='pg-ellipsis';s.textContent='…';pgBtns.appendChild(s);}
    mkBtn('&#8592;',currentEmpPage-1,currentEmpPage===1,false);
    var pages=[];
    if(total<=7){for(var i=1;i<=total;i++)pages.push(i);}
    else{pages.push(1);if(currentEmpPage>3)pages.push('…');var lo=Math.max(2,currentEmpPage-1),hi=Math.min(total-1,currentEmpPage+1);for(var j=lo;j<=hi;j++)pages.push(j);if(currentEmpPage<total-2)pages.push('…');pages.push(total);}
    pages.forEach(function(pg){if(pg==='…')mkE();else mkBtn(pg,pg,false,pg===currentEmpPage);});
    mkBtn('&#8594;',currentEmpPage+1,currentEmpPage===total,false);
}

function updateEmpHeader(k,colorIdx){
    var color=PALETTE[colorIdx%PALETTE.length];
    var av=document.getElementById('empTableAvatar'); av.textContent=k.nama.substring(0,2).toUpperCase(); av.style.background=color;
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
function resetEmpHeader(){ document.getElementById('empTableAvatar').textContent='??'; document.getElementById('empTableAvatar').style.background='var(--accent)'; ['empTableName','empTablePin'].forEach(function(id){document.getElementById(id).textContent='—';}); document.getElementById('empTableDays').textContent='0 hari'; document.getElementById('empTableStats').innerHTML=''; }

/* ══ PERFORMA ══ */
function getEmpsForPeriod(tahun,bulan){
    var allPins={}; RAW_DATA.filter(function(r){return r.tahun===tahun;}).forEach(function(r){if(!allPins[r.pin])allPins[r.pin]={pin:r.pin,nama:r.nama};});
    var relevant=RAW_DATA.filter(function(r){if(r.tahun!==tahun)return false;if(bulan>0&&r.bulan!==bulan)return false;return true;});
    var workDates={}; relevant.forEach(function(r){workDates[r.tanggal]=true;});
    var totalWD=Object.keys(workDates).length, pinStats={};
    relevant.forEach(function(r){
        if(!pinStats[r.pin])pinStats[r.pin]={dateSet:{}};
        if(!pinStats[r.pin].dateSet[r.tanggal])pinStats[r.pin].dateSet[r.tanggal]={masuk:false,terlambat:false,pulangCepat:false};
        if(r.isMasuk){pinStats[r.pin].dateSet[r.tanggal].masuk=true;if(r.terlambat)pinStats[r.pin].dateSet[r.tanggal].terlambat=true;}
        else{if(r.pulangCepat)pinStats[r.pin].dateSet[r.tanggal].pulangCepat=true;}
    });
    return Object.keys(allPins).map(function(pin){
        var info=allPins[pin],sd=pinStats[pin]?Object.values(pinStats[pin].dateSet):[];
        var hadir=sd.filter(function(d){return d.masuk;}).length;
        return{pin:info.pin,nama:info.nama,hadir:hadir,lambat:sd.filter(function(d){return d.terlambat;}).length,cepat:sd.filter(function(d){return d.pulangCepat;}).length,absent:Math.max(0,totalWD-hadir),totalWD:totalWD};
    }).sort(function(a,b){return a.nama<b.nama?-1:1;});
}

function renderPerfPage(){
    var tahun=parseInt(document.getElementById('filterTahunPerf').value);
    var bulan=parseInt(document.getElementById('filterBulanPerf').value);
    document.getElementById('perfPeriodHint').textContent=bulan===0?'📅 Seluruh tahun '+tahun:'📅 '+NAMA_BULAN[bulan]+' '+tahun;
    var emps=getEmpsForPeriod(tahun,bulan);
    document.getElementById('empCountChip').textContent=emps.length+' karyawan';
    renderEmpSelector(emps,tahun,bulan);
    if(selectedEmpPin){var emp=emps.find(function(e){return e.pin===selectedEmpPin;});if(emp)buildBarChart(selectedEmpPin,emp.nama,selectedEmpColor,tahun,bulan);else{document.getElementById('empChartCard').classList.remove('show');document.getElementById('empChartHint').style.display='';selectedEmpPin=null;}}
}
function renderEmpSelector(emps,tahun,bulan){
    var grid=document.getElementById('empSelectorGrid'); grid.innerHTML='';
    if(!emps.length){grid.innerHTML='<div class="empty"><div class="empty-icon">📊</div><div class="empty-text">Tidak ada data</div></div>';return;}
    emps.forEach(function(emp,i){
        var color=PALETTE[i%PALETTE.length], card=document.createElement('div');
        card.className='emp-card'+(emp.pin===selectedEmpPin?' selected':'');
        card.innerHTML='<div class="emp-card-badge"><svg viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg></div>'
            +'<div class="emp-card-header"><div class="emp-card-avatar" style="background:'+color+'">'+emp.nama.substring(0,2).toUpperCase()+'</div>'
            +'<div style="min-width:0"><div class="emp-card-name" title="'+emp.nama+'">'+emp.nama+'</div><div class="emp-card-pin">PIN: '+emp.pin+'</div></div></div>'
            +'<div class="emp-card-stats">'
            +'<div class="emp-card-stat"><div class="emp-card-stat-num" style="color:'+(emp.hadir>0?color:'#475569')+'">'+emp.hadir+'</div><div class="emp-card-stat-lbl">Hadir</div></div>'
            +'<div class="emp-card-stat"><div class="emp-card-stat-num" style="color:'+(emp.lambat>0?'#fbbf24':'#475569')+'">'+emp.lambat+'</div><div class="emp-card-stat-lbl">Lambat</div></div>'
            +'<div class="emp-card-stat"><div class="emp-card-stat-num" style="color:'+(emp.cepat>0?'#fb7185':'#475569')+'">'+emp.cepat+'</div><div class="emp-card-stat-lbl">Cepat</div></div>'
            +'<div class="emp-card-stat"><div class="emp-card-stat-num" style="color:'+(emp.absent>0?'#c084fc':'#475569')+'">'+emp.absent+'</div><div class="emp-card-stat-lbl">Absen</div></div>'
            +'</div>';
        (function(pin,nama,col,t,b,el){el.onclick=function(){selectedEmpPin=pin;selectedEmpColor=col;document.querySelectorAll('.emp-card').forEach(function(c){c.classList.remove('selected');});el.classList.add('selected');buildBarChart(pin,nama,col,t,b);};})(emp.pin,emp.nama,color,tahun,bulan,card);
        grid.appendChild(card);
    });
}
function getMonthlyData(pin,tahun){
    var mMap={},mWD={};
    RAW_DATA.filter(function(r){return r.tahun===tahun;}).forEach(function(r){if(!mWD[r.bulan])mWD[r.bulan]={};mWD[r.bulan][r.tanggal]=true;});
    RAW_DATA.filter(function(r){return r.pin===pin&&r.tahun===tahun;}).forEach(function(r){if(!mMap[r.bulan])mMap[r.bulan]={};if(!mMap[r.bulan][r.tanggal])mMap[r.bulan][r.tanggal]={masuk:null,pulang:null,terlambat:false,pulangCepat:false};var d=mMap[r.bulan][r.tanggal];if(r.isMasuk){if(!d.masuk||r.waktu<d.masuk){d.masuk=r.waktu;d.terlambat=r.terlambat;}}else{if(!d.pulang||r.waktu>d.pulang){d.pulang=r.waktu;d.pulangCepat=r.pulangCepat;}}});
    var result=[];
    for(var m=1;m<=12;m++){if(!mWD[m])continue;var days=mMap[m]?Object.values(mMap[m]):[];var hadir=days.filter(function(d){return d.masuk;}).length;result.push({bulan:m,label:NAMA_BULAN_S[m],hadir:hadir,lambat:days.filter(function(d){return d.terlambat;}).length,cepat:days.filter(function(d){return d.pulangCepat;}).length,tepat:days.filter(function(d){return d.masuk&&!d.terlambat;}).length,absent:Math.max(0,Object.keys(mWD[m]).length-hadir)});}
    return result;
}
function getDailyData(pin,tahun,bulan){
    var dMap={},allDates={};
    RAW_DATA.filter(function(r){return r.tahun===tahun&&r.bulan===bulan;}).forEach(function(r){allDates[r.tanggal]=r.tanggalFmt;});
    RAW_DATA.filter(function(r){return r.pin===pin&&r.tahun===tahun&&r.bulan===bulan;}).forEach(function(r){if(!dMap[r.tanggal])dMap[r.tanggal]={masuk:null,pulang:null,terlambat:false,pulangCepat:false};var d=dMap[r.tanggal];if(r.isMasuk){if(!d.masuk||r.waktu<d.masuk){d.masuk=r.waktu;d.terlambat=r.terlambat;}}else{if(!d.pulang||r.waktu>d.pulang){d.pulang=r.waktu;d.pulangCepat=r.pulangCepat;}}});
    return Object.keys(allDates).sort().map(function(dt){var d=dMap[dt]||{masuk:null,terlambat:false,pulangCepat:false};return{tanggal:dt,label:String(parseInt(dt.split('-')[2])),hadir:d.masuk?1:0,lambat:d.terlambat?1:0,cepat:d.pulangCepat?1:0,tepat:(d.masuk&&!d.terlambat)?1:0,absent:d.masuk?0:1};});
}
function setBarMode(mode){
    barMode=mode;
    ['btnModeAll','btnModeHadir','btnModeLate'].forEach(function(id){document.getElementById(id).classList.remove('active');});
    document.getElementById({all:'btnModeAll',hadir:'btnModeHadir',late:'btnModeLate'}[mode]).classList.add('active');
    if(selectedEmpPin){var tahun=parseInt(document.getElementById('filterTahunPerf').value);var bulan=parseInt(document.getElementById('filterBulanPerf').value);buildBarChart(selectedEmpPin,document.getElementById('bcName').textContent,selectedEmpColor,tahun,bulan);}
}
function buildCustomLegend(activeKeys){
    var c=document.getElementById('chartLegendCustom'); c.innerHTML='';
    activeKeys.forEach(function(key){var cfg=BC[key];var item=document.createElement('div');item.className='cl-item';var ind=cfg.type==='line'?'<div class="cl-line" style="background:'+cfg.hex+'"></div>':'<div class="cl-dot" style="background:'+cfg.bg+';border:2px solid '+cfg.border+'"></div>';item.innerHTML=ind+'<span>'+cfg.label+'</span>';c.appendChild(item);});
}
function buildBarChart(pin,nama,color,tahun,bulan){
    var isDaily=bulan>0, data=isDaily?getDailyData(pin,tahun,bulan):getMonthlyData(pin,tahun);
    document.getElementById('empChartCard').classList.add('show');
    document.getElementById('empChartHint').style.display='none';
    var av=document.getElementById('bcAvatar'); av.textContent=nama.substring(0,2).toUpperCase(); av.style.background=color;
    document.getElementById('bcName').textContent=nama;
    document.getElementById('bcPin').textContent='PIN: '+pin+' · '+(isDaily?NAMA_BULAN[bulan]+' '+tahun:'Tahun '+tahun);
    var yH=0,yL=0,yE=0,yT=0,yA=0; data.forEach(function(m){yH+=m.hadir;yL+=m.lambat;yE+=m.cepat;yT+=m.tepat;yA+=m.absent;});
    renderYearlyStat(yH,yL,yE,yT,yA,isDaily?NAMA_BULAN[bulan]+' '+tahun:'Tahun '+tahun);
    if(!data.length){if(empBarChart){empBarChart.destroy();empBarChart=null;}return;}
    if(empBarChart){empBarChart.destroy();empBarChart=null;}
    var ctx=document.getElementById('empBarChart').getContext('2d'), datasets=[], activeKeys=[];
    if(barMode==='all'||barMode==='hadir'){
        datasets.push({label:BC.hadir.label,type:'bar',data:data.map(function(m){return m.hadir;}),backgroundColor:BC.hadir.bg,borderColor:BC.hadir.border,borderWidth:1.5,borderRadius:6,borderSkipped:false,order:2});activeKeys.push('hadir');
        datasets.push({label:BC.tepat.label,type:'bar',data:data.map(function(m){return m.tepat;}),backgroundColor:BC.tepat.bg,borderColor:BC.tepat.border,borderWidth:1.5,borderRadius:6,borderSkipped:false,order:3});activeKeys.push('tepat');
        datasets.push({label:BC.absent.label,type:'bar',data:data.map(function(m){return m.absent;}),backgroundColor:BC.absent.bg,borderColor:BC.absent.border,borderWidth:1.5,borderRadius:6,borderSkipped:false,order:4});activeKeys.push('absent');
        datasets.push({label:BC.tren.label,type:'line',data:data.map(function(m){return m.hadir;}),borderColor:BC.tren.border,backgroundColor:BC.tren.bg,borderWidth:2.5,tension:.4,pointRadius:4,pointBackgroundColor:BC.tren.border,pointBorderColor:'#fff',pointBorderWidth:2,fill:true,order:1});activeKeys.push('tren');
    }
    if(barMode==='all'||barMode==='late'){
        datasets.push({label:BC.lambat.label,type:'bar',data:data.map(function(m){return m.lambat;}),backgroundColor:BC.lambat.bg,borderColor:BC.lambat.border,borderWidth:1.5,borderRadius:6,borderSkipped:false,order:5});activeKeys.push('lambat');
        datasets.push({label:BC.cepat.label,type:'bar',data:data.map(function(m){return m.cepat;}),backgroundColor:BC.cepat.bg,borderColor:BC.cepat.border,borderWidth:1.5,borderRadius:6,borderSkipped:false,order:6});activeKeys.push('cepat');
    }
    buildCustomLegend(activeKeys);
    empBarChart=new Chart(ctx,{type:'bar',data:{labels:data.map(function(m){return m.label;}),datasets:datasets},options:{responsive:true,maintainAspectRatio:false,interaction:{mode:'index',intersect:false},plugins:{legend:{display:false},tooltip:{callbacks:{title:function(items){var m=data[items[0].dataIndex];return isDaily?m.tanggal:NAMA_BULAN[m.bulan]+' '+tahun;},label:function(c){return ' '+c.dataset.label+': '+c.raw+' hari';}},padding:12,cornerRadius:10,backgroundColor:'rgba(15,23,42,0.95)',titleColor:'#c7d2fe',bodyColor:'#e0e7ff',borderColor:'rgba(99,102,241,0.3)',borderWidth:1}},scales:{x:{grid:{display:false},ticks:{font:{family:'Inter',size:11},color:'#64748b'},border:{display:false}},y:{beginAtZero:true,grid:{color:'rgba(255,255,255,0.05)'},ticks:{font:{family:'JetBrains Mono',size:10},color:'#64748b',stepSize:1,callback:function(v){return Number.isInteger(v)?v:'';}},border:{display:false}}},animation:{duration:600}}});
    setTimeout(function(){document.getElementById('empChartCard').scrollIntoView({behavior:'smooth',block:'nearest'});},80);
}
function renderYearlyStat(h,l,e,t,a,label){
    document.getElementById('yearlyStatStrip').innerHTML=(label?'<div style="width:100%;font-size:11px;font-weight:600;color:#94a3b8;text-transform:uppercase;letter-spacing:.06em;padding-bottom:6px">Ringkasan '+label+'</div>':'')+ys('ys-hadir',h,'Total Hadir')+ys('ys-tepat',t,'Tepat Waktu')+ys('ys-lambat',l,'Terlambat')+ys('ys-cepat',e,'Pulang Cepat')+ys('ys-absent',a,'Tidak Masuk');
}
function ys(cls,val,lbl){
    var colors={'ys-hadir':'#4ade80','ys-lambat':'#fbbf24','ys-cepat':'#fb7185','ys-tepat':'#818cf8','ys-absent':'#c084fc'};
    var bgs={'ys-hadir':'rgba(34,197,94,0.15)','ys-lambat':'rgba(245,158,11,0.15)','ys-cepat':'rgba(244,63,94,0.15)','ys-tepat':'rgba(99,102,241,0.15)','ys-absent':'rgba(168,85,247,0.15)'};
    var borders={'ys-hadir':'rgba(34,197,94,0.3)','ys-lambat':'rgba(245,158,11,0.3)','ys-cepat':'rgba(244,63,94,0.3)','ys-tepat':'rgba(99,102,241,0.3)','ys-absent':'rgba(168,85,247,0.3)'};
    return '<div class="ys-pill" style="background:'+bgs[cls]+';border:1px solid '+borders[cls]+'"><div><div class="ys-val" style="color:'+colors[cls]+'">'+val+'</div><div class="ys-lbl">'+lbl+'</div></div></div>';
}

/* ══ CETAK ══ */
function cetakLaporan(){
    var p=getPeriod(), lbl=periodLabel(p), pr=filterByPeriod(RAW_DATA,p);
    var grouped=[], rowsHtml='', totalRows=0;
    if(viewMode==='harian'){
        var allK={}; RAW_DATA.forEach(function(r){if(!allK[r.pin])allK[r.pin]=r.nama;});
        var dayData={};
        Object.keys(allK).forEach(function(pin){dayData[pin]={pin:pin,nama:allK[pin],masuk:null,pulang:null,terlambat:false,pulangCepat:false,absent:true};});
        pr.forEach(function(r){var d=dayData[r.pin];if(!d)return;if(r.isMasuk){d.absent=false;if(!d.masuk||r.waktu<d.masuk){d.masuk=r.waktu;d.terlambat=r.terlambat;}}else{if(!d.pulang||r.waktu>d.pulang){d.pulang=r.waktu;d.pulangCepat=r.pulangCepat;}}});
        grouped=Object.values(dayData).sort(function(a,b){return a.nama<b.nama?-1:1;});
        grouped.forEach(function(emp){
            totalRows++;
            var st=emp.absent?'Tidak Masuk':emp.terlambat?'Terlambat':emp.pulangCepat?'Pulang Cepat':'Tepat Waktu';
            var sc=emp.absent?'#7c3aed':emp.terlambat?'#d97706':emp.pulangCepat?'#e11d48':'#16a34a';
            rowsHtml+='<tr><td style="text-align:center">'+totalRows+'</td><td>'+emp.nama+'</td><td style="font-family:monospace">'+emp.pin+'</td><td style="font-family:monospace">'+(emp.masuk||'—')+'</td><td style="font-family:monospace">'+(emp.pulang||(emp.absent?'—':'16:00:00'))+'</td><td style="color:'+sc+';font-weight:700">'+st+'</td></tr>';
        });
    } else {
        grouped=buildGrouped(pr,p);
        grouped.forEach(function(k){k.days.forEach(function(d){
            totalRows++;
            var st=d.absent?'Tidak Masuk':d.terlambat?'Terlambat':d.pulangCepat?'Pulang Cepat':d.masuk?'Tepat Waktu':'—';
            var sc=d.absent?'#7c3aed':d.terlambat?'#d97706':d.pulangCepat?'#e11d48':d.masuk?'#16a34a':'#999';
            rowsHtml+='<tr><td style="text-align:center">'+totalRows+'</td><td>'+d.tanggalFmt+'</td><td>'+k.nama+'</td><td style="font-family:monospace">'+k.pin+'</td><td style="font-family:monospace">'+(d.masuk||'—')+'</td><td style="font-family:monospace">'+(d.pulang||(d.masuk?'16:00:00':'—'))+'</td><td style="color:'+sc+';font-weight:700">'+st+'</td></tr>';
        });});
    }
    var total=pr.length, tepat=pr.filter(function(r){return r.isMasuk&&!r.terlambat;}).length, lambat=pr.filter(function(r){return r.terlambat;}).length, cepat=pr.filter(function(r){return r.pulangCepat;}).length, absent=hitungAbsen(pr,p);
    var headerCols=viewMode==='harian'?'<th>No</th><th>Nama Karyawan</th><th>PIN</th><th>Jam Masuk</th><th>Jam Pulang</th><th>Status</th>':'<th>No</th><th>Tanggal</th><th>Nama Karyawan</th><th>PIN</th><th>Jam Masuk</th><th>Jam Pulang</th><th>Status</th>';
    var colSpan=viewMode==='harian'?6:7;
    var win=window.open('','_blank','width=960,height=720');
    if(!win){alert('Popup diblokir. Izinkan popup untuk domain ini.');return;}
    win.document.write('<!DOCTYPE html><html lang="id"><head><meta charset="UTF-8"><title>Laporan Absensi — '+lbl+'</title><style>body{font-family:Arial,sans-serif;font-size:12px;color:#111;margin:20px}.header{text-align:center;margin-bottom:18px;border-bottom:2px solid #1e1b4b;padding-bottom:12px}.header h2{font-size:17px;margin:0 0 5px;color:#1e1b4b}.header p{font-size:11px;color:#555;margin:2px 0}.summary{display:flex;gap:8px;margin-bottom:16px;flex-wrap:wrap}.sum-box{border:1px solid #ddd;border-radius:6px;padding:8px 16px;text-align:center;flex:1;min-width:80px}.sum-num{font-size:20px;font-weight:700;margin-bottom:2px}.sum-lbl{font-size:10px;color:#666;text-transform:uppercase;letter-spacing:.06em}table{width:100%;border-collapse:collapse;font-size:11px}th{background:#1e1b4b;color:#fff;padding:8px 10px;text-align:left;font-size:10px;text-transform:uppercase;letter-spacing:.06em;border:1px solid #1e1b4b}td{padding:7px 10px;border:1px solid #e0e0e0;vertical-align:middle}tr:nth-child(even) td{background:#f8f8ff}.footer{text-align:center;margin-top:16px;font-size:10px;color:#999;border-top:1px solid #eee;padding-top:8px}@page{size:A4;margin:1.5cm}@media print{body{margin:0;-webkit-print-color-adjust:exact;print-color-adjust:exact}}</style></head><body>'
        +'<div class="header"><h2>&#128197; Laporan Absensi Karyawan</h2><p>Periode: <strong>'+lbl+'</strong></p><p>Dicetak: '+new Date().toLocaleDateString('id-ID',{weekday:'long',day:'numeric',month:'long',year:'numeric'})+' pukul '+new Date().toLocaleTimeString('id-ID')+'</p></div>'
        +'<div class="summary">'
        +'<div class="sum-box"><div class="sum-num" style="color:#6366f1">'+total+'</div><div class="sum-lbl">Total Absensi</div></div>'
        +'<div class="sum-box"><div class="sum-num" style="color:#16a34a">'+tepat+'</div><div class="sum-lbl">Tepat Waktu</div></div>'
        +'<div class="sum-box"><div class="sum-num" style="color:#d97706">'+lambat+'</div><div class="sum-lbl">Terlambat</div></div>'
        +'<div class="sum-box"><div class="sum-num" style="color:#e11d48">'+cepat+'</div><div class="sum-lbl">Pulang Cepat</div></div>'
        +'<div class="sum-box"><div class="sum-num" style="color:#7c3aed">'+absent+'</div><div class="sum-lbl">Tidak Masuk</div></div>'
        +'</div>'
        +'<table><thead><tr>'+headerCols+'</tr></thead><tbody>'+(rowsHtml||'<tr><td colspan="'+colSpan+'" style="text-align:center;padding:24px;color:#999">Tidak ada data untuk periode ini</td></tr>')+'</tbody></table>'
        +'<div class="footer">Kipin — Sistem Monitoring Absensi &middot; Publik View &nbsp;|&nbsp; Total '+totalRows+' baris data</div>'
        +'</body></html>');
    win.document.close(); win.focus(); setTimeout(function(){win.print();},500);
}
</script>
</body>
</html>
