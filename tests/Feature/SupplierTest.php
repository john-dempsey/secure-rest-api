<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Database\Seeders\SupplierSeeder;

class SupplierTest extends TestCase
{
    // Create the database and run the migrations in each test
    use RefreshDatabase; 

    private $token;

    protected function setUp(): void
    {
        parent::setUp();
        // Run the seeder SupplierSeeder
        $this->seed(SupplierSeeder::class); 

        $response = $this->postJson('/api/register', [
            'name' => 'Daire Bloggs',
            'email' => 'daire@bloggs.com',
            'password' => 'mysecret',
            'c_password' => 'mysecret'
        ]);
        
        $this->token = $response->json('data.token');
    }

    public function test_suppliers_created(): void
    {
        $this->assertDatabaseCount('suppliers', 10);
    }

    public function test_supplier_index(): void
    {
        $response = $this->withHeader('Authorization', 'Bearer ' . $this->token)
                         ->getJson(route('suppliers.index'));

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'success',
            'message',
            'data' => [
                '*' => [
                    'id',
                    'name',
                    'address',
                    'phone',
                    'email',
                    'created_at',
                    'updated_at',
                ]
            ]
        ]);
        $success = $response->json('success');
        $message = $response->json('message');
        $suppliers = $response->json('data');

        $this->assertEquals($success, true);
        $this->assertEquals($message, 'Suppliers retrieved successfully.');
        $this->assertCount(10, $suppliers);
    }

    public function test_supplier_show(): void
    {
        $response = $this->withHeader('Authorization', 'Bearer ' . $this->token)
                         ->getJson(route('suppliers.show', 1));
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'success',
            'message',
            'data' => [
                'id',
                'name',
                'address',
                'phone',
                'email',
                'created_at',
                'updated_at',
            ]
        ]);

        $success = $response->json('success');
        $message = $response->json('message');

        $this->assertEquals($success, true);
        $this->assertEquals($message, 'Supplier retrieved successfully.');
    }

    public function test_supplier_store(): void
    {
        $response = $this->withHeader('Authorization', 'Bearer ' . $this->token)
                         ->postJson(route('suppliers.store'), [
            'name' => 'Bloggs Supplier',
            'address' => '11 Main Street',
            'phone' => '0123456789',
            'email' => 'info@bloggs.com'
        ]);

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'success',
            'message',
            'data' => [
                'id',
                'name',
                'address',
                'phone',
                'email',
                'created_at',
                'updated_at',
            ]
        ]);

        $success = $response->json('success');
        $message = $response->json('message');
        $name = $response->json('data.name');
        $address = $response->json('data.address');
        $phone = $response->json('data.phone');
        $email = $response->json('data.email');

        $this->assertEquals($success, true);
        $this->assertEquals($message, 'Supplier created successfully.');
        $this->assertEquals($name, 'Bloggs Supplier');
        $this->assertEquals($address, '11 Main Street');
        $this->assertEquals($phone, '0123456789');
        $this->assertEquals($email, 'info@bloggs.com');
    }

    public function test_supplier_update(): void
    {
        $response = $this->withHeader('Authorization', 'Bearer ' . $this->token)
                         ->postJson(route('suppliers.store'), [
            'name' => 'Bloggs Suppliers',
            'address' => '11 Main Street',
            'phone' => '0123456789',
            'email' => 'info@bloggs.com'
        ]);
        $id = $response->json('data.id');

        $response = $this->withHeader('Authorization', 'Bearer ' . $this->token)
                         ->putJson(route('suppliers.update', $id), [
            'name' => 'Bloggs Suppliers Ltd',
            'address' => '12 Main Street',
            'phone' => '0987654321',
            'email' => 'orders@bloggs.com'
        ]);

        
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'success',
            'message',
            'data' => [
                'id',
                'name',
                'address',
                'phone',
                'email',
                'created_at',
                'updated_at',
            ]
        ]);

        $success = $response->json('success');
        $message = $response->json('message');
        $name = $response->json('data.name');
        $address = $response->json('data.address');
        $phone = $response->json('data.phone');
        $email = $response->json('data.email');

        $this->assertEquals($success, true);
        $this->assertEquals($message, 'Supplier updated successfully.');
        $this->assertEquals($name, 'Bloggs Suppliers Ltd');
        $this->assertEquals($address, '12 Main Street');
        $this->assertEquals($phone, '0987654321');
        $this->assertEquals($email, 'orders@bloggs.com');
    }

    public function test_supplier_delete(): void
    {
        $response = $this->withHeader('Authorization', 'Bearer ' . $this->token)
                         ->postJson(route('suppliers.store'), [
            'name' => 'Bloggs Suppliers',
            'address' => '11 Main Street',
            'phone' => '0123456789',
            'email' => 'info@bloggs.com'
        ]);
        $id = $response->json('data.id');

        $response = $this->withHeader('Authorization', 'Bearer ' . $this->token)
                         ->deleteJson(route('suppliers.destroy', $id));
        
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
        $this->assertEquals($message, 'Supplier deleted successfully.');
        $this->assertEmpty($data);

        $this->assertDatabaseMissing('suppliers', [
            'id' => $id,
        ]);
    }
}
