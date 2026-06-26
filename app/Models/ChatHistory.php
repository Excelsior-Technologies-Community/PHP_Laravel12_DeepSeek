<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class ChatHistory extends Model
{
    protected $fillable = [
        'user_id',
        'prompt',
        'response',
        'status',
        'model_used',
        'tokens_used',
        'share_token',
    ];

    protected $casts = [
        'tokens_used' => 'integer',
    ];

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeToday($query)
    {
        return $query->whereDate('created_at', today());
    }

    public function generateShareToken(): string
    {
        $token = Str::random(32);
        $this->update(['share_token' => $token]);
        return $token;
    }
}