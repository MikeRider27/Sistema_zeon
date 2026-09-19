<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Mesa') }} {{ $table->number }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">
            <a href="{{ route('mesas.index') }}" class="text-sm text-gray-500 hover:underline">&larr; Volver al tablero de mesas</a>

            <div
                id="order-taker"
                class="mt-4"
                data-props="{{ json_encode([
                    'table' => ['id' => $table->id, 'number' => $table->number],
                    'initialOrder' => $order,
                    'categories' => $categories,
                    'dataUrl' => route('mesas.pedido.data', $table),
                    'sendUrl' => route('mesas.pedido.enviar', $table),
                    'deleteItemUrlBase' => url('pedidos/items'),
                    'deliverUrlBase' => url('pedidos/items'),
                ]) }}"
            ></div>
        </div>
    </div>
</x-app-layout>
