<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use App\Models\Keranjang;
use App\Models\DetailKeranjang;
use App\Models\DetailPesanan;
use App\Models\Produk;
use App\Models\Pesanan;

class CartController extends Controller
{
    /** Ambil/buat keranjang draft milik user (aman walau kolom status tidak ada) */
    protected function draft(): Keranjang
    {
        $userId = Auth::id();

        if (Schema::hasColumn('keranjang', 'status')) {
            $cart = Keranjang::where('id_pengguna', $userId)
                ->where('status', 'draft')
                ->first();

            if (!$cart) {
                $cart = new Keranjang();
                $cart->id_pengguna    = $userId;
                $cart->status         = 'draft';
                $cart->tanggal_dibuat = now()->toDateString();
                $cart->save();
            }
            return $cart->fresh();
        }

        $cart = Keranjang::firstOrNew(['id_pengguna' => $userId]);
        if (!$cart->exists) {
            $cart->tanggal_dibuat = now()->toDateString();
            $cart->save();
        }
        return $cart->fresh();
    }

    /** Bentuk JSON untuk drawer/cart */
    protected function toCartJson(Keranjang $cart)
    {
        $cart->load(['items.produk']);

        $items = $cart->items->map(function ($i) {
            $harga = (int) ($i->produk->harga ?? 0);
            return [
                'id_detail_keranjang' => $i->id_detail_keranjang,
                'produk_id'           => $i->id_produk,
                'nama'                => $i->produk->nama_produk ?? '',
                'url_gambar'          => $i->produk->url_gambar ? asset($i->produk->url_gambar) : null,
                'harga'               => $harga,
                'qty'                 => (int) $i->jumlah,
                'subtotal'            => (int) $i->subtotal,
            ];
        })->values();

        return response()->json([
            'count' => (int) $items->sum('qty'),
            'total' => (int) $items->sum('subtotal'),
            'items' => $items,
        ]);
    }

    /** GET /cart (opsi router pakai index) */
    public function index()
    {
        return $this->show();
    }

    /** GET /cart (opsi router pakai show) */
    public function show()
    {
        try {
            $cart = $this->draft();
            return $this->toCartJson($cart);
        } catch (\Throwable $e) {
            Log::error('GET /cart failed', ['err' => $e->getMessage()]);
            return response()->json(['message' => 'Cart error'], 500);
        }
    }

    /** POST /cart/add — terima produk_id|product_id, qty|quantity */
    public function add(Request $request)
    {
        $produkId = (int) ($request->input('produk_id') ?? $request->input('product_id'));
        $qty      = (int) ($request->input('qty') ?? $request->input('quantity') ?? 1);

        $request->merge(['__pid' => $produkId, '__qty' => $qty]);
        $request->validate([
            '__pid' => 'required|integer|exists:produk,id_produk',
            '__qty' => 'required|integer|min:1',
        ]);

        try {
            $produk = Produk::findOrFail($produkId);
            $cart   = $this->draft();

            $row = DetailKeranjang::where('id_keranjang', $cart->id_keranjang)
                ->where('id_produk', $produk->id_produk)
                ->first();

            if ($row) {
                $row->jumlah += $qty;
            } else {
                $row = new DetailKeranjang([
                    'id_keranjang' => $cart->id_keranjang,
                    'id_produk'    => $produk->id_produk,
                    'jumlah'       => $qty,
                ]);
            }

            $row->subtotal = (int) ($row->jumlah * (int) $produk->harga);
            $row->save();

            return $this->toCartJson($cart->fresh());
        } catch (\Throwable $e) {
            Log::error('POST /cart/add failed', [
                'produk_id' => $produkId,
                'qty'       => $qty,
                'err'       => $e->getMessage(),
            ]);
            return response()->json(['message' => 'Failed to add item to cart'], 500);
        }
    }

    /** PATCH /cart/item/{detail} — qty=0 => delete */
    public function updateQty(Request $request, $detailId)
    {
        $data = $request->validate(['qty' => 'required|integer|min:0']);

        try {
            $detail = DetailKeranjang::where('id_detail_keranjang', $detailId)
                ->whereHas('keranjang', fn($q) => $q->where('id_pengguna', Auth::id()))
                ->firstOrFail();

            if ((int) $data['qty'] === 0) {
                $detail->delete();
            } else {
                $detail->loadMissing('produk');
                $harga = (int) ($detail->produk->harga ?? 0);
                $detail->jumlah   = (int) $data['qty'];
                $detail->subtotal = $detail->jumlah * $harga;
                $detail->save();
            }

            $cart = $this->draft();
            return $this->toCartJson($cart->fresh());
        } catch (\Throwable $e) {
            Log::error('PATCH /cart/item failed', ['detail' => $detailId, 'err' => $e->getMessage()]);
            return response()->json(['message' => 'Failed to update item'], 500);
        }
    }

    /** DELETE /cart/item/{detail} */
    public function remove($detailId)
    {
        try {
            $detail = DetailKeranjang::where('id_detail_keranjang', $detailId)
                ->whereHas('keranjang', fn($q) => $q->where('id_pengguna', Auth::id()))
                ->firstOrFail();

            $detail->delete();

            $cart = $this->draft();
            return $this->toCartJson($cart->fresh());
        } catch (\Throwable $e) {
            Log::error('DELETE /cart/item failed', ['detail' => $detailId, 'err' => $e->getMessage()]);
            return response()->json(['message' => 'Failed to remove item'], 500);
        }
    }

    /** DELETE /cart — kosongkan semua item */
    public function clear()
    {
        try {
            $cart = $this->draft();
            DetailKeranjang::where('id_keranjang', $cart->id_keranjang)->delete();
            return $this->toCartJson($cart->fresh());
        } catch (\Throwable $e) {
            Log::error('DELETE /cart failed', ['err' => $e->getMessage()]);
            return response()->json(['message' => 'Failed to clear cart'], 500);
        }
    }

    /** POST /cart/sync — mirror dari localStorage ke DB */
    public function sync(Request $request)
    {
        $request->validate(['items' => 'required|array|min:1']);

        try {
            $cart = $this->draft();

            foreach ($request->input('items', []) as $it) {
                $pid = (int) ($it['id'] ?? $it['produk_id'] ?? 0);
                $qty = (int) ($it['qty'] ?? 0);
                if ($pid <= 0 || $qty <= 0) continue;

                $produk = Produk::find($pid);
                if (!$produk) continue;

                $row = DetailKeranjang::firstOrNew([
                    'id_keranjang' => $cart->id_keranjang,
                    'id_produk'    => $pid,
                ]);
                $row->jumlah   = $qty;
                $row->subtotal = (int) ($qty * (int) $produk->harga);
                $row->save();
            }

            return $this->toCartJson($cart->fresh());
        } catch (\Throwable $e) {
            Log::error('POST /cart/sync failed', ['err' => $e->getMessage()]);
            return response()->json(['message' => 'Failed to sync cart'], 500);
        }
    }

    /** Opsional halaman review */
    public function review()
    {
        $cart = $this->draft()->load(['items.produk']);
        return view('pages.review', compact('cart'));
    }

    public function payment()
    {
        // ambil keranjang draft + produk untuk ditampilkan di halaman pembayaran
        $cart = $this->draft()->load(['items.produk']);

        // kalau kosong, kembalikan ke menu
        if ($cart->items->isEmpty()) {
            return redirect()->route('menu')->with('error', 'Keranjang masih kosong.');
        }

        // tampilkan halaman payment
        return view('pages.payment', compact('cart'));
    }

    public function checkoutStore(Request $req)
    {
        $req->validate([
            'telepon'            => ['required','string','min:8'],
            'alamat'             => ['nullable','string','max:255'],
            'catatan_pengiriman' => ['nullable','string','max:255'],
            'lat'                => ['nullable','numeric'],
            'lng'                => ['nullable','numeric'],
            'coupon_code'        => ['nullable','string','max:40'],
            'metode_pengambilan' => ['nullable','in:antar,ambil_sendiri'],
            'metode_pembayaran'  => ['required','in:qris,cod'],
        ]);

        // Ambil keranjang draft user + produk
        $cart = Keranjang::where('id_pengguna', auth()->id())
            ->where('status', 'draft')
            ->with(['items.produk'])
            ->firstOrFail();

        if ($cart->items->isEmpty()) {
            return back()->with('error', 'Keranjang kosong.');
        }

        // Re-calc subtotal dari harga produk saat ini
        $subtotal = 0;
        foreach ($cart->items as $it) {
            $harga = (int) ($it->produk->harga ?? 0);
            $qty   = max(1, (int) $it->jumlah);
            $it->subtotal = $harga * $qty;
            $subtotal    += $it->subtotal;
        }

        $shipping = 5000;  // samakan dengan UI
        $discount = 0;
        $total    = max(0, $subtotal + $shipping - $discount);

        // Satukan alamat/telepon/koordinat/kupon ke catatan (sementara)
        $noteParts = array_filter([
            $req->catatan_pengiriman,
            $req->alamat ? 'Alamat: '.$req->alamat : null,
            $req->telepon ? 'Telp: '.$req->telepon : null,
            ($req->lat && $req->lng) ? ('Lokasi: '.$req->lat.','.$req->lng) : null,
            $req->coupon_code ? 'Kupon: '.$req->coupon_code : null,
        ]);
        $catatan = trim(implode(' | ', $noteParts));

        // Simpan pesanan + detail di dalam transaksi
        $pesanan = DB::transaction(function () use ($cart, $req, $subtotal, $shipping, $total, $catatan) {

            // 1) Buat pesanan
            $pesanan = Pesanan::create([
                'id_pengguna'           => auth()->id(),
                'id_alamat_pengiriman'  => null,
                'id_driver'             => null,
                'biaya_produk'          => $subtotal,
                'biaya_ongkir'          => $shipping,
                'total_pembayaran'      => $total,
                'metode_pengambilan'    => $req->input('metode_pengambilan','antar'),
                'status_pesanan'        => 'menunggu_pembayaran',
                'catatan_pesanan'       => $catatan ?: null,
                'estimasi_waktu'        => null,
                'tanggal_pesanan'       => now(),
                'metode_pembayaran'     => $req->input('metode_pembayaran','cod'),
            ]);

            $orderId = $pesanan->id_pesanan ?? $pesanan->getKey();
            session(['last_order_id' => $orderId]);

            // 2) Buat detail_pesanan
            foreach ($cart->items as $it) {
                DetailPesanan::create([
                    'id_pesanan'  => $orderId,
                    'id_produk'   => $it->id_produk,
                    'nama_produk' => $it->produk->nama_produk ?? 'Produk',
                    'harga'       => (int) ($it->produk->harga ?? 0),
                    'jumlah'      => (int) $it->jumlah,
                    'subtotal'    => (int) $it->subtotal,
                    'url_gambar'  => $it->produk->url_gambar ?? null,
                    'catatan'     => $it->catatan ?? null,
                ]);

                // (opsional) kurangi stok
                Produk::where('id_produk', $it->id_produk)
                      ->decrement('stok', (int) $it->jumlah);
            }

            // 3) Tutup keranjang
            $cart->update(['status' => 'checkout']);
            $cart->items()->delete();

            return $pesanan;
        });

        // --- SETELAH TRANSAKSI: tentukan redirect berdasarkan metode pembayaran ---
        $orderId = $pesanan->id_pesanan ?? $pesanan->getKey();

        if ($req->metode_pembayaran === 'qris') {
            return redirect()
                ->route('orders.qris', ['id' => $orderId]);
        }

        // COD -> langsung ke halaman detail / tracking
        return redirect()
            ->route('orders.show', ['id' => $orderId])
            ->with('success', 'Pesanan dibuat. Lanjut pembayaran.');
    }
}
