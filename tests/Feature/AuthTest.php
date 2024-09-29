<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Database\Seeders\UserSeeder;
use Tests\TestCase;

class AuthTest extends TestCase
{
    // Create the database and run the migrations in each test
    use RefreshDatabase; 

    protected function setUp(): void
    {
        parent::setUp();
        // Run the seeder UserSeeder
        $this->seed(UserSeeder::class); 
    }
    
    public function test_users_created(): void
    {
        $this->assertDatabaseCount('users', 10);
    }

    public function test_user_registered(): void
    {
        $this->assertDatabaseCount('users', 10);
        $response = $this->postJson('/api/register', [
            'name' => 'Daire Bloggs',
            'email' => 'daire@bloggs.com',
            'password' => 'mysecret',
            'c_password' => 'mysecret'
        ]);

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => [
                'token', 'name'
            ],
            'message',
            'success'
        ]);
        $success = $response->json('success');
        $message = $response->json('message');
        $token = $response->json('data.token');
        $name = $response->json('data.name');
        
        $this->assertEquals($success, true);
        $this->assertEquals($message, 'User register successfully.');
        $this->assertEquals($name, 'Daire Bloggs');
        $this->assertNotNull($token);

        $this->assertDatabaseCount('users', 11);
        $this->assertDatabaseHas('users', [
            'name' => 'Daire Bloggs',
            'email' => 'daire@bloggs.com'
        ]);
    }

    public function test_user_logged_in(): void
    {
        $response = $this->postJson('/api/register', [
            'name' => 'Daire Bloggs',
            'email' => 'daire@bloggs.com',
            'password' => 'mysecret',
            'c_password' => 'mysecret'
        ]);
        $response = $this->postJson('/api/login', [
            'email' => 'daire@bloggs.com',
            'password' => 'mysecret'
        ]);

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => [
                'token', 'name'
            ],
            'message',
            'success'
        ]);
        $success = $response->json('success');
        $message = $response->json('message');
        $token = $response->json('data.token');
        $name = $response->json('data.name');

        $this->assertEquals($success, true);
        $this->assertEquals($message, 'User login successfully.');
        $this->assertEquals($name, 'Daire Bloggs');
        $this->assertNotNull($token);
    }

    public function test_user_info(): void
    {
        $response = $this->postJson('/api/register', [
            'name' => 'Daire Bloggs',
            'email' => 'daire@bloggs.com',
            'password' => 'mysecret',
            'c_password' => 'mysecret'
        ]);

        $token = $response->json('data.token');
        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
                         ->getJson('/api/user');

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'id',
            'name',
            'email',
            'email_verified_at',
            'created_at',
            'updated_at'
        ]);

        $name = $response->json('name');
        $email = $response->json('email');
        $this->assertEquals($name, 'Daire Bloggs');
        $this->assertEquals($email, 'daire@bloggs.com');
    }
}
