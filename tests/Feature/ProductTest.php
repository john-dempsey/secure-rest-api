<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Exceptions;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

use Tests\TestCase;

use App\Models\Product;
use App\Models\Role;

class ProductTest extends TestCase
{
    // Create the database and run the migrations in each test
    use RefreshDatabase; 

    private $superUser;
    private $customerUser;
    private $supplierUser;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed();

        $superRole = Role::where('name', 'superuser')->first();
        $customerRole = Role::where('name', 'customer')->first();
        $supplierRole = Role::where('name', 'supplier')->first();

        $this->superUser = $superRole->users()->first();
        $this->customerUser = $customerRole->users()->first();
        $this->supplierUser = $supplierRole->users()->first();
    }

    public function test_product_index(): void
    {
        $response = $this->actingAs($this->supplierUser)
                         ->getJson(route('products.index'));

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'success',
            'message',
            'data' => [
                '*' => [
                    'id',
                    'name',
                    'description',
                    'price',
                    'supplier_id',
                    'created_at',
                    'updated_at',
                ]
            ]
        ]);
        $success = $response->json('success');
        $message = $response->json('message');
        $products = $response->json('data');

        $this->assertEquals($success, true);
        $this->assertEquals($message, 'Products retrieved successfully.');
        $this->assertCount(100, $products);
    }

    public function test_product_index_authorisation_fail(): void
    {
        $response = $this->actingAs($this->customerUser)
                         ->getJson(route('products.index'));

        $response->assertStatus(403);
        $response->assertJsonStructure([
            'success',
            'message',
            'data'
        ]);
        $success = $response->json('success');
        $message = $response->json('message');

        $this->assertEquals($success, false);
        $this->assertEquals($message, 'Permission denied.');
    }

    public function test_product_index_authorisation_superuser(): void
    {
        $response = $this->actingAs($this->superUser)
                         ->getJson(route('products.index'));

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'success',
            'message',
            'data' => [
                '*' => [
                    'id',
                    'name',
                    'description',
                    'price',
                    'supplier_id',
                    'created_at',
                    'updated_at',
                ]
            ]
        ]);
        $success = $response->json('success');
        $message = $response->json('message');
        $products = $response->json('data');

        $this->assertEquals($success, true);
        $this->assertEquals($message, 'Products retrieved successfully.');
        $this->assertCount(100, $products);
    }

    public function test_product_show(): void
    {
        $product = Product::factory()->create();
        $response = $this->actingAs($this->supplierUser)
                         ->getJson(route('products.show', $product->id));
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'success',
            'message',
            'data' => [
                'id',
                'name',
                'description',
                'price',
                'supplier_id',
                'created_at',
                'updated_at',
            ]
        ]);

        $success = $response->json('success');
        $message = $response->json('message');
        $name = $response->json('data.name');
        $description = $response->json('data.description');
        $price = $response->json('data.price');
        $supplier_id = $response->json('data.supplier_id');

        $this->assertEquals($success, true);
        $this->assertEquals($message, 'Product retrieved successfully.');
        $this->assertEquals($name, $product->name);
        $this->assertEquals($description, $product->description);
        $this->assertEquals($price, $product->price);
        $this->assertEquals($supplier_id, $product->supplier_id);

        $this->assertDatabaseHas('products', [
            'id' => $product->id
        ]);
    }

    public function test_product_show_not_found_error(): void
    {
        $missing_productid = mt_rand();
        while(Product::where('id', $missing_productid)->count() > 0) {
                $missing_productid = mt_rand();
        }
        
        $response = $this->actingAs($this->supplierUser)
                         ->getJson(route('products.show', $missing_productid));

        $response->assertStatus(404);
        $response->assertJsonStructure([
            'message',
            'success'
        ]);

        $success = $response->json('success');
        $message = $response->json('message');
        
        $this->assertEquals($success, false);
        $this->assertEquals($message, 'Product not found.');

        $this->assertDatabaseMissing('products', [
            'id' => $missing_productid
        ]);
    }

    public function test_product_store(): void
    {
        $product = Product::factory()->make();
        $response = $this->actingAs($this->supplierUser)
                         ->postJson(route('products.store'), $product->toArray());

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'success',
            'message',
            'data' => [
                'id',
                'name',
                'description',
                'price',
                'supplier_id',
                'created_at',
                'updated_at',
            ]
        ]);

        $success = $response->json('success');
        $message = $response->json('message');
        $name = $response->json('data.name');
        $description = $response->json('data.description');
        $price = $response->json('data.price');
        $supplier_id = $response->json('data.supplier_id');

        $this->assertEquals($success, true);
        $this->assertEquals($message, 'Product created successfully.');
        $this->assertEquals($name, $product->name);
        $this->assertEquals($description, $product->description);
        $this->assertEquals($price, $product->price);
        $this->assertEquals($supplier_id, $product->supplier_id);

        $this->assertDatabaseHas('products', [
            'name' => $product->name
        ]);
    }

    public function test_product_store_validation_error(): void
    {
        $product = Product::factory()->make();
        $product->name = '';
        $response = $this->actingAs($this->supplierUser)
                         ->postJson(route('products.store'), $product->toArray());

        $response->assertStatus(422);
        $response->assertJsonStructure([
            'data',
            'message',
            'success'
        ]);

        $success = $response->json('success');
        $message = $response->json('message');
        
        $this->assertEquals($success, false);
        $this->assertEquals($message, 'Validation Error.');

        $this->assertDatabaseMissing('products', [
            'name' => $product->name
        ]);
    }

    public function test_product_update(): void
    {
        $product = Product::factory()->create();
        $updatedProduct = Product::factory()->make();
        $response = $this->actingAs($this->supplierUser)
                         ->putJson(route('products.update', $product->id), $updatedProduct->toArray());

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'success',
            'message',
            'data' => [
                'id',
                'name',
                'description',
                'price',
                'supplier_id',
                'created_at',
                'updated_at',
            ]
        ]);

        $success = $response->json('success');
        $message = $response->json('message');
        $name = $response->json('data.name');
        $description = $response->json('data.description');
        $price = $response->json('data.price');
        $supplier_id = $response->json('data.supplier_id');

        $this->assertEquals($success, true);
        $this->assertEquals($message, 'Product updated successfully.');
        $this->assertEquals($name, $updatedProduct->name);
        $this->assertEquals($description, $updatedProduct->description);
        $this->assertEquals($price, $updatedProduct->price);
        $this->assertEquals($supplier_id, $updatedProduct->supplier_id);

        $this->assertDatabaseHas('products', [
            'name' => $updatedProduct->name
        ]);
    }

    public function test_product_update_validation_error(): void
    {
        $product = Product::factory()->create();
        $updatedProduct = Product::factory()->make();
        $updatedProduct->name = '';
        $response = $this->actingAs($this->supplierUser)
                         ->putJson(route('products.update', $product->id), $updatedProduct->toArray());

        $response->assertStatus(422);
        $response->assertJsonStructure([
            'data',
            'message',
            'success'
        ]);

        $success = $response->json('success');
        $message = $response->json('message');
        
        $this->assertEquals($success, false);
        $this->assertEquals($message, 'Validation Error.');

        $this->assertDatabaseMissing('products', [
            'name' => $updatedProduct->name
        ]);
        $this->assertDatabaseHas('products', [
            'name' => $product->name
        ]);
    }

    public function test_product_update_not_found_error(): void
    {
        $updatedProduct = Product::factory()->make();
        $missing_productid = mt_rand();
        while(Product::where('id', $missing_productid)->count() > 0) {
                $missing_productid = mt_rand();
        }
        $response = $this->actingAs($this->supplierUser)
                         ->putJson(route('products.update', $missing_productid), $updatedProduct->toArray());

        $response->assertStatus(404);
        $response->assertJsonStructure([
            'message',
            'success'
        ]);

        $success = $response->json('success');
        $message = $response->json('message');
        
        $this->assertEquals($success, false);
        $this->assertEquals($message, 'Product not found.');

        $this->assertDatabaseMissing('products', [
            'id' => $missing_productid
        ]);
    }

    public function test_product_destroy(): void
    {
        $product = Product::factory()->create();
        $response = $this->actingAs($this->supplierUser)
                         ->deleteJson(route('products.destroy', $product->id));
        
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'success',
            'message',
            'data'
        ]);

        $success = $response->json('success');
        $message = $response->json('message');
        $data = $response->json('data');

        $this->assertEquals($success, true);
        $this->assertEquals($message, 'Product deleted successfully.');
        $this->assertEmpty($data);

        $this->assertDatabaseMissing('products', [
            'id' => $product->id,
        ]);
    }

    public function test_product_destroy_not_found_error(): void
    {
        $updatedProduct = Product::factory()->make();
        $missing_productid = mt_rand();
        while(Product::where('id', $missing_productid)->count() > 0) {
                $missing_productid = mt_rand();
        }
        $response = $this->actingAs($this->supplierUser)
                         ->deleteJson(route('products.destroy', $missing_productid));

        $response->assertStatus(404);
        $response->assertJsonStructure([
            'message',
            'success'
        ]);

        $success = $response->json('success');
        $message = $response->json('message');
        
        $this->assertEquals($success, false);
        $this->assertEquals($message, 'Product not found.');

        $this->assertDatabaseMissing('products', [
            'id' => $missing_productid
        ]);
    }
}
