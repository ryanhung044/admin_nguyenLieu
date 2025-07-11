<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CommissionUser extends Model
{
    use HasFactory;
    protected $fillable=[
        'user_id',
        'order_item_id',
        'level',
        'amount',
        'status',
    ];
    
}
