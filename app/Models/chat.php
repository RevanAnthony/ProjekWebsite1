<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Chat extends Model
{
    protected $table = 'chats';
    protected $primaryKey = 'id_chat';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'id_pesanan',
        'context_type',
        'title',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Pesanan::class, 'id_pesanan', 'id_pesanan');
    }

    public function messages(): HasMany
    {
        return $this->hasMany(ChatMessage::class, 'id_chat', 'id_chat');
    }
}
