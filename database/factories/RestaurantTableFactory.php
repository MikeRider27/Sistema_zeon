<?php

namespace Database\Factories;

use App\Enums\TableStatus;
use App\Models\RestaurantTable;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<RestaurantTable>
 */
class RestaurantTableFactory extends Factory
{
    public function definition(): array
    {
        return [
            'number' => fake()->unique()->numberBetween(1, 100),
            'capacity' => fake()->randomElement([2, 4, 6, 8]),
            'status' => TableStatus::Libre,
        ];
    }
}
