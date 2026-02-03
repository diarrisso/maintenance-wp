<x-app-layout>
    <x-slot name="header">{{ $website->name }}</x-slot>
    <div class="space-y-6">
        <div class="bg-white rounded-lg shadow-sm">
            <div class="px-6 py-4 border-b flex justify-between">
                <h3 class="text-lg font-semibold">Website-Informationen</h3>
                <div class="space-x-2">
                    <a href="{{ route('maintenance.create', $website) }}" class="text-success-600 hover:text-success-700">Wartung starten</a>
                    <a href="{{ route('websites.edit', $website) }}" class="text-primary-600 hover:text-primary-700">Bearbeiten</a>
                </div>
            </div>
            <div class="p-6 grid grid-cols-2 gap-6">
                <div><p class="text-sm text-gray-500 mb-1">Kunde</p><p class="text-gray-900"><a href="{{ route('clients.show', $website->client) }}" class="text-primary-600">{{ $website->client->name }}</a></p></div>
                <div><p class="text-sm text-gray-500 mb-1">URL</p><p><a href="{{ $website->url }}" target="_blank" class="text-primary-600">{{ $website->url }}</a></p></div>
                @if($website->hosting_provider)<div><p class="text-sm text-gray-500 mb-1">Hosting</p><p>{{ $website->hosting_provider }}</p></div>@endif
                @if($website->php_version)<div><p class="text-sm text-gray-500 mb-1">PHP</p><p>{{ $website->php_version }}</p></div>@endif
                @if($website->wordpress_version)<div><p class="text-sm text-gray-500 mb-1">WordPress</p><p>{{ $website->wordpress_version }}</p></div>@endif
                <div><p class="text-sm text-gray-500 mb-1">Wartungspaket</p><p>{{ ucfirst($website->maintenance_package) }}</p></div>
                @if($website->next_maintenance_date)<div><p class="text-sm text-gray-500 mb-1">Nächste Wartung</p><p>{{ $website->next_maintenance_date->format('d.m.Y') }}</p></div>@endif
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm">
            <div class="px-6 py-4 border-b"><h3 class="text-lg font-semibold">Wartungshistorie ({{ $website->maintenanceReports->count() }})</h3></div>
            @if($website->maintenanceReports->count() > 0)
                <table class="w-full">
                    <thead class="bg-gray-50 border-b">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Datum</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Techniker</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Aktionen</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y">
                        @foreach($website->maintenanceReports->sortByDesc('maintenance_date') as $report)
                            <tr class="hover:bg-gray-50">
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
                                <td class="px-6 py-4 text-right"><a href="{{ route('reports.show', $report) }}" class="text-primary-600 hover:text-primary-900">Ansehen</a></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <div class="p-8 text-center text-gray-500">Noch keine Wartungen durchgeführt.</div>
            @endif
        </div>
    </div>
</x-app-layout>
