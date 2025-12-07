<?php

namespace App\Http\Controllers\Cashier;

use App\Http\Controllers\Controller;
use App\Models\Pesanan;
use Illuminate\Http\Request;

class CashierOrderController extends Controller
{
    /**
     * Halaman pesanan untuk kasir.
     */
    public function index()
    {
        // Pesanan aktif (belum selesai / dibatalkan)
        $activeOrders = Pesanan::whereNotIn('status_pesanan', ['selesai', 'dibatalkan'])
            ->orderByDesc('tanggal_pesanan')
            ->get();

        // Riwayat pesanan
        $historyOrders = Pesanan::whereIn('status_pesanan', ['selesai', 'dibatalkan'])
            ->orderByDesc('tanggal_pesanan')
            ->limit(30)
            ->get();

        return view('cashier.orders.index', compact('activeOrders', 'historyOrders'));
    }

    /**
     * Update status pesanan dari panel kasir.
     * action: confirm_payment | mark_done | cancel
     */
    public function updateStatus(Request $request, Pesanan $pesanan)
    {
        $request->validate([
            'action' => 'required|in:confirm_payment,mark_done,cancel',
        ]);

        $action = $request->input('action');

        switch ($action) {
            case 'confirm_payment':
                // Kasir mengonfirmasi bahwa pembayaran sudah diterima
                $pesanan->status_pesanan = 'dikonfirmasi_toko';
                break;

            case 'mark_done':
                // Pesanan sudah selesai (sudah diantar / diambil)
                $pesanan->status_pesanan = 'selesai';
                break;

            case 'cancel':
                // Pesanan dibatalkan
                $pesanan->status_pesanan = 'dibatalkan';
                break;
        }

        $pesanan->save();

        return redirect()
            ->route('cashier.orders.index')
            ->with('success', 'Status pesanan #' . ($pesanan->id_pesanan ?? $pesanan->getKey()) . ' diperbarui.');
    }
}
