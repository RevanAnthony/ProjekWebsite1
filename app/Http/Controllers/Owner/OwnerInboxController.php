<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\Ulasan;
use Illuminate\Http\Request;

class OwnerInboxController extends Controller
{
    public function index(Request $request)
    {
        $ratingFilter = $request->query('rating'); // 1..5 atau null
        $search       = $request->query('q');

        // ====== LIST ULASAN (FILTER + SEARCH) ======
        $query = Ulasan::with(['pengguna', 'pesanan'])
            ->orderByDesc('created_at');

        if ($ratingFilter) {
            $query->where('rating', (int) $ratingFilter);
        }

        if ($search) {
            $q = trim($search);
            $query->where(function ($sub) use ($q) {
                $sub->where('komentar', 'like', "%{$q}%")
                    ->orWhereHas('pengguna', function ($u) use ($q) {
                        $u->where('nama', 'like', "%{$q}%");
                    })
                    ->orWhereHas('pesanan', function ($p) use ($q) {
                        $p->where('id_pesanan', 'like', "%{$q}%");
                    });
            });
        }

        $reviews = $query->paginate(15)->withQueryString();

        // ====== KARTU ANGKA ATAS ======
        $avgRating     = round((float) (Ulasan::avg('rating') ?? 0), 1);
        $totalReviews  = (int) Ulasan::count();
        $followUpCount = (int) Ulasan::where('rating', '<=', 3)->count();

        // ====== DISTRIBUSI RATING (1–5) UNTUK BAR CHART ======
        $rawBuckets = Ulasan::selectRaw('rating, COUNT(*) as total')
            ->groupBy('rating')
            ->pluck('total', 'rating')
            ->all();

        // pastikan selalu ada index 1..5
        $ratingBuckets = [];
        for ($i = 1; $i <= 5; $i++) {
            $ratingBuckets[$i] = (int) ($rawBuckets[$i] ?? 0);
        }

        // ====== SENTIMEN (DONUT CHART) ======
        // 4–5 = positive, 3 = neutral, 1–2 = negative
        $sentimentSummary = [
            'positive' => (int) Ulasan::where('rating', '>=', 4)->count(),
            'neutral'  => (int) Ulasan::where('rating', 3)->count(),
            'negative' => (int) Ulasan::where('rating', '<=', 2)->count(),
        ];

        return view('owner.inbox.index', [
            'reviews'          => $reviews,
            'ratingFilter'     => $ratingFilter,
            'search'           => $search,
            'avgRating'        => $avgRating,
            'totalReviews'     => $totalReviews,
            'followUpCount'    => $followUpCount,
            'ratingBuckets'    => $ratingBuckets,
            'sentimentSummary' => $sentimentSummary,
        ]);
    }
}
