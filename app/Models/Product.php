<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'summary',
        'content',
        'thumbnail',
        'images',
        'price',
        'sale_price',
        'slug',
        'sku',
        'category_id',
        'group_id',
        'stock',
        'commission_rate'
    ];

    // Ép kiểu 'images' từ JSON -> array
    protected $casts = [
        'images' => 'array',
    ];

    // Quan hệ với danh mục sản phẩm
    public function category()
    {
        return $this->belongsTo(ProductCategory::class, 'category_id');
    }

    // Quan hệ với nhóm sản phẩm
    public function group()
    {
        return $this->belongsTo(ProductGroup::class, 'group_id');
    }
    public function variants()
    {
        return $this->hasMany(ProductVariant::class);
    }

}
