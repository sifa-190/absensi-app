
    // Slide out on form submit
    const loginForm = document.querySelector('form');
    const loginPanel = document.getElementById('loginPanel');
    if (loginForm) {
        loginForm.addEventListener('submit', function(e) {
            e.preventDefault();
            loginPanel.classList.add('slide-out');
            setTimeout(() => loginForm.submit(), 320);
        });
    }

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

