<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Option extends Model
{
    protected $guarded = ['id'];

    public function optionGroup()
    {
        return $this->belongsTo(OptionGroup::class);
    }

    public function scopeActive($query)
    {
        return $query->where('status', 1);
    }
}
