@php
    $agency = \App\Models\AgencySettings::instance();
    $navy = '#1B4F72';
    $lightBlue = '#D6EAF8';
    $green = '#27AE60';
    $lightGreen = '#D5F5E3';
    $red = '#E74C3C';
    $orange = '#E67E22';
    $gray = '#666666';
    $darkText = '#333333';
    $zebra = '#F8F9FA';

    // Checks list
    $checks = [
        ['label' => 'Startseite', 'value' => $report->check_frontend],
        ['label' => 'Navigation', 'value' => $report->check_navigation],
        ['label' => 'Formulare', 'value' => $report->check_forms],
        ['label' => 'Responsive Design', 'value' => $report->check_responsive],
        ['label' => 'Admin-Login', 'value' => $report->check_admin_login],
        ['label' => 'Medien-Upload', 'value' => $report->check_media_upload],
        ['label' => 'Fehlerfrei (Console)', 'value' => $report->check_no_errors],
        ['label' => 'SSL-Zertifikat', 'value' => $report->check_ssl],
        ['label' => 'Sicherheit', 'value' => $report->check_security],
    ];
    $passedCount = collect($checks)->where('value', true)->count();
    $totalChecks = count($checks);

    // Maintenance interval label
    $intervalLabels = [
        'monthly' => 'Monatlich',
        'quarterly' => 'Vierteljährlich',
        'yearly' => 'Jährlich',
        '2x_monthly' => 'Alle 2 Wochen',
    ];
    $intervalLabel = $intervalLabels[$report->website->client->maintenance_type] ?? ($report->website->client->maintenance_type_label ?? 'Standard');

    // Count total updates
    $pluginCount = $report->pluginUpdates->count();
    $hasWpUpdate = $report->wp_version_before || $report->wp_version_after;
    $hasThemeUpdate = $report->theme_name;
    $totalUpdates = $pluginCount + ($hasWpUpdate ? 1 : 0) + ($hasThemeUpdate ? 1 : 0);

    // Developer info
    $devName = $report->entwickler?->name ?? $report->user->name ?? 'N/A';
    $devPosition = $report->entwickler?->position ?? 'Entwickler';
@endphp
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Wartungsprotokoll - {{ $report->website->name }}</title>
    <style>
        @page {
            margin: 30px 40px 60px 40px;
        }
        body {
            font-family: DejaVu Sans, Arial, sans-serif;
            font-size: 10pt;
            color: {{ $darkText }};
            margin: 0;
            padding: 0;
        }

        /* Header */
        .page-header {
            position: fixed;
            top: -20px;
            right: 0;
            text-align: right;
            font-size: 9pt;
        }
        .page-header .brand { font-weight: bold; color: {{ $navy }}; }
        .page-header .sep { color: #888888; }

        /* Footer */
        .page-footer {
            position: fixed;
            bottom: -40px;
            left: 0;
            right: 0;
            text-align: center;
            font-size: 8pt;
            color: #888888;
        }

        /* Title */
        .main-title {
            font-size: 22pt;
            font-weight: bold;
            color: {{ $navy }};
            margin: 0 0 5px 0;
        }
        .sub-title {
            font-size: 12pt;
            color: {{ $gray }};
            margin: 0 0 20px 0;
        }

        /* Section headings */
        h2.section {
            font-size: 14pt;
            font-weight: bold;
            color: {{ $navy }};
            margin: 25px 0 12px 0;
            padding: 0;
        }

        /* Tables */
        table { width: 100%; border-collapse: collapse; }
        .tbl-header th {
            background-color: {{ $navy }};
            color: #ffffff;
            font-weight: bold;
            font-size: 9pt;
            padding: 8px 10px;
            text-align: left;
        }
        .tbl-row td {
            padding: 7px 10px;
            font-size: 10pt;
            border-bottom: 1px solid #e5e7eb;
        }
        .tbl-row-zebra td { background-color: {{ $zebra }}; }
        .tbl-status-ok {
            background-color: {{ $lightGreen }} !important;
            color: {{ $green }};
            font-weight: bold;
            text-align: center;
        }
        .tbl-status-fail {
            background-color: #FADBD8 !important;
            color: {{ $red }};
            font-weight: bold;
            text-align: center;
        }

        /* Info table */
        .info-table td {
            padding: 8px 12px;
            font-weight: bold;
            font-size: 10pt;
            border: 1px solid #d0d5dd;
        }
        .info-label { background-color: {{ $lightBlue }}; color: {{ $darkText }}; width: 18%; }
        .info-value { color: {{ $darkText }}; width: 32%; }
    </style>
</head>
<body>
    {{-- Fixed Header --}}
    <div class="page-header">
        <span class="brand">{{ $agency->name ?? 'Masinga Tech' }}</span>
        <span class="sep">  |  Wartungsprotokoll</span>
    </div>

    {{-- Fixed Footer --}}
    <div class="page-footer">
        {{ $agency->name ?? 'Masinga Tech' }} &mdash; Webentwicklung & Wartung
    </div>

    {{-- Title --}}
    <div style="margin-top: 10px;">
        <div class="main-title">WARTUNGSPROTOKOLL</div>
        <div class="sub-title">WordPress-Wartung und Update-Bericht</div>
    </div>

    {{-- Info Table --}}
    <table class="info-table" style="margin-bottom: 20px;">
        <tr>
            <td class="info-label">Webseite:</td>
            <td class="info-value">{{ $report->website->name }}</td>
            <td class="info-label">Kunde:</td>
            <td class="info-value">{{ $report->website->client->name }}</td>
        </tr>
        <tr>
            <td class="info-label">Wartungsdatum:</td>
            <td class="info-value">{{ $report->maintenance_date->translatedFormat('d. F Y') }}</td>
            <td class="info-label">Durchgeführt von:</td>
            <td class="info-value">{{ $devName }}</td>
        </tr>
        <tr>
            <td class="info-label">Nächste Wartung:</td>
            <td class="info-value" style="color: {{ $navy }};">
                {{ $report->next_maintenance_date ? $report->next_maintenance_date->translatedFormat('d. F Y') : 'Noch nicht festgelegt' }}
            </td>
            <td class="info-label">Wartungsintervall:</td>
            <td class="info-value">{{ $intervalLabel }}</td>
        </tr>
    </table>

    {{-- Website Condition --}}
    @if($report->website_condition)
        @php
            $conditionStyles = [
                'excellent' => ['bg' => $lightGreen, 'color' => $green, 'label' => 'Ausgezeichnet'],
                'good' => ['bg' => $lightBlue, 'color' => $navy, 'label' => 'Gut'],
                'needs_improvement' => ['bg' => '#FEF3C7', 'color' => $orange, 'label' => 'Verbesserung nötig'],
                'critical' => ['bg' => '#FADBD8', 'color' => $red, 'label' => 'Kritisch'],
            ];
            $cond = $conditionStyles[$report->website_condition] ?? $conditionStyles['good'];
        @endphp
        <div style="background-color: {{ $cond['bg'] }}; border: 1px solid {{ $cond['color'] }}; padding: 10px 15px; margin-bottom: 20px; text-align: center;">
            <strong style="color: {{ $cond['color'] }}; font-size: 12pt;">Website-Zustand: {{ $cond['label'] }}</strong>
            @if($report->website_condition_notes)
                <div style="margin-top: 5px; font-size: 9pt; color: {{ $gray }};">{{ $report->website_condition_notes }}</div>
            @endif
        </div>
    @endif

    {{-- ============================================ --}}
    {{-- SECTION 1: Vorbereitung --}}
    {{-- ============================================ --}}
    <h2 class="section">1. Vorbereitung</h2>
    <table>
        <tr class="tbl-header">
            <th style="width: 60%;">Prüfpunkt</th>
            <th>Status</th>
        </tr>
        <tr class="tbl-row">
            <td>Backup vor der Wartung</td>
            <td class="{{ $report->backup_completed ? 'tbl-status-ok' : 'tbl-status-fail' }}">
                {{ $report->backup_completed ? '✓ Ja' : '✗ Nein' }}
                @if($report->backup_completed && $report->backup_datetime)
                    — erstellt am {{ $report->backup_datetime->format('d.m.Y') }} um {{ $report->backup_datetime->format('H:i') }} Uhr
                @endif
            </td>
        </tr>
        <tr class="tbl-row tbl-row-zebra">
            <td>PHP-Kompatibilität geprüft</td>
            <td class="{{ $report->php_compatible ? 'tbl-status-ok' : 'tbl-status-fail' }}">
                {{ $report->php_compatible ? '✓ Ja — kompatibel' : '✗ Nein' }}
            </td>
        </tr>
    </table>

    {{-- ============================================ --}}
    {{-- SECTION 2: Durchgeführte Aktualisierungen --}}
    {{-- ============================================ --}}
    <h2 class="section">2. Durchgeführte Aktualisierungen</h2>
    @if($totalUpdates > 0)
        <p style="margin-bottom: 10px;">
            <strong>Insgesamt {{ $totalUpdates > 1 ? 'wurden' : 'wurde' }} {{ $totalUpdates }} Komponente{{ $totalUpdates > 1 ? 'n' : '' }} aktualisiert
            @if($hasThemeUpdate && $pluginCount > 0)
                (1 Theme, {{ $pluginCount }} Plugin{{ $pluginCount > 1 ? 's' : '' }})
            @endif
            :</strong>
        </p>
    @endif

    <table>
        <tr class="tbl-header">
            <th>Komponente</th>
            <th>Typ</th>
            <th>Vorher</th>
            <th>Nachher</th>
            <th style="width: 80px; text-align: center;">Status</th>
        </tr>
        @php $rowIndex = 0; @endphp
        @if($hasWpUpdate)
            <tr class="tbl-row {{ $rowIndex % 2 ? 'tbl-row-zebra' : '' }}">
                <td><strong>WordPress</strong></td>
                <td>Core</td>
                <td>{{ $report->wp_version_before ?? '-' }}</td>
                <td>{{ $report->wp_version_after ?? '-' }}</td>
                <td class="tbl-status-ok">✓ OK</td>
            </tr>
            @php $rowIndex++; @endphp
        @endif
        @if($hasThemeUpdate)
            <tr class="tbl-row {{ $rowIndex % 2 ? 'tbl-row-zebra' : '' }}">
                <td><strong>{{ $report->theme_name }}</strong></td>
                <td>Theme</td>
                <td>{{ $report->theme_version_before ?? '-' }}</td>
                <td>{{ $report->theme_version_after ?? '-' }}</td>
                <td class="tbl-status-ok">✓ OK</td>
            </tr>
            @php $rowIndex++; @endphp
        @endif
        @foreach($report->pluginUpdates as $plugin)
            <tr class="tbl-row {{ $rowIndex % 2 ? 'tbl-row-zebra' : '' }}">
                <td>{{ $plugin->plugin_name }}</td>
                <td>Plugin</td>
                <td>{{ $plugin->version_before ?? '-' }}</td>
                <td>{{ $plugin->version_after ?? '-' }}</td>
                <td class="{{ $plugin->status === 'updated' ? 'tbl-status-ok' : ($plugin->status === 'skipped' ? 'tbl-status-fail' : 'tbl-status-fail') }}">
                    @if($plugin->status === 'updated')
                        ✓ OK
                    @elseif($plugin->status === 'skipped')
                        Übersprungen
                    @else
                        Kein Zugang
                    @endif
                </td>
            </tr>
            @php $rowIndex++; @endphp
        @endforeach
    </table>

    {{-- ============================================ --}}
    {{-- SECTION 3: Funktionsprüfungen --}}
    {{-- ============================================ --}}
    <h2 class="section">3. Funktionsprüfungen nach dem Update</h2>
    <p style="margin-bottom: 10px;">Nach den Aktualisierungen wurden folgende Funktionsprüfungen durchgeführt:</p>

    <table>
        <tr class="tbl-header">
            <th style="width: 40px;">Nr.</th>
            <th>Prüfpunkt</th>
            <th>Ergebnis</th>
            <th style="width: 80px; text-align: center;">Status</th>
        </tr>
        @foreach($checks as $i => $check)
            <tr class="tbl-row {{ $i % 2 ? 'tbl-row-zebra' : '' }}">
                <td>{{ $i + 1 }}</td>
                <td>{{ $check['label'] }}</td>
                <td>{{ $check['value'] ? 'Bestanden' : 'Nicht bestanden' }}</td>
                <td class="{{ $check['value'] ? 'tbl-status-ok' : 'tbl-status-fail' }}">
                    {{ $check['value'] ? '✓ OK' : '✗ Fehler' }}
                </td>
            </tr>
        @endforeach
    </table>

    <p style="margin-top: 10px;">
        <strong style="color: {{ $passedCount === $totalChecks ? $green : $red }}; font-size: 11pt;">
            Gesamtergebnis: {{ $passedCount }} von {{ $totalChecks }} Prüfungen bestanden
            {{ $passedCount === $totalChecks ? '✓' : '' }}
        </strong>
    </p>

    {{-- ============================================ --}}
    {{-- SECTION 4: Leistungsmessung --}}
    {{-- ============================================ --}}
    @if($report->loading_time)
        <h2 class="section">4. Leistungsmessung</h2>
        @php
            $loadTime = $report->loading_time;
            if ($loadTime <= 1) {
                $loadColor = $green;
                $loadLabel = 'GUT';
                $loadBg = $lightGreen;
            } elseif ($loadTime <= 2.5) {
                $loadColor = $orange;
                $loadLabel = 'VERBESSERUNGSFÄHIG';
                $loadBg = '#FEF3C7';
            } else {
                $loadColor = $red;
                $loadLabel = 'SCHLECHT';
                $loadBg = '#FADBD8';
            }
        @endphp
        <table>
            <tr class="tbl-header">
                <th>Metrik</th>
                <th>Gemessen</th>
                <th>Zielwert</th>
                <th style="width: 120px; text-align: center;">Bewertung</th>
            </tr>
            <tr class="tbl-row">
                <td>Gesamte Ladezeit</td>
                <td style="color: {{ $loadColor }}; font-weight: bold;">{{ number_format($loadTime * 1000, 0, ',', '.') }} ms</td>
                <td>&lt; 1.000 ms</td>
                <td style="background-color: {{ $loadColor }}; color: #ffffff; font-weight: bold; text-align: center;">{{ $loadLabel }}</td>
            </tr>
        </table>
    @endif

    {{-- ============================================ --}}
    {{-- SECTION 5: Sicherheit --}}
    {{-- ============================================ --}}
    @if($report->security_plugin || $report->security_issues_count !== null || $report->brute_force_attacks_week || $report->firewall_status !== null)
        <h2 class="section">{{ $report->loading_time ? '5' : '4' }}. Sicherheit{{ $report->security_plugin ? ' (' . ucfirst($report->security_plugin) . ')' : '' }}</h2>
        <table>
            <tr class="tbl-header">
                <th>Sicherheitskomponente</th>
                <th>Wert</th>
                <th style="width: 200px;">Bewertung</th>
            </tr>
            @php $secRow = 0; @endphp
            @if($report->firewall_status !== null)
                @php
                    $fwColor = $report->firewall_status >= 75 ? $green : ($report->firewall_status >= 50 ? $orange : $red);
                    $fwLabel = $report->firewall_status >= 75 ? 'Gut' : ($report->firewall_status >= 50 ? 'Verbesserungsfähig' : 'Kritisch');
                @endphp
                <tr class="tbl-row {{ $secRow % 2 ? 'tbl-row-zebra' : '' }}">
                    <td>Firewall-Score</td>
                    <td style="color: {{ $fwColor }}; font-weight: bold;">{{ $report->firewall_status }}%</td>
                    <td style="color: {{ $fwColor }};">{{ $fwLabel }}</td>
                </tr>
                @php $secRow++; @endphp
            @endif
            @if($report->security_issues_count !== null)
                @php
                    $issColor = $report->security_issues_count === 0 ? $green : ($report->security_issues_count <= 3 ? $orange : $red);
                @endphp
                <tr class="tbl-row {{ $secRow % 2 ? 'tbl-row-zebra' : '' }}">
                    <td>Scan-Ergebnisse</td>
                    <td style="color: {{ $issColor }}; font-weight: bold;">{{ $report->security_issues_count }} Fund(e)</td>
                    <td style="color: {{ $issColor }};">{{ $report->security_issues_count === 0 ? 'Keine Probleme' : ($report->security_issues_count <= 3 ? 'Niedrig' : 'Hoch — Handlungsbedarf') }}</td>
                </tr>
                @php $secRow++; @endphp
            @endif
            @if($report->brute_force_attacks_day || $report->brute_force_attacks_week)
                @php
                    $bfWeek = $report->brute_force_attacks_week ?? 0;
                    $bfColor = $bfWeek > 1000 ? $red : ($bfWeek > 100 ? $orange : $green);
                @endphp
                <tr class="tbl-row {{ $secRow % 2 ? 'tbl-row-zebra' : '' }}">
                    <td>Blockierte Angriffe (Woche)</td>
                    <td style="color: {{ $bfColor }}; font-weight: bold;">{{ number_format($bfWeek, 0, ',', '.') }}</td>
                    <td style="color: {{ $bfColor }};">
                        @if($report->brute_force_attacks_day)
                            Davon {{ number_format($report->brute_force_attacks_day, 0, ',', '.') }} heute
                        @else
                            {{ $bfWeek > 1000 ? 'Hoch — Beobachten' : 'Normal' }}
                        @endif
                    </td>
                </tr>
                @php $secRow++; @endphp
            @endif
        </table>

        @if($report->security_issues_details)
            <div style="padding: 10px 0; font-size: 9pt;">
                <strong style="color: {{ $red }};">Sicherheitshinweis:</strong> {!! nl2br(e($report->security_issues_details)) !!}
            </div>
        @endif

        @if($report->security_actions_taken)
            <div style="padding: 10px 0; font-size: 9pt;">
                <strong style="color: {{ $green }};">Durchgeführte Maßnahmen:</strong> {!! nl2br(e($report->security_actions_taken)) !!}
            </div>
        @endif
    @endif

    {{-- ============================================ --}}
    {{-- SECTION 6: Empfehlungen --}}
    {{-- ============================================ --}}
    @php
        $sectionNum = 4;
        if ($report->loading_time) $sectionNum++;
        if ($report->security_plugin || $report->security_issues_count !== null || $report->brute_force_attacks_week || $report->firewall_status !== null) $sectionNum++;
        $recommendations = $report->recommendations()->get();
    @endphp

    @if($report->issues_found || $recommendations->count() > 0)
        <h2 class="section">{{ $sectionNum }}. Festgestellte Probleme und Empfehlungen</h2>

        @if($report->issues_found)
            <p style="margin-bottom: 10px;">{!! nl2br(e($report->issues_found)) !!}</p>
        @endif

        @if($recommendations->count() > 0)
            @php
                $priorityLabels = ['critical' => 'Kritisch', 'high' => 'Hoch', 'medium' => 'Mittel', 'low' => 'Niedrig'];
                $actionLabels = ['replace' => 'Ersetzen', 'remove' => 'Entfernen', 'update' => 'Aktualisieren', 'configure' => 'Konfigurieren', 'install' => 'Installieren'];
            @endphp
            <table>
                <tr class="tbl-header">
                    <th style="width: 30px;">Pr.</th>
                    <th>Maßnahme</th>
                    <th style="width: 100px;">Aktion</th>
                    <th style="width: 100px;">Priorität</th>
                </tr>
                @foreach($recommendations as $i => $rec)
                    @php
                        $prColor = match($rec->priority) {
                            'critical' => $red,
                            'high' => $orange,
                            'low' => '#3b82f6',
                            default => $darkText,
                        };
                    @endphp
                    <tr class="tbl-row {{ $i % 2 ? 'tbl-row-zebra' : '' }}">
                        <td>{{ $i + 1 }}</td>
                        <td>
                            <strong>{{ $rec->title }}</strong>
                            @if($rec->description)
                                <br><span style="font-size: 9pt; color: {{ $gray }};">{{ $rec->description }}</span>
                            @endif
                            @if($rec->current_item && $rec->suggested_item)
                                <br><span style="font-size: 9pt;">{{ $rec->current_item }} → {{ $rec->suggested_item }}</span>
                            @endif
                        </td>
                        <td>{{ $actionLabels[$rec->action] ?? '-' }}</td>
                        <td style="color: {{ $prColor }}; font-weight: bold;">{{ $priorityLabels[$rec->priority] ?? 'Mittel' }}</td>
                    </tr>
                @endforeach
            </table>
        @endif
    @endif

    {{-- Additional notes --}}
    @if($report->recommendations)
        <div style="padding: 10px 0; font-size: 9pt;">
            <strong style="color: {{ $navy }};">Zusätzliche Hinweise:</strong><br>
            {!! nl2br(e($report->recommendations)) !!}
        </div>
    @endif

    {{-- ============================================ --}}
    {{-- SIGNATURE --}}
    {{-- ============================================ --}}
    <div style="margin-top: 40px; border-top: 1px solid #d0d5dd; padding-top: 15px;">
        <table style="border: none;">
            <tr>
                <td style="width: 50%; border: none; padding: 0; vertical-align: top;">
                    @if($report->signature)
                        <img src="{{ $report->signature }}" style="max-height: 60px; max-width: 200px; margin-bottom: 5px;" alt="Unterschrift"><br>
                    @endif
                    <strong>{{ $devName }}</strong><br>
                    <span style="color: {{ $gray }}; font-size: 9pt;">{{ $devPosition }}</span><br>
                    <strong style="color: {{ $navy }}; font-size: 9pt;">{{ $agency->name ?? 'Masinga Tech' }}</strong>
                </td>
                <td style="width: 50%; border: none; padding: 0; vertical-align: top; text-align: right;">
                    <strong>Datum: {{ $report->maintenance_date->translatedFormat('d. F Y') }}</strong><br>
                    @if($report->next_maintenance_date)
                        <span style="color: {{ $gray }}; font-size: 9pt;">Nächste Wartung: {{ $report->next_maintenance_date->translatedFormat('d. F Y') }}</span>
                    @endif
                </td>
            </tr>
        </table>
    </div>
</body>
</html>
