<?php

namespace Database\Factories;

use App\Enums\OrderStatus;
use App\Models\Order;
use App\Models\RestaurantTable;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Order>
 */
class OrderFactory extends Factory
{
    public function definition(): array
    {
        return [
            'restaurant_table_id' => RestaurantTable::factory(),
            'user_id' => User::factory(),
            'status' => OrderStatus::Abierto,
            'total' => 0,
        ];
    }
}
