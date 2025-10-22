(() => {
  const reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

  /* ===== NAV PILL PINK (centered) ===== */
  const nav = document.querySelector('.nav');
  if (nav) {
    const pill = document.createElement('span');
    pill.className = 'nav-pill';
    nav.appendChild(pill);

    const links = [...nav.querySelectorAll('a')];

    // padding ekstra untuk kapsul (sesuaikan jika mau)
    const padX = 16;   // horizontal (px)
    const padY = 6;    // vertical (px)

    const setTo = (el) => {
      if (!el) return;
      const nr = nav.getBoundingClientRect();
      const r  = el.getBoundingClientRect();

      // width/height kapsul = ukuran link + padding
      const w = r.width  + padX * 2;
      const h = r.height + padY * 2;

      // posisi center kapsul relatif ke container .nav
      const cx = r.left - nr.left + r.width  / 2;
      const cy = r.top  - nr.top  + r.height / 2;

      pill.style.width  = w + 'px';
      pill.style.height = h + 'px';
      pill.style.left   = cx + 'px';
      pill.style.top    = cy + 'px';
      pill.style.opacity = '1';
    };

    const active = links.find(a => a.classList.contains('active')) || links[0];
    setTo(active);

    links.forEach(a => {
      a.addEventListener('mouseenter', () => setTo(a));
      a.addEventListener('focus', () => setTo(a));
    });
    nav.addEventListener('mouseleave', () => setTo(active));
    window.addEventListener('resize', () => setTo(active));
  }

  /* ===== Ripple + Magnetic hover (buttons) ===== */
  document.querySelectorAll('.gs-btn').forEach(btn => {
    // Ripple
    btn.addEventListener('pointerdown', (ev) => {
      if (reduceMotion) return;
      const r = btn.getBoundingClientRect();
      const el = document.createElement('span');
      el.className = 'ripple';
      const size = Math.max(r.width, r.height) * 1.6;
      el.style.width = el.style.height = size + 'px';
      el.style.left = (ev.clientX - r.left - size/2) + 'px';
      el.style.top  = (ev.clientY - r.top  - size/2) + 'px';
      btn.appendChild(el);
      el.addEventListener('animationend', () => el.remove(), { once:true });
    });

    // Magnetic
    let raf; const maxShift = 10;
    const onMove = (ev) => {
      if (reduceMotion) return;
      const r = btn.getBoundingClientRect();
      const cx = r.left + r.width/2, cy = r.top + r.height/2;
      const dx = (ev.clientX - cx) / (r.width/2);
      const dy = (ev.clientY - cy) / (r.height/2);
      const tx = Math.max(-1, Math.min(1, dx)) * maxShift;
      const ty = Math.max(-1, Math.min(1, dy)) * maxShift;
      if (raf) cancelAnimationFrame(raf);
      raf = requestAnimationFrame(() => { btn.style.transform = `translate(${tx}px, ${ty}px)`; });
    };
    const reset = () => { if (raf) cancelAnimationFrame(raf); btn.style.transform = ''; };
    btn.addEventListener('pointermove', onMove);
    btn.addEventListener('pointerleave', reset);
    btn.addEventListener('blur', reset);
  });

  /* ===== Scroll reveal ===== */
  const revealEls = document.querySelectorAll('.feature-card, .menu-card, .story-img, .story-copy');
  if (revealEls.length && !reduceMotion) {
    const io = new IntersectionObserver((entries) => {
      entries.forEach(e => { if (e.isIntersecting) { e.target.classList.add('reveal-in'); io.unobserve(e.target); } });
    }, { threshold: 0.18 });
    revealEls.forEach(el => { el.classList.add('will-reveal'); io.observe(el); });
  }
})();
