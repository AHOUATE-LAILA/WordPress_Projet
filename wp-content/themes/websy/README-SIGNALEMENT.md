# Section de Signalement - Thème Websy

## Description
Ce thème WordPress inclut maintenant une section de signalement complète pour les événements sismiques, similaire à l'image de référence. La section comprend un bouton d'appel à l'action et un formulaire modal complet.

## Fonctionnalités
- **Section de signalement** : Bannière attractive avec dégradé orange-rouge
- **Bouton d'action** : Bouton blanc avec icône de cloche
- **Formulaire modal** : Formulaire complet dans une fenêtre popup
- **Type de post personnalisé** : Stockage des signalements dans WordPress
- **Validation et sécurité** : Protection CSRF et validation des données
- **Responsive** : S'adapte à tous les écrans

## Shortcodes Disponibles

### 1. Section Complète : `[signalement_section]`
Affiche la bannière complète avec le bouton et le modal.

**Utilisation de base :**
```
[signalement_section]
```

**Avec attributs personnalisés :**
```
[signalement_section title="Titre personnalisé"]
[signalement_section description="Description personnalisée"]
[signalement_section button_text="Texte du bouton personnalisé"]
[signalement_section class="ma-classe-css"]
```

**Exemple complet :**
```
[signalement_section 
    title="Avez-vous ressenti un tremblement ?"
    description="Partagez votre expérience pour aider la communauté scientifique."
    button_text="Partager maintenant"
    class="signalement-custom"]
```

### 2. Formulaire Seul : `[signalement_form]`
Affiche uniquement le formulaire sans la bannière.

**Utilisation :**
```
[signalement_form]
[signalement_form class="form-compact"]
```

## Champs du Formulaire

### Champs obligatoires (*) :
- **Date et heure** : Moment de la secousse
- **Intensité** : Échelle de 1 à 8
- **Localisation** : Ville ou quartier

### Champs optionnels :
- **Durée** : Durée approximative en secondes
- **Type de mouvement** : Horizontal, vertical, rotatif, ondulant
- **Description** : Détails de l'expérience
- **Nom** : Nom du témoin (optionnel)
- **Email** : Contact (optionnel)

## Intégration dans les Pages

### Dans une page ou article :
```
[signalement_section]

Contenu de votre page...

[signalement_form]
```

### Dans le template de page d'accueil :
```
<!-- Menu de la page d'accueil -->
[homepage_menu]

<!-- Section de signalement -->
[signalement_section]

<!-- Contenu principal -->
Contenu de la page...
```

## Personnalisation

### Modifier les Couleurs
Les couleurs sont définies dans `style.css` :
- `.signalement-section` : Arrière-plan principal
- `.btn-signaler` : Bouton d'action
- `.modal-header` : En-tête du modal

### Modifier le Texte
Vous pouvez personnaliser tous les textes via les attributs du shortcode ou en modifiant les valeurs par défaut dans `functions.php`.

### Ajouter des Champs
Pour ajouter de nouveaux champs au formulaire :
1. Modifiez la fonction `websy_signalement_form_shortcode()`
2. Ajoutez le traitement dans `websy_process_signalement_form()`
3. Mettez à jour le type de post personnalisé

## Gestion des Signalements

### Dans l'Administration WordPress
- **Menu Signalements** : Apparaît dans le menu principal
- **Liste des signalements** : Vue d'ensemble de tous les signalements
- **Édition** : Modification des signalements existants
- **Métadonnées** : Accès aux informations détaillées

### Structure des Données
Chaque signalement est stocké comme un post WordPress avec :
- **Titre** : Format "Signalement - Localisation - Date"
- **Contenu** : Description détaillée
- **Métadonnées** : Tous les champs du formulaire

## Sécurité

### Protection CSRF
- Vérification des nonces WordPress
- Validation des données d'entrée
- Sanitisation des données

### Validation
- Champs obligatoires vérifiés
- Types de données validés
- Limites sur les valeurs numériques

## Compatibilité

### Navigateurs
- Chrome, Firefox, Safari, Edge
- Support des navigateurs mobiles
- Compatible avec les anciennes versions

### Plugins WordPress
- Compatible avec la plupart des plugins
- Pas de conflit avec les thèmes
- Fonctionne avec les plugins de cache

## Support et Maintenance

### Problèmes Courants
1. **Modal ne s'ouvre pas** : Vérifiez que Bootstrap est chargé
2. **Formulaire ne s'envoie pas** : Vérifiez les permissions WordPress
3. **Styles manquants** : Videz le cache et rechargez les CSS

### Mise à Jour
- Sauvegardez vos personnalisations avant mise à jour
- Testez sur un environnement de développement
- Vérifiez la compatibilité avec les nouvelles versions

## Licence
Ce code est sous licence GPL v3, comme le thème WordPress parent. 