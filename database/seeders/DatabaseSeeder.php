<?php

namespace Database\Seeders;

use App\Enums\OrderItemStatus;
use App\Enums\OrderStatus;
use App\Enums\TableStatus;
use App\Enums\UserRole;
use App\Models\Category;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\RestaurantTable;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $admin = User::factory()->role(UserRole::Admin)->create([
            'name' => 'Administrador',
            'email' => 'admin@zeon.test',
        ]);

        $mesero = User::factory()->role(UserRole::Mesero)->create([
            'name' => 'Mesero Demo',
            'email' => 'mesero@zeon.test',
        ]);

        User::factory()->role(UserRole::Cocina)->create([
            'name' => 'Cocina Demo',
            'email' => 'cocina@zeon.test',
        ]);

        User::factory()->role(UserRole::Caja)->create([
            'name' => 'Caja Demo',
            'email' => 'caja@zeon.test',
        ]);

        $menu = [
            'Entradas' => [
                ['name' => 'Ceviche clásico', 'price' => 9.50, 'image' => '/img/prod-1.jpg'],
                ['name' => 'Alitas BBQ', 'price' => 8.00, 'image' => '/img/prod-2.jpg'],
                ['name' => 'Nachos con queso', 'price' => 6.50, 'image' => '/img/prod-3.jpg'],
            ],
            'Platos fuertes' => [
                ['name' => 'Lomo saltado', 'price' => 14.00, 'image' => '/img/prod-4.jpg'],
                ['name' => 'Pollo a la parrilla', 'price' => 12.50, 'image' => '/img/prod-5.jpg'],
                ['name' => 'Pasta alfredo', 'price' => 11.00, 'image' => '/img/photo1.png'],
                ['name' => 'Pizza margarita', 'price' => 13.50, 'image' => '/img/photo2.png'],
            ],
            'Bebidas' => [
                ['name' => 'Limonada', 'price' => 3.00, 'image' => '/img/photo3.jpg'],
                ['name' => 'Gaseosa', 'price' => 2.50, 'image' => null],
                ['name' => 'Cerveza artesanal', 'price' => 5.00, 'image' => null],
            ],
            'Postres' => [
                ['name' => 'Tiramisú', 'price' => 6.00, 'image' => '/img/photo4.jpg'],
                ['name' => 'Cheesecake', 'price' => 6.50, 'image' => null],
            ],
        ];

        $sortOrder = 0;
        foreach ($menu as $categoryName => $products) {
            $category = Category::create([
                'name' => $categoryName,
                'sort_order' => $sortOrder++,
            ]);

            foreach ($products as $product) {
                Product::create([
                    'category_id' => $category->id,
                    'name' => $product['name'],
                    'price' => $product['price'],
                    'image_path' => $product['image'],
                    'is_available' => true,
                ]);
            }
        }

        $tables = collect(range(1, 10))->map(fn (int $number) => RestaurantTable::create([
            'number' => $number,
            'capacity' => [2, 4, 4, 6][$number % 4],
            'status' => TableStatus::Libre,
        ]));

        $firstProducts = Product::query()->inRandomOrder()->limit(3)->get();
        $demoTable = $tables->first();
        $demoTable->update(['status' => TableStatus::Ocupada]);

        $order = Order::create([
            'restaurant_table_id' => $demoTable->id,
            'user_id' => $mesero->id,
            'status' => OrderStatus::EnCocina,
        ]);

        foreach ($firstProducts as $product) {
            OrderItem::create([
                'order_id' => $order->id,
                'product_id' => $product->id,
                'quantity' => 1,
                'unit_price' => $product->price,
                'status' => OrderItemStatus::Pendiente,
            ]);
        }

        $order->recalculateTotal();
    }
}
