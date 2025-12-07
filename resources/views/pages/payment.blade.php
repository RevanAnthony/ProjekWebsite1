{{-- resources/views/pages/payment.blade.php --}}
@extends('layouts.app')
@section('title', 'Payment — Golden Spice')

@section('content')
@php
  $subtotal = $cart->items->sum('subtotal');
  $shipping = 5000; // contoh biaya
  $grand    = $subtotal + $shipping;
@endphp

{{-- Leaflet (map interaktif) --}}
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" crossorigin="">
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" crossorigin=""></script>

<style>
/* ===== PAYMENT PAGE ===== */
.gs-pay{max-width:980px;margin:0 auto;padding:20px 16px 110px;background:#fffaf5}
.gs-pay *{box-sizing:border-box}
.gs-pay__back{display:inline-block;margin-bottom:12px;font-weight:800;text-decoration:none;color:#111}
.gs-card{background:#fff;border-radius:16px;box-shadow:0 10px 28px rgba(0,0,0,.06);padding:16px}
.gs-head{display:flex;justify-content:space-between;align-items:center;margin-bottom:12px}
.gs-head h2{margin:0;font-size:18px;font-weight:900;letter-spacing:.3px}
.gs-btn-edit{border:1px solid #e5e5e5;background:#fff;border-radius:12px;padding:8px 12px;font-weight:700;cursor:pointer}
.gs-map{height:240px;border-radius:12px;overflow:hidden}
#gsMap{height:100%;width:100%}
.gs-map-ctrl{display:flex;gap:8px;margin:10px 0 0 0}
.gs-input,.gs-note{height:44px;border:1px solid #e6e6e6;border-radius:12px;padding:0 12px;background:#fff;width:100%}
.gs-grid{display:grid;grid-template-columns:1fr 1fr;gap:16px}
.gs-field{display:flex;flex-direction:column;gap:6px}
.gs-field label{font-size:12px;color:#666}
.gs-section{margin-top:16px}

/* Order list */
.gs-order-list{display:flex;flex-direction:column}
.gs-row{display:grid;grid-template-columns:72px 1fr auto auto;gap:12px;align-items:center;padding:12px 0;border-bottom:1px solid #f2f2f2}
.gs-thumb img{width:72px;height:72px;border-radius:12px;object-fit:cover}
.gs-title{font-weight:900;display:flex;gap:8px;align-items:center}
.gs-heat{opacity:.8}
.gs-addnote{background:transparent;border:0;color:#e11d48;font-weight:700;padding:0;margin-top:4px;cursor:pointer}
.gs-qty{display:flex;align-items:center;gap:8px}
.gs-qtybtn{width:36px;height:36px;border-radius:10px;border:1px solid #e6e6e6;background:#fff;font-weight:900;line-height:1;cursor:pointer}
.gs-qtyinput{width:52px;text-align:center;border:0;background:transparent;font-weight:800}
.gs-price{font-weight:900;white-space:nowrap}

/* Add note input (toggle) */
.gs-note{display:none;margin-top:6px}
.gs-note.show{display:block}

/* Summary */
.gs-sumrow{display:flex;justify-content:space-between;align-items:center;padding:8px 0}
.gs-sumrow--total{border-top:1px dashed #eee;margin-top:6px;padding-top:12px;font-size:18px;font-weight:900}
.gs-sumrow--discount{color:#0f6b00}

/* Promo CTA */
.gs-cta-coupon{
  position:relative;width:100%;display:flex;align-items:center;gap:16px;
  border:0;border-radius:22px;padding:18px 20px;
  background:linear-gradient(90deg,#d01515 0%, #c70f0f 45%, #de3131 70%, #f6d0d0 100%);
  box-shadow:0 12px 24px rgba(208,21,21,.25);cursor:pointer;appearance:none;-webkit-appearance:none;
}
.gs-cta-coupon:focus{outline:3px solid rgba(255,255,255,.35); outline-offset:2px}
.gs-cta-left{display:flex;align-items:center;gap:16px;padding-left:6px}
.gs-cta-badge{width:40px;height:40px;border-radius:999px;background:#fff;color:#c70f0f;font-weight:900;display:grid;place-items:center;box-shadow:0 4px 10px rgba(0,0,0,.12)}
.gs-cta-sep{height:44px;border-left:3px dashed rgba(255,255,255,.9)}
.gs-cta-text{color:#fff;font-weight:900;font-size:15px;letter-spacing:.2px}
.gs-cta-arrow{margin-left:auto;width:40px;height:40px;border-radius:999px;background:#fff;color:#c70f0f;display:grid;place-items:center;font-size:20px;box-shadow:0 4px 10px rgba(0,0,0,.12)}

/* Bottom sheet kupon */
.gs-sheet{position:fixed;left:0;right:0;bottom:-100%;background:#fff;border-radius:20px 20px 0 0;box-shadow:0 -10px 30px rgba(0,0,0,.15);transition:bottom .25s ease;z-index:3500}
.gs-sheet__inner{max-width:980px;margin:0 auto;padding:16px}
.gs-sheet.open{bottom:0}
.gs-coupon{border:1px solid #eee;border-radius:14px;padding:12px;display:flex;justify-content:space-between;align-items:center;margin-bottom:10px}
.gs-coupon .meta{font-weight:800}
.gs-coupon .desc{font-size:12px;color:#666}
.gs-coupon .apply{border:0;background:#111;color:#fff;font-weight:800;border-radius:10px;padding:8px 12px;cursor:pointer}
.gs-coupon .remove{border:0;background:#e11d48;color:#fff;font-weight:800;border-radius:10px;padding:8px 12px;cursor:pointer}

/* Footer checkout - tombol lebih besar */
.gs-foot{position:fixed;left:0;right:0;bottom:0;background:#fff;border-top:1px solid #eee;padding:12px;z-index:3200}
.gs-foot__bar{max-width:980px;margin:0 auto;display:flex;gap:12px;align-items:center}
.gs-foot__total{font-weight:900;margin-right:auto}
.gs-btn-primary{
  position:relative;
  display:inline-flex;
  align-items:center;
  justify-content:center;
  gap:.55rem;
  font-family:"Hind","Questrial",sans-serif;
  text-transform:uppercase;
  font-weight:800;
  letter-spacing:.3px;
  height:56px;
  padding:0 24px;
  border-radius:14px;
  border:0;
  cursor:pointer;
  user-select:none;
  background:linear-gradient(135deg,#ff3b3b 0%, #ff7a1a 100%);
  color:#fff;
  box-shadow:0 8px 18px rgba(255,61,61,.35), 0 2px 6px rgba(0,0,0,.08);
  transition:.18s;
  overflow:hidden;
}
.gs-btn-primary:active{
  transform:translateY(0) scale(.98);
}


/* Search box for map */
.gs-search{position:relative;flex:1}
.gs-search input{height:40px;border:1px solid #e6e6e6;border-radius:12px;padding:0 12px;width:100%}
.gs-suggest{position:absolute;left:0;right:0;top:44px;background:#fff;border:1px solid #eee;border-radius:12px;max-height:220px;overflow:auto;box-shadow:0 10px 24px rgba(0,0,0,.08);display:none;z-index:70}
.gs-suggest button{display:block;width:100%;text-align:left;padding:10px 12px;border:0;background:#fff;cursor:pointer}
.gs-suggest button:hover{background:#fafafa}

/* Jaga header di atas kontrol map (Leaflet z-index bawaannya tinggi) */
header,.navbar,.topbar,.site-header{z-index:3000 !important}
.leaflet-top,.leaflet-bottom{z-index:900 !important}

/* ==== PAYMENT METHOD CARD ==== */
.payment-method-card .pm-option{
  border-radius:14px;
  border:1px solid #eee;
  padding:10px 12px;
  display:flex;
  align-items:flex-start;
  gap:10px;
  margin-bottom:8px;
}
.payment-method-card .pm-option:hover{
  border-color:#ffc9ba;
  background:#fff7f4;
}
.payment-method-card .pm-option input[type="radio"]{
  margin-top:3px;
}
.payment-method-card .pm-title{
  font-size:15px;
  font-weight:800;
}
.payment-method-card .pm-desc{
  font-size:13px;
  color:#777;
}

@media (max-width:768px){
  .gs-grid{grid-template-columns:1fr}
  .gs-row{grid-template-columns:56px 1fr auto;gap:10px}
  .gs-thumb img{width:56px;height:56px}
  .gs-price{grid-column:3}
  .gs-foot__bar{padding:0 4px}
  .gs-btn-primary{height:56px;font-size:17px}
  .gs-cta-coupon{padding:16px}
  .gs-cta-badge,.gs-cta-arrow{width:36px;height:36px}
  .gs-cta-sep{height:40px}
  .gs-cta-text{font-size:14px}
}
</style>

<div class="gs-pay">
  <a href="{{ route('menu') }}" class="gs-pay__back">← Back to Menu</a>

  {{-- ===== DELIVERY ===== --}}
  <section class="gs-card">
    <div class="gs-head">
      <h2>Delivery</h2>
      <button type="button" class="gs-btn-edit" id="btnUseGPS">Use My Location</button>
    </div>

    <div class="gs-map" aria-label="Interactive map">
      <div id="gsMap"></div>
    </div>

    <div class="gs-map-ctrl">
      <div class="gs-search">
        <input id="gsSearchBox" type="text" placeholder="Cari alamat (contoh: Pondok Indah Mall)">
        <div id="gsSuggest" class="gs-suggest"></div>
      </div>
      <button type="button" class="gs-btn-edit" id="btnEditLoc">Edit Location</button>
    </div>

    <form id="checkoutForm" method="POST"
      action="{{ \Illuminate\Support\Facades\Route::has('orders.checkout') ? route('orders.checkout') : url('/checkout') }}">
      @csrf
      <input type="hidden" name="lat" id="lat">
      <input type="hidden" name="lng" id="lng">

      <div class="gs-grid" style="margin-top:12px">
        <div class="gs-field">
          <label>Location Detail (optional)</label>
          <input class="gs-input" type="text" name="alamat" id="alamat" placeholder="Jl. contoh/nama/rt/rw, blok No. 25" value="{{ old('alamat') }}">
        </div>
        <div class="gs-field">
          <label>Your Phone Number</label>
          <input class="gs-input" type="tel" name="telepon" placeholder="08xx-xxxx-xxxx" value="{{ old('telepon') }}" inputmode="numeric">
        </div>
      </div>

      <div class="gs-field" style="margin-top:10px">
        <label>Location Detail (optional)</label>
        <input class="gs-input" type="text" name="catatan_pengiriman" placeholder="e.g. please leave food at the door/gate" value="{{ old('catatan_pengiriman') }}">
      </div>

      {{-- ===== ORDER LIST ===== --}}
      <section class="gs-card gs-section">
        <div class="gs-head">
          <h2>Orderanmu</h2>
          <a href="{{ route('menu') }}" class="gs-btn-edit" style="text-decoration:none;color:#111">Tambah menu</a>
        </div>

        <div class="gs-order-list">
          @foreach ($cart->items as $i)
            @php
              $p    = $i->produk;
              $name = $p->nama_produk ?? 'Produk';
              $img  = $p->url_gambar ? asset($p->url_gambar) : null;
              $qty  = (int) $i->jumlah;
              $sub  = (int) $i->subtotal;
              $heat = (int) ($p->heat ?? 0);
            @endphp
            <div class="gs-row" data-id="{{ $i->id }}">
              <div class="gs-thumb">@if($img)<img src="{{ $img }}" alt="{{ $name }}">@endif</div>
              <div>
                <div class="gs-title">
                  <span>{{ $name }}</span>
                  @if($heat>0)<span class="gs-heat">@for($h=0;$h<$heat;$h++) 🔥 @endfor</span>@endif
                </div>
                <button class="gs-addnote" type="button">+ add note</button>
                <input class="gs-note" name="items[{{ $i->id }}][note]" placeholder="e.g. no chili sauce">
              </div>
              <div class="gs-qty">
                <button type="button" class="gs-qtybtn js-dec">−</button>
                <input class="gs-qtyinput" type="number" min="1" value="{{ $qty }}" name="items[{{ $i->id }}][qty]" readonly>
                <button type="button" class="gs-qtybtn js-inc">+</button>
              </div>
              <div class="gs-price">Rp {{ number_format($sub,0,',','.') }}</div>
            </div>
          @endforeach
        </div>
      </section>

      {{-- ===== SUMMARY ===== --}}
      <section class="gs-card gs-section">
        <h2 style="margin:0 0 10px 0;font-size:18px;font-weight:900">Ringkasan Pembayaran</h2>
        <div class="gs-sumrow"><span>Harga</span><span id="sum-subtotal">Rp {{ number_format($subtotal,0,',','.') }}</span></div>
        <div class="gs-sumrow"><span>Biaya Penanganan dan Pengiriman</span><span id="sum-shipping">Rp {{ number_format($shipping,0,',','.') }}</span></div>
        <div class="gs-sumrow gs-sumrow--discount" id="sum-discount-row" style="display:none">
          <span>Diskon (kupon)</span><span id="sum-discount">- Rp 0</span>
        </div>
        <div class="gs-sumrow gs-sumrow--total"><span>Total Pembayaran</span><span id="sum-grand">Rp {{ number_format($grand,0,',','.') }}</span></div>
      </section>

      {{-- ===== PAYMENT METHOD ===== --}}
      <section class="gs-card gs-section payment-method-card">
        <h2 style="margin:0 0 10px 0;font-size:18px;font-weight:900">Metode Pembayaran</h2>

        <label class="pm-option">
          <input type="radio" name="metode_pembayaran" value="qris" checked>
          <span>
            <div class="pm-title">QRIS</div>
            <div class="pm-desc">Bayar dengan scan QR saat pesanan dikonfirmasi.</div>
          </span>
        </label>

        <label class="pm-option">
          <input type="radio" name="metode_pembayaran" value="cod">
          <span>
            <div class="pm-title">COD (Cash on Delivery)</div>
            <div class="pm-desc">Bayar tunai ke kurir saat pesanan tiba.</div>
          </span>
        </label>
      </section>

      {{-- ===== PROMO CTA ===== --}}
      <section class="gs-section">
        <button type="button" id="btnOpenPromo" class="gs-cta-coupon" aria-label="Cek Promo Menarik Disini">
          <span class="gs-cta-left">
            <span class="gs-cta-badge">%</span>
            <span class="gs-cta-sep" aria-hidden="true"></span>
          </span>
          <span class="gs-cta-text">Cek Promo Menarik Disini!</span>
          <span class="gs-cta-arrow" aria-hidden="true">➜</span>
        </button>
      </section>

      {{-- ===== BOTTOM SHEET KUPON ===== --}}
      <div id="promoSheet" class="gs-sheet" aria-hidden="true">
        <div class="gs-sheet__inner">
          <div class="gs-head" style="margin-bottom:6px">
            <h2>Pilih Kupon</h2>
            <button type="button" class="gs-btn-edit" id="btnClosePromo">Tutup</button>
          </div>

          <div class="gs-coupon" data-code="HEMAT10" data-type="percent" data-value="10" data-min="50000">
            <div>
              <div class="meta">HEMAT10 — Diskon 10%</div>
              <div class="desc">Min belanja Rp 50.000</div>
            </div>
            <button type="button" class="apply">Pakai</button>
          </div>

          <div class="gs-coupon" data-code="ONGKIR5K" data-type="shipping" data-value="5000" data-min="30000">
            <div>
              <div class="meta">ONGKIR5K — Potong Ongkir Rp 5.000</div>
              <div class="desc">Min belanja Rp 30.000</div>
            </div>
            <button type="button" class="apply">Pakai</button>
          </div>

          <div class="gs-coupon" data-code="SPICY20" data-type="percent" data-value="20" data-min="75000">
            <div>
              <div class="meta">SPICY20 — Diskon 20%</div>
              <div class="desc">Min belanja Rp 75.000</div>
            </div>
            <button type="button" class="apply">Pakai</button>
          </div>

          <div id="activeCoupon" style="display:none;margin-top:10px">
            <div class="gs-coupon">
              <div>
                <div class="meta">Kupon terpakai: <span id="activeCode"></span></div>
                <div class="desc" id="activeDesc"></div>
              </div>
              <button type="button" class="remove" id="btnRemoveCoupon">Hapus</button>
            </div>
          </div>
        </div>
      </div>

      {{-- ===== FOOTER ===== --}}
      <div class="gs-foot">
        <div class="gs-foot__bar">
          <div class="gs-foot__total">Total: <span id="footTotal">Rp {{ number_format($grand,0,',','.') }}</span></div>
          <button class="gs-btn-primary" type="submit">Check out</button>
        </div>
      </div>
    </form>
  </section>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
  /* ---------- Map interaktif (Leaflet + Nominatim) ---------- */
  try {
    const map = L.map('gsMap', { zoomControl: true }).setView([-6.200, 106.816], 12); // Jakarta default
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { attribution:'&copy; OpenStreetMap' }).addTo(map);
    const marker = L.marker(map.getCenter(), { draggable: true }).addTo(map);

    const latEl = document.getElementById('lat');
    const lngEl = document.getElementById('lng');
    const alamatEl = document.getElementById('alamat');

    function setHidden(lat, lng){ if(latEl && lngEl){ latEl.value = lat.toFixed(6); lngEl.value = lng.toFixed(6);} }
    function updateAddressFrom(lat,lng){
      fetch(`https://nominatim.openstreetmap.org/reverse?format=jsonv2&lat=${lat}&lon=${lng}`)
        .then(r=>r.json()).then(data=>{ if(data && data.display_name && alamatEl){ alamatEl.value = data.display_name; } })
        .catch(()=>{});
    }
    function centerTo(lat,lng,zoom=16){ map.setView([lat,lng], zoom); marker.setLatLng([lat,lng]); setHidden(lat,lng); updateAddressFrom(lat,lng); }

    marker.on('dragend', e => { const {lat,lng} = e.target.getLatLng(); setHidden(lat,lng); updateAddressFrom(lat,lng); });

    document.getElementById('btnUseGPS')?.addEventListener('click', ()=>{
      if(navigator.geolocation){
        navigator.geolocation.getCurrentPosition(pos=>{ centerTo(pos.coords.latitude, pos.coords.longitude); },
          ()=>alert('Tidak bisa mengambil lokasi. Izinkan akses lokasi ya.'));
      }
    });
    document.getElementById('btnEditLoc')?.addEventListener('click', ()=> alamatEl?.focus());

    // Pencarian alamat (forward geocode + suggestion)
    const box = document.getElementById('gsSearchBox');
    const sug = document.getElementById('gsSuggest');
    let t=null;
    if (box && sug){
      box.addEventListener('input', ()=>{
        const q = (box.value||'').trim();
        clearTimeout(t);
        if(q.length<3){ sug.style.display='none'; sug.innerHTML=''; return;}
        t=setTimeout(()=>{
          fetch(`https://nominatim.openstreetmap.org/search?format=jsonv2&q=${encodeURIComponent(q)}&limit=5&addressdetails=1`)
            .then(r=>r.json()).then(list=>{
              sug.innerHTML = '';
              list.forEach(it=>{
                const b = document.createElement('button');
                b.textContent = it.display_name;
                b.addEventListener('click', ()=>{
                  centerTo(parseFloat(it.lat), parseFloat(it.lon));
                  box.value = it.display_name;
                  sug.style.display='none';
                });
                sug.appendChild(b);
              });
              sug.style.display = list.length ? 'block' : 'none';
            }).catch(()=>{});
        }, 300);
      });
      document.addEventListener('click', (e)=>{ if(!(sug.contains(e.target)||e.target===box)){ sug.style.display='none'; } });
    }

    // Set nilai lat/lng awal
    setHidden(map.getCenter().lat, map.getCenter().lng);
  } catch (err) {
    console.error('Leaflet init error:', err);
  }

  /* ---------- Qty + Summary ---------- */
  function recalcTotals(discount=window.gsDiscount||0){
    let subtotal = 0;
    document.querySelectorAll('.gs-price').forEach(c=>{
      subtotal += parseInt((c.textContent||'0').replace(/\D/g,''));
    });
    const shipping = {{ $shipping }};
    const grand = Math.max(0, subtotal + shipping - discount);

    document.getElementById('sum-subtotal').textContent = 'Rp ' + subtotal.toLocaleString('id-ID');
    document.getElementById('sum-grand').textContent    = 'Rp ' + grand.toLocaleString('id-ID');
    document.getElementById('footTotal').textContent    = 'Rp ' + grand.toLocaleString('id-ID');
  }

  document.querySelectorAll('.gs-row').forEach(row=>{
    const dec=row.querySelector('.js-dec'), inc=row.querySelector('.js-inc');
    const qty=row.querySelector('.gs-qtyinput'), priceCell=row.querySelector('.gs-price');
    if (!dec || !inc || !qty || !priceCell) return;

    const startQty = Math.max(1, parseInt(qty.value||1));
    const startSub = parseInt((priceCell.textContent||'0').replace(/\D/g,''));
    const unit = Math.max(1, Math.round(startSub / startQty));

    function refresh(){ const v=Math.max(1,parseInt(qty.value||1)); qty.value=v; const sub=v*unit; priceCell.textContent='Rp '+ sub.toLocaleString('id-ID'); recalcTotals(); }
    dec.addEventListener('click',()=>{ qty.value=Math.max(1,(parseInt(qty.value||1)-1)); refresh(); });
    inc.addEventListener('click',()=>{ qty.value=(parseInt(qty.value||1)+1); refresh(); });
  });

  /* ---------- Promo bottom sheet + kupon ---------- */
  const sheet = document.getElementById('promoSheet');
  document.getElementById('btnOpenPromo')?.addEventListener('click',()=> sheet?.classList.add('open'));
  document.getElementById('btnClosePromo')?.addEventListener('click',()=> sheet?.classList.remove('open'));

  const activeWrap = document.getElementById('activeCoupon');
  const activeCode = document.getElementById('activeCode');
  const activeDesc = document.getElementById('activeDesc');
  const discountRow = document.getElementById('sum-discount-row');
  const discountCell = document.getElementById('sum-discount');
  window.gsDiscount = 0;

  function applyCoupon(node){
    const code = node.dataset.code;
    const type = node.dataset.type;   // percent | shipping
    const val  = parseInt(node.dataset.value);
    const min  = parseInt(node.dataset.min||0);

    const sub = parseInt(document.getElementById('sum-subtotal').textContent.replace(/\D/g,''));
    if(sub < min){ alert('Belanja belum memenuhi minimum kupon'); return; }

    let discount = 0;
    if(type==='percent'){ discount = Math.floor(sub * (val/100)); }
    if(type==='shipping'){ discount = Math.min(val, {{ $shipping }}); }

    window.gsDiscount = discount;

    if (activeCode) activeCode.textContent = code;
    if (activeDesc) activeDesc.textContent = (type==='percent' ? `Diskon ${val}%` : `Potongan ongkir Rp ${val.toLocaleString('id-ID')}`) + ` • Min Rp ${min.toLocaleString('id-ID')}`;
    if (activeWrap) activeWrap.style.display = 'block';
    if (discountRow) discountRow.style.display = 'flex';
    if (discountCell) discountCell.textContent = '- Rp ' + discount.toLocaleString('id-ID');

    localStorage.setItem('gs_coupon', JSON.stringify({code,type,val,min,discount}));
    sheet?.classList.remove('open');
    recalcTotals(discount);
  }
  document.querySelectorAll('.gs-coupon .apply').forEach(btn=> btn.addEventListener('click', ()=> applyCoupon(btn.parentElement)));

  document.getElementById('btnRemoveCoupon')?.addEventListener('click', ()=>{
    window.gsDiscount = 0;
    if (activeWrap) activeWrap.style.display = 'none';
    if (discountRow) discountRow.style.display = 'none';
    if (discountCell) discountCell.textContent = '- Rp 0';
    localStorage.removeItem('gs_coupon');
    recalcTotals(0);
  });

  // Restore kupon dari localStorage
  const saved = localStorage.getItem('gs_coupon');
  if(saved){
    try{
      const data = JSON.parse(saved);
      window.gsDiscount = data.discount||0;
      if (activeCode) activeCode.textContent = data.code;
      if (activeDesc) activeDesc.textContent = (data.type==='percent' ? `Diskon ${data.val}%` : `Potongan ongkir Rp ${data.val.toLocaleString('id-ID')}`) + ` • Min Rp ${data.min.toLocaleString('id-ID')}`;
      if (activeWrap) activeWrap.style.display = 'block';
      if (discountRow) discountRow.style.display = 'flex';
      if (discountCell) discountCell.textContent = '- Rp ' + (data.discount||0).toLocaleString('id-ID');
      recalcTotals(window.gsDiscount);
    }catch(e){}
  }

  /* ---------- Toggle "+ add note" — scoped per baris ---------- */
  document.querySelectorAll('.gs-row .gs-addnote').forEach(btn => {
    const row   = btn.closest('.gs-row');
    const input = row?.querySelector('.gs-note');
    if (!input) return;

    const setLabel = () => { btn.textContent = (input.value && input.value.trim() !== '') ? 'Edit catatan' : '+ add note'; };
    setLabel();

    btn.addEventListener('click', (e) => {
      e.preventDefault();
      const showing = !input.classList.contains('show');
      input.classList.toggle('show', showing);
      if (showing) { btn.textContent = 'Simpan catatan'; input.focus(); }
      else { setLabel(); }
    });

    input.addEventListener('keydown', (e) => {
      if (e.key === 'Enter') {
        e.preventDefault();
        input.classList.remove('show');
        setLabel();
      }
    });

    input.addEventListener('blur', () => {
      if (!input.classList.contains('show')) return;
      input.classList.remove('show');
      setLabel();
    });
  });
});
</script>
@endsection
