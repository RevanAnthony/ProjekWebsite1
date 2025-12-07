<?php

namespace App\Http\Controllers;

use App\Models\Pesanan;
use App\Models\Chat;
use App\Models\ChatMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChatController extends Controller
{
    // Halaman chat untuk 1 pesanan (user)
    public function showOrderChat(int $id)
    {
        $user = Auth::user();

        // pastikan pesanan memang milik user yang login
        $order = Pesanan::where('id_pesanan', $id)
            ->where('id_pengguna', $user->id_pengguna)
            ->firstOrFail();

        // buat chat kalau belum ada
        $chat = Chat::firstOrCreate(
            ['id_pesanan' => $order->id_pesanan, 'context_type' => 'order'],
            ['title' => 'Pesanan #' . $order->id_pesanan]
        );

        $messages = $chat->messages()
            ->with('sender')
            ->orderBy('created_at', 'asc')
            ->get();

        // tandai pesan lawan sudah dibaca
        ChatMessage::where('id_chat', $chat->id_chat)
            ->where('sender_role', '!=', $user->role)
            ->where('is_read', false)
            ->update([
                'is_read' => true,
                'read_at' => now(),
            ]);

        return view('orders.chat', [
            'order'    => $order,
            'chat'     => $chat,
            'messages' => $messages,
        ]);
    }

    // Kirim pesan baru dari user
    public function sendOrderMessage(Request $request, int $id)
    {
        $user = Auth::user();

        $order = Pesanan::where('id_pesanan', $id)
            ->where('id_pengguna', $user->id_pengguna)
            ->firstOrFail();

        $chat = Chat::firstOrCreate(
            ['id_pesanan' => $order->id_pesanan, 'context_type' => 'order'],
            ['title' => 'Pesanan #' . $order->id_pesanan]
        );

        $validated = $request->validate([
            'message' => ['required', 'string', 'max:1000'],
        ]);

        ChatMessage::create([
            'id_chat'     => $chat->id_chat,
            'id_pengguna' => $user->id_pengguna,
            'sender_role' => $user->role,
            'isi_pesan'   => $validated['message'],
        ]);

        return redirect()
            ->route('orders.chat', ['id' => $order->id_pesanan])
            ->with('success', 'Pesan terkirim.');
    }
}
