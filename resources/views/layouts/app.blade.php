@php use Illuminate\Support\Str; @endphp
<!doctype html>
<html lang="id">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    @auth
      <meta name="notif-endpoint" content="{{ \Illuminate\Support\Facades\Route::has('notifications.json') ? route('notifications.json') : url('/notifications/json') }}">
      <meta name="notif-mark-endpoint" content="{{ \Illuminate\Support\Facades\Route::has('notifications.markRead') ? route('notifications.markRead') : url('/notifications/mark-read') }}">
    @endauth

    <meta name="app-auth" content="{{ auth()->check() ? '1' : '0' }}">
    <title>@yield('title', 'Golden Spice')</title>

    {{-- Fonts & Icons --}}    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    <link href="https://fonts.googleapis.com/css2?family=Koulen&family=Questrial&family=Hind:wght@700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded:wght@400;600&display=swap">

    {{-- CSS utama (cache-buster) --}}
    <link rel="stylesheet" href="{{ asset('css/golden.css') }}?v={{ @filemtime(public_path('css/golden.css')) }}">
    
    {{-- NOTIF (popup) + CHAT MODAL (style) --}}
    <style>
      /* bell + badge */
      .gs-notif{position:relative;display:flex;align-items:center;margin-right:10px}
      .notif-btn{position:relative;display:inline-flex;align-items:center;justify-content:center;height:42px;min-width:42px;padding:0 10px;border-radius:999px;border:1px solid rgba(255,255,255,.35);background:rgba(255,255,255,.08);color:#fff;cursor:pointer}
      .notif-btn:hover{background:rgba(255,255,255,.14)}
      .notif-badge{
        position:absolute;top:-6px;right:-6px;min-width:18px;height:18px;padding:0 5px;border-radius:999px;
        background:#fff;color:#d0143b;font-size:11px;font-weight:900;display:flex;align-items:center;justify-content:center;
        border:2px solid rgba(208,20,59,.25);
      }

      /* shared modal base */
      .gs-modal{position:fixed;inset:0;display:none;align-items:center;justify-content:center;z-index:120}
      .gs-modal.open{display:flex}
      .gs-modal .ov{position:absolute;inset:0;background:rgba(0,0,0,.45)}
      .gs-modal .box{position:relative;background:#fff;border-radius:18px;overflow:hidden;box-shadow:0 30px 80px rgba(0,0,0,.28)}
      .gs-modal .bar{display:flex;align-items:center;justify-content:space-between;padding:10px 12px;border-bottom:1px solid #eee}
      .gs-modal .bar strong{font-weight:900}
      .gs-modal .x{border:0;background:#f3f3f3;border-radius:999px;width:36px;height:36px;cursor:pointer;display:flex;align-items:center;justify-content:center}

      
      
      
      /* OPTIONAL (lebih pasti): pakai font Koulen versi local
         1) Download Koulen-Regular.woff2
         2) taruh di: public/fonts/Koulen-Regular.woff2
      */
      @font-face{
        font-family:'KoulenLocal';
        src:url('{{ asset('fonts/Koulen-Regular.woff2') }}') format('woff2');
        font-weight:400;
        font-style:normal;
        font-display:swap;
      }

      /* notif modal like mock (Design 2 - fitted) */
      #notifModal .notif-box,
      #notifModal .notif-box *:not(.material-symbols-rounded){
        font-family:'Questrial',sans-serif !important;
      }
      #notifModal .material-symbols-rounded{
        font-family:'Material Symbols Rounded' !important;
        font-weight:normal;
        font-style:normal;
        line-height:1;
        letter-spacing:normal;
        text-transform:none;
        display:inline-block;
        white-space:nowrap;
        word-wrap:normal;
        direction:ltr;
        -webkit-font-feature-settings:'liga';
        -webkit-font-smoothing:antialiased;
      }

      .notif-box{
        width:min(760px,calc(100vw - 24px));
        max-height:min(640px,calc(100vh - 24px));
        border-radius:20px;
        overflow:hidden;
      }

      .notif-bar{
        padding:16px 18px;
border-bottom:1px solid #eee;
      }
            .notif-bar strong{
        font-family:'Questrial',sans-serif !important;
        font-weight:700 !important;          /* bold */
        font-synthesis:weight;
        font-size:27px !important;           /* -2 dari 29px */
        line-height:1 !important;
        letter-spacing:0.03em !important;
        text-shadow:0 0 0 currentColor;      /* biar terlihat lebih tebal */
      }

      

      

      
      /* paksa font judul NOTIFICATIONS (tepat di teksnya) */
            #notifModalTitle{
        font-family:'Questrial',sans-serif !important;
        font-weight:700 !important;          /* bold */
        font-synthesis:weight;
        font-style:normal !important;
        font-size:27px !important;           /* -2 */
        line-height:1 !important;
        letter-spacing:0.03em !important;
        text-shadow:0 0 0 currentColor;
      }
#notifModal .bar strong{
        font-weight:400 !important;
      }
/* kecilkan tombol close di header notif */
      .notif-bar .x{
        width:24px;
        height:24px;
        background:#f3f3f3;
      }
      .notif-bar .x .material-symbols-rounded{font-size:14px}
.notif-body{
        padding:14px 16px 18px;
overflow:auto;
        max-height:calc(min(640px,calc(100vh - 24px)) - 62px);
      }
      .notif-empty{padding:12px 6px;color:#666;font-size:13px}

      .notif-row{
        display:flex;
        gap:14px;
        padding:16px 6px;
        align-items:flex-start;
        cursor:pointer;
      }
      .notif-row + .notif-row{border-top:1px solid #ededed}

      .notif-row{
        position:relative;
      }
      .notif-row + .notif-row::before{
        content:'';
        position:absolute;
        top:-1px;
        left:74px;   /* icon width(64) + gap(14) + padding approx */
        right:6px;
        height:1px;
        background:#ededed;
      }
      /* matikan border-top agar tidak double */
      .notif-row + .notif-row{border-top:0}


      /* icon kiri */
      .notif-ico{
        width:56px;
        height:56px;
        border-radius:14px;
        display:flex;
        align-items:center;
        justify-content:center;
        background:transparent;
        color:#d0143b;
        flex:none;
      }
      .notif-ico .material-symbols-rounded{font-size:34px}

      /* cancelled: bubble merah + icon putih */
      .notif-ico.danger{
        width:56px;height:56px;
        background:#d0143b;
        color:#fff;
        border-radius:999px;
      }
      .notif-ico.danger .material-symbols-rounded{font-size:28px}

      /* message */
      .notif-ico.msg{
        background:#ffecef;
        color:#d0143b;
        border-radius:16px;
      }
      .notif-ico.msg .material-symbols-rounded{font-size:34px}
.notif-content{flex:1;min-width:0}
      .notif-top{display:flex;align-items:baseline;justify-content:space-between;gap:10px}

      .notif-kind{
        font-size:16px;
        font-weight:800;
        color:#111;
        line-height:1.2;
      }
      .notif-time{font-size:12px;color:#999;white-space:nowrap}

      .notif-title{
        margin-top:6px;
        font-size:16px;
        font-weight:800;
        color:#111;
        line-height:1.25;
      }
      .notif-sub{margin-top:6px;color:#666;font-size:13px;line-height:1.35}
      .notif-foot{margin-top:6px;color:#999;font-size:12px}





      /* chat modal sizing */
      .chat-box{width:min(860px,calc(100vw - 24px));height:min(680px,calc(100vh - 24px));border-radius:18px;overflow:hidden}
      .chat-box iframe{width:100%;height:calc(100% - 52px);border:0}
    
      /* footer details */
      .gs-footer .footer-top{ padding:0 0 6px; }
      .gs-footer .footer-grid{
        display:grid;
        grid-template-columns: 1.4fr 1fr 1fr 1fr;
        gap:22px;
        padding-bottom:10px;
      }
      .gs-footer .footer-brand{
        font-family:"Koulen",cursive;
        font-size:26px;
        letter-spacing:.5px;
        margin:0 0 10px;
        color:#fff;
      }
      .gs-footer .footer-desc{
        margin:0;
        color:#cfcfcf;
        font-size:14px;
        line-height:1.6;
      }
      .gs-footer .footer-head{
        font-weight:800;
        font-size:14px;
        letter-spacing:.2px;
        margin:4px 0 10px;
        color:#fff;
      }
      .gs-footer .footer-text{
        margin:0;
        color:#cfcfcf;
        font-size:14px;
        line-height:1.6;
      }
      .gs-footer .footer-links{
        display:flex;
        flex-direction:column;
        gap:8px;
      }
      .gs-footer .footer-link{
        color:#cfcfcf;
        font-size:14px;
        opacity:.92;
      }
      .gs-footer .footer-link:hover{ color:#fff; opacity:1; text-decoration:underline; text-underline-offset:3px; }
      .gs-footer .footer-copy{
        margin:0;
        color:#bdbdbd;
        font-size:13px;
      }
      @media (max-width: 900px){
        .gs-footer .footer-grid{ grid-template-columns: 1fr 1fr; }
      }
      @media (max-width: 520px){
        .gs-footer .footer-grid{ grid-template-columns: 1fr; }
      }

    </style>

    @stack('styles')
  </head>
  <body>

    {{-- =================== HEADER / NAV =================== --}}
    <header class="gs-header">
      <div class="gs-container hdr">
        <a class="brand" href="{{ auth()->check() ? route('home') : route('login') }}">GOLDEN SPICE</a>

        @auth
          <nav id="mainNav" class="nav">
            <a href="{{ route('home') }}"
               class="{{ request()->routeIs('home') ? 'active' : '' }}">Beranda</a>

            <a href="{{ route('menu') }}"
               class="{{ request()->routeIs('menu') ? 'active' : '' }}">Menu</a>

            <a href="{{ route('contact') }}"
               class="{{ request()->routeIs('contact') ? 'active' : '' }}">Contact</a>

            {{-- PENTING: selalu ke orders.index, bukan orders.show --}}
            <a href="{{ route('orders.index') }}"
               class="{{ request()->routeIs('orders.*') ? 'active' : '' }}">Order</a>

            <span class="nav-pill" id="navPill" aria-hidden="true"></span>
          </nav>
        @endauth

        <div class="nav-right">
          @guest
            {{-- NAV untuk Login/Daftar agar nav-pill bisa mengikuti --}}
            <nav id="mainNav" class="nav">
              <a href="{{ route('login') }}"
                 class="{{ request()->routeIs('login') ? 'active' : '' }}">Login</a>
              <a href="{{ route('register') }}"
                 class="{{ request()->routeIs('register') ? 'active' : '' }}">Daftar</a>
              <span class="nav-pill" aria-hidden="true"></span>
            </nav>
          @endguest

          @auth

          {{-- NOTIF BELL (klik => popup) --}}
          <div class="gs-notif">
            <button type="button" class="notif-btn" id="notifBtn"
                    aria-expanded="false" aria-controls="notifModal" title="Notifikasi">
              <span class="material-symbols-rounded">notifications</span>
              <span class="notif-badge" id="notifBadge" hidden>0</span>
            </button>
          </div>

            <div class="user">
              <button type="button"
                      class="user-btn"
                      id="userBtn"
                      aria-expanded="false"
                      aria-controls="userMenu">
                <span class="material-symbols-rounded">account_circle</span>
                <span>{{ Str::of(auth()->user()->nama ?? auth()->user()->email)->limit(18) }}</span>
                <span class="material-symbols-rounded chevron">expand_more</span>
              </button>

              <div class="user-menu" id="userMenu" aria-hidden="true">
                <div class="user-meta">
                  <div class="avatar">
                    {{ strtoupper(mb_substr(auth()->user()->nama ?? 'U', 0, 1)) }}
                  </div>
                  <div>
                    <div><strong>{{ auth()->user()->nama ?? 'User' }}</strong></div>
                    <div class="small">{{ auth()->user()->email }}</div>
                  </div>
                </div>
                <form id="logoutForm" method="POST" action="{{ route('logout') }}">
                  @csrf
                  <button class="logout" type="submit">
                    <span class="material-symbols-rounded">logout</span> Keluar
                  </button>
                </form>
              </div>
            </div>
          @endauth
        </div>
      </div>
    </header>

    {{-- =================== KONTEN =================== --}}
    <main>
      @yield('content')
    </main>

    {{-- =================== FOOTER =================== --}}
        <footer class="gs-footer">
      <div class="gs-container footer-top">
        <div class="footer-grid">
          <div class="footer-col">
            <div class="footer-brand">GOLDEN SPICE</div>
            <p class="footer-desc">
              Nikmati menu favoritmu, pantau status pesanan, dan dapatkan notifikasi update pesanan langsung dari website.
            </p>
          </div>

          <div class="footer-col">
            <div class="footer-head">Lokasi</div>
            <p class="footer-text">
              <strong style="color:#fff">SENTRA NIAGA</strong><br>
              Ruko Jl. Kano Raya No.4
            </p>
          </div>

          <div class="footer-col">
            <div class="footer-head">Navigasi</div>
            <div class="footer-links">
              <a class="footer-link" href="{{ route('home') }}">Beranda</a>
              <a class="footer-link" href="{{ route('menu') }}">Menu</a>
              <a class="footer-link" href="{{ route('orders.index') }}">Order</a>
              <a class="footer-link" href="{{ route('contact') }}">Contact</a>
            </div>
          </div>

          <div class="footer-col">
            <div class="footer-head">Bantuan</div>
            <div class="footer-links">
              <a class="footer-link" href="{{ route('orders.index') }}">Lacak pesanan</a>
              <a class="footer-link" href="{{ route('contact') }}">Hubungi kami</a>
            </div>
          </div>
        </div>
      </div>

      <div class="gs-container footer-bottom">
        <p class="footer-copy">© {{ date('Y') }} Golden Spice. All Rights Reserved.</p>
        <div class="socials" aria-label="Social">
          <span class="dot"></span><span class="dot"></span><span class="dot"></span>
        </div>
      </div>
    </footer>

    {{-- ========== CART: FAB + Drawer (KANAN, hanya di halaman MENU) ========== --}}
    @auth
      @if (request()->routeIs('menu'))
        {{-- FAB --}}
        <button class="fab-cart" type="button" aria-label="Buka keranjang" data-cart-open>
          <svg width="26" height="26" viewBox="0 0 24 24" fill="none" aria-hidden="true">
            <path d="M7 6h14l-1.5 9.5a2 2 0 0 1-2 1.7H9.6a2 2 0 0 1-2-1.5L5.2 3.9H2"
                  stroke="white" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
            <circle cx="10" cy="21" r="1.6" fill="white"/>
            <circle cx="18" cy="21" r="1.6" fill="white"/>
          </svg>
          <span class="fab-badge" id="cartBadge">0</span>
        </button>

        {{-- Overlay & Drawer --}}
        <div class="cart-overlay" aria-hidden="true"></div>

        <aside class="cart-drawer" aria-hidden="true">
          <div class="cart-head">
            <div>
              <div class="cart-brand">GOLDEN SPICE</div>
              <div class="cart-sub">Your Order (<span id="cartCountHdr">0</span>)</div>
            </div>
            <button class="cart-close" data-cart-close aria-label="Tutup">&times;</button>
          </div>

          <div class="cart-body">
            <ul class="cart-list" id="cartList"></ul>
            <div class="cart-empty" id="cartEmpty">Keranjang masih kosong.</div>
          </div>

          <div class="cart-foot">
            <div class="cart-total">
              <span>Total</span>
              <strong id="cartTotal">Rp 0</strong>
            </div>

            <div class="cart-actions" style="display:flex; gap:10px;">
              <button id="cartClearBtn" type="button" class="btn-clear">Hapus semua</button>
              <a href="{{ route('payment.start') }}" class="cart-cta">Review Order</a>
            </div>
          </div>
        </aside>
      @endif
    @endauth

    
    {{-- NOTIF MODAL (popup) --}}
    @auth
      <div class="gs-modal" id="notifModal" aria-hidden="true">
        <div class="ov" data-notif-close></div>
        <div class="box notif-box" role="dialog" aria-modal="true" aria-label="Notifications">
          <div class="bar notif-bar">
            <strong id="notifModalTitle" style="font-family:'Questrial',sans-serif;font-weight:700;font-synthesis:weight;font-size:27px;line-height:1;letter-spacing:0.03em;text-shadow:0 0 0 currentColor;">NOTIFICATIONS (0)</strong>
            <button class="x" type="button" data-notif-close aria-label="Tutup">
              <span class="material-symbols-rounded">close</span>
            </button>
          </div>
          <div class="notif-body">
            <div class="notif-empty" id="notifEmpty">Belum ada notifikasi.</div>
            <div id="notifList"></div>
          </div>
        </div>
      </div>
    @endauth

    {{-- CHAT MODAL (popup) --}}
    @auth
      <div class="gs-modal" id="chatModal" aria-hidden="true">
        <div class="ov" data-chat-close></div>
        <div class="box chat-box" role="dialog" aria-modal="true" aria-label="Chat Admin">
          <div class="bar">
            <strong id="chatModalTitle">Chat Admin</strong>
            <button class="x" type="button" data-chat-close aria-label="Tutup">
              <span class="material-symbols-rounded">close</span>
            </button>
          </div>
          <iframe id="chatModalFrame" src="about:blank" title="Chat"></iframe>
        </div>
      </div>
    @endauth


    {{-- =================== JS =================== --}}

    {{-- NOTIF + CHAT MODAL (script) --}}
    <script>
    (function(){
      const isAuth = document.querySelector('meta[name="app-auth"]')?.content === '1';
      if(!isAuth) return;

      const btn   = document.getElementById('notifBtn');
      const badge = document.getElementById('notifBadge');

      const modal = document.getElementById('notifModal');
      const title = document.getElementById('notifModalTitle');
      const list  = document.getElementById('notifList');
      const empty = document.getElementById('notifEmpty');

      const csrf  = document.querySelector('meta[name="csrf-token"]')?.content;
      const endpoints = {
        list: document.querySelector('meta[name="notif-endpoint"]')?.content || '/notifications/json',
        mark: document.querySelector('meta[name="notif-mark-endpoint"]')?.content || '/notifications/mark-read'
      };

      // chat modal
      const chatModal = document.getElementById('chatModal');
      const chatFrame = document.getElementById('chatModalFrame');
      const chatTitle = document.getElementById('chatModalTitle');

      function openNotifModal(){
        modal?.classList.add('open');
        modal?.setAttribute('aria-hidden','false');
        btn?.setAttribute('aria-expanded','true');
      }
      function closeNotifModal(){
        modal?.classList.remove('open');
        modal?.setAttribute('aria-hidden','true');
        btn?.setAttribute('aria-expanded','false');
      }
      document.querySelectorAll('[data-notif-close]').forEach(el=>el.addEventListener('click', closeNotifModal));

      function openChat(orderId, href){
        if(!chatModal) return;
        chatTitle.textContent = orderId ? `Chat Admin • Order #${orderId}` : 'Chat Admin';
        chatFrame.src = href || (orderId ? `/orders/${orderId}/chat` : 'about:blank');
        chatModal.classList.add('open');
        chatModal.setAttribute('aria-hidden','false');
        closeNotifModal();
      }
      function closeChat(){
        if(!chatModal) return;
        chatModal.classList.remove('open');
        chatModal.setAttribute('aria-hidden','true');
        chatFrame.src = 'about:blank';
      }
      document.querySelectorAll('[data-chat-close]').forEach(el=>el.addEventListener('click', closeChat));

      function setBadge(unread){
        const n = Number(unread || 0);
        if(!badge) return;
        badge.hidden = n <= 0;
        badge.textContent = String(n);
      }

      function iconFor(it){
        const s = (it.status||it.status_label||'').toLowerCase();

        // Cancelled: bubble merah + icon putih (Design 2)
        if(['dibatalkan','cancelled','canceled','batal'].includes(s))
          return {icon:'close', cls:'danger', kind:'Canceled'};

        // Active order: icon merah (tanpa bubble)
        if(['diantar','delivering','dikirim','diproses','proses','menunggu','waiting','pending'].includes(s))
          return {icon:'local_shipping', cls:'', kind:'Active Order'};

        // Completed
        if(['selesai','completed','done'].includes(s))
          return {icon:'task_alt', cls:'', kind:'Completed'};

        // Chat/message
        if(it.type === 'chat')
          return {icon:'chat_bubble', cls:'msg', kind:'Message'};

        // Note/admin
        if(it.type === 'note')
          return {icon:'info', cls:'msg', kind:'Note'};

        return {icon:'receipt_long', cls:'', kind:(it.kind || 'Update')};
      }

      function render(items){
        if(!list || !empty || !title) return;
        list.innerHTML = '';

        if(!items || items.length === 0){
          empty.style.display = 'block';
          title.textContent = 'NOTIFICATIONS (0)';
          setBadge(0);
          return;
        }
        empty.style.display = 'none';

        const unread = items.filter(x => !x.read_at).length;
        title.textContent = `NOTIFICATIONS (${unread})`;
        setBadge(unread);

        items.forEach(it=>{
          const info = iconFor(it);

          const row = document.createElement('div');
          row.className = 'notif-row';

          const ico = document.createElement('div');
          ico.className = `notif-ico ${info.cls || ''}`;
          ico.innerHTML = `<span class="material-symbols-rounded">${info.icon}</span>`;

          const content = document.createElement('div');
          content.className = 'notif-content';

          const timeText = it.time || it.meta_time || it.meta || '';
          const mainTitle = it.title || (it.type === 'chat' ? 'Pesan dari Admin' : 'Update Pesanan');
          const subLine = it.items_summary || it.sub || it.body || '';
          const footLine = it.eta || it.footer || '';

          content.innerHTML = `
            <div class="notif-top">
              <div class="notif-kind">${it.kind || info.kind}</div>
              <div class="notif-time">${timeText}</div>
            </div>
            <div class="notif-title">${mainTitle}</div>
            ${subLine ? `<div class="notif-sub">${subLine}</div>` : ''}
            ${footLine ? `<div class="notif-foot">${footLine}</div>` : ''}
          `;

          row.appendChild(ico);
          row.appendChild(content);

          row.addEventListener('click', async ()=>{
            try{
              if(it.id){
                await fetch(endpoints.mark, {
                  method:'POST',
                  headers:{'Content-Type':'application/json','X-CSRF-TOKEN':csrf,'Accept':'application/json'},
                  body: JSON.stringify({ id: it.id })
                });
              }
            }catch(e){}

            if(it.type === 'chat'){
              openChat(it.order_id, it.href);
              return;
            }

            const href = it.href || (it.order_id ? `/orders/${it.order_id}` : null);
            if(href) window.location.href = href;
          });

          list.appendChild(row);
        });
      }

      async function fetchNotifs(){
        if(Array.isArray(window.GS_NOTIFICATIONS)) return window.GS_NOTIFICATIONS;

        const res = await fetch(endpoints.list, {headers:{'Accept':'application/json'}});
        const json = await res.json();
        return json.data || json.notifications || json || [];
      }

      async function load({open=false} = {}){
        try{
          const items = await fetchNotifs();
          render(items);
          if(open) openNotifModal();
        }catch(e){
          render([]);
          if(open) openNotifModal();
        }
      }

      btn?.addEventListener('click', (e)=>{
        e.preventDefault();
        load({open:true});
      });

      // show badge at first load
      load({open:false});

      document.addEventListener('keydown', (e)=>{
        if(e.key === 'Escape'){ closeNotifModal(); closeChat(); }
      });
    })();
    </script>

    <script src="{{ asset('js/golden.js') }}?v={{ @filemtime(public_path('js/golden.js')) }}" defer></script>
    @stack('scripts')
  </body>
</html>
