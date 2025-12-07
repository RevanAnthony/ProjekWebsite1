<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Keranjang;

class CouponController extends Controller
{
    public function validateCoupon(Request $req)
    {
        $code = strtoupper(trim($req->input('code', '')));

        $cart = Keranjang::where('id_pengguna', auth()->id())
            ->where('status','draft')
            ->with(['items.produk'])
            ->first();

        if (!$cart || $cart->items->isEmpty()) {
            return response()->json(['valid'=>false, 'message'=>'Keranjang kosong'], 422);
        }

        // Hitung subtotal realtime
        $subtotal = 0;
        foreach ($cart->items as $it) {
            $harga = (int) ($it->produk->harga ?? 0);
            $qty   = max(1, (int) $it->jumlah);
            $subtotal += $harga * $qty;
        }
        $shipping = 5000;

        $valid=false; $discount=0; $desc=''; $min=0;
        switch ($code) {
            case 'HEMAT10':  $min=50000; $valid=$subtotal>=$min; if($valid){ $discount=intdiv($subtotal*10,100); $desc='Diskon 10%'; } break;
            case 'ONGKIR5K': $min=30000; $valid=$subtotal>=$min; if($valid){ $discount=min(5000,$shipping); $desc='Potong ongkir 5K'; } break;
            case 'SPICY20':  $min=75000; $valid=$subtotal>=$min; if($valid){ $discount=intdiv($subtotal*20,100); $desc='Diskon 20%'; } break;
        }

        if (!$valid) {
            return response()->json(['valid'=>false,'message'=>'Kupon tidak valid / belum memenuhi minimum.'], 422);
        }

        return response()->json([
            'valid'    => true,
            'code'     => $code,
            'discount' => $discount,
            'desc'     => $desc,
            'subtotal' => $subtotal,
            'shipping' => $shipping,
            'total'    => max(0, $subtotal + $shipping - $discount),
        ]);
    }
}
