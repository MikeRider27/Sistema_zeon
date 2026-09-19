<?php

namespace App\Http\Controllers\Admin;

use App\Enums\TableStatus;
use App\Http\Controllers\Controller;
use App\Models\RestaurantTable;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class RestaurantTableController extends Controller
{
    public function index(): View
    {
        $tables = RestaurantTable::orderBy('number')->get();

        return view('admin.tables.index', compact('tables'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'number' => ['required', 'integer', 'min:1', 'unique:restaurant_tables,number'],
            'capacity' => ['required', 'integer', 'min:1'],
        ]);

        RestaurantTable::create([
            ...$data,
            'status' => TableStatus::Libre,
        ]);

        return back()->with('status', 'Mesa creada.');
    }

    public function update(Request $request, RestaurantTable $table): RedirectResponse
    {
        $data = $request->validate([
            'number' => ['required', 'integer', 'min:1', 'unique:restaurant_tables,number,'.$table->id],
            'capacity' => ['required', 'integer', 'min:1'],
        ]);

        $table->update($data);

        return back()->with('status', 'Mesa actualizada.');
    }

    public function destroy(RestaurantTable $table): RedirectResponse
    {
        $table->delete();

        return back()->with('status', 'Mesa eliminada.');
    }
}
