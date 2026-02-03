# Deployment Guide - Wartung-App auf Mittwald

## Voraussetzungen

- Mittwald Account mit SSH-Zugang
- PHP 8.2+ (empfohlen: 8.3)
- MySQL 8.0+
- Node.js 18+ (für Asset-Compilation)
- Composer 2.x

---

## 1. Mittwald Konfiguration

### 1.1 SSH-Zugang einrichten

1. Mittwald Dashboard öffnen
2. **Projekt** → **SSH-Zugang** aktivieren
3. SSH-Key hochladen oder Passwort notieren

```bash
ssh p123456@p123456.mittwaldserver.info
```

### 1.2 PHP-Version einstellen

1. Mittwald Dashboard → **PHP-Einstellungen**
2. PHP-Version: **8.3**
3. Memory Limit: mindestens **256MB**

### 1.3 MySQL-Datenbank erstellen

1. Mittwald Dashboard → **Datenbanken**
2. **Neue Datenbank** erstellen
3. Zugangsdaten notieren:
   - Host: `localhost` oder `127.0.0.1`
   - Datenbankname
   - Benutzername
   - Passwort

---

## 2. Erstinstallation

### 2.1 Projekt klonen

```bash
# SSH-Verbindung herstellen
ssh p123456@p123456.mittwaldserver.info

# In das Web-Verzeichnis wechseln
cd /html

# Projekt klonen
git clone https://github.com/DEIN-REPO/wartung-app.git
cd wartung-app
```

### 2.2 Environment konfigurieren

```bash
# .env aus Vorlage erstellen
cp .env.production .env

# .env bearbeiten
nano .env
```

**Wichtige Werte anpassen:**

```env
APP_KEY=                    # Wird gleich generiert
APP_URL=https://wartung.achtzigdreissig.de

DB_DATABASE=dein_datenbankname
DB_USERNAME=dein_username
DB_PASSWORD=dein_passwort

MAIL_USERNAME=dein_mail_user
MAIL_PASSWORD=dein_mail_passwort
```

### 2.3 Installation ausführen

```bash
# Composer installieren
composer install --no-dev --optimize-autoloader

# App-Key generieren
php artisan key:generate

# NPM installieren und Assets bauen
npm ci
npm run build

# Storage-Link erstellen
php artisan storage:link

# Datenbank migrieren
php artisan migrate --force

# Admin-Benutzer erstellen
php artisan user:create --name="Mamadi Diarrisso" --email="diarrisso@achtzigdreissig.de" --role=developer
php artisan user:create --name="Natalie Lindner" --email="lindner@achtzigdreissig.de" --role=manager

# Cache optimieren
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Permissions setzen
chmod -R 775 storage bootstrap/cache
```

---

## 3. Document Root konfigurieren

### Mittwald Dashboard:

1. **Domains** → Domain auswählen
2. **Document Root** auf `/html/wartung-app/public` setzen

### Oder per .htaccess im Root:

```apache
RewriteEngine On
RewriteRule ^(.*)$ public/$1 [L]
```

---

## 4. Updates deployen

### Option A: Deploy-Script

```bash
cd /html/wartung-app
./deploy.sh production
```

### Option B: Manuell

```bash
cd /html/wartung-app

# Maintenance Mode
php artisan down

# Git Pull
git pull origin main

# Dependencies
composer install --no-dev --optimize-autoloader
npm ci && npm run build

# Migrations
php artisan migrate --force

# Cache
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Online
php artisan up
```

---

## 5. SSL/HTTPS aktivieren

Mittwald bietet kostenloses Let's Encrypt:

1. Dashboard → **SSL/TLS**
2. **Let's Encrypt** aktivieren
3. Domain auswählen
4. Automatische Erneuerung aktivieren

---

## 6. Cronjobs (optional)

Falls Laravel Scheduler benötigt wird:

```bash
# Mittwald Dashboard → Cronjobs
# Oder per SSH: crontab -e

* * * * * cd /html/wartung-app && php artisan schedule:run >> /dev/null 2>&1
```

---

## 7. Troubleshooting

### Fehler: 500 Internal Server Error

```bash
# Logs prüfen
tail -f storage/logs/laravel.log

# Permissions korrigieren
chmod -R 775 storage bootstrap/cache

# Cache leeren
php artisan cache:clear
php artisan config:clear
```

### Fehler: Assets werden nicht geladen

```bash
# Storage-Link prüfen
ls -la public/storage

# Ggf. neu erstellen
rm public/storage
php artisan storage:link
```

### Fehler: Mail wird nicht gesendet

```bash
# Mail-Konfiguration testen
php artisan tinker
>>> Mail::raw('Test', fn($m) => $m->to('test@example.com')->subject('Test'));
```

---

## 8. Backup

### Datenbank-Backup

```bash
# Manuelles Backup
mysqldump -u USERNAME -p DATABASE > backup_$(date +%Y%m%d).sql

# Mittwald bietet auch automatische Backups im Dashboard
```

### Dateien-Backup

```bash
# Storage-Ordner sichern (enthält PDFs)
tar -czf storage_backup_$(date +%Y%m%d).tar.gz storage/app/public
```

---

## Kontakt bei Problemen

- **Mittwald Support**: support@mittwald.de
- **Entwickler**: diarrisso@achtzigdreissig.de
