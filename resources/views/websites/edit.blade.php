<x-app-layout>
    <x-slot name="header">Website bearbeiten</x-slot>
    <div class="max-w-4xl">
        <div class="bg-white rounded-lg shadow-sm">
            <div class="px-6 py-4 border-b"><h3 class="text-lg font-semibold">{{ $website->name }} bearbeiten</h3></div>
            <form action="{{ route('websites.update', $website) }}" method="POST" class="p-6">
                @csrf
                @method('PUT')
                @include('websites._form')
                <div class="flex justify-end space-x-3 pt-6 border-t mt-6">
                    <a href="{{ route('websites.show', $website) }}" class="px-4 py-2 border rounded-lg hover:bg-gray-50">Abbrechen</a>
                    <button type="submit" class="px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white rounded-lg">Speichern</button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
