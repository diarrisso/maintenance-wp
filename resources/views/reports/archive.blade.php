<x-app-layout>
    <x-slot name="header">Archiv</x-slot>

    <div class="bg-white rounded-lg shadow-sm">
        <div class="px-6 py-4 border-b">
            <h3 class="text-lg font-semibold">Archivierte Berichte</h3>
            <p class="text-sm text-gray-500 mt-1">Berichte werden automatisch nach einer Woche archiviert.</p>
        </div>

        <table class="w-full">
            <thead class="bg-gray-50 border-b">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Website</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kunde</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Datum</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Gesendet am</th>
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
                        <td class="px-6 py-4">{{ $report->sent_at?->format('d.m.Y H:i') ?? '-' }}</td>
                        <td class="px-6 py-4">{{ $report->user->name }}</td>
                        <td class="px-6 py-4 text-right">
                            <div class="flex items-center justify-end space-x-2">
                                <a href="{{ route('reports.show', $report) }}" class="p-1.5 text-gray-500 hover:text-primary-600 hover:bg-gray-100 rounded" title="Ansehen">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                </a>
                                <a href="{{ route('reports.download', $report) }}" class="p-1.5 text-gray-500 hover:text-primary-600 hover:bg-gray-100 rounded" title="PDF herunterladen">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                </a>
                                <form action="{{ route('reports.restore', $report) }}" method="POST" class="inline" x-data
                                      @submit.prevent="confirmAction($el, 'Bericht wiederherstellen', 'Möchten Sie diesen Bericht aus dem Archiv wiederherstellen?', 'Wiederherstellen')">
                                    @csrf
                                    <button type="submit" class="p-1.5 text-gray-500 hover:text-green-600 hover:bg-gray-100 rounded" title="Wiederherstellen">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"/></svg>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="px-6 py-8 text-center text-gray-500">Keine archivierten Berichte vorhanden.</td></tr>
                @endforelse
            </tbody>
        </table>

        @if($reports->hasPages())
            <div class="px-6 py-4 border-t">{{ $reports->links() }}</div>
        @endif
    </div>
</x-app-layout>
