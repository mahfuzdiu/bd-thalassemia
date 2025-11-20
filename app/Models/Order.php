<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = ['order_number', 'user_id', 'total', 'status','payment_method', 'shipping_method', 'shipping_cost', 'shipping_address'];

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }
}
