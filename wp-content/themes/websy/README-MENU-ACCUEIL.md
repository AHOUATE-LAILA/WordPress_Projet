# Menu de la Page d'Accueil - Thème Websy

## Description
Ce thème WordPress inclut maintenant un menu personnalisé spécialement conçu pour la page d'accueil. Le menu offre une navigation attrayante et moderne avec des animations et un design responsive.

## Fonctionnalités
- **Menu personnalisé** : Affiché uniquement sur la page d'accueil
- **Design moderne** : Interface avec dégradés et effets visuels
- **Responsive** : S'adapte automatiquement aux différentes tailles d'écran
- **Animations** : Effets d'entrée et de survol fluides
- **Personnalisable** : Facilement modifiable via l'administration WordPress
- **Shortcodes** : Intégration facile dans n'importe quelle page ou article

## Installation et Configuration

### 1. Créer le Menu
1. Connectez-vous à l'administration WordPress
2. Allez dans **Apparence > Menus**
3. Créez un nouveau menu
4. Ajoutez les pages ou liens souhaités
5. Dans la section "Emplacement du menu", cochez **"Menu Page d'Accueil"**
6. Enregistrez le menu

### 2. Utiliser le Template
Deux options s'offrent à vous :

#### Option A : Page d'Accueil Dynamique
- Le menu s'affichera automatiquement sur la page d'accueil du blog
- Utilise le fichier `front-page.php`

#### Option B : Page d'Accueil Statique
1. Créez une nouvelle page
2. Dans les attributs de page, sélectionnez le template **"Page d'Accueil avec Menu"**
3. Définissez cette page comme page d'accueil dans **Réglages > Lecture**

### 3. Utiliser les Shortcodes
Vous pouvez maintenant intégrer le menu dans n'importe quelle page ou article en utilisant les shortcodes suivants :

#### Shortcode Principal : `[homepage_menu]`
```php
[homepage_menu]
```

**Attributs disponibles :**
- `style` : `default`, `compact`, `full-width`
- `background` : `gradient`, `solid`, `transparent`
- `class` : Classes CSS personnalisées

**Exemples d'utilisation :**
```php
[homepage_menu style="compact"]
[homepage_menu background="transparent"]
[homepage_menu style="full-width" background="solid" class="custom-menu"]
```

#### Shortcode Menu Seul : `[homepage_menu_only]`
```php
[homepage_menu_only]
```

**Attributs disponibles :**
- `class` : Classes CSS personnalisées

**Exemple d'utilisation :**
```php
[homepage_menu_only class="inline-menu"]
```

## Personnalisation

### Modifier les Styles
Les styles sont définis dans `style.css` :
- `.homepage-menu-section` : Section principale du menu
- `.homepage-menu` : Style des éléments de menu
- `.homepage-menu a` : Style des liens
- `.homepage-menu-section-compact` : Style compact
- `.homepage-menu-section-full-width` : Style pleine largeur
- `.homepage-menu-bg-solid` : Arrière-plan uni
- `.homepage-menu-bg-transparent` : Arrière-plan transparent

### Modifier le Contenu
- **Menu de fallback** : Modifiez la fonction `websy_homepage_menu_fallback()` dans `functions.php`
- **Template** : Personnalisez `front-page.php` ou `template-homepage.php`

### Ajouter des Fonctionnalités
Vous pouvez étendre le menu en ajoutant des hooks personnalisés dans `functions.php` :
```php
add_action('websy_homepage_menu', 'votre_fonction_personnalisee');
```

## Structure des Fichiers
```
wp-content/themes/websy/
├── functions.php              # Fonctions du thème et enregistrement du menu
├── front-page.php            # Template pour la page d'accueil dynamique
├── template-homepage.php     # Template personnalisé pour page statique
├── style.css                 # Styles CSS du menu
└── README-MENU-ACCUEIL.md   # Ce fichier
```

## Support
Pour toute question ou problème :
1. Vérifiez que le thème Websy est activé
2. Assurez-vous que le menu est bien assigné à l'emplacement "Menu Page d'Accueil"
3. Videz le cache si vous utilisez un plugin de cache
4. Vérifiez la console du navigateur pour d'éventuelles erreurs JavaScript

## Compatibilité
- WordPress 5.0+
- PHP 7.4+
- Compatible avec la plupart des plugins populaires
- Testé avec les navigateurs modernes (Chrome, Firefox, Safari, Edge)

## Licence
Ce code est sous licence GPL v3, comme le thème WordPress parent. 