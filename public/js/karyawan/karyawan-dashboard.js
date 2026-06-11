/* ══════════════════════════════════════════════════
   karyawan-dashboard.js
   FIX:
   1. liveClock — tick() dipanggil setelah DOM ready (script sudah di bawah body)
   2. populateHari() — label "1 Januari", "2 Januari" dst
   3. filterHari diisi otomatis saat pertama kali ke mode harian
   4. hitungAbsen — hitung berdasar data period yg benar
   5. Data sinkron: filterByPeriod pakai tahun+bulan+hari sesuai viewMode
   ══════════════════════════════════════════════════ */

/* ══ AUTO-DISMISS ALERT ══ */
setTimeout(function () {
    ['alertSuccess', 'alertError'].forEach(function (id) {
        var el = document.getElementById(id);
        if (el) { el.style.transition = 'opacity .6s'; el.style.opacity = '0'; setTimeout(function () { el.remove(); }, 600); }
    });
}, 8000);

/* ══ KONSTANTA ══ */
var NAMA_BULAN   = ['', 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
var NAMA_BULAN_S = ['', 'Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Ags', 'Sep', 'Okt', 'Nov', 'Des'];
var NAMA_HARI    = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
var HARI_CSS     = ['day-minggu', 'day-senin', 'day-selasa', 'day-rabu', 'day-kamis', 'day-jumat', 'day-sabtu'];
var DONUT_COLORS = ['#16a34a', '#d97706', '#e11d48', '#6366f1', '#7c3aed'];
var DONUT_LABELS = ['Tepat Waktu', 'Terlambat', 'Pulang Cepat', 'Tepat Pulang', 'Tidak Masuk'];
var DONUT_TXT    = ['#4ade80', '#fbbf24', '#fb7185', '#818cf8', '#c084fc'];
var BC = {
    hadir:  { hex: '#6366f1', bg: 'rgba(99,102,241,0.70)',  border: '#6366f1', label: 'Total Hadir',    type: 'bar'  },
    tepat:  { hex: '#16a34a', bg: 'rgba(22,163,74,0.70)',   border: '#16a34a', label: 'Tepat Waktu',    type: 'bar'  },
    absent: { hex: '#7c3aed', bg: 'rgba(124,58,237,0.70)',  border: '#7c3aed', label: 'Tidak Masuk',    type: 'bar'  },
    lambat: { hex: '#d97706', bg: 'rgba(217,119,6,0.80)',   border: '#d97706', label: 'Terlambat',      type: 'bar'  },
    cepat:  { hex: '#e11d48', bg: 'rgba(225,29,72,0.80)',   border: '#e11d48', label: 'Pulang Cepat',   type: 'bar'  },
    tren:   { hex: '#0d9488', bg: 'rgba(13,148,136,0.10)',  border: '#0d9488', label: 'Tren Kehadiran', type: 'line' }
};
var PALETTE = ['#6366f1', '#0d9488', '#d97706', '#e11d48', '#16a34a', '#7c3aed', '#0891b2', '#dc2626', '#65a30d', '#9333ea', '#2563eb', '#ea580c', '#059669', '#be185d', '#ca8a04'];

/* ══ STATE ══ */
var RAW_DATA = [];
try { RAW_DATA = JSON.parse(document.getElementById('rawDataScript').textContent); } catch (e) { console.warn('RAW_DATA parse error', e); }

var viewMode         = 'bulanan';
var currentEmpPage   = 1;
var donutChart       = null;
var empBarChart      = null;
var selectedEmpPin   = null;
var selectedEmpColor = '#6366f1';
var barMode          = 'all';
var currentPage      = 'dashboard';

/* ══ LIVE CLOCK — DOM sudah siap karena script di bawah body ══ */
function tick() {
    var el = document.getElementById('liveClock');
    if (el) el.textContent = new Date().toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit', second: '2-digit' });
}
tick();
setInterval(tick, 1000);

/* ══ SIDEBAR ══ */
function toggleSidebar() {
    document.getElementById('sidebar').classList.toggle('open');
    document.getElementById('sidebarOverlay').classList.toggle('show');
}
function closeSidebarMobile() {
    if (window.innerWidth <= 767) {
        document.getElementById('sidebar').classList.remove('open');
        document.getElementById('sidebarOverlay').classList.remove('show');
    }
}

/* ══ INIT ══ */
(function init() {
    var now = new Date(), nowM = now.getMonth() + 1, nowY = now.getFullYear();

    /* Isi dropdown tahun */
    ['filterTahun', 'filterTahunPerf'].forEach(function (id) {
        var sel = document.getElementById(id); if (!sel) return;
        var years = {}; years[nowY] = true;
        RAW_DATA.forEach(function (r) { years[r.tahun] = true; });
        Object.keys(years).sort(function (a, b) { return b - a; }).forEach(function (y) {
            var o = document.createElement('option'); o.value = y; o.textContent = y;
            if (parseInt(y) === nowY) o.selected = true;
            sel.appendChild(o);
        });
    });

    /* Pilih bulan terbaik (bulan dengan data terbanyak tahun ini, fallback bulan sekarang) */
    var bestMonth = nowM;
    if (RAW_DATA.length > 0) {
        var mc = {};
        RAW_DATA.forEach(function (r) { if (r.tahun === nowY) mc[r.bulan] = (mc[r.bulan] || 0) + 1; });
        var best = 0;
        Object.keys(mc).forEach(function (m) { if (mc[m] > best) { best = mc[m]; bestMonth = parseInt(m); } });
        if (best === 0) {
            RAW_DATA.forEach(function (r) { mc[r.bulan] = (mc[r.bulan] || 0) + 1; });
            Object.keys(mc).forEach(function (m) { if (mc[m] > best) { best = mc[m]; bestMonth = parseInt(m); } });
        }
    }
    document.getElementById('filterBulan').value    = bestMonth;
    document.getElementById('filterBulanPerf').value = bestMonth;

    /* Badge jumlah data di sidebar */
    document.getElementById('sbDataBadge').textContent = RAW_DATA.length;

    initDonut();
    onPeriodChange();
})();

/* ══ PAGE SWITCH ══ */
function switchPage(p) {
    closeSidebarMobile();
    currentPage = p;
    ['pageDashboard', 'pageData', 'pagePerforma'].forEach(function (id) { document.getElementById(id).classList.remove('active'); });
    ['sbDashboard', 'sbData', 'sbPerforma'].forEach(function (id) { var el = document.getElementById(id); if (el) el.classList.remove('active'); });
    document.getElementById('page' + p.charAt(0).toUpperCase() + p.slice(1)).classList.add('active');
    var sbEl = document.getElementById('sb' + p.charAt(0).toUpperCase() + p.slice(1)); if (sbEl) sbEl.classList.add('active');
    var names = { dashboard: 'Dashboard', data: 'Data Presensi', performa: 'Chart Performa' };
    var subs  = { dashboard: 'Ringkasan absensi', data: 'Tabel lengkap', performa: 'Analisis karyawan' };
    document.getElementById('tbPageName').textContent = names[p] || p;
    document.getElementById('tbPageSub').textContent  = subs[p]  || '';
    document.getElementById('topbarPeriod').style.display = (p === 'dashboard' || p === 'data') ? 'flex' : 'none';
    if (p === 'performa') renderPerfPage();
    if (p === 'data')     applyFiltersAndRender();
}

/* ══ HELPER HARI ══ */
function getNamaHari(tglStr)   { var pts = tglStr.split('-'); return NAMA_HARI[new Date(+pts[0], +pts[1] - 1, +pts[2]).getDay()]; }
function getHariCssClass(tglStr) { var pts = tglStr.split('-'); return HARI_CSS[new Date(+pts[0], +pts[1] - 1, +pts[2]).getDay()]; }

/* ══ DONUT ══ */
function initDonut() {
    var ctx = document.getElementById('donutChart').getContext('2d');
    donutChart = new Chart(ctx, {
        type: 'doughnut',
        data: { labels: DONUT_LABELS, datasets: [{ data: [0, 0, 0, 0, 0], backgroundColor: DONUT_COLORS, borderColor: '#1e293b', borderWidth: 3, hoverOffset: 10 }] },
        options: {
            responsive: true, maintainAspectRatio: false, cutout: '72%',
            animation: { animateScale: true, duration: 600 },
            plugins: {
                legend: { display: false },
                tooltip: {
                    callbacks: { label: function (c) { var t = c.dataset.data.reduce(function (a, b) { return a + b; }, 0); return ' ' + c.label + ': ' + c.raw + ' (' + (t > 0 ? Math.round(c.raw / t * 100) : 0) + '%)'; } },
                    padding: 10, cornerRadius: 8, backgroundColor: 'rgba(15,23,42,0.95)', titleColor: '#e2e8f0', bodyColor: '#cbd5e1'
                }
            },
            onHover: function (e, els) { onDonutHover(els); }
        }
    });
}
function onDonutHover(els) {
    var d = donutChart.data.datasets[0].data, t = d.reduce(function (a, b) { return a + b; }, 0);
    var nE = document.getElementById('donutCenterNum'), lE = document.getElementById('donutCenterLbl');
    if (els.length) { nE.textContent = d[els[0].index]; nE.style.color = DONUT_COLORS[els[0].index]; lE.textContent = DONUT_LABELS[els[0].index]; }
    else { nE.textContent = t; nE.style.color = '#ffffff'; lE.textContent = 'total'; }
}
function updateDonut(vals) {
    if (!donutChart) return;
    var total = vals.reduce(function (a, b) { return a + b; }, 0);
    donutChart.data.datasets[0].data = vals; donutChart.update();
    document.getElementById('donutCenterNum').textContent = total;
    document.getElementById('donutCenterNum').style.color = '#ffffff';
    document.getElementById('donutCenterLbl').textContent = 'total';
    renderLegend(vals, total);
}
function renderLegend(vals, total) {
    var c = document.getElementById('donutLegends'); c.innerHTML = '';
    DONUT_LABELS.forEach(function (lbl, i) {
        var pct = total > 0 ? Math.round(vals[i] / total * 100) : 0;
        var div = document.createElement('div'); div.className = 'dl-item';
        div.innerHTML = '<span class="dl-dot" style="background:' + DONUT_COLORS[i] + '"></span><span class="dl-label">' + lbl + '</span><span class="dl-val" style="color:' + DONUT_TXT[i] + '">' + vals[i] + '<span class="dl-pct">(' + pct + '%)</span></span>';
        div.setAttribute('onmouseover', 'hoverDonut(' + i + ')');
        div.setAttribute('onmouseout', 'resetDonut()');
        c.appendChild(div);
    });
}
function hoverDonut(i) { if (!donutChart) return; var d = donutChart.data.datasets[0].data; donutChart.setActiveElements([{ datasetIndex: 0, index: i }]); donutChart.tooltip.setActiveElements([{ datasetIndex: 0, index: i }], { x: 0, y: 0 }); donutChart.update(); document.getElementById('donutCenterNum').textContent = d[i]; document.getElementById('donutCenterNum').style.color = DONUT_COLORS[i]; document.getElementById('donutCenterLbl').textContent = DONUT_LABELS[i]; }
function resetDonut() { if (!donutChart) return; var d = donutChart.data.datasets[0].data, t = d.reduce(function (a, b) { return a + b; }, 0); donutChart.setActiveElements([]); donutChart.tooltip.setActiveElements([], { x: 0, y: 0 }); donutChart.update(); document.getElementById('donutCenterNum').textContent = t; document.getElementById('donutCenterNum').style.color = '#ffffff'; document.getElementById('donutCenterLbl').textContent = 'total'; }

/* ══ PERIOD ══ */
function setViewMode(mode) {
    viewMode = mode;
    ['vpHarian', 'vpMingguan', 'vpBulanan', 'vpTahunan'].forEach(function (id) { document.getElementById(id).classList.remove('active'); });
    document.getElementById({ harian: 'vpHarian', mingguan: 'vpMingguan', bulanan: 'vpBulanan', tahunan: 'vpTahunan' }[mode]).classList.add('active');
    document.getElementById('filterBulan').style.display  = (mode !== 'tahunan') ? '' : 'none';
    document.getElementById('filterMinggu').style.display = (mode === 'mingguan') ? '' : 'none';
    document.getElementById('filterHari').style.display   = (mode === 'harian')   ? '' : 'none';
    /* FIX: populate hari langsung saat switch ke mode harian */
    if (mode === 'harian') {
        var y = parseInt(document.getElementById('filterTahun').value);
        var m = parseInt(document.getElementById('filterBulan').value);
        populateHari(y, m);
    }
    onPeriodChange();
}

/**
 * FIX: label dropdown harian = "1 Januari", "2 Januari", …, "31 Januari"
 * bukan hanya angka.
 */
function populateHari(tahun, bulan) {
    var sel = document.getElementById('filterHari');
    var prev = sel.value;
    sel.innerHTML = '';
    var days = new Date(tahun, bulan, 0).getDate(); /* jumlah hari di bulan tsb */
    for (var d = 1; d <= days; d++) {
        var o = document.createElement('option');
        o.value = d;
        o.textContent = d + ' ' + NAMA_BULAN[bulan]; /* ← "1 Januari", "2 Januari", dst */
        sel.appendChild(o);
    }
    /* Pertahankan pilihan sebelumnya jika masih valid */
    if (prev && parseInt(prev) >= 1 && parseInt(prev) <= days) {
        sel.value = prev;
    } else {
        /* Default: hari ini (kalau bulan & tahun cocok) atau hari 1 */
        var now = new Date();
        if (now.getFullYear() === tahun && (now.getMonth() + 1) === bulan) {
            sel.value = now.getDate();
        } else {
            sel.value = 1;
        }
    }
}

function getPeriod() {
    return {
        tahun:  parseInt(document.getElementById('filterTahun').value)  || new Date().getFullYear(),
        bulan:  parseInt(document.getElementById('filterBulan').value)  || new Date().getMonth() + 1,
        minggu: parseInt(document.getElementById('filterMinggu').value) || 1,
        hari:   parseInt(document.getElementById('filterHari').value)   || 1
    };
}

/**
 * FIX: filterByPeriod — filter ketat sesuai viewMode
 * Sinkron dengan logika admin: tahun → bulan → minggu/hari
 */
function filterByPeriod(records, p) {
    return records.filter(function (r) {
        if (r.tahun !== p.tahun) return false;
        if (viewMode === 'tahunan') return true;
        if (r.bulan !== p.bulan)  return false;
        if (viewMode === 'bulanan') return true;
        if (viewMode === 'mingguan') return r.minggu === p.minggu;
        /* harian */
        return r.hari === p.hari;
    });
}

function periodLabel(p) {
    if (viewMode === 'tahunan')  return 'Tahun ' + p.tahun;
    if (viewMode === 'bulanan')  return NAMA_BULAN[p.bulan] + ' ' + p.tahun;
    if (viewMode === 'mingguan') return 'Minggu ' + p.minggu + ', ' + NAMA_BULAN[p.bulan] + ' ' + p.tahun;
    /* harian — format: "Senin, 1 Januari 2025" */
    var tgl = p.tahun + '-' + String(p.bulan).padStart(2, '0') + '-' + String(p.hari).padStart(2, '0');
    return getNamaHari(tgl) + ', ' + p.hari + ' ' + NAMA_BULAN[p.bulan] + ' ' + p.tahun;
}
function viewLabel() { return { harian: 'Harian', mingguan: 'Mingguan', bulanan: 'Bulanan', tahunan: 'Tahunan' }[viewMode]; }

function onPeriodChange() {
    var p = getPeriod(), lbl = periodLabel(p);
    /* Saat bulan berubah di mode harian, re-populate hari */
    if (viewMode === 'harian') populateHari(p.tahun, p.bulan);
    ['chartPeriodLabel', 'tablePeriodLabel'].forEach(function (id) { var el = document.getElementById(id); if (el) el.textContent = lbl; });
    var subEl = document.getElementById('tbPageSub');
    if (subEl && (currentPage === 'dashboard' || currentPage === 'data')) subEl.textContent = lbl;
    document.getElementById('chartViewChip').textContent = viewLabel();

    var pr      = filterByPeriod(RAW_DATA, p);
    var cTepat  = pr.filter(function (r) { return r.isMasuk && !r.terlambat; }).length;
    var cLambat = pr.filter(function (r) { return r.terlambat; }).length;
    var cCepat  = pr.filter(function (r) { return r.pulangCepat; }).length;
    var cAbsent = hitungAbsen(pr, p);

    ['statTotal', 'statTepat', 'statLambat', 'statCepat', 'statAbsent'].forEach(function (id, i) {
        document.getElementById(id).textContent = [pr.length, cTepat, cLambat, cCepat, cAbsent][i];
    });
    var cPulangTepat = pr.filter(function (r) { return !r.isMasuk && !r.pulangCepat; }).length;
    updateDonut([cTepat, cLambat, cCepat, cPulangTepat, cAbsent]);
    renderDashQuickTable(pr, p);
    currentEmpPage = 1;
    if (currentPage === 'data') {
        if (viewMode === 'harian') renderDailyAllEmployees(pr, p);
        else renderTable(buildGrouped(pr, p));
    }
}

/* ══ QUICK TABLE DASHBOARD ══ */
function renderDashQuickTable(pr, p) {
    var wrap = document.getElementById('dashQuickTable');
    var allK = {};
    RAW_DATA.forEach(function (r) { if (!allK[r.pin]) allK[r.pin] = r.nama; });
    var emps = Object.keys(allK).map(function (pin) {
        var empR = pr.filter(function (r) { return r.pin === pin; });
        return { pin: pin, nama: allK[pin], masuk: empR.filter(function (r) { return r.isMasuk; }).length, lambat: empR.filter(function (r) { return r.terlambat; }).length };
    }).sort(function (a, b) { return a.nama < b.nama ? -1 : 1; }).slice(0, 8);
    if (!emps.length) { wrap.innerHTML = '<div class="empty"><div class="empty-icon">📋</div><div class="empty-text">Tidak ada data</div></div>'; return; }
    var html = '<table style="width:100%;border-collapse:collapse;font-size:12px"><thead><tr style="background:#0f172a"><th style="padding:9px 14px;text-align:left;font-size:10px;font-weight:700;color:#ffffff;border-bottom:1px solid rgba(255,255,255,0.1);text-transform:uppercase;letter-spacing:.07em">Karyawan</th><th style="padding:9px 14px;text-align:center;font-size:10px;font-weight:700;color:#ffffff;border-bottom:1px solid rgba(255,255,255,0.1);text-transform:uppercase;letter-spacing:.07em">Hadir</th><th style="padding:9px 14px;text-align:center;font-size:10px;font-weight:700;color:#ffffff;border-bottom:1px solid rgba(255,255,255,0.1);text-transform:uppercase;letter-spacing:.07em">Lambat</th></tr></thead><tbody>';
    emps.forEach(function (emp, i) {
        var color = PALETTE[i % PALETTE.length];
        html += '<tr><td style="padding:10px 14px"><div style="display:flex;align-items:center;gap:8px"><div style="width:26px;height:26px;border-radius:7px;background:' + color + ';display:flex;align-items:center;justify-content:center;font-size:9px;font-weight:600;color:#fff;flex-shrink:0">' + emp.nama.substring(0, 2).toUpperCase() + '</div><span style="font-size:12px;font-weight:500;color:#ffffff">' + emp.nama + '</span></div></td><td style="padding:10px 14px;text-align:center"><span style="font-size:13px;font-weight:600;color:' + (emp.masuk > 0 ? '#4ade80' : '#475569') + '">' + emp.masuk + '</span></td><td style="padding:10px 14px;text-align:center"><span style="font-size:13px;font-weight:600;color:' + (emp.lambat > 0 ? '#fbbf24' : '#475569') + '">' + emp.lambat + '</span></td></tr>';
    });
    html += '</tbody></table>';
    if (Object.keys(allK).length > 8) html += '<div style="text-align:center;padding:10px;font-size:12px;color:#818cf8;cursor:pointer;font-weight:500" onclick="switchPage(\'data\')">Lihat semua karyawan →</div>';
    wrap.innerHTML = html;
}

/* ══ HITUNG ABSEN ══
   FIX: hanya hitung tanggal yang benar-benar ada di periode,
   bukan seluruh RAW_DATA — agar sinkron dengan admin.
*/
function hitungAbsen(periodRecords, p) {
    /* Kumpulkan tanggal unik dalam periode */
    var dates = {};
    periodRecords.forEach(function (r) { dates[r.tanggal] = true; });
    var dArr = Object.keys(dates);
    if (!dArr.length) return 0;

    /* Semua karyawan yang pernah ada (bukan hanya yang hadir periode ini) */
    var allPins = {};
    RAW_DATA.forEach(function (r) { allPins[r.pin] = true; });
    var totalKaryawan = Object.keys(allPins).length;

    var absen = 0;
    dArr.forEach(function (dt) {
        var hadir = {};
        periodRecords.forEach(function (r) { if (r.tanggal === dt && r.isMasuk) hadir[r.pin] = true; });
        absen += totalKaryawan - Object.keys(hadir).length;
    });
    return Math.max(0, absen);
}

/* ══ RENDER HARIAN (semua karyawan 1 hari) ══ */
function renderDailyAllEmployees(periodRecords, p) {
    var allK = {};
    RAW_DATA.forEach(function (r) { if (!allK[r.pin]) allK[r.pin] = r.nama; });
    var dayData = {};
    Object.keys(allK).forEach(function (pin) {
        dayData[pin] = { pin: pin, nama: allK[pin], masuk: null, pulang: null, terlambat: false, pulangCepat: false, absent: true };
    });
    periodRecords.forEach(function (r) {
        if (!dayData[r.pin]) dayData[r.pin] = { pin: r.pin, nama: r.nama, masuk: null, pulang: null, terlambat: false, pulangCepat: false, absent: true };
        var d = dayData[r.pin];
        if (r.isMasuk) {
            d.absent = false;
            if (!d.masuk || r.waktu < d.masuk) { d.masuk = r.waktu; d.terlambat = r.terlambat; }
        } else {
            if (!d.pulang || r.waktu > d.pulang) { d.pulang = r.waktu; d.pulangCepat = r.pulangCepat; }
        }
    });
    var rows = Object.values(dayData).sort(function (a, b) { return a.nama < b.nama ? -1 : 1; });
    var tglStr   = p.tahun + '-' + String(p.bulan).padStart(2, '0') + '-' + String(p.hari).padStart(2, '0');
    var namaHari = getNamaHari(tglStr), hariCss = getHariCssClass(tglStr);
    document.getElementById('countChip').textContent = rows.length + ' karyawan';
    document.getElementById('karyawanNavStrip').style.display = 'none';
    document.getElementById('empTableHeader').style.display   = 'none';
    document.getElementById('tableHead').innerHTML = '<tr><th>No</th><th>Karyawan</th><th>Jam Masuk</th><th>Jam Pulang</th><th>Status</th></tr>';
    var tbody = document.getElementById('tableBody'); tbody.innerHTML = '';
    if (!rows.length) {
        tbody.innerHTML = '<tr><td colspan="5"><div class="empty"><div class="empty-icon">📋</div><div class="empty-text">Tidak ada data untuk hari ini</div></div></td></tr>';
    } else {
        rows.forEach(function (emp, idx) {
            var color    = PALETTE[idx % PALETTE.length];
            var initials = emp.nama.substring(0, 2).toUpperCase();
            var tr = document.createElement('tr'); tr.style.animation = 'rowIn .2s ' + (idx * 0.02) + 's ease both';
            if (emp.absent) tr.className = 'row-absent';
            var noCell  = '<td style="color:#475569;font-size:12px;font-family:\'JetBrains Mono\',monospace">' + String(idx + 1).padStart(2, '0') + '</td>';
            var empCell = '<td><div class="emp-name-cell"><div class="emp-avatar-sm" style="background:' + color + '">' + initials + '</div><div><div class="emp-name-text">' + emp.nama + '</div><div style="display:flex;align-items:center;gap:5px;margin-top:2px"><span class="emp-pin-text">PIN: ' + emp.pin + '</span><span class="date-day-badge ' + hariCss + '">' + namaHari + '</span></div></div></div></td>';
            if (emp.absent) {
                tr.innerHTML = noCell + empCell + '<td><span class="badge-absent">Tidak Masuk</span></td><td><span class="time-none">—</span></td><td><span class="status-pill sp-absent">⚠ Absen</span></td>';
            } else {
                var jm = emp.masuk
                    ? (emp.terlambat
                        ? '<span class="time-badge time-late"><svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="9"/><path d="M12 8v4"/><circle cx="12" cy="16" r=".8" fill="#d97706"/></svg>' + emp.masuk + '</span>'
                        : '<span class="time-badge time-ok"><svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="9"/><path d="M9 12l2 2 4-4"/></svg>' + emp.masuk + '</span>')
                    : '<span class="time-none">—</span>';
                var jp = emp.pulang
                    ? (emp.pulangCepat
                        ? '<span class="time-badge time-early"><svg viewBox="0 0 24 24"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>' + emp.pulang + '</span>'
                        : '<span class="time-badge time-ok"><svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="9"/><path d="M9 12l2 2 4-4"/></svg>' + emp.pulang + '</span>')
                    : '<span class="time-badge time-ok"><svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="9"/><path d="M9 12l2 2 4-4"/></svg>16:00:00</span>';
                var st = emp.terlambat   ? '<span class="status-pill sp-lambat">⚠ Terlambat</span>'
                       : emp.pulangCepat ? '<span class="status-pill sp-cepat">↩ Pulang Cepat</span>'
                       : emp.masuk       ? '<span class="status-pill sp-tepat">✓ Tepat Waktu</span>'
                       : '<span class="time-none">—</span>';
                tr.innerHTML = noCell + empCell + '<td>' + jm + '</td><td>' + jp + '</td><td>' + st + '</td>';
            }
            tbody.appendChild(tr);
        });
    }
    var jH = rows.filter(function (r) { return !r.absent; }).length;
    var jL = rows.filter(function (r) { return r.terlambat; }).length;
    var jA = rows.filter(function (r) { return r.absent; }).length;
    document.getElementById('pgInfo').innerHTML = '📅 <strong style="color:#fff">' + namaHari + ', ' + p.hari + ' ' + NAMA_BULAN[p.bulan] + ' ' + p.tahun + '</strong> &nbsp;| <span style="color:#4ade80;font-weight:600">✓ Hadir: ' + jH + '</span> &nbsp;<span style="color:#fbbf24;font-weight:600">⚠ Lambat: ' + jL + '</span> &nbsp;<span style="color:#c084fc;font-weight:600">✕ Absen: ' + jA + '</span>';
    document.getElementById('pgBtns').innerHTML = '';
}

/* ══ BUILD GROUPED (per karyawan, multi hari) ══ */
function buildGrouped(pr, p) {
    var datesInPeriod = {};
    pr.forEach(function (r) { if (!datesInPeriod[r.tanggal]) datesInPeriod[r.tanggal] = r.tanggalFmt; });
    var allDates = Object.keys(datesInPeriod).sort();
    var allK = {};
    RAW_DATA.forEach(function (r) { if (!allK[r.pin]) allK[r.pin] = r.nama; });
    var kMap = {};
    Object.keys(allK).forEach(function (pin) {
        kMap[pin] = { pin: pin, nama: allK[pin], days: {} };
        allDates.forEach(function (dt) {
            kMap[pin].days[dt] = { tanggal: dt, tanggalFmt: datesInPeriod[dt], masuk: null, pulang: null, terlambat: false, pulangCepat: false, absent: true };
        });
    });
    pr.forEach(function (r) {
        if (!kMap[r.pin]) kMap[r.pin] = { pin: r.pin, nama: r.nama, days: {} };
        if (!kMap[r.pin].days[r.tanggal]) kMap[r.pin].days[r.tanggal] = { tanggal: r.tanggal, tanggalFmt: r.tanggalFmt, masuk: null, pulang: null, terlambat: false, pulangCepat: false, absent: true };
        var d = kMap[r.pin].days[r.tanggal];
        if (r.isMasuk) { d.absent = false; if (!d.masuk || r.waktu < d.masuk) { d.masuk = r.waktu; d.terlambat = r.terlambat; } }
        else { if (!d.pulang || r.waktu > d.pulang) { d.pulang = r.waktu; d.pulangCepat = r.pulangCepat; } }
    });
    var result = [];
    Object.keys(kMap).forEach(function (pin) {
        var k = kMap[pin];
        var days = Object.values(k.days).sort(function (a, b) { return a.tanggal < b.tanggal ? -1 : 1; });
        if (allDates.length > 0) result.push({ pin: k.pin, nama: k.nama, days: days });
    });
    return result.sort(function (a, b) { return a.nama < b.nama ? -1 : 1; });
}

/* ══ FILTER & RENDER (halaman Data Presensi) ══ */
function applyFiltersAndRender() {
    var name  = document.getElementById('searchName').value.toLowerCase().trim();
    var dateF = document.getElementById('filterDate').value;
    var ket   = document.getElementById('filterKet').value;
    var p     = getPeriod();
    if (dateF) {
        var pts = dateF.split('-');
        p.tahun = parseInt(pts[0]); p.bulan = parseInt(pts[1]); p.hari = parseInt(pts[2]);
    }
    var pr = filterByPeriod(RAW_DATA, p);
    if (viewMode === 'harian') {
        if (name) pr = pr.filter(function (r) { return r.nama.toLowerCase().indexOf(name) !== -1; });
        renderDailyAllEmployees(pr, p);
        return;
    }
    var baseRecords = dateF ? RAW_DATA.filter(function (r) { return r.tanggal === dateF; }) : pr;
    var grouped = buildGrouped(baseRecords, p);
    if (name)  grouped = grouped.filter(function (k) { return k.nama.toLowerCase().indexOf(name) !== -1; });
    if (dateF) grouped = grouped.map(function (k) { return { pin: k.pin, nama: k.nama, days: k.days.filter(function (d) { return d.tanggal === dateF; }) }; }).filter(function (k) { return k.days.length > 0; });
    if (ket)   grouped = grouped.map(function (k) { return { pin: k.pin, nama: k.nama, days: k.days.filter(function (d) { if (ket === 'terlambat') return d.terlambat; if (ket === 'cepat') return d.pulangCepat; if (ket === 'tepat') return d.masuk && !d.terlambat; if (ket === 'absent') return d.absent; return true; }) }; }).filter(function (k) { return k.days.length > 0; });
    currentEmpPage = 1;
    renderTable(grouped);
}

function resetFilter() {
    document.getElementById('searchName').value = '';
    document.getElementById('filterDate').value = '';
    document.getElementById('filterKet').value  = '';
    applyFiltersAndRender();
}

function onFilterDateChange() {
    var dateF = document.getElementById('filterDate').value;
    if (dateF) {
        var pts = dateF.split('-'), y = parseInt(pts[0]), m = parseInt(pts[1]), d = parseInt(pts[2]);
        var selY = document.getElementById('filterTahun');
        for (var i = 0; i < selY.options.length; i++) { if (parseInt(selY.options[i].value) === y) { selY.selectedIndex = i; break; } }
        document.getElementById('filterBulan').value = m;
        if (viewMode === 'harian') { populateHari(y, m); document.getElementById('filterHari').value = d; }
        onPeriodChange();
    }
    applyFiltersAndRender();
}

/* ══ RENDER TABLE (per karyawan) ══ */
function renderTable(grouped) {
    document.getElementById('tableHead').innerHTML = '<tr><th>No</th><th>Tanggal</th><th>Jam Masuk</th><th>Jam Pulang</th><th>Status</th></tr>';
    document.getElementById('karyawanNavStrip').style.display = '';
    document.getElementById('empTableHeader').style.display   = '';
    var tbody   = document.getElementById('tableBody');
    var pgBtns  = document.getElementById('pgBtns');
    var pgInfo  = document.getElementById('pgInfo');
    var navStrip = document.getElementById('karyawanNavStrip');
    tbody.innerHTML = '';
    var total     = grouped.length;
    if (currentEmpPage > total) currentEmpPage = Math.max(1, total);
    var totalDays = grouped.reduce(function (s, k) { return s + k.days.length; }, 0);
    document.getElementById('countChip').textContent = total + ' karyawan · ' + totalDays + ' hari';
    navStrip.innerHTML = '<span class="karyawan-nav-label">Karyawan:</span>';
    grouped.forEach(function (k, idx) {
        var btn = document.createElement('button');
        btn.className = 'karyawan-nav-btn' + (idx + 1 === currentEmpPage ? ' active' : '');
        btn.textContent = k.nama.split(' ')[0]; btn.title = k.nama;
        btn.onclick = (function (i) { return function () { currentEmpPage = i + 1; renderTable(grouped); }; })(idx);
        navStrip.appendChild(btn);
    });
    if (!total) {
        resetEmpHeader();
        tbody.innerHTML = '<tr><td colspan="5"><div class="empty"><div class="empty-icon">🔍</div><div class="empty-text">Tidak ada data</div><div class="empty-sub">Pilih periode lain atau ubah filter</div></div></td></tr>';
        pgInfo.textContent = 'Tidak ada data'; pgBtns.innerHTML = '';
        return;
    }
    var k = grouped[currentEmpPage - 1];
    updateEmpHeader(k, currentEmpPage - 1);
    k.days.forEach(function (d, idx) {
        var tr = document.createElement('tr'); tr.style.animation = 'rowIn .2s ' + (idx * 0.02) + 's ease both';
        var noCell = '<td style="color:#475569;font-size:12px;font-family:\'JetBrains Mono\',monospace">' + String(idx + 1).padStart(2, '0') + '</td>';
        if (d.absent) {
            tr.className = 'row-absent';
            tr.innerHTML = noCell + '<td style="font-size:12px;color:#94a3b8">' + d.tanggalFmt + '</td><td><span class="badge-absent">Tidak Masuk</span></td><td><span class="time-none">—</span></td><td><span class="status-pill sp-absent">⚠ Absen</span></td>';
        } else {
            var jm = d.masuk
                ? (d.terlambat
                    ? '<span class="time-badge time-late"><svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="9"/><path d="M12 8v4"/><circle cx="12" cy="16" r=".8" fill="#d97706"/></svg>' + d.masuk + '</span>'
                    : '<span class="time-badge time-ok"><svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="9"/><path d="M9 12l2 2 4-4"/></svg>' + d.masuk + '</span>')
                : '<span class="time-none">—</span>';
            var jp = d.pulang
                ? (d.pulangCepat
                    ? '<span class="time-badge time-early"><svg viewBox="0 0 24 24"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>' + d.pulang + '</span>'
                    : '<span class="time-badge time-ok"><svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="9"/><path d="M9 12l2 2 4-4"/></svg>' + d.pulang + '</span>')
                : '<span class="time-badge time-ok"><svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="9"/><path d="M9 12l2 2 4-4"/></svg>16:00:00</span>';
            var st = d.terlambat   ? '<span class="status-pill sp-lambat">⚠ Terlambat</span>'
                   : d.pulangCepat ? '<span class="status-pill sp-cepat">↩ Pulang Cepat</span>'
                   : d.masuk       ? '<span class="status-pill sp-tepat">✓ Tepat Waktu</span>'
                   : '<span class="time-none">—</span>';
            tr.innerHTML = noCell + '<td style="font-size:12px;color:#94a3b8">' + d.tanggalFmt + '</td><td>' + jm + '</td><td>' + jp + '</td><td>' + st + '</td>';
        }
        tbody.appendChild(tr);
    });
    pgInfo.textContent = 'Karyawan ' + currentEmpPage + ' dari ' + total;
    pgBtns.innerHTML = '';
    function mkBtn(label, page, disabled, active) { var b = document.createElement('button'); b.className = 'pg-btn' + (active ? ' pg-active' : ''); b.innerHTML = label; b.disabled = disabled; b.onclick = function () { currentEmpPage = page; renderTable(grouped); }; pgBtns.appendChild(b); }
    function mkE() { var s = document.createElement('span'); s.className = 'pg-ellipsis'; s.textContent = '…'; pgBtns.appendChild(s); }
    mkBtn('&#8592;', currentEmpPage - 1, currentEmpPage === 1, false);
    var pages = [];
    if (total <= 7) { for (var i = 1; i <= total; i++) pages.push(i); }
    else { pages.push(1); if (currentEmpPage > 3) pages.push('…'); var lo = Math.max(2, currentEmpPage - 1), hi = Math.min(total - 1, currentEmpPage + 1); for (var j = lo; j <= hi; j++) pages.push(j); if (currentEmpPage < total - 2) pages.push('…'); pages.push(total); }
    pages.forEach(function (pg) { if (pg === '…') mkE(); else mkBtn(pg, pg, false, pg === currentEmpPage); });
    mkBtn('&#8594;', currentEmpPage + 1, currentEmpPage === total, false);
}

function updateEmpHeader(k, colorIdx) {
    var color = PALETTE[colorIdx % PALETTE.length];
    var av = document.getElementById('empTableAvatar'); av.textContent = k.nama.substring(0, 2).toUpperCase(); av.style.background = color;
    document.getElementById('empTableName').textContent = k.nama;
    document.getElementById('empTablePin').textContent  = 'PIN: ' + k.pin;
    document.getElementById('empTableDays').textContent = k.days.length + ' hari';
    var tepat = 0, lambat = 0, cepat = 0, absent = 0;
    k.days.forEach(function (d) { if (d.absent) absent++; else { if (d.terlambat) lambat++; else if (d.masuk) tepat++; if (d.pulangCepat) cepat++; } });
    var html = '';
    if (tepat)  html += '<div class="esp esp-tepat">✓ '  + tepat  + ' Tepat</div>';
    if (lambat) html += '<div class="esp esp-lambat">⚠ ' + lambat + ' Lambat</div>';
    if (cepat)  html += '<div class="esp esp-cepat">↩ '  + cepat  + ' Cepat</div>';
    if (absent) html += '<div class="esp esp-absent">✕ ' + absent + ' Absen</div>';
    document.getElementById('empTableStats').innerHTML = html;
}
function resetEmpHeader() {
    document.getElementById('empTableAvatar').textContent = '??';
    document.getElementById('empTableAvatar').style.background = 'var(--accent)';
    ['empTableName', 'empTablePin'].forEach(function (id) { document.getElementById(id).textContent = '—'; });
    document.getElementById('empTableDays').textContent = '0 hari';
    document.getElementById('empTableStats').innerHTML = '';
}

/* ══ PERFORMA PAGE ══ */
function getEmpsForPeriod(tahun, bulan) {
    var allPins = {};
    RAW_DATA.filter(function (r) { return r.tahun === tahun; }).forEach(function (r) { if (!allPins[r.pin]) allPins[r.pin] = { pin: r.pin, nama: r.nama }; });
    var relevant = RAW_DATA.filter(function (r) { if (r.tahun !== tahun) return false; if (bulan > 0 && r.bulan !== bulan) return false; return true; });
    var workDates = {};
    relevant.forEach(function (r) { workDates[r.tanggal] = true; });
    var totalWD = Object.keys(workDates).length, pinStats = {};
    relevant.forEach(function (r) {
        if (!pinStats[r.pin]) pinStats[r.pin] = { dateSet: {} };
        if (!pinStats[r.pin].dateSet[r.tanggal]) pinStats[r.pin].dateSet[r.tanggal] = { masuk: false, terlambat: false, pulangCepat: false };
        if (r.isMasuk) { pinStats[r.pin].dateSet[r.tanggal].masuk = true; if (r.terlambat) pinStats[r.pin].dateSet[r.tanggal].terlambat = true; }
        else { if (r.pulangCepat) pinStats[r.pin].dateSet[r.tanggal].pulangCepat = true; }
    });
    return Object.keys(allPins).map(function (pin) {
        var info = allPins[pin], sd = pinStats[pin] ? Object.values(pinStats[pin].dateSet) : [];
        var hadir = sd.filter(function (d) { return d.masuk; }).length;
        return { pin: info.pin, nama: info.nama, hadir: hadir, lambat: sd.filter(function (d) { return d.terlambat; }).length, cepat: sd.filter(function (d) { return d.pulangCepat; }).length, absent: Math.max(0, totalWD - hadir), totalWD: totalWD };
    }).sort(function (a, b) { return a.nama < b.nama ? -1 : 1; });
}

function renderPerfPage() {
    var tahun = parseInt(document.getElementById('filterTahunPerf').value);
    var bulan = parseInt(document.getElementById('filterBulanPerf').value);
    document.getElementById('perfPeriodHint').textContent = bulan === 0 ? '📅 Seluruh tahun ' + tahun : '📅 ' + NAMA_BULAN[bulan] + ' ' + tahun;
    var emps = getEmpsForPeriod(tahun, bulan);
    document.getElementById('empCountChip').textContent = emps.length + ' karyawan';
    renderEmpSelector(emps, tahun, bulan);
    if (selectedEmpPin) {
        var emp = emps.find(function (e) { return e.pin === selectedEmpPin; });
        if (emp) buildBarChart(selectedEmpPin, emp.nama, selectedEmpColor, tahun, bulan);
        else { document.getElementById('empChartCard').classList.remove('show'); document.getElementById('empChartHint').style.display = ''; selectedEmpPin = null; }
    }
}

function renderEmpSelector(emps, tahun, bulan) {
    var grid = document.getElementById('empSelectorGrid'); grid.innerHTML = '';
    if (!emps.length) { grid.innerHTML = '<div class="empty"><div class="empty-icon">📊</div><div class="empty-text">Tidak ada data</div></div>'; return; }
    emps.forEach(function (emp, i) {
        var color = PALETTE[i % PALETTE.length], card = document.createElement('div');
        card.className = 'emp-card' + (emp.pin === selectedEmpPin ? ' selected' : '');
        card.innerHTML = '<div class="emp-card-badge"><svg viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg></div>'
            + '<div class="emp-card-header"><div class="emp-card-avatar" style="background:' + color + '">' + emp.nama.substring(0, 2).toUpperCase() + '</div>'
            + '<div style="min-width:0"><div class="emp-card-name" title="' + emp.nama + '">' + emp.nama + '</div><div class="emp-card-pin">PIN: ' + emp.pin + '</div></div></div>'
            + '<div class="emp-card-stats">'
            + '<div class="emp-card-stat"><div class="emp-card-stat-num" style="color:' + (emp.hadir > 0 ? color : '#475569') + '">' + emp.hadir + '</div><div class="emp-card-stat-lbl">Hadir</div></div>'
            + '<div class="emp-card-stat"><div class="emp-card-stat-num" style="color:' + (emp.lambat > 0 ? '#fbbf24' : '#475569') + '">' + emp.lambat + '</div><div class="emp-card-stat-lbl">Lambat</div></div>'
            + '<div class="emp-card-stat"><div class="emp-card-stat-num" style="color:' + (emp.cepat > 0 ? '#fb7185' : '#475569') + '">' + emp.cepat + '</div><div class="emp-card-stat-lbl">Cepat</div></div>'
            + '<div class="emp-card-stat"><div class="emp-card-stat-num" style="color:' + (emp.absent > 0 ? '#c084fc' : '#475569') + '">' + emp.absent + '</div><div class="emp-card-stat-lbl">Absen</div></div>'
            + '</div>';
        (function (pin, nama, col, t, b, el) {
            el.onclick = function () {
                selectedEmpPin = pin; selectedEmpColor = col;
                document.querySelectorAll('.emp-card').forEach(function (c) { c.classList.remove('selected'); });
                el.classList.add('selected');
                buildBarChart(pin, nama, col, t, b);
            };
        })(emp.pin, emp.nama, color, tahun, bulan, card);
        grid.appendChild(card);
    });
}

function getMonthlyData(pin, tahun) {
    var mMap = {}, mWD = {};
    RAW_DATA.filter(function (r) { return r.tahun === tahun; }).forEach(function (r) { if (!mWD[r.bulan]) mWD[r.bulan] = {}; mWD[r.bulan][r.tanggal] = true; });
    RAW_DATA.filter(function (r) { return r.pin === pin && r.tahun === tahun; }).forEach(function (r) {
        if (!mMap[r.bulan]) mMap[r.bulan] = {};
        if (!mMap[r.bulan][r.tanggal]) mMap[r.bulan][r.tanggal] = { masuk: null, pulang: null, terlambat: false, pulangCepat: false };
        var d = mMap[r.bulan][r.tanggal];
        if (r.isMasuk) { if (!d.masuk || r.waktu < d.masuk) { d.masuk = r.waktu; d.terlambat = r.terlambat; } }
        else           { if (!d.pulang || r.waktu > d.pulang) { d.pulang = r.waktu; d.pulangCepat = r.pulangCepat; } }
    });
    var result = [];
    for (var m = 1; m <= 12; m++) {
        if (!mWD[m]) continue;
        var days = mMap[m] ? Object.values(mMap[m]) : [];
        var hadir = days.filter(function (d) { return d.masuk; }).length;
        result.push({ bulan: m, label: NAMA_BULAN_S[m], hadir: hadir, lambat: days.filter(function (d) { return d.terlambat; }).length, cepat: days.filter(function (d) { return d.pulangCepat; }).length, tepat: days.filter(function (d) { return d.masuk && !d.terlambat; }).length, absent: Math.max(0, Object.keys(mWD[m]).length - hadir) });
    }
    return result;
}

function getDailyData(pin, tahun, bulan) {
    var dMap = {}, allDates = {};
    RAW_DATA.filter(function (r) { return r.tahun === tahun && r.bulan === bulan; }).forEach(function (r) { allDates[r.tanggal] = r.tanggalFmt; });
    RAW_DATA.filter(function (r) { return r.pin === pin && r.tahun === tahun && r.bulan === bulan; }).forEach(function (r) {
        if (!dMap[r.tanggal]) dMap[r.tanggal] = { masuk: null, pulang: null, terlambat: false, pulangCepat: false };
        var d = dMap[r.tanggal];
        if (r.isMasuk) { if (!d.masuk || r.waktu < d.masuk) { d.masuk = r.waktu; d.terlambat = r.terlambat; } }
        else           { if (!d.pulang || r.waktu > d.pulang) { d.pulang = r.waktu; d.pulangCepat = r.pulangCepat; } }
    });
    return Object.keys(allDates).sort().map(function (dt) {
        var d = dMap[dt] || { masuk: null, terlambat: false, pulangCepat: false };
        return { tanggal: dt, label: String(parseInt(dt.split('-')[2])), hadir: d.masuk ? 1 : 0, lambat: d.terlambat ? 1 : 0, cepat: d.pulangCepat ? 1 : 0, tepat: (d.masuk && !d.terlambat) ? 1 : 0, absent: d.masuk ? 0 : 1 };
    });
}

function setBarMode(mode) {
    barMode = mode;
    ['btnModeAll', 'btnModeHadir', 'btnModeLate'].forEach(function (id) { document.getElementById(id).classList.remove('active'); });
    document.getElementById({ all: 'btnModeAll', hadir: 'btnModeHadir', late: 'btnModeLate' }[mode]).classList.add('active');
    if (selectedEmpPin) {
        var tahun = parseInt(document.getElementById('filterTahunPerf').value);
        var bulan = parseInt(document.getElementById('filterBulanPerf').value);
        buildBarChart(selectedEmpPin, document.getElementById('bcName').textContent, selectedEmpColor, tahun, bulan);
    }
}

function buildCustomLegend(activeKeys) {
    var c = document.getElementById('chartLegendCustom'); c.innerHTML = '';
    activeKeys.forEach(function (key) {
        var cfg = BC[key]; var item = document.createElement('div'); item.className = 'cl-item';
        var ind = cfg.type === 'line' ? '<div class="cl-line" style="background:' + cfg.hex + '"></div>' : '<div class="cl-dot" style="background:' + cfg.bg + ';border:2px solid ' + cfg.border + '"></div>';
        item.innerHTML = ind + '<span>' + cfg.label + '</span>';
        c.appendChild(item);
    });
}

function buildBarChart(pin, nama, color, tahun, bulan) {
    var isDaily = bulan > 0;
    var data    = isDaily ? getDailyData(pin, tahun, bulan) : getMonthlyData(pin, tahun);
    document.getElementById('empChartCard').classList.add('show');
    document.getElementById('empChartHint').style.display = 'none';
    var av = document.getElementById('bcAvatar'); av.textContent = nama.substring(0, 2).toUpperCase(); av.style.background = color;
    document.getElementById('bcName').textContent = nama;
    document.getElementById('bcPin').textContent  = 'PIN: ' + pin + ' · ' + (isDaily ? NAMA_BULAN[bulan] + ' ' + tahun : 'Tahun ' + tahun);
    var yH = 0, yL = 0, yE = 0, yT = 0, yA = 0;
    data.forEach(function (m) { yH += m.hadir; yL += m.lambat; yE += m.cepat; yT += m.tepat; yA += m.absent; });
    renderYearlyStat(yH, yL, yE, yT, yA, isDaily ? NAMA_BULAN[bulan] + ' ' + tahun : 'Tahun ' + tahun);
    if (!data.length) { if (empBarChart) { empBarChart.destroy(); empBarChart = null; } return; }
    if (empBarChart) { empBarChart.destroy(); empBarChart = null; }
    var ctx = document.getElementById('empBarChart').getContext('2d'), datasets = [], activeKeys = [];
    if (barMode === 'all' || barMode === 'hadir') {
        datasets.push({ label: BC.hadir.label, type: 'bar',  data: data.map(function (m) { return m.hadir; }), backgroundColor: BC.hadir.bg,  borderColor: BC.hadir.border,  borderWidth: 1.5, borderRadius: 6, borderSkipped: false, order: 2 }); activeKeys.push('hadir');
        datasets.push({ label: BC.tepat.label, type: 'bar',  data: data.map(function (m) { return m.tepat; }), backgroundColor: BC.tepat.bg,  borderColor: BC.tepat.border,  borderWidth: 1.5, borderRadius: 6, borderSkipped: false, order: 3 }); activeKeys.push('tepat');
        datasets.push({ label: BC.absent.label, type: 'bar', data: data.map(function (m) { return m.absent; }),backgroundColor: BC.absent.bg, borderColor: BC.absent.border, borderWidth: 1.5, borderRadius: 6, borderSkipped: false, order: 4 }); activeKeys.push('absent');
        datasets.push({ label: BC.tren.label, type: 'line',  data: data.map(function (m) { return m.hadir; }), borderColor: BC.tren.border, backgroundColor: BC.tren.bg, borderWidth: 2.5, tension: .4, pointRadius: 4, pointBackgroundColor: BC.tren.border, pointBorderColor: '#fff', pointBorderWidth: 2, fill: true, order: 1 }); activeKeys.push('tren');
    }
    if (barMode === 'all' || barMode === 'late') {
        datasets.push({ label: BC.lambat.label, type: 'bar', data: data.map(function (m) { return m.lambat; }), backgroundColor: BC.lambat.bg, borderColor: BC.lambat.border, borderWidth: 1.5, borderRadius: 6, borderSkipped: false, order: 5 }); activeKeys.push('lambat');
        datasets.push({ label: BC.cepat.label,  type: 'bar', data: data.map(function (m) { return m.cepat; }),  backgroundColor: BC.cepat.bg,  borderColor: BC.cepat.border,  borderWidth: 1.5, borderRadius: 6, borderSkipped: false, order: 6 }); activeKeys.push('cepat');
    }
    buildCustomLegend(activeKeys);
    empBarChart = new Chart(ctx, {
        type: 'bar',
        data: { labels: data.map(function (m) { return m.label; }), datasets: datasets },
        options: {
            responsive: true, maintainAspectRatio: false,
            interaction: { mode: 'index', intersect: false },
            plugins: {
                legend: { display: false },
                tooltip: {
                    callbacks: {
                        title: function (items) { var m = data[items[0].dataIndex]; return isDaily ? m.tanggal : NAMA_BULAN[m.bulan] + ' ' + tahun; },
                        label: function (c) { return ' ' + c.dataset.label + ': ' + c.raw + ' hari'; }
                    },
                    padding: 12, cornerRadius: 10, backgroundColor: 'rgba(15,23,42,0.95)',
                    titleColor: '#c7d2fe', bodyColor: '#e0e7ff', borderColor: 'rgba(99,102,241,0.3)', borderWidth: 1
                }
            },
            scales: {
                x: { grid: { display: false }, ticks: { font: { family: 'Inter', size: 11 }, color: '#64748b' }, border: { display: false } },
                y: { beginAtZero: true, grid: { color: 'rgba(255,255,255,0.05)' }, ticks: { font: { family: 'JetBrains Mono', size: 10 }, color: '#64748b', stepSize: 1, callback: function (v) { return Number.isInteger(v) ? v : ''; } }, border: { display: false } }
            },
            animation: { duration: 600 }
        }
    });
    setTimeout(function () { document.getElementById('empChartCard').scrollIntoView({ behavior: 'smooth', block: 'nearest' }); }, 80);
}

function renderYearlyStat(h, l, e, t, a, label) {
    document.getElementById('yearlyStatStrip').innerHTML =
        (label ? '<div style="width:100%;font-size:11px;font-weight:600;color:#94a3b8;text-transform:uppercase;letter-spacing:.06em;padding-bottom:6px">Ringkasan ' + label + '</div>' : '')
        + ys('ys-hadir', h, 'Total Hadir') + ys('ys-tepat', t, 'Tepat Waktu') + ys('ys-lambat', l, 'Terlambat') + ys('ys-cepat', e, 'Pulang Cepat') + ys('ys-absent', a, 'Tidak Masuk');
}
function ys(cls, val, lbl) {
    var colors  = { 'ys-hadir': '#4ade80', 'ys-lambat': '#fbbf24', 'ys-cepat': '#fb7185', 'ys-tepat': '#818cf8', 'ys-absent': '#c084fc' };
    var bgs     = { 'ys-hadir': 'rgba(34,197,94,0.15)', 'ys-lambat': 'rgba(245,158,11,0.15)', 'ys-cepat': 'rgba(244,63,94,0.15)', 'ys-tepat': 'rgba(99,102,241,0.15)', 'ys-absent': 'rgba(168,85,247,0.15)' };
    var borders = { 'ys-hadir': 'rgba(34,197,94,0.3)',  'ys-lambat': 'rgba(245,158,11,0.3)',  'ys-cepat': 'rgba(244,63,94,0.3)',  'ys-tepat': 'rgba(99,102,241,0.3)',  'ys-absent': 'rgba(168,85,247,0.3)' };
    return '<div class="ys-pill" style="background:' + bgs[cls] + ';border:1px solid ' + borders[cls] + '"><div><div class="ys-val" style="color:' + colors[cls] + '">' + val + '</div><div class="ys-lbl">' + lbl + '</div></div></div>';
}

/* ══ CETAK LAPORAN ══ */
function cetakLaporan() {
    var p   = getPeriod(), lbl = periodLabel(p), pr = filterByPeriod(RAW_DATA, p);
    var grouped = [], rowsHtml = '', totalRows = 0;
    if (viewMode === 'harian') {
        var allK = {}; RAW_DATA.forEach(function (r) { if (!allK[r.pin]) allK[r.pin] = r.nama; });
        var dayData = {};
        Object.keys(allK).forEach(function (pin) { dayData[pin] = { pin: pin, nama: allK[pin], masuk: null, pulang: null, terlambat: false, pulangCepat: false, absent: true }; });
        pr.forEach(function (r) {
            var d = dayData[r.pin]; if (!d) return;
            if (r.isMasuk) { d.absent = false; if (!d.masuk || r.waktu < d.masuk) { d.masuk = r.waktu; d.terlambat = r.terlambat; } }
            else { if (!d.pulang || r.waktu > d.pulang) { d.pulang = r.waktu; d.pulangCepat = r.pulangCepat; } }
        });
        grouped = Object.values(dayData).sort(function (a, b) { return a.nama < b.nama ? -1 : 1; });
        grouped.forEach(function (emp) {
            totalRows++;
            var st = emp.absent ? 'Tidak Masuk' : emp.terlambat ? 'Terlambat' : emp.pulangCepat ? 'Pulang Cepat' : 'Tepat Waktu';
            var sc = emp.absent ? '#7c3aed' : emp.terlambat ? '#d97706' : emp.pulangCepat ? '#e11d48' : '#16a34a';
            rowsHtml += '<tr><td style="text-align:center">' + totalRows + '</td><td>' + emp.nama + '</td><td style="font-family:monospace">' + emp.pin + '</td><td style="font-family:monospace">' + (emp.masuk || '—') + '</td><td style="font-family:monospace">' + (emp.pulang || (emp.absent ? '—' : '16:00:00')) + '</td><td style="color:' + sc + ';font-weight:700">' + st + '</td></tr>';
        });
    } else {
        grouped = buildGrouped(pr, p);
        grouped.forEach(function (k) {
            k.days.forEach(function (d) {
                totalRows++;
                var st = d.absent ? 'Tidak Masuk' : d.terlambat ? 'Terlambat' : d.pulangCepat ? 'Pulang Cepat' : d.masuk ? 'Tepat Waktu' : '—';
                var sc = d.absent ? '#7c3aed' : d.terlambat ? '#d97706' : d.pulangCepat ? '#e11d48' : d.masuk ? '#16a34a' : '#999';
                rowsHtml += '<tr><td style="text-align:center">' + totalRows + '</td><td>' + d.tanggalFmt + '</td><td>' + k.nama + '</td><td style="font-family:monospace">' + k.pin + '</td><td style="font-family:monospace">' + (d.masuk || '—') + '</td><td style="font-family:monospace">' + (d.pulang || (d.masuk ? '16:00:00' : '—')) + '</td><td style="color:' + sc + ';font-weight:700">' + st + '</td></tr>';
            });
        });
    }
    var total  = pr.length;
    var tepat  = pr.filter(function (r) { return r.isMasuk && !r.terlambat; }).length;
    var lambat = pr.filter(function (r) { return r.terlambat; }).length;
    var cepat  = pr.filter(function (r) { return r.pulangCepat; }).length;
    var absent = hitungAbsen(pr, p);
    var headerCols = viewMode === 'harian'
        ? '<th>No</th><th>Nama Karyawan</th><th>PIN</th><th>Jam Masuk</th><th>Jam Pulang</th><th>Status</th>'
        : '<th>No</th><th>Tanggal</th><th>Nama Karyawan</th><th>PIN</th><th>Jam Masuk</th><th>Jam Pulang</th><th>Status</th>';
    var colSpan = viewMode === 'harian' ? 6 : 7;
    var win = window.open('', '_blank', 'width=960,height=720');
    if (!win) { alert('Popup diblokir. Izinkan popup untuk domain ini.'); return; }
    win.document.write('<!DOCTYPE html><html lang="id"><head><meta charset="UTF-8"><title>Laporan Absensi — ' + lbl + '</title><style>body{font-family:Arial,sans-serif;font-size:12px;color:#111;margin:20px}.header{text-align:center;margin-bottom:18px;border-bottom:2px solid #1e1b4b;padding-bottom:12px}.header h2{font-size:17px;margin:0 0 5px;color:#1e1b4b}.header p{font-size:11px;color:#555;margin:2px 0}.summary{display:flex;gap:8px;margin-bottom:16px;flex-wrap:wrap}.sum-box{border:1px solid #ddd;border-radius:6px;padding:8px 16px;text-align:center;flex:1;min-width:80px}.sum-num{font-size:20px;font-weight:700;margin-bottom:2px}.sum-lbl{font-size:10px;color:#666;text-transform:uppercase;letter-spacing:.06em}table{width:100%;border-collapse:collapse;font-size:11px}th{background:#1e1b4b;color:#fff;padding:8px 10px;text-align:left;font-size:10px;text-transform:uppercase;letter-spacing:.06em;border:1px solid #1e1b4b}td{padding:7px 10px;border:1px solid #e0e0e0;vertical-align:middle}tr:nth-child(even) td{background:#f8f8ff}.footer{text-align:center;margin-top:16px;font-size:10px;color:#999;border-top:1px solid #eee;padding-top:8px}@page{size:A4;margin:1.5cm}@media print{body{margin:0;-webkit-print-color-adjust:exact;print-color-adjust:exact}}</style></head><body>'
        + '<div class="header"><h2>&#128197; Laporan Absensi Karyawan</h2><p>Periode: <strong>' + lbl + '</strong></p><p>Dicetak: ' + new Date().toLocaleDateString('id-ID', { weekday: 'long', day: 'numeric', month: 'long', year: 'numeric' }) + ' pukul ' + new Date().toLocaleTimeString('id-ID') + '</p></div>'
        + '<div class="summary">'
        + '<div class="sum-box"><div class="sum-num" style="color:#6366f1">' + total + '</div><div class="sum-lbl">Total Absensi</div></div>'
        + '<div class="sum-box"><div class="sum-num" style="color:#16a34a">' + tepat + '</div><div class="sum-lbl">Tepat Waktu</div></div>'
        + '<div class="sum-box"><div class="sum-num" style="color:#d97706">' + lambat + '</div><div class="sum-lbl">Terlambat</div></div>'
        + '<div class="sum-box"><div class="sum-num" style="color:#e11d48">' + cepat + '</div><div class="sum-lbl">Pulang Cepat</div></div>'
        + '<div class="sum-box"><div class="sum-num" style="color:#7c3aed">' + absent + '</div><div class="sum-lbl">Tidak Masuk</div></div>'
        + '</div>'
        + '<table><thead><tr>' + headerCols + '</tr></thead><tbody>' + (rowsHtml || '<tr><td colspan="' + colSpan + '" style="text-align:center;padding:24px;color:#999">Tidak ada data untuk periode ini</td></tr>') + '</tbody></table>'
        + '<div class="footer">Kipin — Sistem Monitoring Absensi &middot; Publik View &nbsp;|&nbsp; Total ' + totalRows + ' baris data</div>'
        + '</body></html>');
    win.document.close(); win.focus(); setTimeout(function () { win.print(); }, 500);
}