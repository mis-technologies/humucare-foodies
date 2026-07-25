<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OptionGroup extends Model
{
    protected $guarded = ['id'];

    public function options()
    {
        return $this->hasMany(Option::class)->where('status', 1)->orderBy('sort_order');
    }

    /** every option, including disabled ones — for the admin screens */
    public function allOptions()
    {
        return $this->hasMany(Option::class)->orderBy('sort_order');
    }

    public function products()
    {
        return $this->belongsToMany(Product::class, 'product_option_group');
    }

    public function scopeActive($query)
    {
        return $query->where('status', 1);
    }
}
