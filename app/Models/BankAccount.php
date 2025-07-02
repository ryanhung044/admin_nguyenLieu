<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BankAccount extends Model
{
    use HasFactory;
    protected $fillable = ['user_id', 'account_name', 'account_number', 'bank_name','image'];

    public function user() {
        return $this->belongsTo(User::class);
    }

}
