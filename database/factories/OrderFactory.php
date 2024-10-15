<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

use App\Models\Customer;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Order>
 */
class OrderFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $customer_ids = Customer::pluck('id');
        $customer_id = $this->faker->randomElement($customer_ids);

        $status = $this->faker->randomElement(['received', 'confirmed', 'cancelled', 'shipped', 'paid']);
        $dates = [];
        for ($i = 0; $i < 4; $i++) {
            $dates[] = $this->faker->dateTimeThisYear();
        }
        sort($dates);
        $order_dates = [];
        if ($status === 'received') {
            $order_dates['confirmed_date'] = null;
            $order_dates['cancelled_date'] = null;
            $order_dates['shipped_date'] = null;
            $order_dates['payment_date'] = null;
        } elseif ($status === 'confirmed') {
            $order_dates['confirmed_date'] = $dates[0];
            $order_dates['cancelled_date'] = null;
            $order_dates['shipped_date'] = null;
            $order_dates['payment_date'] = null;
        } elseif ($status === 'cancelled') {
            $order_dates['confirmed_date'] = $dates[0];
            $order_dates['cancelled_date'] = $dates[1];
            $order_dates['shipped_date'] = null;
            $order_dates['payment_date'] = null;
        } elseif ($status === 'shipped') {
            $order_dates['confirmed_date'] = $dates[0];
            $order_dates['cancelled_date'] = null;
            $order_dates['shipped_date'] = $dates[2];
            $order_dates['payment_date'] = null;
        } elseif ($status === 'paid') {
            $order_dates['confirmed_date'] = $dates[0];
            $order_dates['cancelled_date'] = null;
            $order_dates['shipped_date'] = $dates[2];
            $order_dates['payment_date'] = $dates[3];
        }
        return [
            'customer_id' => $customer_id,
            'confirmed_date' => $order_dates['confirmed_date'],
            'cancelled_date' => $order_dates['cancelled_date'],
            'shipped_date' => $order_dates['shipped_date'],
            'payment_date' => $order_dates['payment_date'],
            'status' => $status
        ];
    }
}
