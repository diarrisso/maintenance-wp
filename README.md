# WordPress Wartungs-App für Masinga Tech

Eine vollständige Laravel 11 Webanwendung zur Verwaltung von WordPress-Wartungsdiensten.

## Features

- Dashboard mit Statistiken und Übersicht
- Kundenverwaltung (CRUD)
- Website-Verwaltung mit Wartungspaketen
- Interaktive Wartungs-Checkliste mit dynamischem Plugin-Formular
- Automatische PDF-Generierung der Wartungsberichte
- Automatischer E-Mail-Versand an Kunden
- Automatische Berechnung des nächsten Wartungstermins
- Moderne UI mit Tailwind CSS und Masinga Tech Branding
- Responsive Design mit Sidebar-Navigation

## Installation

### 1. Abhängigkeiten installieren

```bash
composer install
npm install
```

### 2. Umgebungskonfiguration

```bash
cp .env.example .env
php artisan key:generate
```

### 3. Datenbank konfigurieren

Bearbeiten Sie die `.env`-Datei:

```env
DB_CONNECTION=sqlite
# Oder für MySQL:
# DB_CONNECTION=mysql
# DB_HOST=127.0.0.1
# DB_PORT=3306
# DB_DATABASE=wartung_app
# DB_USERNAME=root
# DB_PASSWORD=
```

### 4. Mail-Konfiguration (Mailtrap für Tests)

```env
MAIL_MAILER=smtp
MAIL_HOST=sandbox.smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=your_username
MAIL_PASSWORD=your_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@masingatech.com
MAIL_FROM_NAME="Masinga Tech"
```

### 5. Datenbank migrieren und Seeds ausführen

```bash
php artisan migrate --seed
```

Dies erstellt:
- Einen Test-Benutzer: `admin@masingatech.com` / `password`
- 2 Test-Kunden
- 2 Test-Websites

### 6. Assets kompilieren

```bash
npm run build
# Oder für Entwicklung:
npm run dev
```

### 7. Server starten

```bash
php artisan serve
```

Die App ist nun unter `http://localhost:8000` erreichbar.

## Verwendung

### Login
- E-Mail: `admin@masingatech.com`
- Passwort: `password`

### Workflow

1. **Kunde anlegen**: Navigieren Sie zu "Kunden" und erstellen Sie einen neuen Kunden
2. **Website hinzufügen**: Fügen Sie dem Kunden eine oder mehrere Websites hinzu
3. **Wartung durchführen**:
   - Gehen Sie zur Website
   - Klicken Sie auf "Wartung starten"
   - Füllen Sie die Checkliste aus (inkl. dynamisches Plugin-Formular)
   - Speichern Sie als Entwurf oder schließen Sie direkt ab
4. **Abschließen & Senden**:
   - Das System generiert automatisch ein PDF
   - Versendet es per E-Mail an den Kunden
   - Berechnet den nächsten Wartungstermin
   - Aktualisiert den Website-Status

### Dashboard

Das Dashboard zeigt:
- Anzahl Kunden und Websites
- Anstehende Wartungen (nächste 7 Tage)
- Überfällige Wartungen
- Letzte 5 Berichte

### Berichte

Alle Wartungsberichte können:
- Als PDF heruntergeladen werden
- Erneut per E-Mail verschickt werden
- Im Detail angesehen werden

## Wartungspakete

Die App unterstützt folgende Wartungspakete:
- **Monatlich**: Nächste Wartung in 1 Monat
- **Vierteljährlich**: Nächste Wartung in 3 Monaten
- **Jährlich**: Nächste Wartung in 1 Jahr
- **Einmalig**: Keine automatische Berechnung

## Technische Details

- **Framework**: Laravel 11
- **Frontend**: Blade Templates, Tailwind CSS v3, Alpine.js
- **Auth**: Laravel Breeze
- **PDF**: DomPDF
- **Mail**: Laravel Mail mit Mailtrap
- **Datenbank**: SQLite (Standard) oder MySQL

## Datenbankstruktur

- **clients**: Kundeninformationen
- **websites**: Website-Daten mit Wartungspaket
- **maintenance_reports**: Wartungsberichte mit kompletter Checkliste
- **plugin_updates**: Dynamische Plugin-Update-Einträge

## Farbschema (Masinga Tech)

- Primärfarbe: `#2563eb` (Blau)
- Sekundärfarbe: `#1e40af` (Dunkelblau)
- Erfolgsfarbe: `#10b981` (Grün)

## Support

Bei Fragen oder Problemen wenden Sie sich an das Masinga Tech Team.
