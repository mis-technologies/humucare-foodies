<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductPricePerMilliliter extends Model
{
    use HasFactory;

    protected $fillable = [
        'price',
        'milliliter',
        'product_id'
    ];

    /**
     * Get the product that owns the ProductPricePerLiter
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id', 'id');
    }
}
