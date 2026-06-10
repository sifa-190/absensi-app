// Global State untuk Kipin Engine
let viewMode = 'bulanan'; // 'bulanan' atau 'harian'
let currentPage = 'dashboard'; // 'dashboard' atau 'performa'
let currentKaryawanNavFilter = 'ALL';
let mainTableCurrentPage = 1;
const mainTableRowsPerPage = 15;
let selectedEmpIdForBarChart = null;
let barChartMode = 'status'; // 'status' atau 'jam'
let myDonutChart = null;
let myBarChart = null;

// Jalankan saat halaman selesai dimuat
document.addEventListener('DOMContentLoaded', function () {
    initEngine();
});

function initEngine() {
    setupClock();
    setDefaultPeriod();
    syncUIWithMode();
    processAndRender();
    initEmpSelectorGrid();
}

function setupClock() {
    setInterval(() => {
        const now = new Date();
        const str = now.toTimeString().split(' ')[0];
        const el = document.getElementById('liveClock');
        if (el) el.textContent = str;
    }, 1000);
}

function setDefaultPeriod() {
    const now = new Date();
    document.getElementById('filterTahun').value = now.getFullYear();
    document.getElementById('filterBulan').value = now.getMonth() + 1;
    
    const fDate = document.getElementById('filterDate');
    const y = now.getFullYear();
    const m = String(now.getMonth() + 1).padStart(2, '0');
    const d = String(now.getDate()).padStart(2, '0');
    fDate.value = `${y}-${m}-${d}`;
}

function switchPage(pageName) {
    currentPage = pageName;
    document.querySelectorAll('.sb-item').forEach(b => b.classList.remove('active'));
    document.querySelectorAll('.page').forEach(p => p.classList.remove('active'));

    if (pageName === 'dashboard') {
        document.querySelector("button[onclick=\"switchPage('dashboard')\"]").classList.add('active');
        document.getElementById('pageDashboard').classList.add('active');
        document.getElementById('bcPage').textContent = 'Dashboard';
        document.getElementById('bcSub').textContent = viewMode === 'bulanan' ? 'Ringkasan Absensi Bulanan' : 'Ringkasan Absensi Harian';
        document.getElementById('viewPillsWrap').style.display = 'flex';
    } else {
        document.querySelector("button[onclick=\"switchPage('performa')\"]").classList.add('active');
        document.getElementById('pagePerforma').classList.add('active');
        document.getElementById('bcPage').textContent = 'Analisis Performa';
        document.getElementById('bcSub').textContent = 'Grafik & Tren Karyawan';
        document.getElementById('viewPillsWrap').style.display = 'none';
    }
}

function setViewMode(mode) {
    viewMode = mode;
    document.getElementById('btnViewBulanan').classList.toggle('active', mode === 'bulanan');
    document.getElementById('btnViewHarian').classList.toggle('active', mode === 'harian');
    
    syncUIWithMode();
    mainTableCurrentPage = 1;
    processAndRender();
}

function syncUIWithMode() {
    const th = document.getElementById('filterTahun');
    const bl = document.getElementById('filterBulan');
    const hr = document.getElementById('filterHari');
    const fd = document.getElementById('filterDate');
    const sch = document.getElementById('tableSearch');

    if (viewMode === 'bulanan') {
        th.style.display = 'inline-block';
        bl.style.display = 'inline-block';
        hr.style.display = 'none';
        fd.style.display = 'none';
        sch.style.display = 'inline-block';
        document.getElementById('bcSub').textContent = 'Ringkasan Absensi Bulanan';
        document.getElementById('tblTitle').textContent = 'Rincian Log Absensi Karyawan';
        document.getElementById('karyawanNavStrip').style.display = 'flex';
    } else {
        th.style.display = 'none';
        bl.style.display = 'none';
        hr.style.display = 'none';
        fd.style.display = 'inline-block';
        sch.style.display = 'none';
        document.getElementById('bcSub').textContent = 'Ringkasan Absensi Harian';
        document.getElementById('tblTitle').textContent = 'Daftar Hadir Karyawan Per Hari';
        document.getElementById('karyawanNavStrip').style.display = 'none';
    }
}

function onPeriodDropdownChange() {
    mainTableCurrentPage = 1;
    processAndRender();
}

function onFilterDateChange() {
    const dateF = document.getElementById('filterDate').value;
    if (dateF) {
        const parts = dateF.split('-');
        document.getElementById('filterTahun').value = parseInt(parts[0]);
        document.getElementById('filterBulan').value = parseInt(parts[1]);
        mainTableCurrentPage = 1;
        processAndRender();
    }
}

function onSearchInput() {
    mainTableCurrentPage = 1;
    renderMainTableOnly();
}

function setKaryawanNavFilter(filterType) {
    currentKaryawanNavFilter = filterType;
    document.querySelectorAll('.karyawan-nav-btn').forEach(b => b.classList.remove('active'));
    
    const idMap = { 'ALL': 'knbAll', 'TEPAT': 'knbTepat', 'LAMBAT': 'knbLambat', 'CEPAT': 'knbCepat', 'ABSENT': 'knbAbsent' };
    document.getElementById(idMap[filterType]).classList.add('active');
    
    mainTableCurrentPage = 1;
    renderMainTableOnly();
}

function showLoading() {
    document.getElementById('loadingOverlay').classList.add('show');
}

function getFilteredLogs() {
    const logs = window.kipinRawLogs || [];
    const targetY = parseInt(document.getElementById('filterTahun').value);
    const targetM = parseInt(document.getElementById('filterBulan').value);

    let res = logs.filter(l => l.tahun === targetY && l.bulan === targetM);

    if (viewMode === 'harian') {
        const fd = document.getElementById('filterDate').value;
        if (fd) {
            const parts = fd.split('-');
            const targetD = parseInt(parts[2]);
            res = res.filter(l => l.hari === targetD);
        }
    }
    return res;
}

function processAndRender() {
    const currentLogs = getFilteredLogs();
    
    let total = 0, tepat = 0, lambat = 0, cepat = 0, absent = 0;
    
    currentLogs.forEach(l => {
        total++;
        if (l.status_masuk === 'TEPAT') tepat++;
        else if (l.status_masuk === 'TERLAMBAT') lambat++;
        
        if (l.status_pulang === 'PULANG CEPAT') cepat++;
        if (l.is_mangkir) absent++;
    });

    animateValue('stTotal', total);
    animateValue('stTepat', tepat);
    animateValue('stLambat', lambat);
    animateValue('stCepat', cepat);
    animateValue('stAbsent', absent);

    renderDonutChart(tepat, lambat, cepat, absent, total);
    renderMainTableOnly();
}

function animateValue(id, value) {
    const el = document.getElementById(id);
    if (!el) return;
    el.textContent = value;
    el.classList.remove('pop');
    void el.offsetWidth;
    el.classList.add('pop');
}

function renderDonutChart(tepat, lambat, cepat, absent, total) {
    const ctx = document.getElementById('donutChart');
    if (!ctx) return;

    document.getElementById('donutCenterNum').textContent = total;

    const pTepat = total ? Math.round((tepat/total)*100) : 0;
    const pLambat = total ? Math.round((lambat/total)*100) : 0;
    const pCepat = total ? Math.round((cepat/total)*100) : 0;
    const pAbsent = total ? Math.round((absent/total)*100) : 0;

    document.getElementById('lgTepat').innerHTML = `${tepat} <span class="dl-pct">(${pTepat}%)</span>`;
    document.getElementById('lgLambat').innerHTML = `${lambat} <span class="dl-pct">(${pLambat}%)</span>`;
    document.getElementById('lgCepat').innerHTML = `${cepat} <span class="dl-pct">(${pCepat}%)</span>`;
    document.getElementById('lgAbsent').innerHTML = `${absent} <span class="dl-pct">(${pAbsent}%)</span>`;

    if (myDonutChart) {
        myDonutChart.data.datasets[0].data = [tepat, lambat, cepat, absent];
        myDonutChart.update();
        return;
    }

    myDonutChart = new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: ['Tepat Waktu', 'Terlambat', 'Pulang Cepat', 'Mangkir'],
            datasets: [{
                data: [tepat, lambat, cepat, absent],
                backgroundColor: ['#10b981', '#f59e0b', '#f43f5e', '#a855f7'],
                borderWidth: 2,
                borderColor: '#1e293b',
                hoverOffset: 4
            }]
        },
        options: {
            plugins: { legend: { display: false } },
            cutout: '72%',
            responsive: true,
            maintainAspectRatio: false
        }
    });
}

function highlightChartSection(index) {
    if (!myDonutChart) return;
    const meta = myDonutChart.getDatasetMeta(0);
    meta.data.forEach((element, i) => {
        element.options.outerRadius = (i === index) ? meta.controller.outerRadius + 6 : meta.controller.outerRadius;
    });
    myDonutChart.update();
}

function renderMainTableOnly() {
    const wrap = document.getElementById('mainTableWrap');
    const currentLogs = getFilteredLogs();
    const metaKaryawan = window.kipinKaryawanMeta || {};

    let finalData = [];

    if (viewMode === 'bulanan') {
        const query = document.getElementById('tableSearch').value.toLowerCase().trim();
        Object.keys(metaKaryawan).forEach(pin => {
            const k = metaKaryawan[pin];
            if (query && !k.nama.toLowerCase().includes(query) && !pin.includes(query)) return;

            const kLogs = currentLogs.filter(l => String(l.pin) === String(pin));
            let sTepat = 0, sLambat = 0, sCepat = 0, sAbsent = 0;

            kLogs.forEach(l => {
                if (l.status_masuk === 'TEPAT') sTepat++;
                else if (l.status_masuk === 'TERLAMBAT') sLambat++;
                if (l.status_pulang === 'PULANG CEPAT') sCepat++;
                if (l.is_mangkir) sAbsent++;
            });

            if (currentKaryawanNavFilter === 'TEPAT' && sTepat === 0) return;
            if (currentKaryawanNavFilter === 'LAMBAT' && sLambat === 0) return;
            if (currentKaryawanNavFilter === 'CEPAT' && sCepat === 0) return;
            if (currentKaryawanNavFilter === 'ABSENT' && sAbsent === 0) return;

            finalData.push({ pin, nama: k.nama, sTepat, sLambat, sCepat, sAbsent, total: kLogs.length });
        });

        finalData.sort((a,b) => b.total - a.total);

        // Render HTML Tabel Bulanan
        let html = `<table><thead><tr><th>Karyawan</th><th style="text-align:center">Total Log</th><th style="text-align:center">Tepat Waktu</th><th style="text-align:center">Terlambat</th><th style="text-align:center">Pulang Cepat</th><th style="text-align:center">Mangkir</th></tr></thead><tbody>`;
        
        const paginated = paginateArray(finalData, mainTableCurrentPage, mainTableRowsPerPage);
        
        if (paginated.data.length === 0) {
            html += `<tr><td colspan="6" style="text-align:center;padding:30px;color:var(--text-mute)">Tidak ditemukan karyawan yang cocok</td></tr>`;
        } else {
            paginated.data.forEach(r => {
                const init = r.nama.charAt(0).toUpperCase();
                html += `<tr>
                    <td>
                        <div class="emp-name-cell">
                            <div class="emp-avatar-sm" style="background:#4f46e5">${init}</div>
                            <div>
                                <div class="emp-name-text">${r.nama}</div>
                                <div class="emp-pin-text">PIN: ${r.pin}</div>
                            </div>
                        </div>
                    </td>
                    <td style="text-align:center;font-weight:600">${r.total} hari</td>
                    <td style="text-align:center"><span class="status-pill sp-tepat">${r.sTepat}</span></td>
                    <td style="text-align:center"><span class="status-pill sp-lambat">${r.sLambat}</span></td>
                    <td style="text-align:center"><span class="status-pill sp-cepat">${r.sCepat}</span></td>
                    <td style="text-align:center"><span class="status-pill sp-absent">${r.sAbsent}</span></td>
                </tr>`;
            });
        }
        html += `</tbody></table>`;
        wrap.innerHTML = html;
        renderPager(paginated);

    } else {
        // Mode Harian
        currentLogs.sort((a,b) => {
            if(a.is_mangkir !== b.is_mangkir) return a.is_mangkir - b.is_mangkir;
            if(a.jam_masuk && b.jam_masuk) return a.jam_masuk.localeCompare(b.jam_masuk);
            return 0;
        });

        let html = `<table><thead><tr><th>Hari / Tanggal</th><th>Karyawan</th><th>Jam Masuk</th><th>Jam Pulang</th><th>Keterangan</th></tr></thead><tbody>`;
        const paginated = paginateArray(currentLogs, mainTableCurrentPage, mainTableRowsPerPage);

        if (paginated.data.length === 0) {
            html += `<tr><td colspan="5" style="text-align:center;padding:30px;color:var(--text-mute)">Tidak ada log absensi pada tanggal ini</td></tr>`;
        } else {
            paginated.data.forEach(l => {
                const k = metaKaryawan[l.pin] || { nama: 'PIN: ' + l.pin };
                const dayLower = l.nama_hari.toLowerCase();
                const init = k.nama.charAt(0).toUpperCase();
                
                let inBadge = `<span class="time-badge time-none">-</span>`;
                let outBadge = `<span class="time-badge time-none">-</span>`;
                let rowCls = l.is_mangkir ? 'class="row-absent"' : '';

                if (!l.is_mangkir) {
                    if (l.jam_masuk) {
                        const cls = l.status_masuk === 'TEPAT' ? 'time-ok' : 'time-late';
                        inBadge = `<span class="time-badge ${cls}"><svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>${l.jam_masuk.substring(0,5)}</span>`;
                    }
                    if (l.jam_pulang) {
                        const cls = l.status_pulang === 'NORMAL' ? 'time-ok' : 'time-early';
                        outBadge = `<span class="time-badge ${cls}"><svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>${l.jam_pulang.substring(0,5)}</span>`;
                    }
                } else {
                    inBadge = `<span class="badge-absent">MANGKIR</span>`;
                    outBadge = `<span class="badge-absent">MANGKIR</span>`;
                }

                let ketHtml = '';
                if(l.is_mangkir) ketHtml = `<span class="status-pill sp-absent">Tanpa Keterangan</span>`;
                else if(l.status_masuk === 'TERLAMBAT' && l.status_pulang === 'PULANG CEPAT') ketHtml = `<span class="status-pill sp-cepat">Late + Early Out</span>`;
                else if(l.status_masuk === 'TERLAMBAT') ketHtml = `<span class="status-pill sp-lambat">Terlambat Masuk</span>`;
                else if(l.status_pulang === 'PULANG CEPAT') ketHtml = `<span class="status-pill sp-cepat">Pulang Cepat</span>`;
                else ketHtml = `<span class="status-pill sp-tepat">Sempurna</span>`;

                html += `<tr ${rowCls}>
                    <td>
                        <span class="date-day-badge day-${dayLower}">${l.nama_hari}</span>
                        <div style="font-size:11px;color:var(--text-mute);margin-top:2px">${l.tanggal}</div>
                    </td>
                    <td>
                        <div class="emp-name-cell">
                            <div class="emp-avatar-sm" style="background:#10b981">${init}</div>
                            <div>
                                <div class="emp-name-text">${k.nama}</div>
                                <div class="emp-pin-text">PIN: ${l.pin}</div>
                            </div>
                        </div>
                    </td>
                    <td>${inBadge}</td>
                    <td>${outBadge}</td>
                    <td>${ketHtml}</td>
                </tr>`;
            });
        }
        html += `</tbody></table>`;
        wrap.innerHTML = html;
        renderPager(paginated);
    }
}

function paginateArray(array, page, rowsPerPage) {
    const totalItems = array.length;
    const totalPages = Math.ceil(totalItems / rowsPerPage);
    const start = (page - 1) * rowsPerPage;
    const end = start + rowsPerPage;
    const sliced = array.slice(start, end);

    return { data: sliced, page, totalPages, totalItems, start: totalItems ? start + 1 : 0, end: end > totalItems ? totalItems : end };
}

function renderPager(p) {
    document.getElementById('pgInfo').textContent = `Menampilkan ${p.start}-${p.end} dari ${p.totalItems} baris`;
    const wrap = document.getElementById('pgBtns');
    wrap.innerHTML = '';

    const btnPrev = document.createElement('button');
    btnPrev.className = 'pg-btn';
    btnPrev.textContent = '‹';
    btnPrev.disabled = p.page === 1;
    btnPrev.onclick = () => { mainTableCurrentPage--; renderMainTableOnly(); };
    wrap.appendChild(btnPrev);

    let startP = Math.max(1, p.page - 2);
    let endP = Math.min(p.totalPages, startP + 4);
    if(endP - startP < 4) startP = Math.max(1, endP - 4);

    for (let i = startP; i <= endP; i++) {
        const btn = document.createElement('button');
        btn.className = 'pg-btn' + (i === p.page ? ' pg-active' : '');
        btn.textContent = i;
        btn.onclick = () => { mainTableCurrentPage = i; renderMainTableOnly(); };
        wrap.appendChild(btn);
    }

    const btnNext = document.createElement('button');
    btnNext.className = 'pg-btn';
    btnNext.textContent = '›';
    btnNext.disabled = p.page === p.totalPages || p.totalPages === 0;
    btnNext.onclick = () => { mainTableCurrentPage++; renderMainTableOnly(); };
    wrap.appendChild(btnNext);
}

function initEmpSelectorGrid() {
    const grid = document.getElementById('empSelectorGrid');
    if (!grid) return;
    grid.innerHTML = '';

    const meta = window.kipinKaryawanMeta || {};
    const logs = window.kipinRawLogs || [];

    Object.keys(meta).forEach(pin => {
        const k = meta[pin];
        const kLogs = logs.filter(l => String(l.pin) === String(pin));

        let sTepat = 0, sLambat = 0, sCepat = 0, sAbsent = 0;
        kLogs.forEach(l => {
            if (l.status_masuk === 'TEPAT') sTepat++;
            else if (l.status_masuk === 'TERLAMBAT') sLambat++;
            if (l.status_pulang === 'PULANG CEPAT') sCepat++;
            if (l.is_mangkir) sAbsent++;
        });

        const init = k.nama.charAt(0).toUpperCase();
        const card = document.createElement('div');
        card.className = 'emp-card';
        card.id = `empCard-${pin}`;
        card.onclick = () => selectEmployeeForAnalysis(pin);

        card.innerHTML = `
            <div class="emp-card-badge"><svg viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg></div>
            <div class="emp-card-header">
                <div class="emp-card-avatar" style="background:#7c3aed">${init}</div>
                <div>
                    <div class="emp-card-name">${k.nama}</div>
                    <div class="emp-card-pin">PIN: ${pin}</div>
                </div>
            </div>
            <div class="emp-card-stats">
                <div class="emp-card-stat" style="background:rgba(16,185,129,0.08)"><div class="emp-card-stat-num" style="color:#10b981">${sTepat}</div><div class="emp-card-stat-lbl">Ok</div></div>
                <div class="emp-card-stat" style="background:rgba(245,158,11,0.08)"><div class="emp-card-stat-num" style="color:#f59e0b">${sLambat}</div><div class="emp-card-stat-lbl">Late</div></div>
                <div class="emp-card-stat" style="background:rgba(244,63,94,0.08)"><div class="emp-card-stat-num" style="color:#f43f5e">${sCepat}</div><div class="emp-card-stat-lbl">Early</div></div>
                <div class="emp-card-stat" style="background:rgba(168,85,247,0.08)"><div class="emp-card-stat-num" style="color:#a855f7">${sAbsent}</div><div class="emp-card-stat-lbl">Abs</div></div>
            </div>`;
        grid.appendChild(card);
    });
}

function selectEmployeeForAnalysis(pin) {
    selectedEmpIdForBarChart = pin;
    document.querySelectorAll('.emp-card').forEach(c => c.classList.remove('selected'));
    
    const targetCard = document.getElementById(`empCard-${pin}`);
    if (targetCard) targetCard.classList.add('selected');

    const meta = window.kipinKaryawanMeta || {};
    const k = meta[pin] || { nama: 'Karyawan ' + pin };
    
    document.getElementById('bceName').textContent = k.nama;
    document.getElementById('bcePin').textContent = `PIN Identifikasi: ${pin}`;
    document.getElementById('bceAvatar').textContent = k.nama.charAt(0).toUpperCase();

    document.getElementById('chartPanel').classList.add('show');
    renderBarChartTrend();
}

function setBarChartMode(mode) {
    barChartMode = mode;
    document.getElementById('bmbStatus').classList.toggle('active', mode === 'status');
    document.getElementById('bmbJam').classList.toggle('active', mode === 'jam');
    renderBarChartTrend();
}

function renderBarChartTrend() {
    const pin = selectedEmpIdForBarChart;
    if (!pin) return;

    const ctx = document.getElementById('barChart');
    if (!ctx) return;

    const logs = window.kipinRawLogs || [];
    const kLogs = logs.filter(l => String(l.pin) === String(pin));

    // Urutkan berdasarkan kronologi tanggal
    kLogs.sort((a,b) => {
        if(a.tahun !== b.tahun) return a.tahun - b.tahun;
        if(a.bulan !== b.bulan) return a.bulan - b.bulan;
        return a.hari - b.hari;
    });

    const last15Logs = kLogs.slice(-15);
    const labels = last15Logs.map(l => `${l.hari}/${l.bulan}`);

    let chartData = {};

    if (barChartMode === 'status') {
        const dataMasuk = last15Logs.map(l => l.is_mangkir ? 0 : (l.status_masuk === 'TEPAT' ? 1 : 2));
        const dataPulang = last15Logs.map(l => l.is_mangkir ? 0 : (l.status_pulang === 'NORMAL' ? 1 : 2));

        chartData = {
            labels: labels,
            datasets: [
                { label: 'Status Scan Masuk (1=Tepat, 2=Telat, 0=Mangkir)', data: dataMasuk, backgroundColor: '#6366f1', borderRadius: 5 },
                { label: 'Status Scan Pulang (1=Normal, 2=Cepat, 0=Mangkir)', data: dataPulang, backgroundColor: '#0d9488', borderRadius: 5 }
            ]
        };
    } else {
        const dataJam = last15Logs.map(l => {
            if (l.is_mangkir || !l.jam_masuk) return null;
            const p = l.jam_masuk.split(':');
            return parseFloat(p[0]) + (parseFloat(p[1]) / 60);
        });

        chartData = {
            labels: labels,
            datasets: [{
                label: 'Jam Masuk (Format Desimal)',
                data: dataJam,
                type: 'line',
                borderColor: '#10b981',
                backgroundColor: 'rgba(16,185,129,0.1)',
                fill: true,
                tension: 0.2,
                pointRadius: 4
            }]
        };
    }

    if (myBarChart) myBarChart.destroy();

    myBarChart = new Chart(ctx, {
        type: barChartMode === 'status' ? 'bar' : 'line',
        data: chartData,
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    grid: { color: 'rgba(255,255,255,0.06)' },
                    ticks: { color: '#94a3b8' }
                },
                x: {
                    grid: { display: false },
                    ticks: { color: '#94a3b8' }
                }
            },
            plugins: { legend: { display: true, labels: { color: '#f1f5f9' } } }
        }
    });

    // Custom info legend strip text
    const legEl = document.getElementById('barChartLegend');
    if (barChartMode === 'status') {
        legEl.innerHTML = `<span class="cl-item"><b style="color:#6366f1">■</b> Indikator Masuk: Nilai 1 artinya Aman/Tepat, Nilai 2 artinya Terlambat</span>`;
    } else {
        legEl.innerHTML = `<span class="cl-item"><b style="color:#10b981">■</b> Garis Grafik menunjukkan jam kedatangan. Garis mendatar semakin ke bawah artinya datang semakin pagi.</span>`;
    }
}

function printReport() {
    const currentLogs = getFilteredLogs();
    const metaKaryawan = window.kipinKaryawanMeta || {};
    const t = document.getElementById('filterTahun').value;
    const bName = document.getElementById('filterBulan').options[document.getElementById('filterBulan').selectedIndex].text;

    let titleStr = viewMode === 'bulanan' ? `Laporan Bulanan Absensi — ${bName} ${t}` : `Laporan Harian Absensi — ${document.getElementById('filterDate').value}`;

    let rowsHtml = '';
    let no = 1;

    if (viewMode === 'bulanan') {
        Object.keys(metaKaryawan).forEach(pin => {
            const k = metaKaryawan[pin];
            const kLogs = currentLogs.filter(l => String(l.pin) === String(pin));
            let sTepat = 0, sLambat = 0, sCepat = 0, sAbsent = 0;

            kLogs.forEach(l => {
                if (l.status_masuk === 'TEPAT') sTepat++;
                else if (l.status_masuk === 'TERLAMBAT') sLambat++;
                if (l.status_pulang === 'PULANG CEPAT') sCepat++;
                if (l.is_mangkir) sAbsent++;
            });

            rowsHtml += `<tr><td>${no++}</td><td>${pin}</td><td>${k.nama}</td><td>${kLogs.length} Hari</td><td>${sTepat}</td><td>${sLambat}</td><td>${sCepat}</td><td>${sAbsent}</td></tr>`;
        });
    } else {
        currentLogs.forEach(l => {
            const k = metaKaryawan[l.pin] || { nama: '-' };
            rowsHtml += `<tr><td>${no++}</td><td>${l.pin}</td><td>${k.nama}</td><td>${l.nama_hari}, ${l.tanggal}</td><td>${l.jam_masuk || '-'}</td><td>${l.jam_pulang || '-'}</td><td>${l.is_mangkir ? 'MANGKIR' : 'HADIR'}</td></tr>`;
        });
    }

    const printHtml = `
    <html><head><title>Print Report</title>
    <style>
        body { font-family: sans-serif; padding:20px; color:#333; }
        h2 { margin-bottom: 5px; }
        .sub { color:#666; font-size:13px; margin-bottom:20px; }
        table { width:100%; border-collapse:collapse; font-size:12px; }
        th, td { border:1px solid #aaa; padding:8px; text-align:left; }
        th { background:#f4f4f4; }
        .footer { margin-top:30px; font-size:11px; text-align:right; color:#777; }
    </style></head>
    <body>
        <h2>${titleStr}</h2>
        <div class="sub">Dicetak secara otomatis oleh Kipin Monitoring Engine pada ${new Date().toLocaleString('id-ID')}</div>
        <table><thead><tr>` + 
        (viewMode === 'bulanan' 
            ? '<th>No</th><th>PIN</th><th>Nama Karyawan</th><th>Total Log</th><th>Tepat</th><th>Telat</th><th>Plg Cepat</th><th>Mangkir</th>'
            : '<th>No</th><th>PIN</th><th>Nama Karyawan</th><th>Hari Tanggal</th><th>Jam Masuk</th><th>Jam Pulang</th><th>Status</th>')
        + `</tr></thead><tbody>`
        + (rowsHtml || '<tr><td colspan="8" style="text-align:center;padding:20px;color:#999">Tidak ada data</td></tr>')
        + '</tbody></table>'
        + `<div class="footer">Kipin — Sistem Monitoring Absensi &middot; Total ${(no-1)} baris data</div>`
        + '</body></html>';

    const win = window.open('', '_blank', 'width=900,height=700');
    win.document.write(printHtml);
    win.document.close();
    win.focus();
    setTimeout(function(){ win.print(); }, 400);
}

// Ekspos fungsi global agar bisa dipanggil lewat atribut onclick HTML
window.switchPage = switchPage;
window.setViewMode = setViewMode;
window.onPeriodDropdownChange = onPeriodDropdownChange;
window.onFilterDateChange = onFilterDateChange;
window.onSearchInput = onSearchInput;
window.setKaryawanNavFilter = setKaryawanNavFilter;
window.showLoading = showLoading;
window.highlightChartSection = highlightChartSection;
window.setBarChartMode = setBarChartMode;
window.printReport = printReport;

