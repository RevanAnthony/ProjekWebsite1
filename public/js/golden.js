(() => {
  const reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

  /* ===== NAV PILL (gelembung) =====
     - Dibuat & diposisikan dinamis
     - Dibikin lebih lebar (padX/padY) */
  const nav = document.querySelector('.nav');
  if (nav) {
    const pill = document.createElement('span');
    pill.className = 'nav-pill';
    nav.appendChild(pill);

    const links = [...nav.querySelectorAll('a')];
    const padX = 24;  // perbesar horizontal
    const padY = 10;  // perbesar vertical

    const setTo = (el) => {
      if (!el) return;
      const nr = nav.getBoundingClientRect();
      const r  = el.getBoundingClientRect();
      const w = r.width  + padX * 2;
      const h = r.height + padY * 2;
      const cx = r.left - nr.left + r.width/2;
      const cy = r.top  - nr.top  + r.height/2;

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
      a.addEventListener('focus',      () => setTo(a));
    });
    nav.addEventListener('mouseleave', () => setTo(active));
    window.addEventListener('resize',  () => setTo(active));
  }

  /* ===== USER MENU toggle (pojok kanan) ===== */
  (function(){
    const btn  = document.getElementById('userBtn');
    const menu = document.getElementById('userMenu');
    if (!btn || !menu) return;
    const toggle = () => {
      const open = menu.classList.toggle('open');
      menu.setAttribute('aria-hidden', open ? 'false' : 'true');
    };
    btn.addEventListener('click', toggle);
    document.addEventListener('click', (e) => {
      if (!menu.contains(e.target) && !btn.contains(e.target)) {
        menu.classList.remove('open');
        menu.setAttribute('aria-hidden','true');
      }
    });
  })();

  /* ===== Ripple + Magnetic (buttons) ===== */
  document.querySelectorAll('.gs-btn').forEach(btn => {
    btn.addEventListener('pointerdown', (ev) => {
      if (reduceMotion) return;
      const r = btn.getBoundingClientRect();
      const el = document.createElement('span');
      el.className = 'ripple';
      const size = Math.max(r.width, r.height) * 1.6;
      el.style.width = el.style.height = size + 'px';
      el.style.left  = (ev.clientX - r.left - size/2) + 'px';
      el.style.top   = (ev.clientY - r.top  - size/2) + 'px';
      btn.appendChild(el);
      el.addEventListener('animationend', () => el.remove(), { once:true });
    });

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
    const reset = () => { if (raf) cancelAnimationFrame(raf); btn.style.transform = '' };
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

  (() => {
  const body = document.body;
  const openBtn  = document.querySelector('[data-cart-open]');
  const closes   = document.querySelectorAll('[data-cart-close]');
  const backdrop = document.querySelector('.cart-backdrop');

  const open  = () => body.classList.add('cart-open');
  const close = () => body.classList.remove('cart-open');

  openBtn?.addEventListener('click', open);
  closes.forEach(el => el.addEventListener('click', close));
  backdrop?.addEventListener('click', close);

  // ESC untuk menutup
  document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape') close();
  });
  // ===== Drawer Cart (right) =====
(() => {
  const drawer = document.querySelector('.cart-drawer');
  const overlay = document.querySelector('.cart-overlay');
  const openers = document.querySelectorAll('[data-cart-open], .fab-cart'); // tombol FAB existing ikut dipakai
  const closer  = document.querySelector('[data-cart-close]');

  if(!drawer) return;

  const open = () => {
    drawer.classList.add('open');
    overlay?.classList.add('show');
    document.documentElement.style.overflow = 'hidden';
  };
  const close = () => {
    drawer.classList.remove('open');
    overlay?.classList.remove('show');
    document.documentElement.style.overflow = '';
  };

  openers.forEach(btn => btn.addEventListener('click', open));
  closer?.addEventListener('click', close);
  overlay?.addEventListener('click', close);
  document.addEventListener('keydown', (e) => { if(e.key === 'Escape') close(); });

  // jangan biarkan klik di dalam drawer menutup overlay
  drawer.addEventListener('click', (e) => e.stopPropagation());
})();

})();

})();
