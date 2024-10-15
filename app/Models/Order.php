<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_id',
        'confirmed_date',
        'cancelled_date',
        'shipped_date',
        'payment_date',
        'status',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function products()
    {
        return $this->belongsToMany(Product::class)->withPivot('quantity', 'price')->withTimestamps();
    }

    public function totalAmount()
    {
        $total = $this->products->sum(function ($product) {
            return $product->pivot->quantity * $product->pivot->price;
        });
        return number_format($total, 2);
    }
}
