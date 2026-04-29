<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login — PresenskiKu</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Space+Grotesk:wght@700;800&display=swap" rel="stylesheet">
    <style>
        * { margin:0; padding:0; box-sizing:border-box; }
        body { font-family:'Plus Jakarta Sans',sans-serif; min-height:100vh; display:flex; overflow:hidden; background:#060d1f; }

        /* ===== KIRI ===== */
        .pg-left { flex:1; position:relative; display:flex; align-items:center; justify-content:center; overflow:hidden; }

        .grid-bg {
            position:absolute; inset:0;
            background-image:
                linear-gradient(rgba(99,102,241,0.07) 1px, transparent 1px),
                linear-gradient(90deg, rgba(99,102,241,0.07) 1px, transparent 1px);
            background-size:40px 40px;
            animation:gridPulse 4s ease-in-out infinite;
        }
        @keyframes gridPulse { 0%,100%{opacity:.7} 50%{opacity:1} }

        /* Aurora */
        .aurora { position:absolute; inset:0; pointer-events:none; }
        .au1 { position:absolute; width:400px; height:400px; border-radius:50%; background:radial-gradient(circle,rgba(99,102,241,.18) 0%,transparent 70%); top:-80px; left:-80px; animation:auroraMove1 8s ease-in-out infinite; }
        .au2 { position:absolute; width:350px; height:350px; border-radius:50%; background:radial-gradient(circle,rgba(29,158,117,.15) 0%,transparent 70%); bottom:-60px; right:-60px; animation:auroraMove2 10s ease-in-out infinite; }
        .au3 { position:absolute; width:300px; height:300px; border-radius:50%; background:radial-gradient(circle,rgba(124,58,237,.12) 0%,transparent 70%); top:50%; left:50%; transform:translate(-50%,-50%); animation:auroraMove3 6s ease-in-out infinite; }
        @keyframes auroraMove1 { 0%,100%{transform:translate(0,0) scale(1)} 50%{transform:translate(30px,20px) scale(1.1)} }
        @keyframes auroraMove2 { 0%,100%{transform:translate(0,0) scale(1)} 50%{transform:translate(-20px,-30px) scale(1.15)} }
        @keyframes auroraMove3 { 0%,100%{transform:translate(-50%,-50%) scale(1)} 50%{transform:translate(-50%,-50%) scale(1.2)} }

        /* Particles */
        .particles { position:absolute; inset:0; }
        .p { position:absolute; border-radius:50%; animation:floatP linear infinite; opacity:0; }
        @keyframes floatP {
            0%   { opacity:0; transform:translateY(0) scale(0); }
            15%  { opacity:.9; transform:translateY(-10px) scale(1); }
            80%  { opacity:.7; }
            100% { opacity:0; transform:translateY(-160px) scale(.5); }
        }
        .p1  { width:4px;height:4px;background:#6366f1;left:10%;top:70%;animation-duration:8s;animation-delay:0s; }
        .p2  { width:3px;height:3px;background:#1d9e75;left:25%;top:80%;animation-duration:11s;animation-delay:1s; }
        .p3  { width:5px;height:5px;background:#7c3aed;left:80%;top:75%;animation-duration:9s;animation-delay:2s; }
        .p4  { width:3px;height:3px;background:#3b82f6;left:70%;top:85%;animation-duration:13s;animation-delay:.5s; }
        .p5  { width:4px;height:4px;background:#6ee7b7;left:15%;top:90%;animation-duration:7s;animation-delay:3s; }
        .p6  { width:3px;height:3px;background:#818cf8;left:90%;top:65%;animation-duration:10s;animation-delay:1.5s; }
        .p7  { width:5px;height:5px;background:#34d399;left:50%;top:88%;animation-duration:12s;animation-delay:4s; }
        .p8  { width:3px;height:3px;background:#f472b6;left:35%;top:72%;animation-duration:9s;animation-delay:2.5s; }
        .p9  { width:3px;height:3px;background:#a78bfa;left:60%;top:78%;animation-duration:14s;animation-delay:.8s; }
        .p10 { width:4px;height:4px;background:#60a5fa;left:40%;top:82%;animation-duration:8s;animation-delay:3.5s; }
        .p11 { width:2px;height:2px;background:#fb7185;left:20%;top:68%;animation-duration:10s;animation-delay:5s; }
        .p12 { width:4px;height:4px;background:#4ade80;left:75%;top:70%;animation-duration:7s;animation-delay:1.2s; }
        .p13 { width:3px;height:3px;background:#c084fc;left:55%;top:75%;animation-duration:11s;animation-delay:.3s; }
        .p14 { width:2px;height:2px;background:#38bdf8;left:85%;top:80%;animation-duration:9s;animation-delay:4.5s; }

        /* Shooting Stars */
        .star { position:absolute; height:1px; background:linear-gradient(90deg,rgba(255,255,255,0),rgba(255,255,255,.85)); border-radius:2px; animation:shoot linear infinite; opacity:0; }
        .s1 { width:80px; top:18%; left:-80px; animation-duration:3.5s; animation-delay:1s; transform:rotate(12deg); }
        .s2 { width:60px; top:38%; left:-60px; animation-duration:4s; animation-delay:3.2s; transform:rotate(15deg); }
        .s3 { width:100px; top:62%; left:-100px; animation-duration:3s; animation-delay:5.5s; transform:rotate(10deg); }
        .s4 { width:70px; top:78%; left:-70px; animation-duration:4.5s; animation-delay:7s; transform:rotate(8deg); }
        @keyframes shoot {
            0%   { opacity:0; transform:translateX(-60px) rotate(12deg); }
            8%   { opacity:1; }
            80%  { opacity:.8; }
            100% { opacity:0; transform:translateX(110vw) rotate(12deg); }
        }

        /* Rings */
        .scene { position:absolute; inset:0; display:flex; align-items:center; justify-content:center; }
        .ring-wrap { position:relative; width:300px; height:300px; }
        .ring { position:absolute; border-radius:50%; border:1px solid; animation:spin linear infinite; }
        .r1 { inset:0;   border-color:rgba(99,102,241,.4);  animation-duration:20s; }
        .r2 { inset:22px; border-color:rgba(29,158,117,.35); animation-duration:15s; animation-direction:reverse; }
        .r3 { inset:50px; border-color:rgba(124,58,237,.28); animation-duration:25s; }
        .r4 { inset:82px; border-color:rgba(59,130,246,.38); animation-duration:12s; animation-direction:reverse; }
        @keyframes spin { to { transform:rotate(360deg); } }

        /* Orbiting dots */
        .orbit-dot { position:absolute; border-radius:50%; box-shadow:0 0 8px currentColor; }
        .o1 { width:10px;height:10px;background:#6366f1;color:#6366f1;top:0;left:50%;transform:translateX(-50%); }
        .o2 { width:8px;height:8px;background:#1d9e75;color:#1d9e75;bottom:22px;right:22px; }
        .o3 { width:6px;height:6px;background:#7c3aed;color:#7c3aed;top:50px;left:50px; }
        .o4 { width:9px;height:9px;background:#3b82f6;color:#3b82f6;bottom:82px;left:82px; }
        .o5 { width:5px;height:5px;background:#f472b6;color:#f472b6;top:82px;right:82px;animation:orbitPulse 2s ease-in-out infinite; }
        @keyframes orbitPulse { 0%,100%{transform:scale(1);opacity:1} 50%{transform:scale(2);opacity:.4} }

        .center-morph {
            position:absolute; inset:108px; border-radius:50%;
            background:linear-gradient(135deg,#3b5de7,#1d9e75);
            animation:morph 6s ease-in-out infinite, glowPulse 3s ease-in-out infinite;
            display:flex; align-items:center; justify-content:center;
        }
        @keyframes morph {
            0%,100% { border-radius:50%; }
            33%     { border-radius:40% 60% 60% 40%/60% 30% 70% 40%; }
            66%     { border-radius:60% 40% 30% 70%/50% 60% 40% 50%; }
        }
        @keyframes glowPulse {
            0%,100% { box-shadow:0 0 30px rgba(99,102,241,.4), 0 0 60px rgba(29,158,117,.2); }
            50%     { box-shadow:0 0 60px rgba(99,102,241,.7), 0 0 120px rgba(29,158,117,.4); }
        }
        .center-morph svg { width:44px; height:44px; fill:white; opacity:.95; animation:iconBob 3s ease-in-out infinite; }
        @keyframes iconBob { 0%,100%{transform:translateY(0) scale(1)} 50%{transform:translateY(-4px) scale(1.07)} }

        /* Hex accent */
        .hex-row { position:absolute; bottom:70px; left:16px; display:flex; gap:3px; opacity:.18; }
        .hex { width:13px; height:15px; background:#6366f1; clip-path:polygon(50% 0%,100% 25%,100% 75%,50% 100%,0% 75%,0% 25%); animation:hexBlink 4s ease-in-out infinite; }
        .hex:nth-child(2){animation-delay:.5s;background:#1d9e75}
        .hex:nth-child(3){animation-delay:1s;background:#7c3aed}
        .hex:nth-child(4){animation-delay:1.5s}
        .hex:nth-child(5){animation-delay:2s;background:#1d9e75}
        @keyframes hexBlink { 0%,100%{opacity:.3} 50%{opacity:1} }

        /* Left bottom text */
        .left-text { position:absolute; z-index:2; text-align:center; bottom:48px; left:0; right:0; }
        .live-badge {
            display:inline-flex; align-items:center; gap:6px;
            background:rgba(255,255,255,.07); border:1px solid rgba(255,255,255,.12);
            border-radius:20px; padding:5px 13px; margin-bottom:12px;
            animation:badgeGlow 3s ease-in-out infinite;
        }
        @keyframes badgeGlow { 0%,100%{box-shadow:none} 50%{box-shadow:0 0 14px rgba(29,158,117,.35)} }
        .live-dot { width:6px;height:6px;border-radius:50%;background:#1d9e75;box-shadow:0 0 8px #1d9e75;animation:pulse 2s infinite; }
        @keyframes pulse { 0%,100%{opacity:1;transform:scale(1)} 50%{opacity:.4;transform:scale(.5)} }
        .live-txt { font-size:11px;color:rgba(255,255,255,.7);font-weight:600;letter-spacing:.5px; }

        .left-title {
            font-family:'Space Grotesk',sans-serif;
            font-size:clamp(34px,4vw,50px); font-weight:800; color:#fff;
            letter-spacing:-2px; line-height:1; margin-bottom:6px;
            animation:titleShimmer 4s ease-in-out infinite;
        }
        @keyframes titleShimmer { 0%,100%{text-shadow:none} 50%{text-shadow:0 0 40px rgba(129,140,248,.45)} }
        .left-title span {
            background:linear-gradient(135deg,#6ee7b7,#818cf8,#f472b6,#6ee7b7);
            -webkit-background-clip:text; -webkit-text-fill-color:transparent;
            background-clip:text; background-size:300%;
            animation:gradShift 5s linear infinite;
        }
        @keyframes gradShift { 0%{background-position:0%} 100%{background-position:300%} }
        .left-sub { font-size:13px; color:rgba(255,255,255,.4); letter-spacing:.3px; }

        /* ===== KANAN ===== */
        .pg-right {
            width:440px; background:#fff;
            display:flex; flex-direction:column; align-items:flex-start; justify-content:center;
            padding:3rem 2.5rem; position:relative; z-index:2;
            box-shadow:-20px 0 60px rgba(0,0,0,.35);
            animation:slideIn .6s ease;
        }
        @keyframes slideIn { from{opacity:0;transform:translateX(30px)} to{opacity:1;transform:translateX(0)} }

        /* Top shimmer bar */
        .pg-right::before {
            content:''; position:absolute; top:0; left:0; right:0; height:3px;
            background:linear-gradient(90deg,#3b5de7,#1d9e75,#7c3aed,#f472b6,#3b5de7);
            background-size:300%; animation:topBarShift 3s linear infinite;
        }
        @keyframes topBarShift { 0%{background-position:0%} 100%{background-position:300%} }

        .brand-row { display:flex; align-items:center; gap:11px; margin-bottom:2.2rem; }
        .brand-logo {
            width:46px; height:46px; border-radius:13px;
            background:linear-gradient(135deg,#3b5de7,#1d9e75);
            display:flex; align-items:center; justify-content:center;
            animation:logoBounce 4s ease-in-out infinite;
            box-shadow:0 4px 16px rgba(59,93,231,.3);
        }
        @keyframes logoBounce { 0%,100%{transform:translateY(0) rotate(0deg)} 25%{transform:translateY(-3px) rotate(-2deg)} 75%{transform:translateY(-1px) rotate(2deg)} }
        .brand-logo img { width:30px;height:30px;object-fit:contain; }
        .brand-logo svg { width:26px;height:26px;fill:white; }
        .brand-name { font-size:16px; font-weight:800; color:#0f172a; letter-spacing:-.4px; }
        .brand-sub  { font-size:11px; color:#94a3b8; font-weight:500; }

        .form-title { font-size:25px; font-weight:800; color:#0f172a; letter-spacing:-.5px; margin-bottom:4px; }
        .form-sub   { font-size:13px; color:#94a3b8; margin-bottom:1.8rem; min-height:20px; }
        .cursor     { display:inline-block; width:2px; height:14px; background:#94a3b8; margin-left:2px; vertical-align:middle; animation:blink 1s step-end infinite; }
        @keyframes blink { 0%,100%{opacity:1} 50%{opacity:0} }

        .alert-error {
            width:100%; background:#fff0f0; border:1px solid #fecdd3;
            border-radius:10px; padding:11px 14px;
            font-size:13px; color:#be123c; margin-bottom:1.2rem; font-weight:600;
            animation:shakeIn .4s ease;
        }
        @keyframes shakeIn { 0%,100%{transform:translateX(0)} 20%{transform:translateX(-6px)} 40%{transform:translateX(6px)} 60%{transform:translateX(-4px)} 80%{transform:translateX(4px)} }

        .f-label { font-size:11px; font-weight:700; color:#475569; text-transform:uppercase; letter-spacing:.6px; display:block; margin-bottom:6px; }
        .f-wrap { position:relative; width:100%; margin-bottom:1rem; }
        .f-ico { position:absolute; left:13px; top:50%; transform:translateY(-50%); color:#94a3b8; pointer-events:none; transition:color .2s; }
        .f-wrap:focus-within .f-ico { color:#3b5de7; }
        .f-wrap input {
            width:100%; padding:12px 14px 12px 42px;
            border:1.5px solid #e2e8f0; border-radius:11px;
            font-size:14px; font-family:inherit; color:#0f172a;
            background:#f8fafc; outline:none; box-sizing:border-box;
            transition:border-color .25s, background .25s, box-shadow .25s, transform .15s;
        }
        .f-wrap input:focus {
            border-color:#3b5de7; background:#fff;
            box-shadow:0 0 0 4px rgba(59,93,231,.1);
            transform:translateY(-1px);
        }
        .f-wrap input.is-invalid { border-color:#e24b4a; }
        .error-msg { font-size:12px; color:#e24b4a; margin-top:4px; }

        .chk-row { display:flex; align-items:center; justify-content:space-between; width:100%; margin-bottom:1.4rem; }
        .chk-lbl { font-size:13px; color:#475569; display:flex; align-items:center; gap:7px; font-weight:500; cursor:pointer; }
        .forgot { font-size:12px; color:#3b5de7; text-decoration:none; font-weight:600; transition:opacity .2s; }
        .forgot:hover { opacity:.7; text-decoration:underline; }

        .btn-sub {
            width:100%; padding:13px; border:none; border-radius:11px;
            background:linear-gradient(135deg,#2d4fc0,#1d9e75);
            color:#fff; font-size:15px; font-weight:700; font-family:inherit;
            cursor:pointer; letter-spacing:.2px;
            transition:opacity .2s, transform .15s, box-shadow .2s;
            margin-bottom:1.4rem;
            box-shadow:0 6px 20px rgba(45,79,192,.3);
            position:relative; overflow:hidden;
        }
        .btn-sub::after {
            content:''; position:absolute; inset:0;
            background:linear-gradient(135deg,rgba(255,255,255,.18),transparent);
            opacity:0; transition:opacity .2s;
        }
        .btn-sub:hover { opacity:.9; transform:translateY(-2px); box-shadow:0 12px 30px rgba(45,79,192,.45); }
        .btn-sub:hover::after { opacity:1; }
        .btn-sub:active { transform:scale(.98); }

        /* Ripple */
        .ripple {
            position:absolute; border-radius:50%;
            background:rgba(255,255,255,.3);
            transform:scale(0);
            animation:rippleAnim .6s linear;
            pointer-events:none;
        }
        @keyframes rippleAnim { to { transform:scale(5); opacity:0; } }

        .divider { display:flex; align-items:center; gap:10px; width:100%; margin-bottom:1.2rem; }
        .divider hr { flex:1; border:none; border-top:1px solid #e2e8f0; }
        .divider span { font-size:11px; color:#cbd5e1; white-space:nowrap; }

        .info-pill {
            display:flex; align-items:flex-start; gap:9px;
            background:#eef3ff; border:1px solid rgba(59,93,231,.1);
            border-radius:10px; padding:11px 14px; width:100%;
            animation:pillPulse 5s ease-in-out infinite;
        }
        @keyframes pillPulse { 0%,100%{box-shadow:none} 50%{box-shadow:0 0 12px rgba(59,93,231,.12)} }
        .info-pill svg { width:14px;height:14px;fill:#3b5de7;flex-shrink:0;margin-top:2px; }
        .info-pill p { font-size:12px; color:#3b5de7; line-height:1.5; }

        .footer-r { text-align:center; margin-top:1.4rem; font-size:11px; color:#cbd5e1; width:100%; }

        /* ===== RESPONSIVE ===== */
        @media (max-width:768px) {
            body { flex-direction:column; overflow:auto; }
            .pg-left { min-height:260px; }
            .pg-right { width:100%; padding:2rem 1.5rem; box-shadow:none; }
            .left-title { font-size:32px; }
        }
    </style>
</head>
<body>

<!-- ===== PANEL KIRI ===== -->
<div class="pg-left">
    <div class="grid-bg"></div>

    <div class="aurora">
        <div class="au1"></div>
        <div class="au2"></div>
        <div class="au3"></div>
    </div>

    <div class="particles">
        <div class="p p1"></div><div class="p p2"></div><div class="p p3"></div><div class="p p4"></div>
        <div class="p p5"></div><div class="p p6"></div><div class="p p7"></div><div class="p p8"></div>
        <div class="p p9"></div><div class="p p10"></div><div class="p p11"></div><div class="p p12"></div>
        <div class="p p13"></div><div class="p p14"></div>
    </div>

    <div class="star s1"></div>
    <div class="star s2"></div>
    <div class="star s3"></div>
    <div class="star s4"></div>

    <div class="scene">
        <div class="ring-wrap">
            <div class="ring r1"><div class="orbit-dot o1"></div></div>
            <div class="ring r2"><div class="orbit-dot o2"></div></div>
            <div class="ring r3"><div class="orbit-dot o3"></div></div>
            <div class="ring r4">
                <div class="orbit-dot o4"></div>
                <div class="orbit-dot o5"></div>
            </div>
            <div class="center-morph">
                <svg viewBox="0 0 24 24"><path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5"/></svg>
            </div>
        </div>
    </div>

    <div class="hex-row">
        <div class="hex"></div><div class="hex"></div><div class="hex"></div>
        <div class="hex"></div><div class="hex"></div>
    </div>

    <div class="left-text">
        <div class="live-badge">
            <span class="live-dot"></span>
            <span class="live-txt">SISTEM AKTIF</span>
        </div>
        <div class="left-title">Presensi<span>Ku</span></div>
        <div class="left-sub">Monitoring Absensi Karyawan</div>
    </div>
</div>

<!-- ===== PANEL KANAN ===== -->
<div class="pg-right">
    <div class="brand-row">
        <div class="brand-logo">
            <img src="{{ asset('images/kipin.png') }}" alt="Logo"
                 onerror="this.style.display='none';this.nextElementSibling.style.display='block'">
            <svg style="display:none" viewBox="0 0 24 24"><path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5"/></svg>
        </div>
        <div>
            <div class="brand-name">PresensiKu</div>
            <div class="brand-sub">Monitoring Absensi</div>
        </div>
    </div>

    <div class="form-title">Selamat datang</div>
    <div class="form-sub" id="typedSub"><span class="cursor"></span></div>

    @if($errors->any())
        <div class="alert-error">{{ $errors->first() }}</div>
    @endif

    <form method="POST" action="{{ route('login') }}" style="width:100%">
        @csrf

        <label class="f-label" for="email">Email / Username</label>
        <div class="f-wrap">
            <svg class="f-ico" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/>
            </svg>
            <input type="text" id="email" name="email"
                    value="{{ old('email') }}"
                    {{-- placeholder="admin@123.id" --}}
                    class="{{ $errors->has('email') ? 'is-invalid' : '' }}"
                    required />
        </div>
        @error('email') <p class="error-msg">{{ $message }}</p> @enderror

        <label class="f-label" for="password">Password</label>
        <div class="f-wrap">
            <svg class="f-ico" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <rect x="3" y="11" width="18" height="11" rx="2"/>
                <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
            </svg>
                <input type="password" id="password" name="password"
                    {{-- placeholder="••••••••" --}}
                    class="{{ $errors->has('password') ? 'is-invalid' : '' }}"
                    required />
        </div>
        @error('password') <p class="error-msg">{{ $message }}</p> @enderror

        <div class="chk-row">
            <label class="chk-lbl">
                <input type="checkbox" name="remember" {{ old('remember') ? 'checked' : '' }}>
                Ingat saya
            </label>
            @if (Route::has('password.request'))
                <a class="forgot" href="{{ route('password.request') }}">Lupa password?</a>
            @endif
        </div>

        <button type="submit" class="btn-sub" id="loginBtn">Masuk ke Dashboard</button>
    </form>

    <div class="divider"><hr/><span>akses terbatas</span><hr/></div>

    <div class="info-pill">
        <svg viewBox="0 0 24 24"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
        <p>Hanya untuk administrator sistem rekapitulasi absensi PresenskiKu.</p>
    </div>

    <div class="footer-r" style="color: rgb(88, 133, 165)">© 2026 PresenskiKu — Sistem Monitoring Absensi</div>
</div>

<script>
    // Typing animation subtitle
    const subtitleText = "Masuk sebagai administrator";
    const subtitleEl = document.getElementById('typedSub');
    let charIndex = 0;
    function typeSubtitle() {
        if (charIndex <= subtitleText.length) {
            subtitleEl.innerHTML = subtitleText.substring(0, charIndex) + '<span class="cursor"></span>';
            charIndex++;
            setTimeout(typeSubtitle, 55);
        }
    }
    setTimeout(typeSubtitle, 800);

    // Ripple effect on login button
    const loginBtn = document.getElementById('loginBtn');
    loginBtn.addEventListener('click', function(e) {
        const ripple = document.createElement('span');
        ripple.className = 'ripple';
        const rect = loginBtn.getBoundingClientRect();
        const size = Math.max(rect.width, rect.height);
        ripple.style.cssText = `
            width: ${size}px;
            height: ${size}px;
            left: ${e.clientX - rect.left - size / 2}px;
            top: ${e.clientY - rect.top - size / 2}px;
        `;
        loginBtn.appendChild(ripple);
        setTimeout(() => ripple.remove(), 700);
    });
</script>

</body>
</html>