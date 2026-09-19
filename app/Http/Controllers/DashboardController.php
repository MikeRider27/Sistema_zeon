<?php

namespace App\Http\Controllers;

use App\Enums\OrderStatus;
use App\Enums\TableStatus;
use App\Enums\UserRole;
use App\Models\Order;
use App\Models\RestaurantTable;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(Request $request): View|RedirectResponse
    {
        $user = $request->user();

        return match ($user->role) {
            UserRole::Mesero => redirect()->route('mesas.index'),
            UserRole::Cocina => redirect()->route('cocina.index'),
            UserRole::Caja => redirect()->route('caja.index'),
            UserRole::Admin => view('dashboard', [
                'tablesOcupadas' => RestaurantTable::where('status', TableStatus::Ocupada)->count(),
                'tablesTotal' => RestaurantTable::count(),
                'pedidosAbiertos' => Order::whereNot('status', OrderStatus::Cerrado)->count(),
                'ventasHoy' => Order::where('status', OrderStatus::Cerrado)
                    ->whereDate('paid_at', today())
                    ->sum('total'),
            ]),
        };
    }
}
