<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <div class="bg-white overflow-hidden shadow-sm rounded-lg p-6">
                    <p class="text-sm text-gray-500">Mesas ocupadas</p>
                    <p class="mt-2 text-3xl font-semibold text-gray-900">{{ $tablesOcupadas }} / {{ $tablesTotal }}</p>
                </div>
                <div class="bg-white overflow-hidden shadow-sm rounded-lg p-6">
                    <p class="text-sm text-gray-500">Pedidos abiertos</p>
                    <p class="mt-2 text-3xl font-semibold text-gray-900">{{ $pedidosAbiertos }}</p>
                </div>
                <div class="bg-white overflow-hidden shadow-sm rounded-lg p-6 sm:col-span-2 lg:col-span-2">
                    <p class="text-sm text-gray-500">Ventas de hoy</p>
                    <p class="mt-2 text-3xl font-semibold text-gray-900">${{ number_format($ventasHoy, 2) }}</p>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm rounded-lg p-6">
                <h3 class="font-semibold text-gray-800 mb-4">Accesos rápidos</h3>
                <div class="flex flex-wrap gap-3">
                    <a href="{{ route('mesas.index') }}" class="px-4 py-2 bg-gray-800 text-white rounded-md text-sm hover:bg-gray-700">Tablero de mesas</a>
                    <a href="{{ route('cocina.index') }}" class="px-4 py-2 bg-gray-800 text-white rounded-md text-sm hover:bg-gray-700">Pantalla de cocina</a>
                    <a href="{{ route('caja.index') }}" class="px-4 py-2 bg-gray-800 text-white rounded-md text-sm hover:bg-gray-700">Caja</a>
                    <a href="{{ route('admin.products.index') }}" class="px-4 py-2 bg-gray-200 text-gray-800 rounded-md text-sm hover:bg-gray-300">Administración</a>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
