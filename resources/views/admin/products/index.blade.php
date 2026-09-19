<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Administración') }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">
            <x-admin-tabs />

            @if (session('status'))
                <div class="mb-4 p-3 bg-green-100 text-green-800 rounded">{{ session('status') }}</div>
            @endif

            <div class="bg-white shadow-sm rounded-lg p-6 mb-6">
                <h3 class="font-semibold text-gray-800 mb-4">Nuevo producto</h3>
                <form method="POST" action="{{ route('admin.products.store') }}" enctype="multipart/form-data" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-6 gap-3 items-end">
                    @csrf
                    <div class="lg:col-span-2">
                        <x-input-label for="name" value="Nombre" />
                        <x-text-input id="name" name="name" class="mt-1 w-full" required />
                    </div>
                    <div>
                        <x-input-label for="category_id" value="Categoría" />
                        <select id="category_id" name="category_id" class="mt-1 w-full border-gray-300 rounded-md" required>
                            @foreach ($categories as $category)
                                <option value="{{ $category->id }}">{{ $category->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <x-input-label for="price" value="Precio" />
                        <x-text-input id="price" name="price" type="number" step="0.01" min="0" class="mt-1 w-full" required />
                    </div>
                    <div>
                        <x-input-label for="image" value="Imagen" />
                        <input id="image" name="image" type="file" accept="image/*" class="mt-1 w-full text-sm">
                    </div>
                    <div class="flex items-center gap-2">
                        <label class="inline-flex items-center gap-2 text-sm text-gray-600">
                            <input type="checkbox" name="is_available" value="1" checked class="rounded border-gray-300">
                            Disponible
                        </label>
                    </div>
                    <div class="lg:col-span-6">
                        <x-input-label for="description" value="Descripción" />
                        <textarea id="description" name="description" rows="2" class="mt-1 w-full border-gray-300 rounded-md"></textarea>
                    </div>
                    <div>
                        <x-primary-button>Crear</x-primary-button>
                    </div>
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
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Imagen</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nombre</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Categoría</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Precio</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Disponible</th>
                            <th class="px-4 py-3"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach ($products as $product)
                            <tr>
                                <td class="px-4 py-3">
                                    @if ($product->image_url)
                                        <img src="{{ $product->image_url }}" class="w-12 h-12 object-cover rounded">
                                    @else
                                        <div class="w-12 h-12 bg-gray-100 rounded"></div>
                                    @endif
                                </td>
                                <td class="px-4 py-3">
                                    <input form="product-edit-{{ $product->id }}" name="name" value="{{ $product->name }}" class="border-gray-300 rounded-md text-sm w-full">
                                </td>
                                <td class="px-4 py-3">
                                    <select form="product-edit-{{ $product->id }}" name="category_id" class="border-gray-300 rounded-md text-sm">
                                        @foreach ($categories as $category)
                                            <option value="{{ $category->id }}" @selected($category->id === $product->category_id)>{{ $category->name }}</option>
                                        @endforeach
                                    </select>
                                </td>
                                <td class="px-4 py-3">
                                    <input form="product-edit-{{ $product->id }}" name="price" type="number" step="0.01" min="0" value="{{ $product->price }}" class="border-gray-300 rounded-md text-sm w-24">
                                </td>
                                <td class="px-4 py-3">
                                    <input form="product-edit-{{ $product->id }}" type="checkbox" name="is_available" value="1" @checked($product->is_available) class="rounded border-gray-300">
                                </td>
                                <td class="px-4 py-3 text-right space-x-2 whitespace-nowrap">
                                    <button form="product-edit-{{ $product->id }}" class="text-sm text-gray-700 hover:underline">Guardar</button>
                                    <form method="POST" action="{{ route('admin.products.destroy', $product) }}" class="inline" onsubmit="return confirm('¿Eliminar este producto?');">
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

            @foreach ($products as $product)
                <form id="product-edit-{{ $product->id }}" method="POST" action="{{ route('admin.products.update', $product) }}" enctype="multipart/form-data" class="hidden">
                    @csrf
                    @method('PATCH')
                </form>
            @endforeach
        </div>
    </div>
</x-app-layout>
