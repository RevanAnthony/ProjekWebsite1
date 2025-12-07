<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ChatMessage extends Model
{
    protected $table = 'chat_messages';
    protected $primaryKey = 'id_pesan';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'id_chat',
        'id_pengguna',
        'sender_role',
        'isi_pesan',
        'is_read',
        'read_at',
    ];

    protected $casts = [
        'is_read' => 'boolean',
        'read_at' => 'datetime',
    ];

    public function chat(): BelongsTo
    {
        return $this->belongsTo(Chat::class, 'id_chat', 'id_chat');
    }

    public function sender(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_pengguna', 'id_pengguna');
    }
}
