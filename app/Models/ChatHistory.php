<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ChatHistory extends Model
{
    protected $fillable = [
        'user_id',
        'prompt',
        'response',
        'status',
        'model_used',
        'tokens_used'
    ];

    protected $casts = [
        'tokens_used' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Scope for completed chats only
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    // Scope for today's chats
    public function scopeToday($query)
    {
        return $query->whereDate('created_at', today());
    }
}