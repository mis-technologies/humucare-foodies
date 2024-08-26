<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model {
    use HasFactory;
    protected $guarded = [];

    public function subcategories() {
        return $this->hasMany(SubCategory::class, 'category_id')->where('status', 1);
    }
    public function product() {
        return $this->hasMany(Product::class, 'category_id');
    }
    public function scopeActive() {
        return $this->where('status', 1);
    }

}
