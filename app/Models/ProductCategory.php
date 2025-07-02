<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductCategory extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'slug',
        'parent_id',
        'sort_order',
        'image',
    ];
    public function parent()
    {
        return $this->belongsTo(ProductCategory::class, 'parent_id');
    }

    /**
     * Quan hệ với các danh mục con.
     */
    public function children()
    {
        return $this->hasMany(ProductCategory::class, 'parent_id');
    }
}
