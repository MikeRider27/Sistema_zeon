<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Mesas') }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">
            <div
                id="table-board"
                data-props="{{ json_encode([
                    'initialTables' => $tables,
                    'dataUrl' => route('mesas.data'),
                    'orderBaseUrl' => url('mesas'),
                    'canOrder' => auth()->user()->isAdmin() || auth()->user()->isMesero(),
                ]) }}"
            ></div>
        </div>
    </div>
</x-app-layout>
