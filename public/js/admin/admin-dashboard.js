/* ══ AUTO HIDE ALERT ══ */
setTimeout(function () {
    ['alertSuccess', 'alertError'].forEach(function (id) {
        var el = document.getElementById(id);
        if (el) {
            el.style.transition = 'opacity .6s';
            el.style.opacity = '0';
            setTimeout(function () { el.remove(); }, 600);
        }
    });
}, 8000);

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

function avatarHTML(nama, color, size){
    var w = size || 38;
    var svgMale = '<svg viewBox="0 0 24 24" width="'+(w*0.62)+'" height="'+(w*0.62)+'" fill="white"><circle cx="12" cy="7" r="4"/><path d="M4 21v-1a8 8 0 0 1 16 0v1"/></svg>';
    var svgFemale = '<svg viewBox="0 0 24 24" width="'+(w*0.62)+'" height="'+(w*0.62)+'" fill="white"><circle cx="12" cy="6" r="3.5"/><path d="M12 11c-4 0-7 2.5-7 5.5V21h14v-4.5c0-3-3-5.5-7-5.5z"/></svg>';
    var bg = isMale(nama) ? 'linear-gradient(135deg,#2563eb,#6366f1)' : 'linear-gradient(135deg,#be185d,#ec4899)';
    return '<div style="width:'+w+'px;height:'+w+'px;border-radius:10px;background:'+bg+';display:flex;align-items:center;justify-content:center;flex-shrink:0">'+(isMale(nama)?svgMale:svgFemale)+'</div>';
}

/* ══ PARSE RAW DATA ══ */
var RAW_DATA = [];
try { RAW_DATA = JSON.parse(document.getElementById('rawDataScript').textContent); } catch(e){ console.error('Gagal parse rawData:', e); }

/* ══ STATE ══ */
var viewMode         = 'bulanan';
var currentEmpPage   = 1;
var donutChart       = null;
var empBarChart      = null;
var selectedEmpPin   = null;
var selectedEmpColor = '#6366f1';
var barMode          = 'all';
var currentPage      = 'dashboard';

/* ══ JAM — id "liveClock" sesuai HTML ══ */
function tick(){
    var d = new Date();
    var el = document.getElementById('liveClock');
    if (el) el.textContent = d.toLocaleTimeString('id-ID', {hour:'2-digit', minute:'2-digit', second:'2-digit'});
}
tick();
setInterval(tick, 1000);

/* ══ SIDEBAR TOGGLE ══ */
function toggleSidebar(){
    document.getElementById('sidebar').classList.toggle('open');
    document.getElementById('sidebarOverlay').classList.toggle('show');
}
function closeSidebarMobile(){
    if(window.innerWidth <= 767){
        document.getElementById('sidebar').classList.remove('open');
        document.getElementById('sidebarOverlay').classList.remove('show');
    }
}

/* ══ UPLOAD CSV ══ */
var selectedFiles = [];

function handleFileSelect(input){
    if (!input.files || !input.files.length) return;
    for (var i = 0; i < input.files.length; i++) selectedFiles.push(input.files[i]);
    updateFileListUI();
}

function removeFile(idx){
    selectedFiles.splice(idx, 1);
    updateFileListUI();
}

function updateFileListUI(){
    var list = document.getElementById('fileList');
    var btn  = document.getElementById('btnSubmitUpload');
    if (!list || !btn) return;

    if (!selectedFiles.length) {
        list.style.display = 'none';
        list.innerHTML = '';
        btn.disabled = true;
        btn.style.opacity = '0.4';
        btn.style.cursor = 'not-allowed';
        // reset input
        document.getElementById('fileInputHidden').value = '';
        return;
    }

    var html = '<div style="font-size:11px;font-weight:600;color:#64748b;text-transform:uppercase;letter-spacing:.07em;margin-bottom:8px">File dipilih ('+selectedFiles.length+')</div>';
    for (var i = 0; i < selectedFiles.length; i++) {
        var f = selectedFiles[i];
        var size = f.size > 1024*1024 ? (f.size/1024/1024).toFixed(1)+' MB' : Math.round(f.size/1024)+' KB';
        html += '<div style="display:flex;align-items:center;gap:10px;padding:10px 14px;border-radius:8px;background:rgba(99,102,241,0.1);border:1px solid rgba(99,102,241,0.2);margin-bottom:6px">'
            + '<svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="#818cf8" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>'
            + '<span style="font-size:12px;font-weight:500;color:#e2e8f0;flex:1">'+f.name+'</span>'
            + '<span style="font-size:11px;color:#64748b;margin-right:6px">'+size+'</span>'
            + '<button onclick="removeFile('+i+')" type="button" style="width:20px;height:20px;border-radius:50%;background:rgba(244,63,94,0.2);border:1px solid rgba(244,63,94,0.3);display:flex;align-items:center;justify-content:center;cursor:pointer;">'
            + '<svg viewBox="0 0 24 24" width="10" height="10" fill="none" stroke="#fb7185" stroke-width="3"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>'
            + '</button></div>';
    }
    list.style.display = 'block';
    list.innerHTML = html;
    btn.disabled = false;
    btn.style.opacity = '1';
    btn.style.cursor = 'pointer';

    // ── SYNC ke input file pakai DataTransfer ──
    try {
        var dt = new DataTransfer();
        selectedFiles.forEach(function(f){ dt.items.add(f); });
        document.getElementById('fileInputHidden').files = dt.files;
    } catch(e) { console.warn('DataTransfer not supported:', e); }
}

/* ══ SUBMIT UPLOAD — tampilkan loading ══ */
document.addEventListener('DOMContentLoaded', function(){

    // Form submit
    var form = document.getElementById('uploadForm');
    if (form) {
        form.addEventListener('submit', function(e){
            if (!selectedFiles.length) {
                e.preventDefault();
                alert('Pilih file CSV terlebih dahulu!');
                return;
            }
            // Sync ulang sebelum submit
            try {
                var dt = new DataTransfer();
                selectedFiles.forEach(function(f){ dt.items.add(f); });
                document.getElementById('fileInputHidden').files = dt.files;
            } catch(err) { console.warn(err); }

            var overlay = document.getElementById('loadingOverlay');
            if (overlay) overlay.classList.add('show');
        });
    }

    // Drag & Drop
    var dz = document.getElementById('dropZone');
    if (dz) {
        dz.addEventListener('dragover', function(e){
            e.preventDefault();
            dz.style.borderColor = '#6366f1';
            dz.style.background  = 'rgba(99,102,241,0.12)';
        });
        dz.addEventListener('dragleave', function(){
            dz.style.borderColor = 'rgba(99,102,241,0.4)';
            dz.style.background  = 'rgba(99,102,241,0.05)';
        });
        dz.addEventListener('drop', function(e){
            e.preventDefault();
            dz.style.borderColor = 'rgba(99,102,241,0.4)';
            dz.style.background  = 'rgba(99,102,241,0.05)';
            var files = e.dataTransfer.files;
            for (var i = 0; i < files.length; i++) selectedFiles.push(files[i]);
            updateFileListUI();
        });
    }
});

/* ══ INIT ══ */
(function init(){
    var now = new Date(), nowM = now.getMonth()+1, nowY = now.getFullYear();

    // Populate dropdown tahun
    ['filterTahun','filterTahunPerf'].forEach(function(id){
        var sel = document.getElementById(id); if (!sel) return;
        var years = {}; years[nowY] = true;
        RAW_DATA.forEach(function(r){ years[r.tahun] = true; });
        Object.keys(years).sort(function(a,b){ return b-a; }).forEach(function(y){
            var o = document.createElement('option');
            o.value = y; o.textContent = y;
            if (parseInt(y) === nowY) o.selected = true;
            sel.appendChild(o);
        });
    });

    // Pilih bulan terbaik (bulan dengan data terbanyak)
    var bestMonth = nowM;
    if (RAW_DATA.length > 0) {
        var mc = {};
        RAW_DATA.forEach(function(r){ if(r.tahun===nowY) mc[r.bulan]=(mc[r.bulan]||0)+1; });
        var best = 0;
        Object.keys(mc).forEach(function(m){ if(mc[m]>best){ best=mc[m]; bestMonth=parseInt(m); } });
        if (best === 0) {
            RAW_DATA.forEach(function(r){ mc[r.bulan]=(mc[r.bulan]||0)+1; });
            Object.keys(mc).forEach(function(m){ if(mc[m]>best){ best=mc[m]; bestMonth=parseInt(m); } });
        }
    }
    document.getElementById('filterBulan').value = bestMonth;
    document.getElementById('filterBulanPerf').value = bestMonth;

    // Badge jumlah data
    document.getElementById('sbDataBadge').textContent = RAW_DATA.length;

    initDonut();
    onPeriodChange();
})();

/* ══ PAGE SWITCH ══ */
function switchPage(p){
    closeSidebarMobile();
    currentPage = p;
    ['pageDashboard','pageData','pagePerforma','pageUpload'].forEach(function(id){
        document.getElementById(id).classList.remove('active');
    });
    ['sbDashboard','sbData','sbPerforma','sbUpload'].forEach(function(id){
        var el = document.getElementById(id); if(el) el.classList.remove('active');
    });
    var pid = 'page'+p.charAt(0).toUpperCase()+p.slice(1);
    var sid = 'sb'+p.charAt(0).toUpperCase()+p.slice(1);
    document.getElementById(pid).classList.add('active');
    var sbEl = document.getElementById(sid); if(sbEl) sbEl.classList.add('active');

    var names = {dashboard:'Dashboard', data:'Data Presensi', performa:'Chart Performa', upload:'Upload CSV'};
    var subs  = {dashboard:'Ringkasan', data:'Tabel lengkap', performa:'Analisis karyawan', upload:'Import data CSV'};
    document.getElementById('tbPageName').textContent = names[p] || p;
    document.getElementById('tbPageSub').textContent  = subs[p]  || '';

    var showPeriod = (p === 'dashboard' || p === 'data');
    document.getElementById('topbarPeriod').style.display = showPeriod ? 'flex' : 'none';

    if (p === 'performa') renderPerfPage();
    if (p === 'data') applyFiltersAndRender();
}

/* ══ HELPER ══ */
function getNamaHari(tglStr){
    var parts = tglStr.split('-');
    return NAMA_HARI[new Date(parseInt(parts[0]), parseInt(parts[1])-1, parseInt(parts[2])).getDay()];
}
function getHariCssClass(tglStr){
    var parts = tglStr.split('-');
    return HARI_CSS[new Date(parseInt(parts[0]), parseInt(parts[1])-1, parseInt(parts[2])).getDay()];
}

/* ══ DONUT ══ */
function initDonut(){
    var ctx = document.getElementById('donutChart').getContext('2d');
    donutChart = new Chart(ctx, {
        type: 'doughnut',
        data: { labels: DONUT_LABELS, datasets: [{ data:[0,0,0,0,0], backgroundColor:DONUT_COLORS, borderColor:'#ffffff', borderWidth:3, hoverOffset:10 }] },
        options: {
            responsive:true, maintainAspectRatio:false, cutout:'72%',
            animation:{ animateScale:true, duration:600 },
            plugins:{ legend:{display:false}, tooltip:{ callbacks:{ label:function(c){ var t=c.dataset.data.reduce(function(a,b){return a+b;},0); return ' '+c.label+': '+c.raw+' ('+(t>0?Math.round(c.raw/t*100):0)+'%)'; } }, padding:10, cornerRadius:8 } },
            onHover: function(e, els){ onDonutHover(els); }
        }
    });
}
function onDonutHover(els){
    var data=donutChart.data.datasets[0].data, total=data.reduce(function(a,b){return a+b;},0);
    var nE=document.getElementById('donutCenterNum'), lE=document.getElementById('donutCenterLbl');
    if(els.length){ var i=els[0].index; nE.textContent=data[i]; nE.style.color=DONUT_COLORS[i]; lE.textContent=DONUT_LABELS[i]; }
    else{ nE.textContent=total; nE.style.color='var(--text)'; lE.textContent='total'; }
}
function updateDonut(vals){
    if(!donutChart) return;
    var total = vals.reduce(function(a,b){return a+b;},0);
    donutChart.data.datasets[0].data = vals; donutChart.update();
    document.getElementById('donutCenterNum').textContent = total;
    document.getElementById('donutCenterNum').style.color = 'var(--text)';
    document.getElementById('donutCenterLbl').textContent = 'total';
    renderLegend(vals, total);
}
function renderLegend(vals, total){
    var c = document.getElementById('donutLegends'); c.innerHTML = '';
    DONUT_LABELS.forEach(function(lbl, i){
        var pct = total > 0 ? Math.round(vals[i]/total*100) : 0;
        var div = document.createElement('div'); div.className = 'dl-item';
        div.style.background = 'rgba(255,255,255,0.05)';
        div.innerHTML = '<span class="dl-dot" style="background:'+DONUT_COLORS[i]+'"></span><span class="dl-label" style="color:#e2e8f0">'+lbl+'</span><span class="dl-val" style="color:'+DONUT_TXT[i]+'">'+vals[i]+'<span class="dl-pct" style="color:#94a3b8">('+pct+'%)</span></span>';
        div.setAttribute('onmouseover','hoverDonut('+i+')');
        div.setAttribute('onmouseout','resetDonut()');
        c.appendChild(div);
    });
}
function hoverDonut(i){
    if(!donutChart) return;
    var d = donutChart.data.datasets[0].data;
    donutChart.setActiveElements([{datasetIndex:0,index:i}]);
    donutChart.tooltip.setActiveElements([{datasetIndex:0,index:i}],{x:0,y:0});
    donutChart.update();
    document.getElementById('donutCenterNum').textContent = d[i];
    document.getElementById('donutCenterNum').style.color = DONUT_COLORS[i];
    document.getElementById('donutCenterLbl').textContent = DONUT_LABELS[i];
}
function resetDonut(){
    if(!donutChart) return;
    var d=donutChart.data.datasets[0].data, t=d.reduce(function(a,b){return a+b;},0);
    donutChart.setActiveElements([]); donutChart.tooltip.setActiveElements([],{x:0,y:0}); donutChart.update();
    document.getElementById('donutCenterNum').textContent = t;
    document.getElementById('donutCenterNum').style.color = 'var(--text)';
    document.getElementById('donutCenterLbl').textContent = 'total';
}

/* ══ PERIOD ══ */
function setViewMode(mode){
    viewMode = mode;
    ['vpHarian','vpMingguan','vpBulanan','vpTahunan'].forEach(function(id){ document.getElementById(id).classList.remove('active'); });
    document.getElementById({harian:'vpHarian',mingguan:'vpMingguan',bulanan:'vpBulanan',tahunan:'vpTahunan'}[mode]).classList.add('active');
    document.getElementById('filterBulan').style.display  = (mode !== 'tahunan') ? '' : 'none';
    document.getElementById('filterMinggu').style.display = (mode === 'mingguan') ? '' : 'none';
    document.getElementById('filterHari').style.display   = (mode === 'harian')   ? '' : 'none';
    if (mode === 'harian') populateHari(parseInt(document.getElementById('filterTahun').value), parseInt(document.getElementById('filterBulan').value));
    onPeriodChange();
}

function populateHari(tahun, bulan){
    var sel = document.getElementById('filterHari'), prev = sel.value; sel.innerHTML = '';
    var days = new Date(tahun, bulan, 0).getDate(); // jumlah hari dalam bulan
    for (var d = 1; d <= days; d++){
        var o = document.createElement('option');
        o.value = d;
        o.textContent = d + ' ' + NAMA_BULAN[bulan]; // "1 Januari", "2 Januari", dst
        sel.appendChild(o);
    }
    if (prev && parseInt(prev) <= days) sel.value = prev;
    else sel.value = 1; // default ke tanggal 1
}

function getPeriod(){
    return {
        tahun:  parseInt(document.getElementById('filterTahun').value)  || new Date().getFullYear(),
        bulan:  parseInt(document.getElementById('filterBulan').value)  || new Date().getMonth()+1,
        minggu: parseInt(document.getElementById('filterMinggu').value) || 1,
        hari:   parseInt(document.getElementById('filterHari').value)   || 1
    };
}

function filterByPeriod(records, p){
    return records.filter(function(r){
        if (r.tahun !== p.tahun) return false;
        if (viewMode === 'tahunan') return true;
        if (r.bulan !== p.bulan) return false;
        if (viewMode === 'bulanan') return true;
        if (viewMode === 'mingguan') return r.minggu === p.minggu;
        return r.hari === p.hari;
    });
}

function periodLabel(p){
    if (viewMode === 'tahunan')  return 'Tahun ' + p.tahun;
    if (viewMode === 'bulanan')  return NAMA_BULAN[p.bulan] + ' ' + p.tahun;
    if (viewMode === 'mingguan') return 'Minggu ' + p.minggu + ', ' + NAMA_BULAN[p.bulan] + ' ' + p.tahun;
    var tgl = p.tahun+'-'+String(p.bulan).padStart(2,'0')+'-'+String(p.hari).padStart(2,'0');
    return getNamaHari(tgl) + ', ' + p.hari + ' ' + NAMA_BULAN[p.bulan] + ' ' + p.tahun;
}

function viewLabel(){ return {harian:'Harian',mingguan:'Mingguan',bulanan:'Bulanan',tahunan:'Tahunan'}[viewMode]; }

/* ══ MAIN PERIOD CHANGE ══ */
function onPeriodChange(){
    var p = getPeriod(), lbl = periodLabel(p);
    if (viewMode === 'harian') populateHari(p.tahun, p.bulan);

    ['chartPeriodLabel','tablePeriodLabel'].forEach(function(id){
        var el = document.getElementById(id); if(el) el.textContent = lbl;
    });
    var subEl = document.getElementById('tbPageSub');
    if (subEl && (currentPage==='dashboard'||currentPage==='data')) subEl.textContent = lbl;
    document.getElementById('chartViewChip').textContent = viewLabel();

    var pr = filterByPeriod(RAW_DATA, p);
    var cTepat  = pr.filter(function(r){ return r.isMasuk && !r.terlambat; }).length;
    var cLambat = pr.filter(function(r){ return r.terlambat; }).length;
    var cCepat  = pr.filter(function(r){ return r.pulangCepat; }).length;
    var cAbsent = hitungAbsen(pr, p);

    ['statTotal','statTepat','statLambat','statCepat','statAbsent'].forEach(function(id, i){
        var el = document.getElementById(id);
        el.textContent = [pr.length, cTepat, cLambat, cCepat, cAbsent][i];
        el.classList.remove('pop'); void el.offsetWidth; el.classList.add('pop');
    });

    var cPulangTepat = pr.filter(function(r){ return !r.isMasuk && !r.pulangCepat; }).length;
    updateDonut([cTepat, cLambat, cCepat, cPulangTepat, cAbsent]);
    renderDashQuickTable(pr, p);

    currentEmpPage = 1;
    if (currentPage === 'data'){
        if (viewMode === 'harian') renderDailyAllEmployees(pr, p);
        else renderTable(buildGrouped(pr, p));
    }
}

/* ══ DASHBOARD QUICK TABLE ══ */
function renderDashQuickTable(pr, p){
    var wrap = document.getElementById('dashQuickTable');
    var allK = {};
    RAW_DATA.forEach(function(r){ if(!allK[r.pin]) allK[r.pin]=r.nama; });
    var emps = Object.keys(allK).map(function(pin){
        var empR = pr.filter(function(r){ return r.pin===pin; });
        var masuk = empR.filter(function(r){ return r.isMasuk; }).length;
        var lambat = empR.filter(function(r){ return r.terlambat; }).length;
        return {pin:pin, nama:allK[pin], masuk:masuk, lambat:lambat};
    }).sort(function(a,b){ return a.nama<b.nama?-1:1; }).slice(0,8);

    if (!emps.length){ wrap.innerHTML='<div class="empty"><div class="empty-icon">📋</div><div class="empty-text">Tidak ada data</div></div>'; return; }
    var html = '<table style="width:100%;border-collapse:collapse;font-size:12px"><thead><tr style="background:var(--bg)"><th style="padding:9px 14px;text-align:left;font-size:10px;font-weight:600;color:var(--text-mute);border-bottom:1px solid var(--border);text-transform:uppercase;letter-spacing:.07em">Karyawan</th><th style="padding:9px 14px;text-align:center;font-size:10px;font-weight:600;color:var(--text-mute);border-bottom:1px solid var(--border)">Hadir</th><th style="padding:9px 14px;text-align:center;font-size:10px;font-weight:600;color:var(--text-mute);border-bottom:1px solid var(--border)">Lambat</th></tr></thead><tbody>';
    emps.forEach(function(emp, i){
        var color = PALETTE[i%PALETTE.length];
        html += '<tr style="border-bottom:1px solid rgba(0,0,0,0.04)"><td style="padding:10px 14px"><div style="display:flex;align-items:center;gap:8px"><div style="width:26px;height:26px;border-radius:7px;background:'+color+';display:flex;align-items:center;justify-content:center;font-size:9px;font-weight:600;color:#fff">'+emp.nama.substring(0,2).toUpperCase()+'</div><span style="font-size:12px;font-weight:500;color:var(--text)">'+emp.nama+'</span></div></td><td style="padding:10px 14px;text-align:center"><span style="font-size:13px;font-weight:600;color:'+(emp.masuk>0?'#16a34a':'var(--text-mute)')+'">'+emp.masuk+'</span></td><td style="padding:10px 14px;text-align:center"><span style="font-size:13px;font-weight:600;color:'+(emp.lambat>0?'#d97706':'var(--text-mute)')+'">'+emp.lambat+'</span></td></tr>';
    });
    html += '</tbody></table>';
    if (Object.keys(allK).length > 8) html += '<div style="text-align:center;padding:10px;font-size:12px;color:var(--accent);cursor:pointer;font-weight:500" onclick="switchPage(\'data\')">Lihat semua karyawan →</div>';
    wrap.innerHTML = html;
}

/* ══ HITUNG ABSEN ══ */
function hitungAbsen(periodRecords, p){
    var dates = {};
    periodRecords.forEach(function(r){ dates[r.tanggal]=true; });
    var dArr = Object.keys(dates); if (!dArr.length) return 0;
    var allPins = {}; RAW_DATA.forEach(function(r){ allPins[r.pin]=true; });
    var total = Object.keys(allPins).length, absen = 0;
    dArr.forEach(function(dt){
        var hadir = {};
        periodRecords.forEach(function(r){ if(r.tanggal===dt&&r.isMasuk) hadir[r.pin]=true; });
        absen += total - Object.keys(hadir).length;
    });
    return Math.max(0, absen);
}

/* ══ DAILY VIEW ══ */
function renderDailyAllEmployees(periodRecords, p){
    var allK = {};
    RAW_DATA.forEach(function(r){ if(!allK[r.pin]) allK[r.pin]=r.nama; });
    var dayData = {};
    Object.keys(allK).forEach(function(pin){ dayData[pin]={pin:pin,nama:allK[pin],masuk:null,pulang:null,terlambat:false,pulangCepat:false,absent:true}; });
    periodRecords.forEach(function(r){
        if(!dayData[r.pin]) dayData[r.pin]={pin:r.pin,nama:r.nama,masuk:null,pulang:null,terlambat:false,pulangCepat:false,absent:true};
        var d = dayData[r.pin];
        if (r.isMasuk){ d.absent=false; if(!d.masuk||r.waktu<d.masuk){d.masuk=r.waktu;d.terlambat=r.terlambat;} }
        else{ if(!d.pulang||r.waktu>d.pulang){d.pulang=r.waktu;d.pulangCepat=r.pulangCepat;} }
    });
    var rows = Object.values(dayData).sort(function(a,b){ return a.nama<b.nama?-1:1; });
    var tglStr = p.tahun+'-'+String(p.bulan).padStart(2,'0')+'-'+String(p.hari).padStart(2,'0');
    var namaHari = getNamaHari(tglStr);
    var hariCss  = getHariCssClass(tglStr);

    document.getElementById('countChip').textContent = rows.length+' karyawan';
    document.getElementById('karyawanNavStrip').style.display = 'none';
    document.getElementById('empTableHeader').style.display = 'none';
    document.getElementById('tableHead').innerHTML = '<tr><th>No</th><th>Karyawan</th><th>Jam Masuk</th><th>Jam Pulang</th><th>Status</th></tr>';
    var tbody = document.getElementById('tableBody'); tbody.innerHTML = '';

    if (!rows.length){
        tbody.innerHTML='<tr><td colspan="5"><div class="empty"><div class="empty-icon">📋</div><div class="empty-text">Tidak ada data untuk hari ini</div></div></td></tr>';
    } else {
        rows.forEach(function(emp, idx){
            var color = PALETTE[idx%PALETTE.length];
            var tr = document.createElement('tr');
            tr.style.animation = 'rowIn .2s '+(idx*0.02)+'s ease both';
            if (emp.absent) tr.className = 'row-absent';
            var noCell  = '<td style="color:var(--text-light);font-size:12px;font-family:\'JetBrains Mono\',monospace">'+String(idx+1).padStart(2,'0')+'</td>';
            var empCell = '<td><div class="emp-name-cell"><div class="emp-avatar-sm" style="background:'+color+'">'+emp.nama.substring(0,2).toUpperCase()+'</div><div><div class="emp-name-text">'+emp.nama+'</div><div style="display:flex;align-items:center;gap:5px;margin-top:2px"><span class="emp-pin-text">PIN: '+emp.pin+'</span><span class="date-day-badge '+hariCss+'">'+namaHari+'</span></div></div></div></td>';
            if (emp.absent){
                tr.innerHTML = noCell+empCell+'<td><span class="badge-absent">Tidak Masuk</span></td><td><span class="time-none">—</span></td><td><span class="status-pill sp-absent">⚠ Absen</span></td>';
            } else {
                var jamMasuk  = emp.masuk ? (emp.terlambat ? '<span class="time-badge time-late">'+emp.masuk+'</span>' : '<span class="time-badge time-ok">'+emp.masuk+'</span>') : '<span class="time-none">—</span>';
                var jamPulang = emp.pulang ? (emp.pulangCepat ? '<span class="time-badge time-early">'+emp.pulang+'</span>' : '<span class="time-badge time-ok">'+emp.pulang+'</span>') : '<span class="time-badge time-ok">16:00:00</span>';
                var status    = emp.terlambat ? '<span class="status-pill sp-lambat">⚠ Terlambat</span>' : emp.pulangCepat ? '<span class="status-pill sp-cepat">↩ Pulang Cepat</span>' : emp.masuk ? '<span class="status-pill sp-tepat">✓ Tepat Waktu</span>' : '<span class="time-none">—</span>';
                tr.innerHTML = noCell+empCell+'<td>'+jamMasuk+'</td><td>'+jamPulang+'</td><td>'+status+'</td>';
            }
            tbody.appendChild(tr);
        });
    }
    var jmlHadir  = rows.filter(function(r){ return !r.absent; }).length;
    var jmlLambat = rows.filter(function(r){ return r.terlambat; }).length;
    var jmlAbsent = rows.filter(function(r){ return r.absent; }).length;
    document.getElementById('pgInfo').innerHTML = '📅 <strong>'+namaHari+', '+p.hari+' '+NAMA_BULAN[p.bulan]+' '+p.tahun+'</strong> &nbsp;| <span style="color:#16a34a;font-weight:600">✓ Hadir: '+jmlHadir+'</span> &nbsp;<span style="color:#d97706;font-weight:600">⚠ Lambat: '+jmlLambat+'</span> &nbsp;<span style="color:#7c3aed;font-weight:600">✕ Absen: '+jmlAbsent+'</span>';
    document.getElementById('pgBtns').innerHTML = '';
}

/* ══ BUILD GROUPED ══ */
function buildGrouped(pr, p){
    var datesInPeriod = {};
    pr.forEach(function(r){ if(!datesInPeriod[r.tanggal]) datesInPeriod[r.tanggal]=r.tanggalFmt; });
    var allDates = Object.keys(datesInPeriod).sort();
    var allK = {}; RAW_DATA.forEach(function(r){ if(!allK[r.pin]) allK[r.pin]=r.nama; });
    var kMap = {};
    Object.keys(allK).forEach(function(pin){
        kMap[pin] = {pin:pin, nama:allK[pin], days:{}};
        allDates.forEach(function(dt){ kMap[pin].days[dt]={tanggal:dt,tanggalFmt:datesInPeriod[dt],masuk:null,pulang:null,terlambat:false,pulangCepat:false,absent:true}; });
    });
    pr.forEach(function(r){
        if(!kMap[r.pin]) kMap[r.pin]={pin:r.pin,nama:r.nama,days:{}};
        if(!kMap[r.pin].days[r.tanggal]) kMap[r.pin].days[r.tanggal]={tanggal:r.tanggal,tanggalFmt:r.tanggalFmt,masuk:null,pulang:null,terlambat:false,pulangCepat:false,absent:true};
        var d = kMap[r.pin].days[r.tanggal];
        if(r.isMasuk){ d.absent=false; if(!d.masuk||r.waktu<d.masuk){d.masuk=r.waktu;d.terlambat=r.terlambat;} }
        else{ if(!d.pulang||r.waktu>d.pulang){d.pulang=r.waktu;d.pulangCepat=r.pulangCepat;} }
    });
    var result = [];
    Object.keys(kMap).forEach(function(pin){
        var k = kMap[pin];
        var days = Object.values(k.days).sort(function(a,b){ return a.tanggal<b.tanggal?-1:1; });
        if (allDates.length > 0) result.push({pin:k.pin, nama:k.nama, days:days});
    });
    return result.sort(function(a,b){ return a.nama<b.nama?-1:1; });
}

/* ══ FILTER & RENDER ══ */
function applyFiltersAndRender(){
    var name  = document.getElementById('searchName').value.toLowerCase().trim();
    var dateF = document.getElementById('filterDate').value;
    var ket   = document.getElementById('filterKet').value;
    var p     = getPeriod();

    if (dateF){
        var parts = dateF.split('-');
        p.tahun = parseInt(parts[0]);
        p.bulan = parseInt(parts[1]);
        p.hari  = parseInt(parts[2]);
    }

    var pr = filterByPeriod(RAW_DATA, p);

    if (viewMode === 'harian'){
        if (name) pr = pr.filter(function(r){ return r.nama.toLowerCase().indexOf(name)!==-1; });
        renderDailyAllEmployees(pr, p);
        return;
    }

    var baseRecords = dateF ? RAW_DATA.filter(function(r){ return r.tanggal===dateF; }) : pr;
    var grouped = buildGrouped(baseRecords, p);

    if (name) grouped = grouped.filter(function(k){ return k.nama.toLowerCase().indexOf(name)!==-1; });
    if (dateF){
        grouped = grouped.map(function(k){ return {pin:k.pin,nama:k.nama,days:k.days.filter(function(d){return d.tanggal===dateF;})}; })
                         .filter(function(k){ return k.days.length>0; });
    }
    if (ket){
        grouped = grouped.map(function(k){
            return {pin:k.pin, nama:k.nama, days:k.days.filter(function(d){
                if(ket==='terlambat') return d.terlambat;
                if(ket==='cepat')    return d.pulangCepat;
                if(ket==='tepat')    return d.masuk && !d.terlambat;
                if(ket==='absent')   return d.absent;
                return true;
            })};
        }).filter(function(k){ return k.days.length>0; });
    }

    currentEmpPage = 1;
    renderTable(grouped);
}

function resetFilter(){
    document.getElementById('searchName').value = '';
    document.getElementById('filterDate').value = '';
    document.getElementById('filterKet').value  = '';
    applyFiltersAndRender();
}

/* ══ RENDER TABLE ══ */
function renderTable(grouped){
    document.getElementById('tableHead').innerHTML = '<tr><th>No</th><th>Tanggal</th><th>Jam Masuk</th><th>Jam Pulang</th><th>Status</th></tr>';
    document.getElementById('karyawanNavStrip').style.display = '';
    document.getElementById('empTableHeader').style.display = '';
    var tbody=document.getElementById('tableBody'), pgBtns=document.getElementById('pgBtns'), pgInfo=document.getElementById('pgInfo');
    var navStrip = document.getElementById('karyawanNavStrip');
    tbody.innerHTML = '';
    var total = grouped.length;
    if (currentEmpPage > total) currentEmpPage = Math.max(1, total);
    var totalDays = grouped.reduce(function(s,k){ return s+k.days.length; }, 0);
    document.getElementById('countChip').textContent = total+' karyawan · '+totalDays+' hari';

    navStrip.innerHTML = '<span class="karyawan-nav-label">Karyawan:</span>';
    grouped.forEach(function(k, idx){
        var btn = document.createElement('button');
        btn.className = 'karyawan-nav-btn'+(idx+1===currentEmpPage?' active':'');
        btn.textContent = k.nama.split(' ')[0]; btn.title = k.nama;
        btn.onclick = (function(i){ return function(){ currentEmpPage=i+1; renderTable(grouped); }; })(idx);
        navStrip.appendChild(btn);
    });

    if (!total){
        resetEmpHeader();
        tbody.innerHTML = '<tr><td colspan="5"><div class="empty"><div class="empty-icon">🔍</div><div class="empty-text">Tidak ada data</div><div class="empty-sub">Pilih periode lain atau ubah filter</div></div></td></tr>';
        pgInfo.textContent = 'Tidak ada data'; pgBtns.innerHTML = ''; return;
    }

    var k = grouped[currentEmpPage-1];
    updateEmpHeader(k, currentEmpPage-1);
    k.days.forEach(function(d, idx){
        var tr = document.createElement('tr');
        tr.style.animation = 'rowIn .2s '+(idx*0.02)+'s ease both';
        var noCell = '<td style="color:var(--text-light);font-size:12px;font-family:\'JetBrains Mono\',monospace">'+String(idx+1).padStart(2,'0')+'</td>';
        if (d.absent){
            tr.className = 'row-absent';
            tr.innerHTML = noCell+'<td style="font-size:12px;color:var(--text-mute)">'+d.tanggalFmt+'</td><td><span class="badge-absent">Tidak Masuk</span></td><td><span class="time-none">—</span></td><td><span class="status-pill sp-absent">⚠ Absen</span></td>';
        } else {
            var jamMasuk  = d.masuk ? (d.terlambat ? '<span class="time-badge time-late">'+d.masuk+'</span>' : '<span class="time-badge time-ok">'+d.masuk+'</span>') : '<span class="time-none">—</span>';
            var jamPulang = d.pulang ? (d.pulangCepat ? '<span class="time-badge time-early">'+d.pulang+'</span>' : '<span class="time-badge time-ok">'+d.pulang+'</span>') : '<span class="time-badge time-ok">16:00:00</span>';
            var status    = d.terlambat ? '<span class="status-pill sp-lambat">⚠ Terlambat</span>' : d.pulangCepat ? '<span class="status-pill sp-cepat">↩ Pulang Cepat</span>' : d.masuk ? '<span class="status-pill sp-tepat">✓ Tepat Waktu</span>' : '<span class="time-none">—</span>';
            tr.innerHTML = noCell+'<td style="font-size:12px;color:var(--text-mute)">'+d.tanggalFmt+'</td><td>'+jamMasuk+'</td><td>'+jamPulang+'</td><td>'+status+'</td>';
        }
        tbody.appendChild(tr);
    });

    pgInfo.textContent = 'Karyawan '+currentEmpPage+' dari '+total;
    pgBtns.innerHTML = '';
    function mkBtn(label, page, disabled, active){ var b=document.createElement('button'); b.className='pg-btn'+(active?' pg-active':''); b.innerHTML=label; b.disabled=disabled; b.onclick=function(){ currentEmpPage=page; renderTable(grouped); }; pgBtns.appendChild(b); }
    function mkE(){ var s=document.createElement('span'); s.className='pg-ellipsis'; s.textContent='…'; pgBtns.appendChild(s); }
    mkBtn('&#8592;', currentEmpPage-1, currentEmpPage===1, false);
    var pages = [];
    if(total<=7){ for(var i=1;i<=total;i++) pages.push(i); }
    else{ pages.push(1); if(currentEmpPage>3) pages.push('…'); var lo=Math.max(2,currentEmpPage-1),hi=Math.min(total-1,currentEmpPage+1); for(var j=lo;j<=hi;j++) pages.push(j); if(currentEmpPage<total-2) pages.push('…'); pages.push(total); }
    pages.forEach(function(pg){ if(pg==='…') mkE(); else mkBtn(pg,pg,false,pg===currentEmpPage); });
    mkBtn('&#8594;', currentEmpPage+1, currentEmpPage===total, false);
}

function updateEmpHeader(k, colorIdx){
    var color = PALETTE[colorIdx%PALETTE.length];
    var av = document.getElementById('empTableAvatar');
    av.textContent = k.nama.substring(0,2).toUpperCase(); av.style.background = color;
    document.getElementById('empTableName').textContent = k.nama;
    document.getElementById('empTablePin').textContent  = 'PIN: '+k.pin;
    document.getElementById('empTableDays').textContent = k.days.length+' hari';
    var tepat=0,lambat=0,cepat=0,absent=0;
    k.days.forEach(function(d){ if(d.absent)absent++; else{ if(d.terlambat)lambat++; else if(d.masuk)tepat++; if(d.pulangCepat)cepat++; } });
    var html='';
    if(tepat)  html+='<div class="esp esp-tepat">✓ '+tepat+' Tepat</div>';
    if(lambat) html+='<div class="esp esp-lambat">⚠ '+lambat+' Lambat</div>';
    if(cepat)  html+='<div class="esp esp-cepat">↩ '+cepat+' Cepat</div>';
    if(absent) html+='<div class="esp esp-absent">✕ '+absent+' Absen</div>';
    document.getElementById('empTableStats').innerHTML = html;
}
function resetEmpHeader(){
    document.getElementById('empTableAvatar').textContent = '??';
    document.getElementById('empTableAvatar').style.background = 'var(--accent)';
    ['empTableName','empTablePin'].forEach(function(id){ document.getElementById(id).textContent='—'; });
    document.getElementById('empTableDays').textContent = '0 hari';
    document.getElementById('empTableStats').innerHTML = '';
}

/* ══ PERFORMA ══ */
function getEmpsForPeriod(tahun, bulan){
    var allPins = {};
    RAW_DATA.filter(function(r){ return r.tahun===tahun; }).forEach(function(r){ if(!allPins[r.pin]) allPins[r.pin]={pin:r.pin,nama:r.nama}; });
    var relevant = RAW_DATA.filter(function(r){ if(r.tahun!==tahun)return false; if(bulan>0&&r.bulan!==bulan)return false; return true; });
    var workDates = {}; relevant.forEach(function(r){ workDates[r.tanggal]=true; });
    var totalWD = Object.keys(workDates).length;
    var pinStats = {};
    relevant.forEach(function(r){
        if(!pinStats[r.pin]) pinStats[r.pin]={dateSet:{}};
        if(!pinStats[r.pin].dateSet[r.tanggal]) pinStats[r.pin].dateSet[r.tanggal]={masuk:false,terlambat:false,pulangCepat:false};
        if(r.isMasuk){ pinStats[r.pin].dateSet[r.tanggal].masuk=true; if(r.terlambat) pinStats[r.pin].dateSet[r.tanggal].terlambat=true; }
        else{ if(r.pulangCepat) pinStats[r.pin].dateSet[r.tanggal].pulangCepat=true; }
    });
    return Object.keys(allPins).map(function(pin){
        var info=allPins[pin], sd=pinStats[pin]?Object.values(pinStats[pin].dateSet):[];
        var hadir=sd.filter(function(d){return d.masuk;}).length;
        return {pin:info.pin,nama:info.nama,hadir:hadir,lambat:sd.filter(function(d){return d.terlambat;}).length,cepat:sd.filter(function(d){return d.pulangCepat;}).length,absent:Math.max(0,totalWD-hadir),totalWD:totalWD};
    }).sort(function(a,b){ return a.nama<b.nama?-1:1; });
}

function renderPerfPage(){
    var tahun = parseInt(document.getElementById('filterTahunPerf').value);
    var bulan = parseInt(document.getElementById('filterBulanPerf').value);
    document.getElementById('perfPeriodHint').textContent = bulan===0 ? '📅 Seluruh tahun '+tahun : '📅 '+NAMA_BULAN[bulan]+' '+tahun;
    var emps = getEmpsForPeriod(tahun, bulan);
    document.getElementById('empCountChip').textContent = emps.length+' karyawan';
    renderEmpSelector(emps, tahun, bulan);
    if (selectedEmpPin){
        var emp = emps.find(function(e){ return e.pin===selectedEmpPin; });
        if (emp) buildBarChart(selectedEmpPin, emp.nama, selectedEmpColor, tahun, bulan);
        else { document.getElementById('empChartCard').classList.remove('show'); document.getElementById('empChartHint').style.display=''; selectedEmpPin=null; }
    }
}

function renderEmpSelector(emps, tahun, bulan){
    var grid = document.getElementById('empSelectorGrid'); grid.innerHTML='';
    if(!emps.length){ grid.innerHTML='<div class="empty"><div class="empty-icon">📊</div><div class="empty-text">Tidak ada data</div></div>'; return; }
    emps.forEach(function(emp, i){
        var color = PALETTE[i%PALETTE.length];
        var card  = document.createElement('div');
        card.className = 'emp-card'+(emp.pin===selectedEmpPin?' selected':'');
        card.innerHTML = '<div class="emp-card-badge"><svg viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg></div>'
            + '<div class="emp-card-header">'+avatarHTML(emp.nama,color,38)
            + '<div style="min-width:0"><div class="emp-card-name" title="'+emp.nama+'">'+emp.nama+'</div><div class="emp-card-pin">PIN: '+emp.pin+'</div></div></div>'
            + '<div class="emp-card-stats">'
            + '<div class="emp-card-stat" style="background:rgba(99,102,241,0.15);border:1px solid rgba(99,102,241,0.25)"><div class="emp-card-stat-num" style="color:#818cf8">'+emp.hadir+'</div><div class="emp-card-stat-lbl">Hadir</div></div>'
            + '<div class="emp-card-stat" style="background:rgba(245,158,11,0.15);border:1px solid rgba(245,158,11,0.25)"><div class="emp-card-stat-num" style="color:#fbbf24">'+emp.lambat+'</div><div class="emp-card-stat-lbl">Lambat</div></div>'
            + '<div class="emp-card-stat" style="background:rgba(244,63,94,0.15);border:1px solid rgba(244,63,94,0.25)"><div class="emp-card-stat-num" style="color:#fb7185">'+emp.cepat+'</div><div class="emp-card-stat-lbl">Cepat</div></div>'
            + '<div class="emp-card-stat" style="background:rgba(168,85,247,0.15);border:1px solid rgba(168,85,247,0.25)"><div class="emp-card-stat-num" style="color:#c084fc">'+emp.absent+'</div><div class="emp-card-stat-lbl">Absen</div></div>'
            + '</div>';
        (function(pin, nama, col, t, b, el){
            el.onclick = function(){
                selectedEmpPin=pin; selectedEmpColor=col;
                document.querySelectorAll('.emp-card').forEach(function(c){ c.classList.remove('selected'); });
                el.classList.add('selected');
                buildBarChart(pin, nama, col, t, b);
            };
        })(emp.pin, emp.nama, color, tahun, bulan, card);
        grid.appendChild(card);
    });
}

function getMonthlyData(pin, tahun){
    var mMap={}, mWD={};
    RAW_DATA.filter(function(r){ return r.tahun===tahun; }).forEach(function(r){ if(!mWD[r.bulan]) mWD[r.bulan]={}; mWD[r.bulan][r.tanggal]=true; });
    RAW_DATA.filter(function(r){ return r.pin===pin&&r.tahun===tahun; }).forEach(function(r){
        if(!mMap[r.bulan]) mMap[r.bulan]={};
        if(!mMap[r.bulan][r.tanggal]) mMap[r.bulan][r.tanggal]={masuk:null,pulang:null,terlambat:false,pulangCepat:false};
        var d=mMap[r.bulan][r.tanggal];
        if(r.isMasuk){ if(!d.masuk||r.waktu<d.masuk){d.masuk=r.waktu;d.terlambat=r.terlambat;} }
        else{ if(!d.pulang||r.waktu>d.pulang){d.pulang=r.waktu;d.pulangCepat=r.pulangCepat;} }
    });
    var result=[];
    for(var m=1;m<=12;m++){
        if(!mWD[m]) continue;
        var days=mMap[m]?Object.values(mMap[m]):[];
        var hadir=days.filter(function(d){return d.masuk;}).length;
        result.push({bulan:m,label:NAMA_BULAN_S[m],hadir:hadir,lambat:days.filter(function(d){return d.terlambat;}).length,cepat:days.filter(function(d){return d.pulangCepat;}).length,tepat:days.filter(function(d){return d.masuk&&!d.terlambat;}).length,absent:Math.max(0,Object.keys(mWD[m]).length-hadir)});
    }
    return result;
}

function getDailyData(pin, tahun, bulan){
    var dMap={}, allDates={};
    RAW_DATA.filter(function(r){ return r.tahun===tahun&&r.bulan===bulan; }).forEach(function(r){ allDates[r.tanggal]=r.tanggalFmt; });
    RAW_DATA.filter(function(r){ return r.pin===pin&&r.tahun===tahun&&r.bulan===bulan; }).forEach(function(r){
        if(!dMap[r.tanggal]) dMap[r.tanggal]={masuk:null,pulang:null,terlambat:false,pulangCepat:false};
        var d=dMap[r.tanggal];
        if(r.isMasuk){ if(!d.masuk||r.waktu<d.masuk){d.masuk=r.waktu;d.terlambat=r.terlambat;} }
        else{ if(!d.pulang||r.waktu>d.pulang){d.pulang=r.waktu;d.pulangCepat=r.pulangCepat;} }
    });
    return Object.keys(allDates).sort().map(function(dt){
        var d=dMap[dt]||{masuk:null,terlambat:false,pulangCepat:false};
        return {tanggal:dt,label:String(parseInt(dt.split('-')[2])),hadir:d.masuk?1:0,lambat:d.terlambat?1:0,cepat:d.pulangCepat?1:0,tepat:(d.masuk&&!d.terlambat)?1:0,absent:d.masuk?0:1};
    });
}

function setBarMode(mode){
    barMode=mode;
    ['btnModeAll','btnModeHadir','btnModeLate'].forEach(function(id){ document.getElementById(id).classList.remove('active'); });
    document.getElementById({all:'btnModeAll',hadir:'btnModeHadir',late:'btnModeLate'}[mode]).classList.add('active');
    if(selectedEmpPin){
        var tahun=parseInt(document.getElementById('filterTahunPerf').value);
        var bulan=parseInt(document.getElementById('filterBulanPerf').value);
        buildBarChart(selectedEmpPin, document.getElementById('bcName').textContent, selectedEmpColor, tahun, bulan);
    }
}

function buildCustomLegend(activeKeys){
    var c=document.getElementById('chartLegendCustom'); c.innerHTML='';
    activeKeys.forEach(function(key){
        var cfg=BC[key];
        var item=document.createElement('div'); item.className='cl-item';
        var ind=cfg.type==='line'?'<div class="cl-line" style="background:'+cfg.hex+'"></div>':'<div class="cl-dot" style="background:'+cfg.bg+';border:2px solid '+cfg.border+'"></div>';
        item.innerHTML=ind+'<span>'+cfg.label+'</span>';
        c.appendChild(item);
    });
}

function buildBarChart(pin, nama, color, tahun, bulan){
    var isDaily = bulan > 0;
    var data = isDaily ? getDailyData(pin,tahun,bulan) : getMonthlyData(pin,tahun);
    document.getElementById('empChartCard').classList.add('show');
    document.getElementById('empChartHint').style.display = 'none';
    var av=document.getElementById('bcAvatar');
    var bgColor=isMale(nama)?'linear-gradient(135deg,#2563eb,#6366f1)':'linear-gradient(135deg,#be185d,#ec4899)';
    av.style.background=bgColor;
    av.innerHTML='<svg viewBox="0 0 24 24" width="22" height="22" fill="white"><path d="M12 12c2.7 0 4.8-2.1 4.8-4.8S14.7 2.4 12 2.4 7.2 4.5 7.2 7.2 9.3 12 12 12zm0 2.4c-3.2 0-9.6 1.6-9.6 4.8v2.4h19.2v-2.4c0-3.2-6.4-4.8-9.6-4.8z"/></svg>';
    document.getElementById('bcName').textContent=nama;
    document.getElementById('bcPin').textContent='PIN: '+pin+' · '+(isDaily?NAMA_BULAN[bulan]+' '+tahun:'Tahun '+tahun);
    var yH=0,yL=0,yE=0,yT=0,yA=0;
    data.forEach(function(m){ yH+=m.hadir;yL+=m.lambat;yE+=m.cepat;yT+=m.tepat;yA+=m.absent; });
    renderYearlyStat(yH,yL,yE,yT,yA,isDaily?NAMA_BULAN[bulan]+' '+tahun:'Tahun '+tahun);
    if(!data.length){ if(empBarChart){empBarChart.destroy();empBarChart=null;} return; }
    if(empBarChart){ empBarChart.destroy(); empBarChart=null; }
    var ctx=document.getElementById('empBarChart').getContext('2d');
    var datasets=[],activeKeys=[];
    if(barMode==='all'||barMode==='hadir'){
        datasets.push({label:'Total Hadir',data:data.map(function(m){return m.hadir;}),backgroundColor:'rgba(99,102,241,0.85)',borderColor:'#6366f1',borderWidth:1.5,borderRadius:6,borderSkipped:false,barPercentage:isDaily?0.5:0.8,categoryPercentage:isDaily?0.6:0.9}); activeKeys.push('hadir');
        datasets.push({label:'Tepat Waktu',data:data.map(function(m){return m.tepat;}),backgroundColor:'rgba(34,197,94,0.85)',borderColor:'#22c55e',borderWidth:1.5,borderRadius:6,borderSkipped:false,barPercentage:isDaily?0.5:0.8,categoryPercentage:isDaily?0.6:0.9}); activeKeys.push('tepat');
        datasets.push({label:'Tidak Masuk',data:data.map(function(m){return m.absent;}),backgroundColor:'rgba(168,85,247,0.85)',borderColor:'#a855f7',borderWidth:1.5,borderRadius:6,borderSkipped:false,barPercentage:isDaily?0.5:0.8,categoryPercentage:isDaily?0.6:0.9}); activeKeys.push('absent');
    }
    if(barMode==='all'||barMode==='late'){
        datasets.push({label:'Terlambat',data:data.map(function(m){return m.lambat;}),backgroundColor:'rgba(245,158,11,0.85)',borderColor:'#f59e0b',borderWidth:1.5,borderRadius:6,borderSkipped:false,barPercentage:isDaily?0.5:0.8,categoryPercentage:isDaily?0.6:0.9}); activeKeys.push('lambat');
        datasets.push({label:'Pulang Cepat',data:data.map(function(m){return m.cepat;}),backgroundColor:'rgba(244,63,94,0.85)',borderColor:'#f43f5e',borderWidth:1.5,borderRadius:6,borderSkipped:false,barPercentage:isDaily?0.5:0.8,categoryPercentage:isDaily?0.6:0.9}); activeKeys.push('cepat');
    }
    buildCustomLegend(activeKeys);
    empBarChart=new Chart(ctx,{
        type:'bar',
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
    setTimeout(function(){ document.getElementById('empChartCard').scrollIntoView({behavior:'smooth',block:'nearest'}); }, 80);
}

function renderYearlyStat(h, l, e, t, a, label){
    document.getElementById('yearlyStatStrip').innerHTML =
        (label?'<div style="width:100%;font-size:11px;font-weight:600;color:var(--text-mute);text-transform:uppercase;letter-spacing:.06em;padding-bottom:6px">Ringkasan '+label+'</div>':'')+
        ys('ys-hadir',h,'Total Hadir')+ys('ys-tepat',t,'Tepat Waktu')+ys('ys-lambat',l,'Terlambat')+ys('ys-cepat',e,'Pulang Cepat')+ys('ys-absent',a,'Tidak Masuk');
}
function ys(cls, val, lbl){
    var colors={'ys-hadir':'#4ade80','ys-lambat':'#fbbf24','ys-cepat':'#fb7185','ys-tepat':'#818cf8','ys-absent':'#c084fc'};
    var bgs={'ys-hadir':'rgba(34,197,94,0.15)','ys-lambat':'rgba(245,158,11,0.15)','ys-cepat':'rgba(244,63,94,0.15)','ys-tepat':'rgba(99,102,241,0.15)','ys-absent':'rgba(168,85,247,0.15)'};
    var borders={'ys-hadir':'rgba(34,197,94,0.3)','ys-lambat':'rgba(245,158,11,0.3)','ys-cepat':'rgba(244,63,94,0.3)','ys-tepat':'rgba(99,102,241,0.3)','ys-absent':'rgba(168,85,247,0.3)'};
    return '<div class="ys-pill" style="background:'+bgs[cls]+';border:1px solid '+borders[cls]+'"><div><div class="ys-val" style="color:'+colors[cls]+'">'+val+'</div><div class="ys-lbl" style="color:#94a3b8">'+lbl+'</div></div></div>';
}

/* ══ CETAK LAPORAN ══ */
function cetakLaporan(){
    var p=getPeriod(), lbl=periodLabel(p), pr=filterByPeriod(RAW_DATA,p);
    var grouped=[], no=1, rowsHtml='';

    if(viewMode==='harian'){
        var allK={};
        RAW_DATA.forEach(function(r){ if(!allK[r.pin]) allK[r.pin]=r.nama; });
        var dayData={};
        Object.keys(allK).forEach(function(pin){ dayData[pin]={pin:pin,nama:allK[pin],masuk:null,pulang:null,terlambat:false,pulangCepat:false,absent:true}; });
        pr.forEach(function(r){
            var d=dayData[r.pin]; if(!d) return;
            if(r.isMasuk){ d.absent=false; if(!d.masuk||r.waktu<d.masuk){d.masuk=r.waktu;d.terlambat=r.terlambat;} }
            else{ if(!d.pulang||r.waktu>d.pulang){d.pulang=r.waktu;d.pulangCepat=r.pulangCepat;} }
        });
        grouped=Object.values(dayData).sort(function(a,b){ return a.nama<b.nama?-1:1; });
        grouped.forEach(function(emp){
            var st=emp.absent?'Tidak Masuk':emp.terlambat?'Terlambat':emp.pulangCepat?'Pulang Cepat':'Tepat Waktu';
            rowsHtml+='<tr><td>'+(no++)+'</td><td>'+emp.nama+'</td><td>'+emp.pin+'</td><td>'+(emp.masuk||'—')+'</td><td>'+(emp.pulang||(emp.absent?'—':'16:00:00'))+'</td><td>'+st+'</td></tr>';
        });
    } else {
        grouped=buildGrouped(pr,p);
        grouped.forEach(function(k){
            k.days.forEach(function(d){
                var st=d.absent?'Tidak Masuk':d.terlambat?'Terlambat':d.pulangCepat?'Pulang Cepat':d.masuk?'Tepat Waktu':'—';
                rowsHtml+='<tr><td>'+(no++)+'</td><td>'+d.tanggalFmt+'</td><td>'+k.nama+'</td><td>'+k.pin+'</td><td>'+(d.masuk||'—')+'</td><td>'+(d.pulang||(d.masuk?'16:00:00':'—'))+'</td><td>'+st+'</td></tr>';
            });
        });
    }

    var total=pr.length, tepat=pr.filter(function(r){return r.isMasuk&&!r.terlambat;}).length;
    var lambat=pr.filter(function(r){return r.terlambat;}).length, cepat=pr.filter(function(r){return r.pulangCepat;}).length, absent=hitungAbsen(pr,p);
    var headerCols=viewMode==='harian'?'<th>No</th><th>Nama</th><th>PIN</th><th>Masuk</th><th>Pulang</th><th>Status</th>':'<th>No</th><th>Tanggal</th><th>Nama</th><th>PIN</th><th>Masuk</th><th>Pulang</th><th>Status</th>';

    var printHtml='<!DOCTYPE html><html lang="id"><head><meta charset="UTF-8"><title>Laporan Absensi — '+lbl+'</title>'
        +'<style>body{font-family:Arial,sans-serif;font-size:12px;color:#111;margin:0;padding:0}.header{text-align:center;margin-bottom:20px;border-bottom:2px solid #333;padding-bottom:12px}.header h2{font-size:16px;margin:0 0 4px}.summary{display:flex;gap:10px;margin-bottom:16px;flex-wrap:wrap}.sum-box{border:1px solid #ddd;border-radius:6px;padding:8px 14px;text-align:center;min-width:90px}.sum-num{font-size:18px;font-weight:700;margin-bottom:2px}.sum-lbl{font-size:10px;color:#666;text-transform:uppercase}table{width:100%;border-collapse:collapse;font-size:11px}th{background:#f0f0f0;padding:7px 10px;text-align:left;border:1px solid #ccc;font-size:10px;text-transform:uppercase}td{padding:7px 10px;border:1px solid #e0e0e0}tr:nth-child(even) td{background:#fafafa}.footer{text-align:center;margin-top:20px;font-size:10px;color:#999;border-top:1px solid #eee;padding-top:10px}@page{size:A4;margin:1.5cm}</style></head><body>'
        +'<div class="header"><h2>Laporan Absensi Karyawan</h2><p>Periode: <strong>'+lbl+'</strong></p><p>Dicetak: '+new Date().toLocaleDateString('id-ID',{weekday:'long',day:'numeric',month:'long',year:'numeric'})+' pukul '+new Date().toLocaleTimeString('id-ID')+'</p></div>'
        +'<div class="summary"><div class="sum-box"><div class="sum-num" style="color:#6366f1">'+total+'</div><div class="sum-lbl">Total</div></div><div class="sum-box"><div class="sum-num" style="color:#16a34a">'+tepat+'</div><div class="sum-lbl">Tepat Waktu</div></div><div class="sum-box"><div class="sum-num" style="color:#d97706">'+lambat+'</div><div class="sum-lbl">Terlambat</div></div><div class="sum-box"><div class="sum-num" style="color:#e11d48">'+cepat+'</div><div class="sum-lbl">Pulang Cepat</div></div><div class="sum-box"><div class="sum-num" style="color:#7c3aed">'+absent+'</div><div class="sum-lbl">Tidak Masuk</div></div></div>'
        +'<table><thead><tr>'+headerCols+'</tr></thead><tbody>'+(rowsHtml||'<tr><td colspan="7" style="text-align:center;padding:20px;color:#999">Tidak ada data</td></tr>')+'</tbody></table>'
        +'<div class="footer">Kipin — Sistem Monitoring Absensi &middot; Total '+(no-1)+' baris data</div>'
        +'</body></html>';

    var win=window.open('','_blank','width=900,height=700');
    win.document.write(printHtml); win.document.close(); win.focus();
    setTimeout(function(){ win.print(); }, 400);
}

function onFilterDateChange(){
    var dateF=document.getElementById('filterDate').value;
    if(dateF){
        var parts=dateF.split('-'), y=parseInt(parts[0]), m=parseInt(parts[1]), d=parseInt(parts[2]);
        var selY=document.getElementById('filterTahun');
        for(var i=0;i<selY.options.length;i++){ if(parseInt(selY.options[i].value)===y){selY.selectedIndex=i;break;} }
        document.getElementById('filterBulan').value=m;
        if(viewMode==='harian'){ populateHari(y,m); document.getElementById('filterHari').value=d; }
        onPeriodChange();
    }
    applyFiltersAndRender();
}