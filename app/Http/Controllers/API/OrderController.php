<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

use App\Http\Controllers\API\BaseController as BaseController;
use App\Http\Requests\OrderRequest;
use App\Http\Resources\OrderResource;
use App\Models\Order;
use App\Models\Product;
use App\Events\OrderProcessedEvent;

class OrderController extends BaseController
{
    public function index()
    {
        if (Gate::denies('viewAny', Order::class)) {
            return $this->sendError(
                'Permission denied.', 
                ['You are not authorized to perform this action.'],
                403
            );
        }
        $orders = Order::all();

        return $this->sendResponse(
            OrderResource::collection($orders), 
            'Orders retrieved successfully.'
        );
    }

    public function store(OrderRequest $request)
    {
        if (Gate::denies('create', Order::class)) {
            return $this->sendError(
                'Permission denied.', 
                ['You are not authorized to perform this action.'],
                403
            );
        }

        try {
            DB::beginTransaction();

            $validatedData = $request->validated();
            $order = Order::create([
                'customer_id' => $validatedData['customer_id'],
                'confirmed_date' => null,
                'cancelled_date' => null,
                'shipped_date' => null,
                'payment_date' => null,
                'status' => 'received',
            ]);
            foreach ($validatedData['products'] as $productData) {
                $product = Product::findOrFail($productData['product_id']);
                $order->products()->attach($product->id, [
                    'quantity' => $productData['quantity'],
                    'price' => $productData['price'],
                ]);
            }

            DB::commit();

            OrderProcessedEvent::dispatch($order, 'received');

            return $this->sendResponse(
                new OrderResource($order), 'Order created successfully.'
            );
        }
        catch (\Exception $e) {
            DB::rollBack();

            return $this->sendError(
                'Database error.', 
                ['Failed to create the order.'],
                500
            );
        }
    }

    public function show(Order $order)
    {
        if (Gate::denies('view', $order)) {
            return $this->sendError(
                'Permission denied.', 
                ['You are not authorized to perform this action.'],
                403
            );
        }
        return $this->sendResponse(
            new OrderResource($order), 'Order retrieved successfully.'
        );
    }

    public function update(OrderRequest $request, Order $order)
    {
        if (Gate::denies('update', $order)) {
            return $this->sendError(
                'Permission denied.', 
                ['You are not authorized to perform this action.'],
                403
            );
        }

        try {
            DB::beginTransaction();

            $validatedData = $request->validated();
            if (isset($validatedData['customer_id'])) {
                if ($order->status !== 'received') {
                    return $this->sendError(
                        'Invalid action.', 
                        ['You cannot update the customer for this order.'],
                        422
                    );
                }
                $order->customer_id = $validatedData['customer_id'];
            }
            if (isset($validatedData['status'])) {
                if ($validatedData['status'] === 'confirmed' && $order->status === 'received') {
                    $order->status = $validatedData['status'];
                    $order->confirmed_date = now();
                } 
                elseif ($validatedData['status'] === 'cancelled' && $order->status === 'received') {
                    $order->status = $validatedData['status'];
                    $order->cancelled_date = now();
                } 
                elseif ($validatedData['status'] === 'shipped' && $order->status === 'confirmed') {
                    $order->status = $validatedData['status'];
                    $order->shipped_date = now();
                } elseif ($validatedData['status'] === 'paid' && $order->status === 'shipped') {
                    $order->status = $validatedData['status'];
                    $order->payment_date = now();
                }
                else {
                    return $this->sendError(
                        'Invalid action.', 
                        ['You cannot update the order status to '. $validatedData['status'] . '.'],
                        422
                    );
                }
            }
            $order->save();
            
            if (isset($validatedData['products'])) {
                if ($order->status !== 'received') {
                    return $this->sendError(
                        'Invalid action.', 
                        ['You cannot update the products for this order.'],
                        422
                    );
                }
                $order->products()->detach();
                foreach ($validatedData['products'] as $productData) {
                    $product = Product::findOrFail($productData['product_id']);
                    $order->products()->attach($product->id, [
                        'quantity' => $productData['quantity'],
                        'price' => $productData['price'],
                    ]);
                }
            }

            DB::commit();

            OrderProcessedEvent::dispatch($order, isset($validatedData['status']) ? $validatedData['status'] : 'updated');

            return $this->sendResponse(
                new OrderResource($order), 'Order updated successfully.'
            );
        }
        catch (\Exception $e) {
            DB::rollBack();

            return $this->sendError(
                'Database error.', 
                ['Failed to update the order.', $e->getMessage()],
                500
            );
        }
    }

    public function destroy(Order $order)
    {
        if (Gate::denies('delete', $order)) {
            return $this->sendError(
                'Permission denied.', 
                ['You are not authorized to perform this action.'],
                403
            );
        }

        try {
            DB::beginTransaction();
            $order->products()->detach();
            $order->delete();
            DB::commit();

            OrderProcessedEvent::dispatch($order, 'deleted');

            return $this->sendResponse(
                [], 'Order deleted successfully.'
            );
        }
        catch (\Exception $e) {
            DB::rollBack();

            return $this->sendError(
                'Database error.', 
                ['Failed to delete the order.'],
                500
            );
        }
    }
}
