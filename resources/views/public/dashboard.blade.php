<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kipin — Monitoring Absensi</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;600&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg:#f0f4ff;--card:#ffffff;--border:rgba(99,102,241,0.12);
            --accent:#6366f1;--accent2:#4f46e5;--accent3:#818cf8;--accentbg:#eef2ff;
            --teal:#0d9488;--amber:#d97706;--amberbg:#fef3c7;
            --rose:#e11d48;--rosebg:#ffe4e6;--green:#16a34a;--greenbg:#dcfce7;
            --text:#1e1b4b;--text2:#4338ca;--textmute:#64748b;--textlight:#94a3b8;
            --radius:16px;--radius-sm:10px;
            --clr-hadir:#6366f1;--clr-tepat:#16a34a;--clr-absent:#7c3aed;
            --clr-lambat:#d97706;--clr-cepat:#e11d48;--clr-tren:#0d9488;
        }
        *{box-sizing:border-box;margin:0;padding:0;}
        body{background:var(--bg);min-height:100vh;font-family:'Plus Jakarta Sans',sans-serif;color:var(--text);overflow-x:hidden;}
        body::before{content:'';position:fixed;inset:0;background-image:linear-gradient(rgba(99,102,241,0.04) 1px,transparent 1px),linear-gradient(90deg,rgba(99,102,241,0.04) 1px,transparent 1px);background-size:36px 36px;pointer-events:none;z-index:0;}
        .glow-blob{position:fixed;border-radius:50%;filter:blur(80px);pointer-events:none;z-index:0;}
        .glow-1{width:600px;height:600px;background:rgba(99,102,241,0.10);top:-160px;left:-120px;}
        .glow-2{width:500px;height:500px;background:rgba(13,148,136,0.07);bottom:0;right:-100px;}
        .glow-3{width:300px;height:300px;background:rgba(225,29,72,0.05);top:40%;left:50%;}

        .page{display:none;}.page.active{display:block;}
        .wrap{position:relative;z-index:1;max-width:1240px;margin:0 auto;padding:1.5rem 1.5rem 3rem;}

        /* TOPBAR */
        .topbar{display:flex;align-items:center;gap:18px;background:linear-gradient(135deg,#6366f1 0%,#4f46e5 50%,#0d9488 100%);border-radius:20px;padding:1.1rem 1.6rem;margin-bottom:1.5rem;position:relative;overflow:hidden;box-shadow:0 8px 32px rgba(99,102,241,0.28);}
        .topbar::before{content:'';position:absolute;inset:0;background:radial-gradient(ellipse at 80% 50%,rgba(255,255,255,0.08) 0%,transparent 60%);pointer-events:none;}
        .logo-wrap{width:68px;height:68px;border-radius:18px;background:rgba(255,255,255,0.18);border:2px solid rgba(255,255,255,0.35);display:flex;align-items:center;justify-content:center;flex-shrink:0;overflow:hidden;}
        .logo-wrap img{width:54px;height:54px;object-fit:contain;}
        .topbar-info{flex:1;}
        .topbar-title{font-size:19px;font-weight:800;color:#fff;letter-spacing:-.03em;}
        .topbar-sub{font-size:12px;color:rgba(255,255,255,0.75);margin-top:5px;display:flex;align-items:center;gap:7px;}
        .live-dot{width:7px;height:7px;border-radius:50%;background:#a3e635;box-shadow:0 0 6px #a3e635;animation:pulse 2s infinite;}
        .clock{font-family:'JetBrains Mono',monospace;font-size:24px;font-weight:600;color:#fff;letter-spacing:-.02em;}
        .date-label{font-size:11px;color:rgba(255,255,255,0.7);margin-top:4px;}

        /* readonly badge */
        .readonly-badge{display:inline-flex;align-items:center;gap:6px;padding:6px 14px;border-radius:20px;background:rgba(255,255,255,0.15);border:1.5px solid rgba(255,255,255,0.3);color:#fff;font-size:11px;font-weight:700;letter-spacing:.04em;flex-shrink:0;}
        .readonly-badge svg{width:13px;height:13px;stroke:#fff;fill:none;stroke-width:2;}

        /* NAV TABS */
        .nav-tabs{display:flex;gap:8px;margin-bottom:1.5rem;background:#fff;border:1px solid var(--border);border-radius:var(--radius);padding:8px;box-shadow:0 2px 12px rgba(99,102,241,0.06);}
        .nav-tab{flex:1;display:flex;align-items:center;justify-content:center;gap:8px;padding:11px 20px;border-radius:10px;font-size:13px;font-weight:700;cursor:pointer;border:none;background:transparent;color:var(--textmute);font-family:inherit;transition:all .2s;}
        .nav-tab svg{width:15px;height:15px;stroke:currentColor;fill:none;stroke-width:2;}
        .nav-tab:hover{color:var(--accent);background:var(--accentbg);}
        .nav-tab.active{background:linear-gradient(135deg,var(--accent),var(--accent2));color:#fff;box-shadow:0 4px 14px rgba(99,102,241,0.3);}

        /* PERIOD BAR */
        .period-filter-bar{display:flex;align-items:center;gap:12px;background:#fff;border:1px solid var(--border);border-radius:var(--radius);padding:14px 20px;margin-bottom:1.5rem;box-shadow:0 2px 12px rgba(99,102,241,0.06);flex-wrap:wrap;}
        .period-filter-icon{width:34px;height:34px;border-radius:10px;background:var(--accentbg);display:flex;align-items:center;justify-content:center;flex-shrink:0;}
        .period-filter-icon svg{width:16px;height:16px;stroke:var(--accent);fill:none;stroke-width:2;}
        .period-filter-label{font-size:12px;font-weight:700;color:var(--textmute);text-transform:uppercase;letter-spacing:.06em;white-space:nowrap;}
        .period-selects{display:flex;gap:8px;align-items:center;flex-wrap:wrap;}
        .period-select{padding:8px 30px 8px 14px;font-size:13px;font-weight:600;border:1.5px solid rgba(99,102,241,0.2);border-radius:9px;background:var(--accentbg);color:var(--text);font-family:inherit;outline:none;cursor:pointer;transition:border-color .2s;appearance:none;background-image:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 24 24' fill='none' stroke='%236366f1' stroke-width='2.5'%3E%3Cpolyline points='6 9 12 15 18 9'/%3E%3C/svg%3E");background-repeat:no-repeat;background-position:right 10px center;}
        .period-select:focus{border-color:var(--accent);box-shadow:0 0 0 3px rgba(99,102,241,0.1);}
        .period-badge{display:inline-flex;align-items:center;gap:6px;padding:6px 14px;border-radius:20px;background:linear-gradient(135deg,var(--accent),var(--teal));color:#fff;font-size:12px;font-weight:700;white-space:nowrap;box-shadow:0 3px 10px rgba(99,102,241,0.3);}
        .period-badge-dot{width:6px;height:6px;border-radius:50%;background:rgba(255,255,255,0.8);animation:pulse 2s infinite;}
        .view-mode-pills{display:flex;gap:3px;background:#f1f5f9;border-radius:9px;padding:3px;flex-shrink:0;}
        .view-pill{padding:6px 13px;border-radius:7px;font-size:11px;font-weight:700;border:none;background:transparent;color:var(--textmute);cursor:pointer;font-family:inherit;transition:all .15s;white-space:nowrap;}
        .view-pill.active{background:#fff;color:var(--accent);box-shadow:0 1px 6px rgba(99,102,241,0.15);}

        /* STAT */
        .stat-grid{display:grid;grid-template-columns:repeat(5,1fr);gap:14px;margin-bottom:1.5rem;}
        .stat-card{background:var(--card);border:1px solid var(--border);border-radius:var(--radius);padding:1.3rem 1.4rem;position:relative;overflow:hidden;box-shadow:0 2px 12px rgba(99,102,241,0.06);transition:transform .2s,box-shadow .2s;}
        .stat-card:hover{transform:translateY(-3px);box-shadow:0 8px 28px rgba(99,102,241,0.13);}
        .stat-card::before{content:'';position:absolute;top:0;left:0;right:0;height:3px;}
        .c-total::before{background:linear-gradient(90deg,#6366f1,#818cf8);}
        .c-tepat::before{background:linear-gradient(90deg,#16a34a,#4ade80);}
        .c-lambat::before{background:linear-gradient(90deg,#d97706,#fbbf24);}
        .c-cepat::before{background:linear-gradient(90deg,#e11d48,#fb7185);}
        .c-absent::before{background:linear-gradient(90deg,#7c3aed,#a78bfa);}
        .stat-icon{width:40px;height:40px;border-radius:12px;display:flex;align-items:center;justify-content:center;margin-bottom:14px;}
        .stat-icon svg{width:20px;height:20px;fill:none;stroke-width:1.8;}
        .icon-total{background:var(--accentbg)}.icon-total svg{stroke:var(--accent);}
        .icon-tepat{background:var(--greenbg)}.icon-tepat svg{stroke:var(--green);}
        .icon-lambat{background:var(--amberbg)}.icon-lambat svg{stroke:var(--amber);}
        .icon-cepat{background:var(--rosebg)}.icon-cepat svg{stroke:var(--rose);}
        .icon-absent{background:#f3e8ff}.icon-absent svg{stroke:#7c3aed;}
        .stat-label{font-size:11px;font-weight:700;color:var(--textmute);text-transform:uppercase;letter-spacing:.07em;margin-bottom:5px;}
        .stat-val{font-size:34px;font-weight:800;letter-spacing:-.04em;line-height:1;}
        .v-total{color:var(--accent);}.v-tepat{color:var(--green);}.v-lambat{color:var(--amber);}.v-cepat{color:var(--rose);}.v-absent{color:#7c3aed;}
        @keyframes statPop{0%{transform:scale(.9);opacity:.5;}60%{transform:scale(1.05);}100%{transform:scale(1);opacity:1;}}
        .stat-val.pop{animation:statPop .3s ease;}

        /* CARD */
        .card{background:var(--card);border:1px solid var(--border);border-radius:var(--radius);margin-bottom:1.25rem;overflow:hidden;box-shadow:0 2px 12px rgba(99,102,241,0.06);}
        .card-head{display:flex;align-items:center;gap:10px;padding:.9rem 1.4rem;border-bottom:1px solid var(--border);font-size:13px;font-weight:700;color:var(--text);background:linear-gradient(90deg,#fafbff 0%,#fff 100%);flex-wrap:wrap;}
        .card-head-icon{width:30px;height:30px;border-radius:9px;background:var(--accentbg);display:flex;align-items:center;justify-content:center;flex-shrink:0;}
        .card-head-icon svg{width:15px;height:15px;stroke:var(--accent);fill:none;stroke-width:2;}
        .chip{font-size:10px;padding:3px 10px;border-radius:20px;background:var(--accentbg);color:var(--accent);border:1px solid rgba(99,102,241,0.2);font-weight:700;letter-spacing:.04em;margin-left:auto;}
        .card-body{padding:1.1rem 1.4rem;}

        /* DONUT */
        .donut-wrap{display:flex;flex-wrap:wrap;gap:28px;align-items:center;justify-content:center;padding:1.4rem;}
        .donut-canvas-wrap{position:relative;width:220px;height:220px;flex-shrink:0;}
        .donut-center{position:absolute;top:50%;left:50%;transform:translate(-50%,-50%);text-align:center;pointer-events:none;}
        .donut-center-num{font-size:30px;font-weight:800;color:var(--text);letter-spacing:-.04em;line-height:1;transition:all .3s;}
        .donut-center-lbl{font-size:11px;color:var(--textmute);margin-top:3px;font-weight:600;}
        .donut-legends{display:flex;flex-direction:column;gap:9px;flex:1;min-width:220px;}
        .donut-legend-item{display:flex;align-items:center;gap:12px;padding:11px 14px;border-radius:12px;cursor:pointer;transition:transform .15s,box-shadow .15s;}
        .donut-legend-item:hover{transform:translateX(4px);box-shadow:0 2px 12px rgba(0,0,0,0.08);}
        .donut-legend-dot{width:14px;height:14px;border-radius:5px;flex-shrink:0;}
        .donut-legend-label{font-size:13px;font-weight:600;color:var(--text);flex:1;}
        .donut-legend-val{font-size:15px;font-weight:800;}
        .donut-legend-pct{font-size:11px;font-weight:500;margin-left:4px;opacity:.7;}

        /* FILTER */
        .filter-row{display:grid;grid-template-columns:1fr 1fr 1fr auto;gap:12px;align-items:end;}
        .f-group{display:flex;flex-direction:column;gap:5px;}
        .f-label{font-size:10px;font-weight:700;color:var(--textmute);text-transform:uppercase;letter-spacing:.07em;}
        .f-input{padding:9px 12px;font-size:13px;border:1.5px solid rgba(99,102,241,0.15);border-radius:var(--radius-sm);background:#fafbff;color:var(--text);font-family:inherit;outline:none;transition:border-color .2s;}
        .f-input:focus{border-color:var(--accent);box-shadow:0 0 0 3px rgba(99,102,241,0.1);}
        .btn-reset{padding:9px 16px;font-size:12px;font-weight:600;background:#f1f5f9;color:var(--textmute);border:1.5px solid #e2e8f0;border-radius:var(--radius-sm);cursor:pointer;font-family:inherit;white-space:nowrap;transition:all .2s;}
        .btn-reset:hover{border-color:var(--accent);color:var(--accent);background:var(--accentbg);}
        .btn-print{display:flex;align-items:center;gap:8px;padding:8px 20px;font-size:12px;font-weight:700;background:linear-gradient(135deg,#6366f1,#4f46e5);color:#fff;border:none;border-radius:10px;cursor:pointer;font-family:inherit;box-shadow:0 4px 14px rgba(99,102,241,0.30);white-space:nowrap;margin-left:8px;}
        .btn-print svg{width:14px;height:14px;stroke:#fff;fill:none;stroke-width:2;}

        /* TABLE */
        .tbl-wrap{overflow-x:auto;}
        table{width:100%;border-collapse:collapse;font-size:13px;}
        thead tr{background:#f8faff;}
        th{padding:11px 16px;text-align:left;font-size:10px;font-weight:700;color:var(--textmute);border-bottom:1.5px solid rgba(99,102,241,0.1);text-transform:uppercase;letter-spacing:.07em;white-space:nowrap;}
        td{padding:12px 16px;border-bottom:1px solid rgba(99,102,241,0.06);vertical-align:middle;}
        tbody tr:last-child td{border-bottom:none;}
        tbody tr:hover td{background:#f5f7ff;}
        tbody tr.row-absent td{background:#fdf4ff;}
        tbody tr.row-absent:hover td{background:#f3e8ff;}

        .date-day-cell{display:flex;flex-direction:column;gap:3px;}
        .date-day-badge{display:inline-flex;align-items:center;gap:5px;padding:2px 9px;border-radius:20px;font-size:10px;font-weight:700;letter-spacing:.04em;width:fit-content;}
        .day-senin{background:#eef2ff;color:#4338ca;border:1px solid #c7d2fe;}
        .day-selasa{background:#f0fdf4;color:#15803d;border:1px solid #bbf7d0;}
        .day-rabu{background:#fffbeb;color:#92400e;border:1px solid #fde68a;}
        .day-kamis{background:#fff1f2;color:#be123c;border:1px solid #fecdd3;}
        .day-jumat{background:#f5f3ff;color:#6d28d9;border:1px solid #ddd6fe;}
        .day-sabtu{background:#ecfeff;color:#0e7490;border:1px solid #a5f3fc;}
        .day-minggu{background:#fff7ed;color:#c2410c;border:1px solid #fed7aa;}

        .emp-avatar-sm{width:34px;height:34px;border-radius:10px;color:#fff;display:inline-flex;align-items:center;justify-content:center;font-size:11px;font-weight:800;flex-shrink:0;}
        .emp-name-cell{display:flex;align-items:center;gap:10px;}
        .emp-name-text{font-size:13px;font-weight:700;color:var(--text);}
        .emp-pin-text{font-family:'JetBrains Mono',monospace;font-size:10px;color:var(--textmute);}

        .time-badge{display:inline-flex;align-items:center;gap:6px;padding:5px 11px;border-radius:8px;font-family:'JetBrains Mono',monospace;font-size:12px;font-weight:600;white-space:nowrap;}
        .time-badge svg{width:12px;height:12px;flex-shrink:0;fill:none;stroke-width:2;}
        .time-ok{background:#dcfce7;color:#15803d;border:1px solid #bbf7d0;}.time-ok svg{stroke:#15803d;}
        .time-late{background:#fef3c7;color:#92400e;border:1px solid #fde68a;}.time-late svg{stroke:#d97706;}
        .time-early{background:#ffe4e6;color:#be123c;border:1px solid #fecdd3;}.time-early svg{stroke:#e11d48;}
        .time-none{font-size:11px;color:var(--textlight);font-style:italic;}
        .badge-absent{display:inline-flex;align-items:center;gap:5px;padding:5px 11px;border-radius:8px;background:#f3e8ff;color:#6d28d9;border:1px solid #ddd6fe;font-size:12px;font-weight:700;}
        .status-pill{font-size:11px;font-weight:700;padding:3px 10px;border-radius:20px;}
        .sp-tepat{background:#dcfce7;color:#15803d;border:1px solid #bbf7d0;}
        .sp-lambat{background:#fef3c7;color:#92400e;border:1px solid #fde68a;}
        .sp-cepat{background:#ffe4e6;color:#be123c;border:1px solid #fecdd3;}
        .sp-absent{background:#f3e8ff;color:#6d28d9;border:1px solid #ddd6fe;}

        /* KARYAWAN NAV */
        .karyawan-nav-strip{display:flex;align-items:center;gap:8px;padding:10px 1.4rem;background:#fafbff;border-bottom:1px solid var(--border);overflow-x:auto;}
        .karyawan-nav-label{font-size:11px;font-weight:700;color:var(--textmute);text-transform:uppercase;letter-spacing:.06em;white-space:nowrap;flex-shrink:0;}
        .karyawan-nav-btn{padding:5px 12px;border-radius:20px;font-size:11px;font-weight:700;border:1.5px solid rgba(99,102,241,0.15);background:#fff;color:var(--textmute);cursor:pointer;font-family:inherit;white-space:nowrap;flex-shrink:0;transition:all .15s;}
        .karyawan-nav-btn:hover{border-color:var(--accent);color:var(--accent);background:var(--accentbg);}
        .karyawan-nav-btn.active{background:var(--accent);color:#fff;border-color:var(--accent);box-shadow:0 2px 8px rgba(99,102,241,0.3);}

        .emp-table-header{display:flex;align-items:center;gap:16px;padding:16px 1.4rem;background:linear-gradient(135deg,#eef2ff 0%,#f0fdf4 100%);border-bottom:1.5px solid rgba(99,102,241,0.12);}
        .emp-table-avatar{width:56px;height:56px;border-radius:16px;color:#fff;display:flex;align-items:center;justify-content:center;font-size:18px;font-weight:800;flex-shrink:0;box-shadow:0 4px 16px rgba(0,0,0,0.18);}
        .emp-table-info{flex:1;}
        .emp-table-name{font-size:18px;font-weight:800;color:var(--text);letter-spacing:-.02em;}
        .emp-table-meta{display:flex;align-items:center;gap:10px;margin-top:5px;flex-wrap:wrap;}
        .emp-table-pin{font-family:'JetBrains Mono',monospace;font-size:11px;font-weight:600;background:var(--accent);color:#fff;padding:3px 10px;border-radius:6px;}
        .emp-table-days{font-size:12px;color:var(--textmute);font-weight:600;}
        .emp-table-stats{display:flex;gap:8px;flex-wrap:wrap;}
        .emp-stat-pill{display:flex;align-items:center;gap:5px;padding:5px 12px;border-radius:20px;font-size:11px;font-weight:700;}
        .esp-tepat{background:#dcfce7;color:#15803d;border:1px solid #bbf7d0;}
        .esp-lambat{background:#fef3c7;color:#92400e;border:1px solid #fde68a;}
        .esp-cepat{background:#ffe4e6;color:#be123c;border:1px solid #fecdd3;}
        .esp-absent{background:#f3e8ff;color:#6d28d9;border:1px solid #ddd6fe;}

        /* PAGINATION */
        .pg-bar{display:flex;align-items:center;justify-content:space-between;padding:12px 1.4rem;flex-wrap:wrap;gap:10px;border-top:1px solid rgba(99,102,241,0.08);background:#fafbff;}
        .pg-info{font-size:12px;color:var(--textmute);font-weight:500;}
        .pg-btns{display:flex;gap:4px;align-items:center;flex-wrap:wrap;}
        .pg-btn{min-width:32px;height:32px;padding:0 8px;border:1.5px solid rgba(99,102,241,0.15);background:#fff;color:var(--text);border-radius:8px;font-size:12px;font-weight:600;cursor:pointer;display:flex;align-items:center;justify-content:center;font-family:inherit;transition:all .15s;}
        .pg-btn:hover:not(:disabled){border-color:var(--accent);color:var(--accent);background:var(--accentbg);}
        .pg-btn:disabled{opacity:.35;cursor:not-allowed;}
        .pg-btn.pg-active{background:var(--accent);color:#fff;border-color:var(--accent);font-weight:700;}
        .pg-ellipsis{font-size:13px;padding:0 4px;color:var(--textlight);}

        /* PERFORMA */
        .emp-selector-wrap{padding:1.2rem 1.4rem 1.4rem;}
        .emp-selector-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(210px,1fr));gap:12px;}
        .emp-card{background:#fafbff;border:2px solid rgba(99,102,241,0.12);border-radius:14px;padding:14px;cursor:pointer;transition:all .22s;position:relative;overflow:hidden;}
        .emp-card::before{content:'';position:absolute;top:0;left:0;right:0;height:3px;background:transparent;transition:background .22s;}
        .emp-card:hover{border-color:rgba(99,102,241,0.35);background:#fff;transform:translateY(-2px);box-shadow:0 8px 24px rgba(99,102,241,0.13);}
        .emp-card.selected{border-color:var(--accent);background:#fff;box-shadow:0 8px 28px rgba(99,102,241,0.18);}
        .emp-card.selected::before{background:linear-gradient(90deg,var(--accent),var(--teal));}
        .emp-card-badge{position:absolute;top:10px;right:10px;width:20px;height:20px;border-radius:50%;background:var(--accent);display:none;align-items:center;justify-content:center;}
        .emp-card.selected .emp-card-badge{display:flex;}
        .emp-card-badge svg{width:10px;height:10px;stroke:#fff;fill:none;stroke-width:3;}
        .emp-card-header{display:flex;align-items:center;gap:10px;margin-bottom:12px;}
        .emp-card-avatar{width:42px;height:42px;border-radius:12px;color:#fff;display:flex;align-items:center;justify-content:center;font-size:14px;font-weight:800;flex-shrink:0;}
        .emp-card-name{font-size:13px;font-weight:800;color:var(--text);overflow:hidden;text-overflow:ellipsis;white-space:nowrap;max-width:130px;}
        .emp-card-pin{font-family:'JetBrains Mono',monospace;font-size:10px;color:var(--textmute);margin-top:2px;}
        .emp-card-stats{display:grid;grid-template-columns:repeat(4,1fr);gap:5px;}
        .emp-card-stat{text-align:center;padding:7px 4px;border-radius:9px;background:rgba(255,255,255,0.8);border:1px solid rgba(0,0,0,0.04);}
        .emp-card-stat-num{font-size:15px;font-weight:800;}
        .emp-card-stat-lbl{font-size:8px;font-weight:700;color:var(--textmute);text-transform:uppercase;letter-spacing:.05em;margin-top:2px;}
        .emp-absent-banner{margin-top:10px;padding:6px 10px;background:#f3e8ff;border:1px solid #ddd6fe;border-radius:9px;font-size:11px;font-weight:700;color:#7c3aed;}

        .chart-panel{display:none;}.chart-panel.show{display:block;}
        .bar-chart-topbar{display:flex;align-items:center;gap:12px;padding:1rem 1.4rem;border-bottom:1px solid var(--border);flex-wrap:wrap;background:linear-gradient(90deg,#fafbff,#fff);}
        .bar-chart-emp-info{display:flex;align-items:center;gap:12px;flex:1;}
        .bar-chart-emp-avatar{width:44px;height:44px;border-radius:12px;color:#fff;display:flex;align-items:center;justify-content:center;font-size:15px;font-weight:800;flex-shrink:0;}
        .bar-chart-emp-name{font-size:16px;font-weight:800;color:var(--text);}
        .bar-chart-emp-pin{font-family:'JetBrains Mono',monospace;font-size:11px;color:var(--textmute);margin-top:3px;}
        .bar-mode-btns{display:flex;gap:6px;flex-shrink:0;}
        .bar-mode-btn{padding:7px 16px;border-radius:9px;font-size:11px;font-weight:700;border:1.5px solid rgba(99,102,241,0.2);background:#fff;color:var(--textmute);cursor:pointer;font-family:inherit;transition:all .15s;white-space:nowrap;}
        .bar-mode-btn:hover{border-color:var(--accent);color:var(--accent);background:var(--accentbg);}
        .bar-mode-btn.active{background:linear-gradient(135deg,var(--accent),var(--accent2));color:#fff;border-color:var(--accent);}
        .bar-chart-body{padding:1.4rem 1.4rem 1rem;}
        .bar-chart-canvas-wrap{position:relative;height:400px;}

        .chart-legend-custom{display:flex;flex-wrap:wrap;gap:10px;padding:12px 1.4rem 0;justify-content:center;}
        .cl-item{display:flex;align-items:center;gap:6px;font-size:11px;font-weight:700;color:var(--textmute);}
        .cl-dot{width:12px;height:12px;border-radius:3px;flex-shrink:0;}
        .cl-line{width:20px;height:3px;border-radius:2px;flex-shrink:0;}

        .yearly-stat-strip{display:flex;gap:10px;padding:12px 1.4rem 16px;flex-wrap:wrap;border-top:1px solid rgba(99,102,241,0.08);background:#fafbff;}
        .ys-pill{display:flex;align-items:center;gap:10px;padding:10px 16px;border-radius:14px;flex:1;min-width:110px;}
        .ys-hadir{background:#ecfdf5;border:1px solid #bbf7d0;}
        .ys-lambat{background:#fef3c7;border:1px solid #fde68a;}
        .ys-cepat{background:#ffe4e6;border:1px solid #fecdd3;}
        .ys-tepat{background:#eef2ff;border:1px solid #c7d2fe;}
        .ys-absent{background:#f3e8ff;border:1px solid #ddd6fe;}
        .ys-icon{width:34px;height:34px;border-radius:10px;display:flex;align-items:center;justify-content:center;flex-shrink:0;}
        .ys-hadir .ys-icon{background:#d1fae5;}.ys-hadir .ys-icon svg{stroke:#16a34a;}
        .ys-lambat .ys-icon{background:#fef9c3;}.ys-lambat .ys-icon svg{stroke:#d97706;}
        .ys-cepat  .ys-icon{background:#ffe4e6;}.ys-cepat  .ys-icon svg{stroke:#e11d48;}
        .ys-tepat  .ys-icon{background:#e0e7ff;}.ys-tepat  .ys-icon svg{stroke:#6366f1;}
        .ys-absent .ys-icon{background:#ede9fe;}.ys-absent .ys-icon svg{stroke:#7c3aed;}
        .ys-icon svg{width:16px;height:16px;fill:none;stroke-width:2;}
        .ys-val{font-size:22px;font-weight:800;line-height:1;}
        .ys-hadir .ys-val{color:#16a34a;}.ys-lambat .ys-val{color:#d97706;}.ys-cepat .ys-val{color:#e11d48;}.ys-tepat .ys-val{color:#6366f1;}.ys-absent .ys-val{color:#7c3aed;}
        .ys-lbl{font-size:11px;color:var(--textmute);font-weight:600;margin-top:2px;}

        .chart-select-hint{display:flex;flex-direction:column;align-items:center;justify-content:center;padding:3rem 2rem;gap:12px;}
        .chart-select-hint-icon{width:64px;height:64px;border-radius:20px;background:linear-gradient(135deg,#eef2ff,#e0e7ff);display:flex;align-items:center;justify-content:center;}
        .chart-select-hint-icon svg{width:30px;height:30px;stroke:var(--accent);fill:none;stroke-width:1.5;}
        .chart-select-hint-text{font-size:15px;font-weight:700;color:var(--textmute);}
        .chart-select-hint-sub{font-size:12px;color:var(--textlight);}

        .empty{text-align:center;padding:3.5rem 1rem;}
        .empty-icon{font-size:40px;margin-bottom:14px;}
        .empty-text{font-size:14px;font-weight:700;color:var(--textmute);}
        .empty-sub{font-size:12px;color:var(--textlight);margin-top:6px;}

        .footer{text-align:center;padding:1.5rem 0 .5rem;font-size:12px;color:var(--textmute);}
        .footer span{color:var(--accent);font-weight:700;}

        @media print{
            .glow-blob,.nav-tabs{display:none!important;}
            body{background:#fff!important;}body::before{display:none!important;}
            .wrap{max-width:100%;padding:0 12px;}
            .period-filter-bar,.filter-row,.pg-bar,.karyawan-nav-strip,.footer,.btn-print,.btn-reset{display:none!important;}
            .card{box-shadow:none!important;border:1px solid #ddd!important;page-break-inside:avoid;}
            .topbar{background:#6366f1!important;-webkit-print-color-adjust:exact;print-color-adjust:exact;border-radius:12px!important;}
            @page{margin:1.5cm;size:A4;}
        }

        @keyframes slideDown{from{opacity:0;transform:translateY(-14px);}to{opacity:1;transform:translateY(0);}}
        @keyframes fadeUp{from{opacity:0;transform:translateY(10px);}to{opacity:1;transform:translateY(0);}}
        @keyframes pulse{0%,100%{opacity:1;transform:scale(1);}50%{opacity:.4;transform:scale(.5);}}
        @keyframes spin{to{transform:rotate(360deg);}}
        @keyframes rowIn{from{opacity:0;transform:translateY(6px);}to{opacity:1;transform:translateY(0);}}

        @media(max-width:900px){.stat-grid{grid-template-columns:repeat(3,1fr);}}
        @media(max-width:600px){.filter-row{grid-template-columns:1fr;}.topbar{flex-wrap:wrap;}.wrap{padding:1rem 1rem 2rem;}.donut-wrap{flex-direction:column;}.bar-chart-canvas-wrap{height:280px;}.stat-grid{grid-template-columns:repeat(2,1fr);}.emp-table-header{flex-wrap:wrap;}}
    </style>
</head>
<body>
<div class="glow-blob glow-1"></div>
<div class="glow-blob glow-2"></div>
<div class="glow-blob glow-3"></div>

<div class="wrap">
    <!-- TOPBAR -->
    <div class="topbar">
        <div class="logo-wrap">
            <img src="{{ asset('images/kipin.png') }}" alt="Kipin" onerror="this.style.display='none'">
        </div>
        <div class="topbar-info">
            <div class="topbar-title">Monitoring Absensi Karyawan</div>
            <div class="topbar-sub"><span class="live-dot"></span>Kipin &mdash; Tampilan Publik</div>
        </div>
        <div style="display:flex;align-items:center;gap:14px;flex-shrink:0;flex-wrap:wrap;">
            <div><div class="clock" id="liveClock"></div><div class="date-label" id="liveDate"></div></div>
            <div class="readonly-badge">
                <svg viewBox="0 0 24 24"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                View Only
            </div>
        </div>
    </div>

    <!-- NAV TABS -->
    <div class="nav-tabs">
        <button class="nav-tab active" id="tabDashboard" onclick="switchPage('dashboard')">
            <svg viewBox="0 0 24 24"><rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/></svg>
            Dashboard &amp; Tabel
        </button>
        <button class="nav-tab" id="tabPerforma" onclick="switchPage('performa')">
            <svg viewBox="0 0 24 24"><rect x="2" y="13" width="4" height="9" rx="1"/><rect x="9" y="9" width="4" height="13" rx="1"/><rect x="16" y="5" width="4" height="17" rx="1"/><path d="M4 6l4-3 4 3 4-4"/></svg>
            Chart Performa Karyawan
        </button>
    </div>

    <!-- ═══ PAGE DASHBOARD ═══ -->
    <div class="page active" id="pageDashboard">

        <div class="period-filter-bar">
            <div class="period-filter-icon"><svg viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="18" rx="2"/><path d="M16 2v4M8 2v4M3 10h18"/></svg></div>
            <span class="period-filter-label">Tampilan</span>
            <div class="view-mode-pills">
                <button class="view-pill" id="vpHarian"   onclick="setViewMode('harian')">Harian</button>
                <button class="view-pill" id="vpMingguan" onclick="setViewMode('mingguan')">Mingguan</button>
                <button class="view-pill active" id="vpBulanan"  onclick="setViewMode('bulanan')">Bulanan</button>
                <button class="view-pill" id="vpTahunan"  onclick="setViewMode('tahunan')">Tahunan</button>
            </div>
            <div class="period-selects">
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
            <div class="period-badge"><span class="period-badge-dot"></span><span id="periodBadgeText">—</span></div>
        </div>

        <div class="stat-grid">
            <div class="stat-card c-total">
                <div class="stat-icon icon-total"><svg viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="18" rx="2"/><path d="M16 2v4M8 2v4M3 10h18"/></svg></div>
                <div class="stat-label">Total Absensi</div><div class="stat-val v-total" id="statTotal">0</div>
            </div>
            <div class="stat-card c-tepat">
                <div class="stat-icon icon-tepat"><svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="9"/><path d="M9 12l2 2 4-4"/></svg></div>
                <div class="stat-label">Tepat Waktu</div><div class="stat-val v-tepat" id="statTepat">0</div>
            </div>
            <div class="stat-card c-lambat">
                <div class="stat-icon icon-lambat"><svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="9"/><path d="M12 8v4"/><circle cx="12" cy="16" r=".5" fill="currentColor"/></svg></div>
                <div class="stat-label">Terlambat</div><div class="stat-val v-lambat" id="statLambat">0</div>
            </div>
            <div class="stat-card c-cepat">
                <div class="stat-icon icon-cepat"><svg viewBox="0 0 24 24"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg></div>
                <div class="stat-label">Pulang Cepat</div><div class="stat-val v-cepat" id="statCepat">0</div>
            </div>
            <div class="stat-card c-absent">
                <div class="stat-icon icon-absent"><svg viewBox="0 0 24 24"><circle cx="12" cy="8" r="4"/><path d="M4 20c0-4 3.58-7 8-7s8 3 8 7"/><line x1="17" y1="11" x2="23" y2="11"/></svg></div>
                <div class="stat-label">Tidak Masuk</div><div class="stat-val v-absent" id="statAbsent">0</div>
            </div>
        </div>

        <!-- DONUT CHART -->
        <div class="card">
            <div class="card-head">
                <div class="card-head-icon"><svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="9"/><path d="M12 3a9 9 0 0 1 9 9h-9z"/></svg></div>
                Grafik Ringkasan — <span id="chartPeriodLabel" style="color:var(--accent);margin-left:4px">—</span>
                <span class="chip" id="chartViewChip">Bulanan</span>
                <button class="btn-print" onclick="window.print()"><svg viewBox="0 0 24 24"><polyline points="6 9 6 2 18 2 18 9"/><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/><rect x="6" y="14" width="12" height="8"/></svg>Print / PDF</button>
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

        <div class="card">
            <div class="card-head"><div class="card-head-icon"><svg viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg></div>Filter &amp; Pencarian</div>
            <div class="card-body">
                <div class="filter-row">
                    <div class="f-group"><label class="f-label">Cari Nama</label><input class="f-input" type="text" id="searchName" placeholder="Nama karyawan..." oninput="applyFiltersAndRender()"></div>
                    <div class="f-group"><label class="f-label">Tanggal</label><input class="f-input" type="date" id="filterDate" onchange="applyFiltersAndRender()"></div>
                    <div class="f-group"><label class="f-label">Keterangan</label>
                        <select class="f-input" id="filterKet" onchange="applyFiltersAndRender()">
                            <option value="">Semua</option><option value="tepat">Tepat Waktu</option>
                            <option value="terlambat">Terlambat</option><option value="cepat">Pulang Cepat</option><option value="absent">Tidak Masuk</option>
                        </select>
                    </div>
                    <div><button class="btn-reset" onclick="resetFilter()">&#8635; Reset</button></div>
                </div>
            </div>
        </div>

        <!-- DATA PRESENSI -->
        <div class="card">
            <div class="card-head">
                <div class="card-head-icon"><svg viewBox="0 0 24 24"><rect x="3" y="3" width="18" height="18" rx="2"/><path d="M3 9h18M9 21V9"/></svg></div>
                Data Presensi — <span id="tablePeriodLabel" style="color:var(--accent);margin-left:4px">—</span>
                <span class="chip" id="countChip">0 data</span>
            </div>
            <div class="karyawan-nav-strip" id="karyawanNavStrip" style="display:none"><span class="karyawan-nav-label">Karyawan:</span></div>
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

    </div><!-- /pageDashboard -->

    <!-- ═══ PAGE PERFORMA ═══ -->
    <div class="page" id="pagePerforma">
        <div class="period-filter-bar">
            <div class="period-filter-icon"><svg viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="18" rx="2"/><path d="M16 2v4M8 2v4M3 10h18"/></svg></div>
            <span class="period-filter-label">Periode</span>
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
            <div class="period-badge"><span class="period-badge-dot"></span><span id="perfYearBadge">—</span></div>
            <div style="margin-left:auto;font-size:12px;color:var(--textmute);font-weight:600;" id="perfPeriodHint"></div>
        </div>

        <div class="card">
            <div class="card-head">
                <div class="card-head-icon"><svg viewBox="0 0 24 24"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75"/></svg></div>
                Pilih Karyawan <span class="chip" id="empCountChip">0 karyawan</span>
                <span style="font-size:11px;color:var(--textmute);font-weight:500;margin-left:4px">← Klik untuk lihat chart</span>
            </div>
            <div class="emp-selector-wrap"><div class="emp-selector-grid" id="empSelectorGrid"></div></div>
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
                <div class="chart-select-hint-icon"><svg viewBox="0 0 24 24"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/></svg></div>
                <div class="chart-select-hint-text">Pilih karyawan di atas untuk melihat chart performa</div>
                <div class="chart-select-hint-sub">Chart menampilkan data kehadiran per bulan / per hari</div>
            </div>
        </div>
    </div><!-- /pagePerforma -->

    <div class="footer">&copy; {{ date('Y') }} <span>Kipin</span> &mdash; Sistem Monitoring Absensi &middot; Sakera</div>
</div>

<!-- RAW DATA — sama persis dengan admin, hanya read-only -->
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

/* ══ PARSE DATA ══ */
var RAW_DATA = [];
try { RAW_DATA = JSON.parse(document.getElementById('rawDataScript').textContent); }
catch(e) { console.error('RAW parse error', e); }

/* ══ STATE ══ */
var viewMode         = 'bulanan';
var currentEmpPage   = 1;
var donutChart       = null;
var empBarChart      = null;
var selectedEmpPin   = null;
var selectedEmpColor = '#6366f1';
var barMode          = 'all';

/* ══ CLOCK ══ */
function tick() {
    var d = new Date();
    document.getElementById('liveClock').textContent = d.toLocaleTimeString('id-ID',{hour:'2-digit',minute:'2-digit',second:'2-digit'});
    document.getElementById('liveDate').textContent  = d.toLocaleDateString('id-ID',{weekday:'long',day:'numeric',month:'long',year:'numeric'});
}
tick(); setInterval(tick, 1000);

/* ══ INIT ══ */
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
        RAW_DATA.forEach(function(r){ if (r.tahun===nowY) monthCount[r.bulan]=(monthCount[r.bulan]||0)+1; });
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

/* ══ HELPER ══ */
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
function lighten(hex){
    if(!hex||hex.length<7) return '#818cf8';
    var r=parseInt(hex.slice(1,3),16),g=parseInt(hex.slice(3,5),16),b=parseInt(hex.slice(5,7),16);
    return '#'+[Math.min(255,r+70),Math.min(255,g+70),Math.min(255,b+70)].map(function(v){return v.toString(16).padStart(2,'0');}).join('');
}

/* ══ DONUT ══ */
function initDonut() {
    var ctx = document.getElementById('donutChart').getContext('2d');
    donutChart = new Chart(ctx, {
        type:'doughnut',
        data:{ labels:DONUT_LABELS, datasets:[{ data:[0,0,0,0,0], backgroundColor:DONUT_COLORS, borderColor:'#ffffff', borderWidth:4, hoverOffset:12 }] },
        options:{
            responsive:true, maintainAspectRatio:false, cutout:'72%',
            animation:{ animateScale:true, duration:700, easing:'easeInOutQuart' },
            plugins:{
                legend:{ display:false },
                tooltip:{ callbacks:{ label:function(c){ var t=c.dataset.data.reduce(function(a,b){return a+b;},0); return '  '+c.label+': '+c.raw+' ('+(t>0?Math.round(c.raw/t*100):0)+'%)'; } }, padding:10, cornerRadius:10 }
            },
            onHover:function(e,els){ onDonutHover(els); }
        }
    });
}
function onDonutHover(els) {
    var data=donutChart.data.datasets[0].data, total=data.reduce(function(a,b){return a+b;},0);
    var nE=document.getElementById('donutCenterNum'), lE=document.getElementById('donutCenterLbl');
    if (els.length) { var i=els[0].index; nE.textContent=data[i]; nE.style.color=DONUT_COLORS[i]; lE.textContent=DONUT_LABELS[i]; }
    else { nE.textContent=total; nE.style.color='var(--text)'; lE.textContent='total'; }
}
function updateDonut(vals) {
    if (!donutChart) return;
    var total=vals.reduce(function(a,b){return a+b;},0);
    donutChart.data.datasets[0].data=vals; donutChart.update();
    var nE=document.getElementById('donutCenterNum');
    nE.textContent=total; nE.style.color='var(--text)';
    document.getElementById('donutCenterLbl').textContent='total';
    renderLegend(vals,total);
}
function renderLegend(vals, total) {
    var c=document.getElementById('donutLegends'); c.innerHTML='';
    DONUT_LABELS.forEach(function(lbl,i){
        var pct=total>0?Math.round(vals[i]/total*100):0;
        var div=document.createElement('div');
        div.className='donut-legend-item';
        div.style.background=DONUT_BGS[i]; div.style.borderLeft='4px solid '+DONUT_COLORS[i];
        div.setAttribute('onmouseover','hoverDonut('+i+')');
        div.setAttribute('onmouseout','resetDonut()');
        div.innerHTML='<span class="donut-legend-dot" style="background:'+DONUT_COLORS[i]+'"></span><span class="donut-legend-label">'+lbl+'</span><span class="donut-legend-val" style="color:'+DONUT_TXT[i]+'">'+vals[i]+'<span class="donut-legend-pct">('+pct+'%)</span></span>';
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
    document.getElementById('donutCenterNum').style.color='var(--text)';
    document.getElementById('donutCenterLbl').textContent='total';
}

/* ══ PERIOD / VIEW MODE ══ */
function setViewMode(mode) {
    viewMode=mode;
    ['vpHarian','vpMingguan','vpBulanan','vpTahunan'].forEach(function(id){ document.getElementById(id).classList.remove('active'); });
    document.getElementById({harian:'vpHarian',mingguan:'vpMingguan',bulanan:'vpBulanan',tahunan:'vpTahunan'}[mode]).classList.add('active');
    document.getElementById('filterBulan').style.display  = (mode!=='tahunan') ? '' : 'none';
    document.getElementById('filterMinggu').style.display = (mode==='mingguan') ? '' : 'none';
    document.getElementById('filterHari').style.display   = (mode==='harian') ? '' : 'none';
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
    var tgl = p.tahun+'-'+String(p.bulan).padStart(2,'0')+'-'+String(p.hari).padStart(2,'0');
    return getNamaHari(tgl)+', '+p.hari+' '+NAMA_BULAN[p.bulan]+' '+p.tahun;
}
function viewLabel() { return {harian:'Harian',mingguan:'Mingguan',bulanan:'Bulanan',tahunan:'Tahunan'}[viewMode]; }

/* ══ MAIN PERIOD CHANGE ══ */
function onPeriodChange() {
    if (viewMode === 'harian') {
        populateHari(parseInt(document.getElementById('filterTahun').value), parseInt(document.getElementById('filterBulan').value));
    }
    var p=getPeriod(), lbl=periodLabel(p);
    ['periodBadgeText','chartPeriodLabel','tablePeriodLabel'].forEach(function(id){ document.getElementById(id).textContent=lbl; });
    document.getElementById('chartViewChip').textContent=viewLabel();

    var pr=filterByPeriod(RAW_DATA,p);
    var cTepat  = pr.filter(function(r){return r.isMasuk&&!r.terlambat;}).length;
    var cLambat = pr.filter(function(r){return r.terlambat;}).length;
    var cCepat  = pr.filter(function(r){return r.pulangCepat;}).length;
    var cAbsent = hitungAbsen(pr,p);

    ['statTotal','statTepat','statLambat','statCepat','statAbsent'].forEach(function(id,i){
        var el=document.getElementById(id);
        el.textContent=[pr.length,cTepat,cLambat,cCepat,cAbsent][i];
        el.classList.remove('pop'); void el.offsetWidth; el.classList.add('pop');
    });

    var cPulangTepat=pr.filter(function(r){return !r.isMasuk&&!r.pulangCepat;}).length;
    updateDonut([cTepat,cLambat,cCepat,cPulangTepat,cAbsent]);

    currentEmpPage=1;
    if (viewMode==='harian') { renderDailyAllEmployees(pr, p); }
    else { renderTable(buildGrouped(pr,p)); }
}

/* ══ DAILY VIEW ══ */
function renderDailyAllEmployees(periodRecords, p) {
    var allK={};
    RAW_DATA.forEach(function(r){ if(!allK[r.pin]) allK[r.pin]=r.nama; });
    var dayData={};
    Object.keys(allK).forEach(function(pin){
        dayData[pin]={ pin:pin, nama:allK[pin], masuk:null, pulang:null, terlambat:false, pulangCepat:false, absent:true };
    });
    periodRecords.forEach(function(r){
        if(!dayData[r.pin]) dayData[r.pin]={ pin:r.pin, nama:r.nama, masuk:null, pulang:null, terlambat:false, pulangCepat:false, absent:true };
        var d=dayData[r.pin];
        if(r.isMasuk){ d.absent=false; if(!d.masuk||r.waktu<d.masuk){d.masuk=r.waktu; d.terlambat=r.terlambat;} }
        else { if(!d.pulang||r.waktu>d.pulang){d.pulang=r.waktu; d.pulangCepat=r.pulangCepat;} }
    });
    var rows=Object.values(dayData).sort(function(a,b){ return a.nama<b.nama?-1:1; });
    var jmlHadir=rows.filter(function(r){return !r.absent;}).length;
    var jmlLambat=rows.filter(function(r){return r.terlambat;}).length;
    var jmlAbsent=rows.filter(function(r){return r.absent;}).length;
    var tglStr = p.tahun+'-'+String(p.bulan).padStart(2,'0')+'-'+String(p.hari).padStart(2,'0');
    var namaHari = getNamaHari(tglStr);
    var hariCss  = getHariCssClass(tglStr);
    var tglFmt   = p.hari+' '+NAMA_BULAN[p.bulan]+' '+p.tahun;
    document.getElementById('countChip').textContent=rows.length+' karyawan';
    document.getElementById('karyawanNavStrip').style.display='none';
    document.getElementById('empTableHeader').style.display='none';
    document.getElementById('tableHead').innerHTML='<tr><th>No</th><th>Karyawan</th><th>Jam Masuk</th><th>Jam Pulang</th><th>Status</th></tr>';
    var tbody=document.getElementById('tableBody'); tbody.innerHTML='';
    if(rows.length===0){
        tbody.innerHTML='<tr><td colspan="5"><div class="empty"><div class="empty-icon">📋</div><div class="empty-text">Tidak ada data untuk hari ini</div></div></td></tr>';
    } else {
        rows.forEach(function(emp, idx){
            var color=PALETTE[idx%PALETTE.length];
            var initials=emp.nama.substring(0,2).toUpperCase();
            var tr=document.createElement('tr');
            tr.style.animation='rowIn .25s '+(idx*0.02)+'s ease both';
            if(emp.absent) tr.className='row-absent';
            var noCell='<td style="color:var(--textlight);font-size:12px;font-family:\'JetBrains Mono\',monospace">'+String(idx+1).padStart(2,'0')+'</td>';
            var empCell='<td><div class="emp-name-cell">'+
                '<div class="emp-avatar-sm" style="background:linear-gradient(135deg,'+color+','+lighten(color)+')">'+initials+'</div>'+
                '<div><div class="emp-name-text">'+emp.nama+'</div>'+
                '<div style="display:flex;align-items:center;gap:6px;margin-top:3px;">'+
                '<span class="emp-pin-text">PIN: '+emp.pin+'</span>'+
                '<span class="date-day-badge '+hariCss+'">'+namaHari+'</span>'+
                '</div></div></div></td>';
            if(emp.absent){
                tr.innerHTML=noCell+empCell+
                    '<td><span class="badge-absent"><svg viewBox="0 0 24 24" width="12" height="12" fill="none" stroke="#6d28d9" stroke-width="2"><circle cx="12" cy="8" r="4"/><path d="M4 20c0-4 3.58-7 8-7s8 3 8 7"/><line x1="17" y1="11" x2="23" y2="11"/></svg>Tidak Masuk</span></td>'+
                    '<td><span class="time-none">—</span></td>'+
                    '<td><span class="status-pill sp-absent">⚠ Absen</span></td>';
            } else {
                var jamMasuk='<span class="time-none">—</span>';
                if(emp.masuk) jamMasuk=emp.terlambat
                    ?'<span class="time-badge time-late"><svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="9"/><path d="M12 8v4"/><circle cx="12" cy="16" r=".8" fill="#d97706"/></svg>'+emp.masuk+'</span>'
                    :'<span class="time-badge time-ok"><svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="9"/><path d="M9 12l2 2 4-4"/></svg>'+emp.masuk+'</span>';
                var jamPulang='<span class="time-none">—</span>';
                if(emp.pulang) jamPulang=emp.pulangCepat
                    ?'<span class="time-badge time-early"><svg viewBox="0 0 24 24"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>'+emp.pulang+'</span>'
                    :'<span class="time-badge time-ok"><svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="9"/><path d="M9 12l2 2 4-4"/></svg>'+emp.pulang+'</span>';
                var status='<span class="time-none">—</span>';
                if(emp.terlambat) status='<span class="status-pill sp-lambat">⚠ Terlambat</span>';
                else if(emp.pulangCepat) status='<span class="status-pill sp-cepat">↩ Pulang Cepat</span>';
                else if(emp.masuk) status='<span class="status-pill sp-tepat">✓ Tepat Waktu</span>';
                tr.innerHTML=noCell+empCell+'<td>'+jamMasuk+'</td><td>'+jamPulang+'</td><td>'+status+'</td>';
            }
            tbody.appendChild(tr);
        });
    }
    document.getElementById('pgInfo').innerHTML='📅 <strong>'+namaHari+', '+tglFmt+'</strong> &nbsp;|&nbsp; <span style="color:#16a34a;font-weight:700">✓ Hadir: '+jmlHadir+'</span> &nbsp;<span style="color:#d97706;font-weight:700">⚠ Terlambat: '+jmlLambat+'</span> &nbsp;<span style="color:#7c3aed;font-weight:700">✕ Absen: '+jmlAbsent+'</span>';
    document.getElementById('pgBtns').innerHTML='';
}

/* ══ HITUNG ABSEN ══ */
function hitungAbsen(periodRecords, p) {
    var datesInPeriod={};
    periodRecords.forEach(function(r){ datesInPeriod[r.tanggal]=true; });
    var dates=Object.keys(datesInPeriod);
    if(dates.length===0) return 0;
    var allPins={};
    RAW_DATA.forEach(function(r){ allPins[r.pin]=true; });
    var totalKaryawan=Object.keys(allPins).length;
    var absenTotal=0;
    dates.forEach(function(dt){
        var hadir={};
        periodRecords.forEach(function(r){ if(r.tanggal===dt&&r.isMasuk) hadir[r.pin]=true; });
        absenTotal+=totalKaryawan-Object.keys(hadir).length;
    });
    return Math.max(0,absenTotal);
}

/* ══ BUILD GROUPED ══ */
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
        else { if(!dObj.pulang||r.waktu>dObj.pulang){dObj.pulang=r.waktu;dObj.pulangCepat=r.pulangCepat;} }
    });
    var result=[];
    Object.keys(kMap).forEach(function(pin){
        var k=kMap[pin];
        var days=Object.values(k.days).sort(function(a,b){return a.tanggal<b.tanggal?-1:1;});
        if(allDates.length>0) result.push({ pin:k.pin, nama:k.nama, days:days });
    });
    result.sort(function(a,b){return a.nama<b.nama?-1:1;});
    return result;
}

/* ══ FILTER & RENDER ══ */
function applyFiltersAndRender() {
    var p=getPeriod(), pr=filterByPeriod(RAW_DATA,p);
    if(viewMode==='harian'){ renderDailyAllEmployees(pr,p); return; }
    var grouped=buildGrouped(pr,p);
    var name=document.getElementById('searchName').value.toLowerCase().trim();
    var dateF=document.getElementById('filterDate').value;
    var ket=document.getElementById('filterKet').value;
    if(name)  grouped=grouped.filter(function(k){return k.nama.toLowerCase().indexOf(name)!==-1;});
    if(dateF) grouped=grouped.map(function(k){return {pin:k.pin,nama:k.nama,days:k.days.filter(function(d){return d.tanggal===dateF;})};}).filter(function(k){return k.days.length>0;});
    if(ket){
        grouped=grouped.map(function(k){
            return {pin:k.pin,nama:k.nama,days:k.days.filter(function(d){
                if(ket==='terlambat') return d.terlambat;
                if(ket==='cepat')    return d.pulangCepat;
                if(ket==='tepat')    return d.masuk&&!d.terlambat;
                if(ket==='absent')   return d.absent;
                return true;
            })};
        }).filter(function(k){return k.days.length>0;});
    }
    currentEmpPage=1;
    renderTable(grouped);
}
function resetFilter() {
    document.getElementById('searchName').value='';
    document.getElementById('filterDate').value='';
    document.getElementById('filterKet').value='';
    applyFiltersAndRender();
}

/* ══ RENDER TABLE ══ */
function renderTable(grouped) {
    document.getElementById('tableHead').innerHTML='<tr><th>No</th><th>Tanggal</th><th>Jam Masuk</th><th>Jam Pulang</th><th>Status</th></tr>';
    document.getElementById('karyawanNavStrip').style.display='';
    document.getElementById('empTableHeader').style.display='';
    var tbody=document.getElementById('tableBody');
    var pgBtns=document.getElementById('pgBtns');
    var pgInfo=document.getElementById('pgInfo');
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
    if(total===0){
        resetEmpHeader();
        tbody.innerHTML='<tr><td colspan="5"><div class="empty"><div class="empty-icon">🔍</div><div class="empty-text">Tidak ada data untuk periode ini</div></div></td></tr>';
        pgInfo.textContent='Tidak ada data'; pgBtns.innerHTML=''; return;
    }
    var k=grouped[currentEmpPage-1];
    updateEmpHeader(k,currentEmpPage-1);
    k.days.forEach(function(d,idx){
        var tr=document.createElement('tr');
        tr.style.animation='rowIn .25s '+(idx*0.025)+'s ease both';
        var noCell='<td style="color:var(--textlight);font-size:12px;font-family:\'JetBrains Mono\',monospace">'+String(idx+1).padStart(2,'0')+'</td>';
        if(d.absent){
            tr.className='row-absent';
            tr.innerHTML=noCell+'<td style="font-size:12px;color:var(--textmute)">'+d.tanggalFmt+'</td>'+
                '<td><span class="badge-absent"><svg viewBox="0 0 24 24" width="12" height="12" fill="none" stroke="#7c3aed" stroke-width="2"><circle cx="12" cy="8" r="4"/><path d="M4 20c0-4 3.58-7 8-7s8 3 8 7"/><line x1="17" y1="11" x2="23" y2="11"/></svg>Tidak Masuk</span></td>'+
                '<td><span class="time-none">—</span></td>'+
                '<td><span style="font-size:11px;font-weight:700;color:#6d28d9;background:#f3e8ff;padding:3px 10px;border-radius:20px;border:1px solid #ddd6fe;">⚠ Absen</span></td>';
        } else {
            var jamMasuk='<span class="time-none">—</span>';
            if(d.masuk) jamMasuk=d.terlambat
                ?'<span class="time-badge time-late"><svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="9"/><path d="M12 8v4"/><circle cx="12" cy="16" r=".8" fill="#d97706"/></svg>'+d.masuk+'</span>'
                :'<span class="time-badge time-ok"><svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="9"/><path d="M9 12l2 2 4-4"/></svg>'+d.masuk+'</span>';
            var jamPulang='<span class="time-none">—</span>';
            if(d.pulang) jamPulang=d.pulangCepat
                ?'<span class="time-badge time-early"><svg viewBox="0 0 24 24"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>'+d.pulang+'</span>'
                :'<span class="time-badge time-ok"><svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="9"/><path d="M9 12l2 2 4-4"/></svg>'+d.pulang+'</span>';
            var status='<span class="time-none">—</span>';
            if(d.terlambat) status='<span class="status-pill sp-lambat">⚠ Terlambat</span>';
            else if(d.pulangCepat) status='<span class="status-pill sp-cepat">↩ Pulang Cepat</span>';
            else if(d.masuk) status='<span class="status-pill sp-tepat">✓ Tepat Waktu</span>';
            tr.innerHTML=noCell+'<td style="font-size:12px;color:var(--textmute)">'+d.tanggalFmt+'</td><td>'+jamMasuk+'</td><td>'+jamPulang+'</td><td>'+status+'</td>';
        }
        tbody.appendChild(tr);
    });
    pgInfo.textContent='Karyawan '+currentEmpPage+' dari '+total;
    pgBtns.innerHTML='';
    function mkBtn(label,page,disabled,active){
        var b=document.createElement('button');
        b.className='pg-btn'+(active?' pg-active':''); b.innerHTML=label; b.disabled=disabled;
        b.onclick=function(){currentEmpPage=page;renderTable(grouped);}; pgBtns.appendChild(b);
    }
    function mkE(){ var s=document.createElement('span'); s.className='pg-ellipsis'; s.textContent='…'; pgBtns.appendChild(s); }
    mkBtn('&#8592;',currentEmpPage-1,currentEmpPage===1,false);
    var pages=[];
    if(total<=7){for(var i=1;i<=total;i++) pages.push(i);}
    else{
        pages.push(1);
        if(currentEmpPage>3) pages.push('…');
        var lo=Math.max(2,currentEmpPage-1), hi=Math.min(total-1,currentEmpPage+1);
        for(var j=lo;j<=hi;j++) pages.push(j);
        if(currentEmpPage<total-2) pages.push('…');
        pages.push(total);
    }
    pages.forEach(function(pg){if(pg==='…') mkE(); else mkBtn(pg,pg,false,pg===currentEmpPage);});
    mkBtn('&#8594;',currentEmpPage+1,currentEmpPage===total,false);
}

/* ══ EMP HEADER ══ */
function updateEmpHeader(k,colorIdx){
    var color=PALETTE[colorIdx%PALETTE.length], initials=k.nama.substring(0,2).toUpperCase();
    var av=document.getElementById('empTableAvatar');
    av.textContent=initials; av.style.background='linear-gradient(135deg,'+color+','+lighten(color)+')';
    document.getElementById('empTableName').textContent=k.nama;
    document.getElementById('empTablePin').textContent='PIN: '+k.pin;
    document.getElementById('empTableDays').textContent=k.days.length+' hari';
    var tepat=0,lambat=0,cepat=0,absent=0;
    k.days.forEach(function(d){ if(d.absent) absent++; else{ if(d.terlambat) lambat++; else if(d.masuk) tepat++; if(d.pulangCepat) cepat++; } });
    var html='';
    if(tepat)  html+='<div class="emp-stat-pill esp-tepat">✓ '+tepat+' Tepat</div>';
    if(lambat) html+='<div class="emp-stat-pill esp-lambat">⚠ '+lambat+' Terlambat</div>';
    if(cepat)  html+='<div class="emp-stat-pill esp-cepat">↩ '+cepat+' Pulang Cepat</div>';
    if(absent) html+='<div class="emp-stat-pill esp-absent">✕ '+absent+' Absen</div>';
    document.getElementById('empTableStats').innerHTML=html;
}
function resetEmpHeader(){
    document.getElementById('empTableAvatar').textContent='??';
    document.getElementById('empTableAvatar').style.background='linear-gradient(135deg,#6366f1,#0d9488)';
    document.getElementById('empTableName').textContent='—';
    document.getElementById('empTablePin').textContent='—';
    document.getElementById('empTableDays').textContent='0 hari';
    document.getElementById('empTableStats').innerHTML='';
}

/* ══ PAGE SWITCH ══ */
function switchPage(p){
    ['pageDashboard','pagePerforma'].forEach(function(id){document.getElementById(id).classList.remove('active');});
    ['tabDashboard','tabPerforma'].forEach(function(id){document.getElementById(id).classList.remove('active');});
    document.getElementById('page'+p.charAt(0).toUpperCase()+p.slice(1)).classList.add('active');
    document.getElementById('tab'+p.charAt(0).toUpperCase()+p.slice(1)).classList.add('active');
    if(p==='performa') renderPerfPage();
}

/* ══ PERFORMA PAGE ══ */
function getEmpsForPeriod(tahun,bulan){
    var allPins={};
    RAW_DATA.filter(function(r){return r.tahun===tahun;}).forEach(function(r){ if(!allPins[r.pin]) allPins[r.pin]={pin:r.pin,nama:r.nama}; });
    var relevant=RAW_DATA.filter(function(r){ if(r.tahun!==tahun) return false; if(bulan>0&&r.bulan!==bulan) return false; return true; });
    var workDates={};
    relevant.forEach(function(r){ workDates[r.tanggal]=true; });
    var totalWD=Object.keys(workDates).length;
    var pinStats={};
    relevant.forEach(function(r){
        if(!pinStats[r.pin]) pinStats[r.pin]={dateSet:{}};
        var dk=r.tanggal;
        if(!pinStats[r.pin].dateSet[dk]) pinStats[r.pin].dateSet[dk]={masuk:false,terlambat:false,pulangCepat:false};
        if(r.isMasuk){pinStats[r.pin].dateSet[dk].masuk=true;if(r.terlambat)pinStats[r.pin].dateSet[dk].terlambat=true;}
        else{if(r.pulangCepat)pinStats[r.pin].dateSet[dk].pulangCepat=true;}
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
function renderPerfPage(){
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
function renderEmpSelector(emps,tahun,bulan){
    var grid=document.getElementById('empSelectorGrid'); grid.innerHTML='';
    if(!emps.length){ grid.innerHTML='<div class="empty"><div class="empty-icon">📊</div><div class="empty-text">Tidak ada data</div></div>'; return; }
    emps.forEach(function(emp,i){
        var color=PALETTE[i%PALETTE.length], initials=emp.nama.substring(0,2).toUpperCase();
        var card=document.createElement('div');
        card.className='emp-card'+(emp.pin===selectedEmpPin?' selected':'');
        card.innerHTML=
            '<div class="emp-card-badge"><svg viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg></div>'+
            '<div class="emp-card-header">'+
                '<div class="emp-card-avatar" style="background:linear-gradient(135deg,'+color+','+lighten(color)+')">'+initials+'</div>'+
                '<div style="min-width:0"><div class="emp-card-name" title="'+emp.nama+'">'+emp.nama+'</div><div class="emp-card-pin">PIN: '+emp.pin+'</div></div>'+
            '</div>'+
            '<div class="emp-card-stats">'+
                '<div class="emp-card-stat"><div class="emp-card-stat-num" style="color:'+(emp.hadir>0?color:'#94a3b8')+'">'+emp.hadir+'</div><div class="emp-card-stat-lbl">Hadir</div></div>'+
                '<div class="emp-card-stat"><div class="emp-card-stat-num" style="color:'+(emp.lambat>0?'#d97706':'#94a3b8')+'">'+emp.lambat+'</div><div class="emp-card-stat-lbl">Lambat</div></div>'+
                '<div class="emp-card-stat"><div class="emp-card-stat-num" style="color:'+(emp.cepat>0?'#e11d48':'#94a3b8')+'">'+emp.cepat+'</div><div class="emp-card-stat-lbl">Cepat</div></div>'+
                '<div class="emp-card-stat"><div class="emp-card-stat-num" style="color:'+(emp.absent>0?'#7c3aed':'#94a3b8')+'">'+emp.absent+'</div><div class="emp-card-stat-lbl">Absen</div></div>'+
            '</div>'+
            (emp.absent>0?'<div class="emp-absent-banner">⚠ Tidak masuk '+emp.absent+'x dari '+emp.totalWD+' hari kerja</div>':'');
        (function(pin,nama,col,t,b,el){
            el.onclick=function(){
                selectedEmpPin=pin; selectedEmpColor=col;
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
    RAW_DATA.filter(function(r){return r.tahun===tahun;}).forEach(function(r){ if(!mWD[r.bulan]) mWD[r.bulan]={}; mWD[r.bulan][r.tanggal]=true; });
    RAW_DATA.filter(function(r){return r.pin===pin&&r.tahun===tahun;}).forEach(function(r){
        if(!mMap[r.bulan]) mMap[r.bulan]={};
        if(!mMap[r.bulan][r.tanggal]) mMap[r.bulan][r.tanggal]={masuk:null,pulang:null,terlambat:false,pulangCepat:false};
        var d=mMap[r.bulan][r.tanggal];
        if(r.isMasuk){if(!d.masuk||r.waktu<d.masuk){d.masuk=r.waktu;d.terlambat=r.terlambat;}}
        else{if(!d.pulang||r.waktu>d.pulang){d.pulang=r.waktu;d.pulangCepat=r.pulangCepat;}}
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
function getDailyData(pin,tahun,bulan){
    var dMap={},allDates={};
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
function buildCustomLegend(activeKeys) {
    var container = document.getElementById('chartLegendCustom'); container.innerHTML = '';
    activeKeys.forEach(function(key) {
        var c = BC[key];
        var item = document.createElement('div'); item.className = 'cl-item';
        var indicator = c.type==='line'
            ? '<div class="cl-line" style="background:'+c.hex+';"></div>'
            : '<div class="cl-dot" style="background:'+c.bg+';border:2px solid '+c.border+';"></div>';
        item.innerHTML = indicator + '<span>'+c.label+'</span>';
        container.appendChild(item);
    });
}
function buildBarChart(pin,nama,color,tahun,bulan){
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
    var datasets=[], activeKeys=[];
    if(barMode==='all'||barMode==='hadir'){
        datasets.push({label:BC.hadir.label,type:'bar',data:data.map(function(m){return m.hadir;}),backgroundColor:BC.hadir.bg,borderColor:BC.hadir.border,borderWidth:1.5,borderRadius:6,borderSkipped:false,order:2});
        activeKeys.push('hadir');
        datasets.push({label:BC.tepat.label,type:'bar',data:data.map(function(m){return m.tepat;}),backgroundColor:BC.tepat.bg,borderColor:BC.tepat.border,borderWidth:1.5,borderRadius:6,borderSkipped:false,order:3});
        activeKeys.push('tepat');
        datasets.push({label:BC.absent.label,type:'bar',data:data.map(function(m){return m.absent;}),backgroundColor:BC.absent.bg,borderColor:BC.absent.border,borderWidth:1.5,borderRadius:6,borderSkipped:false,order:4});
        activeKeys.push('absent');
        datasets.push({label:BC.tren.label,type:'line',data:data.map(function(m){return m.hadir;}),borderColor:BC.tren.border,backgroundColor:BC.tren.bg,borderWidth:2.5,tension:0.4,pointRadius:5,pointBackgroundColor:BC.tren.border,pointBorderColor:'#fff',pointBorderWidth:2,fill:true,order:1});
        activeKeys.push('tren');
    }
    if(barMode==='all'||barMode==='late'){
        datasets.push({label:BC.lambat.label,type:'bar',data:data.map(function(m){return m.lambat;}),backgroundColor:BC.lambat.bg,borderColor:BC.lambat.border,borderWidth:1.5,borderRadius:6,borderSkipped:false,order:5});
        activeKeys.push('lambat');
        datasets.push({label:BC.cepat.label,type:'bar',data:data.map(function(m){return m.cepat;}),backgroundColor:BC.cepat.bg,borderColor:BC.cepat.border,borderWidth:1.5,borderRadius:6,borderSkipped:false,order:6});
        activeKeys.push('cepat');
    }
    buildCustomLegend(activeKeys);
    empBarChart=new Chart(ctx,{
        type:'bar',
        data:{ labels:data.map(function(m){return m.label;}), datasets:datasets },
        options:{
            responsive:true, maintainAspectRatio:false,
            interaction:{mode:'index',intersect:false},
            plugins:{
                legend:{display:false},
                tooltip:{callbacks:{title:function(items){var m=data[items[0].dataIndex];return isDaily?m.tanggal:NAMA_BULAN[m.bulan]+' '+tahun;},label:function(c2){return ' '+c2.dataset.label+': '+c2.raw+' hari';}},padding:12,cornerRadius:12,backgroundColor:'rgba(30,27,75,0.92)',titleColor:'#c7d2fe',bodyColor:'#e0e7ff',borderColor:'rgba(99,102,241,0.4)',borderWidth:1}
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
function renderYearlyStat(h,l,e,t,a,label){
    document.getElementById('yearlyStatStrip').innerHTML=
        (label?'<div style="width:100%;font-size:11px;font-weight:700;color:var(--textmute);text-transform:uppercase;letter-spacing:.06em;padding-bottom:6px">Ringkasan '+label+'</div>':'')+
        ys('ys-hadir','<svg viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="18" rx="2"/><path d="M16 2v4M8 2v4M3 10h18"/></svg>',h,'Total Hadir')+
        ys('ys-tepat','<svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="9"/><path d="M9 12l2 2 4-4"/></svg>',t,'Tepat Waktu')+
        ys('ys-lambat','<svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="9"/><path d="M12 8v4"/><circle cx="12" cy="16" r=".5" fill="currentColor"/></svg>',l,'Terlambat')+
        ys('ys-cepat','<svg viewBox="0 0 24 24"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>',e,'Pulang Cepat')+
        ys('ys-absent','<svg viewBox="0 0 24 24"><circle cx="12" cy="8" r="4"/><path d="M4 20c0-4 3.58-7 8-7s8 3 8 7"/><line x1="17" y1="11" x2="23" y2="11"/></svg>',a,'Tidak Masuk');
}
function ys(cls,icon,val,lbl){
    return '<div class="ys-pill '+cls+'"><div class="ys-icon">'+icon+'</div><div><div class="ys-val">'+val+'</div><div class="ys-lbl">'+lbl+'</div></div></div>';
}
</script>
</body>
</html>