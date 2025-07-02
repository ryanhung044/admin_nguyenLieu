<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class article extends Model
{
    use HasFactory;
    protected $fillable = [
        'title',
        'slug',
        'summary',
        'content',
        'category_id',
        'sort_order',
        'image',
        'published_at',
    ];

    protected $dates = ['published_at'];

    // Quan hệ với chuyên mục (category)
    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

}
