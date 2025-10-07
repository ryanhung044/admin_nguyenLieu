<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',        // Nếu có đăng nhập
        'name',
        'phone',
        'address',
        'payment_method',
        'referrer_id',
        'total',
        'created_at',
        'status',          // pending, completed, canceled, etc.
        'status_payment',
        'shipping_code',
        'shipping_provider'
    ];

    // Một đơn hàng có nhiều item
    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    // Nếu có user
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function referrer()
    {
        return $this->belongsTo(User::class, 'referrer_id'); // hoặc User::class tùy hệ thống
    }
    

}
