<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kipin — Dashboard Absensi</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;600&display=swap" rel="stylesheet">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Plus Jakarta Sans', 'sans-serif'],
                        mono: ['JetBrains Mono', 'monospace'],
                    },
                }
            }
        }
    </script>
    <style>
        /* ── CSS Variables ── */
        :root {
            --accent:       #6366f1;
            --accent-dark:  #4f46e5;
            --accent-light: #818cf8;
            --accent-bg:    #eef2ff;
            --teal:         #0d9488;
            --amber:        #d97706;
            --amber-bg:     #fef3c7;
            --rose:         #e11d48;
            --rose-bg:      #ffe4e6;
            --green:        #16a34a;
            --green-bg:     #dcfce7;
            --violet:       #7c3aed;
            --violet-bg:    #f3e8ff;
            --ink:          #1e1b4b;
            --ink-muted:    #64748b;
            --ink-light:    #94a3b8;
            --sidebar-w:    240px;
            --shadow-card:  0 2px 12px rgba(99,102,241,0.07);
            --shadow-hover: 0 8px 28px rgba(99,102,241,0.14);
            --shadow-btn:   0 4px 14px rgba(99,102,241,0.35);
        }

        /* ── Reset & Base ── */
        *, *::before, *::after { box-sizing: border-box; }
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: #f0f4ff;
            color: var(--ink);
            min-height: 100vh;
            overflow-x: hidden;
        }
        body::before {
            content: '';
            position: fixed; inset: 0;
            background-image:
                linear-gradient(rgba(99,102,241,0.04) 1px, transparent 1px),
                linear-gradient(90deg, rgba(99,102,241,0.04) 1px, transparent 1px);
            background-size: 36px 36px;
            pointer-events: none;
            z-index: 0;
        }

        /* ── Layout ── */
        .app-shell { display: flex; min-height: 100vh; }
        .main-content {
            margin-left: var(--sidebar-w);
            flex: 1;
            min-width: 0;
            position: relative;
            z-index: 1;
        }
        .main-inner { max-width: 1100px; margin: 0 auto; padding: 1.5rem 1.5rem 3rem; }

        /* ── Glow Blobs ── */
        .blob {
            position: fixed; border-radius: 9999px;
            pointer-events: none; z-index: 0;
        }
        .blob-1 { width:600px; height:600px; top:-160px; left:-128px; background:rgba(99,102,241,0.10); filter:blur(80px); }
        .blob-2 { width:500px; height:500px; bottom:0;   right:-96px;  background:rgba(13,148,136,0.07);  filter:blur(80px); }
        .blob-3 { width:300px; height:300px; top:40%;   left:50%;     background:rgba(225,29,72,0.05);   filter:blur(80px); }

        /* ── Sidebar ── */
        .sidebar {
            position: fixed; top: 0; left: 0;
            width: var(--sidebar-w); height: 100vh;
            background: linear-gradient(180deg, #4f46e5 0%, #6366f1 50%, #0d9488 100%);
            box-shadow: 4px 0 24px rgba(99,102,241,0.25);
            display: flex; flex-direction: column;
            z-index: 100;
            overflow: hidden;
        }
        .sidebar::before {
            content: '';
            position: absolute; inset: 0;
            background: radial-gradient(ellipse at 20% 20%, rgba(255,255,255,0.08) 0%, transparent 60%);
            pointer-events: none;
        }

        /* Sidebar Brand */
        .sidebar-brand {
            padding: 1.25rem 1.1rem 1rem;
            border-bottom: 1px solid rgba(255,255,255,0.14);
            flex-shrink: 0;
        }
        .sidebar-logo {
            width: 50px; height: 50px;
            border-radius: 14px;
            background: rgba(255,255,255,0.18);
            border: 2px solid rgba(255,255,255,0.35);
            display: flex; align-items: center; justify-content: center;
            overflow: hidden; margin-bottom: 10px;
        }
        .sidebar-logo img { width: 38px; height: 38px; object-fit: contain; }
        .sidebar-name { font-size: 15px; font-weight: 800; color: #fff; letter-spacing: -0.02em; }
        .sidebar-sub {
            font-size: 11px; color: rgba(255,255,255,0.65);
            margin-top: 3px; display: flex; align-items: center; gap: 5px;
        }

        /* Sidebar Clock */
        .sidebar-clock {
            padding: 0.8rem 1.1rem;
            border-bottom: 1px solid rgba(255,255,255,0.10);
            flex-shrink: 0;
        }
        .clock-time { font-family: 'JetBrains Mono', monospace; font-size: 20px; font-weight: 600; color: #fff; letter-spacing: -0.02em; }
        .clock-date { font-size: 10px; color: rgba(255,255,255,0.65); margin-top: 2px; line-height: 1.4; }

        /* Sidebar Nav */
        .sidebar-nav { flex: 1; padding: 0.7rem; overflow-y: auto; overflow-x: hidden; }
        .nav-label {
            font-size: 9px; font-weight: 700; color: rgba(255,255,255,0.42);
            text-transform: uppercase; letter-spacing: .10em;
            padding: 0 10px 8px; margin-top: 4px; display: block;
        }
        .nav-item {
            position: relative;
            display: flex; align-items: center; gap: 10px;
            padding: 10px 12px; border-radius: 11px;
            font-size: 13px; font-weight: 600; color: rgba(255,255,255,0.70);
            background: transparent; border: 0; width: 100%;
            cursor: pointer; text-align: left;
            transition: background .15s, color .15s;
            margin-bottom: 2px;
        }
        .nav-item:hover { background: rgba(255,255,255,0.12); color: #fff; }
        .nav-item.active { background: rgba(255,255,255,0.18); color: #fff; }
        .nav-item.active::before {
            content: '';
            position: absolute; left: 0; top: 50%; transform: translateY(-50%);
            width: 3px; height: 60%; background: #fff; border-radius: 0 3px 3px 0;
        }
        .nav-item svg { width: 17px; height: 17px; stroke: currentColor; fill: none; stroke-width: 2; flex-shrink: 0; }

        /* Quick Stats Box */
        .quick-stats-box {
            padding: 10px 12px;
            border-radius: 11px;
            background: rgba(255,255,255,0.08);
            margin-top: 2px;
        }
        .qs-label { font-size: 10px; color: rgba(255,255,255,0.50); font-weight: 700; text-transform: uppercase; letter-spacing: .06em; margin-bottom: 6px; }
        .qs-row { display: flex; justify-content: space-between; font-size: 11px; color: rgba(255,255,255,0.75); margin-bottom: 4px; }
        .qs-row:last-child { margin-bottom: 0; }
        .qs-val { font-weight: 700; }

        /* Sidebar Footer */
        .sidebar-footer {
            padding: 0.8rem 1rem;
            border-top: 1px solid rgba(255,255,255,0.14);
            flex-shrink: 0;
        }
        .logout-btn {
            display: flex; align-items: center; gap: 8px;
            padding: 9px 14px; width: 100%;
            background: rgba(255,255,255,0.12);
            border: 1px solid rgba(255,255,255,0.25);
            border-radius: 10px;
            color: #fff; font-size: 12px; font-weight: 700;
            cursor: pointer; transition: background .15s;
        }
        .logout-btn:hover { background: rgba(255,255,255,0.22); }
        .logout-btn svg { width: 14px; height: 14px; stroke: #fff; fill: none; stroke-width: 2.2; }

        /* ── Live Dot ── */
        .live-dot {
            width: 6px; height: 6px; border-radius: 9999px; flex-shrink: 0;
            background: #a3e635; box-shadow: 0 0 6px #a3e635;
            animation: pulseDot 2s infinite;
        }
        .period-dot {
            width: 6px; height: 6px; border-radius: 9999px;
            background: rgba(255,255,255,0.80);
            animation: pulseDot 2s infinite;
        }
        @keyframes pulseDot {
            0%, 100% { opacity: 1; transform: scale(1); }
            50%       { opacity: .4; transform: scale(.5); }
        }

        /* ── Cards ── */
        .card {
            background: #fff;
            border: 1.5px solid rgba(99,102,241,0.10);
            border-radius: 16px;
            box-shadow: var(--shadow-card);
            overflow: hidden;
            margin-bottom: 1.1rem;
        }
        .card-header {
            display: flex; align-items: center; gap: 10px;
            padding: 12px 20px;
            border-bottom: 1.5px solid rgba(99,102,241,0.10);
            font-size: 13px; font-weight: 700; color: var(--ink);
            background: linear-gradient(90deg, #fafbff, #fff);
            flex-wrap: wrap;
        }
        .card-icon {
            width: 30px; height: 30px; border-radius: 9px;
            background: var(--accent-bg);
            display: flex; align-items: center; justify-content: center;
            flex-shrink: 0;
        }
        .card-icon svg { width: 15px; height: 15px; stroke: var(--accent); fill: none; stroke-width: 2; }
        .card-body { padding: 1.1rem 1.25rem; }

        /* ── Page Title ── */
        .page-title-wrap { display: flex; align-items: center; gap: 12px; margin-bottom: 1.5rem; }
        .page-title-icon {
            width: 38px; height: 38px; border-radius: 12px;
            display: flex; align-items: center; justify-content: center;
            flex-shrink: 0;
            box-shadow: 0 3px 10px rgba(99,102,241,0.30);
        }
        .page-title-icon svg { width: 18px; height: 18px; stroke: #fff; fill: none; stroke-width: 2; }
        .page-title { font-size: 20px; font-weight: 800; color: var(--ink); letter-spacing: -0.02em; }
        .page-subtitle { font-size: 12px; color: var(--ink-muted); margin-top: 2px; }

        /* ── Period Filter Bar ── */
        .period-bar {
            display: flex; align-items: center; gap: 12px;
            background: #fff;
            border: 1.5px solid rgba(99,102,241,0.10);
            border-radius: 16px;
            padding: 13px 20px;
            margin-bottom: 1.5rem;
            box-shadow: var(--shadow-card);
            flex-wrap: wrap;
        }
        .period-bar-icon {
            width: 34px; height: 34px; border-radius: 10px;
            background: var(--accent-bg);
            display: flex; align-items: center; justify-content: center;
            flex-shrink: 0;
        }
        .period-bar-icon svg { width: 16px; height: 16px; stroke: var(--accent); fill: none; stroke-width: 2; }
        .period-bar-label { font-size: 11px; font-weight: 700; color: var(--ink-muted); text-transform: uppercase; letter-spacing: .06em; white-space: nowrap; }

        /* View Pills */
        .view-pills {
            display: flex; gap: 2px;
            background: #f1f5f9; border-radius: 9px; padding: 3px;
            flex-shrink: 0;
        }
        .view-pill {
            padding: 6px 12px; border-radius: 7px;
            font-size: 11px; font-weight: 700;
            border: 0; background: transparent; color: var(--ink-muted);
            cursor: pointer; transition: all .15s; white-space: nowrap;
        }
        .view-pill.active { background: #fff; color: var(--accent); box-shadow: 0 1px 6px rgba(99,102,241,0.15); }
        .view-pill:not(.active):hover { color: var(--accent); }

        /* Period Selects */
        .period-selects { display: flex; gap: 8px; align-items: center; flex-wrap: wrap; }
        .period-select {
            appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 24 24' fill='none' stroke='%236366f1' stroke-width='2.5'%3E%3Cpolyline points='6 9 12 15 18 9'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 10px center;
            padding: 7px 32px 7px 13px;
            font-size: 13px; font-weight: 600;
            border: 1.5px solid rgba(99,102,241,0.20);
            border-radius: 9px; background-color: var(--accent-bg);
            color: var(--ink); outline: none; cursor: pointer;
            transition: border-color .15s, box-shadow .15s;
            font-family: 'Plus Jakarta Sans', sans-serif;
        }
        .period-select:focus { border-color: var(--accent); box-shadow: 0 0 0 3px rgba(99,102,241,0.10); }

        /* Period Badge */
        .period-badge {
            display: inline-flex; align-items: center; gap: 6px;
            padding: 6px 14px; border-radius: 9999px;
            background: linear-gradient(135deg, var(--accent), var(--teal));
            color: #fff; font-size: 12px; font-weight: 700;
            white-space: nowrap;
            box-shadow: 0 3px 10px rgba(99,102,241,0.30);
        }

        /* ── Stat Grid ── */
        .stat-grid { display: grid; grid-template-columns: repeat(5, 1fr); gap: 12px; margin-bottom: 1.5rem; }
        .stat-card {
            background: #fff;
            border: 1.5px solid rgba(99,102,241,0.10);
            border-radius: 16px;
            padding: 1.25rem 1.3rem;
            position: relative; overflow: hidden;
            box-shadow: var(--shadow-card);
            transition: transform .2s, box-shadow .2s;
        }
        .stat-card:hover { transform: translateY(-3px); box-shadow: var(--shadow-hover); }
        .stat-card::before {
            content: ''; position: absolute;
            top: 0; left: 0; right: 0; height: 3px;
        }
        .stat-card.c-total::before  { background: linear-gradient(90deg, #6366f1, #818cf8); }
        .stat-card.c-tepat::before  { background: linear-gradient(90deg, #16a34a, #4ade80); }
        .stat-card.c-lambat::before { background: linear-gradient(90deg, #d97706, #fbbf24); }
        .stat-card.c-cepat::before  { background: linear-gradient(90deg, #e11d48, #fb7185); }
        .stat-card.c-absent::before { background: linear-gradient(90deg, #7c3aed, #a78bfa); }
        .stat-icon {
            width: 40px; height: 40px; border-radius: 12px;
            display: flex; align-items: center; justify-content: center;
            margin-bottom: 12px;
        }
        .stat-icon svg { width: 20px; height: 20px; fill: none; stroke-width: 1.8; }
        .stat-label { font-size: 10px; font-weight: 700; color: var(--ink-muted); text-transform: uppercase; letter-spacing: .07em; margin-bottom: 5px; }
        .stat-val { font-size: 34px; font-weight: 800; letter-spacing: -0.02em; line-height: 1; }
        @keyframes statPop {
            0%   { transform: scale(.9); opacity: .5; }
            60%  { transform: scale(1.05); }
            100% { transform: scale(1); opacity: 1; }
        }
        .stat-val.pop { animation: statPop .3s ease; }

        /* ── Upload Card ── */
        .file-input {
            flex: 1; min-width: 200px;
            padding: 10px 14px; font-size: 13px;
            border: 1.5px dashed rgba(99,102,241,0.30);
            border-radius: 10px; background: var(--accent-bg);
            color: var(--ink); font-family: 'Plus Jakarta Sans', sans-serif;
            cursor: pointer; transition: border-color .15s;
        }
        .file-input:hover { border-color: var(--accent); }
        .upload-btn {
            display: flex; align-items: center; gap: 8px;
            padding: 10px 22px; font-size: 13px; font-weight: 700;
            color: #fff; border: 0; border-radius: 10px; cursor: pointer;
            background: linear-gradient(135deg, var(--accent), var(--accent-dark));
            box-shadow: var(--shadow-btn); white-space: nowrap;
            transition: opacity .15s;
        }
        .upload-btn:hover { opacity: .9; }
        .upload-btn svg { width: 14px; height: 14px; stroke: #fff; fill: none; stroke-width: 2.5; }
        .chip-csv {
            margin-left: auto; font-size: 10px; font-weight: 700;
            padding: 3px 10px; border-radius: 9999px;
            background: var(--accent-bg); color: var(--accent);
            border: 1px solid rgba(99,102,241,0.20); letter-spacing: .04em;
        }
        .chip-period {
            font-size: 10px; font-weight: 700;
            padding: 3px 10px; border-radius: 9999px;
            background: var(--accent-bg); color: var(--accent);
            border: 1px solid rgba(99,102,241,0.20);
        }

        /* ── Donut Chart ── */
        .donut-wrap { display: flex; flex-wrap: wrap; gap: 28px; align-items: center; justify-content: center; padding: 1.25rem; }
        .donut-canvas-wrap { position: relative; width: 220px; height: 220px; flex-shrink: 0; }
        .donut-center {
            position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%);
            text-align: center; pointer-events: none;
        }
        .donut-center-num { font-size: 30px; font-weight: 800; color: var(--ink); letter-spacing: -0.02em; line-height: 1; transition: color .2s; }
        .donut-center-lbl { font-size: 11px; color: var(--ink-muted); margin-top: 3px; font-weight: 600; }
        .donut-legends { display: flex; flex-direction: column; gap: 8px; flex: 1; min-width: 220px; }
        .legend-item {
            display: flex; align-items: center; gap: 12px;
            padding: 11px 14px; border-radius: 12px;
            cursor: pointer; transition: transform .15s, box-shadow .15s;
            border-left: 4px solid transparent;
        }
        .legend-item:hover { transform: translateX(3px); box-shadow: 0 2px 8px rgba(0,0,0,0.08); }
        .legend-dot { width: 14px; height: 14px; border-radius: 5px; flex-shrink: 0; }
        .legend-name { font-size: 13px; font-weight: 600; color: var(--ink); flex: 1; }
        .legend-val { font-size: 15px; font-weight: 800; }
        .legend-pct { font-size: 11px; font-weight: 500; opacity: .7; margin-left: 3px; }

        /* ── Filter Bar ── */
        .filter-grid { display: grid; grid-template-columns: 1fr 1fr 1fr auto; gap: 12px; align-items: end; }
        .filter-field { display: flex; flex-direction: column; gap: 5px; }
        .filter-label { font-size: 10px; font-weight: 700; color: var(--ink-muted); text-transform: uppercase; letter-spacing: .07em; }
        .filter-input, .filter-select {
            padding: 9px 12px; font-size: 13px;
            border: 1.5px solid rgba(99,102,241,0.14);
            border-radius: 10px; background: #fafbff; color: var(--ink);
            font-family: 'Plus Jakarta Sans', sans-serif;
            outline: none; transition: border-color .15s, box-shadow .15s;
        }
        .filter-input:focus, .filter-select:focus {
            border-color: var(--accent);
            box-shadow: 0 0 0 3px rgba(99,102,241,0.10);
        }
        .reset-btn {
            padding: 9px 16px; font-size: 12px; font-weight: 600;
            background: #f1f5f9; color: var(--ink-muted);
            border: 1.5px solid #e2e8f0; border-radius: 10px;
            cursor: pointer; white-space: nowrap;
            transition: border-color .15s, color .15s, background .15s;
        }
        .reset-btn:hover { border-color: var(--accent); color: var(--accent); background: var(--accent-bg); }

        /* ── Table ── */
        .table-container { overflow-x: auto; }
        table { width: 100%; border-collapse: collapse; font-size: 13px; }
        thead tr { background: #f8faff; }
        thead th {
            padding: 11px 16px;
            text-align: left; font-size: 10px; font-weight: 700;
            color: var(--ink-muted); text-transform: uppercase; letter-spacing: .07em;
            border-bottom: 1.5px solid rgba(99,102,241,0.10);
            white-space: nowrap;
        }
        tbody tr { transition: background .1s; }
        tbody tr:hover td { background: #f5f7ff; }
        tbody tr.row-absent td { background: #fdf4ff; }
        tbody tr.row-absent:hover td { background: #f3e8ff; }
        td {
            padding: 11px 16px;
            border-bottom: 1px solid rgba(99,102,241,0.06);
            vertical-align: middle;
        }
        .td-no { font-family: 'JetBrains Mono', monospace; font-size: 11px; color: var(--ink-light); }
        @keyframes rowIn {
            from { opacity: 0; transform: translateY(6px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        /* ── Karyawan Nav Strip ── */
        .karyawan-strip {
            display: flex; align-items: center; gap: 8px;
            padding: 10px 20px; background: #fafbff;
            border-bottom: 1.5px solid rgba(99,102,241,0.10);
            overflow-x: auto;
        }
        .karyawan-strip-label { font-size: 11px; font-weight: 700; color: var(--ink-muted); text-transform: uppercase; letter-spacing: .06em; white-space: nowrap; flex-shrink: 0; }
        .kary-nav-btn {
            padding: 5px 12px; border-radius: 9999px;
            font-size: 11px; font-weight: 700;
            border: 1.5px solid rgba(99,102,241,0.14);
            background: #fff; color: var(--ink-muted);
            cursor: pointer; white-space: nowrap; flex-shrink: 0;
            transition: all .15s;
        }
        .kary-nav-btn:hover { border-color: var(--accent); color: var(--accent); background: var(--accent-bg); }
        .kary-nav-btn.active { background: var(--accent); color: #fff; border-color: var(--accent); }

        /* ── Emp Table Header ── */
        .emp-header {
            display: flex; align-items: center; gap: 16px;
            padding: 16px 20px;
            background: linear-gradient(135deg, #eef2ff, #f0fdf4);
            border-bottom: 1.5px solid rgba(99,102,241,0.12);
            flex-wrap: wrap;
        }
        .emp-avatar {
            width: 56px; height: 56px; border-radius: 16px;
            display: flex; align-items: center; justify-content: center;
            font-size: 18px; font-weight: 800; color: #fff;
            flex-shrink: 0; box-shadow: 0 4px 16px rgba(0,0,0,0.18);
        }
        .emp-name { font-size: 18px; font-weight: 800; color: var(--ink); letter-spacing: -0.01em; }
        .emp-meta { display: flex; align-items: center; gap: 10px; margin-top: 4px; flex-wrap: wrap; }
        .emp-pin { font-family: 'JetBrains Mono', monospace; font-size: 11px; font-weight: 600; background: var(--accent); color: #fff; padding: 3px 10px; border-radius: 6px; }
        .emp-days { font-size: 12px; color: var(--ink-muted); font-weight: 600; }
        .emp-stats { display: flex; gap: 8px; flex-wrap: wrap; margin-left: auto; }
        .emp-stat-pill {
            display: flex; align-items: center; gap: 5px;
            padding: 5px 12px; border-radius: 9999px;
            font-size: 11px; font-weight: 700;
        }

        /* ── Badges & Pills ── */
        .badge-absent {
            display: inline-flex; align-items: center; gap: 5px;
            padding: 5px 11px; border-radius: 8px;
            background: #f3e8ff; color: #6d28d9;
            border: 1px solid #ddd6fe; font-size: 12px; font-weight: 700;
        }
        .badge-absent svg { width: 12px; height: 12px; stroke: #6d28d9; fill: none; stroke-width: 2; }
        .time-badge {
            display: inline-flex; align-items: center; gap: 6px;
            padding: 5px 11px; border-radius: 8px;
            font-family: 'JetBrains Mono', monospace; font-size: 12px; font-weight: 600;
        }
        .time-badge svg { width: 12px; height: 12px; fill: none; stroke-width: 2; }
        .time-badge.ok     { background: #dcfce7; color: #15803d; border: 1px solid #bbf7d0; }
        .time-badge.ok svg { stroke: #15803d; }
        .time-badge.late     { background: #fef3c7; color: #92400e; border: 1px solid #fde68a; }
        .time-badge.late svg { stroke: #d97706; }
        .time-badge.early     { background: #ffe4e6; color: #be123c; border: 1px solid #fecdd3; }
        .time-badge.early svg { stroke: #e11d48; }

        .status-pill { display: inline-block; padding: 3px 10px; border-radius: 9999px; font-size: 11px; font-weight: 700; }
        .status-pill.tepat   { background: #dcfce7; color: #15803d; border: 1px solid #bbf7d0; }
        .status-pill.lambat  { background: #fef3c7; color: #92400e; border: 1px solid #fde68a; }
        .status-pill.cepat   { background: #ffe4e6; color: #be123c; border: 1px solid #fecdd3; }
        .status-pill.absent  { background: #f3e8ff; color: #6d28d9; border: 1px solid #ddd6fe; }

        /* Day name badges */
        .day-badge { display: inline-flex; align-items: center; gap: 5px; padding: 2px 9px; border-radius: 9999px; font-size: 10px; font-weight: 700; letter-spacing: .04em; }
        .day-senin   { background:#eef2ff; color:#4338ca; border:1px solid #c7d2fe; }
        .day-selasa  { background:#f0fdf4; color:#15803d; border:1px solid #bbf7d0; }
        .day-rabu    { background:#fffbeb; color:#92400e; border:1px solid #fde68a; }
        .day-kamis   { background:#fff1f2; color:#be123c; border:1px solid #fecdd3; }
        .day-jumat   { background:#f5f3ff; color:#6d28d9; border:1px solid #ddd6fe; }
        .day-sabtu   { background:#ecfeff; color:#0e7490; border:1px solid #a5f3fc; }
        .day-minggu  { background:#fff7ed; color:#c2410c; border:1px solid #fed7aa; }

        /* ── Pagination ── */
        .pagination-bar {
            display: flex; align-items: center; justify-content: space-between;
            padding: 12px 20px; flex-wrap: wrap; gap: 10px;
            border-top: 1px solid rgba(99,102,241,0.08);
            background: #fafbff;
        }
        .pg-info { font-size: 12px; color: var(--ink-muted); font-weight: 500; }
        .pg-btns { display: flex; gap: 4px; align-items: center; flex-wrap: wrap; }
        .pg-btn {
            min-width: 32px; height: 32px; padding: 0 8px;
            border: 1.5px solid rgba(99,102,241,0.14); background: #fff;
            color: var(--ink); border-radius: 8px; font-size: 12px; font-weight: 600;
            cursor: pointer; display: flex; align-items: center; justify-content: center;
            transition: all .15s;
        }
        .pg-btn:hover:not(:disabled) { border-color: var(--accent); color: var(--accent); background: var(--accent-bg); }
        .pg-btn.active { background: var(--accent); color: #fff; border-color: var(--accent); font-weight: 700; }
        .pg-btn:disabled { opacity: .35; cursor: not-allowed; }
        .pg-ellipsis { font-size: 13px; color: var(--ink-light); padding: 0 4px; }

        /* ── Empty State ── */
        .empty-state { text-align: center; padding: 56px 16px; }
        .empty-icon { font-size: 40px; margin-bottom: 14px; }
        .empty-title { font-size: 14px; font-weight: 700; color: var(--ink-muted); }
        .empty-sub { font-size: 12px; color: var(--ink-light); margin-top: 6px; }

        /* ── Count Chip ── */
        .count-chip { margin-left: auto; font-size: 10px; font-weight: 700; padding: 3px 10px; border-radius: 9999px; background: var(--accent-bg); color: var(--accent); border: 1px solid rgba(99,102,241,0.20); }

        /* ── Print Button ── */
        .print-btn {
            display: flex; align-items: center; gap: 8px;
            margin-left: auto; padding: 8px 18px;
            font-size: 12px; font-weight: 700; color: #fff; border: 0;
            border-radius: 10px; cursor: pointer; white-space: nowrap;
            background: linear-gradient(135deg, var(--accent), var(--accent-dark));
            box-shadow: 0 4px 14px rgba(99,102,241,0.30);
            transition: opacity .15s;
        }
        .print-btn:hover { opacity: .9; }
        .print-btn svg { width: 14px; height: 14px; stroke: #fff; fill: none; stroke-width: 2; }

        /* ── Loading Overlay ── */
        .loading-overlay {
            position: fixed; inset: 0; z-index: 9999;
            display: none; flex-direction: column;
            align-items: center; justify-content: center; gap: 20px;
            background: rgba(240,244,255,0.90); backdrop-filter: blur(6px);
        }
        .loading-overlay.show { display: flex; }
        .spinner {
            width: 48px; height: 48px;
            border: 3px solid #e0e7ff; border-top-color: var(--accent);
            border-radius: 9999px; animation: spin .7s linear infinite;
        }
        @keyframes spin { to { transform: rotate(360deg); } }
        .loading-text { font-size: 13px; color: var(--ink-muted); font-weight: 600; }
        .loading-bar-wrap { width: 176px; height: 3px; background: #e0e7ff; border-radius: 9999px; overflow: hidden; }
        .loading-bar {
            height: 100%; background: linear-gradient(90deg, var(--accent), var(--teal));
            border-radius: 9999px;
            animation: barSlide 1.4s ease-in-out infinite;
        }
        @keyframes barSlide { 0% { transform: translateX(-200%); } 100% { transform: translateX(400%); } }

        /* ── Alert ── */
        .alert { padding: 10px 14px; border-radius: 10px; font-size: 13px; font-weight: 600; margin-bottom: 1rem; }
        .alert-success { background: var(--green-bg); border: 1px solid #bbf7d0; color: #15803d; }
        .alert-error   { background: var(--rose-bg);  border: 1px solid #fecdd3; color: #be123c; }

        /* ── Pages ── */
        .page { display: none; }
        .page.active { display: block; }

        /* ── Performa Page ── */
        .emp-selector-grid { display: grid; gap: 12px; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); }
        .emp-selector-card {
            position: relative; background: #fafbff;
            border: 2px solid rgba(99,102,241,0.12); border-radius: 14px;
            padding: 14px; cursor: pointer; overflow: hidden;
            transition: border-color .2s, background .2s, transform .2s, box-shadow .2s;
        }
        .emp-selector-card:hover { border-color: rgba(99,102,241,0.35); background: #fff; transform: translateY(-2px); box-shadow: var(--shadow-hover); }
        .emp-selector-card.selected { border-color: var(--accent); background: #fff; box-shadow: var(--shadow-hover); }
        .emp-selector-card.selected::before {
            content: ''; position: absolute; top: 0; left: 0; right: 0; height: 3px;
            background: linear-gradient(90deg, var(--accent), var(--teal));
        }
        .emp-card-avatar {
            width: 42px; height: 42px; border-radius: 12px;
            display: flex; align-items: center; justify-content: center;
            font-size: 14px; font-weight: 800; color: #fff; flex-shrink: 0;
        }
        .emp-card-name { font-size: 13px; font-weight: 800; color: var(--ink); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 130px; }
        .emp-card-pin { font-family: 'JetBrains Mono', monospace; font-size: 10px; color: var(--ink-muted); margin-top: 2px; }
        .emp-stat-mini { display: grid; grid-template-columns: repeat(4,1fr); gap: 5px; margin-top: 10px; }
        .emp-stat-mini-cell { text-align: center; padding: 7px 4px; border-radius: 9px; background: rgba(255,255,255,0.80); border: 1px solid rgba(0,0,0,0.04); }
        .emp-stat-mini-val { font-size: 15px; font-weight: 800; line-height: 1; }
        .emp-stat-mini-lbl { font-size: 8px; font-weight: 700; color: var(--ink-muted); text-transform: uppercase; letter-spacing: .05em; margin-top: 2px; }
        .emp-absent-warn { margin-top: 10px; padding: 6px 10px; background: #f3e8ff; border: 1px solid #ddd6fe; border-radius: 9px; font-size: 11px; font-weight: 700; color: #7c3aed; }
        .emp-check-badge {
            position: absolute; top: 10px; right: 10px;
            width: 20px; height: 20px; border-radius: 9999px;
            background: var(--accent);
            align-items: center; justify-content: center;
            display: none;
        }
        .emp-selector-card.selected .emp-check-badge { display: flex; }
        .emp-check-badge svg { width: 10px; height: 10px; stroke: #fff; fill: none; stroke-width: 3; }

        /* Bar mode buttons */
        .bar-mode-btn {
            padding: 7px 16px; border-radius: 9px; font-size: 11px; font-weight: 700;
            border: 1.5px solid rgba(99,102,241,0.20); background: #fff; color: var(--ink-muted);
            cursor: pointer; white-space: nowrap; transition: all .15s;
        }
        .bar-mode-btn:hover { border-color: var(--accent); color: var(--accent); background: var(--accent-bg); }
        .bar-mode-btn.active { background: linear-gradient(135deg, var(--accent), var(--accent-dark)); color: #fff; border-color: var(--accent); }

        /* Yearly stat strip */
        .yearly-stat-strip { display: flex; gap: 10px; padding: 12px 20px; flex-wrap: wrap; border-top: 1px solid rgba(99,102,241,0.08); background: #fafbff; }
        .yearly-stat-item { display: flex; align-items: center; gap: 10px; padding: 10px 16px; border-radius: 14px; flex: 1; min-width: 110px; }
        .yearly-stat-icon { width: 34px; height: 34px; border-radius: 10px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
        .yearly-stat-icon svg { width: 16px; height: 16px; fill: none; stroke-width: 2; }
        .yearly-stat-num { font-size: 22px; font-weight: 800; line-height: 1; }
        .yearly-stat-lbl { font-size: 11px; color: var(--ink-muted); font-weight: 600; margin-top: 2px; }

        /* Custom chart legend */
        .chart-legend { display: flex; flex-wrap: wrap; gap: 10px; padding: 12px 20px; justify-content: center; }
        .chart-legend-item { display: flex; align-items: center; gap: 6px; font-size: 11px; font-weight: 700; color: var(--ink-muted); }
        .legend-bar  { width: 20px; height: 3px; border-radius: 2px; flex-shrink: 0; }
        .legend-square { width: 12px; height: 12px; border-radius: 3px; flex-shrink: 0; }

        /* Hint card */
        .hint-card { display: flex; flex-direction: column; align-items: center; justify-content: center; padding: 48px 16px; gap: 12px; }
        .hint-icon { width: 64px; height: 64px; border-radius: 20px; background: linear-gradient(135deg, #eef2ff, #e0e7ff); display: flex; align-items: center; justify-content: center; }
        .hint-icon svg { width: 30px; height: 30px; stroke: var(--accent); fill: none; stroke-width: 1.5; }
        .hint-title { font-size: 15px; font-weight: 700; color: var(--ink-muted); }
        .hint-sub { font-size: 12px; color: var(--ink-light); }

        .chart-panel { display: none; }
        .chart-panel.show { display: block; }

        /* ── Mobile Toggle ── */
        .sidebar-toggle {
            position: fixed; top: 14px; left: 14px; z-index: 200;
            width: 38px; height: 38px; border-radius: 10px;
            background: var(--accent); border: 0; cursor: pointer;
            display: none; align-items: center; justify-content: center;
            box-shadow: var(--shadow-btn);
        }
        .sidebar-toggle svg { width: 18px; height: 18px; stroke: #fff; fill: none; stroke-width: 2; }
        .sidebar-overlay { display: none; position: fixed; inset: 0; background: rgba(0,0,0,.4); z-index: 99; }

        /* ── Responsive ── */
        @media (max-width: 900px) {
            .sidebar { transform: translateX(-100%); transition: transform .3s ease; }
            .sidebar.open { transform: translateX(0); }
            .main-content { margin-left: 0 !important; }
            .sidebar-toggle { display: flex; }
            .stat-grid { grid-template-columns: repeat(2, 1fr); }
            .filter-grid { grid-template-columns: 1fr 1fr; }
        }
        @media (max-width: 560px) {
            .stat-grid { grid-template-columns: 1fr; }
            .filter-grid { grid-template-columns: 1fr; }
        }

        /* ── Print ── */
        @media print {
            .sidebar, .sidebar-toggle, .sidebar-overlay, .loading-overlay,
            .no-print { display: none !important; }
            .main-content { margin-left: 0 !important; }
            body { background: #fff !important; }
            body::before { display: none !important; }
        }

        /* ── Misc ── */
        .file-info { font-size: 11px; color: var(--ink-muted); margin-top: 8px; display: none; }
        .footer-text { text-align: center; padding: 24px 0 8px; font-size: 12px; color: var(--ink-muted); }
        .footer-text .accent { color: var(--accent); font-weight: 700; }
    </style>
</head>
<body>

<!-- Glow Blobs -->
<div class="blob blob-1"></div>
<div class="blob blob-2"></div>
<div class="blob blob-3"></div>

<!-- Loading Overlay -->
<div class="loading-overlay" id="loadingOverlay">
    <div class="spinner"></div>
    <div class="loading-text">Memproses data absensi...</div>
    <div class="loading-bar-wrap"><div class="loading-bar"></div></div>
</div>

<!-- Mobile Toggle -->
<button class="sidebar-toggle no-print" id="sidebarToggle" onclick="toggleSidebar()">
    <svg viewBox="0 0 24 24"><line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="18" x2="21" y2="18"/></svg>
</button>
<div class="sidebar-overlay" id="sidebarOverlay" onclick="toggleSidebar()"></div>

<!-- ══ SIDEBAR ══ -->
<aside class="sidebar" id="sidebar">
    <!-- Brand -->
    <div class="sidebar-brand">
        <div class="sidebar-logo">
            <img src="{{ asset('images/kipin.png') }}" alt="Kipin" onerror="this.style.display='none'">
        </div>
        <div class="sidebar-name">Kipin Absensi</div>
        <div class="sidebar-sub">
            <span class="live-dot"></span>
            Monitoring Real-time
        </div>
    </div>

    <!-- Clock -->
    <div class="sidebar-clock">
        <div class="clock-time" id="liveClock"></div>
        <div class="clock-date"  id="liveDate"></div>
    </div>

    <!-- Nav -->
    <nav class="sidebar-nav">
        <span class="nav-label">Menu</span>
        <button class="nav-item active" id="tabDashboard" onclick="switchPage('dashboard')">
            <svg viewBox="0 0 24 24"><rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/></svg>
            Dashboard &amp; Tabel
        </button>
        <button class="nav-item" id="tabPerforma" onclick="switchPage('performa')">
            <svg viewBox="0 0 24 24"><rect x="2" y="13" width="4" height="9" rx="1"/><rect x="9" y="9" width="4" height="13" rx="1"/><rect x="16" y="5" width="4" height="17" rx="1"/><path d="M4 6l4-3 4 3 4-4"/></svg>
            Chart Performa
        </button>

        <span class="nav-label" style="margin-top:12px">Info</span>
        <div class="quick-stats-box">
            <div class="qs-label">Statistik Cepat</div>
            <div class="qs-row"><span>Total Absensi</span><span class="qs-val" id="sqTotal">—</span></div>
            <div class="qs-row"><span>Tepat Waktu</span><span class="qs-val" style="color:#86efac" id="sqTepat">—</span></div>
            <div class="qs-row"><span>Terlambat</span><span class="qs-val" style="color:#fcd34d" id="sqLambat">—</span></div>
            <div class="qs-row"><span>Tidak Masuk</span><span class="qs-val" style="color:#c4b5fd" id="sqAbsent">—</span></div>
        </div>
    </nav>

    <!-- Footer -->
    <div class="sidebar-footer">
        <form action="{{ route('logout') }}" method="POST">@csrf
            <button type="submit" class="logout-btn">
                <svg viewBox="0 0 24 24"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
                Logout
            </button>
        </form>
    </div>
</aside>

<!-- ══ MAIN CONTENT ══ -->
<div class="main-content">
<div class="main-inner">

    <!-- ═══ PAGE DASHBOARD ═══ -->
    <div class="page active" id="pageDashboard">

        <!-- Page Title -->
        <div class="page-title-wrap">
            <div class="page-title-icon" style="background:linear-gradient(135deg,#6366f1,#4f46e5)">
                <svg viewBox="0 0 24 24"><rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/></svg>
            </div>
            <div>
                <div class="page-title">Dashboard Absensi</div>
                <div class="page-subtitle">Monitoring kehadiran karyawan secara real-time</div>
            </div>
        </div>

        <!-- Period Filter Bar -->
        <div class="period-bar">
            <div class="period-bar-icon">
                <svg viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="18" rx="2"/><path d="M16 2v4M8 2v4M3 10h18"/></svg>
            </div>
            <span class="period-bar-label">Tampilan</span>
            <div class="view-pills">
                <button class="view-pill"        id="vpHarian"   onclick="setViewMode('harian')">Harian</button>
                <button class="view-pill"        id="vpMingguan" onclick="setViewMode('mingguan')">Mingguan</button>
                <button class="view-pill active" id="vpBulanan"  onclick="setViewMode('bulanan')">Bulanan</button>
                <button class="view-pill"        id="vpTahunan"  onclick="setViewMode('tahunan')">Tahunan</button>
            </div>
            <div class="period-selects">
                <select class="period-select" id="filterTahun"  onchange="onPeriodChange()"></select>
                <select class="period-select" id="filterBulan"  onchange="onPeriodChange()">
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
            <div class="period-badge">
                <span class="period-dot"></span>
                <span id="periodBadgeText">—</span>
            </div>
        </div>

        <!-- Stat Grid -->
        <div class="stat-grid">
            <div class="stat-card c-total">
                <div class="stat-icon" style="background:#eef2ff">
                    <svg style="stroke:#6366f1" viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="18" rx="2"/><path d="M16 2v4M8 2v4M3 10h18"/></svg>
                </div>
                <div class="stat-label">Total Absensi</div>
                <div class="stat-val" style="color:#6366f1" id="statTotal">0</div>
            </div>
            <div class="stat-card c-tepat">
                <div class="stat-icon" style="background:#dcfce7">
                    <svg style="stroke:#16a34a" viewBox="0 0 24 24"><circle cx="12" cy="12" r="9"/><path d="M9 12l2 2 4-4"/></svg>
                </div>
                <div class="stat-label">Tepat Waktu</div>
                <div class="stat-val" style="color:#16a34a" id="statTepat">0</div>
            </div>
            <div class="stat-card c-lambat">
                <div class="stat-icon" style="background:#fef3c7">
                    <svg style="stroke:#d97706" viewBox="0 0 24 24"><circle cx="12" cy="12" r="9"/><path d="M12 8v4"/><circle cx="12" cy="16" r=".5" fill="#d97706"/></svg>
                </div>
                <div class="stat-label">Terlambat</div>
                <div class="stat-val" style="color:#d97706" id="statLambat">0</div>
            </div>
            <div class="stat-card c-cepat">
                <div class="stat-icon" style="background:#ffe4e6">
                    <svg style="stroke:#e11d48" viewBox="0 0 24 24"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
                </div>
                <div class="stat-label">Pulang Cepat</div>
                <div class="stat-val" style="color:#e11d48" id="statCepat">0</div>
            </div>
            <div class="stat-card c-absent">
                <div class="stat-icon" style="background:#f3e8ff">
                    <svg style="stroke:#7c3aed" viewBox="0 0 24 24"><circle cx="12" cy="8" r="4"/><path d="M4 20c0-4 3.58-7 8-7s8 3 8 7"/><line x1="17" y1="11" x2="23" y2="11"/></svg>
                </div>
                <div class="stat-label">Tidak Masuk</div>
                <div class="stat-val" style="color:#7c3aed" id="statAbsent">0</div>
            </div>
        </div>

        @if(session('success'))
        <div class="alert alert-success" id="alertSuccess">{!! session('success') !!}</div>
        @endif
        @if(session('error'))
        <div class="alert alert-error" id="alertError">{!! session('error') !!}</div>
        @endif
        <script>
            setTimeout(function(){
                ['alertSuccess','alertError'].forEach(function(id){
                    var el=document.getElementById(id);
                    if(el){el.style.transition='opacity .8s';el.style.opacity='0';setTimeout(function(){el.remove();},800);}
                });
            },12000);
        </script>



        <!-- Donut Chart Card -->
        <div class="card">
            <div class="card-header">
                <div class="card-icon"><svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="9"/><path d="M12 3a9 9 0 0 1 9 9h-9z"/></svg></div>
                Grafik Ringkasan —
                <span style="color:var(--accent);margin-left:4px" id="chartPeriodLabel">—</span>
                <span class="chip-period" id="chartViewChip">Bulanan</span>
                <button class="print-btn no-print" onclick="window.print()">
                    <svg viewBox="0 0 24 24"><polyline points="6 9 6 2 18 2 18 9"/><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/><rect x="6" y="14" width="12" height="8"/></svg>
                    Print / PDF
                </button>
            </div>
            <div class="donut-wrap">
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

        <!-- Filter Card -->
        <div class="card">
            <div class="card-header">
                <div class="card-icon"><svg viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg></div>
                Filter &amp; Pencarian
            </div>
            <div class="card-body">
                <div class="filter-grid">
                    <div class="filter-field">
                        <label class="filter-label">Cari Nama</label>
                        <input class="filter-input" type="text" id="searchName" placeholder="Nama karyawan..." oninput="applyFiltersAndRender()">
                    </div>
                    <div class="filter-field">
                        <label class="filter-label">Tanggal</label>
                        <input class="filter-input" type="date" id="filterDate" onchange="applyFiltersAndRender()">
                    </div>
                    <div class="filter-field">
                        <label class="filter-label">Keterangan</label>
                        <select class="filter-select" id="filterKet" onchange="applyFiltersAndRender()">
                            <option value="">Semua</option>
                            <option value="tepat">Tepat Waktu</option>
                            <option value="terlambat">Terlambat</option>
                            <option value="cepat">Pulang Cepat</option>
                            <option value="absent">Tidak Masuk</option>
                        </select>
                    </div>
                    <div>
                        <button class="reset-btn" onclick="resetFilter()">&#8635; Reset</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Table Card -->
        <div class="card">
            <div class="card-header">
                <div class="card-icon"><svg viewBox="0 0 24 24"><rect x="3" y="3" width="18" height="18" rx="2"/><path d="M3 9h18M9 21V9"/></svg></div>
                Data Presensi —
                <span style="color:var(--accent);margin-left:4px" id="tablePeriodLabel">—</span>
                <span class="count-chip" id="countChip">0 data</span>
            </div>
            <div class="karyawan-strip" id="karyawanNavStrip" style="display:none">
                <span class="karyawan-strip-label">Karyawan:</span>
            </div>
            <div class="emp-header" id="empTableHeader" style="display:none">
                <div class="emp-avatar" id="empTableAvatar">??</div>
                <div>
                    <div class="emp-name" id="empTableName">—</div>
                    <div class="emp-meta">
                        <span class="emp-pin" id="empTablePin">—</span>
                        <span class="emp-days" id="empTableDays">0 hari</span>
                    </div>
                </div>
                <div class="emp-stats" id="empTableStats"></div>
            </div>
            <div class="table-container">
                <table id="mainTable">
                    <thead id="tableHead">
                        <tr>
                            <th>No</th><th>Tanggal</th><th>Jam Masuk</th><th>Jam Pulang</th><th>Status</th>
                        </tr>
                    </thead>
                    <tbody id="tableBody"></tbody>
                </table>
            </div>
            <div class="pagination-bar" id="pgBar">
                <div class="pg-info" id="pgInfo">—</div>
                <div class="pg-btns" id="pgBtns"></div>
            </div>
        </div>

    </div><!-- /pageDashboard -->

    <!-- ═══ PAGE PERFORMA ═══ -->
    <div class="page" id="pagePerforma">

        <div class="page-title-wrap">
            <div class="page-title-icon" style="background:linear-gradient(135deg,#0d9488,#059669)">
                <svg viewBox="0 0 24 24"><rect x="2" y="13" width="4" height="9" rx="1"/><rect x="9" y="9" width="4" height="13" rx="1"/><rect x="16" y="5" width="4" height="17" rx="1"/><path d="M4 6l4-3 4 3 4-4"/></svg>
            </div>
            <div>
                <div class="page-title">Chart Performa Karyawan</div>
                <div class="page-subtitle">Analisis kehadiran dan keterlambatan per karyawan</div>
            </div>
        </div>

        <!-- Period Bar Performa -->
        <div class="period-bar">
            <div class="period-bar-icon">
                <svg viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="18" rx="2"/><path d="M16 2v4M8 2v4M3 10h18"/></svg>
            </div>
            <span class="period-bar-label">Periode</span>
            <div class="period-selects">
                <select class="period-select" id="filterTahunPerf" onchange="renderPerfPage()"></select>
                <select class="period-select" id="filterBulanPerf" onchange="renderPerfPage()">
                    <option value="0">Semua Bulan</option>
                    <option value="1">Januari</option><option value="2">Februari</option><option value="3">Maret</option>
                    <option value="4">April</option><option value="5">Mei</option><option value="6">Juni</option>
                    <option value="7">Juli</option><option value="8">Agustus</option><option value="9">September</option>
                    <option value="10">Oktober</option><option value="11">November</option><option value="12">Desember</option>
                </select>
            </div>
            <div class="period-badge">
                <span class="period-dot"></span>
                <span id="perfYearBadge">—</span>
            </div>
            <div style="margin-left:auto;font-size:12px;color:var(--ink-muted);font-weight:600" id="perfPeriodHint"></div>
        </div>

        <!-- Emp Selector Card -->
        <div class="card">
            <div class="card-header">
                <div class="card-icon"><svg viewBox="0 0 24 24"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75"/></svg></div>
                Pilih Karyawan
                <span class="chip-period" id="empCountChip">0 karyawan</span>
                <span style="font-size:11px;color:var(--ink-muted);font-weight:500;margin-left:4px">← Klik untuk lihat chart</span>
            </div>
            <div class="card-body">
                <div class="emp-selector-grid" id="empSelectorGrid"></div>
            </div>
        </div>

        <!-- Chart Panel -->
        <div class="card chart-panel" id="empChartCard">
            <div class="card-header">
                <div style="display:flex;align-items:center;gap:12px;flex:1">
                    <div class="emp-card-avatar" id="bcAvatar" style="width:44px;height:44px;border-radius:12px">??</div>
                    <div>
                        <div style="font-size:16px;font-weight:800;color:var(--ink)" id="bcName">—</div>
                        <div style="font-family:'JetBrains Mono',monospace;font-size:11px;color:var(--ink-muted);margin-top:3px" id="bcPin">—</div>
                    </div>
                </div>
                <div style="display:flex;gap:6px;flex-shrink:0;flex-wrap:wrap">
                    <button class="bar-mode-btn active" id="btnModeAll"   onclick="setBarMode('all')">Semua Data</button>
                    <button class="bar-mode-btn"        id="btnModeHadir" onclick="setBarMode('hadir')">Kehadiran</button>
                    <button class="bar-mode-btn"        id="btnModeLate"  onclick="setBarMode('late')">Keterlambatan</button>
                </div>
            </div>
            <div class="chart-legend" id="chartLegendCustom"></div>
            <div style="padding:1.25rem 1.25rem 1rem">
                <div style="position:relative;height:400px"><canvas id="empBarChart"></canvas></div>
            </div>
            <div class="yearly-stat-strip" id="yearlyStatStrip"></div>
        </div>

        <!-- Chart Hint -->
        <div class="card" id="empChartHint">
            <div class="hint-card">
                <div class="hint-icon">
                    <svg viewBox="0 0 24 24"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/></svg>
                </div>
                <div class="hint-title">Pilih karyawan di atas untuk melihat chart performa</div>
                <div class="hint-sub">Chart menampilkan data kehadiran per bulan / per hari</div>
            </div>
        </div>

    </div><!-- /pagePerforma -->

    <div class="footer-text">&copy; {{ date('Y') }} <span class="accent">Kipin</span> &mdash; Sistem Monitoring Absensi &middot; Sakera</div>
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
/* ── Constants ── */
var NAMA_BULAN   = ['','Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
var NAMA_BULAN_S = ['','Jan','Feb','Mar','Apr','Mei','Jun','Jul','Ags','Sep','Okt','Nov','Des'];
var NAMA_HARI    = ['Minggu','Senin','Selasa','Rabu','Kamis','Jumat','Sabtu'];
var HARI_CSS     = ['day-minggu','day-senin','day-selasa','day-rabu','day-kamis','day-jumat','day-sabtu'];

var DONUT_COLORS = ['#16a34a','#d97706','#e11d48','#6366f1','#7c3aed'];
var DONUT_LABELS = ['Tepat Waktu','Terlambat','Pulang Cepat','Tepat Pulang','Tidak Masuk'];
var DONUT_BGS    = ['#dcfce7','#fef3c7','#ffe4e6','#eef2ff','#f3e8ff'];
var DONUT_TXT    = ['#15803d','#92400e','#be123c','#4338ca','#6d28d9'];

var BC = {
    hadir:  { hex:'#6366f1', bg:'rgba(99,102,241,0.72)',  border:'#6366f1', label:'Total Hadir',    type:'bar'  },
    tepat:  { hex:'#16a34a', bg:'rgba(22,163,74,0.72)',   border:'#16a34a', label:'Tepat Waktu',    type:'bar'  },
    absent: { hex:'#7c3aed', bg:'rgba(124,58,237,0.72)',  border:'#7c3aed', label:'Tidak Masuk',    type:'bar'  },
    lambat: { hex:'#d97706', bg:'rgba(217,119,6,0.80)',   border:'#d97706', label:'Terlambat',      type:'bar'  },
    cepat:  { hex:'#e11d48', bg:'rgba(225,29,72,0.80)',   border:'#e11d48', label:'Pulang Cepat',   type:'bar'  },
    tren:   { hex:'#0d9488', bg:'rgba(13,148,136,0.10)',  border:'#0d9488', label:'Tren Kehadiran', type:'line' }
};

var PALETTE = ['#6366f1','#0d9488','#d97706','#e11d48','#16a34a','#7c3aed','#0891b2','#0077b6','#dc2626','#65a30d','#9333ea','#2563eb','#ea580c','#059669','#be185d','#ca8a04','#0f766e','#c2410c','#0369a1','#7e22ce'];

/* ── State ── */
var RAW_DATA = [];
try { RAW_DATA = JSON.parse(document.getElementById('rawDataScript').textContent); }
catch(e) { console.error('RAW parse error', e); }

var viewMode = 'bulanan';
var currentEmpPage = 1;
var donutChart = null;
var empBarChart = null;
var selectedEmpPin = null;
var selectedEmpColor = '#6366f1';
var barMode = 'all';

/* ── Sidebar Mobile ── */
function toggleSidebar() {
    var sb = document.getElementById('sidebar');
    var ov = document.getElementById('sidebarOverlay');
    var isOpen = sb.classList.toggle('open');
    ov.style.display = isOpen ? 'block' : 'none';
}
function checkResponsive() {
    var btn = document.getElementById('sidebarToggle');
    if (window.innerWidth <= 900) {
        btn.style.display = 'flex';
    } else {
        btn.style.display = 'none';
        document.getElementById('sidebar').classList.remove('open');
        document.getElementById('sidebarOverlay').style.display = 'none';
    }
}
window.addEventListener('resize', checkResponsive);
checkResponsive();

/* ── Clock ── */
function tick() {
    var d = new Date();
    document.getElementById('liveClock').textContent = d.toLocaleTimeString('id-ID',{hour:'2-digit',minute:'2-digit',second:'2-digit'});
    document.getElementById('liveDate').textContent  = d.toLocaleDateString('id-ID',{weekday:'long',day:'numeric',month:'long',year:'numeric'});
}
tick(); setInterval(tick, 1000);

/* ── File Name ── */
function showFileName(input) {
    var el = document.getElementById('fileInfo');
    if (input.files && input.files[0]) {
        el.style.display = 'block';
        el.innerHTML = '&#128196; <strong style="color:var(--accent)">' + input.files[0].name + '</strong> (' + Math.round(input.files[0].size/1024) + ' KB)';
    } else { el.style.display = 'none'; }
}

/* ── Init ── */
(function init() {
    var now = new Date(), nowM = now.getMonth()+1, nowY = now.getFullYear();
    ['filterTahun','filterTahunPerf'].forEach(function(id) {
        var sel = document.getElementById(id); if (!sel) return;
        var years = {}; years[nowY] = true;
        RAW_DATA.forEach(function(r){ years[r.tahun] = true; });
        Object.keys(years).sort(function(a,b){return b-a;}).forEach(function(y){
            var o = document.createElement('option'); o.value = y; o.textContent = y;
            if (parseInt(y) === nowY) o.selected = true;
            sel.appendChild(o);
        });
    });
    var bestMonth = nowM;
    if (RAW_DATA.length > 0) {
        var monthCount = {};
        RAW_DATA.forEach(function(r){ if(r.tahun===nowY) monthCount[r.bulan]=(monthCount[r.bulan]||0)+1; });
        var best=0;
        Object.keys(monthCount).forEach(function(m){ if(monthCount[m]>best){best=monthCount[m];bestMonth=parseInt(m);} });
        if (best===0) {
            RAW_DATA.forEach(function(r){ monthCount[r.bulan]=(monthCount[r.bulan]||0)+1; });
            Object.keys(monthCount).forEach(function(m){ if(monthCount[m]>best){best=monthCount[m];bestMonth=parseInt(m);} });
        }
    }
    document.getElementById('filterBulan').value     = bestMonth;
    document.getElementById('filterBulanPerf').value = bestMonth;
    initDonut();
    onPeriodChange();
})();

function getNamaHari(tanggalStr) {
    var parts = tanggalStr.split('-');
    var d = new Date(parseInt(parts[0]), parseInt(parts[1])-1, parseInt(parts[2]));
    return NAMA_HARI[d.getDay()];
}
function getHariCssClass(tanggalStr) {
    var parts = tanggalStr.split('-');
    var d = new Date(parseInt(parts[0]), parseInt(parts[1])-1, parseInt(parts[2]));
    return HARI_CSS[d.getDay()];
}

/* ── Donut ── */
function initDonut() {
    var ctx = document.getElementById('donutChart').getContext('2d');
    donutChart = new Chart(ctx, {
        type: 'doughnut',
        data: { labels: DONUT_LABELS, datasets: [{ data:[0,0,0,0,0], backgroundColor:DONUT_COLORS, borderColor:'#ffffff', borderWidth:4, hoverOffset:12 }] },
        options: {
            responsive: true, maintainAspectRatio: false, cutout: '72%',
            animation: { animateScale: true, duration: 700, easing: 'easeInOutQuart' },
            plugins: {
                legend: { display: false },
                tooltip: { callbacks: { label: function(c){ var t=c.dataset.data.reduce(function(a,b){return a+b;},0); return '  '+c.label+': '+c.raw+' ('+(t>0?Math.round(c.raw/t*100):0)+'%)'; } }, padding:10, cornerRadius:10 }
            },
            onHover: function(e,els){ onDonutHover(els); }
        }
    });
}

function onDonutHover(els) {
    var data=donutChart.data.datasets[0].data, total=data.reduce(function(a,b){return a+b;},0);
    var nE=document.getElementById('donutCenterNum'), lE=document.getElementById('donutCenterLbl');
    if(els.length){ var i=els[0].index; nE.textContent=data[i]; nE.style.color=DONUT_COLORS[i]; lE.textContent=DONUT_LABELS[i]; }
    else { nE.textContent=total; nE.style.color='var(--ink)'; lE.textContent='total'; }
}

function updateDonut(vals) {
    if (!donutChart) return;
    var total=vals.reduce(function(a,b){return a+b;},0);
    donutChart.data.datasets[0].data=vals; donutChart.update();
    document.getElementById('donutCenterNum').textContent=total;
    document.getElementById('donutCenterNum').style.color='var(--ink)';
    document.getElementById('donutCenterLbl').textContent='total';
    renderLegend(vals,total);
}

function renderLegend(vals, total) {
    var c=document.getElementById('donutLegends'); c.innerHTML='';
    DONUT_LABELS.forEach(function(lbl,i){
        var pct=total>0?Math.round(vals[i]/total*100):0;
        var div=document.createElement('div');
        div.className='legend-item';
        div.style.background=DONUT_BGS[i];
        div.style.borderLeftColor=DONUT_COLORS[i];
        div.setAttribute('onmouseover','hoverDonut('+i+')');
        div.setAttribute('onmouseout','resetDonut()');
        div.innerHTML=
            '<div class="legend-dot" style="background:'+DONUT_COLORS[i]+'"></div>'+
            '<span class="legend-name">'+lbl+'</span>'+
            '<span class="legend-val" style="color:'+DONUT_TXT[i]+'">'+vals[i]+'<span class="legend-pct">('+pct+'%)</span></span>';
        c.appendChild(div);
    });
}

function hoverDonut(i) {
    if(!donutChart) return;
    var d=donutChart.data.datasets[0].data;
    donutChart.setActiveElements([{datasetIndex:0,index:i}]);
    donutChart.tooltip.setActiveElements([{datasetIndex:0,index:i}],{x:0,y:0});
    donutChart.update();
    document.getElementById('donutCenterNum').textContent=d[i];
    document.getElementById('donutCenterNum').style.color=DONUT_COLORS[i];
    document.getElementById('donutCenterLbl').textContent=DONUT_LABELS[i];
}

function resetDonut() {
    if(!donutChart) return;
    var d=donutChart.data.datasets[0].data, t=d.reduce(function(a,b){return a+b;},0);
    donutChart.setActiveElements([]); donutChart.tooltip.setActiveElements([],{x:0,y:0}); donutChart.update();
    document.getElementById('donutCenterNum').textContent=t;
    document.getElementById('donutCenterNum').style.color='var(--ink)';
    document.getElementById('donutCenterLbl').textContent='total';
}

/* ── Period ── */
function setViewMode(mode) {
    viewMode=mode;
    ['vpHarian','vpMingguan','vpBulanan','vpTahunan'].forEach(function(id){ document.getElementById(id).classList.remove('active'); });
    document.getElementById({harian:'vpHarian',mingguan:'vpMingguan',bulanan:'vpBulanan',tahunan:'vpTahunan'}[mode]).classList.add('active');
    document.getElementById('filterBulan').style.display  = (mode!=='tahunan') ? '' : 'none';
    document.getElementById('filterMinggu').style.display = (mode==='mingguan') ? '' : 'none';
    document.getElementById('filterHari').style.display   = (mode==='harian')   ? '' : 'none';
    if (mode==='harian') populateHari(parseInt(document.getElementById('filterTahun').value), parseInt(document.getElementById('filterBulan').value));
    onPeriodChange();
}

function populateHari(tahun,bulan) {
    var sel=document.getElementById('filterHari'), prev=sel.value; sel.innerHTML='';
    var days=new Date(tahun,bulan,0).getDate();
    for(var d=1;d<=days;d++){ var o=document.createElement('option'); o.value=d; o.textContent=d+' '+NAMA_BULAN[bulan]; sel.appendChild(o); }
    if(prev&&parseInt(prev)<=days) sel.value=prev;
}

function getPeriod() {
    return {
        tahun:  parseInt(document.getElementById('filterTahun').value)  || new Date().getFullYear(),
        bulan:  parseInt(document.getElementById('filterBulan').value)  || new Date().getMonth()+1,
        minggu: parseInt(document.getElementById('filterMinggu').value) || 1,
        hari:   parseInt(document.getElementById('filterHari').value)   || 1
    };
}

function filterByPeriod(records,p) {
    return records.filter(function(r){
        if(r.tahun!==p.tahun) return false;
        if(viewMode==='tahunan') return true;
        if(r.bulan!==p.bulan) return false;
        if(viewMode==='bulanan') return true;
        if(viewMode==='mingguan') return r.minggu===p.minggu;
        return r.hari===p.hari;
    });
}

function periodLabel(p) {
    if(viewMode==='tahunan')  return 'Tahun '+p.tahun;
    if(viewMode==='bulanan')  return NAMA_BULAN[p.bulan]+' '+p.tahun;
    if(viewMode==='mingguan') return 'Minggu '+p.minggu+', '+NAMA_BULAN[p.bulan]+' '+p.tahun;
    var tgl=p.tahun+'-'+String(p.bulan).padStart(2,'0')+'-'+String(p.hari).padStart(2,'0');
    return getNamaHari(tgl)+', '+p.hari+' '+NAMA_BULAN[p.bulan]+' '+p.tahun;
}
function viewLabel() { return {harian:'Harian',mingguan:'Mingguan',bulanan:'Bulanan',tahunan:'Tahunan'}[viewMode]; }

function updateSidebarStats(total,tepat,lambat,absent) {
    document.getElementById('sqTotal').textContent  = total;
    document.getElementById('sqTepat').textContent  = tepat;
    document.getElementById('sqLambat').textContent = lambat;
    document.getElementById('sqAbsent').textContent = absent;
}

function onPeriodChange() {
    var p=getPeriod(), lbl=periodLabel(p);
    if(viewMode==='harian') populateHari(p.tahun,p.bulan);
    ['periodBadgeText','chartPeriodLabel','tablePeriodLabel'].forEach(function(id){ document.getElementById(id).textContent=lbl; });
    document.getElementById('chartViewChip').textContent=viewLabel();
    var pr=filterByPeriod(RAW_DATA,p);
    var cTepat  = pr.filter(function(r){return r.isMasuk&&!r.terlambat;}).length;
    var cLambat = pr.filter(function(r){return r.terlambat;}).length;
    var cCepat  = pr.filter(function(r){return r.pulangCepat;}).length;
    var cAbsent = hitungAbsen(pr,p);
    var vals=[pr.length,cTepat,cLambat,cCepat,cAbsent];
    ['statTotal','statTepat','statLambat','statCepat','statAbsent'].forEach(function(id,i){
        var el=document.getElementById(id);
        el.textContent=vals[i];
        el.classList.remove('pop'); void el.offsetWidth; el.classList.add('pop');
    });
    updateSidebarStats(pr.length,cTepat,cLambat,cAbsent);
    var cPulangTepat=pr.filter(function(r){return !r.isMasuk&&!r.pulangCepat;}).length;
    updateDonut([cTepat,cLambat,cCepat,cPulangTepat,cAbsent]);
    currentEmpPage=1;
    if(viewMode==='harian') renderDailyAllEmployees(pr,p);
    else renderTable(buildGrouped(pr,p));
}

/* ── Daily View ── */
function renderDailyAllEmployees(periodRecords,p) {
    var allK={};
    RAW_DATA.forEach(function(r){ if(!allK[r.pin]) allK[r.pin]=r.nama; });
    var dayData={};
    Object.keys(allK).forEach(function(pin){
        dayData[pin]={ pin:pin, nama:allK[pin], masuk:null, pulang:null, terlambat:false, pulangCepat:false, absent:true };
    });
    periodRecords.forEach(function(r){
        if(!dayData[r.pin]) dayData[r.pin]={ pin:r.pin, nama:r.nama, masuk:null, pulang:null, terlambat:false, pulangCepat:false, absent:true };
        var d=dayData[r.pin];
        if(r.isMasuk){ d.absent=false; if(!d.masuk||r.waktu<d.masuk){d.masuk=r.waktu;d.terlambat=r.terlambat;} }
        else{ if(!d.pulang||r.waktu>d.pulang){d.pulang=r.waktu;d.pulangCepat=r.pulangCepat;} }
    });
    var rows=Object.values(dayData).sort(function(a,b){return a.nama<b.nama?-1:1;});
    var jmlHadir=rows.filter(function(r){return !r.absent;}).length;
    var jmlLambat=rows.filter(function(r){return r.terlambat;}).length;
    var jmlAbsent=rows.filter(function(r){return r.absent;}).length;
    var tglStr=p.tahun+'-'+String(p.bulan).padStart(2,'0')+'-'+String(p.hari).padStart(2,'0');
    var namaHari=getNamaHari(tglStr), hariCss=getHariCssClass(tglStr);
    var tglFmt=p.hari+' '+NAMA_BULAN[p.bulan]+' '+p.tahun;
    document.getElementById('countChip').textContent=rows.length+' karyawan';
    document.getElementById('karyawanNavStrip').style.display='none';
    document.getElementById('empTableHeader').style.display='none';
    setThead(['No','Karyawan','Jam Masuk','Jam Pulang','Status']);
    var tbody=document.getElementById('tableBody'); tbody.innerHTML='';
    if(!rows.length){
        tbody.innerHTML='<tr><td colspan="5">'+emptyState('📋','Tidak ada data untuk hari ini','Pilih tanggal lain atau upload data CSV')+'</td></tr>';
    } else {
        rows.forEach(function(emp,idx){
            var color=PALETTE[idx%PALETTE.length];
            var initials=emp.nama.substring(0,2).toUpperCase();
            var tr=document.createElement('tr');
            tr.style.animation='rowIn .25s '+(idx*.02)+'s ease both';
            if(emp.absent) tr.className='row-absent';
            var noCell=makeTdNo(idx+1);
            var empCell='<td class="align-middle"><div style="display:flex;align-items:center;gap:10px">'+
                '<div style="width:34px;height:34px;border-radius:10px;background:linear-gradient(135deg,'+color+','+lighten(color)+');display:flex;align-items:center;justify-content:center;font-size:11px;font-weight:800;color:#fff;flex-shrink:0">'+initials+'</div>'+
                '<div><div style="font-size:13px;font-weight:700;color:var(--ink)">'+emp.nama+'</div>'+
                '<div style="display:flex;align-items:center;gap:6px;margin-top:3px">'+
                '<span style="font-family:\'JetBrains Mono\',monospace;font-size:10px;color:var(--ink-muted)">PIN: '+emp.pin+'</span>'+
                '<span class="day-badge '+hariCss+'">'+namaHari+'</span>'+
                '</div></div></div></td>';
            if(emp.absent){
                tr.innerHTML=noCell+empCell+
                    '<td>'+mkAbsentBadge()+'</td>'+
                    '<td><span style="font-size:11px;font-style:italic;color:var(--ink-light)">—</span></td>'+
                    '<td><span class="status-pill absent">⚠ Absen</span></td>';
            } else {
                tr.innerHTML=noCell+empCell+
                    '<td>'+mkTimeBadge(emp.masuk,emp.terlambat,'masuk')+'</td>'+
                    '<td>'+mkTimeBadge(emp.pulang,emp.pulangCepat,'pulang')+'</td>'+
                    '<td>'+mkStatusPill(emp)+'</td>';
            }
            tbody.appendChild(tr);
        });
    }
    document.getElementById('pgInfo').innerHTML=
        '📅 <strong>'+namaHari+', '+tglFmt+'</strong> &nbsp;|&nbsp; '+
        '<span style="color:#16a34a;font-weight:700">✓ Hadir: '+jmlHadir+'</span> &nbsp;'+
        '<span style="color:#d97706;font-weight:700">⚠ Terlambat: '+jmlLambat+'</span> &nbsp;'+
        '<span style="color:#7c3aed;font-weight:700">✕ Absen: '+jmlAbsent+'</span>';
    document.getElementById('pgBtns').innerHTML='';
}

/* ── HTML Helpers ── */
function setThead(cols) {
    document.getElementById('tableHead').innerHTML =
        '<tr>'+ cols.map(function(c){ return '<th>'+c+'</th>'; }).join('') +'</tr>';
}
function emptyState(icon,title,sub) {
    return '<div class="empty-state"><div class="empty-icon">'+icon+'</div><div class="empty-title">'+title+'</div><div class="empty-sub">'+sub+'</div></div>';
}
function makeTdNo(n) {
    return '<td class="td-no">'+String(n).padStart(2,'0')+'</td>';
}
function mkAbsentBadge() {
    return '<span class="badge-absent"><svg viewBox="0 0 24 24"><circle cx="12" cy="8" r="4"/><path d="M4 20c0-4 3.58-7 8-7s8 3 8 7"/><line x1="17" y1="11" x2="23" y2="11"/></svg>Tidak Masuk</span>';
}
function mkTimeBadge(waktu,flag,type) {
    var iconOk   = '<svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="9"/><path d="M9 12l2 2 4-4"/></svg>';
    var iconWarn = '<svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="9"/><path d="M12 8v4"/><circle cx="12" cy="16" r=".8" fill="currentColor"/></svg>';
    var iconBack = '<svg viewBox="0 0 24 24"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>';
    if(!waktu){
        if(type==='pulang') return '<span class="time-badge ok">'+iconOk+'16:00:00</span>';
        return '<span style="font-size:11px;font-style:italic;color:var(--ink-light)">—</span>';
    }
    if(type==='masuk'){
        return flag
            ? '<span class="time-badge late">'+iconWarn+waktu+'</span>'
            : '<span class="time-badge ok">'+iconOk+waktu+'</span>';
    }
    return flag
        ? '<span class="time-badge early">'+iconBack+waktu+'</span>'
        : '<span class="time-badge ok">'+iconOk+waktu+'</span>';
}
function mkStatusPill(emp) {
    if(emp.terlambat)   return '<span class="status-pill lambat">⚠ Terlambat</span>';
    if(emp.pulangCepat) return '<span class="status-pill cepat">↩ Pulang Cepat</span>';
    if(emp.masuk)       return '<span class="status-pill tepat">✓ Tepat Waktu</span>';
    return '<span style="font-size:11px;font-style:italic;color:var(--ink-light)">—</span>';
}

/* ── Hitung Absen ── */
function hitungAbsen(periodRecords,p) {
    var datesInPeriod={};
    periodRecords.forEach(function(r){ datesInPeriod[r.tanggal]=true; });
    var dates=Object.keys(datesInPeriod);
    if(!dates.length) return 0;
    var allPins={};
    RAW_DATA.forEach(function(r){ allPins[r.pin]=true; });
    var totalK=Object.keys(allPins).length, absenTotal=0;
    dates.forEach(function(dt){
        var hadir={};
        periodRecords.forEach(function(r){ if(r.tanggal===dt&&r.isMasuk) hadir[r.pin]=true; });
        absenTotal+=totalK-Object.keys(hadir).length;
    });
    return Math.max(0,absenTotal);
}

/* ── Build Grouped ── */
function buildGrouped(periodRecords,p) {
    var datesInPeriod={};
    periodRecords.forEach(function(r){ if(!datesInPeriod[r.tanggal]) datesInPeriod[r.tanggal]=r.tanggalFmt; });
    var allDates=Object.keys(datesInPeriod).sort();
    var allK={};
    RAW_DATA.forEach(function(r){ if(!allK[r.pin]) allK[r.pin]=r.nama; });
    var kMap={};
    Object.keys(allK).forEach(function(pin){
        kMap[pin]={ pin:pin, nama:allK[pin], days:{} };
        allDates.forEach(function(dt){ kMap[pin].days[dt]={ tanggal:dt, tanggalFmt:datesInPeriod[dt], masuk:null, pulang:null, terlambat:false, pulangCepat:false, absent:true }; });
    });
    periodRecords.forEach(function(r){
        if(!kMap[r.pin]) kMap[r.pin]={ pin:r.pin, nama:r.nama, days:{} };
        if(!kMap[r.pin].days[r.tanggal]) kMap[r.pin].days[r.tanggal]={ tanggal:r.tanggal, tanggalFmt:r.tanggalFmt, masuk:null, pulang:null, terlambat:false, pulangCepat:false, absent:true };
        var dObj=kMap[r.pin].days[r.tanggal];
        if(r.isMasuk){ dObj.absent=false; if(!dObj.masuk||r.waktu<dObj.masuk){dObj.masuk=r.waktu;dObj.terlambat=r.terlambat;} }
        else{ if(!dObj.pulang||r.waktu>dObj.pulang){dObj.pulang=r.waktu;dObj.pulangCepat=r.pulangCepat;} }
    });
    var result=[];
    Object.keys(kMap).forEach(function(pin){
        var k=kMap[pin];
        var days=Object.values(k.days).sort(function(a,b){return a.tanggal<b.tanggal?-1:1;});
        if(allDates.length>0) result.push({ pin:k.pin, nama:k.nama, days:days });
    });
    return result.sort(function(a,b){return a.nama<b.nama?-1:1;});
}

/* ── Filter & Render ── */
function applyFiltersAndRender() {
    var p=getPeriod(), pr=filterByPeriod(RAW_DATA,p);
    if(viewMode==='harian'){ renderDailyAllEmployees(pr,p); return; }
    var grouped=buildGrouped(pr,p);
    var name=document.getElementById('searchName').value.toLowerCase().trim();
    var dateF=document.getElementById('filterDate').value;
    var ket=document.getElementById('filterKet').value;
    if(name)  grouped=grouped.filter(function(k){return k.nama.toLowerCase().indexOf(name)!==-1;});
    if(dateF) grouped=grouped.map(function(k){return{pin:k.pin,nama:k.nama,days:k.days.filter(function(d){return d.tanggal===dateF;})};}).filter(function(k){return k.days.length>0;});
    if(ket)   grouped=grouped.map(function(k){return{pin:k.pin,nama:k.nama,days:k.days.filter(function(d){
        if(ket==='terlambat') return d.terlambat;
        if(ket==='cepat')    return d.pulangCepat;
        if(ket==='tepat')    return d.masuk&&!d.terlambat;
        if(ket==='absent')   return d.absent;
        return true;
    })};}).filter(function(k){return k.days.length>0;});
    currentEmpPage=1;
    renderTable(grouped);
}

function resetFilter() {
    document.getElementById('searchName').value='';
    document.getElementById('filterDate').value='';
    document.getElementById('filterKet').value='';
    applyFiltersAndRender();
}

/* ── Render Table ── */
function renderTable(grouped) {
    setThead(['No','Tanggal','Jam Masuk','Jam Pulang','Status']);
    var navStrip=document.getElementById('karyawanNavStrip');
    navStrip.style.display='flex';
    document.getElementById('empTableHeader').style.display='flex';
    var tbody=document.getElementById('tableBody');
    var pgBtns=document.getElementById('pgBtns');
    var pgInfo=document.getElementById('pgInfo');
    tbody.innerHTML='';
    var total=grouped.length;
    if(currentEmpPage>total) currentEmpPage=Math.max(1,total);
    var totalDays=grouped.reduce(function(s,k){return s+k.days.length;},0);
    document.getElementById('countChip').textContent=total+' karyawan · '+totalDays+' hari';
    // nav strip
    navStrip.innerHTML='<span class="karyawan-strip-label">Karyawan:</span>';
    grouped.forEach(function(k,idx){
        var btn=document.createElement('button');
        btn.className='kary-nav-btn'+(idx+1===currentEmpPage?' active':'');
        btn.textContent=k.nama.split(' ')[0]; btn.title=k.nama;
        btn.onclick=(function(i){return function(){currentEmpPage=i+1;renderTable(grouped);};})(idx);
        navStrip.appendChild(btn);
    });
    if(!total){
        resetEmpHeader();
        tbody.innerHTML='<tr><td colspan="5">'+emptyState('🔍','Tidak ada data untuk periode ini','Pilih periode lain atau ubah filter')+'</td></tr>';
        pgInfo.textContent='Tidak ada data'; pgBtns.innerHTML=''; return;
    }
    var k=grouped[currentEmpPage-1];
    updateEmpHeader(k,currentEmpPage-1);
    k.days.forEach(function(d,idx){
        var tr=document.createElement('tr');
        tr.style.animation='rowIn .25s '+(idx*.025)+'s ease both';
        var noCell=makeTdNo(idx+1);
        if(d.absent){
            tr.className='row-absent';
            tr.innerHTML=noCell+
                '<td style="font-size:12px;color:var(--ink-muted)">'+d.tanggalFmt+'</td>'+
                '<td>'+mkAbsentBadge()+'</td>'+
                '<td><span style="font-size:11px;font-style:italic;color:var(--ink-light)">—</span></td>'+
                '<td><span class="status-pill absent">⚠ Absen</span></td>';
        } else {
            tr.innerHTML=noCell+
                '<td style="font-size:12px;color:var(--ink-muted)">'+d.tanggalFmt+'</td>'+
                '<td>'+mkTimeBadge(d.masuk,d.terlambat,'masuk')+'</td>'+
                '<td>'+mkTimeBadge(d.pulang,d.pulangCepat,'pulang')+'</td>'+
                '<td>'+mkStatusPill(d)+'</td>';
        }
        tbody.appendChild(tr);
    });
    pgInfo.textContent='Karyawan '+currentEmpPage+' dari '+total;
    // pagination
    pgBtns.innerHTML='';
    function mkBtn(label,page,disabled,active){
        var b=document.createElement('button');
        b.className='pg-btn'+(active?' active':'');
        b.innerHTML=label; b.disabled=disabled;
        b.onclick=function(){currentEmpPage=page;renderTable(grouped);};
        pgBtns.appendChild(b);
    }
    function mkEllipsis(){ var s=document.createElement('span'); s.className='pg-ellipsis'; s.textContent='…'; pgBtns.appendChild(s); }
    mkBtn('&#8592;',currentEmpPage-1,currentEmpPage===1,false);
    var pages=[];
    if(total<=7){ for(var i=1;i<=total;i++) pages.push(i); }
    else {
        pages.push(1);
        if(currentEmpPage>3) pages.push('…');
        var lo=Math.max(2,currentEmpPage-1), hi=Math.min(total-1,currentEmpPage+1);
        for(var j=lo;j<=hi;j++) pages.push(j);
        if(currentEmpPage<total-2) pages.push('…');
        pages.push(total);
    }
    pages.forEach(function(pg){ if(pg==='…') mkEllipsis(); else mkBtn(pg,pg,false,pg===currentEmpPage); });
    mkBtn('&#8594;',currentEmpPage+1,currentEmpPage===total,false);
}

/* ── Emp Header ── */
function updateEmpHeader(k,colorIdx) {
    var color=PALETTE[colorIdx%PALETTE.length];
    var av=document.getElementById('empTableAvatar');
    av.textContent=k.nama.substring(0,2).toUpperCase();
    av.style.background='linear-gradient(135deg,'+color+','+lighten(color)+')';
    document.getElementById('empTableName').textContent=k.nama;
    document.getElementById('empTablePin').textContent='PIN: '+k.pin;
    document.getElementById('empTableDays').textContent=k.days.length+' hari';
    var tepat=0,lambat=0,cepat=0,absent=0;
    k.days.forEach(function(d){ if(d.absent) absent++; else{ if(d.terlambat) lambat++; else if(d.masuk) tepat++; if(d.pulangCepat) cepat++; } });
    var html='';
    if(tepat)  html+='<span class="emp-stat-pill" style="background:#dcfce7;color:#15803d;border:1px solid #bbf7d0">✓ '+tepat+' Tepat</span>';
    if(lambat) html+='<span class="emp-stat-pill" style="background:#fef3c7;color:#92400e;border:1px solid #fde68a">⚠ '+lambat+' Terlambat</span>';
    if(cepat)  html+='<span class="emp-stat-pill" style="background:#ffe4e6;color:#be123c;border:1px solid #fecdd3">↩ '+cepat+' Pulang Cepat</span>';
    if(absent) html+='<span class="emp-stat-pill" style="background:#f3e8ff;color:#6d28d9;border:1px solid #ddd6fe">✕ '+absent+' Absen</span>';
    document.getElementById('empTableStats').innerHTML=html;
}

function resetEmpHeader() {
    var av=document.getElementById('empTableAvatar');
    av.textContent='??'; av.style.background='linear-gradient(135deg,#6366f1,#0d9488)';
    document.getElementById('empTableName').textContent='—';
    document.getElementById('empTablePin').textContent='—';
    document.getElementById('empTableDays').textContent='0 hari';
    document.getElementById('empTableStats').innerHTML='';
}

/* ── Utility ── */
function lighten(hex) {
    if(!hex||hex.length<7) return '#818cf8';
    var r=parseInt(hex.slice(1,3),16), g=parseInt(hex.slice(3,5),16), b=parseInt(hex.slice(5,7),16);
    return '#'+[Math.min(255,r+70),Math.min(255,g+70),Math.min(255,b+70)].map(function(v){return v.toString(16).padStart(2,'0');}).join('');
}

/* ── Page Switch ── */
function switchPage(p) {
    ['pageDashboard','pagePerforma'].forEach(function(id){document.getElementById(id).classList.remove('active');});
    ['tabDashboard','tabPerforma'].forEach(function(id){document.getElementById(id).classList.remove('active');});
    document.getElementById('page'+p.charAt(0).toUpperCase()+p.slice(1)).classList.add('active');
    document.getElementById('tab'+p.charAt(0).toUpperCase()+p.slice(1)).classList.add('active');
    document.getElementById('sidebar').classList.remove('open');
    document.getElementById('sidebarOverlay').style.display='none';
    if(p==='performa') renderPerfPage();
}

/* ── Performa Page ── */
function getEmpsForPeriod(tahun,bulan) {
    var allPins={};
    RAW_DATA.filter(function(r){return r.tahun===tahun;}).forEach(function(r){ if(!allPins[r.pin]) allPins[r.pin]={pin:r.pin,nama:r.nama}; });
    var relevant=RAW_DATA.filter(function(r){ if(r.tahun!==tahun) return false; if(bulan>0&&r.bulan!==bulan) return false; return true; });
    var workDates={}; relevant.forEach(function(r){ workDates[r.tanggal]=true; });
    var totalWD=Object.keys(workDates).length;
    var pinStats={};
    relevant.forEach(function(r){
        if(!pinStats[r.pin]) pinStats[r.pin]={dateSet:{}};
        if(!pinStats[r.pin].dateSet[r.tanggal]) pinStats[r.pin].dateSet[r.tanggal]={masuk:false,terlambat:false,pulangCepat:false};
        if(r.isMasuk){pinStats[r.pin].dateSet[r.tanggal].masuk=true;if(r.terlambat)pinStats[r.pin].dateSet[r.tanggal].terlambat=true;}
        else if(r.pulangCepat) pinStats[r.pin].dateSet[r.tanggal].pulangCepat=true;
    });
    return Object.keys(allPins).map(function(pin){
        var info=allPins[pin], sd=pinStats[pin]?Object.values(pinStats[pin].dateSet):[];
        var hadir=sd.filter(function(d){return d.masuk;}).length;
        return {pin:info.pin,nama:info.nama,hadir:hadir,
            lambat:sd.filter(function(d){return d.terlambat;}).length,
            cepat:sd.filter(function(d){return d.pulangCepat;}).length,
            absent:Math.max(0,totalWD-hadir),totalWD:totalWD};
    }).sort(function(a,b){return a.nama<b.nama?-1:1;});
}

function renderPerfPage() {
    var tahun=parseInt(document.getElementById('filterTahunPerf').value);
    var bulan=parseInt(document.getElementById('filterBulanPerf').value);
    document.getElementById('perfYearBadge').textContent=bulan===0?tahun:NAMA_BULAN[bulan]+' '+tahun;
    document.getElementById('perfPeriodHint').textContent=bulan===0?'📅 Seluruh bulan dalam tahun terpilih':'📅 Data harian bulan '+NAMA_BULAN[bulan]+' '+tahun;
    var emps=getEmpsForPeriod(tahun,bulan);
    document.getElementById('empCountChip').textContent=emps.length+' karyawan';
    renderEmpSelector(emps,tahun,bulan);
    if(selectedEmpPin){
        var emp=emps.find(function(e){return e.pin===selectedEmpPin;});
        if(emp) buildBarChart(selectedEmpPin,emp.nama,selectedEmpColor,tahun,bulan);
        else { document.getElementById('empChartCard').classList.remove('show'); document.getElementById('empChartHint').style.display=''; selectedEmpPin=null; }
    }
}

function renderEmpSelector(emps,tahun,bulan) {
    var grid=document.getElementById('empSelectorGrid'); grid.innerHTML='';
    if(!emps.length){
        grid.innerHTML='<div style="text-align:center;padding:56px 16px"><div style="font-size:40px;margin-bottom:14px">📊</div><div class="empty-title">Tidak ada data</div><div class="empty-sub">Upload CSV atau pilih tahun lain</div></div>';
        return;
    }
    emps.forEach(function(emp,i){
        var color=PALETTE[i%PALETTE.length], initials=emp.nama.substring(0,2).toUpperCase();
        var card=document.createElement('div');
        card.className='emp-selector-card'+(emp.pin===selectedEmpPin?' selected':'');
        card.innerHTML=
            '<div class="emp-check-badge"><svg viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg></div>'+
            '<div style="display:flex;align-items:center;gap:10px;margin-bottom:12px">'+
                '<div class="emp-card-avatar" style="background:linear-gradient(135deg,'+color+','+lighten(color)+')">'+initials+'</div>'+
                '<div><div class="emp-card-name" title="'+emp.nama+'">'+emp.nama+'</div><div class="emp-card-pin">PIN: '+emp.pin+'</div></div>'+
            '</div>'+
            '<div class="emp-stat-mini">'+
                empMiniCell(emp.hadir, color, 'Hadir')+
                empMiniCell(emp.lambat, emp.lambat>0?'#d97706':'#94a3b8', 'Lambat')+
                empMiniCell(emp.cepat,  emp.cepat>0?'#e11d48':'#94a3b8', 'Cepat')+
                empMiniCell(emp.absent, emp.absent>0?'#7c3aed':'#94a3b8', 'Absen')+
            '</div>'+
            (emp.absent>0?'<div class="emp-absent-warn">⚠ Tidak masuk '+emp.absent+'x dari '+emp.totalWD+' hari kerja</div>':'');
        (function(pin,nama,col,t,b,el){
            el.onclick=function(){
                selectedEmpPin=pin; selectedEmpColor=col;
                document.querySelectorAll('.emp-selector-card').forEach(function(c){ c.classList.remove('selected'); });
                el.classList.add('selected');
                buildBarChart(pin,nama,col,t,b);
            };
        })(emp.pin,emp.nama,color,tahun,bulan,card);
        grid.appendChild(card);
    });
}

function empMiniCell(val,color,lbl) {
    return '<div class="emp-stat-mini-cell">'+
        '<div class="emp-stat-mini-val" style="color:'+color+'">'+val+'</div>'+
        '<div class="emp-stat-mini-lbl">'+lbl+'</div>'+
    '</div>';
}

function getMonthlyData(pin,tahun) {
    var mMap={}, mWD={};
    RAW_DATA.filter(function(r){return r.tahun===tahun;}).forEach(function(r){ if(!mWD[r.bulan]) mWD[r.bulan]={}; mWD[r.bulan][r.tanggal]=true; });
    RAW_DATA.filter(function(r){return r.pin===pin&&r.tahun===tahun;}).forEach(function(r){
        if(!mMap[r.bulan]) mMap[r.bulan]={};
        if(!mMap[r.bulan][r.tanggal]) mMap[r.bulan][r.tanggal]={masuk:null,terlambat:false,pulangCepat:false};
        var d=mMap[r.bulan][r.tanggal];
        if(r.isMasuk){if(!d.masuk||r.waktu<d.masuk){d.masuk=r.waktu;d.terlambat=r.terlambat;}}
        else{if(!d.pulang||r.waktu>d.pulang){d.pulangCepat=r.pulangCepat;}}
    });
    var result=[];
    for(var m=1;m<=12;m++){
        if(!mWD[m]) continue;
        var days=mMap[m]?Object.values(mMap[m]):[];
        var hadir=days.filter(function(d){return d.masuk;}).length;
        result.push({bulan:m,label:NAMA_BULAN_S[m],hadir:hadir,
            lambat:days.filter(function(d){return d.terlambat;}).length,
            cepat:days.filter(function(d){return d.pulangCepat;}).length,
            tepat:days.filter(function(d){return d.masuk&&!d.terlambat;}).length,
            absent:Math.max(0,Object.keys(mWD[m]).length-hadir)});
    }
    return result;
}

function getDailyData(pin,tahun,bulan) {
    var dMap={}, allDates={};
    RAW_DATA.filter(function(r){return r.tahun===tahun&&r.bulan===bulan;}).forEach(function(r){ allDates[r.tanggal]=r.tanggalFmt; });
    RAW_DATA.filter(function(r){return r.pin===pin&&r.tahun===tahun&&r.bulan===bulan;}).forEach(function(r){
        if(!dMap[r.tanggal]) dMap[r.tanggal]={masuk:null,pulang:null,terlambat:false,pulangCepat:false};
        var d=dMap[r.tanggal];
        if(r.isMasuk){if(!d.masuk||r.waktu<d.masuk){d.masuk=r.waktu;d.terlambat=r.terlambat;}}
        else{if(!d.pulang||r.waktu>d.pulang){d.pulang=r.waktu;d.pulangCepat=r.pulangCepat;}}
    });
    return Object.keys(allDates).sort().map(function(dt){
        var d=dMap[dt]||{masuk:null,terlambat:false,pulangCepat:false};
        return {tanggal:dt,label:String(parseInt(dt.split('-')[2])),
            hadir:d.masuk?1:0,lambat:d.terlambat?1:0,cepat:d.pulangCepat?1:0,
            tepat:(d.masuk&&!d.terlambat)?1:0,absent:d.masuk?0:1};
    });
}

function setBarMode(mode) {
    barMode=mode;
    ['btnModeAll','btnModeHadir','btnModeLate'].forEach(function(id){document.getElementById(id).classList.remove('active');});
    document.getElementById({all:'btnModeAll',hadir:'btnModeHadir',late:'btnModeLate'}[mode]).classList.add('active');
    if(selectedEmpPin){
        var tahun=parseInt(document.getElementById('filterTahunPerf').value);
        var bulan=parseInt(document.getElementById('filterBulanPerf').value);
        buildBarChart(selectedEmpPin,document.getElementById('bcName').textContent,selectedEmpColor,tahun,bulan);
    }
}

function buildCustomLegend(activeKeys) {
    var container=document.getElementById('chartLegendCustom'); container.innerHTML='';
    activeKeys.forEach(function(key){
        var c=BC[key];
        var item=document.createElement('div');
        item.className='chart-legend-item';
        var indicator=c.type==='line'
            ?'<div class="legend-bar" style="background:'+c.hex+'"></div>'
            :'<div class="legend-square" style="background:'+c.bg+';border:2px solid '+c.border+'"></div>';
        item.innerHTML=indicator+'<span>'+c.label+'</span>';
        container.appendChild(item);
    });
}

function buildBarChart(pin,nama,color,tahun,bulan) {
    var isDaily=bulan>0;
    var data=isDaily?getDailyData(pin,tahun,bulan):getMonthlyData(pin,tahun);
    document.getElementById('empChartCard').classList.add('show');
    document.getElementById('empChartHint').style.display='none';
    var av=document.getElementById('bcAvatar');
    av.textContent=nama.substring(0,2).toUpperCase();
    av.style.background='linear-gradient(135deg,'+color+','+lighten(color)+')';
    document.getElementById('bcName').textContent=nama;
    document.getElementById('bcPin').textContent='PIN: '+pin+' · '+(isDaily?NAMA_BULAN[bulan]+' '+tahun:'Tahun '+tahun);
    var yH=0,yL=0,yE=0,yT=0,yA=0;
    data.forEach(function(m){yH+=m.hadir;yL+=m.lambat;yE+=m.cepat;yT+=m.tepat;yA+=m.absent;});
    renderYearlyStat(yH,yL,yE,yT,yA,isDaily?NAMA_BULAN[bulan]+' '+tahun:'Tahun '+tahun);
    if(!data.length){if(empBarChart){empBarChart.destroy();empBarChart=null;} return;}
    if(empBarChart){empBarChart.destroy();empBarChart=null;}
    var ctx=document.getElementById('empBarChart').getContext('2d');
    var datasets=[],activeKeys=[];
    if(barMode==='all'||barMode==='hadir'){
        datasets.push({label:BC.hadir.label,type:'bar',data:data.map(function(m){return m.hadir;}),backgroundColor:BC.hadir.bg,borderColor:BC.hadir.border,borderWidth:1.5,borderRadius:6,borderSkipped:false,order:2});activeKeys.push('hadir');
        datasets.push({label:BC.tepat.label,type:'bar',data:data.map(function(m){return m.tepat;}),backgroundColor:BC.tepat.bg,borderColor:BC.tepat.border,borderWidth:1.5,borderRadius:6,borderSkipped:false,order:3});activeKeys.push('tepat');
        datasets.push({label:BC.absent.label,type:'bar',data:data.map(function(m){return m.absent;}),backgroundColor:BC.absent.bg,borderColor:BC.absent.border,borderWidth:1.5,borderRadius:6,borderSkipped:false,order:4});activeKeys.push('absent');
        datasets.push({label:BC.tren.label,type:'line',data:data.map(function(m){return m.hadir;}),borderColor:BC.tren.border,backgroundColor:BC.tren.bg,borderWidth:2.5,tension:0.4,pointRadius:5,pointBackgroundColor:BC.tren.border,pointBorderColor:'#fff',pointBorderWidth:2,fill:true,order:1});activeKeys.push('tren');
    }
    if(barMode==='all'||barMode==='late'){
        datasets.push({label:BC.lambat.label,type:'bar',data:data.map(function(m){return m.lambat;}),backgroundColor:BC.lambat.bg,borderColor:BC.lambat.border,borderWidth:1.5,borderRadius:6,borderSkipped:false,order:5});activeKeys.push('lambat');
        datasets.push({label:BC.cepat.label,type:'bar',data:data.map(function(m){return m.cepat;}),backgroundColor:BC.cepat.bg,borderColor:BC.cepat.border,borderWidth:1.5,borderRadius:6,borderSkipped:false,order:6});activeKeys.push('cepat');
    }
    buildCustomLegend(activeKeys);
    empBarChart=new Chart(ctx,{
        type:'bar',
        data:{labels:data.map(function(m){return m.label;}),datasets:datasets},
        options:{
            responsive:true, maintainAspectRatio:false,
            interaction:{mode:'index',intersect:false},
            plugins:{
                legend:{display:false},
                tooltip:{
                    callbacks:{
                        title:function(items){var m=data[items[0].dataIndex];return isDaily?m.tanggal:NAMA_BULAN[m.bulan]+' '+tahun;},
                        label:function(c2){return ' '+c2.dataset.label+': '+c2.raw+' hari';}
                    },
                    padding:12, cornerRadius:12,
                    backgroundColor:'rgba(30,27,75,0.92)',
                    titleColor:'#c7d2fe', bodyColor:'#e0e7ff',
                    borderColor:'rgba(99,102,241,0.4)', borderWidth:1
                }
            },
            scales:{
                x:{grid:{display:false},ticks:{font:{family:'Plus Jakarta Sans',size:11,weight:'600'},color:'#64748b'},border:{display:false}},
                y:{beginAtZero:true,grid:{color:'rgba(99,102,241,0.08)'},ticks:{font:{family:'JetBrains Mono',size:10},color:'#94a3b8',stepSize:1,callback:function(v){return Number.isInteger(v)?v:'';}},border:{display:false}}
            },
            animation:{duration:700,easing:'easeInOutQuart'}
        }
    });
    setTimeout(function(){document.getElementById('empChartCard').scrollIntoView({behavior:'smooth',block:'nearest'});},80);
}

function renderYearlyStat(h,l,e,t,a,label) {
    var strip=document.getElementById('yearlyStatStrip');
    strip.innerHTML=(label?'<div style="width:100%;font-size:11px;font-weight:700;color:var(--ink-muted);text-transform:uppercase;letter-spacing:.06em;padding-bottom:6px">Ringkasan '+label+'</div>':'')+
        mkYs('#ecfdf5','#bbf7d0','#16a34a','#d1fae5','<rect x="3" y="4" width="18" height="18" rx="2"/><path d="M16 2v4M8 2v4M3 10h18"/>',h,'Total Hadir')+
        mkYs('#eef2ff','#c7d2fe','#6366f1','#e0e7ff','<circle cx="12" cy="12" r="9"/><path d="M9 12l2 2 4-4"/>',t,'Tepat Waktu')+
        mkYs('#fef3c7','#fde68a','#d97706','#fef9c3','<circle cx="12" cy="12" r="9"/><path d="M12 8v4"/><circle cx="12" cy="16" r=".5" fill="currentColor"/>',l,'Terlambat')+
        mkYs('#ffe4e6','#fecdd3','#e11d48','#ffe4e6','<path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/>',e,'Pulang Cepat')+
        mkYs('#f3e8ff','#ddd6fe','#7c3aed','#ede9fe','<circle cx="12" cy="8" r="4"/><path d="M4 20c0-4 3.58-7 8-7s8 3 8 7"/><line x1="17" y1="11" x2="23" y2="11"/>',a,'Tidak Masuk');
}

function mkYs(wrapBg,wrapBorder,color,iconBg,svgPaths,val,lbl) {
    return '<div class="yearly-stat-item" style="background:'+wrapBg+';border:1px solid '+wrapBorder+'">'+
        '<div class="yearly-stat-icon" style="background:'+iconBg+'">'+
            '<svg class="yearly-stat-icon-svg" viewBox="0 0 24 24" style="stroke:'+color+'">'+svgPaths+'</svg>'+
        '</div>'+
        '<div>'+
            '<div class="yearly-stat-num" style="color:'+color+'">'+val+'</div>'+
            '<div class="yearly-stat-lbl">'+lbl+'</div>'+
        '</div>'+
    '</div>';
}
</script>
</body>
</html>