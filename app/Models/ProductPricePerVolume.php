<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductPricePerVolume extends Model
{
    use HasFactory;

    protected $fillable = [

        'product_id',
        'volume',
        'price'
    ];
}
