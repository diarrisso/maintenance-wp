<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: #2563eb; color: white; padding: 30px; text-align: center; }
        .content { padding: 30px; background: #f9fafb; }
        .footer { padding: 20px; text-align: center; color: #666; font-size: 14px; }
        .button { display: inline-block; padding: 12px 24px; background: #2563eb; color: white; text-decoration: none; border-radius: 5px; margin: 20px 0; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Masinga Tech</h1>
            <p>WordPress Wartungsbericht</p>
        </div>
        <div class="content">
            <p>Sehr geehrte Damen und Herren,</p>
            <p>die Wartung für Ihre Website <strong>{{ $report->website->name }}</strong> wurde am <strong>{{ $report->maintenance_date->format('d.m.Y') }}</strong> erfolgreich durchgeführt.</p>
            <p>Im Anhang finden Sie den vollständigen Wartungsbericht mit allen Details zu den durchgeführten Aktualisierungen und Prüfungen.</p>
            @if($report->next_maintenance_date)
                <p>Die nächste Wartung ist geplant für: <strong>{{ $report->next_maintenance_date->format('d.m.Y') }}</strong></p>
            @endif
            <p>Bei Fragen stehen wir Ihnen gerne zur Verfügung.</p>
            <p>Mit freundlichen Grüßen,<br><strong>Ihr Masinga Tech Team</strong></p>
        </div>
        <div class="footer">
            <p>Masinga Tech | E-Mail: info@masingatech.com | Web: www.masingatech.com</p>
        </div>
    </div>
</body>
</html>
