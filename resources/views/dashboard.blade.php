<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kipin — Dashboard Absensi</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;600&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg:#f0f4ff;--card:#ffffff;--border:rgba(99,102,241,0.12);
            --accent:#6366f1;--accent2:#4f46e5;--accent3:#818cf8;--accentbg:#eef2ff;
            --teal:#0d9488;--amber:#d97706;--amberbg:#fef3c7;
            --rose:#e11d48;--rosebg:#ffe4e6;--green:#16a34a;--greenbg:#dcfce7;
            --text:#1e1b4b;--text2:#4338ca;--textmute:#64748b;--textlight:#94a3b8;
            --radius:16px;--radius-sm:10px;
        }
        *{box-sizing:border-box;margin:0;padding:0;}
        body{background:var(--bg);min-height:100vh;font-family:'Plus Jakarta Sans',sans-serif;color:var(--text);overflow-x:hidden;}
        body::before{content:'';position:fixed;inset:0;background-image:linear-gradient(rgba(99,102,241,0.04) 1px,transparent 1px),linear-gradient(90deg,rgba(99,102,241,0.04) 1px,transparent 1px);background-size:36px 36px;pointer-events:none;z-index:0;}
        .glow-blob{position:fixed;border-radius:50%;filter:blur(80px);pointer-events:none;z-index:0;}
        .glow-1{width:600px;height:600px;background:rgba(99,102,241,0.10);top:-160px;left:-120px;}
        .glow-2{width:500px;height:500px;background:rgba(13,148,136,0.07);bottom:0;right:-100px;}
        .glow-3{width:300px;height:300px;background:rgba(225,29,72,0.05);top:40%;left:50%;}

        .loading-overlay{display:none;position:fixed;inset:0;background:rgba(240,244,255,0.92);backdrop-filter:blur(8px);z-index:9999;flex-direction:column;align-items:center;justify-content:center;gap:20px;}
        .loading-overlay.show{display:flex;}
        .spinner{width:48px;height:48px;border:3px solid #e0e7ff;border-top-color:var(--accent);border-radius:50%;animation:spin .7s linear infinite;}
        .loading-text{font-size:14px;color:var(--textmute);font-weight:600;}
        .loading-bar-wrap{width:180px;height:3px;background:#e0e7ff;border-radius:99px;overflow:hidden;}
        .loading-bar{height:100%;width:40%;background:linear-gradient(90deg,var(--accent),var(--teal));border-radius:99px;animation:barSlide 1.4s ease-in-out infinite;}

        .page{display:none;}.page.active{display:block;}
        .wrap{position:relative;z-index:1;max-width:1240px;margin:0 auto;padding:1.5rem 1.5rem 3rem;}

        /* TOPBAR */
        .topbar{display:flex;align-items:center;gap:18px;background:linear-gradient(135deg,#6366f1 0%,#4f46e5 50%,#0d9488 100%);border-radius:20px;padding:1.1rem 1.6rem;margin-bottom:1.5rem;animation:slideDown .5s ease;position:relative;overflow:hidden;box-shadow:0 8px 32px rgba(99,102,241,0.28);}
        .topbar::before{content:'';position:absolute;inset:0;background:radial-gradient(ellipse at 80% 50%,rgba(255,255,255,0.08) 0%,transparent 60%);pointer-events:none;}
        .logo-wrap{width:68px;height:68px;border-radius:18px;background:rgba(255,255,255,0.18);border:2px solid rgba(255,255,255,0.35);display:flex;align-items:center;justify-content:center;flex-shrink:0;overflow:hidden;backdrop-filter:blur(6px);}
        .logo-wrap img{width:54px;height:54px;object-fit:contain;}
        .logo-fallback{width:36px;height:36px;fill:#fff;display:none;}
        .topbar-info{flex:1;}
        .topbar-title{font-size:19px;font-weight:800;color:#fff;letter-spacing:-.03em;line-height:1;}
        .topbar-sub{font-size:12px;color:rgba(255,255,255,0.75);margin-top:5px;display:flex;align-items:center;gap:7px;}
        .live-dot{width:7px;height:7px;border-radius:50%;background:#a3e635;box-shadow:0 0 6px #a3e635;animation:pulse 2s infinite;}
        .clock{font-family:'JetBrains Mono',monospace;font-size:24px;font-weight:600;color:#fff;letter-spacing:-.02em;line-height:1;}
        .date-label{font-size:11px;color:rgba(255,255,255,0.7);margin-top:4px;}

        /* NAV TABS */
        .nav-tabs{display:flex;gap:8px;margin-bottom:1.5rem;background:#fff;border:1px solid var(--border);border-radius:var(--radius);padding:8px;box-shadow:0 2px 12px rgba(99,102,241,0.06);}
        .nav-tab{flex:1;display:flex;align-items:center;justify-content:center;gap:8px;padding:11px 20px;border-radius:10px;font-size:13px;font-weight:700;cursor:pointer;border:none;background:transparent;color:var(--textmute);font-family:inherit;transition:all .2s;}
        .nav-tab svg{width:15px;height:15px;stroke:currentColor;fill:none;stroke-width:2;}
        .nav-tab:hover{color:var(--accent);background:var(--accentbg);}
        .nav-tab.active{background:linear-gradient(135deg,var(--accent),var(--accent2));color:#fff;box-shadow:0 4px 14px rgba(99,102,241,0.3);}

        /* PERIOD BAR */
        .period-filter-bar{display:flex;align-items:center;gap:12px;background:#fff;border:1px solid var(--border);border-radius:var(--radius);padding:14px 20px;margin-bottom:1.5rem;box-shadow:0 2px 12px rgba(99,102,241,0.06);flex-wrap:wrap;animation:fadeUp .4s ease both;}
        .period-filter-icon{width:34px;height:34px;border-radius:10px;background:var(--accentbg);display:flex;align-items:center;justify-content:center;flex-shrink:0;}
        .period-filter-icon svg{width:16px;height:16px;stroke:var(--accent);fill:none;stroke-width:2;}
        .period-filter-label{font-size:12px;font-weight:700;color:var(--textmute);text-transform:uppercase;letter-spacing:.06em;white-space:nowrap;}
        .period-selects{display:flex;gap:8px;align-items:center;flex-wrap:wrap;}
        .period-select{padding:8px 30px 8px 14px;font-size:13px;font-weight:600;border:1.5px solid rgba(99,102,241,0.2);border-radius:9px;background:var(--accentbg);color:var(--text);font-family:inherit;outline:none;cursor:pointer;transition:border-color .2s;appearance:none;background-image:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 24 24' fill='none' stroke='%236366f1' stroke-width='2.5'%3E%3Cpolyline points='6 9 12 15 18 9'/%3E%3C/svg%3E");background-repeat:no-repeat;background-position:right 10px center;}
        .period-select:focus{border-color:var(--accent);box-shadow:0 0 0 3px rgba(99,102,241,0.1);}
        .period-badge{display:inline-flex;align-items:center;gap:6px;padding:6px 14px;border-radius:20px;background:linear-gradient(135deg,var(--accent),var(--teal));color:#fff;font-size:12px;font-weight:700;white-space:nowrap;box-shadow:0 3px 10px rgba(99,102,241,0.3);}
        .period-badge-dot{width:6px;height:6px;border-radius:50%;background:rgba(255,255,255,0.8);animation:pulse 2s infinite;}

        /* STAT */
        .stat-grid{display:grid;grid-template-columns:repeat(4,1fr);gap:14px;margin-bottom:1.5rem;}
        .stat-card{background:var(--card);border:1px solid var(--border);border-radius:var(--radius);padding:1.3rem 1.4rem;position:relative;overflow:hidden;animation:fadeUp .5s ease both;box-shadow:0 2px 12px rgba(99,102,241,0.06);transition:transform .2s,box-shadow .2s;}
        .stat-card:hover{transform:translateY(-3px);box-shadow:0 8px 28px rgba(99,102,241,0.13);}
        .stat-card::before{content:'';position:absolute;top:0;left:0;right:0;height:3px;}
        .c-total::before{background:linear-gradient(90deg,#6366f1,#818cf8);}
        .c-tepat::before{background:linear-gradient(90deg,#16a34a,#4ade80);}
        .c-lambat::before{background:linear-gradient(90deg,#d97706,#fbbf24);}
        .c-cepat::before{background:linear-gradient(90deg,#e11d48,#fb7185);}
        .stat-icon{width:40px;height:40px;border-radius:12px;display:flex;align-items:center;justify-content:center;margin-bottom:14px;}
        .stat-icon svg{width:20px;height:20px;}
        .icon-total{background:var(--accentbg)}.icon-total svg{stroke:var(--accent);}
        .icon-tepat{background:var(--greenbg)}.icon-tepat svg{stroke:var(--green);}
        .icon-lambat{background:var(--amberbg)}.icon-lambat svg{stroke:var(--amber);}
        .icon-cepat{background:var(--rosebg)}.icon-cepat svg{stroke:var(--rose);}
        .stat-label{font-size:11px;font-weight:700;color:var(--textmute);text-transform:uppercase;letter-spacing:.07em;margin-bottom:5px;}
        .stat-val{font-size:34px;font-weight:800;letter-spacing:-.04em;line-height:1;}
        .v-total{color:var(--accent);}.v-tepat{color:var(--green);}.v-lambat{color:var(--amber);}.v-cepat{color:var(--rose);}
        .stat-val.updating{animation:statPop .3s ease;}

        /* CARD */
        .card{background:var(--card);border:1px solid var(--border);border-radius:var(--radius);margin-bottom:1.25rem;overflow:hidden;animation:fadeUp .5s ease both;box-shadow:0 2px 12px rgba(99,102,241,0.06);}
        .card-head{display:flex;align-items:center;gap:10px;padding:.9rem 1.4rem;border-bottom:1px solid var(--border);font-size:13px;font-weight:700;color:var(--text);background:linear-gradient(90deg,#fafbff 0%,#fff 100%);flex-wrap:wrap;}
        .card-head-icon{width:30px;height:30px;border-radius:9px;background:var(--accentbg);display:flex;align-items:center;justify-content:center;flex-shrink:0;}
        .card-head-icon svg{width:15px;height:15px;stroke:var(--accent);fill:none;stroke-width:2;}
        .chip{font-size:10px;padding:3px 10px;border-radius:20px;background:var(--accentbg);color:var(--accent);border:1px solid rgba(99,102,241,0.2);font-weight:700;letter-spacing:.04em;margin-left:auto;}
        .card-body{padding:1.1rem 1.4rem;}
        .alert-ok{background:var(--greenbg);border:1px solid #bbf7d0;color:#15803d;border-radius:var(--radius-sm);padding:10px 14px;font-size:13px;margin-bottom:1rem;font-weight:600;}
        .alert-err{background:var(--rosebg);border:1px solid #fecdd3;color:#be123c;border-radius:var(--radius-sm);padding:10px 14px;font-size:13px;margin-bottom:1rem;font-weight:600;}

        /* UPLOAD */
        .upload-zone{display:flex;gap:12px;align-items:center;flex-wrap:wrap;}
        .upload-input{flex:1;min-width:200px;padding:10px 14px;font-size:13px;border:1.5px dashed rgba(99,102,241,0.3);border-radius:10px;background:var(--accentbg);color:var(--text);font-family:inherit;transition:border-color .2s;}
        .upload-input:hover{border-color:var(--accent);}
        .upload-input::file-selector-button{background:white;color:var(--accent);border:1px solid rgba(99,102,241,0.3);padding:4px 12px;border-radius:6px;cursor:pointer;font-size:12px;font-weight:700;margin-right:10px;font-family:inherit;}
        .file-info{font-size:11px;color:var(--textmute);margin-top:8px;display:none;}
        .btn-upload{padding:10px 24px;font-size:13px;font-weight:700;background:linear-gradient(135deg,var(--accent),var(--accent2));color:#fff;border:none;border-radius:10px;cursor:pointer;font-family:inherit;display:flex;align-items:center;gap:8px;transition:opacity .2s,transform .1s;white-space:nowrap;box-shadow:0 4px 14px rgba(99,102,241,0.35);}
        .btn-upload:hover{opacity:.88;}.btn-upload:active{transform:scale(.97);}
        .btn-upload svg{width:14px;height:14px;stroke:#fff;fill:none;stroke-width:2.5;}

        /* FILTER */
        .filter-row{display:grid;grid-template-columns:1fr 1fr 1fr auto;gap:12px;align-items:end;}
        .f-group{display:flex;flex-direction:column;gap:5px;}
        .f-label{font-size:10px;font-weight:700;color:var(--textmute);text-transform:uppercase;letter-spacing:.07em;}
        .f-input{padding:9px 12px;font-size:13px;border:1.5px solid rgba(99,102,241,0.15);border-radius:var(--radius-sm);background:#fafbff;color:var(--text);font-family:inherit;outline:none;transition:border-color .2s,box-shadow .2s;}
        .f-input:focus{border-color:var(--accent);box-shadow:0 0 0 3px rgba(99,102,241,0.1);}
        .btn-reset{padding:9px 16px;font-size:12px;font-weight:600;background:#f1f5f9;color:var(--textmute);border:1.5px solid #e2e8f0;border-radius:var(--radius-sm);cursor:pointer;font-family:inherit;white-space:nowrap;transition:all .2s;}
        .btn-reset:hover{border-color:var(--accent);color:var(--accent);background:var(--accentbg);}

        /* RESULT BAR */
        .result-bar{padding:8px 1.4rem;font-size:12px;color:var(--textmute);background:#fafbff;border-bottom:1px solid var(--border);display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:8px;}
        .result-bar strong{color:var(--accent);font-weight:700;}

        /* TABLE */
        .tbl-wrap{overflow-x:auto;}
        table{width:100%;border-collapse:collapse;font-size:13px;}
        thead tr{background:#f8faff;}
        th{padding:11px 16px;text-align:left;font-size:10px;font-weight:700;color:var(--textmute);border-bottom:1.5px solid rgba(99,102,241,0.1);text-transform:uppercase;letter-spacing:.07em;white-space:nowrap;}
        td{padding:12px 16px;border-bottom:1px solid rgba(99,102,241,0.06);vertical-align:middle;color:var(--text);}
        tbody tr:last-child td{border-bottom:none;}
        tbody tr:hover td{background:#f5f7ff;transition:background .15s;}
        .time-badge{display:inline-flex;align-items:center;gap:6px;padding:5px 11px;border-radius:8px;font-family:'JetBrains Mono',monospace;font-size:12px;font-weight:600;white-space:nowrap;}
        .time-badge svg{width:12px;height:12px;flex-shrink:0;fill:none;stroke-width:2;}
        .time-ok  {background:#dcfce7;color:#15803d;border:1px solid #bbf7d0;}.time-ok svg{stroke:#15803d;}
        .time-late{background:#fef3c7;color:#92400e;border:1px solid #fde68a;}.time-late svg{stroke:#d97706;}
        .time-early{background:#ffe4e6;color:#be123c;border:1px solid #fecdd3;}.time-early svg{stroke:#e11d48;}
        .time-none{font-size:11px;color:var(--textlight);font-style:italic;}
        .avatar{width:34px;height:34px;border-radius:50%;background:linear-gradient(135deg,var(--accent),var(--teal));color:#fff;display:inline-flex;align-items:center;justify-content:center;font-size:11px;font-weight:800;margin-right:10px;flex-shrink:0;box-shadow:0 2px 8px rgba(99,102,241,0.25);}
        .nama-cell{display:flex;align-items:center;}
        .pin{font-family:'JetBrains Mono',monospace;font-size:11px;font-weight:600;background:var(--accentbg);color:var(--accent);padding:3px 9px;border-radius:6px;border:1px solid rgba(99,102,241,0.18);}
        .karyawan-nav-strip{display:flex;align-items:center;gap:8px;padding:10px 1.4rem;background:#fafbff;border-bottom:1px solid var(--border);overflow-x:auto;}
        .karyawan-nav-label{font-size:11px;font-weight:700;color:var(--textmute);text-transform:uppercase;letter-spacing:.06em;white-space:nowrap;flex-shrink:0;}
        .karyawan-nav-btn{padding:5px 12px;border-radius:20px;font-size:11px;font-weight:700;border:1.5px solid rgba(99,102,241,0.15);background:#fff;color:var(--textmute);cursor:pointer;font-family:inherit;white-space:nowrap;flex-shrink:0;transition:all .15s;}
        .karyawan-nav-btn:hover{border-color:var(--accent);color:var(--accent);background:var(--accentbg);}
        .karyawan-nav-btn.active{background:var(--accent);color:#fff;border-color:var(--accent);box-shadow:0 2px 8px rgba(99,102,241,0.3);}

        /* DONUT */
        .donut-wrap{display:flex;flex-wrap:wrap;gap:28px;align-items:center;justify-content:center;padding:1.4rem;}
        .donut-canvas-wrap{position:relative;width:210px;height:210px;flex-shrink:0;}
        .donut-center{position:absolute;top:50%;left:50%;transform:translate(-50%,-50%);text-align:center;pointer-events:none;}
        .donut-center-num{font-size:30px;font-weight:800;color:var(--text);letter-spacing:-.04em;line-height:1;transition:all .3s;}
        .donut-center-lbl{font-size:11px;color:var(--textmute);margin-top:3px;font-weight:600;}
        .donut-legends{display:flex;flex-direction:column;gap:10px;flex:1;min-width:200px;}
        .donut-legend-item{display:flex;align-items:center;gap:12px;padding:10px 14px;border-radius:12px;border:1px solid rgba(99,102,241,0.08);cursor:pointer;transition:background .15s,transform .15s;}
        .donut-legend-item:hover{transform:translateX(3px);}
        .donut-legend-dot{width:12px;height:12px;border-radius:4px;flex-shrink:0;}
        .donut-legend-label{font-size:13px;font-weight:600;color:var(--text);flex:1;}
        .donut-legend-val{font-size:13px;font-weight:700;}
        .donut-legend-pct{font-size:11px;color:var(--textmute);font-weight:500;margin-left:3px;}
        .btn-print{display:flex;align-items:center;gap:8px;padding:8px 20px;font-size:12px;font-weight:700;background:linear-gradient(135deg,#6366f1,#4f46e5);color:#fff;border:none;border-radius:10px;cursor:pointer;font-family:inherit;box-shadow:0 4px 14px rgba(99,102,241,0.30);transition:opacity .2s;white-space:nowrap;margin-left:8px;}
        .btn-print:hover{opacity:.88;}
        .btn-print svg{width:14px;height:14px;stroke:#fff;fill:none;stroke-width:2;}
        .pg-bar{display:flex;align-items:center;justify-content:space-between;padding:12px 1.4rem;flex-wrap:wrap;gap:10px;border-top:1px solid rgba(99,102,241,0.08);background:#fafbff;}
        .pg-info{font-size:12px;color:var(--textmute);font-weight:500;}
        .pg-btns{display:flex;gap:4px;align-items:center;flex-wrap:wrap;}
        .pg-btn{min-width:32px;height:32px;padding:0 8px;border:1.5px solid rgba(99,102,241,0.15);background:#fff;color:var(--text);border-radius:8px;font-size:12px;font-weight:600;cursor:pointer;display:flex;align-items:center;justify-content:center;font-family:inherit;transition:all .15s;}
        .pg-btn:hover:not(:disabled){border-color:var(--accent);color:var(--accent);background:var(--accentbg);}
        .pg-btn:disabled{opacity:.35;cursor:not-allowed;}
        .pg-btn.pg-active{background:var(--accent);color:#fff;border-color:var(--accent);font-weight:700;box-shadow:0 2px 8px rgba(99,102,241,0.3);}
        .pg-ellipsis{font-size:13px;padding:0 4px;color:var(--textlight);line-height:32px;}

        /* ══════════════════════════════════════
           PERFORMA PAGE — EMPLOYEE SELECTOR
        ══════════════════════════════════════ */
        .emp-selector-wrap{padding:1.2rem 1.4rem 1.4rem;}
        .emp-selector-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(210px,1fr));gap:12px;}
        .emp-card{background:#fafbff;border:2px solid rgba(99,102,241,0.12);border-radius:14px;padding:14px;cursor:pointer;transition:all .22s;position:relative;overflow:hidden;}
        .emp-card::before{content:'';position:absolute;top:0;left:0;right:0;height:3px;background:transparent;transition:background .22s;}
        .emp-card:hover{border-color:rgba(99,102,241,0.35);background:#fff;transform:translateY(-2px);box-shadow:0 8px 24px rgba(99,102,241,0.13);}
        .emp-card.selected{border-color:var(--accent);background:#fff;box-shadow:0 8px 28px rgba(99,102,241,0.18);}
        .emp-card.selected::before{background:linear-gradient(90deg,var(--accent),var(--teal));}
        /* Dim card if zero data in selected period */
        .emp-card.no-data{opacity:0.55;filter:grayscale(0.3);}
        .emp-card.no-data:hover{opacity:0.75;filter:none;}
        .emp-card-badge{position:absolute;top:10px;right:10px;width:20px;height:20px;border-radius:50%;background:var(--accent);display:none;align-items:center;justify-content:center;box-shadow:0 2px 8px rgba(99,102,241,0.4);}
        .emp-card.selected .emp-card-badge{display:flex;}
        .emp-card-badge svg{width:10px;height:10px;stroke:#fff;fill:none;stroke-width:3;}
        .emp-card-header{display:flex;align-items:center;gap:10px;margin-bottom:12px;}
        .emp-card-avatar{width:42px;height:42px;border-radius:12px;color:#fff;display:flex;align-items:center;justify-content:center;font-size:14px;font-weight:800;flex-shrink:0;box-shadow:0 4px 12px rgba(0,0,0,0.15);}
        .emp-card-name{font-size:13px;font-weight:800;color:var(--text);line-height:1.3;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;max-width:120px;}
        .emp-card-pin{font-family:'JetBrains Mono',monospace;font-size:10px;color:var(--textmute);margin-top:2px;}
        .emp-card-stats{display:grid;grid-template-columns:repeat(3,1fr);gap:6px;}
        .emp-card-stat{text-align:center;padding:7px 4px;border-radius:9px;background:rgba(255,255,255,0.8);border:1px solid rgba(0,0,0,0.04);}
        .emp-card-stat-num{font-size:17px;font-weight:800;line-height:1;}
        .emp-card-stat-lbl{font-size:8px;font-weight:700;color:var(--textmute);text-transform:uppercase;letter-spacing:.05em;margin-top:2px;}
        /* No-data badge on card */
        .emp-nodata-badge{display:none;position:absolute;bottom:10px;right:10px;font-size:9px;font-weight:700;background:#f1f5f9;color:var(--textmute);border:1px solid #e2e8f0;padding:2px 7px;border-radius:20px;}
        .emp-card.no-data .emp-nodata-badge{display:block;}

        /* ══════════════════════════════════════
           PERFORMA PAGE — BAR CHART
        ══════════════════════════════════════ */
        .chart-panel{display:none;animation:fadeUp .35s ease;}
        .chart-panel.show{display:block;}

        .bar-chart-topbar{display:flex;align-items:center;gap:12px;padding:1rem 1.4rem;border-bottom:1px solid var(--border);flex-wrap:wrap;background:linear-gradient(90deg,#fafbff,#fff);}
        .bar-chart-emp-info{display:flex;align-items:center;gap:12px;flex:1;}
        .bar-chart-emp-avatar{width:44px;height:44px;border-radius:12px;color:#fff;display:flex;align-items:center;justify-content:center;font-size:15px;font-weight:800;flex-shrink:0;box-shadow:0 4px 14px rgba(0,0,0,0.15);}
        .bar-chart-emp-name{font-size:16px;font-weight:800;color:var(--text);}
        .bar-chart-emp-pin{font-family:'JetBrains Mono',monospace;font-size:11px;color:var(--textmute);margin-top:3px;}
        .bar-mode-btns{display:flex;gap:6px;flex-shrink:0;}
        .bar-mode-btn{padding:7px 16px;border-radius:9px;font-size:11px;font-weight:700;border:1.5px solid rgba(99,102,241,0.2);background:#fff;color:var(--textmute);cursor:pointer;font-family:inherit;transition:all .15s;white-space:nowrap;}
        .bar-mode-btn:hover{border-color:var(--accent);color:var(--accent);background:var(--accentbg);}
        .bar-mode-btn.active{background:linear-gradient(135deg,var(--accent),var(--accent2));color:#fff;border-color:var(--accent);box-shadow:0 3px 10px rgba(99,102,241,0.3);}

        .bar-chart-body{padding:1.4rem 1.4rem 1rem;}
        .bar-chart-canvas-wrap{position:relative;height:400px;}

        /* YEARLY STAT STRIP */
        .yearly-stat-strip{display:flex;gap:10px;padding:12px 1.4rem 16px;flex-wrap:wrap;border-top:1px solid rgba(99,102,241,0.08);background:#fafbff;}
        .ys-pill{display:flex;align-items:center;gap:10px;padding:10px 16px;border-radius:14px;flex:1;min-width:130px;}
        .ys-hadir{background:#ecfdf5;border:1px solid #bbf7d0;}
        .ys-lambat{background:#fef3c7;border:1px solid #fde68a;}
        .ys-cepat{background:#ffe4e6;border:1px solid #fecdd3;}
        .ys-tepat{background:#eef2ff;border:1px solid #c7d2fe;}
        .ys-icon{width:34px;height:34px;border-radius:10px;display:flex;align-items:center;justify-content:center;flex-shrink:0;}
        .ys-hadir .ys-icon{background:#d1fae5;}.ys-hadir .ys-icon svg{stroke:#16a34a;}
        .ys-lambat .ys-icon{background:#fef9c3;}.ys-lambat .ys-icon svg{stroke:#d97706;}
        .ys-cepat  .ys-icon{background:#ffe4e6;}.ys-cepat  .ys-icon svg{stroke:#e11d48;}
        .ys-tepat  .ys-icon{background:#e0e7ff;}.ys-tepat  .ys-icon svg{stroke:#6366f1;}
        .ys-icon svg{width:16px;height:16px;fill:none;stroke-width:2;}
        .ys-body{display:flex;flex-direction:column;}
        .ys-val{font-size:24px;font-weight:800;line-height:1;}
        .ys-hadir .ys-val{color:#16a34a;}
        .ys-lambat .ys-val{color:#d97706;}
        .ys-cepat  .ys-val{color:#e11d48;}
        .ys-tepat  .ys-val{color:#6366f1;}
        .ys-lbl{font-size:11px;color:var(--textmute);font-weight:600;margin-top:2px;}

        /* CHART SELECT HINT */
        .chart-select-hint{display:flex;flex-direction:column;align-items:center;justify-content:center;padding:3rem 2rem;gap:12px;}
        .chart-select-hint-icon{width:64px;height:64px;border-radius:20px;background:linear-gradient(135deg,#eef2ff,#e0e7ff);display:flex;align-items:center;justify-content:center;}
        .chart-select-hint-icon svg{width:30px;height:30px;stroke:var(--accent);fill:none;stroke-width:1.5;}
        .chart-select-hint-text{font-size:15px;font-weight:700;color:var(--textmute);}
        .chart-select-hint-sub{font-size:12px;color:var(--textlight);}

        .empty{text-align:center;padding:3.5rem 1rem;color:var(--textlight);}
        .empty-icon{font-size:40px;margin-bottom:14px;}
        .empty-text{font-size:14px;font-weight:700;color:var(--textmute);}
        .empty-sub{font-size:12px;margin-top:6px;}
        .footer{text-align:center;padding:1.5rem 0 .5rem;font-size:12px;color:var(--textmute);}
        .footer span{color:var(--accent);font-weight:700;}

        @media print{
            .glow-blob,.loading-overlay,.nav-tabs{display:none!important;}
            body{background:#fff!important;}body::before{display:none!important;}
            .wrap{max-width:100%;padding:0 12px;}
            .period-filter-bar .period-selects,.period-filter-bar button,.card:has(.upload-input),.filter-row,.pg-bar,.result-bar,.karyawan-nav-strip,.footer,.btn-print,.btn-upload,.btn-reset{display:none!important;}
            .card{box-shadow:none!important;border:1px solid #ddd!important;page-break-inside:avoid;}
            .topbar{background:#6366f1!important;-webkit-print-color-adjust:exact;print-color-adjust:exact;box-shadow:none!important;border-radius:12px!important;}
            @page{margin:1.5cm;size:A4;}
        }

        @keyframes slideDown{from{opacity:0;transform:translateY(-14px);}to{opacity:1;transform:translateY(0);}}
        @keyframes fadeUp{from{opacity:0;transform:translateY(10px);}to{opacity:1;transform:translateY(0);}}
        @keyframes pulse{0%,100%{opacity:1;transform:scale(1);}50%{opacity:.4;transform:scale(.5);}}
        @keyframes spin{to{transform:rotate(360deg);}}
        @keyframes barSlide{0%{transform:translateX(-200%);}100%{transform:translateX(400%);}}
        @keyframes rowIn{from{opacity:0;transform:translateY(6px);}to{opacity:1;transform:translateY(0);}}
        @keyframes statPop{0%{transform:scale(.9);opacity:.5;}60%{transform:scale(1.05);}100%{transform:scale(1);opacity:1;}}

        @media(max-width:900px){.stat-grid{grid-template-columns:repeat(2,1fr);}
        .emp-selector-grid{grid-template-columns:repeat(auto-fill,minmax(160px,1fr));}}
        @media(max-width:600px){.filter-row{grid-template-columns:1fr;}.topbar{flex-wrap:wrap;}.wrap{padding:1rem 1rem 2rem;}.donut-wrap{flex-direction:column;}.bar-chart-canvas-wrap{height:280px;}.bar-mode-btns{width:100%;}.bar-mode-btn{flex:1;text-align:center;}}
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
            <img src="{{ asset('images/kipin.png') }}" alt="Kipin Logo" onerror="this.style.display='none';this.nextElementSibling.style.display='block'">
            <svg class="logo-fallback" viewBox="0 0 24 24" fill="none" stroke-width="2"><path d="M17 12h-5v5h5v-5zM16 1v2H8V1H6v2H5c-1.11 0-1.99.9-1.99 2L3 19c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2h-1V1h-2zm3 18H5V8h14v11z" fill="currentColor"/></svg>
        </div>
        <div class="topbar-info">
            <div class="topbar-title">Dashboard Monitoring Absensi</div>
            <div class="topbar-sub"><span class="live-dot"></span>Kipin &mdash; Data Real-time</div>
        </div>
        <div style="display:flex;align-items:center;gap:14px;flex-shrink:0;">
            <div><div class="clock" id="liveClock"></div><div class="date-label" id="liveDate"></div></div>
            <form action="{{ route('logout') }}" method="POST">@csrf
                <button type="submit" style="display:flex;align-items:center;gap:7px;padding:8px 16px;background:rgba(255,255,255,0.15);border:1.5px solid rgba(255,255,255,0.3);border-radius:10px;color:#fff;font-size:13px;font-weight:700;font-family:inherit;cursor:pointer;backdrop-filter:blur(6px);transition:background .2s" onmouseover="this.style.background='rgba(255,255,255,0.25)'" onmouseout="this.style.background='rgba(255,255,255,0.15)'">
                    <svg width="15" height="15" fill="none" stroke="#fff" stroke-width="2.2" viewBox="0 0 24 24"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>Logout
                </button>
            </form>
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
            <span class="period-filter-label">Periode</span>
            <div class="period-selects">
                <select class="period-select" id="filterBulan" onchange="applyPeriodFilter()">
                    <option value="1">Januari</option><option value="2">Februari</option><option value="3">Maret</option>
                    <option value="4">April</option><option value="5">Mei</option><option value="6">Juni</option>
                    <option value="7">Juli</option><option value="8">Agustus</option><option value="9">September</option>
                    <option value="10">Oktober</option><option value="11">November</option><option value="12">Desember</option>
                </select>
                <select class="period-select" id="filterTahun" onchange="applyPeriodFilter()"></select>
            </div>
            <div class="period-badge"><span class="period-badge-dot"></span><span id="periodBadgeText">—</span></div>
        </div>

        <div class="stat-grid">
            <div class="stat-card c-total" style="animation-delay:.05s">
                <div class="stat-icon icon-total"><svg fill="none" stroke-width="1.8" viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="18" rx="2"/><path d="M16 2v4M8 2v4M3 10h18"/></svg></div>
                <div class="stat-label">Total Absensi</div><div class="stat-val v-total" id="statTotal">0</div>
            </div>
            <div class="stat-card c-tepat" style="animation-delay:.10s">
                <div class="stat-icon icon-tepat"><svg fill="none" stroke-width="1.8" viewBox="0 0 24 24"><circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 3"/></svg></div>
                <div class="stat-label">Tepat Waktu</div><div class="stat-val v-tepat" id="statTepat">0</div>
            </div>
            <div class="stat-card c-lambat" style="animation-delay:.15s">
                <div class="stat-icon icon-lambat"><svg fill="none" stroke-width="1.8" viewBox="0 0 24 24"><circle cx="12" cy="12" r="9"/><path d="M12 8v4"/><circle cx="12" cy="16" r=".5" fill="currentColor"/></svg></div>
                <div class="stat-label">Terlambat</div><div class="stat-val v-lambat" id="statLambat">0</div>
            </div>
            <div class="stat-card c-cepat" style="animation-delay:.20s">
                <div class="stat-icon icon-cepat"><svg fill="none" stroke-width="1.8" viewBox="0 0 24 24"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg></div>
                <div class="stat-label">Pulang Cepat</div><div class="stat-val v-cepat" id="statCepat">0</div>
            </div>
        </div>

        @if(session('success'))<div class="alert-ok">&#10003; {{ session('success') }}</div>@endif
        @if(session('error'))<div class="alert-err">&#10005; {{ session('error') }}</div>@endif

        <div class="card">
            <div class="card-head">
                <div class="card-head-icon"><svg viewBox="0 0 24 24"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg></div>
                Upload Data Revo <span class="chip">CSV</span>
            </div>
            <div class="card-body">
                <form action="{{ route('import.presensi') }}" method="POST" enctype="multipart/form-data" onsubmit="document.getElementById('loadingOverlay').classList.add('show')">
                    @csrf
                    <div class="upload-zone">
                        <input type="file" class="upload-input" name="file_csv" required onchange="showFileName(this)">
                        <button class="btn-upload" type="submit"><svg viewBox="0 0 24 24"><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/><path d="M5 19h14"/></svg>Upload &amp; Proses</button>
                    </div>
                    <div class="file-info" id="fileInfo"></div>
                </form>
            </div>
        </div>

        <div class="card" style="animation-delay:.25s">
            <div class="card-head">
                <div class="card-head-icon"><svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="9"/><path d="M12 3a9 9 0 0 1 9 9h-9z"/></svg></div>
                Grafik Ringkasan — <span id="chartPeriodLabel" style="color:var(--accent);margin-left:4px">—</span>
                <span class="chip">Donut</span>
                <button class="btn-print" onclick="window.print()"><svg viewBox="0 0 24 24"><polyline points="6 9 6 2 18 2 18 9"/><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/><rect x="6" y="14" width="12" height="8"/></svg>Print / PDF</button>
            </div>
            <div class="donut-wrap">
                <div class="donut-canvas-wrap"><canvas id="donutChart"></canvas><div class="donut-center"><div class="donut-center-num" id="donutCenterNum">0</div><div class="donut-center-lbl" id="donutCenterLbl">total</div></div></div>
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
                            <option value="">Semua</option><option value="tepat">Tepat Waktu</option><option value="terlambat">Terlambat</option><option value="cepat">Pulang Cepat</option>
                        </select>
                    </div>
                    <div><button class="btn-reset" onclick="resetFilter()">&#8635; Reset</button></div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-head">
                <div class="card-head-icon"><svg viewBox="0 0 24 24"><rect x="3" y="3" width="18" height="18" rx="2"/><path d="M3 9h18M9 21V9"/></svg></div>
                Data Presensi — <span id="tablePeriodLabel" style="color:var(--accent);margin-left:4px">—</span>
                <span class="chip" id="countChip">0 data</span>
            </div>
            <div class="karyawan-nav-strip" id="karyawanNavStrip"><span class="karyawan-nav-label">Karyawan:</span></div>
            <div class="result-bar"><span>Karyawan ke-<strong id="karyawanIdx">–</strong>: <strong id="karyawanName">–</strong> &nbsp;|&nbsp; <strong id="shownTotal">0</strong> hari absensi</span></div>
            <div class="tbl-wrap">
                <table>
                    <thead><tr><th>No</th><th>PIN</th><th>Nama Karyawan</th><th>Tanggal</th><th>Jam Masuk</th><th>Jam Pulang</th></tr></thead>
                    <tbody id="tableBody"></tbody>
                </table>
            </div>
            <div class="pg-bar"><div class="pg-info" id="pgInfo">Karyawan 1 dari 1</div><div class="pg-btns" id="pgBtns"></div></div>
        </div>

    </div><!-- /pageDashboard -->

    <!-- ═══ PAGE PERFORMA ═══ -->
    <div class="page" id="pagePerforma">

        <!-- Year + Month Filter -->
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
            <div style="margin-left:auto;font-size:12px;color:var(--textmute);font-weight:600;" id="perfPeriodHint">
                &#x1F4C5; Menampilkan data seluruh bulan dalam tahun terpilih
            </div>
        </div>

        <!-- Employee Selector -->
        <div class="card" style="animation-delay:.05s">
            <div class="card-head">
                <div class="card-head-icon"><svg viewBox="0 0 24 24"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg></div>
                Pilih Karyawan
                <span class="chip" id="empCountChip">0 karyawan</span>
                <span style="font-size:11px;color:var(--textmute);font-weight:500;margin-left:4px">← Klik untuk lihat chart</span>
            </div>
            <div class="emp-selector-wrap">
                <div class="emp-selector-grid" id="empSelectorGrid"></div>
            </div>
        </div>

        <!-- Chart Panel (hidden until employee selected) -->
        <div class="card chart-panel" id="empChartCard" style="animation-delay:.1s">

            <!-- Chart Topbar -->
            <div class="bar-chart-topbar">
                <div class="bar-chart-emp-info">
                    <div class="bar-chart-emp-avatar" id="bcAvatar">??</div>
                    <div>
                        <div class="bar-chart-emp-name" id="bcName">—</div>
                        <div class="bar-chart-emp-pin" id="bcPin">—</div>
                    </div>
                </div>
                <div class="bar-mode-btns">
                    <button class="bar-mode-btn active" id="btnModeAll"    onclick="setBarMode('all')">Semua Data</button>
                    <button class="bar-mode-btn"        id="btnModeHadir"  onclick="setBarMode('hadir')">Kehadiran</button>
                    <button class="bar-mode-btn"        id="btnModeLate"   onclick="setBarMode('late')">Keterlambatan</button>
                </div>
            </div>

            <!-- Chart Canvas -->
            <div class="bar-chart-body">
                <div class="bar-chart-canvas-wrap">
                    <canvas id="empBarChart"></canvas>
                </div>
            </div>

            <!-- Yearly Total Stats -->
            <div class="yearly-stat-strip" id="yearlyStatStrip"></div>

        </div>

        <!-- Hint when no employee selected -->
        <div class="card" id="empChartHint" style="animation-delay:.1s">
            <div class="chart-select-hint">
                <div class="chart-select-hint-icon">
                    <svg viewBox="0 0 24 24"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/></svg>
                </div>
                <div class="chart-select-hint-text">Pilih karyawan di atas untuk melihat chart performa</div>
                <div class="chart-select-hint-sub">Chart akan menampilkan data kehadiran per bulan sepanjang tahun yang dipilih</div>
            </div>
        </div>

    </div><!-- /pagePerforma -->

    <div class="footer">&copy; {{ date('Y') }} <span>Kipin</span> &mdash; Sistem Monitoring Absensi &middot; Sakera</div>
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
@endphp
{
    "pin":          "{{ $d->karyawan->id_mesin }}",
    "nama":         "{{ addslashes($d->karyawan->nama) }}",
    "tanggal":      "{{ $waktu->format('Y-m-d') }}",
    "tanggalFmt":   "{{ $waktu->translatedFormat('d M Y') }}",
    "tanggalShort": "{{ $waktu->format('d') }}",
    "waktu":        "{{ $waktu->format('H:i:s') }}",
    "bulan":        {{ (int)$waktu->format('n') }},
    "tahun":        {{ (int)$waktu->format('Y') }},
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
/* ════ CLOCK ════ */
function tick(){var d=new Date();document.getElementById('liveClock').textContent=d.toLocaleTimeString('id-ID',{hour:'2-digit',minute:'2-digit',second:'2-digit'});document.getElementById('liveDate').textContent=d.toLocaleDateString('id-ID',{weekday:'long',day:'numeric',month:'long',year:'numeric'});}
tick();setInterval(tick,1000);
function showFileName(input){var el=document.getElementById('fileInfo');if(input.files&&input.files[0]){var f=input.files[0];el.style.display='block';el.innerHTML='&#128196; <strong style="color:var(--accent)">'+f.name+'</strong> ('+Math.round(f.size/1024)+' KB)';}else{el.style.display='none';}}

var NAMA_BULAN=['','Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
var NAMA_BULAN_SHORT=['','Jan','Feb','Mar','Apr','Mei','Jun','Jul','Ags','Sep','Okt','Nov','Des'];

/* PALETTE 20 warna */
var PALETTE=[
    '#0077b6','#0096c7','#00b4d8','#48cae4','#90e0ef',
    '#6366f1','#0d9488','#d97706','#e11d48','#16a34a',
    '#7c3aed','#0891b2','#dc2626','#65a30d','#9333ea',
    '#2563eb','#ea580c','#059669','#be185d','#ca8a04'
];

/* ════ INIT ════ */
(function(){
    var now=new Date(),nowM=now.getMonth()+1,nowY=now.getFullYear();
    ['filterTahun','filterTahunPerf'].forEach(function(id){
        var sel=document.getElementById(id);
        if(!sel) return;
        for(var y=nowY;y>=nowY-5;y--){var o=document.createElement('option');o.value=y;o.textContent=y;if(y===nowY)o.selected=true;sel.appendChild(o);}
    });
    document.getElementById('filterBulan').value=nowM;
    /* Set performa bulan filter to current month too */
    document.getElementById('filterBulanPerf').value=nowM;
})();

/* RAW DATA */
var RAW_DATA=[];
try{RAW_DATA=JSON.parse(document.getElementById('rawDataScript').textContent);}catch(e){}

/* ════ PAGE SWITCH ════ */
var CURRENT_PAGE='dashboard';
function switchPage(p){
    CURRENT_PAGE=p;
    document.getElementById('pageDashboard').classList.toggle('active',p==='dashboard');
    document.getElementById('pagePerforma').classList.toggle('active',p==='performa');
    document.getElementById('tabDashboard').classList.toggle('active',p==='dashboard');
    document.getElementById('tabPerforma').classList.toggle('active',p==='performa');
    if(p==='performa') renderPerfPage();
}

/* ════ DONUT (Dashboard) ════ */
var donutColors=['#16a34a','#d97706','#e11d48','#6366f1'];
var donutLabels=['Tepat Waktu','Terlambat','Pulang Cepat','Tepat Pulang'];
var donutChart=new Chart(document.getElementById('donutChart'),{
    type:'doughnut',
    data:{labels:donutLabels,datasets:[{data:[0,0,0,0],backgroundColor:donutColors,borderColor:'#ffffff',borderWidth:4,hoverOffset:10}]},
    options:{responsive:true,maintainAspectRatio:false,cutout:'70%',animation:{animateScale:true,duration:700,easing:'easeInOutQuart'},
        plugins:{legend:{display:false},tooltip:{callbacks:{label:function(ctx){var t=ctx.dataset.data.reduce(function(a,b){return a+b;},0);var p=t>0?Math.round(ctx.raw/t*100):0;return '  '+ctx.label+': '+ctx.raw+' ('+p+'%)';}},padding:10,cornerRadius:10}},
        onHover:function(e,els){var data=donutChart.data.datasets[0].data,total=data.reduce(function(a,b){return a+b;},0);var nE=document.getElementById('donutCenterNum'),lE=document.getElementById('donutCenterLbl');if(els.length){var i=els[0].index;nE.textContent=data[i];lE.textContent=donutLabels[i];nE.style.color=donutColors[i];}else{nE.textContent=total;lE.textContent='total';nE.style.color='var(--text)';}}
    }
});
function highlightDonut(i){var d=donutChart.data.datasets[0].data;donutChart.setActiveElements([{datasetIndex:0,index:i}]);donutChart.tooltip.setActiveElements([{datasetIndex:0,index:i}],{x:0,y:0});donutChart.update();document.getElementById('donutCenterNum').textContent=d[i];document.getElementById('donutCenterNum').style.color=donutColors[i];document.getElementById('donutCenterLbl').textContent=donutLabels[i];}
function resetDonut(){var d=donutChart.data.datasets[0].data,t=d.reduce(function(a,b){return a+b;},0);donutChart.setActiveElements([]);donutChart.tooltip.setActiveElements([],{x:0,y:0});donutChart.update();document.getElementById('donutCenterNum').textContent=t;document.getElementById('donutCenterNum').style.color='var(--text)';document.getElementById('donutCenterLbl').textContent='total';}
function updateLegend(vals,total){
    var c=document.getElementById('donutLegends'),bgs=['#dcfce7','#fef3c7','#ffe4e6','#eef2ff'],vc=['#15803d','#92400e','#be123c','#4338ca'];
    c.innerHTML='';
    donutLabels.forEach(function(lbl,i){
        var pct=total>0?Math.round(vals[i]/total*100):0;
        var div=document.createElement('div');div.className='donut-legend-item';div.style.background=bgs[i];div.style.borderColor=donutColors[i]+'22';
        div.setAttribute('onmouseover','highlightDonut('+i+')');div.setAttribute('onmouseout','resetDonut()');
        div.innerHTML='<span class="donut-legend-dot" style="background:'+donutColors[i]+'"></span><span class="donut-legend-label">'+lbl+'</span><span class="donut-legend-val" style="color:'+vc[i]+'">'+vals[i]+(total>0?'<span class="donut-legend-pct">('+pct+'%)</span>':'')+'</span>';
        c.appendChild(div);
    });
}

/* ════ GROUP DATA (for dashboard table) ════ */
function groupByKaryawan(bulan,tahun,withFilters){
    var name=withFilters&&document.getElementById('searchName')?document.getElementById('searchName').value.toLowerCase().trim():'';
    var date=withFilters&&document.getElementById('filterDate')?document.getElementById('filterDate').value:'';
    var ket=withFilters&&document.getElementById('filterKet')?document.getElementById('filterKet').value:'';
    var filtered=RAW_DATA.filter(function(r){
        if(r.bulan!==bulan||r.tahun!==tahun) return false;
        if(name&&r.nama.toLowerCase().indexOf(name)===-1) return false;
        if(date&&r.tanggal!==date) return false;
        return true;
    });
    var kMap={},kOrder=[];
    filtered.forEach(function(r){
        var key=r.pin;
        if(!kMap[key]){kMap[key]={pin:r.pin,nama:r.nama,days:{}};kOrder.push(key);}
        var kObj=kMap[key];
        if(!kObj.days[r.tanggal]){kObj.days[r.tanggal]={tanggal:r.tanggal,tanggalFmt:r.tanggalFmt,tanggalShort:r.tanggalShort,masuk:null,pulang:null,terlambat:false,pulangCepat:false};}
        var dObj=kObj.days[r.tanggal];
        if(r.isMasuk){if(!dObj.masuk||r.waktu<dObj.masuk){dObj.masuk=r.waktu;dObj.terlambat=r.terlambat;}}
        else{if(!dObj.pulang||r.waktu>dObj.pulang){dObj.pulang=r.waktu;dObj.pulangCepat=r.pulangCepat;}}
    });
    var result=kOrder.map(function(key){var kObj=kMap[key];var days=Object.values(kObj.days).sort(function(a,b){return a.tanggal<b.tanggal?-1:1;});return{pin:kObj.pin,nama:kObj.nama,days:days};});
    if(ket&&withFilters){
        result=result.map(function(k){
            var fd=k.days.filter(function(d){if(ket==='terlambat')return d.terlambat;if(ket==='cepat')return d.pulangCepat;if(ket==='tepat')return !d.terlambat&&d.masuk;return true;});
            return{pin:k.pin,nama:k.nama,days:fd};
        }).filter(function(k){return k.days.length>0;});
    }
    return result;
}

/* ════════════════════════════════════════════
   PERFORMA PAGE — DATA FUNCTIONS
════════════════════════════════════════════ */

/**
 * getEmployeesForPeriod — FIXED
 * Sekarang menggunakan bulan filter juga.
 * bulan=0 → semua bulan dalam tahun; bulan>0 → bulan spesifik.
 * Semua karyawan yang punya data di TAHUN itu tetap ditampilkan,
 * tapi stats-nya dihitung berdasarkan bulan yang dipilih.
 * Karyawan tanpa data di bulan terpilih → totalDays/terlambat/pulangCepat = 0.
 */
function getEmployeesForPeriod(tahun, bulan){
    /* Step 1: Kumpulkan semua PIN unik untuk tahun ini */
    var allPinsInYear={};
    RAW_DATA.filter(function(r){return r.tahun===tahun;}).forEach(function(r){
        if(!allPinsInYear[r.pin]) allPinsInYear[r.pin]={pin:r.pin,nama:r.nama};
    });

    /* Step 2: Hitung stats berdasarkan periode yang dipilih */
    var pinStatsMap={};
    var relevantData=RAW_DATA.filter(function(r){
        if(r.tahun!==tahun) return false;
        if(bulan>0 && r.bulan!==bulan) return false;
        return true;
    });

    relevantData.forEach(function(r){
        if(!pinStatsMap[r.pin]) pinStatsMap[r.pin]={dateSet:{}};
        var dk=r.tanggal;
        if(!pinStatsMap[r.pin].dateSet[dk]) pinStatsMap[r.pin].dateSet[dk]={masuk:false,terlambat:false,pulangCepat:false};
        if(r.isMasuk){
            pinStatsMap[r.pin].dateSet[dk].masuk=true;
            if(r.terlambat) pinStatsMap[r.pin].dateSet[dk].terlambat=true;
        } else {
            if(r.pulangCepat) pinStatsMap[r.pin].dateSet[dk].pulangCepat=true;
        }
    });

    /* Step 3: Build result — semua karyawan di tahun ini, stats dari periode filter */
    return Object.keys(allPinsInYear).map(function(pin){
        var info=allPinsInYear[pin];
        var statsData=pinStatsMap[pin]?Object.values(pinStatsMap[pin].dateSet):[];
        return {
            pin: info.pin,
            nama: info.nama,
            totalDays: statsData.filter(function(d){return d.masuk;}).length,
            terlambat: statsData.filter(function(d){return d.terlambat;}).length,
            pulangCepat: statsData.filter(function(d){return d.pulangCepat;}).length
        };
    });
}

/* Get monthly stats for one employee in a year */
function getEmpMonthlyData(pin,tahun){
    var mMap={};
    RAW_DATA.filter(function(r){return r.pin===pin&&r.tahun===tahun;}).forEach(function(r){
        var m=r.bulan;
        if(!mMap[m]) mMap[m]={};
        var dk=r.tanggal;
        if(!mMap[m][dk]) mMap[m][dk]={masuk:null,pulang:null,terlambat:false,pulangCepat:false};
        var d=mMap[m][dk];
        if(r.isMasuk){if(!d.masuk||r.waktu<d.masuk){d.masuk=r.waktu;d.terlambat=r.terlambat;}}
        else{if(!d.pulang||r.waktu>d.pulang){d.pulang=r.waktu;d.pulangCepat=r.pulangCepat;}}
    });
    var result=[];
    for(var m=1;m<=12;m++){
        if(!mMap[m]) continue;
        var days=Object.values(mMap[m]);
        var hadir=days.filter(function(d){return d.masuk;}).length;
        var late=days.filter(function(d){return d.terlambat;}).length;
        var early=days.filter(function(d){return d.pulangCepat;}).length;
        var tepat=days.filter(function(d){return d.masuk&&!d.terlambat;}).length;
        result.push({bulan:m,label:NAMA_BULAN_SHORT[m],hadir:hadir,terlambat:late,pulangCepat:early,tepat:tepat});
    }
    return result;
}

/* ════════════════════════════════════════════
   PERFORMA PAGE — RENDER
════════════════════════════════════════════ */
var empBarChartInstance=null;
var selectedEmpPin=null;
var selectedEmpColor='#0096c7';
var currentBarMode='all';

function renderPerfPage(){
    var tahun=parseInt(document.getElementById('filterTahunPerf').value);
    var bulan=parseInt(document.getElementById('filterBulanPerf').value);
    var badgeText=bulan===0?tahun:NAMA_BULAN[bulan]+' '+tahun;
    document.getElementById('perfYearBadge').textContent=badgeText;
    document.getElementById('perfPeriodHint').textContent=bulan===0
        ?'📅 Menampilkan data seluruh bulan dalam tahun terpilih'
        :'📅 Menampilkan data harian bulan '+NAMA_BULAN[bulan]+' '+tahun;

    /* ── FIXED: gunakan bulan dalam getEmployeesForPeriod ── */
    var emps=getEmployeesForPeriod(tahun, bulan);
    document.getElementById('empCountChip').textContent=emps.length+' karyawan';
    renderEmpSelector(emps,tahun,bulan);

    /* Re-render chart if employee was selected */
    if(selectedEmpPin){
        var emp=emps.find(function(e){return e.pin===selectedEmpPin;});
        if(emp) buildBarChart(selectedEmpPin,emp.nama,selectedEmpColor,tahun,bulan);
        else { document.getElementById('empChartCard').classList.remove('show'); document.getElementById('empChartHint').style.display=''; selectedEmpPin=null; }
    }
}

function renderEmpSelector(emps,tahun,bulan){
    var grid=document.getElementById('empSelectorGrid');
    grid.innerHTML='';
    if(emps.length===0){
        grid.innerHTML='<div class="empty"><div class="empty-icon">&#128202;</div><div class="empty-text">Tidak ada data untuk tahun ini</div><div class="empty-sub">Upload file CSV atau pilih tahun lain</div></div>';
        return;
    }
    emps.forEach(function(emp,i){
        var color=PALETTE[i%PALETTE.length];
        var initials=emp.nama.substring(0,2).toUpperCase();
        var isSelected=emp.pin===selectedEmpPin;
        var hasData=emp.totalDays>0||emp.terlambat>0||emp.pulangCepat>0;

        var card=document.createElement('div');
        /* Tambahkan class no-data jika tidak ada data di periode ini */
        card.className='emp-card'+(isSelected?' selected':'')+(hasData?'':' no-data');

        card.innerHTML=
            '<div class="emp-card-badge"><svg viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg></div>'+
            '<div class="emp-card-header">'+
                '<div class="emp-card-avatar" style="background:linear-gradient(135deg,'+color+','+lightenColor(color)+'80)">'+initials+'</div>'+
                '<div style="min-width:0">'+
                    '<div class="emp-card-name" title="'+emp.nama+'">'+emp.nama+'</div>'+
                    '<div class="emp-card-pin">PIN: '+emp.pin+'</div>'+
                '</div>'+
            '</div>'+
            '<div class="emp-card-stats">'+
                '<div class="emp-card-stat"><div class="emp-card-stat-num" style="color:'+(hasData?color:'#94a3b8')+'">'+emp.totalDays+'</div><div class="emp-card-stat-lbl">Hari</div></div>'+
                '<div class="emp-card-stat"><div class="emp-card-stat-num" style="color:'+(emp.terlambat>0?'#d97706':'#94a3b8')+'">'+emp.terlambat+'</div><div class="emp-card-stat-lbl">Lambat</div></div>'+
                '<div class="emp-card-stat"><div class="emp-card-stat-num" style="color:'+(emp.pulangCepat>0?'#e11d48':'#94a3b8')+'">'+emp.pulangCepat+'</div><div class="emp-card-stat-lbl">Cepat</div></div>'+
            '</div>'+
            '<div class="emp-nodata-badge">Tidak ada data</div>';

        (function(pin,nama,col,tahunVal,bulanVal,cardEl){
            cardEl.onclick=function(){
                selectedEmpPin=pin;
                selectedEmpColor=col;
                document.querySelectorAll('.emp-card').forEach(function(c){c.classList.remove('selected');});
                cardEl.classList.add('selected');
                buildBarChart(pin,nama,col,tahunVal,bulanVal);
            };
        })(emp.pin,emp.nama,color,tahun,bulan,card);
        grid.appendChild(card);
    });
}

/* Lighten color helper */
function lightenColor(hex){
    var r=parseInt(hex.slice(1,3),16),g=parseInt(hex.slice(3,5),16),b=parseInt(hex.slice(5,7),16);
    r=Math.min(255,r+60);g=Math.min(255,g+60);b=Math.min(255,b+60);
    return '#'+[r,g,b].map(function(v){return v.toString(16).padStart(2,'0');}).join('');
}

/* Bar mode toggle */
function setBarMode(mode){
    currentBarMode=mode;
    ['btnModeAll','btnModeHadir','btnModeLate'].forEach(function(id){document.getElementById(id).classList.remove('active');});
    document.getElementById(mode==='all'?'btnModeAll':mode==='hadir'?'btnModeHadir':'btnModeLate').classList.add('active');
    if(selectedEmpPin){
        var tahun=parseInt(document.getElementById('filterTahunPerf').value);
        var bulan=parseInt(document.getElementById('filterBulanPerf').value);
        buildBarChart(selectedEmpPin,document.getElementById('bcName').textContent,selectedEmpColor,tahun,bulan);
    }
}

/* Get daily stats for one employee in a specific month+year */
function getEmpDailyData(pin,tahun,bulan){
    var dMap={};
    RAW_DATA.filter(function(r){return r.pin===pin&&r.tahun===tahun&&r.bulan===bulan;}).forEach(function(r){
        var dk=r.tanggal;
        if(!dMap[dk]) dMap[dk]={tanggal:dk,masuk:null,pulang:null,terlambat:false,pulangCepat:false};
        var d=dMap[dk];
        if(r.isMasuk){if(!d.masuk||r.waktu<d.masuk){d.masuk=r.waktu;d.terlambat=r.terlambat;}}
        else{if(!d.pulang||r.waktu>d.pulang){d.pulang=r.waktu;d.pulangCepat=r.pulangCepat;}}
    });
    return Object.values(dMap).sort(function(a,b){return a.tanggal<b.tanggal?-1:1;}).map(function(d){
        var hadir=d.masuk?1:0;
        var terlambat=d.terlambat?1:0;
        var pulangCepat=d.pulangCepat?1:0;
        var tepat=(d.masuk&&!d.terlambat)?1:0;
        var dayNum=parseInt(d.tanggal.split('-')[2]);
        return{tanggal:d.tanggal,label:String(dayNum),hadir:hadir,terlambat:terlambat,pulangCepat:pulangCepat,tepat:tepat};
    });
}

/* ════════════════════════════════════════════
   BUILD BAR CHART — CORE FUNCTION
════════════════════════════════════════════ */
function buildBarChart(pin,nama,color,tahun,bulan){
    if(bulan===undefined) bulan=0;
    var isMonthMode=bulan>0;
    var monthData=isMonthMode?getEmpDailyData(pin,tahun,bulan):getEmpMonthlyData(pin,tahun);

    /* Show chart panel, hide hint */
    document.getElementById('empChartCard').classList.add('show');
    document.getElementById('empChartHint').style.display='none';

    /* Update header */
    var initials=nama.substring(0,2).toUpperCase();
    var av=document.getElementById('bcAvatar');
    av.textContent=initials;
    av.style.background='linear-gradient(135deg,'+color+','+lightenColor(color)+'99)';
    document.getElementById('bcName').textContent=nama;
    document.getElementById('bcPin').textContent='PIN: '+pin+' · '+(isMonthMode?NAMA_BULAN[bulan]+' '+tahun:'Tahun '+tahun);

    /* Yearly/Monthly totals */
    var yHadir=0,yLate=0,yEarly=0,yTepat=0;
    monthData.forEach(function(m){yHadir+=m.hadir;yLate+=m.terlambat;yEarly+=m.pulangCepat;yTepat+=m.tepat;});
    renderYearlyStats(yHadir,yLate,yEarly,yTepat,isMonthMode?(NAMA_BULAN[bulan]+' '+tahun):'Tahun '+tahun);

    /* Empty state */
    if(!monthData.length){
        if(empBarChartInstance){empBarChartInstance.destroy();empBarChartInstance=null;}
        var ctx2=document.getElementById('empBarChart').getContext('2d');
        ctx2.clearRect(0,0,9999,9999);
        return;
    }

    var labels=monthData.map(function(m){return m.label;});
    var hadirData=monthData.map(function(m){return m.hadir;});
    var lateData=monthData.map(function(m){return m.terlambat;});
    var earlyData=monthData.map(function(m){return m.pulangCepat;});
    var tepatData=monthData.map(function(m){return m.tepat;});
    if(empBarChartInstance){empBarChartInstance.destroy();empBarChartInstance=null;}

    var ctx=document.getElementById('empBarChart').getContext('2d');

    /* Cyan/teal gradient for main bars */
    var gradMain=ctx.createLinearGradient(0,0,0,400);
    gradMain.addColorStop(0,'#0096c7');
    gradMain.addColorStop(0.5,'#00b4d8');
    gradMain.addColorStop(1,'#90e0ef');

    var gradTepat=ctx.createLinearGradient(0,0,0,400);
    gradTepat.addColorStop(0,'#0077b6');
    gradTepat.addColorStop(1,'#48cae4');

    var datasets=[];

    if(currentBarMode==='all'||currentBarMode==='hadir'){
        datasets.push({label:'Total Hadir',type:'bar',data:hadirData,backgroundColor:gradMain,borderColor:'rgba(0,119,182,0.3)',borderWidth:1,borderRadius:6,borderSkipped:false,order:2});
        datasets.push({label:'Tepat Waktu',type:'bar',data:tepatData,backgroundColor:gradTepat,borderColor:'rgba(0,119,182,0.2)',borderWidth:1,borderRadius:6,borderSkipped:false,order:3});
    }
    if(currentBarMode==='all'||currentBarMode==='late'){
        datasets.push({label:'Terlambat',type:'bar',data:lateData,backgroundColor:'rgba(251,191,36,0.85)',borderColor:'rgba(217,119,6,0.3)',borderWidth:1,borderRadius:6,borderSkipped:false,order:4});
        datasets.push({label:'Pulang Cepat',type:'bar',data:earlyData,backgroundColor:'rgba(251,113,133,0.85)',borderColor:'rgba(225,29,72,0.3)',borderWidth:1,borderRadius:6,borderSkipped:false,order:5});
    }
    if(currentBarMode==='all'||currentBarMode==='hadir'){
        datasets.push({label:'Tren Kehadiran',type:'line',data:hadirData,borderColor:'#023e8a',backgroundColor:'transparent',borderWidth:2.5,tension:0.45,pointRadius:5,pointBackgroundColor:'#023e8a',pointBorderColor:'#fff',pointBorderWidth:2,pointHoverRadius:8,fill:false,order:1});
    }

    empBarChartInstance=new Chart(ctx,{
        type:'bar',
        data:{labels:labels,datasets:datasets},
        options:{
            responsive:true,maintainAspectRatio:false,
            interaction:{mode:'index',intersect:false},
            plugins:{
                legend:{display:true,position:'top',labels:{usePointStyle:true,pointStyle:'rectRounded',font:{family:'Plus Jakarta Sans',size:11,weight:'700'},color:'#64748b',padding:18,boxWidth:14,boxHeight:14}},
                tooltip:{
                    callbacks:{
                        title:function(items){var d=monthData[items[0].dataIndex];if(isMonthMode)return d.tanggal+' ('+NAMA_BULAN[bulan]+' '+tahun+')';return NAMA_BULAN[d.bulan]+' '+tahun;},
                        label:function(ctx){return ' '+ctx.dataset.label+': '+ctx.raw+' hari';}
                    },
                    padding:12,cornerRadius:12,backgroundColor:'rgba(2,62,138,0.94)',
                    titleFont:{family:'Plus Jakarta Sans',size:13,weight:'800'},bodyFont:{family:'Plus Jakarta Sans',size:12},
                    titleColor:'#bae6fd',bodyColor:'#e0f2fe',borderColor:'rgba(0,180,216,0.3)',borderWidth:1
                }
            },
            scales:{
                x:{grid:{display:false},ticks:{font:{family:'Plus Jakarta Sans',size:11,weight:'600'},color:'#64748b'},border:{display:false}},
                y:{beginAtZero:true,grid:{color:'rgba(0,150,199,0.08)',drawBorder:false},ticks:{font:{family:'JetBrains Mono',size:10},color:'#94a3b8',stepSize:1,padding:8,callback:function(v){return Number.isInteger(v)?v:'';}},border:{display:false}}
            },
            animation:{duration:700,easing:'easeInOutQuart'}
        }
    });

    setTimeout(function(){document.getElementById('empChartCard').scrollIntoView({behavior:'smooth',block:'nearest'});},80);
}

/* Yearly/Monthly stat strip */
function renderYearlyStats(hadir,late,early,tepat,periodLabel){
    var strip=document.getElementById('yearlyStatStrip');
    var lbl=periodLabel||'';
    strip.innerHTML=
        (lbl?'<div style="width:100%;padding:0 0 6px;font-size:11px;font-weight:700;color:var(--textmute);text-transform:uppercase;letter-spacing:.06em;">Ringkasan '+lbl+'</div>':'')+
        mkYSPill('ys-hadir','<svg viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="18" rx="2"/><path d="M16 2v4M8 2v4M3 10h18"/></svg>',hadir,'Total Hari Hadir')+
        mkYSPill('ys-tepat','<svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="9"/><path d="M9 12l2 2 4-4"/></svg>',tepat,'Tepat Waktu')+
        mkYSPill('ys-lambat','<svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="9"/><path d="M12 8v4"/><circle cx="12" cy="16" r=".5" fill="currentColor"/></svg>',late,'Terlambat')+
        mkYSPill('ys-cepat','<svg viewBox="0 0 24 24"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>',early,'Pulang Cepat');
}
function mkYSPill(cls,icon,val,lbl){
    return '<div class="ys-pill '+cls+'"><div class="ys-icon">'+icon+'</div><div class="ys-body"><div class="ys-val">'+val+'</div><div class="ys-lbl">'+lbl+'</div></div></div>';
}

/* ════ DASHBOARD TABLE ════ */
var currentKaryawanPage=1;
function renderTable(grouped){
    var tbody=document.getElementById('tableBody'),pgBtns=document.getElementById('pgBtns'),pgInfo=document.getElementById('pgInfo'),navStrip=document.getElementById('karyawanNavStrip');
    tbody.innerHTML='';
    var total=grouped.length;
    if(currentKaryawanPage>total) currentKaryawanPage=Math.max(1,total);
    var totalDays=grouped.reduce(function(s,k){return s+k.days.length;},0);
    document.getElementById('countChip').textContent=total+' karyawan · '+totalDays+' hari';
    navStrip.innerHTML='<span class="karyawan-nav-label">Karyawan:</span>';
    grouped.forEach(function(k,idx){
        var btn=document.createElement('button');
        btn.className='karyawan-nav-btn'+(idx+1===currentKaryawanPage?' active':'');
        btn.textContent=k.nama.split(' ')[0];btn.title=k.nama;
        btn.onclick=(function(i){return function(){currentKaryawanPage=i+1;renderTable(grouped);};})(idx);
        navStrip.appendChild(btn);
    });
    if(total===0){
        tbody.innerHTML='<tr><td colspan="6"><div class="empty"><div class="empty-icon">&#128269;</div><div class="empty-text">Tidak ada data untuk periode ini</div><div class="empty-sub">Coba pilih bulan/tahun lain atau ubah filter</div></div></td></tr>';
        document.getElementById('karyawanIdx').textContent='–';document.getElementById('karyawanName').textContent='–';document.getElementById('shownTotal').textContent='0';
        pgInfo.textContent='Tidak ada data';pgBtns.innerHTML='';return;
    }
    var k=grouped[currentKaryawanPage-1];
    document.getElementById('karyawanIdx').textContent=currentKaryawanPage;
    document.getElementById('karyawanName').textContent=k.nama;
    document.getElementById('shownTotal').textContent=k.days.length;
    k.days.forEach(function(d,idx){
        var tr=document.createElement('tr');tr.style.animation='rowIn .3s '+(idx*0.03)+'s ease both';
        var jamMasuk='<span class="time-none">—</span>';
        if(d.masuk){
            if(d.terlambat){jamMasuk='<span class="time-badge time-late"><svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="9"/><path d="M12 8v4"/><circle cx="12" cy="16" r=".8" fill="#d97706"/></svg>'+d.masuk+'</span>';}
            else{jamMasuk='<span class="time-badge time-ok"><svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="9"/><path d="M9 12l2 2 4-4"/></svg>'+d.masuk+'</span>';}
        }
        var jamPulang='<span class="time-none">—</span>';
        if(d.pulang){
            if(d.pulangCepat){jamPulang='<span class="time-badge time-early"><svg viewBox="0 0 24 24"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>'+d.pulang+'</span>';}
            else{jamPulang='<span class="time-badge time-ok"><svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="9"/><path d="M9 12l2 2 4-4"/></svg>'+d.pulang+'</span>';}
        }
        var initials=k.nama.substring(0,2).toUpperCase();
        tr.innerHTML='<td style="color:var(--textlight);font-size:12px;font-family:\'JetBrains Mono\',monospace">'+String(idx+1).padStart(2,'0')+'</td>'+
            '<td><span class="pin">'+k.pin+'</span></td>'+
            '<td><div class="nama-cell"><div class="avatar">'+initials+'</div><span style="font-weight:600">'+k.nama+'</span></div></td>'+
            '<td style="font-size:12px;color:var(--textmute)">'+d.tanggalFmt+'</td>'+
            '<td>'+jamMasuk+'</td><td>'+jamPulang+'</td>';
        tbody.appendChild(tr);
    });
    pgInfo.textContent='Karyawan '+currentKaryawanPage+' dari '+total;
    pgBtns.innerHTML='';
    function mkBtn(label,page,disabled,active){var b=document.createElement('button');b.className='pg-btn'+(active?' pg-active':'');b.innerHTML=label;b.disabled=disabled;b.onclick=function(){currentKaryawanPage=page;renderTable(grouped);};pgBtns.appendChild(b);}
    function mkE(){var s=document.createElement('span');s.className='pg-ellipsis';s.textContent='…';pgBtns.appendChild(s);}
    mkBtn('&#8592;',currentKaryawanPage-1,currentKaryawanPage===1,false);
    var pages=[];
    if(total<=7){for(var i=1;i<=total;i++)pages.push(i);}
    else{pages.push(1);if(currentKaryawanPage>3)pages.push('…');var lo=Math.max(2,currentKaryawanPage-1),hi=Math.min(total-1,currentKaryawanPage+1);for(var i=lo;i<=hi;i++)pages.push(i);if(currentKaryawanPage<total-2)pages.push('…');pages.push(total);}
    pages.forEach(function(p){if(p==='…')mkE();else mkBtn(p,p,false,p===currentKaryawanPage);});
    mkBtn('&#8594;',currentKaryawanPage+1,currentKaryawanPage===total,false);
}

/* ════ APPLY PERIOD (Dashboard) ════ */
function applyPeriodFilter(){
    var bulan=parseInt(document.getElementById('filterBulan').value);
    var tahun=parseInt(document.getElementById('filterTahun').value);
    var periodLabel=NAMA_BULAN[bulan]+' '+tahun;
    document.getElementById('periodBadgeText').textContent=periodLabel;
    document.getElementById('chartPeriodLabel').textContent=periodLabel;
    document.getElementById('tablePeriodLabel').textContent=periodLabel;

    var pr=RAW_DATA.filter(function(r){return r.bulan===bulan&&r.tahun===tahun;});
    var cTotal=pr.length,cTepat=pr.filter(function(r){return r.isMasuk&&!r.terlambat;}).length,cLambat=pr.filter(function(r){return r.terlambat;}).length,cCepat=pr.filter(function(r){return r.pulangCepat;}).length,cPT=pr.filter(function(r){return !r.isMasuk&&!r.pulangCepat;}).length;
    function sv(id,val){var el=document.getElementById(id);el.textContent=val;el.classList.remove('updating');void el.offsetWidth;el.classList.add('updating');}
    sv('statTotal',cTotal);sv('statTepat',cTepat);sv('statLambat',cLambat);sv('statCepat',cCepat);
    var nd=[cTepat,cLambat,cCepat,cPT],dt=nd.reduce(function(a,b){return a+b;},0);
    donutChart.data.datasets[0].data=nd;donutChart.update();
    document.getElementById('donutCenterNum').textContent=dt;document.getElementById('donutCenterNum').style.color='var(--text)';document.getElementById('donutCenterLbl').textContent='total';
    updateLegend(nd,dt);
    currentKaryawanPage=1;
    renderTable(groupByKaryawan(bulan,tahun,true));
}

function applyFiltersAndRender(){
    var b=parseInt(document.getElementById('filterBulan').value),t=parseInt(document.getElementById('filterTahun').value);
    currentKaryawanPage=1;renderTable(groupByKaryawan(b,t,true));
}
function resetFilter(){document.getElementById('searchName').value='';document.getElementById('filterDate').value='';document.getElementById('filterKet').value='';applyFiltersAndRender();}

/* ════ INIT ════ */
applyPeriodFilter();
</script>
</body>
</html>