<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    use HasFactory;
    protected $fillable = [
        'order_id',
        'product_id',
        'product_name',
        'thumbnail',
        'price',
        'quantity',
        'referrer_id',
        'commission_amount',
    ];

    // Một item thuộc về một đơn hàng
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    // Optional: nếu vẫn muốn truy ngược về sản phẩm
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

}
