<x-app-layout>
    <x-slot name="header">Neuer Entwickler</x-slot>

    <div class="max-w-3xl">
        <div class="bg-white rounded-lg shadow-sm">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">Entwickler erstellen</h3>
            </div>

            <form action="{{ route('entwicklers.store') }}" method="POST" enctype="multipart/form-data" class="p-6 space-y-6">
                @csrf

                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-1">
                        Name <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="name" id="name" value="{{ old('name') }}" required
                           class="w-full rounded-lg border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 @error('name') border-red-500 @enderror">
                    @error('name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-1">
                        E-Mail <span class="text-red-500">*</span>
                    </label>
                    <input type="email" name="email" id="email" value="{{ old('email') }}" required
                           class="w-full rounded-lg border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 @error('email') border-red-500 @enderror">
                    @error('email')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label for="phone" class="block text-sm font-medium text-gray-700 mb-1">Telefon</label>
                        <input type="text" name="phone" id="phone" value="{{ old('phone') }}"
                               class="w-full rounded-lg border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500">
                    </div>
                    <div>
                        <label for="position" class="block text-sm font-medium text-gray-700 mb-1">Position</label>
                        <input type="text" name="position" id="position" value="{{ old('position') }}" placeholder="z.B. Senior Developer"
                               class="w-full rounded-lg border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500">
                    </div>
                </div>

                <div>
                    <label for="photo" class="block text-sm font-medium text-gray-700 mb-1">Foto</label>
                    <input type="file" name="photo" id="photo" accept="image/*"
                           class="w-full rounded-lg border border-gray-300 p-2">
                    <p class="mt-1 text-xs text-gray-500">Max. 2MB, JPG/PNG</p>
                </div>

                <div>
                    <label class="flex items-center">
                        <input type="checkbox" name="active" value="1" checked class="rounded border-gray-300 text-primary-600 mr-2">
                        <span class="text-sm text-gray-700">Aktiv</span>
                    </label>
                </div>

                <div class="flex items-center justify-end space-x-3 pt-4 border-t border-gray-200">
                    <a href="{{ route('entwicklers.index') }}" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
                        Abbrechen
                    </a>
                    <button type="submit" class="px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white rounded-lg transition-colors">
                        Entwickler erstellen
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
