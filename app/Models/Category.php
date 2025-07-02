<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;
    protected $fillable = [
        'title', 'slug', 'parent_category_id', 'description'
    ];

    // Quan hệ cha - con (một chuyên mục có thể có nhiều chuyên mục con)
    public function children()
    {
        return $this->hasMany(Category::class, 'parent_category_id');
    }

    // Quan hệ với chuyên mục cha (nếu có)
    public function parent()
    {
        return $this->belongsTo(Category::class, 'parent_category_id');
    }

}
