<x-app-layout>
    <x-slot name="header">Wartungsberichte</x-slot>

    <div class="bg-white rounded-lg shadow-sm">
        <div class="px-6 py-4 border-b">
            <h3 class="text-lg font-semibold">Alle Berichte</h3>
        </div>

        <table class="w-full">
            <thead class="bg-gray-50 border-b">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Website</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kunde</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Datum</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Techniker</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Aktionen</th>
                </tr>
            </thead>
            <tbody class="divide-y">
                @forelse($reports as $report)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4"><a href="{{ route('reports.show', $report) }}" class="text-primary-600 font-medium">{{ $report->website->name }}</a></td>
                        <td class="px-6 py-4">{{ $report->website->client->name }}</td>
                        <td class="px-6 py-4">{{ $report->maintenance_date->format('d.m.Y') }}</td>
                        <td class="px-6 py-4">
                            <span class="px-2 py-1 rounded-full text-xs
                                @if($report->status === 'sent') bg-success-100 text-success-800
                                @elseif($report->status === 'completed') bg-blue-100 text-blue-800
                                @else bg-gray-100 text-gray-800 @endif">
                                {{ ucfirst($report->status) }}
                            </span>
                        </td>
                        <td class="px-6 py-4">{{ $report->user->name }}</td>
                        <td class="px-6 py-4 text-right">
                            <div class="flex items-center justify-end space-x-2">
                                {{-- Ansehen --}}
                                <a href="{{ route('reports.show', $report) }}" class="p-1.5 text-gray-500 hover:text-primary-600 hover:bg-gray-100 rounded" title="Ansehen">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                </a>
                                @if($report->status !== 'draft')
                                    {{-- PDF Download --}}
                                    <a href="{{ route('reports.download', $report) }}" class="p-1.5 text-gray-500 hover:text-primary-600 hover:bg-gray-100 rounded" title="PDF herunterladen">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                    </a>
                                @endif
                                @if(Auth::user()->isDeveloper() && in_array($report->status, ['completed', 'sent']))
                                    {{-- Duplizieren --}}
                                    <form action="{{ route('reports.duplicate', $report) }}" method="POST" class="inline" x-data
                                          @submit.prevent="confirmAction($el, 'Bericht duplizieren', 'Möchten Sie diesen Bericht als neuen Entwurf duplizieren?', 'Duplizieren')">
                                        @csrf
                                        <button type="submit" class="p-1.5 text-gray-500 hover:text-indigo-600 hover:bg-gray-100 rounded" title="Duplizieren">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                                        </button>
                                    </form>
                                @endif
                                @if(Auth::user()->isDeveloper())
                                    {{-- Bearbeiten --}}
                                    <a href="{{ route('maintenance.edit', $report) }}" class="p-1.5 text-gray-500 hover:text-blue-600 hover:bg-gray-100 rounded" title="Bearbeiten">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                    </a>
                                    {{-- Löschen --}}
                                    <form action="{{ route('reports.destroy', $report) }}" method="POST" class="inline" x-data
                                          @submit.prevent="confirmDelete($el, 'Bericht löschen', 'Möchten Sie diesen Bericht wirklich löschen?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-1.5 text-gray-500 hover:text-red-600 hover:bg-gray-100 rounded" title="Löschen">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="px-6 py-8 text-center text-gray-500">Keine Berichte vorhanden.</td></tr>
                @endforelse
            </tbody>
        </table>

        @if($reports->hasPages())
            <div class="px-6 py-4 border-t">{{ $reports->links() }}</div>
        @endif
    </div>
</x-app-layout>
