<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\Pesanan;
use App\Models\DetailPesanan;
use App\Models\Ulasan;
use Illuminate\Support\Facades\DB;

class OwnerDashboardController extends Controller
{
    public function index()
    {
        $now          = now();
        $startOfMonth = $now->copy()->startOfMonth();
        $endOfMonth   = $now->copy()->endOfMonth();

        // Pesanan bulan ini
        $ordersQuery = Pesanan::query()
            ->whereBetween('created_at', [$startOfMonth, $endOfMonth]);

        $orderIds          = $ordersQuery->pluck('id_pesanan');
        $totalSales        = (int) $ordersQuery->sum('total_pembayaran');
        $totalTransactions = (int) $ordersQuery->count();
        $totalItemsSold    = (int) DetailPesanan::whereIn('id_pesanan', $orderIds)->sum('jumlah');
        $avgRating         = (float) (Ulasan::avg('rating') ?? 0);

        // Grafik 7 hari terakhir
        $labels = [];
        $values = [];
        for ($i = 6; $i >= 0; $i--) {
            $d    = $now->copy()->subDays($i)->startOfDay();
            $dEnd = $d->copy()->endOfDay();
            $labels[] = $d->format('D'); // Sen, Sel, Rab, ...

            $values[] = (int) Pesanan::whereBetween('created_at', [$d, $dEnd])
                ->sum('total_pembayaran');
        }

        // Menu terlaris (top 5)
        $topMenus = DetailPesanan::select('id_produk', DB::raw('SUM(jumlah) as total_qty'))
            ->groupBy('id_produk')
            ->orderByDesc('total_qty')
            ->take(5)
            ->get()
            ->load('produk');

        // Ulasan terbaru (max 3)
        $latestReviews = Ulasan::with(['pengguna', 'pesanan'])
            ->orderByDesc('created_at')
            ->take(3)
            ->get();

        // Ringkasan keuangan sederhana (estimasi)
        $totalRevenue = $totalSales;
        $cogsEstimate = (int) round($totalRevenue * 0.6); // biaya bahan (60%)
        $operational  = (int) round($totalRevenue * 0.2); // operasional (20%)
        $totalCost    = $cogsEstimate + $operational;
        $netProfit    = $totalRevenue - $totalCost;

        return view('owner.dashboard.index', [
            'totalSales'        => $totalSales,
            'totalTransactions' => $totalTransactions,
            'totalItemsSold'    => $totalItemsSold,
            'avgRating'         => $avgRating,
            'chartLabels'       => $labels,
            'chartValues'       => $values,
            'topMenus'          => $topMenus,
            'latestReviews'     => $latestReviews,
            'totalRevenue'      => $totalRevenue,
            'cogsEstimate'      => $cogsEstimate,
            'operational'       => $operational,
            'totalCost'         => $totalCost,
            'netProfit'         => $netProfit,
        ]);
    }
}
