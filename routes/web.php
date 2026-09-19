<?php

use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\RestaurantTableController;
use App\Http\Controllers\Admin\StaffController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Restaurant\CashierController;
use App\Http\Controllers\Restaurant\KitchenController;
use App\Http\Controllers\Restaurant\OrderController;
use App\Http\Controllers\Restaurant\TableBoardController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return auth()->check() ? redirect()->route('dashboard') : redirect()->route('login');
});

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Panel de administración
    Route::middleware('role:admin')->prefix('admin')->name('admin.')->group(function () {
        Route::get('categorias', [CategoryController::class, 'index'])->name('categories.index');
        Route::post('categorias', [CategoryController::class, 'store'])->name('categories.store');
        Route::patch('categorias/{category}', [CategoryController::class, 'update'])->name('categories.update');
        Route::delete('categorias/{category}', [CategoryController::class, 'destroy'])->name('categories.destroy');

        Route::get('productos', [ProductController::class, 'index'])->name('products.index');
        Route::post('productos', [ProductController::class, 'store'])->name('products.store');
        Route::patch('productos/{product}', [ProductController::class, 'update'])->name('products.update');
        Route::delete('productos/{product}', [ProductController::class, 'destroy'])->name('products.destroy');

        Route::get('mesas', [RestaurantTableController::class, 'index'])->name('tables.index');
        Route::post('mesas', [RestaurantTableController::class, 'store'])->name('tables.store');
        Route::patch('mesas/{table}', [RestaurantTableController::class, 'update'])->name('tables.update');
        Route::delete('mesas/{table}', [RestaurantTableController::class, 'destroy'])->name('tables.destroy');

        Route::get('personal', [StaffController::class, 'index'])->name('staff.index');
        Route::post('personal', [StaffController::class, 'store'])->name('staff.store');
        Route::patch('personal/{user}', [StaffController::class, 'update'])->name('staff.update');
        Route::delete('personal/{user}', [StaffController::class, 'destroy'])->name('staff.destroy');
    });

    // Mesero: tablero de mesas y toma de pedidos
    Route::middleware('role:admin,mesero,caja')->group(function () {
        Route::get('mesas', [TableBoardController::class, 'index'])->name('mesas.index');
        Route::get('mesas-data', [TableBoardController::class, 'data'])->name('mesas.data');
    });

    Route::middleware('role:admin,mesero')->group(function () {
        Route::get('mesas/{table}/pedido', [OrderController::class, 'show'])->name('mesas.pedido');
        Route::get('mesas/{table}/pedido-data', [OrderController::class, 'data'])->name('mesas.pedido.data');
        Route::post('mesas/{table}/pedido/enviar', [OrderController::class, 'send'])->name('mesas.pedido.enviar');
        Route::delete('pedidos/items/{item}', [OrderController::class, 'destroyItem'])->name('pedidos.items.destroy');
        Route::patch('pedidos/items/{item}/entregado', [OrderController::class, 'markDelivered'])->name('pedidos.items.entregado');
    });

    // Cocina
    Route::middleware('role:admin,cocina')->group(function () {
        Route::get('cocina', [KitchenController::class, 'index'])->name('cocina.index');
        Route::get('cocina-data', [KitchenController::class, 'data'])->name('cocina.data');
        Route::patch('cocina/items/{item}', [KitchenController::class, 'updateStatus'])->name('cocina.items.update');
    });

    // Caja
    Route::middleware('role:admin,caja,mesero')->group(function () {
        Route::get('caja', [CashierController::class, 'index'])->name('caja.index');
        Route::get('pedidos/{order}/cobrar', [CashierController::class, 'show'])->name('pedidos.cobrar');
        Route::post('pedidos/{order}/cobrar', [CashierController::class, 'store'])->name('pedidos.cobrar.store');
    });
});

require __DIR__.'/auth.php';
