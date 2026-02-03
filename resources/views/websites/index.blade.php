<x-app-layout>
    <x-slot name="header">Websites</x-slot>

    @if(session('success'))
        <div class="mb-4 bg-success-50 border border-success-200 text-success-800 px-4 py-3 rounded-lg">{{ session('success') }}</div>
    @endif

    <div class="bg-white rounded-lg shadow-sm">
        <div class="px-6 py-4 border-b flex justify-between">
            <h3 class="text-lg font-semibold">Alle Websites</h3>
            <a href="{{ route('websites.create') }}" class="bg-primary-600 hover:bg-primary-700 text-white px-4 py-2 rounded-lg">Neue Website</a>
        </div>

        <div class="px-6 py-4 border-b">
            <form method="GET" class="flex gap-2">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Suche..." class="flex-1 rounded-lg border-gray-300">
                <button type="submit" class="bg-gray-600 hover:bg-gray-700 text-white px-6 py-2 rounded-lg">Suchen</button>
                @if(request('search'))
                    <a href="{{ route('websites.index') }}" class="bg-gray-200 hover:bg-gray-300 px-6 py-2 rounded-lg">Zurücksetzen</a>
                @endif
            </form>
        </div>

        <table class="w-full">
            <thead class="bg-gray-50 border-b">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Name</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kunde</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">URL</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Wartungspaket</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nächste Wartung</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Aktionen</th>
                </tr>
            </thead>
            <tbody class="divide-y">
                @forelse($websites as $website)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4"><a href="{{ route('websites.show', $website) }}" class="text-primary-600 font-medium">{{ $website->name }}</a></td>
                        <td class="px-6 py-4">{{ $website->client->name }}</td>
                        <td class="px-6 py-4"><a href="{{ $website->url }}" target="_blank" class="text-primary-600">{{ $website->url }}</a></td>
                        <td class="px-6 py-4">
                            <span class="px-2 py-1 rounded-full text-xs
                                @if($website->maintenance_package === 'monthly') bg-blue-100 text-blue-800
                                @elseif($website->maintenance_package === 'quarterly') bg-purple-100 text-purple-800
                                @elseif($website->maintenance_package === 'yearly') bg-green-100 text-green-800
                                @else bg-gray-100 text-gray-800 @endif">
                                {{ ucfirst($website->maintenance_package) }}
                            </span>
                        </td>
                        <td class="px-6 py-4">{{ $website->next_maintenance_date ? $website->next_maintenance_date->format('d.m.Y') : '-' }}</td>
                        <td class="px-6 py-4 text-right">
                            <div class="flex items-center justify-end space-x-1">
                                {{-- Wartung erstellen --}}
                                <a href="{{ route('maintenance.create', $website) }}" class="p-1.5 text-gray-500 hover:text-success-600 hover:bg-gray-100 rounded" title="Wartung erstellen">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                                </a>
                                {{-- Ansehen --}}
                                <a href="{{ route('websites.show', $website) }}" class="p-1.5 text-gray-500 hover:text-primary-600 hover:bg-gray-100 rounded" title="Ansehen">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                </a>
                                {{-- Bearbeiten --}}
                                <a href="{{ route('websites.edit', $website) }}" class="p-1.5 text-gray-500 hover:text-blue-600 hover:bg-gray-100 rounded" title="Bearbeiten">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                </a>
                                {{-- Löschen --}}
                                <form action="{{ route('websites.destroy', $website) }}" method="POST" class="inline" x-data
                                      @submit.prevent="confirmDelete($el, 'Website löschen', 'Möchten Sie die Website {{ $website->name }} wirklich löschen?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-1.5 text-gray-500 hover:text-red-600 hover:bg-gray-100 rounded" title="Löschen">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="px-6 py-8 text-center text-gray-500">Keine Websites gefunden.</td></tr>
                @endforelse
            </tbody>
        </table>

        @if($websites->hasPages())
            <div class="px-6 py-4 border-t">{{ $websites->links() }}</div>
        @endif
    </div>
</x-app-layout>
