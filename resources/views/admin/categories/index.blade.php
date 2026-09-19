<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Administración') }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
            <x-admin-tabs />

            @if (session('status'))
                <div class="mb-4 p-3 bg-green-100 text-green-800 rounded">{{ session('status') }}</div>
            @endif

            <div class="bg-white shadow-sm rounded-lg p-6 mb-6">
                <h3 class="font-semibold text-gray-800 mb-4">Nueva categoría</h3>
                <form method="POST" action="{{ route('admin.categories.store') }}" class="flex flex-wrap gap-3 items-end">
                    @csrf
                    <div>
                        <x-input-label for="name" value="Nombre" />
                        <x-text-input id="name" name="name" class="mt-1" required />
                    </div>
                    <div>
                        <x-input-label for="sort_order" value="Orden" />
                        <x-text-input id="sort_order" name="sort_order" type="number" class="mt-1 w-24" value="0" />
                    </div>
                    <x-primary-button>Crear</x-primary-button>
                </form>
                <x-input-error :messages="$errors->get('name')" class="mt-2" />
            </div>

            <div class="bg-white shadow-sm rounded-lg overflow-hidden">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nombre</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Orden</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Productos</th>
                            <th class="px-6 py-3"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach ($categories as $category)
                            <tr>
                                <td class="px-6 py-3">
                                    <input form="category-edit-{{ $category->id }}" name="name" value="{{ $category->name }}" class="border-gray-300 rounded-md text-sm w-full">
                                </td>
                                <td class="px-6 py-3">
                                    <input form="category-edit-{{ $category->id }}" name="sort_order" type="number" value="{{ $category->sort_order }}" class="border-gray-300 rounded-md text-sm w-20">
                                </td>
                                <td class="px-6 py-3 text-sm text-gray-600">{{ $category->products_count }}</td>
                                <td class="px-6 py-3 text-right space-x-2 whitespace-nowrap">
                                    <button form="category-edit-{{ $category->id }}" class="text-sm text-gray-700 hover:underline">Guardar</button>
                                    <form method="POST" action="{{ route('admin.categories.destroy', $category) }}" class="inline" onsubmit="return confirm('¿Eliminar esta categoría?');">
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

            @foreach ($categories as $category)
                <form id="category-edit-{{ $category->id }}" method="POST" action="{{ route('admin.categories.update', $category) }}" class="hidden">
                    @csrf
                    @method('PATCH')
                </form>
            @endforeach
        </div>
    </div>
</x-app-layout>
