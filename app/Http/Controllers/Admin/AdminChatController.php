<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Chat;
use App\Models\ChatMessage;
use App\Models\Pesanan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminChatController extends Controller
{
    /**
     * Halaman chat admin:
     * - Kiri: daftar chat
     * - Kanan: isi chat (pakai chat pertama sebagai default)
     */
    public function index()
    {
        $admin       = Auth::guard('admin')->user();
        $currentRole = $admin->role ?? 'admin';

        // Ambil semua chat beserta order, pelanggan, dan last message
        $chats = Chat::with([
                'order.pengguna',
                'messages' => function ($q) {
                    $q->orderBy('created_at', 'desc');
                },
            ])
            ->orderBy('updated_at', 'desc')
            ->get();

        // Hitung pesan belum dibaca per chat
        if ($chats->isEmpty()) {
            $unreadCounts = collect();
            $activeChat   = null;
            $messages     = collect();
        } else {
            $unreadCounts = ChatMessage::selectRaw('id_chat, COUNT(*) as unread_count')
                ->whereIn('id_chat', $chats->pluck('id_chat'))
                ->where('sender_role', '!=', $currentRole)
                ->where('is_read', false)
                ->groupBy('id_chat')
                ->pluck('unread_count', 'id_chat');

            // chat pertama sebagai aktif default
            $activeChat = $chats->first();

            // tandai pesan lawan di chat aktif sebagai sudah dibaca
            ChatMessage::where('id_chat', $activeChat->id_chat)
                ->where('sender_role', '!=', $currentRole)
                ->where('is_read', false)
                ->update([
                    'is_read' => true,
                    'read_at' => now(),
                ]);

            $messages = $activeChat->messages()
                ->with('sender')
                ->orderBy('created_at', 'asc')
                ->get();
        }

        return view('admin.chats.index', [
            'chats'        => $chats,
            'unreadCounts' => $unreadCounts,
            'activeChat'   => $activeChat,
            'messages'     => $messages,
        ]);
    }

    /**
     * Klik salah satu chat di list (kiri) -> aktifkan chat tersebut.
     */
    public function show(int $chatId)
    {
        $admin       = Auth::guard('admin')->user();
        $currentRole = $admin->role ?? 'admin';

        // list kiri tetap sama seperti index
        $chats = Chat::with([
                'order.pengguna',
                'messages' => function ($q) {
                    $q->orderBy('created_at', 'desc');
                },
            ])
            ->orderBy('updated_at', 'desc')
            ->get();

        if ($chats->isEmpty()) {
            return view('admin.chats.index', [
                'chats'        => $chats,
                'unreadCounts' => collect(),
                'activeChat'   => null,
                'messages'     => collect(),
            ]);
        }

        $unreadCounts = ChatMessage::selectRaw('id_chat, COUNT(*) as unread_count')
            ->whereIn('id_chat', $chats->pluck('id_chat'))
            ->where('sender_role', '!=', $currentRole)
            ->where('is_read', false)
            ->groupBy('id_chat')
            ->pluck('unread_count', 'id_chat');

        // cari chat aktif berdasarkan id
        $activeChat = $chats->firstWhere('id_chat', $chatId);

        // kalau nggak ketemu di list (misal karena filter), fallback findOrFail
        if (!$activeChat) {
            $activeChat = Chat::with('order.pengguna')
                ->findOrFail($chatId);
        }

        ChatMessage::where('id_chat', $activeChat->id_chat)
            ->where('sender_role', '!=', $currentRole)
            ->where('is_read', false)
            ->update([
                'is_read' => true,
                'read_at' => now(),
            ]);

        $messages = $activeChat->messages()
            ->with('sender')
            ->orderBy('created_at', 'asc')
            ->get();

        return view('admin.chats.index', [
            'chats'        => $chats,
            'unreadCounts' => $unreadCounts,
            'activeChat'   => $activeChat,
            'messages'     => $messages,
        ]);
    }

    /**
     * Kirim pesan dari admin/kasir.
     */
    public function sendMessage(Request $request, int $chatId)
    {
        $admin       = Auth::guard('admin')->user();
        $currentRole = $admin->role ?? 'admin';

        $chat = Chat::with('order')->findOrFail($chatId);

        $validated = $request->validate([
            'message' => ['required', 'string', 'max:1000'],
        ]);

        ChatMessage::create([
            'id_chat'     => $chat->id_chat,
            'id_pengguna' => $admin->id_pengguna, // sesuaikan kalau PK model admin beda
            'sender_role' => $currentRole,
            'isi_pesan'   => $validated['message'],
        ]);

        return redirect()
            ->route('admin.chats.show', ['chat' => $chat->id_chat])
            ->with('success', 'Pesan terkirim.');
    }

    /**
     * Buka chat dari kartu pesanan di panel kasir.
     */
    public function orderChat(int $orderId)
    {
        $order = Pesanan::with('pengguna')->findOrFail($orderId);

        $chat = Chat::firstOrCreate(
            ['id_pesanan' => $order->id_pesanan, 'context_type' => 'order'],
            ['title' => 'Pesanan #' . $order->id_pesanan]
        );

        return redirect()->route('admin.chats.show', ['chat' => $chat->id_chat]);
    }
}
