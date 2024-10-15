<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class OrderRequest extends FormRequest
{
    public function rules(): array
    {
        if ($this->isMethod('post')) {
            return $this->createRules();
        }
        return $this->updateRules();
    }

    protected function createRules(): array
    {
        return [
            'customer_id' => 'required|exists:customers,id',
            'products' => 'required|array',
            'products.*.product_id' => 'required|exists:products,id',
            'products.*.quantity' => 'required|integer|min:1',
            'products.*.price' => 'required|numeric|min:0',
        ];
    }

    protected function updateRules(): array
    {
        return [
            'customer_id' => 'sometimes|required|exists:customers,id',
            'products' => 'sometimes|required|array',
            'products.*.product_id' => 'sometimes|required|exists:products,id',
            'products.*.quantity' => 'sometimes|required|integer|min:1',
            'products.*.price' => 'sometimes|required|numeric|min:0',
            'status' => 'sometimes|required|in:created,confirmed,cancelled,shipped,paid',
        ];
    }
}
