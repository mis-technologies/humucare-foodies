<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model {
    use HasFactory;
    protected $guarded = [];

    public function productGallery() {
        return $this->hasMany(ProductGallery::class, 'product_id');
    }
    public function category() {
        return $this->belongsTo(Category::class, 'category_id');
    }

    public function subcategory() {
        return $this->belongsTo(SubCategory::class, 'subcategory_id');
    }

    public function brand() {
        return $this->belongsTo(Brand::class, 'brand_id');
    }

    public function orderDetails() {
        return $this->hasMany(OrderDetail::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class, 'product_id');
    }

    public function scopeActive() {

        return $this->where('status',1)
            ->whereHas('category', function ($category) {
                $category->where('status', 1);
            })->whereHas('subcategory', function ($subcategory) {
                $subcategory->where('status', 1);
            })->whereHas('brand', function ($brand) {
                $brand->where('status', 1);
            });
    }
}
