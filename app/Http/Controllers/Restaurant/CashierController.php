<?php

namespace App\Http\Controllers\Restaurant;

use App\Enums\OrderStatus;
use App\Enums\PaymentMethod;
use App\Enums\TableStatus;
use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class CashierController extends Controller
{
    public function index(): View
    {
        $orders = Order::with('table')
            ->whereNot('status', OrderStatus::Cerrado)
            ->orderByDesc('created_at')
            ->get();

        return view('restaurant.cashier.index', compact('orders'));
    }

    public function show(Order $order): View
    {
        $order->load('items.product', 'table');

        return view('restaurant.cashier.show', compact('order'));
    }

    public function store(Request $request, Order $order): RedirectResponse
    {
        $data = $request->validate([
            'payment_method' => ['required', Rule::enum(PaymentMethod::class)],
        ]);

        $order->update([
            'payment_method' => $data['payment_method'],
            'status' => OrderStatus::Cerrado,
            'paid_at' => now(),
        ]);

        $order->table->update(['status' => TableStatus::Libre]);

        return redirect()->route('caja.index')->with('status', 'Cuenta cobrada. Mesa liberada.');
    }
}
