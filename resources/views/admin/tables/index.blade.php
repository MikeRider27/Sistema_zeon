<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Administración') }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <x-admin-tabs />

            @if (session('status'))
                <div class="mb-4 p-3 bg-green-100 text-green-800 rounded">{{ session('status') }}</div>
            @endif

            <div class="bg-white shadow-sm rounded-lg p-6 mb-6">
                <h3 class="font-semibold text-gray-800 mb-4">Nueva mesa</h3>
                <form method="POST" action="{{ route('admin.tables.store') }}" class="flex flex-wrap gap-3 items-end">
                    @csrf
                    <div>
                        <x-input-label for="number" value="Número" />
                        <x-text-input id="number" name="number" type="number" min="1" class="mt-1 w-24" required />
                    </div>
                    <div>
                        <x-input-label for="capacity" value="Capacidad" />
                        <x-text-input id="capacity" name="capacity" type="number" min="1" class="mt-1 w-24" value="4" required />
                    </div>
                    <x-primary-button>Crear</x-primary-button>
                </form>
                @if ($errors->any())
                    <div class="mt-3 text-sm text-red-600">
                        <ul class="list-disc list-inside">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
            </div>

            <div class="bg-white shadow-sm rounded-lg overflow-hidden">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Número</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Capacidad</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Estado</th>
                            <th class="px-6 py-3"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach ($tables as $table)
                            <tr>
                                <td class="px-6 py-3">
                                    <input form="table-edit-{{ $table->id }}" name="number" type="number" value="{{ $table->number }}" class="border-gray-300 rounded-md text-sm w-20">
                                </td>
                                <td class="px-6 py-3">
                                    <input form="table-edit-{{ $table->id }}" name="capacity" type="number" value="{{ $table->capacity }}" class="border-gray-300 rounded-md text-sm w-20">
                                </td>
                                <td class="px-6 py-3">
                                    <span class="px-2 py-1 text-xs rounded-full {{ $table->status->value === 'libre' ? 'bg-green-100 text-green-800' : 'bg-amber-100 text-amber-800' }}">
                                        {{ ucfirst($table->status->value) }}
                                    </span>
                                </td>
                                <td class="px-6 py-3 text-right space-x-2 whitespace-nowrap">
                                    <button form="table-edit-{{ $table->id }}" class="text-sm text-gray-700 hover:underline">Guardar</button>
                                    <form method="POST" action="{{ route('admin.tables.destroy', $table) }}" class="inline" onsubmit="return confirm('¿Eliminar esta mesa?');">
                                        @csrf
                                        @method('DELETE')
                                        <button class="text-sm text-red-600 hover:underline">Eliminar</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @foreach ($tables as $table)
                <form id="table-edit-{{ $table->id }}" method="POST" action="{{ route('admin.tables.update', $table) }}" class="hidden">
                    @csrf
                    @method('PATCH')
                </form>
            @endforeach
        </div>
    </div>
</x-app-layout>
