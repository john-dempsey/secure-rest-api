<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Exceptions;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

use Tests\TestCase;

use App\Models\Order;
use App\Models\Product;
use App\Models\Role;
use App\Events\OrderProcessed;

class OrderTest extends TestCase
{
    // Create the database and run the migrations in each test
    use RefreshDatabase; 

    private $customerUser;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed();

        $customerRole = Role::where('name', 'customer')->first();

        $this->customerUser = $customerRole->users()->first();
    }

    public function test_order_store_dispatch(): void
    {
        Event::fake();

        $order = Order::factory()->make()->toArray();

        $order["products"] = [];
        foreach(Product::all()->random(3) as $product) {
            $discount = rand(0, 100);
            $order["products"][] = [
                "product_id" => $product->id,
                'quantity' => rand(1, 5),
                'price' => ($discount > 75) ? number_format($product->price * 0.75, 2) : $product->price
            ];
        };
        $response = $this->actingAs($this->customerUser)
                         ->postJson(route('orders.store'), $order);

        $response->assertStatus(200);

        Event::assertDispatched(OrderProcessed::class);
    }
}