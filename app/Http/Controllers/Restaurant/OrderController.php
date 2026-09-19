<?php

namespace App\Http\Controllers\Restaurant;

use App\Enums\OrderItemStatus;
use App\Enums\OrderStatus;
use App\Enums\TableStatus;
use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\RestaurantTable;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class OrderController extends Controller
{
    public function show(RestaurantTable $table): View
    {
        $order = $table->currentOrder();

        return view('restaurant.orders.show', [
            'table' => $table,
            'order' => $order ? $this->orderPayload($order) : null,
            'categories' => Category::with(['products' => fn ($query) => $query->where('is_available', true)])
                ->orderBy('sort_order')
                ->get(),
        ]);
    }

    public function data(RestaurantTable $table): JsonResponse
    {
        $order = $table->currentOrder();

        return response()->json($order ? $this->orderPayload($order) : null);
    }

    public function send(Request $request, RestaurantTable $table): JsonResponse
    {
        $data = $request->validate([
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required', 'exists:products,id'],
            'items.*.quantity' => ['required', 'integer', 'min:1'],
            'items.*.notes' => ['nullable', 'string', 'max:255'],
        ]);

        $order = DB::transaction(function () use ($table, $data, $request) {
            $order = $this->currentOrEmptyOrder($table, $request);

            foreach ($data['items'] as $item) {
                $product = Product::findOrFail($item['product_id']);

                $order->items()->create([
                    'product_id' => $product->id,
                    'quantity' => $item['quantity'],
                    'unit_price' => $product->price,
                    'notes' => $item['notes'] ?? null,
                    'status' => OrderItemStatus::Pendiente,
                ]);
            }

            $table->update(['status' => TableStatus::Ocupada]);
            $order->recalculateTotal();
            $order->refresh();
            $order->recalculateStatus();

            return $order;
        });

        return response()->json($this->orderPayload($order->fresh('items.product')));
    }

    public function destroyItem(Request $request, OrderItem $item): JsonResponse
    {
        if ($item->status !== OrderItemStatus::Pendiente) {
            abort(422, 'Solo se pueden quitar productos que aún no empezaron a prepararse.');
        }

        $order = $item->order;
        $item->delete();
        $order->recalculateTotal();
        $order->recalculateStatus();

        return response()->json($this->orderPayload($order->fresh('items.product')));
    }

    public function markDelivered(OrderItem $item): JsonResponse
    {
        if ($item->status !== OrderItemStatus::Listo) {
            abort(422, 'Ese producto todavía no está listo en cocina.');
        }

        $item->update(['status' => OrderItemStatus::Entregado]);

        $order = $item->order;
        $order->recalculateStatus();

        return response()->json($this->orderPayload($order->fresh('items.product')));
    }

    private function currentOrEmptyOrder(RestaurantTable $table, Request $request): Order
    {
        return $table->currentOrder() ?? $table->orders()->create([
            'user_id' => $request->user()->id,
            'status' => OrderStatus::Abierto,
        ]);
    }

    private function orderPayload(Order $order): array
    {
        $order->loadMissing('items.product');

        return [
            'id' => $order->id,
            'status' => $order->status->value,
            'total' => (float) $order->total,
            'items' => $order->items->map(fn (OrderItem $item) => [
                'id' => $item->id,
                'product_name' => $item->product->name,
                'quantity' => $item->quantity,
                'unit_price' => (float) $item->unit_price,
                'notes' => $item->notes,
                'status' => $item->status->value,
            ]),
        ];
    }
}
