<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    /** @use HasFactory<\Database\Factories\ProductFactory> */
    use HasFactory;

    protected $fillable = ['uuid', 'product_sku','name', 'description', 'is_public'];

    public function variants()
    {
        return $this->hasMany(ProductVariant::class);
    }
}
