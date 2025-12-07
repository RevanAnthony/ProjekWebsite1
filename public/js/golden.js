/* =========================================================
   GOLDEN SPICE — Global JS (persist cart ke DB saat login)
   ========================================================= */

/* ---------- Globals / Flags ---------- */
const CSRF = document.querySelector('meta[name="csrf-token"]')?.content || '';
const IS_AUTH = (
  document.querySelector('meta[name="app-auth"]')?.content ||
  document.querySelector('meta[name="auth"]')?.content
) === '1';

/* ---------- Local cart helpers (guest fallback) ---------- */
const KEY_NEW = 'gs_cart_v1';
const KEY_OLD = 'gs_cart';
const fmtID   = n => Number(n || 0).toLocaleString('id-ID');
const rupiah  = n => 'Rp ' + fmtID(n);

const normalizeCartRaw = raw => {
  if (!raw) return {};
  let obj; try { obj = JSON.parse(raw) || {}; } catch { return {}; }
  const out = {};
  (Array.isArray(obj) ? obj : Object.values(obj)).forEach(it => {
    const id = String(it.id ?? it.product_id);
    if (!id) return;
    out[id] = {
      id,
      name:  it.name  || '',
      price: Number(it.price || 0),
      qty:   Number(it.qty   || 0),
      img:   it.img   || ''
    };
  });
  return out;
};
function lcRead(){ return { items: { ...normalizeCartRaw(localStorage.getItem(KEY_OLD)),
                                     ...normalizeCartRaw(localStorage.getItem(KEY_NEW)) } }; }
function lcWrite(state){ localStorage.setItem(KEY_NEW, JSON.stringify(state.items)); }

/* ---------- Debounced sync local -> server (hanya jika login) ---------- */
let SYNC_TIMER = null;
function syncToServer() {
  if (!IS_AUTH) return;
  const st    = lcRead();
  const items = Object.values(st.items || {}).map(i => ({
    produk_id: Number(i.id),
    qty: Number(i.qty || 0)
  }));
  clearTimeout(SYNC_TIMER);
  if (!items.length) return;
  SYNC_TIMER = setTimeout(() => {
    fetch('/cart/sync', {
      method: 'POST',
      headers: {
        'Accept':'application/json',
        'X-Requested-With':'XMLHttpRequest',
        'Content-Type':'application/json',
        'X-CSRF-TOKEN': CSRF
      },
      credentials: 'same-origin',
      body: JSON.stringify({ items })
    }).catch(()=>{});
  }, 200);
}

/* ---------- Small API helper (SELALU kirim CSRF) ---------- */
async function api(url, method = 'GET', body = null) {
  const headers = {
    'Accept': 'application/json',
    'X-Requested-With': 'XMLHttpRequest',
    'X-CSRF-TOKEN': CSRF               // <-- wajib untuk POST/PATCH/DELETE
  };
  if (body !== null) headers['Content-Type'] = 'application/json';

  const res = await fetch(url, {
    method,
    headers,
    credentials: 'same-origin',
    body: body !== null ? JSON.stringify(body) : null
  });

  if (res.status === 419) throw new Error('CSRF token mismatch (419)');
  if (!res.ok) {
    const text = await res.text().catch(()=> '');
    throw new Error(`${method} ${url} => ${res.status} ${text}`);
  }
  return res.json();
}

/* Convenience wrappers */
const cartFetch   = ()                         => api('/cart');
const cartAdd     = (produk_id, qty = 1)       => api('/cart/add', 'POST',    { produk_id: Number(produk_id), qty: Number(qty) });
const cartUpdate  = (detailId, qty)            => api(`/cart/item/${detailId}`, 'PATCH',  { qty: Number(qty) });
const cartRemove  = (detailId)                 => api(`/cart/item/${detailId}`, 'DELETE');
const cartClear   = ()                         => api('/cart', 'DELETE');

/* =========================================================
   NAV PILL (indicator)
   ========================================================= */
(() => {
  const nav = document.querySelector('#mainNav') || document.querySelector('.nav');
  if (!nav) return;
  let pill = nav.querySelector('.nav-pill'); if (!pill){ pill=document.createElement('span'); pill.className='nav-pill'; nav.appendChild(pill); }
  const padX=22,padY=8;
  const move = (a)=>{ if(!a)return; const nr=nav.getBoundingClientRect(), r=a.getBoundingClientRect();
    const w=r.width+padX*2,h=r.height+padY*2,cx=r.left-nr.left+r.width/2,cy=r.top-nr.top+r.height/2;
    Object.assign(pill.style,{width:w+'px',height:h+'px',left:cx+'px',top:cy+'px',opacity:'1'}); };
  const active = nav.querySelector('a.active') || nav.querySelector('a');
  requestAnimationFrame(()=>move(active));
  nav.addEventListener('mouseover',e=>{const a=e.target.closest('a'); if(a) move(a);});
  nav.addEventListener('mouseleave',()=>move(active));
  addEventListener('resize',()=>move(active));
})();

/* =========================================================
   CART — Drawer + Kartu Produk (sinkron ke server)
   ========================================================= */
(() => {
  const overlay = document.querySelector('.cart-overlay');
  const drawer  = document.querySelector('.cart-drawer');
  const badge   = document.querySelector('#cartBadge');
  const countEl = document.querySelector('#cartCountHdr');
  const list    = document.querySelector('#cartList');
  const empty   = document.querySelector('#cartEmpty');
  const totalEl = document.querySelector('#cartTotal');
  const MODE    = IS_AUTH ? 'server' : 'local';

  if (!overlay || !drawer || !badge || !list) return;

  const setBadge = n => { badge.textContent = n; if (countEl) countEl.textContent = n; };
  const setTotal = n => { if (totalEl) totalEl.textContent = rupiah(n); };

  /* --- Index productId -> { detailId, qty } utk tombol +/- di kartu --- */
  let CART_INDEX = {};
  function updateCardQuantities() {
    document.querySelectorAll('.menu-card').forEach(card => {
      const pid = Number(card.getAttribute('data-pid') || card.getAttribute('data-id'));
      const entry = CART_INDEX[pid];
      const val = card.querySelector('[data-qty-val], .qty-val');
      const qty = entry ? entry.qty : 0;
      if (val) val.textContent = qty > 0 ? qty : 1;  // default tampilan 1
      if (qty > 0) card.classList.add('has-qty');
      else         card.classList.remove('has-qty');
    });
  }

  /* ---------- Renderers ---------- */
  function renderLocal() {
    const st = lcRead();
    const arr = Object.values(st.items);
    const count = arr.reduce((s,i)=>s+i.qty,0);
    const total = arr.reduce((s,i)=>s+i.qty*i.price,0);

    setBadge(count); setTotal(total);

    list.innerHTML = '';
    if (!count) { empty.style.display=''; CART_INDEX={}; updateCardQuantities(); return; }
    empty.style.display='none';

    arr.forEach(i => {
      const li = document.createElement('li');
      li.className = 'cart-item';
      li.innerHTML = `
        <img class="cart-thumb" src="${i.img||''}" alt="">
        <div class="cart-info">
          <div class="cart-name">${i.name}</div>
          <div class="cart-qty">
            <button class="btn dec" data-id="${i.id}">−</button>
            <span class="val">${i.qty}</span>
            <button class="btn inc" data-id="${i.id}">+</button>
          </div>
        </div>
        <div class="cart-price">${rupiah(i.qty*i.price)}</div>`;
      list.appendChild(li);
    });

    CART_INDEX = {};
    arr.forEach(i => { CART_INDEX[Number(i.id)] = { detailId: null, qty: Number(i.qty) }; });
    updateCardQuantities();
  }

  function renderServer(state) {
    if (!state) return;
    const items = Array.isArray(state.items) ? state.items : [];

    const count = Number(state.count ?? items.reduce((s,i)=>s+Number(i.jumlah||0),0));
    const total = Number(state.total ?? items.reduce((s,i)=>s+Number(i.jumlah||0)*Number(i.harga||0),0));
    setBadge(count); setTotal(total);

    list.innerHTML = '';
    if (!items.length) { empty.style.display=''; CART_INDEX={}; updateCardQuantities(); return; }
    empty.style.display='none';

    items.forEach(i => {
      const detailId = i.id_detail_keranjang ?? i.id_detail ?? i.detail_id ?? i.id;
      const pid   = Number(i.id_produk ?? i.produk_id);
      const qty   = Number(i.jumlah ?? i.qty ?? 0);
      const nama  = i.nama ?? i.name ?? '';
      const harga = Number(i.harga ?? i.price ?? 0);
      const img   = i.url_gambar || i.img || '';

      const li = document.createElement('li');
      li.className = 'cart-item';
      li.innerHTML = `
        <img class="cart-thumb" src="${img}" alt="">
        <div class="cart-info">
          <div class="cart-name">${nama}</div>
          <div class="cart-qty">
            <button class="btn dec" data-detail="${detailId}" data-qty="${qty}">−</button>
            <span class="val">${qty}</span>
            <button class="btn inc" data-detail="${detailId}" data-qty="${qty}">+</button>
          </div>
        </div>
        <div class="cart-price">${rupiah(qty*harga)}</div>`;
      list.appendChild(li);

      if (pid) CART_INDEX[pid] = { detailId, qty };
    });

    updateCardQuantities();
  }

  async function refreshServer(){
    try {
      const data = await cartFetch();
      if (data) renderServer(data);
    } catch (err) {
      console.error('cartFetch failed:', err);
    }
  }

  /* ---------- Add-to-cart (tombol TAMBAH) ---------- */
  document.addEventListener('click', async (e) => {
    const btn = e.target.closest('[data-add-to-cart], .add-to-cart');
    if (!btn) return;
    e.preventDefault();
    const pid = Number(btn.dataset.addToCart ?? btn.dataset.id);
    if (!pid) return;

    try {
      if (MODE === 'server') {
        const data = await cartAdd(pid, 1);
        if (data) renderServer(data);
      } else {
        const st = lcRead();
        const id = String(pid);
        const price = Number(btn.dataset.price||0);
        const name  = btn.dataset.name||'';
        const img   = btn.dataset.img||'';
        st.items[id] = st.items[id] || { id, name, price, img, qty: 0 };
        st.items[id].qty += 1;
        lcWrite(st);
        renderLocal();
        syncToServer();
      }
    } catch (err) {
      console.error('Add to cart failed:', err);
    }
  });

  /* ---------- +/- di KARTU PRODUK ---------- */
  document.addEventListener('click', async (e) => {
    const inc = e.target.closest('[data-qty-inc], .qty-btn.inc');
    const dec = e.target.closest('[data-qty-dec], .qty-btn.dec');
    if (!inc && !dec) return;

    const card = e.target.closest('.menu-card');
    const pidAttr = (inc || dec)?.dataset.pid || card?.getAttribute('data-pid') || card?.getAttribute('data-id');
    const pid = Number(pidAttr);
    if (!pid) return;

    try {
      if (MODE === 'server') {
        const entry = CART_INDEX[pid];
        if (!entry) {
          const data = await cartAdd(pid, 1);
          if (data) renderServer(data);
          return;
        }
        const next = entry.qty + (inc ? 1 : -1);
        if (next <= 0) await cartRemove(entry.detailId);
        else           await cartUpdate(entry.detailId, next);
        await refreshServer();
      } else {
        const st = lcRead();
        const key = String(pid);
        const it = st.items[key] || { id:key, qty:0, name:'', price:0, img:'' };
        it.qty += (inc ? 1 : -1);
        if (it.qty <= 0) delete st.items[key]; else st.items[key] = it;
        lcWrite(st);
        renderLocal();
        syncToServer();
      }
    } catch (err) {
      console.error('Card +/- failed:', err);
    }
  });

  /* ---------- +/- di DRAWER ---------- */
  list.addEventListener('click', async (e) => {
    const incDetail = e.target.closest('.inc[data-detail]');
    const decDetail = e.target.closest('.dec[data-detail]');
    const incLocal  = e.target.closest('.inc[data-id]');
    const decLocal  = e.target.closest('.dec[data-id]');

    try {
      if (MODE === 'server' && (incDetail || decDetail)) {
        const btn = incDetail || decDetail;
        const detailId = btn.dataset.detail;
        const curQty   = Number(btn.dataset.qty || 0);
        const nextQty  = incDetail ? curQty + 1 : curQty - 1;
        if (nextQty <= 0) await cartRemove(detailId);
        else              await cartUpdate(detailId, nextQty);
        await refreshServer();
        return;
      }

      if (MODE === 'local' && (incLocal || decLocal)) {
        const id = (incLocal || decLocal).dataset.id;
        const st = lcRead();
        const it = st.items[id]; if (!it) return;
        it.qty += incLocal ? 1 : -1;
        if (it.qty <= 0) delete st.items[id]; else st.items[id] = it;
        lcWrite(st);
        renderLocal();
        syncToServer();
      }
    } catch (err) {
      console.error('Drawer +/- failed:', err);
    }
  });

  /* ---------- Clear all ---------- */
  document.querySelector('#cartClearBtn')?.addEventListener('click', async () => {
    try {
      if (MODE === 'server') { await cartClear(); await refreshServer(); }
      else { lcWrite({ items: {} }); renderLocal(); syncToServer(); }
    } catch (err) {
      console.error('Clear cart failed:', err);
    }
  });

  /* ---------- Drawer open/close ---------- */
  const open  = e => { e?.preventDefault(); overlay.classList.add('show'); drawer.classList.add('open'); document.documentElement.classList.add('drawer-open'); };
  const close = e => { e?.preventDefault(); overlay.classList.remove('show'); drawer.classList.remove('open'); document.documentElement.classList.remove('drawer-open'); };
  document.addEventListener('click', e => {
    if (e.target.closest('[data-cart-open], .fab-cart')) open(e);
    if (e.target.closest('[data-cart-close]') || e.target === overlay) close(e);
  });
  drawer.addEventListener('click', e => { if (e.target.closest('[data-cart-close]')) { close(e); return; } e.stopPropagation(); });
  document.addEventListener('keydown', e => { if (e.key === 'Escape') close(e); });

  /* ---------- Start ---------- */
  if (MODE === 'server') refreshServer();
  else { renderLocal(); syncToServer(); }
})();

/* =========================================================
   UI EXTRAS: User dropdown, Ripple + Magnetic, Scroll reveal
   ========================================================= */
(() => {
  // User dropdown
  const btn = document.querySelector('#userBtn');
  const menu = document.querySelector('#userMenu');
  if (btn && menu) {
    const toggle = () => { const open = menu.classList.toggle('open'); menu.setAttribute('aria-hidden', open ? 'false' : 'true'); };
    btn.addEventListener('click', (e) => { e.preventDefault(); toggle(); });
    document.addEventListener('click', (e) => {
      if (!menu.contains(e.target) && !btn.contains(e.target)) { menu.classList.remove('open'); menu.setAttribute('aria-hidden','true'); }
    });
  }

  // Ripple + Magnetic
  const reduce = matchMedia('(prefers-reduced-motion: reduce)').matches;
  document.querySelectorAll('.gs-btn').forEach((b) => {
    b.addEventListener('pointerdown', (ev) => {
      if (reduce) return;
      const r = b.getBoundingClientRect();
      const el = document.createElement('span');
      el.className = 'ripple';
      const size = Math.max(r.width, r.height) * 1.6;
      Object.assign(el.style, {
        position: 'absolute',
        borderRadius: '9999px',
        pointerEvents: 'none',
        width: size + 'px',
        height: size + 'px',
        left: (ev.clientX - r.left - size/2) + 'px',
        top:  (ev.clientY - r.top  - size/2) + 'px'
      });
      b.appendChild(el);
      el.addEventListener('animationend', () => el.remove(), { once:true });
      setTimeout(() => el.remove(), 700);
    });

    let raf; const maxShift = 10;
    const onMove = (ev) => {
      if (reduce) return;
      const r = b.getBoundingClientRect();
      const cx = r.left + r.width/2, cy = r.top + r.height/2;
      const dx = (ev.clientX - cx) / (r.width/2);
      const dy = (ev.clientY - cy) / (r.height/2);
      const tx = Math.max(-1, Math.min(1, dx)) * maxShift;
      const ty = Math.max(-1, Math.min(1, dy)) * maxShift;
      if (raf) cancelAnimationFrame(raf);
      raf = requestAnimationFrame(() => { b.style.transform = `translate(${tx}px, ${ty}px)`; });
    };
    const reset = () => { if (raf) cancelAnimationFrame(raf); b.style.transform = ''; };
    b.addEventListener('pointermove', onMove);
    b.addEventListener('pointerleave', reset);
    b.addEventListener('blur', reset);
  });

  // Scroll reveal
  const els = document.querySelectorAll('.feature-card, .menu-card, .story-img, .story-copy');
  if (els.length && !reduce) {
    const io = new IntersectionObserver((entries) => {
      entries.forEach(e => { if (e.isIntersecting) { e.target.classList.add('reveal-in'); io.unobserve(e.target); } });
    }, { threshold: 0.18 });
    els.forEach(el => { el.classList.add('will-reveal'); io.observe(el); });
  }
  
  
/* =========================================================
   PASSWORD — Toggle visibility (show/hide)
   ========================================================= */

(() => {
  document.addEventListener('click', (e) => {
    const btn = e.target.closest('[data-password-toggle]');
    if (!btn) return;

    e.preventDefault();

    // 1) Utama: cari via selector di atribut
    let input = null;
    const sel = btn.getAttribute('data-password-toggle');
    if (sel) input = document.querySelector(sel);

    // 2) Cadangan: cari input saudara dalam wrapper yang sama
    if (!input) {
      const scope = btn.closest('.gs-input') || btn.parentElement;
      input = scope?.querySelector('input[type="password"], input[type="text"]') || null;
    }
    if (!input) return;

    // Toggle
    const show = input.type === 'password';
    try { input.type = show ? 'text' : 'password'; } catch (_) {}
    btn.setAttribute('aria-pressed', show ? 'true' : 'false');

    // Fokus & taruh kursor di akhir
    input.focus({ preventScroll: true });
    try {
      const len = input.value.length;
      input.setSelectionRange(len, len);
    } catch (_) {}
  });
})();

/* =========================================================
   REGISTER — Password strength meter + match checker
   (auto-aktif hanya di halaman register)
   ========================================================= */
(() => {
  const form =
    document.querySelector('form[action*="register.store"]') ||
    document.querySelector('form[action*="/register"]') ||
    document.querySelector('form[action*="register"]');
  if (!form) return;

  const pw  = form.querySelector('#password[name="password"]');
  const pc  = form.querySelector('#password_confirmation[name="password_confirmation"]');
  const btn = form.querySelector('.btn-submit, button[type="submit"], input[type="submit"]');
  if (!pw || !pc) return;

  // --- UI yang sudah ada / atau auto-buat ---
  let meterWrap = form.querySelector('#pwMeter');
  if (!meterWrap) {
    meterWrap = document.createElement('div');
    meterWrap.className = 'pw-meter';
    meterWrap.id = 'pwMeter';
    meterWrap.innerHTML = `
      <div class="bar"><div class="fill" style="height:8px;width:0%"></div></div>
      <div class="pw-hints" style="display:flex;gap:8px;flex-wrap:wrap;margin-top:6px;font-size:12px;color:#666">
        <span class="chip bad" data-crit="len" style="padding:4px 8px;border-radius:999px;background:#f3f3f3">≥ 8 karakter</span>
        <span class="chip bad" data-crit="mix" style="padding:4px 8px;border-radius:999px;background:#f3f3f3">Huruf besar & kecil</span>
        <span class="chip bad" data-crit="num" style="padding:4px 8px;border-radius:999px;background:#f3f3f3">Angka</span>
        <span class="chip bad" data-crit="sym" style="padding:4px 8px;border-radius:999px;background:#f3f3f3">Simbol</span>
      </div>`;
    const field = pw.closest('.field') || pw.parentElement;
    field && field.appendChild(meterWrap);
  }
  const meterFill = meterWrap.querySelector('.fill');
  const chips = {
    len: meterWrap.querySelector('[data-crit="len"]'),
    mix: meterWrap.querySelector('[data-crit="mix"]'),
    num: meterWrap.querySelector('[data-crit="num"]'),
    sym: meterWrap.querySelector('[data-crit="sym"]'),
  };

  // Label "Keamanan password: Low/Medium/High"
  let strengthEl = form.querySelector('#pwStrength');
  if (!strengthEl) {
    strengthEl = document.createElement('div');
    strengthEl.id = 'pwStrength';
    strengthEl.className = 'pw-strength';
    strengthEl.style.marginTop = '6px';
    strengthEl.style.fontSize  = '12px';
    strengthEl.style.fontWeight = '700';
    strengthEl.textContent = 'Keamanan password: Low';
    meterWrap.appendChild(strengthEl); // tampil di bawah meter/hints
  }

  // Catatan kecocokan password
  let matchNote = form.querySelector('#pwMatch');
  if (!matchNote) {
    matchNote = document.createElement('div');
    matchNote.id = 'pwMatch';
    matchNote.className = 'match-note';
    matchNote.style.fontSize = '12px';
    matchNote.style.marginTop = '6px';
    matchNote.textContent = 'Ketik ulang password di atas.';
    const pcField = pc.closest('.field') || pc.parentElement;
    pcField && pcField.appendChild(matchNote);
  }

  function renderStrength(score) {
    let level = 'Low';
    let color = '#e11d48'; // merah
    if (score === 2) { level = 'Medium'; color = '#f59e0b'; } // amber
    if (score >= 3) { level = 'High';   color = '#16a34a'; } // hijau
    strengthEl.textContent = 'Keamanan password: ' + level;
    strengthEl.style.color = color;
  }

  function assess(str) {
    const c = {
      len: str.length >= 8,
      mix: /[a-z]/.test(str) && /[A-Z]/.test(str),
      num: /\d/.test(str),
      sym: /[^A-Za-z0-9]/.test(str),
    };
    let score = 0; Object.values(c).forEach(v => { if (v) score++; });

    // meter
    const colors = ['#ff7a7a','#ffb74d','#ffd54f','#66bb6a']; // weak -> strong
    if (meterFill) {
      meterFill.style.width = (score/4)*100 + '%';
      meterFill.style.background = colors[Math.max(0, score-1)] || '#ff7a7a';
      meterFill.style.borderRadius = '999px';
    }

    // chips
    Object.entries(c).forEach(([k, ok]) => {
      const el = chips[k]; if (!el) return;
      el.classList.toggle('ok', ok);
      el.classList.toggle('bad', !ok);
      el.style.background = ok ? '#e8fff0' : '#f3f3f3';
      el.style.color = ok ? '#0e6b35' : '#666';
    });

    // highlight input
    pw.classList.toggle('ok', score >= 3);
    pw.classList.toggle('bad', score > 0 && score < 3);

    renderStrength(score);
    return score;
  }

  function checkMatch() {
    const same = pw.value.length > 0 && pw.value === pc.value;
    matchNote.textContent = same ? 'Password cocok.' : (pc.value ? 'Password tidak cocok.' : 'Ketik ulang password di atas.');
    matchNote.classList.toggle('ok',  same);
    matchNote.classList.toggle('bad', !same && !!pc.value);
    pc.classList.toggle('ok', same);
    pc.classList.toggle('bad', !same && !!pc.value);
    return same;
  }

  function update() {
    const score = assess(pw.value);
    const same  = checkMatch();
    if (btn) btn.disabled = !(score >= 3 && same); // ubah ke >=4 kalau mau super ketat
  }

  pw.addEventListener('input', update);
  pc.addEventListener('input', update);
  update();
})();

  
})();
