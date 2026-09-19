<?php

namespace App\Http\Controllers\Restaurant;

use App\Enums\OrderItemStatus;
use App\Http\Controllers\Controller;
use App\Models\OrderItem;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class KitchenController extends Controller
{
    public function index(): View
    {
        return view('restaurant.kitchen.index', [
            'tickets' => $this->ticketsPayload(),
        ]);
    }

    public function data(): JsonResponse
    {
        return response()->json($this->ticketsPayload());
    }

    public function updateStatus(Request $request, OrderItem $item): JsonResponse
    {
        $data = $request->validate([
            'status' => ['required', Rule::in([OrderItemStatus::Preparando->value, OrderItemStatus::Listo->value])],
        ]);

        $item->update(['status' => $data['status']]);
        $item->order->recalculateStatus();

        return response()->json($this->ticketsPayload());
    }

    private function ticketsPayload()
    {
        return OrderItem::query()
            ->whereIn('status', [OrderItemStatus::Pendiente, OrderItemStatus::Preparando])
            ->with(['product', 'order.table'])
            ->orderBy('created_at')
            ->get()
            ->map(fn (OrderItem $item) => [
                'id' => $item->id,
                'order_id' => $item->order_id,
                'table_number' => $item->order->table->number,
                'product_name' => $item->product->name,
                'quantity' => $item->quantity,
                'notes' => $item->notes,
                'status' => $item->status->value,
                'created_at' => $item->created_at->toIso8601String(),
            ]);
    }
}
