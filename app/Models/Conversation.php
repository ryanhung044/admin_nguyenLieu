<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Conversation extends Model
{
    use HasFactory;
    protected $fillable = [
        'platform',
        'external_id',
        'user_id',
        'last_message',
        'last_time'
    ];

    public function messages()
    {
        return $this->hasMany(Message::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'external_id', 'zalo_id');
    }

    public function unreadMessagesCount()
    {
        // Giả sử 'read_at' lưu thời điểm đọc của admin
        return $this->messages()->whereNull('admin_read_at')->count();
    }
}
