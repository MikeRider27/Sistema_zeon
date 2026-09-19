<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Cocina') }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">
            <div
                id="kitchen-display"
                data-props="{{ json_encode([
                    'initialTickets' => $tickets,
                    'dataUrl' => route('cocina.data'),
                    'updateUrlBase' => url('cocina/items'),
                ]) }}"
            ></div>
        </div>
    </div>
</x-app-layout>
