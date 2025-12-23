<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\Pesanan;
use App\Models\DetailPesanan;
use App\Models\Ulasan;
use App\Models\Produk;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OwnerDashboardController extends Controller
{
    public function index()
    {
        $now          = now();
        $startOfMonth = $now->copy()->startOfMonth();
        $endOfMonth   = $now->copy()->endOfMonth();

        // Base query pesanan bulan ini
        $ordersBase = Pesanan::query()
            ->whereBetween('created_at', [$startOfMonth, $endOfMonth]);

        $orderIds          = (clone $ordersBase)->pluck('id_pesanan');
        $totalSales        = (int) (clone $ordersBase)->sum('total_pembayaran');
        $totalTransactions = (int) (clone $ordersBase)->count();

        // MENU SOLD (yang dibutuhkan blade: $menuSold)
        $menuSold = (int) DetailPesanan::whereIn('id_pesanan', $orderIds)->sum('jumlah');

        // RATING (yang dibutuhkan blade: $ratingAvg, $ratingCount)
        $ratingAvg   = (float) (Ulasan::avg('rating') ?? 0);
        $ratingCount = (int) Ulasan::count();

        // ===== Grafik Overall (7 hari terakhir) -> $overallLabels, $overallValues
        $overallLabels = [];
        $overallValues = [];

        for ($i = 6; $i >= 0; $i--) {
            $d    = $now->copy()->subDays($i)->startOfDay();
            $dEnd = $d->copy()->endOfDay();

            $overallLabels[] = $d->format('D'); // Mon, Tue, ...
            $overallValues[] = (int) Pesanan::whereBetween('created_at', [$d, $dEnd])
                ->sum('total_pembayaran');
        }

        // ===== Menu Terlaris bulan ini (Top 5) -> $topProducts
        // Ambil dari detail_pesanan + join pesanan supaya sesuai bulan ini
        $topProducts = DetailPesanan::query()
            ->join('pesanan', 'detail_pesanan.id_pesanan', '=', 'pesanan.id_pesanan')
            ->whereBetween('pesanan.created_at', [$startOfMonth, $endOfMonth])
            ->select(
                'detail_pesanan.id_produk',
                DB::raw('SUM(detail_pesanan.jumlah) as qty'),
                DB::raw('MAX(detail_pesanan.nama_produk) as nama_produk'),
                DB::raw('MAX(detail_pesanan.harga) as harga')
            )
            ->groupBy('detail_pesanan.id_produk')
            ->orderByDesc('qty')
            ->take(5)
            ->get();

        // ===== Ulasan terbaru (max 3) -> $recentReviews
        // load pesanan.items biar bisa tampil "Pesanan: 1x ..."
        $recentReviews = Ulasan::with(['pengguna', 'pesanan.items'])
            ->orderByDesc('created_at')
            ->take(3)
            ->get();

        // (Opsional) tetap kirim alias lama biar tidak error kalau ada blade lain yang masih pake variabel lama
        $totalItemsSold = $menuSold;
        $avgRating      = $ratingAvg;
        $chartLabels    = $overallLabels;
        $chartValues    = $overallValues;

        return view('owner.dashboard.index', [
            // variabel untuk dashboard baru
            'totalSales'        => $totalSales,
            'totalTransactions' => $totalTransactions,
            'menuSold'          => $menuSold,
            'ratingAvg'         => $ratingAvg,
            'ratingCount'       => $ratingCount,
            'overallLabels'     => $overallLabels,
            'overallValues'     => $overallValues,
            'topProducts'       => $topProducts,
            'recentReviews'     => $recentReviews,

            // alias lama (kalau masih ada yang pakai)
            'totalItemsSold'    => $totalItemsSold,
            'avgRating'         => $avgRating,
            'chartLabels'       => $chartLabels,
            'chartValues'       => $chartValues,
        ]);
    }

    /**
     * Endpoint JSON untuk Chart per Menu (klik Menu Terlaris)
     * ?range=7d | 30d | 1y
     */
    public function menuSales(Request $request, $produk)
    {
        $produkId = (int) $produk;
        $range = $request->get('range', '7d');

        $produkModel = Produk::find($produkId);
        $produkName  = $produkModel?->nama_produk ?? 'Menu';

        $labels  = [];
        $revenue = [];

        if ($range === '30d') {
            $start = now()->subDays(29)->startOfDay();
            $end   = now()->endOfDay();

            $rows = DetailPesanan::query()
                ->join('pesanan', 'detail_pesanan.id_pesanan', '=', 'pesanan.id_pesanan')
                ->where('detail_pesanan.id_produk', $produkId)
                ->whereBetween('pesanan.created_at', [$start, $end])
                ->selectRaw('DATE(pesanan.created_at) as d, SUM(detail_pesanan.subtotal) as rev')
                ->groupBy('d')
                ->pluck('rev', 'd');

            for ($i = 29; $i >= 0; $i--) {
                $day = now()->subDays($i)->format('Y-m-d');
                $labels[]  = now()->subDays($i)->format('d M');
                $revenue[] = (int) ($rows[$day] ?? 0);
            }
        } elseif ($range === '1y') {
            $start = now()->copy()->startOfMonth()->subMonths(11);
            $end   = now()->copy()->endOfMonth();

            $rows = DetailPesanan::query()
                ->join('pesanan', 'detail_pesanan.id_pesanan', '=', 'pesanan.id_pesanan')
                ->where('detail_pesanan.id_produk', $produkId)
                ->whereBetween('pesanan.created_at', [$start, $end])
                ->selectRaw('DATE_FORMAT(pesanan.created_at, "%Y-%m") as ym, SUM(detail_pesanan.subtotal) as rev')
                ->groupBy('ym')
                ->pluck('rev', 'ym');

            for ($i = 11; $i >= 0; $i--) {
                $dt = now()->copy()->subMonths($i);
                $key = $dt->format('Y-m');
                $labels[]  = $dt->format('M');
                $revenue[] = (int) ($rows[$key] ?? 0);
            }
        } else {
            // default 7d
            $start = now()->subDays(6)->startOfDay();
            $end   = now()->endOfDay();

            $rows = DetailPesanan::query()
                ->join('pesanan', 'detail_pesanan.id_pesanan', '=', 'pesanan.id_pesanan')
                ->where('detail_pesanan.id_produk', $produkId)
                ->whereBetween('pesanan.created_at', [$start, $end])
                ->selectRaw('DATE(pesanan.created_at) as d, SUM(detail_pesanan.subtotal) as rev')
                ->groupBy('d')
                ->pluck('rev', 'd');

            for ($i = 6; $i >= 0; $i--) {
                $day = now()->subDays($i)->format('Y-m-d');
                $labels[]  = now()->subDays($i)->format('D');
                $revenue[] = (int) ($rows[$day] ?? 0);
            }
        }

        return response()->json([
            'produk'  => ['id' => $produkId, 'nama' => $produkName],
            'labels'  => $labels,
            'revenue' => $revenue,
        ]);
    }
}
