<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $this->load('customer', 'products');
        return [
            'id' => $this->id,
            'customer' => new CustomerResource($this->customer),
            'confirmed_date' => $this->confirmed_date,
            'cancelled_date' => $this->cancelled_date,
            'shipped_date' => $this->shipped_date,
            'payment_date' => $this->payment_date,
            'total_amount' => $this->totalAmount(),
            'status' => $this->status,
            'products' => $this->products->map(function ($product) {
                return [
                    'product_id' => $product->id,
                    'price' => $product->pivot->price,
                    'quantity' => $product->pivot->quantity,
                ];
            }),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
