@php $tabs = [
    'admin.categories.index' => ['label' => 'Categorías', 'route' => 'admin.categories.index'],
    'admin.products.index' => ['label' => 'Productos', 'route' => 'admin.products.index'],
    'admin.tables.index' => ['label' => 'Mesas', 'route' => 'admin.tables.index'],
    'admin.staff.index' => ['label' => 'Personal', 'route' => 'admin.staff.index'],
]; @endphp

<div class="border-b border-gray-200 mb-6">
    <nav class="-mb-px flex space-x-6">
        @foreach ($tabs as $key => $tab)
            <a href="{{ route($tab['route']) }}"
               class="whitespace-nowrap py-3 px-1 border-b-2 text-sm font-medium {{ request()->routeIs($key) ? 'border-gray-800 text-gray-900' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                {{ $tab['label'] }}
            </a>
        @endforeach
    </nav>
</div>
