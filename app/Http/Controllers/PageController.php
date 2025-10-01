<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PageController extends Controller
{
    public function home()   { return view('pages.home'); }
    public function about()  { return view('pages.about'); }
    public function contact(){ return view('pages.contact'); }

    public function sendContact(Request $request)
    {
        $data = $request->validate([
            'name'    => ['required','string','max:100'],
            'email'   => ['required','email'],
            'message' => ['required','string','max:2000'],
        ]);

        // Simpel dulu: catat ke log
        \Log::info('Contact form', $data);

        // Nanti bisa diganti kirim email / simpan DB
        return back()->with('ok', 'Terima kasih! Pesanmu sudah kami terima.');
    }
}
