<?php

namespace App\Http\Controllers;

use App\Models\ChatMessage;
use App\Models\Pesanan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class NotificationController extends Controller
{
    /**
     * GET /notifications/json
     * Return daftar notifikasi untuk user (pelanggan).
     * - Chat: dihitung dari chat_messages yang belum dibaca.
     * - Update pesanan: dihitung dari updated_at pesanan (dibanding notif_seen_at user).
     */
    public function json(Request $request)
    {
        $user = Auth::user();
        $role = $user->role ?? 'customer';

        $limit = (int) $request->query('limit', 20);
        $limit = max(5, min($limit, 50));

        $seenAt = null;
        if (Schema::hasTable('pengguna') && Schema::hasColumn('pengguna', 'notif_seen_at')) {
            $seenAt = $user->notif_seen_at ? \Illuminate\Support\Carbon::parse($user->notif_seen_at) : null;
        }

        $items = [];

        // =========================
        // 1) NOTIF UPDATE PESANAN
        // =========================
        $activeOrders = Pesanan::where('id_pengguna', $user->id_pengguna)
            ->whereNotIn('status_pesanan', ['selesai', 'dibatalkan'])
            ->orderByDesc('updated_at')
            ->orderByDesc('created_at')
            ->limit(10)
            ->get();

        $historyOrders = Pesanan::where('id_pengguna', $user->id_pengguna)
            ->whereIn('status_pesanan', ['selesai', 'dibatalkan'])
            ->orderByDesc('updated_at')
            ->orderByDesc('created_at')
            ->limit(5)
            ->get();

        $orders = $activeOrders->concat($historyOrders);

        foreach ($orders as $order) {
            $ts = $order->updated_at ?? $order->created_at;
            $tsCarbon = $ts ? \Illuminate\Support\Carbon::parse($ts) : null;

            $readAt = null;
            if ($seenAt && $tsCarbon && $tsCarbon->lte($seenAt)) {
                $readAt = $seenAt;
            }

            $status = (string) ($order->status_pesanan ?? '');

            $items[] = [
                'id'           => 'order-' . $order->id_pesanan,
                'type'         => 'order',
                'order_id'     => (int) $order->id_pesanan,
                'status_label' => $status,
                'kind'         => $this->kindFromStatus($status),
                'title'        => 'Update Pesanan #' . $order->id_pesanan,
                'sub'          => $this->humanStatus($status),
                'time'         => $tsCarbon ? $tsCarbon->format('H.i') : '',
                'read_at'      => $readAt ? $readAt->toISOString() : null,
                'href'         => route('orders.show', ['id' => $order->id_pesanan]),
                '_sort_ts'     => $tsCarbon ? $tsCarbon->timestamp : 0,
            ];
        }

        // =========================
        // 2) NOTIF CHAT (UNREAD) - 1 item per chat
        // =========================
        $chatAgg = ChatMessage::query()
            ->selectRaw('chat_messages.id_chat, chats.id_pesanan as order_id, MAX(chat_messages.created_at) as last_at, COUNT(*) as unread_count')
            ->join('chats', 'chat_messages.id_chat', '=', 'chats.id_chat')
            ->join('pesanan', 'chats.id_pesanan', '=', 'pesanan.id_pesanan')
            ->where('pesanan.id_pengguna', $user->id_pengguna)
            ->where('chat_messages.is_read', false)
            ->where(function ($q) use ($role) {
                $q->whereNull('chat_messages.sender_role')
                  ->orWhere('chat_messages.sender_role', '!=', $role);
            })
            ->groupBy('chat_messages.id_chat', 'chats.id_pesanan')
            ->orderByDesc('last_at')
            ->limit(20)
            ->get();

        foreach ($chatAgg as $row) {
            $lastMsg = ChatMessage::where('id_chat', $row->id_chat)
                ->where(function ($q) use ($role) {
                    $q->whereNull('sender_role')->orWhere('sender_role', '!=', $role);
                })
                ->orderByDesc('created_at')
                ->first();

            $preview = $lastMsg ? Str::limit((string) $lastMsg->isi_pesan, 70) : 'Ada pesan baru dari admin.';
            $unread  = (int) ($row->unread_count ?? 0);
            $lastAt  = $row->last_at ? \Illuminate\Support\Carbon::parse($row->last_at) : null;

            $items[] = [
                'id'       => 'chat-' . $row->id_chat,
                'type'     => 'chat',
                'order_id' => (int) $row->order_id,
                'kind'     => 'Message',
                'title'    => 'Pesan dari Admin',
                'sub'      => $unread > 1 ? "({$unread} pesan) {$preview}" : $preview,
                'time'     => $lastAt ? $lastAt->format('H.i') : '',
                'read_at'  => null,
                'href'     => route('orders.chat', ['id' => (int) $row->order_id]),
                '_sort_ts' => $lastAt ? $lastAt->timestamp : 0,
            ];
        }

        // =========================
        // 3) Sorting (newest first)
        // =========================
        usort($items, function ($a, $b) {
            return ((int) ($b['_sort_ts'] ?? 0)) <=> ((int) ($a['_sort_ts'] ?? 0));
        });

        // hapus field internal _sort_ts supaya response bersih
        $items = array_map(function ($it) {
            unset($it['_sort_ts']);
            return $it;
        }, $items);

        return response()->json([
            'data' => $items,
        ]);
    }

    /**
     * POST /notifications/mark-read
     * Body: { id: "chat-12" | "order-23" | "all" }
     */
    public function markRead(Request $request)
    {
        $user = Auth::user();
        $role = $user->role ?? 'customer';

        $request->validate([
            'id' => ['required', 'string', 'max:80'],
        ]);

        $id = $request->input('id');

        // 1) Mark read chat per chat
        if (Str::startsWith($id, 'chat-')) {
            $chatId = (int) Str::after($id, 'chat-');

            ChatMessage::where('id_chat', $chatId)
                ->where('is_read', false)
                ->where(function ($q) use ($role) {
                    $q->whereNull('sender_role')
                      ->orWhere('sender_role', '!=', $role);
                })
                ->update([
                    'is_read' => true,
                    'read_at' => now(),
                ]);

            return response()->json(['ok' => true]);
        }

        // 2) Mark read order notifications (global) via notif_seen_at
        if (Str::startsWith($id, 'order-') || $id === 'all' || $id === '__open__') {
            if (Schema::hasTable('pengguna') && Schema::hasColumn('pengguna', 'notif_seen_at')) {
                $user->forceFill(['notif_seen_at' => now()])->save();
            }
            return response()->json(['ok' => true]);
        }

        // 3) Fallback: kalau id numeric, anggap id_pesan
        if (ctype_digit($id)) {
            ChatMessage::where('id_pesan', (int) $id)
                ->where('is_read', false)
                ->where(function ($q) use ($role) {
                    $q->whereNull('sender_role')->orWhere('sender_role', '!=', $role);
                })
                ->update([
                    'is_read' => true,
                    'read_at' => now(),
                ]);
        }

        return response()->json(['ok' => true]);
    }

    private function humanStatus(string $status): string
    {
        $s = strtolower(trim($status));

        return match (true) {
            str_contains($s, 'menunggu') || str_contains($s, 'pending') || str_contains($s, 'konfirmasi')
                => 'Menunggu konfirmasi pembayaran.',

            str_contains($s, 'dapur') || str_contains($s, 'masak') || str_contains($s, 'cook')
                => 'Pesanan sedang disiapkan di dapur.',

            str_contains($s, 'diantar') || str_contains($s, 'kirim') || str_contains($s, 'delivery')
                => 'Driver sedang mengantar pesananmu.',

            str_contains($s, 'selesai') || str_contains($s, 'done')
                => 'Pesanan sudah selesai.',

            str_contains($s, 'batal') || str_contains($s, 'cancel')
                => 'Pesanan dibatalkan.',

            default
                => $status !== '' ? ('Status: ' . $status) : 'Update pesanan.',
        };
    }

    private function kindFromStatus(string $status): string
    {
        $s = strtolower(trim($status));

        return match (true) {
            str_contains($s, 'batal') || str_contains($s, 'cancel')  => 'Canceled',
            str_contains($s, 'selesai') || str_contains($s, 'done')  => 'Completed',
            str_contains($s, 'diantar') || str_contains($s, 'kirim') || str_contains($s, 'delivery') => 'Active Order',
            default => 'Active Order',
        };
    }
}
