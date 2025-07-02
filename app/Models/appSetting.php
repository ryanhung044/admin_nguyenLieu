<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class appSetting extends Model
{
    use HasFactory;
    protected $fillable = [
        'app_name',
        'address',
        'latitude',
        'longitude',
        'phone',
        'email',
        'default_color',
        'description',
        'logo_path',
        'banner_path',
        'favicon_path',
        'donated',
    ];
}
