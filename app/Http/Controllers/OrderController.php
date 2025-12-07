<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pesanan;
use App\Models\DetailPesanan;
use App\Models\Ulasan;

class OrderController extends Controller
{
    /**
     * Halaman list pesanan (aktif + riwayat).
     */
    public function index()
    {
        $userId = auth()->id();

        // Pesanan yang masih berjalan (apapun selain selesai/dibatalkan)
        $activeOrders = Pesanan::where('id_pengguna', $userId)
            ->whereNotIn('status_pesanan', ['selesai', 'dibatalkan'])
            ->orderByDesc('tanggal_pesanan')
            ->get();

        // Riwayat: pesanan selesai / dibatalkan (dibatasi 20 terakhir)
        $historyOrders = Pesanan::where('id_pengguna', $userId)
            ->whereIn('status_pesanan', ['selesai', 'dibatalkan'])
            ->orderByDesc('tanggal_pesanan')
            ->limit(20)
            ->get();

        return view('orders.index', [
            'activeOrders'  => $activeOrders,
            'historyOrders' => $historyOrders,
        ]);
    }

    /**
     * Detail / tracking satu pesanan (order-status) untuk USER.
     * User hanya boleh lihat pesanan miliknya sendiri.
     */
    public function show($id)
    {
        $userId = auth()->id();

        $order = Pesanan::where('id_pesanan', $id)
            ->where('id_pengguna', $userId)
            ->firstOrFail();

        $items = DetailPesanan::where('id_pesanan', $order->id_pesanan)->get();

        return view('pages.order-status', compact('order', 'items'));
    }

    /**
     * Halaman QRIS untuk pesanan tertentu.
     * Tetap dikunci ke pemilik pesanan + hanya untuk metode pembayaran QRIS.
     */
    public function showQris($id)
    {
        $userId = auth()->id();

        $pesanan = Pesanan::where('id_pesanan', $id)
            ->where('id_pengguna', $userId)
            ->firstOrFail();

        // Kalau bukan QRIS, lempar ke halaman detail biasa saja
        if ($pesanan->metode_pembayaran !== 'qris') {
            $orderId = $pesanan->id_pesanan ?? $pesanan->getKey();

            return redirect()->route('orders.show', ['id' => $orderId]);
        }

        // Paksa pakai public/images/qris.jpg
        $qrisImageUrl = asset('images/qris.jpg');

        return view('orders.qris', compact('pesanan', 'qrisImageUrl'));
    }

    /**
     * User klik "Saya sudah bayar" di halaman QRIS.
     * - Hanya pemilik pesanan yang boleh
     * - Hanya metode QRIS
     * - Hanya kalau status masih "menunggu_pembayaran"
     */
    public function confirmQris($id)
    {
        $userId = auth()->id();

        $pesanan = Pesanan::where('id_pesanan', $id)
            ->where('id_pengguna', $userId)
            ->firstOrFail();

        // QRIS saja yang boleh lewat sini
        if ($pesanan->metode_pembayaran !== 'qris') {
            $orderId = $pesanan->id_pesanan ?? $pesanan->getKey();

            return redirect()->route('orders.show', ['id' => $orderId]);
        }

        // Hanya update kalau masih di status "menunggu_pembayaran"
        if ($pesanan->status_pesanan === 'menunggu_pembayaran') {
            $pesanan->update([
                // artinya: user mengklaim sudah bayar, kasir belum cek
                'status_pesanan' => 'menunggu_konfirmasi_toko',
            ]);
        }

        $orderId = $pesanan->id_pesanan ?? $pesanan->getKey();

        return redirect()
            ->route('orders.show', ['id' => $orderId])
            ->with('success', 'Terima kasih, pembayaran kamu akan dicek oleh kasir.');
    }

    /**
     * Simpan / update ulasan untuk satu pesanan.
     * Hanya boleh kalau pesanan milik user dan statusnya 'selesai'.
     */
    public function submitReview(Request $request, $id)
    {
        $user = auth()->user();

        // Cari pesanan dan pastikan milik user
        $pesanan = Pesanan::where('id_pesanan', $id)
            ->where('id_pengguna', $user->id_pengguna)
            ->firstOrFail();

        // Kalau belum selesai, jangan boleh review
        if ($pesanan->status_pesanan !== 'selesai') {
            return redirect()
                ->route('orders.show', ['id' => $id])
                ->with('error', 'Ulasan hanya bisa diberikan untuk pesanan yang sudah selesai.');
        }

        // Validasi input
        $data = $request->validate([
            'rating'   => ['required', 'integer', 'min:1', 'max:5'],
            'komentar' => ['nullable', 'string', 'max:1000'],
        ]);

        // Simpan / update (1 pesanan 1 ulasan per user)
        $pesanan->ulasan()->updateOrCreate(
            [
                'id_pesanan' => $pesanan->id_pesanan,
            ],
            [
                'id_pengguna'    => $user->id_pengguna,
                'rating'         => $data['rating'],
                'komentar'       => $data['komentar'] ?? null,
                'tanggal_ulasan' => now(),
            ]
        );

        return redirect()
            ->route('orders.show', ['id' => $pesanan->id_pesanan])
            ->with('success', 'Terima kasih, ulasan kamu sudah tersimpan.');
    }
}
