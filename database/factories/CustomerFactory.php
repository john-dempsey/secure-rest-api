<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Customer>
 */
class CustomerFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = fake('en_GB')->unique()->name();
        return [
            'name' => $name,
            'address' => fake('en_GB')->address(),
            'phone' => fake('en_GB')->phoneNumber(),
            'email' => Str::slug($name) . '@' . fake()->safeEmailDomain()
        ];
    }
}
