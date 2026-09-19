<?php

namespace App\Models;

use App\Enums\OrderStatus;
use App\Enums\TableStatus;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['number', 'capacity', 'status'])]
class RestaurantTable extends Model
{
    use HasFactory;

    protected function casts(): array
    {
        return [
            'status' => TableStatus::class,
        ];
    }

    /**
     * @return HasMany<Order, $this>
     */
    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function currentOrder(): ?Order
    {
        return $this->orders()->whereNot('status', OrderStatus::Cerrado)->latest()->first();
    }
}
