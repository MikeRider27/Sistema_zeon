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
            @error('user')
                <div class="mb-4 p-3 bg-red-100 text-red-800 rounded">{{ $message }}</div>
            @enderror

            <div class="bg-white shadow-sm rounded-lg p-6 mb-6">
                <h3 class="font-semibold text-gray-800 mb-4">Nuevo usuario</h3>
                <form method="POST" action="{{ route('admin.staff.store') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3 items-end">
                    @csrf
                    <div>
                        <x-input-label for="name" value="Nombre" />
                        <x-text-input id="name" name="name" class="mt-1 w-full" required />
                    </div>
                    <div>
                        <x-input-label for="email" value="Correo" />
                        <x-text-input id="email" name="email" type="email" class="mt-1 w-full" required />
                    </div>
                    <div>
                        <x-input-label for="password" value="Contraseña" />
                        <x-text-input id="password" name="password" type="password" class="mt-1 w-full" required />
                    </div>
                    <div>
                        <x-input-label for="role" value="Rol" />
                        <select id="role" name="role" class="mt-1 w-full border-gray-300 rounded-md">
                            @foreach ($roles as $role)
                                <option value="{{ $role->value }}">{{ $role->label() }}</option>
                            @endforeach
                        </select>
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
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nombre</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Correo</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Rol</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nueva contraseña</th>
                            <th class="px-6 py-3"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach ($users as $user)
                            <tr>
                                <td class="px-6 py-3">
                                    <input form="user-edit-{{ $user->id }}" name="name" value="{{ $user->name }}" class="border-gray-300 rounded-md text-sm w-full">
                                </td>
                                <td class="px-6 py-3">
                                    <input form="user-edit-{{ $user->id }}" name="email" type="email" value="{{ $user->email }}" class="border-gray-300 rounded-md text-sm w-full">
                                </td>
                                <td class="px-6 py-3">
                                    <select form="user-edit-{{ $user->id }}" name="role" class="border-gray-300 rounded-md text-sm">
                                        @foreach ($roles as $role)
                                            <option value="{{ $role->value }}" @selected($role === $user->role)>{{ $role->label() }}</option>
                                        @endforeach
                                    </select>
                                </td>
                                <td class="px-6 py-3">
                                    <input form="user-edit-{{ $user->id }}" name="password" type="password" placeholder="Dejar en blanco" class="border-gray-300 rounded-md text-sm w-full">
                                </td>
                                <td class="px-6 py-3 text-right space-x-2 whitespace-nowrap">
                                    <button form="user-edit-{{ $user->id }}" class="text-sm text-gray-700 hover:underline">Guardar</button>
                                    <form method="POST" action="{{ route('admin.staff.destroy', $user) }}" class="inline" onsubmit="return confirm('¿Eliminar este usuario?');">
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

            @foreach ($users as $user)
                <form id="user-edit-{{ $user->id }}" method="POST" action="{{ route('admin.staff.update', $user) }}" class="hidden">
                    @csrf
                    @method('PATCH')
                </form>
            @endforeach
        </div>
    </div>
</x-app-layout>
