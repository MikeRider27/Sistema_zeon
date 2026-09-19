<?php

namespace App\Http\Controllers\Restaurant;

use App\Http\Controllers\Controller;
use App\Models\RestaurantTable;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

class TableBoardController extends Controller
{
    public function index(): View
    {
        return view('restaurant.tables.index', [
            'tables' => $this->tablesPayload(),
        ]);
    }

    public function data(): JsonResponse
    {
        return response()->json($this->tablesPayload());
    }

    private function tablesPayload()
    {
        return RestaurantTable::orderBy('number')
            ->get()
            ->map(fn (RestaurantTable $table) => [
                'id' => $table->id,
                'number' => $table->number,
                'capacity' => $table->capacity,
                'status' => $table->status->value,
                'current_order' => optional($table->currentOrder(), fn ($order) => [
                    'id' => $order->id,
                    'status' => $order->status->value,
                    'total' => (float) $order->total,
                ]),
            ]);
    }
}
