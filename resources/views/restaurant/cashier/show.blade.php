<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Cobrar') }} &mdash; {{ __('Mesa') }} {{ $order->table->number }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <a href="{{ route('caja.index') }}" class="text-sm text-gray-500 hover:underline">&larr; Volver a caja</a>

            <div class="bg-white shadow-sm rounded-lg p-6 mt-4">
                <ul class="divide-y divide-gray-100">
                    @foreach ($order->items as $item)
                        <li class="py-2 flex items-center justify-between text-sm">
                            <span>{{ $item->quantity }}x {{ $item->product->name }}</span>
                            <span>${{ number_format($item->quantity * $item->unit_price, 2) }}</span>
                        </li>
                    @endforeach
                </ul>
                <div class="flex items-center justify-between font-semibold text-lg mt-4 pt-4 border-t">
                    <span>Total</span>
                    <span>${{ number_format($order->total, 2) }}</span>
                </div>

                <form method="POST" action="{{ route('pedidos.cobrar.store', $order) }}" class="mt-6">
                    @csrf
                    <x-input-label for="payment_method" value="Método de pago" />
                    <select id="payment_method" name="payment_method" class="mt-1 w-full border-gray-300 rounded-md" required>
                        <option value="efectivo">Efectivo</option>
                        <option value="tarjeta">Tarjeta</option>
                        <option value="transferencia">Transferencia</option>
                    </select>
                    <x-input-error :messages="$errors->get('payment_method')" class="mt-2" />

                    <x-primary-button class="mt-4">Confirmar cobro y cerrar mesa</x-primary-button>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
