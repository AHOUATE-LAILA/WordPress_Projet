# 🌍 Plateforme Gaisio - Gestionnaire de Tremblements de Terre

## 📋 Description

**Gaisio** est une plateforme WordPress complète dédiée à la surveillance, l'analyse et la gestion des tremblements de terre. Développée pour l'Institut National de Géophysique (ING) et le Centre National pour la Recherche Scientifique et Technique (CNRST), elle permet aux utilisateurs de contribuer à la collecte de données sismiques et aux administrateurs de gérer la plateforme.

## ✨ Fonctionnalités Principales

### 🗺️ **Interface Publique**
- **Carte Interactive** : Visualisation des tremblements de terre sur une carte interactive
- **Données Détaillées** : Tableau complet des séismes enregistrés avec filtres et tri
- **Actualités** : Carrousel d'actualités sismiques et géologiques
- **Ressources** : Centre de documentation et publications scientifiques
- **Signalement** : Formulaire de signalement de secousses ressenties

### 👤 **Espace Utilisateur**
- **Connexion Sécurisée** : Authentification par nom d'utilisateur et code d'accès
- **Saisie de Données** : Formulaire d'enregistrement des tremblements de terre
- **Tableau de Bord** : Consultation des données personnelles et statistiques
- **Gestion de Profil** : Modification des informations personnelles

### 🛠️ **Administration**
- **Gestion des Actualités** : Ajout, modification et suppression d'articles
- **Gestion des Utilisateurs** : Création, modification et suppression de comptes
- **Statistiques** : Tableau de bord avec métriques de la plateforme
- **Téléchargement de Credentials** : Export des informations de connexion utilisateur

## 🚀 Installation

### Prérequis
- WordPress 5.0+
- PHP 7.4+
- MySQL 5.7+
- Serveur web (Apache/Nginx)

### Étapes d'Installation
1. **Télécharger** le plugin dans `wp-content/plugins/`
2. **Activer** le plugin depuis l'administration WordPress
3. **Configurer** les permissions utilisateur si nécessaire
4. **Créer** les pages avec les shortcodes appropriés

## 📱 Shortcodes Disponibles

### Interface Publique
```php
[gaisio_public_home]     // Page d'accueil complète avec menu
[gaisio_earthquake_map]  // Carte interactive des tremblements
[gaisio_earthquake_table] // Tableau des données sismiques
[gaisio_footer]          // Pied de page de la plateforme
```

### Espace Utilisateur
```php
[gaisio_user_dashboard]  // Interface de connexion (Admin/Utilisateur)
[gaisio_earthquake_form] // Formulaire de saisie des séismes
```

### Administration
```php
[gaisio_admin]           // Interface d'administration complète
```

## 🗄️ Structure de la Base de Données

### Table `wp_gaisio_users`
```sql
- id (AUTO_INCREMENT)
- user_id (ID WordPress)
- username (Nom d'utilisateur)
- email (Adresse email)
- access_code (Code d'accès généré)
- created_at (Date de création)
```

### Table `wp_gaisio_earthquakes`
```sql
- id (AUTO_INCREMENT)
- user_id (ID de l'utilisateur)
- datetime_utc (Date/heure UTC)
- latitude (Latitude décimale)
- longitude (Longitude décimale)
- depth (Profondeur en km)
- magnitude (Magnitude Richter)
- created_at (Date d'enregistrement)
```

### Table `wp_gaisio_news`
```sql
- id (AUTO_INCREMENT)
- title (Titre de l'actualité)
- content (Contenu de l'article)
- image_url (URL de l'image)
- created_at (Date de création)
```

## 🎨 Personnalisation

### Fichiers CSS
- `css/gaisio-earthquake.css` : Styles de l'interface publique
- `css/gaisio-admin.css` : Styles de l'administration

### Fichiers JavaScript
- `js/gaisio-earthquake.js` : Logique de l'interface publique
- `js/gaisio-admin.js` : Logique de l'administration

### Images
- `assets/images/logo-morseps2.png` : Logo principal de la plateforme

## 🔐 Sécurité

- **Protection CSRF** : Nonces WordPress pour tous les formulaires
- **Validation des Données** : Sanitisation et vérification des entrées
- **Gestion des Permissions** : Vérification des capacités utilisateur
- **Authentification Sécurisée** : Système de connexion robuste

## 📊 Fonctionnalités Techniques

### AJAX
- Chargement dynamique des données
- Mise à jour en temps réel
- Gestion des formulaires sans rechargement

### Responsive Design
- Interface adaptée mobile/desktop
- Navigation tactile optimisée
- Grilles flexibles

### Performance
- Chargement asynchrone des données
- Optimisation des requêtes base de données
- Cache des ressources statiques

## 🌐 Support Multilingue

- **Français** : Langue principale
- **Interface unifiée** : Pas de changement de langue
- **Traductions intégrées** : Tous les textes en français

## 🔧 Maintenance

### Logs
- Enregistrement des erreurs
- Suivi des actions utilisateur
- Debug des fonctionnalités

### Sauvegarde
- Tables personnalisées
- Configuration du plugin
- Données utilisateur

## 📞 Support

### Équipe de Développement
- **Daba Kandoz Stag** - Institut National de Recherche Scientifique et Technique
- **Contact** : Via l'administration de la plateforme

### Documentation
- Ce fichier README
- Code source commenté
- Exemples d'utilisation

## 📈 Évolutions Futures

- [ ] API REST pour intégrations externes
- [ ] Notifications en temps réel
- [ ] Analyse prédictive des séismes
- [ ] Intégration avec d'autres systèmes sismiques
- [ ] Application mobile dédiée

## 📄 Licence

© 2024 - Centre National pour la Recherche Scientifique et Technique (CNRST) et Institut National de Géophysique (ING)

---

**Version** : 2.0.0  
**Dernière mise à jour** : Décembre 2024  
**Compatibilité** : WordPress 5.0+ 