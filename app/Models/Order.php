<?php

namespace App\Models;

use App\Enums\OrderItemStatus;
use App\Enums\OrderStatus;
use App\Enums\PaymentMethod;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['restaurant_table_id', 'user_id', 'status', 'notes', 'payment_method', 'total', 'paid_at'])]
class Order extends Model
{
    use HasFactory;

    protected function casts(): array
    {
        return [
            'status' => OrderStatus::class,
            'payment_method' => PaymentMethod::class,
            'total' => 'decimal:2',
            'paid_at' => 'datetime',
        ];
    }

    /**
     * @return BelongsTo<RestaurantTable, $this>
     */
    public function table(): BelongsTo
    {
        return $this->belongsTo(RestaurantTable::class, 'restaurant_table_id');
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function waiter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * @return HasMany<OrderItem, $this>
     */
    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function recalculateTotal(): void
    {
        $this->total = $this->items->sum(fn (OrderItem $item) => $item->quantity * $item->unit_price);
        $this->save();
    }

    public function recalculateStatus(): void
    {
        if ($this->status === OrderStatus::Cerrado) {
            return;
        }

        $statuses = $this->items()->pluck('status');

        $this->status = match (true) {
            $statuses->isEmpty() => OrderStatus::Abierto,
            $statuses->contains(fn (OrderItemStatus $status) => in_array($status, [OrderItemStatus::Pendiente, OrderItemStatus::Preparando], true)) => OrderStatus::EnCocina,
            $statuses->contains(OrderItemStatus::Listo) => OrderStatus::Listo,
            default => OrderStatus::Servido,
        };

        $this->save();
    }
}
