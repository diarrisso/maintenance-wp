<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; margin: 0; padding: 0; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: {{ $agency->primary_color ?? '#7d5cc6' }}; color: white; padding: 30px; text-align: center; }
        .header img { max-height: 60px; margin-bottom: 15px; }
        .content { padding: 30px; background: #f9fafb; }
        .info-box { background: white; border-radius: 8px; padding: 20px; margin: 20px 0; }
        .info-row { display: flex; margin-bottom: 8px; }
        .info-label { font-weight: bold; min-width: 120px; color: #666; }
        .footer { padding: 20px; text-align: center; color: #666; font-size: 14px; }
        .button { display: inline-block; padding: 14px 28px; background: {{ $agency->primary_color ?? '#7d5cc6' }}; color: white !important; text-decoration: none; border-radius: 6px; margin: 20px 0; font-weight: bold; }
        .button:hover { opacity: 0.9; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            @if($agency->logo_url)
                <img src="{{ $agency->logo_url }}" alt="{{ $agency->name }}">
            @else
                <h1 style="margin: 0;">{{ $agency->name }}</h1>
            @endif
            <p style="margin: 10px 0 0 0; opacity: 0.9;">Benachrichtigung</p>
        </div>
        <div class="content">
            <p>Hallo {{ $manager->name }},</p>

            <p>Ein neuer Wartungsbericht ist bereit und wartet auf Ihre Freigabe zum Versand an den Kunden.</p>

            <div class="info-box">
                <table style="width: 100%; border-collapse: collapse;">
                    <tr>
                        <td style="padding: 8px 0; font-weight: bold; color: #666; width: 140px;">Website:</td>
                        <td style="padding: 8px 0;">{{ $report->website->name }}</td>
                    </tr>
                    <tr>
                        <td style="padding: 8px 0; font-weight: bold; color: #666;">Kunde:</td>
                        <td style="padding: 8px 0;">{{ $report->website->client->name }}</td>
                    </tr>
                    <tr>
                        <td style="padding: 8px 0; font-weight: bold; color: #666;">Erstellt von:</td>
                        <td style="padding: 8px 0;">{{ $entwicklerName }}</td>
                    </tr>
                    <tr>
                        <td style="padding: 8px 0; font-weight: bold; color: #666;">Wartungsdatum:</td>
                        <td style="padding: 8px 0;">{{ $report->maintenance_date->format('d.m.Y') }}</td>
                    </tr>
                </table>
            </div>

            <p style="text-align: center;">
                <a href="{{ route('reports.show', $report) }}" class="button">Bericht ansehen und senden</a>
            </p>

            <p style="color: #666; font-size: 14px;">
                Bitte prüfen Sie den Bericht und senden Sie ihn bei Freigabe an den Kunden.
            </p>

            <p>Mit freundlichen Grüßen,<br><strong>{{ $agency->name }}</strong></p>
        </div>
        <div class="footer">
            <p>{{ $agency->name }} @if($agency->email)| E-Mail: {{ $agency->email }}@endif @if($agency->website)| Web: {{ $agency->website }}@endif</p>
        </div>
    </div>
</body>
</html>
