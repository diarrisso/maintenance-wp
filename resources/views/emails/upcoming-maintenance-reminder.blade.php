@php
    $agency = \App\Models\AgencySettings::instance();
    $primaryColor = $agency->primary_color ?? '#2563eb';
@endphp

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Wartungs-Erinnerung - {{ $agency->name }}</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 20px;">
    <!-- Header -->
    <div style="background: {{ $primaryColor }}; color: white; padding: 30px; text-align: center; border-radius: 8px 8px 0 0;">
        <h1 style="margin: 0; font-size: 24px;">⏰ Wartungs-Erinnerung</h1>
        <p style="margin: 10px 0 0; font-size: 14px; opacity: 0.9;">Morgen fällige Wartungen</p>
    </div>

    <!-- Content -->
    <div style="background: #f8fafc; padding: 30px; border-radius: 0 0 8px 8px;">
        <p style="font-size: 16px; margin-bottom: 20px;">Hallo <strong>{{ $user->name }}</strong>,</p>

        <div style="background: #fff; border-left: 4px solid {{ $primaryColor }}; padding: 20px; margin: 20px 0; border-radius: 4px;">
            <p style="margin: 0; font-size: 16px; font-weight: bold; color: {{ $primaryColor }};">
                📅 {{ $websites->count() }} Website(s) benötigen morgen eine Wartung
            </p>
            <p style="margin: 10px 0 0; font-size: 14px; color: #6b7280;">
                Datum: <strong>{{ now()->addDay()->format('d.m.Y') }}</strong>
            </p>
        </div>

        <h3 style="color: #374151; font-size: 18px; margin: 30px 0 15px;">Wartungsplan für morgen:</h3>

        @foreach($websites as $website)
            <div style="background: white; border: 1px solid #e5e7eb; padding: 15px; margin-bottom: 12px; border-radius: 6px;">
                <div style="display: flex; align-items: center; margin-bottom: 8px;">
                    <span style="font-size: 20px; margin-right: 10px;">🌐</span>
                    <strong style="font-size: 16px; color: #1f2937;">{{ $website->name }}</strong>
                </div>
                <div style="padding-left: 30px; font-size: 14px; color: #6b7280;">
                    <div style="margin-bottom: 5px;">
                        <strong>Kunde:</strong> {{ $website->client->name }}
                    </div>
                    <div style="margin-bottom: 5px;">
                        <strong>URL:</strong> <a href="{{ $website->url }}" style="color: {{ $primaryColor }};">{{ $website->url }}</a>
                    </div>
                    <div>
                        <strong>Paket:</strong>
                        <span style="background: #eff6ff; color: #1e40af; padding: 2px 8px; border-radius: 4px; font-size: 12px;">
                            {{ $website->client->maintenance_type_label ?? 'Standard' }}
                        </span>
                    </div>
                </div>
            </div>
        @endforeach

        <div style="background: #fef3c7; border: 1px solid #fbbf24; padding: 15px; margin: 25px 0; border-radius: 6px;">
            <p style="margin: 0; font-size: 14px; color: #92400e;">
                <strong>💡 Tipp:</strong> Planen Sie die Wartungen rechtzeitig ein, um Verzögerungen zu vermeiden.
            </p>
        </div>

        <div style="text-align: center; margin: 30px 0;">
            <a href="{{ url('/dashboard') }}" style="display: inline-block; background: {{ $primaryColor }}; color: white; padding: 14px 30px; text-decoration: none; border-radius: 6px; font-weight: bold; font-size: 16px;">
                Zum Dashboard
            </a>
        </div>

        <p style="font-size: 14px; color: #6b7280; margin-top: 25px;">
            Mit freundlichen Grüßen,<br>
            <strong>{{ $agency->name }}</strong>
        </p>
    </div>

    <!-- Footer -->
    <div style="text-align: center; padding: 20px; font-size: 12px; color: #9ca3af;">
        <p style="margin: 5px 0;">{{ $agency->name }} - WordPress Wartung & Entwicklung</p>
        @if($agency->email)
            <p style="margin: 5px 0;">E-Mail: <a href="mailto:{{ $agency->email }}" style="color: {{ $primaryColor }};">{{ $agency->email }}</a></p>
        @endif
        @if($agency->website)
            <p style="margin: 5px 0;">Web: <a href="{{ $agency->website }}" style="color: {{ $primaryColor }};">{{ $agency->website }}</a></p>
        @endif
        <p style="margin: 15px 0 5px; font-size: 11px; color: #d1d5db;">
            Diese E-Mail wurde automatisch generiert.
        </p>
    </div>
</body>
</html>
