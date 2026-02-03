<x-app-layout>
    <x-slot name="header">
        {{ $client->name }}
    </x-slot>

    <div class="space-y-6">
        <!-- Kunden-Informationen -->
        <div class="bg-white rounded-lg shadow-sm">
            <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
                <h3 class="text-lg font-semibold text-gray-900">Kunden-Informationen</h3>
                <div class="space-x-2">
                    <a href="{{ route('clients.edit', $client) }}"
                       class="text-primary-600 hover:text-primary-700">
                        Bearbeiten
                    </a>
                </div>
            </div>

            <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <p class="text-sm font-medium text-gray-500 mb-1">Name</p>
                    <p class="text-gray-900">{{ $client->name }}</p>
                </div>

                <div>
                    <p class="text-sm font-medium text-gray-500 mb-1">E-Mail</p>
                    <p class="text-gray-900">
                        <a href="mailto:{{ $client->email }}" class="text-primary-600 hover:text-primary-700">
                            {{ $client->email }}
                        </a>
                    </p>
                </div>

                @if($client->phone)
                    <div>
                        <p class="text-sm font-medium text-gray-500 mb-1">Telefon</p>
                        <p class="text-gray-900">
                            <a href="tel:{{ $client->phone }}" class="text-primary-600 hover:text-primary-700">
                                {{ $client->phone }}
                            </a>
                        </p>
                    </div>
                @endif

                @if($client->company)
                    <div>
                        <p class="text-sm font-medium text-gray-500 mb-1">Firma</p>
                        <p class="text-gray-900">{{ $client->company }}</p>
                    </div>
                @endif

                <div>
                    <p class="text-sm font-medium text-gray-500 mb-1">Wartungspaket</p>
                    <p>
                        <span class="px-2 py-1 rounded-full text-xs font-medium
                            {{ $client->maintenance_type === '2x_monthly' ? 'bg-blue-100 text-blue-800' : 'bg-green-100 text-green-800' }}">
                            {{ $client->maintenance_type_label }}
                        </span>
                    </p>
                </div>

                @if($client->notes)
                    <div class="md:col-span-2">
                        <p class="text-sm font-medium text-gray-500 mb-1">Notizen</p>
                        <p class="text-gray-900 whitespace-pre-wrap">{{ $client->notes }}</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Websites -->
        <div class="bg-white rounded-lg shadow-sm">
            <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
                <h3 class="text-lg font-semibold text-gray-900">
                    Websites ({{ $client->websites->count() }})
                </h3>
                <a href="{{ route('websites.create', ['client_id' => $client->id]) }}"
                   class="bg-primary-600 hover:bg-primary-700 text-white px-4 py-2 rounded-lg transition-colors">
                    Website hinzufügen
                </a>
            </div>

            @if($client->websites->count() > 0)
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50 border-b border-gray-200">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">URL</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Wartungspaket</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nächste Wartung</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Berichte</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Aktionen</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($client->websites as $website)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <a href="{{ route('websites.show', $website) }}"
                                           class="text-sm font-medium text-primary-600 hover:text-primary-700">
                                            {{ $website->name }}
                                        </a>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        <a href="{{ $website->url }}" target="_blank" class="text-primary-600 hover:text-primary-700">
                                            {{ $website->url }}
                                        </a>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                                        <span class="px-2 py-1 rounded-full text-xs font-medium
                                            @if($website->maintenance_package === 'monthly') bg-blue-100 text-blue-800
                                            @elseif($website->maintenance_package === 'quarterly') bg-purple-100 text-purple-800
                                            @elseif($website->maintenance_package === 'yearly') bg-green-100 text-green-800
                                            @else bg-gray-100 text-gray-800
                                            @endif">
                                            @if($website->maintenance_package === 'monthly') Monatlich
                                            @elseif($website->maintenance_package === 'quarterly') Vierteljährlich
                                            @elseif($website->maintenance_package === 'yearly') Jährlich
                                            @else Einmalig
                                            @endif
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        @if($website->next_maintenance_date)
                                            {{ $website->next_maintenance_date->format('d.m.Y') }}
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $website->maintenanceReports->count() }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <a href="{{ route('websites.show', $website) }}"
                                           class="text-primary-600 hover:text-primary-900">
                                            Ansehen
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="p-8 text-center text-gray-500">
                    Diesem Kunden sind noch keine Websites zugeordnet.
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
