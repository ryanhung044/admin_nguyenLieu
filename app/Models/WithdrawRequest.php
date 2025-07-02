<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WithdrawRequest extends Model
{
    use HasFactory;
    protected $fillable = ['user_id', 'amount', 'note', 'status','image'];

    public function user() {
        return $this->belongsTo(User::class);
    }
    public function bankAccount()
    {
        return $this->hasOne(BankAccount::class, 'user_id', 'user_id');
    }
    
}
