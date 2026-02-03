# Guide Administrateur - Wartung App

Application de gestion de maintenance WordPress pour Masinga Tech.

---

## Connexion

- **URL** : `https://votre-domaine.com/login`
- **Identifiants par défaut** : `admin@masingatech.com` / `password`

---

## Menu Principal (Sidebar)

| Menu | Description |
|------|-------------|
| **Dashboard** | Vue d'ensemble avec statistiques et alertes |
| **Kunden** (Clients) | Gestion des clients |
| **Websites** | Gestion des sites WordPress |
| **Berichte** (Rapports) | Historique des rapports de maintenance |

---

## 1. Dashboard

Le tableau de bord affiche :

### Cartes statistiques
- **Kunden** : Nombre total de clients
- **Websites** : Nombre total de sites web
- **Anstehend (7 Tage)** : Maintenances prévues dans les 7 prochains jours
- **Überfällig** : Maintenances en retard (en rouge)

### Tableaux
- **Anstehende Wartungen** : Liste des maintenances à venir avec lien direct pour démarrer
- **Überfällige Wartungen** : Liste des maintenances en retard (urgent)
- **Letzte Berichte** : 5 derniers rapports avec statut (Entwurf/Abgeschlossen/Gesendet)

---

## 2. Gestion des Clients (Kunden)

### Liste des clients
- Recherche par nom, email ou entreprise
- Affiche le nombre de sites associés
- Actions : Voir, Modifier, Supprimer

### Créer un client
Cliquer sur **"Neuer Kunde"**

| Champ | Description |
|-------|-------------|
| Name | Nom du contact (obligatoire) |
| E-Mail | Email du client (obligatoire) |
| Telefon | Numéro de téléphone |
| Firma | Nom de l'entreprise |
| Notizen | Notes internes |

### Fiche client
Affiche les informations du client et la liste de ses sites web avec possibilité d'en ajouter directement.

---

## 3. Gestion des Sites Web (Websites)

### Liste des sites
- Recherche par nom, URL ou client
- Affiche le forfait de maintenance et la prochaine date
- Code couleur des forfaits :
  - 🔵 **Monthly** (mensuel)
  - 🟣 **Quarterly** (trimestriel)
  - 🟢 **Yearly** (annuel)
  - ⚪ **One-time** (ponctuel)

### Créer un site
Cliquer sur **"Neue Website"**

| Champ | Description |
|-------|-------------|
| Kunde | Client propriétaire (obligatoire) |
| Name | Nom du site (obligatoire) |
| URL | Adresse du site (obligatoire) |
| Hosting-Anbieter | Hébergeur (OVH, Hostinger, etc.) |
| Wartungspaket | Type de forfait maintenance |
| Nächster Wartungstermin | Prochaine date de maintenance |

### Actions rapides
- **Wartung** : Démarrer une maintenance
- **Bearbeiten** : Modifier les informations

---

## 4. Processus de Maintenance

### Étape 1 : Démarrer la maintenance
Depuis la liste des sites ou le dashboard, cliquer sur **"Wartung"** ou l'icône flèche.

### Étape 2 : Remplir la checklist

#### Phase 1 - Vorbereitung (Préparation)
- [ ] Backup durchgeführt (Backup effectué)
- Backup Datum/Uhrzeit (Date/heure du backup)
- [ ] PHP-Version kompatibel (PHP compatible)

#### Phase 2 - Aktualisierungen (Mises à jour)
| Élément | Avant | Après |
|---------|-------|-------|
| WordPress Version | ex: 6.4.1 | ex: 6.4.2 |
| Theme | ex: Flavor 2.0 | ex: Flavor 2.1 |
| Plugins | Ajouter dynamiquement avec le bouton "Plugin hinzufügen" |

#### Phase 3 - Prüfungen (Vérifications)
- [ ] Startseite OK (Page d'accueil)
- [ ] Navigation/Menüs OK
- [ ] Kontaktformulare OK (Formulaires)
- [ ] Mobile/Responsive OK
- [ ] Admin-Login OK
- [ ] Medien-Upload OK
- [ ] Keine Log-Fehler (Pas d'erreurs)
- [ ] WooCommerce OK (si applicable)
- [ ] SSL/HTTPS OK
- [ ] Sicherheitsscan OK (Sécurité)
- Ladezeit (Temps de chargement en secondes)

#### Notizen (Notes)
- **Festgestellte Probleme** : Problèmes rencontrés
- **Empfehlungen** : Recommandations pour le client
- **Nächster Wartungstermin** : Prochaine maintenance (calculée automatiquement selon le forfait)

### Étape 3 : Sauvegarder
- **"Als Entwurf speichern"** : Sauvegarder comme brouillon pour continuer plus tard

### Étape 4 : Compléter
- **"Abschließen"** : Marquer comme terminé → génère automatiquement le PDF

### Étape 5 : Envoyer au client
- **"E-Mail senden"** : Envoie le rapport PDF par email au client

---

## 5. Rapports (Berichte)

### Liste des rapports
Affiche tous les rapports avec :
- Site web et client
- Date de maintenance
- Technicien
- Statut :
  - **Entwurf** (Brouillon) - gris
  - **Abgeschlossen** (Terminé) - bleu
  - **Gesendet** (Envoyé) - vert

### Détail d'un rapport
- Visualisation complète de la checklist
- **Télécharger PDF** : Télécharger le rapport
- **Erneut senden** : Renvoyer l'email au client

---

## 6. Profil Utilisateur

Accessible via le menu utilisateur en bas de la sidebar.

- Modifier le nom et l'email
- Changer le mot de passe
- Se déconnecter (**Abmelden**)

---

## Raccourcis et Astuces

| Action | Raccourci |
|--------|-----------|
| Dashboard | Cliquer sur "Masinga Tech" dans le header |
| Nouvelle maintenance | Depuis le dashboard, cliquer sur la flèche à côté du site |
| Recherche | Disponible sur toutes les listes |

---

## Calcul automatique des dates

La prochaine maintenance est calculée automatiquement selon le forfait :

| Forfait | Intervalle |
|---------|------------|
| Monthly | +1 mois |
| Quarterly | +3 mois |
| Yearly | +12 mois |
| One-time | Pas de récurrence |

---

## Support

Pour toute question technique, contacter l'équipe Masinga Tech.
