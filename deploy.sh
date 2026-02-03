#!/bin/bash

#############################################
# Wartung-App Deployment Script für Mittwald
#
# Usage: ./deploy.sh [production|staging]
#############################################

set -e

# Couleurs pour les messages
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Configuration
ENVIRONMENT=${1:-production}
APP_NAME="wartung-app"

echo -e "${BLUE}========================================${NC}"
echo -e "${BLUE}  Wartung-App Deployment - Mittwald${NC}"
echo -e "${BLUE}  Environment: ${YELLOW}${ENVIRONMENT}${NC}"
echo -e "${BLUE}========================================${NC}"

# Vérification de l'environnement
if [ "$ENVIRONMENT" != "production" ] && [ "$ENVIRONMENT" != "staging" ]; then
    echo -e "${RED}Error: Environment must be 'production' or 'staging'${NC}"
    exit 1
fi

# Fonction pour afficher les étapes
step() {
    echo -e "\n${GREEN}▶ $1${NC}"
}

# Fonction pour afficher les warnings
warn() {
    echo -e "${YELLOW}⚠ $1${NC}"
}

# Fonction pour afficher les erreurs
error() {
    echo -e "${RED}✖ $1${NC}"
    exit 1
}

# Fonction pour afficher le succès
success() {
    echo -e "${GREEN}✔ $1${NC}"
}

#############################################
# 1. Mode maintenance ON
#############################################
step "Activation du mode maintenance..."
php artisan down --retry=60 --refresh=15 || true
success "Mode maintenance activé"

#############################################
# 2. Pull des dernières modifications
#############################################
step "Récupération des dernières modifications Git..."
git fetch origin
git reset --hard origin/main
success "Code mis à jour"

#############################################
# 3. Installation des dépendances PHP
#############################################
step "Installation des dépendances Composer..."
composer install --no-interaction --prefer-dist --optimize-autoloader --no-dev
success "Dépendances PHP installées"

#############################################
# 4. Installation et build des assets
#############################################
step "Installation des dépendances NPM..."
npm ci --silent
success "Dépendances NPM installées"

step "Build des assets de production..."
npm run build
success "Assets compilés"

#############################################
# 5. Optimisation Laravel
#############################################
step "Optimisation de Laravel..."

# Clear tous les caches
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear

# Rebuild les caches pour la production
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache

success "Laravel optimisé"

#############################################
# 6. Migrations de base de données
#############################################
step "Exécution des migrations..."
php artisan migrate --force
success "Migrations exécutées"

#############################################
# 7. Storage link
#############################################
step "Création du lien symbolique storage..."
if [ ! -L "public/storage" ]; then
    php artisan storage:link
    success "Lien storage créé"
else
    success "Lien storage déjà existant"
fi

#############################################
# 8. Permissions des fichiers
#############################################
step "Configuration des permissions..."
chmod -R 775 storage bootstrap/cache
success "Permissions configurées"

#############################################
# 9. Restart des services (si queue worker)
#############################################
step "Redémarrage des workers..."
php artisan queue:restart || warn "Pas de queue worker actif"

#############################################
# 10. Mode maintenance OFF
#############################################
step "Désactivation du mode maintenance..."
php artisan up
success "Application en ligne"

#############################################
# Résumé
#############################################
echo -e "\n${GREEN}========================================${NC}"
echo -e "${GREEN}  Déploiement terminé avec succès!${NC}"
echo -e "${GREEN}========================================${NC}"
echo -e "Environment: ${YELLOW}${ENVIRONMENT}${NC}"
echo -e "Date: $(date '+%Y-%m-%d %H:%M:%S')"
echo -e "${GREEN}========================================${NC}\n"
