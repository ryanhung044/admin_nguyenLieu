<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    use HasFactory;
    protected $fillable = [
        'conversation_id', 'sender_type',
        'message_type', 'message_text', 'message_data', 'sent_at'
    ];

    protected $casts = [
        'message_data' => 'array',
        'sent_at' => 'datetime',
    ];

    public function conversation() {
        return $this->belongsTo(Conversation::class);
    }

}
