<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pesanan;
use Illuminate\Http\Request;

class AdminOrderController extends Controller
{
    /**
     * Halaman manajemen pesanan untuk kasir/admin.
     *
     * View: resources/views/admin/orders/index.blade.php
     * Variabel:
     *  - $activeOrders
     *  - $historyOrders
     *  - $statusCounts
     */
    public function index()
    {
        // Pesanan aktif = belum selesai & belum dibatalkan
        $activeOrders = Pesanan::whereNotIn('status_pesanan', ['selesai', 'dibatalkan'])
            ->orderByDesc('tanggal_pesanan')
            ->orderByDesc('created_at')
            ->get();

        // Riwayat = selesai / dibatalkan
        $historyOrders = Pesanan::whereIn('status_pesanan', ['selesai', 'dibatalkan'])
            ->orderByDesc('tanggal_pesanan')
            ->orderByDesc('created_at')
            ->limit(30)
            ->get();

        // Untuk hitungan badge tab (Semua, Pending, Dapur, Delivery, Selesai)
        $allOrders = $activeOrders->concat($historyOrders);

        $statusCounts = [
            'all'      => $allOrders->count(),
            'pending'  => 0,
            'cooking'  => 0,
            'delivery' => 0,
            'done'     => 0,
        ];

        foreach ($allOrders as $order) {
            $raw = strtolower($order->status_pesanan ?? '');

            if (
                str_contains($raw, 'pending') ||
                str_contains($raw, 'menunggu') ||
                str_contains($raw, 'konfirmasi')
            ) {
                $statusCounts['pending']++;
            } elseif (
                str_contains($raw, 'dapur') ||
                str_contains($raw, 'masak') ||
                str_contains($raw, 'cook')
            ) {
                $statusCounts['cooking']++;
            } elseif (
                str_contains($raw, 'antar') ||
                str_contains($raw, 'delivery') ||
                str_contains($raw, 'kirim')
            ) {
                $statusCounts['delivery']++;
            } elseif (
                str_contains($raw, 'selesai') ||
                str_contains($raw, 'done')
            ) {
                $statusCounts['done']++;
            }
        }

        return view('admin.orders.index', [
            'activeOrders'  => $activeOrders,
            'historyOrders' => $historyOrders,
            'statusCounts'  => $statusCounts,
        ]);
    }

    /**
     * Detail pesanan untuk kasir/admin.
     *
     * View: resources/views/admin/orders/show.blade.php
     */
    public function show(Pesanan $order)
    {
        // load relasi yang dipakai di view (kalau belum ke-load)
        $order->loadMissing(['items', 'pengguna']);

        return view('admin.orders.show', [
            'order' => $order,
        ]);
    }

    /**
     * Update status pesanan dari panel kasir.
     *
     * Aksi:
     *  - to_cooking  : dari pending -> dapur
     *  - to_delivery : dari dapur   -> diantar
     *  - finish      : dari diantar -> selesai
     *  - cancel      : batalkan pesanan
     *
     * Route: POST /gs-kasir-panel-x01/orders/{order}/status
     */
    public function updateStatus(Request $request, Pesanan $order)
    {
        $data = $request->validate([
            'action' => 'required|in:to_cooking,to_delivery,finish,cancel',
        ]);

        switch ($data['action']) {
            case 'to_cooking':
                // Dari pesanan masuk -> dapur
                $order->status_pesanan = 'diproses_dapur';
                break;

            case 'to_delivery':
                // Dari dapur -> sedang diantar
                $order->status_pesanan = 'diantar';
                break;

            case 'finish':
                // Pesanan selesai
                $order->status_pesanan = 'selesai';
                break;

            case 'cancel':
                // Pesanan dibatalkan
                $order->status_pesanan = 'dibatalkan';
                break;
        }

        $order->save();

        $orderId = $order->id_pesanan ?? $order->getKey();

        return redirect()
            ->route('admin.orders.index')
            ->with('success', "Status pesanan #{$orderId} berhasil diperbarui.");
    }
}
