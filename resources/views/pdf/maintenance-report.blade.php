@php
    $agency = \App\Models\AgencySettings::instance();
    $primaryColor = $agency->primary_color ?? '#2563eb';
@endphp
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Wartungsbericht - {{ $agency->name }}</title>
    <style>
        body { font-family: DejaVu Sans, Arial, sans-serif; font-size: 11pt; color: #333; margin: 0; padding: 0; }
        .header-content { display: table; width: 100%; }
        .header-logo { display: table-cell; vertical-align: middle; width: 150px; }
        .header-logo img { max-height: 60px; max-width: 140px; }
        .header-title { display: table-cell; vertical-align: middle; text-align: right; }
        .header-title h1 { margin: 0; font-size: 22pt; }
        .header-title p { margin: 5px 0 0; font-size: 12pt; color: #374151; }

        .client-info { border: 1px solid #e2e8f0; padding: 15px 20px; margin: 0 20px 20px; }
        .client-header { display: table; width: 100%; margin-bottom: 10px; }
        .client-logo { display: table-cell; vertical-align: middle; width: 80px; }
        .client-logo img { max-height: 50px; max-width: 70px; }
        .client-name { display: table-cell; vertical-align: middle; }
        .client-name h2 { margin: 0; font-size: 16pt; color: #1e40af; }

        .content { padding: 0 20px; }
        h3 { color: #374151; margin-top: 20px; font-size: 12pt; }

        table { width: 100%; border-collapse: collapse; margin: 15px 0; }
        th, td { padding: 8px 10px; text-align: left; border-bottom: 1px solid #e5e7eb; }
        th { font-weight: bold; color: #374151; }

        .check-ok { color: #059669; font-weight: bold; }
        .check-fail { color: #dc2626; font-weight: bold; }
        .status-updated { color: #065f46; padding: 2px 8px; font-size: 9pt; font-weight: bold; }
        .status-skipped { color: #92400e; padding: 2px 8px; font-size: 9pt; font-weight: bold; }
        .status-no-access { color: #991b1b; padding: 2px 8px; font-size: 9pt; font-weight: bold; }

        .info-grid { display: table; width: 100%; margin: 15px 0; }
        .info-row { display: table-row; }
        .info-label { display: table-cell; width: 35%; font-weight: bold; padding: 6px 10px 6px 0; color: #6b7280; }
        .info-value { display: table-cell; padding: 6px 0; }
    </style>
</head>
<body>
    {{-- Header avec logo agence --}}
    <div style="padding: 20px 30px; margin-bottom: 20px; border-bottom: 2px solid {{ $primaryColor }};">
        <div class="header-content">
            <div class="header-logo">
                @if($agency->logo_path && file_exists($agency->logo_path))
                    <img src="{{ $agency->logo_path }}" alt="{{ $agency->name }}">
                @elseif($agency->logo_white_path && file_exists($agency->logo_white_path))
                    <img src="{{ $agency->logo_white_path }}" alt="{{ $agency->name }}">
                @elseif(file_exists(public_path('img/80-30_Bildmarke_RGB_24.svg')))
                    <img src="{{ public_path('img/80-30_Bildmarke_RGB_24.svg') }}" alt="{{ $agency->name }}">
                @else
                    <span style="font-size: 18pt; font-weight: bold; color: {{ $primaryColor }};">{{ substr($agency->name, 0, 2) }}</span>
                @endif
            </div>
            <div class="header-title">
                <h1 style="color: {{ $primaryColor }};">Wartungsbericht</h1>
                <p style="color: #374151;">WordPress Maintenance Report</p>
            </div>
        </div>
    </div>

    {{-- Informations client avec logo --}}
    <div class="client-info" style="border: 1px solid #e2e8f0; padding: 15px 20px; margin: 0 20px 20px;">
        <div class="client-header">
            @if($report->website->client->logo)
                <div class="client-logo">
                    <img src="{{ storage_path('app/public/' . $report->website->client->logo) }}" alt="{{ $report->website->client->name }}">
                </div>
            @endif
            <div class="client-name">
                <h2>{{ $report->website->client->name }}</h2>
            </div>
        </div>
        <div class="info-grid">
            <div class="info-row">
                <div class="info-label">Website:</div>
                <div class="info-value">{{ $report->website->name }} ({{ $report->website->url }})</div>
            </div>
            <div class="info-row">
                <div class="info-label">Wartungsdatum:</div>
                <div class="info-value">{{ $report->maintenance_date->format('d.m.Y') }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Wartungspaket:</div>
                <div class="info-value">{{ $report->website->client->maintenance_type_label ?? 'Standard' }}</div>
            </div>
            @if($report->website->client->maintenance_type === '2x_monthly' && $report->maintenance_number)
            <div class="info-row">
                <div class="info-label">Wartung Nr.:</div>
                <div class="info-value">{{ $report->maintenance_number }}. Wartung des Monats</div>
            </div>
            @endif
        </div>
    </div>

    {{-- Website-Zustand Box --}}
    @if($report->website_condition)
        @php
            $conditionColors = [
                'excellent' => ['border' => '#10b981', 'text' => '#065f46'],
                'good' => ['border' => '#3b82f6', 'text' => '#1e40af'],
                'needs_improvement' => ['border' => '#eab308', 'text' => '#854d0e'],
                'critical' => ['border' => '#ef4444', 'text' => '#991b1b'],
            ];
            $conditionLabels = [
                'excellent' => '🌟 Ausgezeichnet',
                'good' => '✅ Gut',
                'needs_improvement' => '⚠️ Verbesserung nötig',
                'critical' => '🚨 Kritisch',
            ];
            $colors = $conditionColors[$report->website_condition] ?? $conditionColors['good'];
        @endphp
        <div style="border: 2px solid {{ $colors['border'] }}; padding: 15px 20px; margin: 0 20px 20px; border-radius: 8px; text-align: center;">
            <div style="font-size: 16pt; font-weight: bold; color: {{ $colors['text'] }};">
                Website-Zustand: {{ $conditionLabels[$report->website_condition] ?? 'Gut' }}
            </div>
            @if($report->website_condition_notes)
                <div style="margin-top: 8px; font-size: 10pt; color: #6b7280;">
                    {{ $report->website_condition_notes }}
                </div>
            @endif
        </div>
    @endif

    <div class="content">
        {{-- Entwickler/Techniker Box --}}
        <div style="border: 1px solid #e2e8f0; padding: 15px; margin: 20px 0;">
            <h4 style="margin: 0 0 10px; color: {{ $primaryColor }};">Durchgeführt von:</h4>
            @if($report->entwickler)
                <div class="info-grid">
                    <div class="info-row">
                        <div class="info-label">Entwickler:</div>
                        <div class="info-value"><strong>{{ $report->entwickler->name }}</strong></div>
                    </div>
                    @if($report->entwickler->position)
                        <div class="info-row">
                            <div class="info-label">Position:</div>
                            <div class="info-value">{{ $report->entwickler->position }}</div>
                        </div>
                    @endif
                    @if($report->entwickler->email)
                        <div class="info-row">
                            <div class="info-label">E-Mail:</div>
                            <div class="info-value">{{ $report->entwickler->email }}</div>
                        </div>
                    @endif
                </div>
            @else
                <div class="info-grid">
                    <div class="info-row">
                        <div class="info-label">Techniker:</div>
                        <div class="info-value"><strong>{{ $report->user->name ?? 'N/A' }}</strong></div>
                    </div>
                </div>
            @endif
            @if($report->signature)
                <div style="margin-top: 15px; border-top: 1px solid #e2e8f0; padding-top: 10px;">
                    <div style="font-size: 9pt; color: #6b7280; margin-bottom: 5px;">Unterschrift:</div>
                    <img src="{{ $report->signature }}" style="max-height: 80px; max-width: 250px;" alt="Unterschrift">
                </div>
            @endif
        </div>

        <h2 style="color: {{ $primaryColor }}; border-bottom: 2px solid {{ $primaryColor }}; padding-bottom: 5px; margin-top: 25px; font-size: 14pt;">Phase 1 - Vorbereitung</h2>
        <table>
            <tr>
                <td style="width: 70%;">Backup durchgeführt</td>
                <td class="{{ $report->backup_completed ? 'check-ok' : 'check-fail' }}">
                    {{ $report->backup_completed ? 'Ja' : 'Nein' }}
                    @if($report->backup_datetime) ({{ $report->backup_datetime->format('d.m.Y H:i') }})@endif
                </td>
            </tr>
            <tr>
                <td>PHP-Version kompatibel</td>
                <td class="{{ $report->php_compatible ? 'check-ok' : 'check-fail' }}">
                    {{ $report->php_compatible ? 'Ja' : 'Nein' }}
                </td>
            </tr>
        </table>

        <h2 style="color: {{ $primaryColor }}; border-bottom: 2px solid {{ $primaryColor }}; padding-bottom: 5px; margin-top: 25px; font-size: 14pt;">Phase 2 - Aktualisierungen</h2>

        <h3>WordPress & Theme</h3>
        <table>
            <tr><th>Komponente</th><th>Version vorher</th><th>Version nachher</th></tr>
            @if($report->wp_version_before || $report->wp_version_after)
                <tr>
                    <td><strong>WordPress Core</strong></td>
                    <td>{{ $report->wp_version_before ?? '-' }}</td>
                    <td>{{ $report->wp_version_after ?? '-' }}</td>
                </tr>
            @endif
            @if($report->theme_name)
                <tr>
                    <td><strong>Theme:</strong> {{ $report->theme_name }}</td>
                    <td>{{ $report->theme_version_before ?? '-' }}</td>
                    <td>{{ $report->theme_version_after ?? '-' }}</td>
                </tr>
            @endif
        </table>

        @if($report->pluginUpdates->count() > 0)
            <h3>Plugins</h3>
            <table>
                <tr><th>Plugin</th><th>Vorher</th><th>Nachher</th><th>Status</th></tr>
                @foreach($report->pluginUpdates as $plugin)
                    <tr>
                        <td>{{ $plugin->plugin_name }}</td>
                        <td>{{ $plugin->version_before ?? '-' }}</td>
                        <td>{{ $plugin->version_after ?? '-' }}</td>
                        <td>
                            @if($plugin->status === 'updated')
                                <span class="status-updated">Aktualisiert</span>
                            @elseif($plugin->status === 'skipped')
                                <span class="status-skipped">Übersprungen</span>
                            @elseif($plugin->status === 'no_access')
                                <span class="status-no-access">Kein Zugang</span>
                            @endif
                            @if($plugin->notes)
                                <br><small style="color: #6b7280;">{{ $plugin->notes }}</small>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </table>
        @endif

        <h2 style="color: {{ $primaryColor }}; border-bottom: 2px solid {{ $primaryColor }}; padding-bottom: 5px; margin-top: 25px; font-size: 14pt;">Phase 3 - Funktionsprüfungen</h2>
        <table>
            <tr><th style="width: 70%;">Prüfung</th><th>Ergebnis</th></tr>
            @if($report->check_frontend !== null)
                <tr>
                    <td>Startseite / Frontend</td>
                    <td style="color: {{ $report->check_frontend ? '#059669' : '#dc2626' }}; font-weight: bold;">{{ $report->check_frontend ? 'OK' : 'Fehler' }}</td>
                </tr>
            @endif
            <tr>
                <td>Navigation / Menüs</td>
                <td style="color: {{ $report->check_navigation ? '#059669' : '#dc2626' }}; font-weight: bold;">{{ $report->check_navigation ? 'OK' : 'Fehler' }}</td>
            </tr>
            <tr>
                <td>Kontaktformulare</td>
                <td style="color: {{ $report->check_forms ? '#059669' : '#dc2626' }}; font-weight: bold;">{{ $report->check_forms ? 'OK' : 'Fehler' }}</td>
            </tr>
            <tr>
                <td>Mobile / Responsive Design</td>
                <td style="color: {{ $report->check_responsive ? '#059669' : '#dc2626' }}; font-weight: bold;">{{ $report->check_responsive ? 'OK' : 'Fehler' }}</td>
            </tr>
            <tr>
                <td>Admin-Login</td>
                <td style="color: {{ $report->check_admin_login ? '#059669' : '#dc2626' }}; font-weight: bold;">{{ $report->check_admin_login ? 'OK' : 'Fehler' }}</td>
            </tr>
            <tr>
                <td>Medien-Upload</td>
                <td style="color: {{ $report->check_media_upload ? '#059669' : '#dc2626' }}; font-weight: bold;">{{ $report->check_media_upload ? 'OK' : 'Fehler' }}</td>
            </tr>
            <tr>
                <td>Keine Log-Fehler</td>
                <td style="color: {{ $report->check_no_errors ? '#059669' : '#dc2626' }}; font-weight: bold;">{{ $report->check_no_errors ? 'OK' : 'Fehler' }}</td>
            </tr>

            <tr>
                <td>SSL / HTTPS</td>
                <td style="color: {{ $report->check_ssl ? '#059669' : '#dc2626' }}; font-weight: bold;">{{ $report->check_ssl ? 'OK' : 'Fehler' }}</td>
            </tr>
            <tr>
                <td>Sicherheitsscan</td>
                <td style="color: {{ $report->check_security ? '#059669' : '#dc2626' }}; font-weight: bold;">{{ $report->check_security ? 'OK' : 'Fehler' }}</td>
            </tr>
        </table>

        @if($report->loading_time)
            <p><strong>Ladezeit der Startseite:</strong> {{ $report->loading_time }} Sekunden</p>
        @endif

        @if($report->security_plugin || $report->security_issues_count || $report->brute_force_attacks_week)
            <h2 style="color: {{ $primaryColor }}; border-bottom: 2px solid {{ $primaryColor }}; padding-bottom: 5px; margin-top: 25px; font-size: 14pt;">Sicherheitsanalyse</h2>
            <table>
                @if($report->security_plugin)
                    <tr>
                        <td style="width: 40%;"><strong>Security Plugin</strong></td>
                        <td>{{ ucfirst($report->security_plugin) }}</td>
                    </tr>
                @endif
                @if($report->firewall_status !== null)
                    <tr>
                        <td><strong>Firewall Status</strong></td>
                        <td>
                            @if($report->firewall_status >= 75)
                                <span class="check-ok">{{ $report->firewall_status }}%</span>
                            @elseif($report->firewall_status >= 50)
                                <span style="color: #d97706;">{{ $report->firewall_status }}%</span>
                            @else
                                <span class="check-fail">{{ $report->firewall_status }}%</span>
                            @endif
                        </td>
                    </tr>
                @endif
                @if($report->security_issues_count !== null)
                    <tr>
                        <td><strong>Sicherheitsprobleme</strong></td>
                        <td class="{{ $report->security_issues_count > 0 ? 'check-fail' : 'check-ok' }}">
                            {{ $report->security_issues_count }} Problem(e) gefunden
                        </td>
                    </tr>
                @endif
                @if($report->brute_force_attacks_day || $report->brute_force_attacks_week)
                    <tr>
                        <td><strong>Brute-Force Angriffe</strong></td>
                        <td>
                            @if($report->brute_force_attacks_day) Heute: {{ number_format($report->brute_force_attacks_day) }} @endif
                            @if($report->brute_force_attacks_day && $report->brute_force_attacks_week) | @endif
                            @if($report->brute_force_attacks_week) Diese Woche: {{ number_format($report->brute_force_attacks_week) }} @endif
                        </td>
                    </tr>
                @endif
            </table>

            @if($report->security_issues_details)
                <h3>Sicherheitsprobleme (Details)</h3>
                <div style="border: 1px solid #ef4444; padding: 15px; margin: 10px 0;">
                    {!! nl2br(e($report->security_issues_details)) !!}
                </div>
            @endif

            @if($report->firewall_notes)
                <h3>Firewall Konfiguration</h3>
                <div style="border: 1px solid #64748b; padding: 15px; margin: 10px 0;">
                    {!! nl2br(e($report->firewall_notes)) !!}
                </div>
            @endif

            @if($report->security_actions_taken)
                <h3>Durchgeführte Sicherheitsmaßnahmen</h3>
                <div style="border: 1px solid #10b981; padding: 15px; margin: 10px 0;">
                    {!! nl2br(e($report->security_actions_taken)) !!}
                </div>
            @endif
        @endif

        @if($report->issues_found)
            <h2 style="color: {{ $primaryColor }}; border-bottom: 2px solid {{ $primaryColor }}; padding-bottom: 5px; margin-top: 25px; font-size: 14pt;">Festgestellte Probleme</h2>
            <div style="border: 1px solid #ef4444; padding: 15px; margin: 10px 0;">
                {!! nl2br(e($report->issues_found)) !!}
            </div>
        @endif

        @if($report->recommendations()->count() > 0)
            <h2 style="color: {{ $primaryColor }}; border-bottom: 2px solid {{ $primaryColor }}; padding-bottom: 5px; margin-top: 25px; font-size: 14pt;">Empfehlungen für den Kunden</h2>
            @php
                $priorityColors = [
                    'critical' => ['border' => '#ef4444', 'text' => '#991b1b'],
                    'high' => ['border' => '#f97316', 'text' => '#9a3412'],
                    'medium' => ['border' => '#eab308', 'text' => '#854d0e'],
                    'low' => ['border' => '#3b82f6', 'text' => '#1e40af'],
                ];
                $priorityLabels = ['critical' => 'Kritisch', 'high' => 'Hoch', 'medium' => 'Mittel', 'low' => 'Niedrig'];
                $typeLabels = ['security' => 'Sicherheit', 'plugin' => 'Plugin', 'theme' => 'Theme', 'performance' => 'Performance', 'other' => 'Sonstige'];
                $actionLabels = ['replace' => 'Ersetzen', 'remove' => 'Entfernen', 'update' => 'Aktualisieren', 'configure' => 'Konfigurieren', 'install' => 'Installieren'];
            @endphp
            @foreach($report->recommendations()->get() as $rec)
                @php $colors = $priorityColors[$rec->priority] ?? $priorityColors['medium']; @endphp
                <div style="border: 1px solid {{ $colors['border'] }}; padding: 15px; margin: 10px 0; border-radius: 4px;">
                    <div style="display: table; width: 100%;">
                        <div style="display: table-cell; vertical-align: top;">
                            <strong style="color: {{ $colors['text'] }}; font-size: 12pt;">{{ $rec->title }}</strong>
                        </div>
                        <div style="display: table-cell; text-align: right; vertical-align: top; width: 120px;">
                            <span style="border: 1px solid {{ $colors['border'] }}; color: {{ $colors['border'] }}; padding: 2px 8px; border-radius: 4px; font-size: 9pt; font-weight: bold;">{{ $priorityLabels[$rec->priority] ?? 'Mittel' }}</span>
                        </div>
                    </div>
                    <div style="margin-top: 8px; font-size: 10pt; color: #6b7280;">
                        <span style="border: 1px solid #9ca3af; padding: 2px 6px; border-radius: 3px; font-size: 9pt;">{{ $typeLabels[$rec->type] ?? $rec->type }}</span>
                        @if($rec->action)
                            <span style="margin-left: 5px;">→ {{ $actionLabels[$rec->action] ?? $rec->action }}</span>
                        @endif
                    </div>
                    @if($rec->current_item || $rec->suggested_item)
                        <div style="margin-top: 10px; font-size: 10pt;">
                            @if($rec->current_item)
                                <div><strong>Aktuell:</strong> {{ $rec->current_item }}</div>
                            @endif
                            @if($rec->suggested_item)
                                <div style="color: #059669;"><strong>Alternative:</strong> {{ $rec->suggested_item }}</div>
                            @endif
                        </div>
                    @endif
                    @if($rec->description)
                        <div style="margin-top: 10px; font-size: 10pt; color: #374151;">
                            {!! nl2br(e($rec->description)) !!}
                        </div>
                    @endif
                </div>
            @endforeach
        @endif

        @if($report->recommendations)
            <h2 style="color: {{ $primaryColor }}; border-bottom: 2px solid {{ $primaryColor }}; padding-bottom: 5px; margin-top: 25px; font-size: 14pt;">Zusätzliche Hinweise</h2>
            <div style="border: 1px solid #3b82f6; padding: 15px; margin: 10px 0;">
                {!! nl2br(e($report->recommendations)) !!}
            </div>
        @endif

        @if($report->next_maintenance_date)
            <h2 style="color: {{ $primaryColor }}; border-bottom: 2px solid {{ $primaryColor }}; padding-bottom: 5px; margin-top: 25px; font-size: 14pt;">Nächster Wartungstermin</h2>
            <p style="font-size: 14pt;"><strong>{{ $report->next_maintenance_date->format('d.m.Y') }}</strong></p>
        @endif
    </div>

    <div style="margin-top: 40px; padding: 20px; border-top: 2px solid {{ $primaryColor }}; text-align: center; font-size: 9pt; color: #6b7280;">
        <p><strong style="color: {{ $primaryColor }};">{{ $agency->name }}</strong> - WordPress Wartung & Entwicklung</p>
        @if($agency->address_street || $agency->address_city)
            <p>{{ $agency->address_street }}@if($agency->address_street && $agency->address_city), @endif{{ $agency->address_zip }} {{ $agency->address_city }}@if($agency->address_country), {{ $agency->address_country }}@endif</p>
        @endif
        <p>
            @if($agency->email)E-Mail: {{ $agency->email }}@endif
            @if($agency->email && $agency->website) | @endif
            @if($agency->website)Web: {{ $agency->website }}@endif
            @if($agency->phone) | Tel: {{ $agency->phone }}@endif
        </p>
        @if($agency->tax_id)
            <p>USt-IdNr.: {{ $agency->tax_id }}</p>
        @endif
        <p style="margin-top: 10px; font-size: 8pt;">Bericht erstellt am: {{ now()->format('d.m.Y H:i') }} Uhr</p>
    </div>
</body>
</html>
