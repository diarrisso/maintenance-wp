<x-app-layout>
    <x-slot name="header">Wartungsbericht</x-slot>

    @if(session('success'))<div class="mb-4 bg-success-50 border border-success-200 text-success-800 px-4 py-3 rounded-lg">{{ session('success') }}</div>@endif
    @if(session('warning'))<div class="mb-4 bg-yellow-50 border border-yellow-200 text-yellow-800 px-4 py-3 rounded-lg">{{ session('warning') }}</div>@endif

    <div class="space-y-6">
        <div class="bg-white rounded-lg shadow-sm">
            <div class="px-6 py-4 border-b flex justify-between">
                <div>
                    <h3 class="text-lg font-semibold">{{ $report->website->name }}</h3>
                    <p class="text-sm text-gray-600">{{ $report->website->client->name }} | {{ $report->maintenance_date->format('d.m.Y') }}</p>
                </div>
                <div class="flex items-center space-x-3">
                    @if($report->status === 'draft')
                        <span class="px-3 py-1 bg-yellow-100 text-yellow-800 rounded-full text-sm">Entwurf</span>
                        @if(Auth::user()->isDeveloper())
                            <a href="{{ route('maintenance.edit', $report) }}" class="px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-lg text-sm">Bearbeiten</a>
                            <form action="{{ route('maintenance.complete', $report) }}" method="POST" class="inline" x-data
                                  @submit.prevent="confirmAction($el, 'Bericht abschließen', 'Möchten Sie den Bericht abschließen und das PDF erstellen?', 'Abschließen')">
                                @csrf
                                <button type="submit" class="px-4 py-2 bg-success-600 hover:bg-success-700 text-white rounded-lg text-sm inline-flex items-center">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    Abschließen
                                </button>
                            </form>
                            <form action="{{ route('reports.destroy', $report) }}" method="POST" class="inline" x-data
                                  @submit.prevent="confirmDelete($el, 'Entwurf löschen', 'Möchten Sie diesen Entwurf wirklich löschen?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="px-3 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg text-sm" title="Löschen">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                </button>
                            </form>
                        @endif
                    @elseif($report->status === 'completed')
                        <span class="px-3 py-1 bg-blue-100 text-blue-800 rounded-full text-sm">Abgeschlossen</span>
                        <a href="{{ route('reports.download', $report) }}" class="px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white rounded-lg text-sm inline-flex items-center">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            PDF
                        </a>
                        <form action="{{ route('reports.send-email', $report) }}" method="POST" class="inline" x-data
                              @submit.prevent="confirmAction($el, 'E-Mail senden', 'Möchten Sie die E-Mail an den Kunden und PM senden?', 'Senden')">
                            @csrf
                            <button type="submit" class="px-4 py-2 bg-orange-500 hover:bg-orange-600 text-white rounded-lg text-sm inline-flex items-center" title="E-Mail senden">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                                E-Mail senden
                            </button>
                        </form>
                        @if($report->website->teams_webhook_url)
                            <form action="{{ route('reports.send-teams', $report) }}" method="POST" class="inline">
                                @csrf
                                <button type="submit" class="px-3 py-2 bg-purple-600 hover:bg-purple-700 text-white rounded-lg text-sm" title="Teams senden">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M19.35 8.04c-.24 0-.47.02-.7.05A5.99 5.99 0 0012 4a6 6 0 00-5.94 5.05 4.5 4.5 0 00.44 8.95h12.35a3.5 3.5 0 00.5-6.96z"/></svg>
                                </button>
                            </form>
                        @endif
                        @if(Auth::user()->isDeveloper())
                            <form action="{{ route('reports.duplicate', $report) }}" method="POST" class="inline" x-data @submit.prevent="confirmAction($el, 'Bericht duplizieren', 'Möchten Sie diesen Bericht als neuen Entwurf duplizieren?', 'Duplizieren')">
                                @csrf
                                <button type="submit" class="px-3 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-sm" title="Duplizieren">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                                </button>
                            </form>
                            <form action="{{ route('reports.regenerate-pdf', $report) }}" method="POST" class="inline" x-data @submit.prevent="confirmAction($el, 'PDF neu generieren', 'Möchten Sie das PDF neu generieren?', 'Generieren')">
                                @csrf
                                <button type="submit" class="px-3 py-2 bg-teal-600 hover:bg-teal-700 text-white rounded-lg text-sm" title="PDF neu generieren">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                                </button>
                            </form>
                            <a href="{{ route('maintenance.edit', $report) }}" class="px-3 py-2 bg-gray-500 hover:bg-gray-600 text-white rounded-lg text-sm" title="Bearbeiten">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                            </a>
                            <form action="{{ route('reports.destroy', $report) }}" method="POST" class="inline" x-data @submit.prevent="confirmDelete($el, 'Bericht löschen', 'Möchten Sie diesen Bericht wirklich löschen? Diese Aktion kann nicht rückgängig gemacht werden.')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="px-3 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg text-sm" title="Löschen">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                </button>
                            </form>
                        @endif
                    @else
                        <div class="flex items-center space-x-2">
                            <span class="px-3 py-1 bg-success-100 text-success-800 rounded-full text-sm">Bereits gesendet</span>
                            @if($report->sent_at)
                                <span class="text-sm text-gray-500">am {{ $report->sent_at->format('d.m.Y H:i') }}</span>
                            @endif
                        </div>
                        <a href="{{ route('reports.download', $report) }}" class="px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white rounded-lg text-sm inline-flex items-center">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            PDF
                        </a>
                        <form action="{{ route('reports.send-email', $report) }}" method="POST" class="inline" x-data @submit.prevent="confirmAction($el, 'E-Mail erneut senden', 'Möchten Sie die E-Mail erneut senden?', 'Senden')">
                            @csrf
                            <button type="submit" class="px-3 py-2 bg-gray-500 hover:bg-gray-600 text-white rounded-lg text-sm inline-flex items-center" title="E-Mail erneut senden">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                                Erneut senden
                            </button>
                        </form>
                        @if($report->website->teams_webhook_url)
                            <form action="{{ route('reports.send-teams', $report) }}" method="POST" class="inline">
                                @csrf
                                <button type="submit" class="px-3 py-2 bg-purple-600 hover:bg-purple-700 text-white rounded-lg text-sm" title="Teams senden">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M19.35 8.04c-.24 0-.47.02-.7.05A5.99 5.99 0 0012 4a6 6 0 00-5.94 5.05 4.5 4.5 0 00.44 8.95h12.35a3.5 3.5 0 00.5-6.96z"/></svg>
                                </button>
                            </form>
                        @endif
                        @if(Auth::user()->isDeveloper())
                            <form action="{{ route('reports.duplicate', $report) }}" method="POST" class="inline" x-data @submit.prevent="confirmAction($el, 'Bericht duplizieren', 'Möchten Sie diesen Bericht als neuen Entwurf duplizieren?', 'Duplizieren')">
                                @csrf
                                <button type="submit" class="px-3 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-sm" title="Duplizieren">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                                </button>
                            </form>
                            <form action="{{ route('reports.regenerate-pdf', $report) }}" method="POST" class="inline" x-data @submit.prevent="confirmAction($el, 'PDF neu generieren', 'Möchten Sie das PDF neu generieren?', 'Generieren')">
                                @csrf
                                <button type="submit" class="px-3 py-2 bg-teal-600 hover:bg-teal-700 text-white rounded-lg text-sm" title="PDF neu generieren">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                                </button>
                            </form>
                            <form action="{{ route('reports.destroy', $report) }}" method="POST" class="inline" x-data @submit.prevent="confirmDelete($el, 'Bericht löschen', 'Möchten Sie diesen Bericht wirklich löschen? Diese Aktion kann nicht rückgängig gemacht werden.')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="px-3 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg text-sm" title="Löschen">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                </button>
                            </form>
                        @endif
                    @endif
                </div>
            </div>

            <div class="p-6 space-y-6">
                <div>
                    <h4 class="font-semibold mb-2">Vorbereitung</h4>
                    <p>Backup: {{ $report->backup_completed ? 'Ja' : 'Nein' }} @if($report->backup_datetime)({{ $report->backup_datetime->format('d.m.Y H:i') }})@endif</p>
                    <p>PHP kompatibel: {{ $report->php_compatible ? 'Ja' : 'Nein' }}</p>
                </div>

                @if($report->wp_version_before || $report->wp_version_after || $report->pluginUpdates->count() > 0)
                    <div>
                        <h4 class="font-semibold mb-2">Aktualisierungen</h4>
                        <table class="w-full border">
                            <thead class="bg-gray-50"><tr><th class="px-4 py-2 text-left">Komponente</th><th class="px-4 py-2 text-left">Vorher</th><th class="px-4 py-2 text-left">Nachher</th></tr></thead>
                            <tbody class="divide-y">
                                @if($report->wp_version_before || $report->wp_version_after)
                                    <tr><td class="px-4 py-2">WordPress</td><td>{{ $report->wp_version_before }}</td><td>{{ $report->wp_version_after }}</td></tr>
                                @endif
                                @if($report->theme_name)
                                    <tr><td class="px-4 py-2">{{ $report->theme_name }}</td><td>{{ $report->theme_version_before }}</td><td>{{ $report->theme_version_after }}</td></tr>
                                @endif
                                @foreach($report->pluginUpdates as $plugin)
                                    <tr><td class="px-4 py-2">{{ $plugin->plugin_name }}</td><td>{{ $plugin->version_before }}</td><td>{{ $plugin->version_after }}</td></tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif

                <div>
                    <h4 class="font-semibold mb-2">Prüfungen</h4>
                    <div class="grid grid-cols-3 gap-2">
                        <div>✓ Startseite: {{ $report->check_frontend ? 'OK' : 'Fehler' }}</div>
                        <div>✓ Navigation: {{ $report->check_navigation ? 'OK' : 'Fehler' }}</div>
                        <div>✓ Formulare: {{ $report->check_forms ? 'OK' : 'Fehler' }}</div>
                        <div>✓ Responsive: {{ $report->check_responsive ? 'OK' : 'Fehler' }}</div>
                        <div>✓ Admin-Login: {{ $report->check_admin_login ? 'OK' : 'Fehler' }}</div>
                        <div>✓ Medien-Upload: {{ $report->check_media_upload ? 'OK' : 'Fehler' }}</div>
                        <div>✓ Keine Fehler: {{ $report->check_no_errors ? 'OK' : 'Fehler' }}</div>
                        <div>✓ SSL: {{ $report->check_ssl ? 'OK' : 'Fehler' }}</div>
                        <div>✓ Sicherheit: {{ $report->check_security ? 'OK' : 'Fehler' }}</div>
                    </div>
                    @if($report->loading_time)<p class="mt-2">Ladezeit: {{ $report->loading_time }}s</p>@endif
                </div>

                @if($report->issues_found)
                    <div>
                        <h4 class="font-semibold mb-2">Probleme</h4>
                        <p class="whitespace-pre-wrap">{{ $report->issues_found }}</p>
                    </div>
                @endif

                @if($report->recommendations()->count() > 0)
                    <div>
                        <h4 class="font-semibold mb-3">Empfehlungen für den Kunden</h4>
                        <div class="space-y-3">
                            @foreach($report->recommendations()->get() as $rec)
                                <div class="border rounded-lg p-4 {{ $rec->priority === 'critical' ? 'bg-red-50 border-red-200' : ($rec->priority === 'high' ? 'bg-orange-50 border-orange-200' : ($rec->priority === 'medium' ? 'bg-yellow-50 border-yellow-200' : 'bg-blue-50 border-blue-200')) }}">
                                    <div class="flex justify-between items-start">
                                        <div class="font-medium">{{ $rec->title }}</div>
                                        <span class="px-2 py-1 text-xs rounded-full {{ $rec->priority_color }}">{{ $rec->priority_label }}</span>
                                    </div>
                                    <div class="mt-2 text-sm text-gray-600">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded bg-gray-200 text-gray-700 text-xs mr-2">{{ $rec->type_label }}</span>
                                        @if($rec->action)<span class="text-gray-500">→ {{ $rec->action_label }}</span>@endif
                                    </div>
                                    @if($rec->current_item || $rec->suggested_item)
                                        <div class="mt-2 text-sm">
                                            @if($rec->current_item)<div><strong>Aktuell:</strong> {{ $rec->current_item }}</div>@endif
                                            @if($rec->suggested_item)<div class="text-green-700"><strong>Alternative:</strong> {{ $rec->suggested_item }}</div>@endif
                                        </div>
                                    @endif
                                    @if($rec->description)
                                        <div class="mt-2 text-sm text-gray-700">{{ $rec->description }}</div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                @if($report->recommendations)
                    <div>
                        <h4 class="font-semibold mb-2">Zusätzliche Hinweise</h4>
                        <p class="whitespace-pre-wrap">{{ $report->recommendations }}</p>
                    </div>
                @endif

                @if($report->next_maintenance_date)
                    <div>
                        <h4 class="font-semibold mb-2">Nächste Wartung</h4>
                        <p>{{ $report->next_maintenance_date->format('d.m.Y') }}</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
