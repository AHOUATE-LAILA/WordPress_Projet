<?php
/**
 * Plugin Name: Gaisio Earthquake Manager
 * Description: Plateforme complète de gestion des tremblements de terre pour l'ING/CNRST
 * Version: 2.0.0
 * Author: Daba Kandoz Stag - Institut National de Recherche Scientifique et Technique
 * Text Domain: gaisio-earthquake-manager
 * 
 * @package GaisioEarthquakeManager
 * @since 1.0.0
 */

// Empêcher l'accès direct
if (!defined('ABSPATH')) {
    exit;
}

// Définition des constantes
define('GAISIO_PLUGIN_URL', plugin_dir_url(__FILE__));
define('GAISIO_PLUGIN_PATH', plugin_dir_path(__FILE__));

/**
 * Classe principale du plugin Gaisio Earthquake Manager
 * 
 * Gère toutes les fonctionnalités de la plateforme :
 * - Interface publique (carte, données, actualités, ressources, signalement)
 * - Espace utilisateur (connexion, saisie de données)
 * - Administration (actualités, utilisateurs, statistiques)
 * 
 * @since 1.0.0
 */
class GaisioEarthquakeManager {
    
    /**
     * Constructeur de la classe
     * Initialise tous les hooks WordPress et AJAX
     */
    public function __construct() {
        add_action('init', array($this, 'init'));
        add_action('wp_enqueue_scripts', array($this, 'enqueue_scripts'));
        
        // Désactiver jQuery Migrate pour éviter les erreurs
        add_action('wp_enqueue_scripts', array($this, 'disable_jquery_migrate'), 999);
        add_action('wp_head', array($this, 'disable_jquery_migrate_warnings'), 999);
        
        // Initialiser les traductions
        add_action('init', array($this, 'init_translations'));
        
        // Hooks AJAX pour le frontend
        add_action('wp_ajax_gaisio_save_earthquake', array($this, 'save_earthquake_data'));
        add_action('wp_ajax_nopriv_gaisio_save_earthquake', array($this, 'save_earthquake_data'));
        add_action('wp_ajax_gaisio_get_earthquakes', array($this, 'get_earthquakes'));
        add_action('wp_ajax_nopriv_gaisio_get_earthquakes', array($this, 'get_earthquakes'));
        add_action('wp_ajax_gaisio_get_stats', array($this, 'get_stats'));
        add_action('wp_ajax_nopriv_gaisio_get_stats', array($this, 'get_stats'));
        add_action('wp_ajax_gaisio_get_location_info', array($this, 'get_location_info'));
        add_action('wp_ajax_nopriv_gaisio_get_location_info', array($this, 'get_location_info'));
        add_action('wp_ajax_gaisio_get_news_frontend', array($this, 'get_news_frontend'));
        add_action('wp_ajax_nopriv_gaisio_get_news_frontend', array($this, 'get_news_frontend'));
        
        // Hooks AJAX pour le signalement
        add_action('wp_ajax_gaisio_submit_signalement', array($this, 'submit_signalement_ajax'));
        add_action('wp_ajax_nopriv_gaisio_submit_signalement', array($this, 'submit_signalement_ajax'));
        
        // Hooks AJAX pour l'administration
        add_action('wp_ajax_gaisio_admin_save_news', array($this, 'admin_save_news'));
        add_action('wp_ajax_gaisio_admin_update_news', array($this, 'admin_update_news'));
        add_action('wp_ajax_gaisio_admin_get_news', array($this, 'admin_get_news'));
        add_action('wp_ajax_gaisio_admin_delete_news', array($this, 'admin_delete_news'));
        add_action('wp_ajax_gaisio_admin_get_users', array($this, 'admin_get_users'));
        add_action('wp_ajax_gaisio_admin_delete_user', array($this, 'admin_delete_user'));
        add_action('wp_ajax_gaisio_admin_create_user', array($this, 'admin_create_user'));
        add_action('wp_ajax_gaisio_admin_get_stats', array($this, 'admin_get_stats'));
        add_action('wp_ajax_gaisio_download_user_credentials_pdf', array($this, 'download_user_credentials_pdf'));
        
        // Hooks AJAX pour l'authentification des utilisateurs
        add_action('wp_ajax_nopriv_gaisio_user_login', array($this, 'user_login_ajax'));
        add_action('wp_ajax_gaisio_user_login', array($this, 'user_login_ajax'));
        add_action('wp_ajax_gaisio_user_logout', array($this, 'user_logout_ajax'));
        // Hooks AJAX pour la connexion administrateur
        add_action('wp_ajax_nopriv_gaisio_admin_login', array($this, 'admin_login_ajax'));
        add_action('wp_ajax_gaisio_admin_login', array($this, 'admin_login_ajax'));
        
        // Hooks AJAX pour la traduction

        
        // Hooks pour le formulaire de signalement
        add_action('init', array($this, 'register_signalement_post_type'));
        // Supprimer l'ancien hook qui causait des redirections
        // add_action('init', array($this, 'process_signalement_form'));
        
        add_action('wp_footer', array($this, 'add_footer_once'));
        
        // Hooks d'administration
        add_action('admin_menu', array($this, 'add_admin_menu'));
        add_action('admin_enqueue_scripts', array($this, 'admin_enqueue_scripts'));
        
        register_activation_hook(__FILE__, array($this, 'activate'));
        register_deactivation_hook(__FILE__, array($this, 'deactivate'));
    }
    
    public function init() {
        // Créer les tables personnalisées lors de l'activation
        $this->create_tables();
        
        // Ajouter les shortcodes
        add_shortcode('gaisio_user_dashboard', array($this, 'user_dashboard_shortcode'));
        add_shortcode('gaisio_earthquake_form', array($this, 'earthquake_form_shortcode'));
        add_shortcode('gaisio_earthquake_table', array($this, 'earthquake_table_shortcode'));
        add_shortcode('gaisio_public_home', array($this, 'public_home_shortcode'));
        add_shortcode('gaisio_earthquake_map', array($this, 'earthquake_map_shortcode'));
        add_shortcode('gaisio_footer', array($this, 'footer_shortcode'));
        add_shortcode('gaisio_admin_page', array($this, 'admin_page_shortcode'));
        add_shortcode('gaisio_news_carousel', array($this, 'news_carousel_shortcode'));
        add_shortcode('gaisio_user_login', array($this, 'user_login_shortcode'));
    }
    
    /**
     * Initialiser les traductions
     */
    public function init_translations() {
        // Définir la langue par défaut
        if (!isset($_SESSION['gaisio_language'])) {
            $_SESSION['gaisio_language'] = 'fr';
        }
    }
    
    /**
     * Obtenir les traductions
     */
    public function get_translations() {
        return array(
            'fr' => array(
                'home' => 'Accueil',
                'news' => 'Actualités',
                'resources' => 'Ressources',
                'report' => 'Signaler',
                'platform_title' => '🌍 Plateforme Gaisio - Tremblements de Terre',
                'platform_subtitle' => 'Surveillance et analyse des séismes en temps réel',
                'interactive_map_title' => '🗺️ Carte interactive des tremblements de terre',
                'interactive_map_desc' => 'Visualisez les tremblements de terre enregistrés sur une carte interactive',
                'detailed_data_title' => '📊 Données détaillées',
                'detailed_data_desc' => 'Consultez toutes les données de tremblements de terre enregistrées',
                'news_title' => '📰 Actualités',
                'news_desc' => 'Dernières actualités de l\'institut',
                'resources_title' => '📚 Centre des ressources',
                'resources_desc' => 'Publications et documents de référence',
                'report_title' => '🚨 Signalement de Secousses',
                'report_desc' => 'Partagez votre expérience pour aider la communauté scientifique',
                
                'signalement_title' => 'Avez-vous ressenti une secousse ?',
                'signalement_desc' => 'En signalant une secousse, vous contribuez à collecter des informations précieuses qui aident à l\'analyse des événements sismiques.',
                'signalement_button' => 'Signaler maintenant',
                'form_title' => 'Formulaire de Signalement',
                'close_form' => 'Fermer',
                'submit_report' => 'Envoyer le signalement',
                'cancel' => 'Annuler'
            ),
            'en' => array(
                'home' => 'Home',
                'news' => 'News',
                'resources' => 'Resources',
                'report' => 'Report',
                'platform_title' => '🌍 Gaisio Platform - Earthquakes',
                'platform_subtitle' => 'Real-time earthquake monitoring and analysis',
                'interactive_map_title' => '🗺️ Interactive Earthquake Map',
                'interactive_map_desc' => 'View recorded earthquakes on an interactive map',
                'detailed_data_title' => '📊 Detailed Data',
                'detailed_data_desc' => 'Consult all recorded earthquake data',
                'news_title' => '📰 News',
                'news_desc' => 'Latest news from the institute',
                'resources_title' => '📚 Resource Center',
                'resources_desc' => 'Publications and reference documents',
                'report_title' => '🚨 Earthquake Report',
                'report_desc' => 'Share your experience to help the scientific community',
                'language_switch' => '🌐 Français',
                'language_switch_back' => '',
                'signalement_title' => 'Did you feel a tremor?',
                'signalement_desc' => 'By reporting a tremor, you contribute to collecting valuable information that helps analyze seismic events.',
                'signalement_button' => 'Report now',
                'form_title' => 'Report Form',
                'close_form' => 'Close',
                'submit_report' => 'Submit Report',
                'cancel' => 'Cancel'
            )
        );
    }
    
    public function enqueue_scripts() {
        // Forcer le chargement de jQuery
        wp_enqueue_script('jquery');
        
        // Charger le script principal avec une version mise à jour
        wp_enqueue_script('gaisio-earthquake-js', GAISIO_PLUGIN_URL . 'js/gaisio-earthquake.js', array('jquery'), '1.0.4', true);
        
        // Localiser le script avec les traductions et le nonce
        wp_localize_script('gaisio-earthquake-js', 'gaisio_translations', $this->get_translations());
        wp_localize_script('gaisio-earthquake-js', 'gaisio_ajax', array(
            'ajax_url' => admin_url('admin-ajax.php'),

        ));
        
        // Charger le script public pour les formulaires utilisateur
        wp_enqueue_script('gaisio-public', GAISIO_PLUGIN_URL . 'js/gaisio-public.js', array('jquery'), '1.0.0', true);
        
        // Localiser les variables pour le script public
        wp_localize_script('gaisio-public', 'gaisio_public', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('gaisio_public_nonce')
        ));
        
        // Localiser les variables pour la connexion utilisateur
        wp_localize_script('gaisio-public', 'gaisio_user', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('gaisio_user_nonce')
        ));
        
        // Ajouter le script JavaScript pour la section de signalement
        wp_add_inline_script('gaisio-earthquake-js', '
            function showSignalementForm() {
                document.getElementById("signalement-form-container").style.display = "block";
                document.getElementById("signalement-form-container").scrollIntoView({ behavior: "smooth" });
            }
            
            function hideSignalementForm() {
                document.getElementById("signalement-form-container").style.display = "none";
            }
            
            // Gestion de la soumission AJAX du formulaire
            jQuery(document).ready(function($) {
                // Désactiver les avertissements jQuery Migrate pour éviter les erreurs
                if (typeof jQuery.migrateMute === "function") {
                    jQuery.migrateMute = true;
                }
                
                $("#signalement-form").on("submit", function(e) {
                    e.preventDefault();
                    
                    var $form = $(this);
                    var $submitBtn = $("#submit-signalement");
                    var $messages = $("#signalement-messages");
                    
                    // Vérifier que ajaxurl est disponible
                    if (typeof ajaxurl === "undefined") {
                        console.error("ajaxurl non défini");
                        $messages.html(\'<div class="signalement-error">Erreur de configuration AJAX. Veuillez rafraîchir la page.</div>\');
                        return;
                    }
                    
                    // Désactiver le bouton et afficher un indicateur de chargement
                    $submitBtn.prop("disabled", true).html(\'<i class="fa fa-spinner fa-spin"></i> Envoi en cours...\');
                    
                    // Effacer les messages précédents
                    $messages.empty();
                    
                    // Récupérer les données du formulaire
                    var formData = new FormData(this);
                    formData.append("action", "gaisio_submit_signalement");
                    
                    // Debug: afficher les données envoyées
                    console.log("Envoi du formulaire de signalement...");
                    console.log("URL AJAX:", ajaxurl);
                    console.log("Action:", "gaisio_submit_signalement");
                    
                    // Envoyer la requête AJAX
                    $.ajax({
                        url: ajaxurl,
                        type: "POST",
                        data: formData,
                        processData: false,
                        contentType: false,
                        timeout: 30000, // Timeout de 30 secondes
                        success: function(response) {
                            console.log("Réponse reçue:", response);
                            
                            if (response.success) {
                                // Afficher le message de succès
                                $messages.html(\'<div class="signalement-success">\' + response.data.message + \'</div>\');
                                
                                // Masquer le formulaire après 3 secondes
                                setTimeout(function() {
                                    hideSignalementForm();
                                    // Réinitialiser le formulaire
                                    $form[0].reset();
                                    // Effacer les messages
                                    $messages.empty();
                                }, 3000);
                            } else {
                                // Afficher le message d\'erreur
                                var errorMsg = response.data && response.data.message ? response.data.message : "Erreur inconnue";
                                $messages.html(\'<div class="signalement-error">\' + errorMsg + \'</div>\');
                            }
                        },
                        error: function(xhr, status, error) {
                            console.error("Erreur AJAX:", {xhr: xhr, status: status, error: error});
                            
                            var errorMessage = "Une erreur est survenue lors de l\'envoi du formulaire.";
                            
                            if (status === "timeout") {
                                errorMessage = "La requête a pris trop de temps. Veuillez réessayer.";
                            } else if (xhr.status === 0) {
                                errorMessage = "Erreur de connexion au serveur. Vérifiez votre connexion internet.";
                            } else if (xhr.status >= 400 && xhr.status < 500) {
                                errorMessage = "Erreur de requête (code " + xhr.status + "). Veuillez vérifier les données saisies.";
                            } else if (xhr.status >= 500) {
                                errorMessage = "Erreur serveur (code " + xhr.status + "). Veuillez réessayer plus tard.";
                            }
                            
                            $messages.html(\'<div class="signalement-error">\' + errorMessage + \'</div>\');
                        },
                        complete: function() {
                            // Réactiver le bouton
                            $submitBtn.prop("disabled", false).html(\'<i class="fa fa-paper-plane"></i> Envoyer le signalement\');
                        }
                    });
                });
            });
        ');
        wp_enqueue_style('gaisio-earthquake-css', GAISIO_PLUGIN_URL . 'css/gaisio-earthquake.css', array(), '1.0.5');
        
        // Charger les styles d'administration sur toutes les pages
        wp_enqueue_style('gaisio-admin-css', GAISIO_PLUGIN_URL . 'css/gaisio-admin.css', array(), '1.0.5');
        
        // Charger Leaflet pour la carte
        wp_enqueue_script('leaflet', 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.js', array(), '1.9.4', true);
        wp_enqueue_style('leaflet-css', 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.css', array(), '1.9.4');
        
        // Localiser le script pour AJAX avec plus de données de débogage
        wp_localize_script('gaisio-earthquake-js', 'gaisio_ajax', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('gaisio_nonce'),
            'plugin_url' => GAISIO_PLUGIN_URL,
            'debug' => WP_DEBUG
        ));
        
        // Ajouter ajaxurl pour les utilisateurs non connectés
        wp_localize_script('gaisio-earthquake-js', 'ajaxurl', admin_url('admin-ajax.php'));
        
        // Charger le script d'administration si l'utilisateur est admin
        if (current_user_can('manage_options')) {
            wp_enqueue_script('gaisio-admin-js', GAISIO_PLUGIN_URL . 'js/gaisio-admin.js', array('jquery'), '1.0.0', true);
            wp_localize_script('gaisio-admin-js', 'gaisio_admin', array(
                'ajax_url' => admin_url('admin-ajax.php'),
                'nonce' => wp_create_nonce('gaisio_admin_nonce')
            ));
            
            // Ajouter aussi les variables AJAX du frontend pour le rafraîchissement
            wp_localize_script('gaisio-admin-js', 'gaisio_ajax', array(
                'ajax_url' => admin_url('admin-ajax.php'),
                'nonce' => wp_create_nonce('gaisio_nonce')
            ));
        }
    }
    
    public function activate() {
        $this->create_tables();
    }
    
    public function deactivate() {
        // Nettoyage si nécessaire
    }
    
    private function create_tables() {
        global $wpdb;
        
        $charset_collate = $wpdb->get_charset_collate();
        
        // Table des utilisateurs Gaisio
        $table_users = $wpdb->prefix . 'gaisio_users';
        $sql_users = "CREATE TABLE $table_users (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            user_id bigint(20) NOT NULL,
            username varchar(100) NOT NULL,
            email varchar(100) NOT NULL,
            access_code varchar(100) DEFAULT NULL,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            UNIQUE KEY user_id (user_id)
        ) $charset_collate;";
        
        // Table des tremblements de terre
        $table_earthquakes = $wpdb->prefix . 'gaisio_earthquakes';
        $sql_earthquakes = "CREATE TABLE $table_earthquakes (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            user_id bigint(20) NOT NULL,
            datetime_utc datetime NOT NULL,
            latitude decimal(10,8) NOT NULL,
            longitude decimal(11,8) NOT NULL,
            depth decimal(10,2) NOT NULL,
            magnitude decimal(3,1) DEFAULT NULL,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY user_id (user_id),
            KEY datetime_utc (datetime_utc)
        ) $charset_collate;";
        
        // Table des actualités
        $table_news = $wpdb->prefix . 'gaisio_news';
        $sql_news = "CREATE TABLE $table_news (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            title varchar(255) NOT NULL,
            content text NOT NULL,
            image_url varchar(500) DEFAULT NULL,
            status enum('published', 'draft') DEFAULT 'published',
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY status (status),
            KEY created_at (created_at)
        ) $charset_collate;";
        
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql_users);
        dbDelta($sql_earthquakes);
        dbDelta($sql_news);
        
        // Mettre à jour la table existante si nécessaire
        $this->update_table_structure();
    }
    
    // Fonction pour mettre à jour la structure de la table
    private function update_table_structure() {
        global $wpdb;
        
        // Mettre à jour la table gaisio_earthquakes
        $table = $wpdb->prefix . 'gaisio_earthquakes';
        
        // Vérifier si la colonne description existe et la supprimer
        $column_exists = $wpdb->get_results("SHOW COLUMNS FROM $table LIKE 'description'");
        if (!empty($column_exists)) {
            $wpdb->query("ALTER TABLE $table DROP COLUMN description");
        }
        
        // Mettre à jour la table gaisio_users
        $table_users = $wpdb->prefix . 'gaisio_users';
        
        // Vérifier si la colonne access_code existe
        $access_code_exists = $wpdb->get_results("SHOW COLUMNS FROM $table_users LIKE 'access_code'");
        if (empty($access_code_exists)) {
            $wpdb->query("ALTER TABLE $table_users ADD COLUMN access_code varchar(100) DEFAULT NULL AFTER email");
        }
    }
    

    
    // Shortcode pour la page de sélection de type de connexion
    public function user_dashboard_shortcode($atts) {
        $atts = shortcode_atts(array(
            'title' => '🔐 Connexion Gaisio'
        ), $atts);
        
        ob_start();
        ?>
        <div class="gaisio-login-selection">
            <div class="login-header">
                <h1><?php echo esc_html($atts['title']); ?></h1>
                <p class="login-subtitle">Choisissez votre type de connexion</p>
            </div>
            
            <div class="login-options">
                <div class="login-option" id="admin-option">
                    <div class="option-icon">👨‍💼</div>
                    <h3>Administrateur</h3>
                    <p>Accès à l'interface d'administration</p>
                    <button class="gaisio-btn option-btn" onclick="showAdminLogin()">
                        🔐 Connexion Admin
                    </button>
                </div>
                
                <div class="login-option" id="user-option">
                    <div class="option-icon">👤</div>
                    <h3>Utilisateur</h3>
                    <p>Accès à votre espace personnel</p>
                    <button class="gaisio-btn option-btn" onclick="showUserLogin()">
                        🔐 Connexion Utilisateur
                    </button>
                </div>
            </div>
            
            <!-- Formulaire de connexion Admin (caché par défaut) -->
            <div id="admin-login-form" class="login-form-container" style="display: none;">
                <div class="form-header">
                    <h3>👨‍💼 Connexion Administrateur</h3>
                    <button type="button" class="btn-close-form" onclick="hideAdminLogin()">
                        <i class="fa fa-times"></i>
                    </button>
                </div>
                
                <div id="admin-login-message"></div>
                
                <form class="gaisio-admin-login-form" id="gaisio-admin-login-form" method="post">
                    <?php wp_nonce_field('gaisio_admin_nonce', 'admin_nonce_field'); ?>
                    
                    <div class="form-group">
                        <label for="admin-username">Nom d'utilisateur *</label>
                        <input type="text" class="form-control" id="admin-username" name="admin_username" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="admin-password">Mot de passe *</label>
                        <input type="password" class="form-control" id="admin-password" name="admin_password" required>
                    </div>
                    
                    <div class="form-group">
                        <label class="checkbox-label">
                            <input type="checkbox" id="admin-remember" name="admin_remember">
                            <span class="checkmark"></span>
                            Se souvenir de moi
                        </label>
                    </div>
                    
                    <div class="form-actions">
                        <button type="submit" id="submit-admin-login" class="btn btn-primary">
                            <span class="btn-text">🔐 Se connecter</span>
                            <span class="btn-loading" style="display: none;">
                                <span class="spinner"></span> Connexion...
                            </span>
                        </button>
                        <button type="button" class="btn btn-secondary" onclick="hideAdminLogin()">
                            Annuler
                        </button>
                    </div>
                </form>
            </div>
            
            <!-- Formulaire de connexion Utilisateur (caché par défaut) -->
            <div id="user-login-form" class="login-form-container" style="display: none;">
                <div class="form-header">
                    <h3>👤 Connexion Utilisateur</h3>
                    <button type="button" class="btn-close-form" onclick="hideUserLogin()">
                        <i class="fa fa-times"></i>
                    </button>
                </div>
                
                <div id="login-message"></div>
                
                <form class="gaisio-user-login-form" id="gaisio-user-login-form" method="post">
                    <?php wp_nonce_field('gaisio_user_nonce', 'user_nonce_field'); ?>
                    
                    <div class="form-group">
                        <label for="login-username">Nom d'utilisateur *</label>
                        <input type="text" class="form-control" id="login-username" name="login_username" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="login-access-code">Code d'accès *</label>
                        <input type="text" class="form-control" id="login-access-code" name="login_access_code" required>
                    </div>
                    
                    <div class="form-group">
                        <label class="checkbox-label">
                            <input type="checkbox" id="login-remember" name="login_remember">
                            <span class="checkmark"></span>
                            Se souvenir de moi
                        </label>
                    </div>
                    
                    <div class="form-actions">
                        <button type="submit" id="submit-user-login" class="btn btn-primary">
                            <span class="btn-text">🔐 Se connecter</span>
                            <span class="btn-loading" style="display: none;">
                                <span class="spinner"></span> Connexion...
                            </span>
                        </button>
                        <button type="button" class="btn btn-secondary" onclick="hideUserLogin()">
                            Annuler
                        </button>
                    </div>
                </form>
            </div>
        </div>
        
        <script>
        function showAdminLogin() {
            document.getElementById('admin-login-form').style.display = 'block';
            document.getElementById('user-login-form').style.display = 'none';
            document.querySelector('.login-options').style.display = 'none';
        }
        
        function showUserLogin() {
            document.getElementById('user-login-form').style.display = 'block';
            document.getElementById('admin-login-form').style.display = 'none';
            document.querySelector('.login-options').style.display = 'none';
        }
        
        function hideAdminLogin() {
            document.getElementById('admin-login-form').style.display = 'none';
            document.querySelector('.login-options').style.display = 'flex';
        }
        
        function hideUserLogin() {
            document.getElementById('user-login-form').style.display = 'none';
            document.querySelector('.login-options').style.display = 'flex';
        }
        </script>
        <?php
        return ob_get_clean();
    }
    
    // Shortcode pour le formulaire de saisie des tremblements de terre
    public function earthquake_form_shortcode($atts) {
        ob_start();
        ?>
        <div class="gaisio-earthquake-form">
            <h2>Saisir un tremblement de terre</h2>
            
            <?php if (!is_user_logged_in()): ?>
                <!-- Message pour utilisateurs non connectés -->
                <div style="text-align: center; padding: 2rem;">
                    <div style="background: var(--gaisio-warning); color: white; padding: 1.5rem; border-radius: 10px; margin-bottom: 1.5rem;">
                        <h3 style="margin: 0; color: white;">⚠️ Connexion requise</h3>
                        <p style="margin: 0.5rem 0 0 0;">Vous devez être connecté pour saisir des données de tremblements de terre.</p>
                    </div>
                    <div class="gaisio-auth-buttons">
                        <a href="<?php echo wp_login_url(get_permalink()); ?>" class="gaisio-btn-login">
                            🔐 Se connecter
                        </a>
                       
                    </div>
                </div>
            <?php else: ?>
                <!-- Formulaire pour utilisateurs connectés -->
                <div style="text-align: center; margin-bottom: 2rem;">
                    <div style="background: var(--gaisio-success); color: white; padding: 1rem; border-radius: 10px; margin-bottom: 1rem;">
                        <h3 style="margin: 0; color: white;">✅ Connecté : <?php echo wp_get_current_user()->display_name; ?></h3>
                    </div>
                    <div class="gaisio-auth-buttons">
                        <a href="<?php echo home_url('/login/'); ?>" class="gaisio-btn-logout">
                            🚪 Se déconnecter
                        </a>
                    </div>
                </div>
                
                <form id="gaisio-earthquake-form" method="post">
                    <div class="form-group">
                        <label for="datetime_utc">Date et heure (UTC) *</label>
                        <input type="datetime-local" id="datetime_utc" name="datetime_utc" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="latitude">Latitude *</label>
                        <input type="number" id="latitude" name="latitude" step="0.00000001" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="longitude">Longitude *</label>
                        <input type="number" id="longitude" name="longitude" step="0.00000001" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="depth">Profondeur (km) *</label>
                        <input type="number" id="depth" name="depth" step="0.01" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="magnitude">Magnitude *</label>
                        <input type="number" id="magnitude" name="magnitude" step="0.1" min="0.0" max="10.0" required 
                               placeholder="Ex: 4.5">
                        <small class="form-help">Saisissez la magnitude du tremblement de terre (échelle de Richter)</small>
                    </div>
                    
                    <button type="submit" class="gaisio-btn">Enregistrer</button>
                </form>
                <div id="gaisio-earthquake-message"></div>
            <?php endif; ?>
        </div>
        <?php
        return ob_get_clean();
    }
    
    // Shortcode pour afficher le tableau des tremblements de terre
    public function earthquake_table_shortcode($atts) {
        ob_start();
        
        ?>
        <div class="gaisio-earthquake-table">
            <h2>Tremblements de terre enregistrés</h2>
            
            <!-- Barre de filtres -->
            <div class="gaisio-table-filters">
                <div class="filter-group">
                    <label>Rechercher dans le tableau :</label>
                    <div class="search-container">
                        <input type="text" id="global-search" placeholder="Rechercher par date, magnitude, commune, province..." class="filter-input">
                        <button id="clear-filters" class="gaisio-btn-secondary" style="background: #e74c3c !important; color: white !important; border: none !important; border-radius: 6px !important; font-weight: 500 !important; transition: all 0.3s ease !important; cursor: pointer !important;">Effacer</button>
                    </div>
                </div>
            </div>
            
            <div id="gaisio-earthquake-list">
                <table class="gaisio-table">
                    <thead>
                        <tr>
                            <th data-sort="datetime">Date/Heure (UTC) </th>
                            <th data-sort="latitude">Latitude </th>
                            <th data-sort="longitude">Longitude </th>
                            <th data-sort="depth">Profondeur (km) </th>
                            <th data-sort="magnitude">Magnitude </th>
                            <th data-sort="commune">Commune </th>
                            <th data-sort="province">Province </th>
                        </tr>
                    </thead>
                    <tbody id="gaisio-earthquake-tbody">
                        <!-- Les données seront chargées via AJAX -->
                    </tbody>
                </table>
                <div id="no-results" class="no-results" style="display: none;">
                    <p>Aucun résultat trouvé avec les filtres actuels.</p>
                </div>
            </div>
        </div>
        <?php
        return ob_get_clean();
    }
    
    // Shortcode pour la page d'accueil publique avec menu de navigation
    public function public_home_shortcode($atts) {
        $atts = shortcode_atts(array(
            'title' => '🌍 Plateforme Gaisio - Tremblements de Terre'
        ), $atts);
        
        // Démarrer la session si elle n'est pas déjà démarrée
        if (session_status() === PHP_SESSION_NONE) {
            // Session gérée par WordPress
        }
        
        // Obtenir la langue actuelle
        $current_language = $_SESSION['gaisio_language'] ?? 'fr';
        $translations = $this->get_translations();
        $t = $translations[$current_language];
        
         ob_start();
        ?>
        <div class="gaisio-public-home" data-language="<?php echo esc_attr($current_language); ?>">
            <!-- Menu de Navigation -->
            <nav class="gaisio-public-nav">
                <div class="nav-container">
                    <div class="nav-brand">
                        <div class="logo-container">
                            <img src="<?php echo plugin_dir_url(__FILE__); ?>assets/images/logo-morseps2.png" alt="Logo Gaisio" class="nav-logo">
                        </div>
                        
                    </div>
                    <ul class="nav-menu">
                        <li class="nav-item">
                            <a href="#accueil" class="nav-link active" data-section="accueil" data-translate="home">
                                 <?php echo esc_html($t['home']); ?>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="#actualites" class="nav-link" data-section="actualites" data-translate="news">
                                <?php echo esc_html($t['news']); ?>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="#ressources" class="nav-link" data-section="ressources" data-translate="resources">
                                <?php echo esc_html($t['resources']); ?>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="#signaler" class="nav-link" data-section="signaler" data-translate="report">
                                <?php echo esc_html($t['report']); ?>
                            </a>
                        </li>
                    </ul>
                    <div class="nav-toggle">
                        <span></span>
                        <span></span>
                        <span></span>
                    </div>
                </div>
            </nav>
            


            <!-- Contenu Principal Original -->
            <div class="gaisio-public-content">
                <div class="gaisio-public-header">
                    <h1 data-translate="platform_title"><?php echo esc_html($t['platform_title']); ?></h1>
                    <p data-translate="platform_subtitle"><?php echo esc_html($t['platform_subtitle']); ?></p>
                    <div class="gaisio-public-stats">
                        <div class="stat-item">
                            <span class="stat-number" id="total-earthquakes">0</span>
                            <span class="stat-label">Nombre des séismes</span>
                        </div>
                        <div class="stat-item">
                            <span class="stat-number" id="total-users">0</span>
                            <span class="stat-label">Utilisateurs actifs</span>
                        </div>
                        <div class="stat-item">
                            <span class="stat-number" id="latest-magnitude">0.0</span>
                            <span class="stat-label">Magnitude élevée</span>
                        </div>
                    </div>
                </div>
                
                <div class="gaisio-public-section">
                    <h2 data-translate="interactive_map_title"><?php echo esc_html($t['interactive_map_title']); ?></h2>
                    <p data-translate="interactive_map_desc"><?php echo esc_html($t['interactive_map_desc']); ?></p>
                    <?php echo do_shortcode('[gaisio_earthquake_map]'); ?>
                </div>
                
                <!-- Section des données détaillées - visible pour tous -->
                <div class="gaisio-public-section">
                    <h2 data-translate="detailed_data_title"><?php echo esc_html($t['detailed_data_title']); ?></h2>
                    <p data-translate="detailed_data_desc"><?php echo esc_html($t['detailed_data_desc']); ?></p>
                    <?php echo do_shortcode('[gaisio_earthquake_table]'); ?>
                </div>
                

            </div>
            
            <!-- Carrousel des actualités -->
            <div class="gaisio-public-section">
                <h2 data-translate="news_title"><?php echo esc_html($t['news_title']); ?></h2>
                <p data-translate="news_desc"><?php echo esc_html($t['news_desc']); ?></p>
                <div class="gaisio-news-carousel" id="gaisio-news-section">
                    <div class="carousel-container">
                        <div class="carousel-slides" id="gaisio-news-slides">
                            <!-- Actualités chargées dynamiquement ici -->
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Carrousel des ressources -->
            <div class="gaisio-public-section">
                <h2 data-translate="resources_title"><?php echo esc_html($t['resources_title']); ?></h2>
                <p data-translate="resources_desc"><?php echo esc_html($t['resources_desc']); ?></p>
                <div class="gaisio-resources-carousel" id="gaisio-resources-section">
                    <div class="resources-container">
                        <div class="resources-slides" id="gaisio-resources-slides">
                            <!-- Ressources statiques -->
                            <div class="resource-card">
                                <div class="card-inner">
                                    <div class="card-front">
                                        <img src="https://sismo.ma/wp-content/uploads/2024/10/Geology-247x373.webp" alt="Guide de sismologie">
                                    </div>
                                    <div class="card-back">
                                        <h3>Article ressource test par lorem</h3>
                                        <p class="author">Jabour E</p>
                                        <p class="description"> 1 novembre 2024-Pétrographie</p>
                                        
                                    </div>
                                </div>
                            </div>
                            
                            <div class="resource-card">
                                <div class="card-inner">
                                    <div class="card-front">
                                        <img src="https://sismo.ma/wp-content/uploads/2024/11/978-3-319-07599-0-247x373.webp" alt="Rapport annuel">
                                    </div>
                                    <div class="card-back">
                                        <h3>Article ressource test par lorem</h3>
                                        <p class="author"></p>
                                        <p class="description">1 novembre 2024-
                                        Sismologie</p>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="resource-card">
                                <div class="card-inner">
                                    <div class="card-front">
                                        <img src="https://sismo.ma/wp-content/uploads/2024/11/978-3-319-76855-7-247x373.webp" alt="Méthodes de prévention">
                                    </div>
                                    <div class="card-back">
                                        <h3>Article ressource test par lorem</h3>
                                        <p class="author">Alaoui</p>
                                        <p class="description">1 novembre 2024-
                                        Sédimentologie</p>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="resource-card">
                                <div class="card-inner">
                                    <div class="card-front">
                                        <img src="https://sismo.ma/wp-content/uploads/2024/11/978-1-4020-8222-1-247x373.jpeg" alt="Atlas sismique">
                                    </div>
                                    <div class="card-back">
                                        <h3>Article ressource test par lorem</h3>
                                        <p class="author"> Karim Ed</p>
                                        <p class="description">1 novembre 2024-
                                        Tectonique</p>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="resource-card">
                                <div class="card-inner">
                                    <div class="card-front">
                                        <img src="https://sismo.ma/wp-content/uploads/2024/11/978-3-319-16964-4-247x373.jpeg" alt="Technologies de détection">
                                    </div>
                                    <div class="card-back">
                                        <h3>Article ressource test par lorem</h3>
                                        <p class="author">Sami alfred</p>
                                        <p class="description">1 novembre 2024-
                                        Sédimentologie</p>
                                    </div>
                                </div>
                            </div>
                            
                           
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Section de signalement intégrée automatiquement -->
            <div class="gaisio-public-section">
                <h2 data-translate="report_title"><?php echo esc_html($t['report_title']); ?></h2>
                <p data-translate="report_desc"><?php echo esc_html($t['report_desc']); ?></p>
                
                <!-- Section de signalement intégrée -->
                <section class="signalement-section">
                    <div class="av-container">
                        <div class="row">
                            <div class="col-12">
                                <div class="signalement-content">
                                    <div class="signalement-text">
                                        <h2>Avez-vous ressenti une secousse ?</h2>
                                        <p>En signalant une secousse, vous contribuez à collecter des informations précieuses qui aident à l'analyse des événements sismiques.</p>
                                    </div>
                                    <div class="signalement-button">
                                        <button type="button" class="btn-signaler" onclick="showSignalementForm()">
                                            <i class="fa fa-bell"></i>
                                            Signaler maintenant
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Formulaire de signalement (caché par défaut) -->
                    <div id="signalement-form-container" class="signalement-form-container" style="display: none;">
                        <div class="av-container">
                            <div class="row">
                                <div class="col-12">
                                    <div class="form-header">
                                        <h3>Formulaire de Signalement</h3>
                                        <button type="button" class="btn-close-form" onclick="hideSignalementForm()">
                                            <i class="fa fa-times"></i>
                                        </button>
                                    </div>
                                    
                                    <!-- Messages de succès/erreur -->
                                    <div id="signalement-messages"></div>
                                    
                                    <form class="signalement-form" id="signalement-form" method="post">
                                        <?php wp_nonce_field( 'signalement_nonce', 'signalement_nonce_field' ); ?>
                                        
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="signalement_date">Date et heure de la secousse *</label>
                                                    <input type="datetime-local" class="form-control" id="signalement_date" name="signalement_date" required>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="signalement_intensite">Intensité ressentie *</label>
                                                    <select class="form-control" id="signalement_intensite" name="signalement_intensite" required>
                                                        <option value="">Sélectionnez l'intensité</option>
                                                        <option value="1">1 - Très faible (à peine perceptible)</option>
                                                        <option value="2">2 - Faible (perceptible par quelques personnes)</option>
                                                        <option value="3">3 - Légère (perceptible par la plupart des personnes)</option>
                                                        <option value="4">4 - Modérée (réveille les personnes endormies)</option>
                                                        <option value="5">5 - Forte (peut causer des dommages légers)</option>
                                                        <option value="6">6 - Très forte (dommages modérés)</option>
                                                        <option value="7">7 - Majeure (dommages importants)</option>
                                                        <option value="8">8 - Très majeure (destruction massive)</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="signalement_duree">Durée approximative (secondes)</label>
                                                    <input type="number" class="form-control" id="signalement_duree" name="signalement_duree" min="1" max="300" placeholder="Ex: 15">
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="signalement_type">Type de mouvement ressenti</label>
                                                    <select class="form-control" id="signalement_type" name="signalement_type">
                                                        <option value="">Sélectionnez le type</option>
                                                        <option value="horizontal">Mouvement horizontal</option>
                                                        <option value="vertical">Mouvement vertical</option>
                                                        <option value="rotatif">Mouvement rotatif</option>
                                                        <option value="ondulant">Mouvement ondulant</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="form-group">
                                            <label for="signalement_localisation">Localisation (ville, quartier) *</label>
                                            <input type="text" class="form-control" id="signalement_localisation" name="signalement_localisation" placeholder="Ex: Paris, 8ème arrondissement" required>
                                        </div>
                                        
                                        <div class="form-group">
                                            <label for="signalement_description">Description détaillée</label>
                                            <textarea class="form-control" id="signalement_description" name="signalement_description" rows="4" placeholder="Décrivez ce que vous avez ressenti, les effets observés, etc."></textarea>
                                        </div>
                                        
                                        <div class="form-group">
                                            <label for="signalement_nom">Votre nom (optionnel)</label>
                                            <input type="text" class="form-control" id="signalement_nom" name="signalement_nom" placeholder="Votre nom">
                                        </div>
                                        
                                        <div class="form-group">
                                            <label for="signalement_email">Votre email (optionnel)</label>
                                            <input type="email" class="form-control" id="signalement_email" name="signalement_email" placeholder="votre@email.com">
                                        </div>
                                        
                                        <div class="form-actions">
                                            <button type="submit" id="submit-signalement" class="btn btn-primary btn-signaler-submit">
                                                <i class="fa fa-paper-plane"></i>
                                                Envoyer le signalement
                                            </button>
                                            <button type="button" class="btn btn-secondary btn-cancel" onclick="hideSignalementForm()">
                                                Annuler
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
                
                <!-- Modal du formulaire de signalement -->
 
            </div>
        </div>

        <script>
        // Fonction pour naviguer vers les sections existantes
        function showSection(sectionId) {
            // Mettre à jour le menu actif
            document.querySelectorAll('.nav-link').forEach(link => {
                link.classList.remove('active');
            });
            document.querySelector(`[data-section="${sectionId}"]`).classList.add('active');
            
            // Faire défiler vers la section correspondante
            let targetElement;
            switch(sectionId) {
                case 'accueil':
                    targetElement = document.querySelector('.gaisio-public-header');
                    break;
                case 'actualites':
                    targetElement = document.querySelector('.gaisio-news-carousel').closest('.gaisio-public-section');
                    break;
                case 'ressources':
                    targetElement = document.querySelector('.gaisio-resources-carousel').closest('.gaisio-public-section');
                    break;
                case 'signaler':
                    targetElement = document.querySelector('.signalement-section').closest('.gaisio-public-section');
                    break;
                default:
                    targetElement = document.querySelector('.gaisio-public-header');
            }
            
            if (targetElement) {
                targetElement.scrollIntoView({ behavior: 'smooth' });
            }
        }
        
        // Navigation par clic sur les liens du menu
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.nav-link').forEach(link => {
                link.addEventListener('click', function(e) {
                    e.preventDefault();
                    const sectionId = this.getAttribute('data-section');
                    showSection(sectionId);
                });
            });
            
            // Menu mobile toggle
            const navToggle = document.querySelector('.nav-toggle');
            const navMenu = document.querySelector('.nav-menu');
            
            if (navToggle && navMenu) {
                navToggle.addEventListener('click', function() {
                    navMenu.classList.toggle('active');
                    navToggle.classList.toggle('active');
                });
            }
        });
        
        // Fonction pour changer la langue

        </script>
        <?php
        return ob_get_clean();
    }
    
    // Shortcode pour la carte interactive
    public function earthquake_map_shortcode($atts) {
        ob_start();
        ?>
        <div class="gaisio-earthquake-map">
            
            <div id="gaisio-map" style="height: 500px; width: 100%; border-radius: 10px; overflow: hidden;"></div>
            
            <!-- Légende des symboles -->
            <div class="gaisio-map-legend">
                <h4>Sismos enregistrés&nbsp;:</h4>
                <div class="legend-items">
                    <div class="legend-item">
                        <span class="legend-symbol legend-latest"></span>
                        <span class="legend-text">Le plus récent (24h)</span>
                    </div>
                    <div class="legend-item">
                        <span class="legend-symbol legend-last24"></span>
                        <span class="legend-text">Dernières 24 heures</span>
                    </div>
                    <div class="legend-item">
                        <span class="legend-symbol legend-2-7"></span>
                        <span class="legend-text">Entre 2 et 7 jours</span>
                    </div>
                    <div class="legend-item">
                        <span class="legend-symbol legend-8-30"></span>
                        <span class="legend-text">Entre 8 et 30 jours</span>
                    </div>
                    <div class="legend-item">
                        <span class="legend-symbol legend-felt legend-circle"></span>
                        <span class="legend-text">Ressenti</span>
                    </div>
                </div>
            </div>
            
            <div class="gaisio-map-controls">
                
                <button id="center-map" class="gaisio-btn-secondary">🎯 Centrer la carte</button>
            </div>
        </div>
        <?php
        return ob_get_clean();
    }
    
    // Shortcode pour le footer
    public function footer_shortcode($atts) {
        ob_start();
        ?>
        <footer class="gaisio-footer">
            <div class="gaisio-footer-content">
                <p>Le Centre National pour la Recherche Scientifique et Technique (CNRST) et l'Institut National de Géophysique (ING) - © 2024</p>
            </div>
        </footer>
        <?php
        return ob_get_clean();
    }

    public function add_footer_once() {
        // Vérifier si on est sur une page qui contient nos shortcodes
        global $post;
        if (!$post) return;
        
        $content = $post->post_content;
        
        // Détection plus précise des shortcodes Gaisio
        $gaisio_shortcodes = array(
            '[gaisio_login_form]',
            '[gaisio_register_form]',
            '[gaisio_user_dashboard]',
            '[gaisio_earthquake_form]',
            '[gaisio_earthquake_table]',
            '[gaisio_public_home]',
            '[gaisio_earthquake_map]',
            '[gaisio_footer]',
            '[gaisio_user_login]'
        );
        
        $has_gaisio_shortcode = false;
        foreach ($gaisio_shortcodes as $shortcode) {
            if (strpos($content, $shortcode) !== false) {
                $has_gaisio_shortcode = true;
                break;
            }
        }
        
        // N'ajouter le footer que si on a un shortcode Gaisio sur la page
        if ($has_gaisio_shortcode) {
            // Utiliser un flag pour éviter la répétition
            static $footer_added = false;
            if (!$footer_added) {
                echo do_shortcode('[gaisio_footer]');
                $footer_added = true;
            }
        }
    }
    
    // Fonction pour calculer automatiquement la magnitude avec reverse IPI centre
    private function calculate_magnitude($latitude, $longitude, $depth) {
        // Algorithme reverse IPI centre pour le calcul de magnitude
        // Basé sur la relation inverse entre intensité, profondeur et distance
        
        // Calcul de la distance épicentrale (distance depuis l'épicentre)
        $epicentral_distance = $this->calculate_epicentral_distance($latitude, $longitude, $depth);
        
        // Calcul de l'intensité locale basée sur la profondeur
        $local_intensity = $this->calculate_local_intensity($depth);
        
        // Calcul de l'atténuation avec la distance
        $attenuation_factor = $this->calculate_attenuation($epicentral_distance);
        
        // Calcul de la magnitude basé sur l'intensité et l'atténuation
        $magnitude = $this->intensity_to_magnitude($local_intensity, $attenuation_factor);
        
        // Limiter la magnitude entre 2.0 et 8.5 (réaliste pour la plupart des séismes)
        $magnitude = max(2.0, min(8.5, $magnitude));
        
        return round($magnitude, 1);
    }
    
    // Calcul de la distance épicentrale
    private function calculate_epicentral_distance($latitude, $longitude, $depth) {
        // Distance hypocentrale = sqrt(distance_surface² + profondeur²)
        $surface_distance = sqrt($latitude * $latitude + $longitude * $longitude);
        $hypocentral_distance = sqrt($surface_distance * $surface_distance + $depth * $depth);
        
        return $hypocentral_distance;
    }
    
    // Calcul de l'intensité locale basée sur la profondeur
    private function calculate_local_intensity($depth) {
        // Relation inverse : plus la profondeur est faible, plus l'intensité locale est élevée
        $base_intensity = 6.0; // Intensité de base
        
        if ($depth < 10) {
            // Tremblements très peu profonds (crustaux)
            $intensity_factor = 1.3;
        } elseif ($depth < 30) {
            // Tremblements peu profonds
            $intensity_factor = 1.1;
        } elseif ($depth < 70) {
            // Profondeur moyenne
            $intensity_factor = 1.0;
        } else {
            // Tremblements profonds (subduction)
            $intensity_factor = 0.9;
        }
        
        return $base_intensity * $intensity_factor;
    }
    
    // Calcul de l'atténuation avec la distance
    private function calculate_attenuation($distance) {
        // Formule d'atténuation géométrique et anélastique
        // Basée sur la relation de Gutenberg-Richter
        
        if ($distance < 10) {
            // Zone proche de l'épicentre
            $attenuation = 1.0;
        } elseif ($distance < 100) {
            // Zone régionale
            $attenuation = 1.0 - (log10($distance / 10) * 0.3);
        } else {
            // Zone distante
            $attenuation = 1.0 - (log10($distance / 10) * 0.4);
        }
        
        return max(0.1, $attenuation); // Minimum 0.1
    }
    
    // Conversion de l'intensité en magnitude
    private function intensity_to_magnitude($intensity, $attenuation) {
        // Relation empirique entre intensité et magnitude
        // Basée sur la relation de Gutenberg-Richter modifiée
        
        $effective_intensity = $intensity * $attenuation;
        
        // Relation linéaire avec facteur de correction
        $magnitude = 2.0 + ($effective_intensity - 2.0) * 0.8;
        
        // Ajouter une variation aléatoire pour simuler la variabilité naturelle
        $random_variation = (mt_rand(-20, 20) / 100); // ±0.2
        $magnitude += $random_variation;
        
        return $magnitude;
    }

    public function save_earthquake_data() {
        check_ajax_referer('gaisio_nonce', 'nonce');
        
        if (!is_user_logged_in()) {
            wp_die('Utilisateur non connecté');
        }
        
        $user_id = get_current_user_id();
        $datetime_utc = sanitize_text_field($_POST['datetime_utc']);
        $latitude = floatval($_POST['latitude']);
        $longitude = floatval($_POST['longitude']);
        $depth = floatval($_POST['depth']);
        $magnitude = floatval($_POST['magnitude']);
        
        // Validation de la magnitude
        if ($magnitude < 0.0 || $magnitude > 10.0) {
            wp_send_json_error('La magnitude doit être comprise entre 0.0 et 10.0');
        }
        
        global $wpdb;
        $table = $wpdb->prefix . 'gaisio_earthquakes';
        
        $result = $wpdb->insert(
            $table,
            array(
                'user_id' => $user_id,
                'datetime_utc' => $datetime_utc,
                'latitude' => $latitude,
                'longitude' => $longitude,
                'depth' => $depth,
                'magnitude' => $magnitude
            ),
            array('%d', '%s', '%f', '%f', '%f', '%f')
        );
        
        if ($result) {
            wp_send_json_success('Tremblement de terre enregistré avec succès (Magnitude: ' . $magnitude . ')');
        } else {
            wp_send_json_error('Erreur lors de l\'enregistrement');
        }
    }
    

    
    // Fonction AJAX pour récupérer les tremblements de terre
    public function get_earthquakes() {
        check_ajax_referer('gaisio_nonce', 'nonce');
        
        global $wpdb;
        $table = $wpdb->prefix . 'gaisio_earthquakes';
        $users_table = $wpdb->prefix . 'users';
        
        $earthquakes = $wpdb->get_results("
            SELECT e.*, u.display_name 
            FROM $table e 
            LEFT JOIN $users_table u ON e.user_id = u.ID 
            ORDER BY e.datetime_utc DESC
        ");
        
        wp_send_json_success($earthquakes);
    }
    
    // Fonction AJAX pour récupérer les statistiques
    public function get_stats() {
        check_ajax_referer('gaisio_nonce', 'nonce');
        
        global $wpdb;
        $earthquakes_table = $wpdb->prefix . 'gaisio_earthquakes';
        $users_table = $wpdb->prefix . 'gaisio_users';
        
        // Nombre total de tremblements de terre
        $total_earthquakes = $wpdb->get_var("SELECT COUNT(*) FROM $earthquakes_table");
        
        // Nombre total d'utilisateurs
        $total_users = $wpdb->get_var("SELECT COUNT(*) FROM $users_table");
        
        // Magnitude la plus élevée
        $latest_magnitude = $wpdb->get_var("
            SELECT magnitude 
            FROM $earthquakes_table 
            WHERE magnitude IS NOT NULL 
            ORDER BY magnitude DESC 
            LIMIT 1
        ");
        
        $stats = array(
            'total_earthquakes' => intval($total_earthquakes),
            'total_users' => intval($total_users),
            'latest_magnitude' => $latest_magnitude ? number_format($latest_magnitude, 1) : '0.0'
        );
        
        wp_send_json_success($stats);
    }
    
    // Fonction AJAX pour récupérer les informations de localisation
    public function get_location_info() {
        check_ajax_referer('gaisio_nonce', 'nonce');
        
        $latitude = floatval($_POST['latitude']);
        $longitude = floatval($_POST['longitude']);
        
        // Utiliser l'API Nominatim (OpenStreetMap) pour le géocodage inverse
        $url = "https://nominatim.openstreetmap.org/reverse?format=json&lat={$latitude}&lon={$longitude}&zoom=10&addressdetails=1";
        
        $response = wp_remote_get($url, array(
            'timeout' => 10,
            'user-agent' => 'Gaisio Earthquake Manager/1.0'
        ));
        
        if (is_wp_error($response)) {
            wp_send_json_error('Erreur lors de la récupération des informations de localisation');
        }
        
        $body = wp_remote_retrieve_body($response);
        $data = json_decode($body, true);
        
        if (!$data || !isset($data['address'])) {
            wp_send_json_error('Aucune information de localisation trouvée');
        }
        
        $address = $data['address'];
        
        // Extraire la commune et la province
        $commune = '';
        $province = '';
        
        // Chercher la commune dans différents champs possibles
        if (isset($address['city'])) {
            $commune = $address['city'];
        } elseif (isset($address['town'])) {
            $commune = $address['town'];
        } elseif (isset($address['village'])) {
            $commune = $address['village'];
        } elseif (isset($address['municipality'])) {
            $commune = $address['municipality'];
        } elseif (isset($address['county'])) {
            $commune = $address['county'];
        }
        
        // Chercher la province dans différents champs possibles
        if (isset($address['state'])) {
            $province = $address['state'];
        } elseif (isset($address['province'])) {
            $province = $address['province'];
        } elseif (isset($address['region'])) {
            $province = $address['region'];
        }
        
        // Si pas de commune trouvée, utiliser le nom du lieu
        if (empty($commune) && isset($data['display_name'])) {
            $parts = explode(',', $data['display_name']);
            $commune = trim($parts[0]);
        }
        
        $location_info = array(
            'commune' => $commune ?: 'Non déterminée',
            'province' => $province ?: 'Non déterminée',
            'full_address' => isset($data['display_name']) ? $data['display_name'] : ''
        );
        
        wp_send_json_success($location_info);
    }
    
    // Fonction AJAX pour récupérer les actualités depuis le frontend
    public function get_news_frontend() {
        check_ajax_referer('gaisio_nonce', 'nonce');
        
        global $wpdb;
        $table_news = $wpdb->prefix . 'gaisio_news';
        
        // Récupérer seulement les actualités publiées depuis la base de données
        $news = $wpdb->get_results("
            SELECT * FROM $table_news 
            WHERE status = 'published'
            ORDER BY created_at DESC
            LIMIT 6
        ");
        
        // Si aucune actualité en base, retourner un tableau vide
        if (empty($news)) {
            wp_send_json_success(array());
        }
        
        wp_send_json_success($news);
    }
    
    // Shortcode pour le carrousel d'actualités
    public function news_carousel_shortcode($atts) {
        // Attributs par défaut
        $atts = shortcode_atts(array(
            'interval' => '3000', // Intervalle en millisecondes (3 secondes par défaut)
            'title' => '📰 Actualités',
            'description' => 'Dernières actualités de l\'institut'
        ), $atts);
        
        ob_start();
        ?>
        <div class="gaisio-public-section">
            <h2><?php echo esc_html($atts['title']); ?></h2>
            <p><?php echo esc_html($atts['description']); ?></p>
            <div class="gaisio-news-carousel" id="gaisio-news-section" data-interval="<?php echo esc_attr($atts['interval']); ?>">
                <div class="carousel-container">
                    <div class="carousel-slides" id="gaisio-news-slides">
                        <div class="loading">Chargement des actualités...</div>
                    </div>
                </div>
            </div>
        </div>
        <?php
        return ob_get_clean();
    }
    
    // Shortcode pour la page d'administration publique
    public function admin_page_shortcode($atts) {
        // Vérifier si l'utilisateur est connecté et a les permissions d'administration
        if (!is_user_logged_in() || !current_user_can('manage_options')) {
            return '<div class="gaisio-error">Accès refusé. Vous devez être administrateur pour accéder à cette page.</div>';
        }
        
        ob_start();
        ?>
        <div class="gaisio-admin-public">
            <div class="gaisio-admin-tabs">
                <div class="admin-menu-left">
                    <div class="admin-logo">
                        <img src="<?php echo plugin_dir_url(__FILE__); ?>assets/images/logo-morseps2.png" alt="Logo Gaisio Admin" class="admin-logo-img">
                        <div class="admin-logo-text">
                            <span class="logo-title">Gaisio</span>
                            <span class="logo-subtitle">Administration</span>
                        </div>
                    </div>
                </div>
                
                <div class="admin-menu-center">
                    <button class="tab-button active" data-tab="news">
                        <span class="tab-icon">📰</span>
                        <span class="tab-text">Actualités</span>
                    </button>
                    <button class="tab-button" data-tab="users">
                        <span class="tab-icon">👥</span>
                        <span class="tab-text">Utilisateurs</span>
                    </button>
                    <button class="tab-button" data-tab="stats">
                        <span class="tab-icon">📊</span>
                        <span class="tab-text">Statistiques</span>
                    </button>
                    <button class="tab-button admin-logout-btn" onclick="adminLogout()">
                        <span class="logout-icon">🚪</span>
                        <span class="tab-text">Se déconnecter</span>
                    </button>
                </div>
            </div>
            
            <!-- Onglet Actualités -->
            <div id="tab-news" class="tab-content active">
                <div class="gaisio-admin-section">
                    <h2>📰 Ajouter une Actualité</h2>
                    <form id="gaisio-news-form">
                        <div class="form-group">
                            <label for="news-title">Titre de l'actualité *</label>
                            <input type="text" id="news-title" name="title" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="news-content">Contenu *</label>
                            <textarea id="news-content" name="content" rows="5" required></textarea>
                        </div>
                        
                        <div class="form-group">
                            <label for="news-image">URL de l'image (optionnel)</label>
                            <input type="url" id="news-image" name="image_url" placeholder="https://example.com/image.jpg">
                        </div>
                        
                        <div class="form-group">
                            <label for="news-status">Statut</label>
                            <select id="news-status" name="status">
                                <option value="published">Publié</option>
                                <option value="draft">Brouillon</option>
                            </select>
                        </div>
                        
                        <button type="submit" class="gaisio-btn">💾 Enregistrer l'actualité</button>
                    </form>
                </div>
                
                <div class="gaisio-admin-section">
                    <h2>📰 Actualités existantes</h2>
                    <div id="gaisio-news-list">
                        <div class="loading">Chargement des actualités...</div>
                    </div>
                </div>
            </div>
            
            <!-- Onglet Utilisateurs -->
            <div id="tab-users" class="tab-content">
                <div class="gaisio-admin-section">
                    <h2>👥 Créer un nouvel utilisateur</h2>
                    <form id="gaisio-create-user-form">
                        <div class="form-row">
                            <div class="form-group">
                                <label for="user-username">Nom d'utilisateur *</label>
                                <input type="text" id="user-username" name="username" required>
                            </div>
                            <div class="form-group">
                                <label for="user-email">Email *</label>
                                <input type="email" id="user-email" name="email" required>
                            </div>
                        </div>
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label for="user-display-name">Nom d'affichage *</label>
                                <input type="text" id="user-display-name" name="display_name" required>
                            </div>
                            <div class="form-group">
                                <label for="user-role">Rôle</label>
                                <select id="user-role" name="role">
                                    <option value="subscriber">Abonné</option>
                                    <option value="contributor">Contributeur</option>
                                    <option value="author">Auteur</option>
                                    <option value="editor">Éditeur</option>
                                    <option value="administrator">Administrateur</option>
                                </select>
                            </div>
                        </div>
                        
                        <button type="submit" class="gaisio-btn">👤 Créer l'utilisateur</button>
                    </form>
                </div>
                
                <div class="gaisio-admin-section">
                    <h2>👥 Utilisateurs enregistrés</h2>
                    <div id="gaisio-users-list">
                        <div class="loading">Chargement des utilisateurs...</div>
                    </div>
                </div>
            </div>
            
            <!-- Onglet Statistiques -->
            <div id="tab-stats" class="tab-content">
                <div class="gaisio-admin-section">
                    <h2>📊 Statistiques générales</h2>
                    <div id="gaisio-stats-display">
                        <div class="loading">Chargement des statistiques...</div>
                    </div>
                </div>
            </div>
            
            <!-- Footer de la partie publique -->
            <div class="gaisio-admin-footer">
                <?php echo do_shortcode('[gaisio_footer]'); ?>
            </div>
        </div>
        
        <script>
        function adminLogout() {
            if (confirm('Êtes-vous sûr de vouloir vous déconnecter ?')) {
                // Redirection directe vers la page de login
                window.location.href = '<?php echo home_url("/login/"); ?>';
            }
        }
        </script>
        <?php
        return ob_get_clean();
    }
    
    // Shortcode pour le formulaire de connexion utilisateur
    public function user_login_shortcode($atts) {
        // Si l'utilisateur est déjà connecté, afficher le tableau de bord
        if (is_user_logged_in()) {
            return do_shortcode('[gaisio_user_dashboard]');
        }
        
        $atts = shortcode_atts(array(
            'redirect' => '',
            'title' => '🔐 Connexion Utilisateur'
        ), $atts);
        
        ob_start();
        ?>
        <div class="gaisio-login-container">
            <div class="gaisio-login-form">
                <h2><?php echo esc_html($atts['title']); ?></h2>
                <p class="login-description">Connectez-vous avec vos identifiants générés par l'administrateur</p>
                
                <form id="gaisio-user-login-form">
                    <div class="form-group">
                        <label for="login-username">Nom d'utilisateur *</label>
                        <input type="text" id="login-username" name="username" required 
                               placeholder="Votre nom d'utilisateur">
                    </div>
                    
                    <div class="form-group">
                        <label for="login-access-code">Code d'accès *</label>
                        <input type="password" id="login-access-code" name="access_code" required 
                               placeholder="Votre code d'accès">
                    </div>
                    
                    <div class="form-group">
                        <label class="checkbox-label">
                            <input type="checkbox" id="login-remember" name="remember">
                            <span class="checkmark"></span>
                            Se souvenir de moi
                        </label>
                    </div>
                    
                    <button type="submit" class="gaisio-btn login-btn">
                        <span class="btn-text">🔐 Se connecter</span>
                        <span class="btn-loading" style="display: none;">
                            <span class="spinner"></span> Connexion...
                        </span>
                    </button>
                </form>
                
                <div class="login-help">
                    <p>💡 <strong>Besoin d'aide ?</strong></p>
                    <p>Contactez votre administrateur pour obtenir vos identifiants de connexion.</p>
                </div>
                
                <div id="login-message" class="login-message" style="display: none;"></div>
            </div>
        </div>
        <?php
        return ob_get_clean();
    }
    
    // ========================================
    // FONCTIONS D'ADMINISTRATION
    // ========================================
    
    // Ajouter le menu d'administration
    public function add_admin_menu() {
        add_menu_page(
            'Gaisio Admin',
            'Gaisio Admin',
            'manage_options',
            'gaisio-admin',
            array($this, 'admin_page'),
            'dashicons-admin-site',
            30
        );
    }
    
    // Charger les scripts d'administration
    public function admin_enqueue_scripts($hook) {
        if ($hook !== 'toplevel_page_gaisio-admin') {
            return;
        }
        
        wp_enqueue_style('gaisio-admin-css', GAISIO_PLUGIN_URL . 'css/gaisio-admin.css', array(), '1.0.0');
        wp_enqueue_script('gaisio-admin-js', GAISIO_PLUGIN_URL . 'js/gaisio-admin.js', array('jquery'), '1.0.0', true);
        
        wp_localize_script('gaisio-admin-js', 'gaisio_admin', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('gaisio_admin_nonce')
        ));
        
        wp_localize_script('gaisio-public', 'gaisio_public', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('gaisio_public_nonce')
        ));
        
        wp_localize_script('gaisio-public', 'gaisio_user', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('gaisio_user_nonce')
        ));
    }
    
    // Page d'administration
    public function admin_page() {
        ?>
        <div class="wrap">
            <h1>🌍 Gaisio Administration</h1>
            
            <div class="gaisio-admin-tabs">
                <button class="tab-button active" data-tab="news">📰 Gestion des Actualités</button>
                <button class="tab-button" data-tab="users">👥 Gestion des Utilisateurs</button>
                <button class="tab-button" data-tab="stats">📊 Statistiques</button>
            </div>
            
            <!-- Onglet Actualités -->
            <div id="tab-news" class="tab-content active">
                <div class="gaisio-admin-section">
                    <h2>📰 Ajouter une Actualité</h2>
                    <form id="gaisio-news-form">
                        <div class="form-group">
                            <label for="news-title">Titre de l'actualité *</label>
                            <input type="text" id="news-title" name="title" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="news-content">Contenu *</label>
                            <textarea id="news-content" name="content" rows="5" required></textarea>
                        </div>
                        
                        <div class="form-group">
                            <label for="news-image">URL de l'image (optionnel)</label>
                            <input type="url" id="news-image" name="image_url" placeholder="https://example.com/image.jpg">
                        </div>
                        
                        <div class="form-group">
                            <label for="news-status">Statut</label>
                            <select id="news-status" name="status">
                                <option value="published">Publié</option>
                                <option value="draft">Brouillon</option>
                            </select>
                        </div>
                        
                        <button type="submit" class="gaisio-btn">💾 Enregistrer l'actualité</button>
                    </form>
                </div>
                
                <div class="gaisio-admin-section">
                    <h2>📰 Actualités existantes</h2>
                    <div id="gaisio-news-list">
                        <div class="loading">Chargement des actualités...</div>
                    </div>
                </div>
            </div>
            
            <!-- Onglet Utilisateurs -->
            <div id="tab-users" class="tab-content">
                <div class="gaisio-admin-section">
                    <h2>👥 Créer un nouvel utilisateur</h2>
                    <form id="gaisio-create-user-form">
                        <div class="form-row">
                            <div class="form-group">
                                <label for="user-username">Nom d'utilisateur *</label>
                                <input type="text" id="user-username" name="username" required>
                            </div>
                            <div class="form-group">
                                <label for="user-email">Email *</label>
                                <input type="email" id="user-email" name="email" required>
                            </div>
                        </div>
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label for="user-display-name">Nom d'affichage *</label>
                                <input type="text" id="user-display-name" name="display_name" required>
                            </div>
                            <div class="form-group">
                                <label for="user-role">Rôle</label>
                                <select id="user-role" name="role">
                                    <option value="subscriber">Abonné</option>
                                    <option value="contributor">Contributeur</option>
                                    <option value="author">Auteur</option>
                                    <option value="editor">Éditeur</option>
                                    <option value="administrator">Administrateur</option>
                                </select>
                            </div>
                        </div>
                        
                        <button type="submit" class="gaisio-btn">👤 Créer l'utilisateur</button>
                    </form>
                </div>
                
                <div class="gaisio-admin-section">
                    <h2>👥 Utilisateurs enregistrés</h2>
                    <div id="gaisio-users-list">
                        <div class="loading">Chargement des utilisateurs...</div>
                    </div>
                </div>
            </div>
            
            <!-- Onglet Statistiques -->
            <div id="tab-stats" class="tab-content">
                <div class="gaisio-admin-section">
                    <h2>📊 Statistiques générales</h2>
                    <div id="gaisio-stats-display">
                        <div class="loading">Chargement des statistiques...</div>
                    </div>
                </div>
            </div>
            
            <!-- Footer de la partie publique -->
            <div class="gaisio-admin-footer">
                <?php echo do_shortcode('[gaisio_footer]'); ?>
            </div>
        </div>
        <?php
    }
    
    // Fonction AJAX pour sauvegarder une actualité
    public function admin_save_news() {
        check_ajax_referer('gaisio_admin_nonce', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_send_json_error('Permissions insuffisantes');
        }
        
        $title = sanitize_text_field($_POST['title']);
        $content = sanitize_textarea_field($_POST['content']);
        $image_url = esc_url_raw($_POST['image_url']);
        $status = sanitize_text_field($_POST['status']);
        
        if (empty($title) || empty($content)) {
            wp_send_json_error('Le titre et le contenu sont obligatoires');
        }
        
        global $wpdb;
        $table_news = $wpdb->prefix . 'gaisio_news';
        
        $result = $wpdb->insert(
            $table_news,
            array(
                'title' => $title,
                'content' => $content,
                'image_url' => $image_url,
                'status' => $status
            ),
            array('%s', '%s', '%s', '%s')
        );
        
        if ($result === false) {
            wp_send_json_error('Erreur lors de l\'enregistrement de l\'actualité');
        }
        
        wp_send_json_success('Actualité enregistrée avec succès');
    }
    
    // Fonction AJAX pour mettre à jour une actualité
    public function admin_update_news() {
        check_ajax_referer('gaisio_admin_nonce', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_send_json_error('Permissions insuffisantes');
        }
        
        $news_id = intval($_POST['news_id']);
        $title = sanitize_text_field($_POST['title']);
        $content = sanitize_textarea_field($_POST['content']);
        $image_url = esc_url_raw($_POST['image_url']);
        $status = sanitize_text_field($_POST['status']);
        
        if (empty($title) || empty($content)) {
            wp_send_json_error('Le titre et le contenu sont obligatoires');
        }
        
        global $wpdb;
        $table_news = $wpdb->prefix . 'gaisio_news';
        
        $result = $wpdb->update(
            $table_news,
            array(
                'title' => $title,
                'content' => $content,
                'image_url' => $image_url,
                'status' => $status,
                'updated_at' => current_time('mysql')
            ),
            array('id' => $news_id),
            array('%s', '%s', '%s', '%s', '%s'),
            array('%d')
        );
        
        if ($result === false) {
            wp_send_json_error('Erreur lors de la mise à jour de l\'actualité');
        }
        
        wp_send_json_success('Actualité mise à jour avec succès');
    }
    
    // Fonction AJAX pour récupérer les actualités
    public function admin_get_news() {
        check_ajax_referer('gaisio_admin_nonce', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_send_json_error('Permissions insuffisantes');
        }
        
        global $wpdb;
        $table_news = $wpdb->prefix . 'gaisio_news';
        
        $news = $wpdb->get_results("
            SELECT * FROM $table_news 
            ORDER BY created_at DESC
        ");
        
        wp_send_json_success($news);
    }
    
    // Fonction AJAX pour supprimer une actualité
    public function admin_delete_news() {
        check_ajax_referer('gaisio_admin_nonce', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_send_json_error('Permissions insuffisantes');
        }
        
        $news_id = intval($_POST['news_id']);
        
        global $wpdb;
        $table_news = $wpdb->prefix . 'gaisio_news';
        
        $result = $wpdb->delete(
            $table_news,
            array('id' => $news_id),
            array('%d')
        );
        
        if ($result === false) {
            wp_send_json_error('Erreur lors de la suppression');
        }
        
        wp_send_json_success('Actualité supprimée avec succès');
    }
    
    // Fonction AJAX pour récupérer les utilisateurs
    public function admin_get_users() {
        check_ajax_referer('gaisio_admin_nonce', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_send_json_error('Permissions insuffisantes');
        }
        
        global $wpdb;
        $table_users = $wpdb->prefix . 'gaisio_users';
        
        $users = $wpdb->get_results("
            SELECT gu.*, u.display_name, u.user_email, u.user_login
            FROM $table_users gu
            LEFT JOIN {$wpdb->users} u ON gu.user_id = u.ID
            ORDER BY gu.created_at DESC
        ");
        
        // Ajouter les informations de rôle pour chaque utilisateur
        foreach ($users as $user) {
            $wp_user = get_user_by('ID', $user->user_id);
            if ($wp_user) {
                $user->role = $wp_user->roles[0] ?? 'subscriber';
            } else {
                $user->role = 'subscriber';
            }
        }
        
        wp_send_json_success($users);
    }
    
    // Fonction AJAX pour supprimer un utilisateur
    public function admin_delete_user() {
        check_ajax_referer('gaisio_admin_nonce', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_send_json_error('Permissions insuffisantes');
        }
        
        $user_id = intval($_POST['user_id']);
        
        // Log pour débogage
        error_log('Gaisio: Tentative de suppression de l\'utilisateur ID: ' . $user_id);
        
        global $wpdb;
        $table_users = $wpdb->prefix . 'gaisio_users';
        
        // Vérifier si l'utilisateur existe dans la table Gaisio
        $user_exists = $wpdb->get_var($wpdb->prepare(
            "SELECT COUNT(*) FROM $table_users WHERE user_id = %d",
            $user_id
        ));
        
        if (!$user_exists) {
            wp_send_json_error('Utilisateur non trouvé dans la base de données Gaisio');
        }
        
        // Supprimer d'abord de la table Gaisio
        $result_gaisio = $wpdb->delete(
            $table_users,
            array('user_id' => $user_id),
            array('%d')
        );
        
        if ($result_gaisio === false) {
            error_log('Gaisio: Erreur suppression table Gaisio - ' . $wpdb->last_error);
            wp_send_json_error('Erreur lors de la suppression de la table Gaisio: ' . $wpdb->last_error);
        }
        
        error_log('Gaisio: Utilisateur supprimé de la table Gaisio avec succès');
        
        // Supprimer ensuite de la table WordPress
        $user = get_user_by('ID', $user_id);
        if ($user) {
            $result_wp = wp_delete_user($user_id);
            
            if ($result_wp === false) {
                error_log('Gaisio: Erreur suppression WordPress - ID: ' . $user_id);
                wp_send_json_error('Utilisateur supprimé de Gaisio mais erreur lors de la suppression WordPress');
            }
            
            error_log('Gaisio: Utilisateur supprimé de WordPress avec succès');
        } else {
            error_log('Gaisio: Utilisateur non trouvé dans WordPress - ID: ' . $user_id);
        }
        
        // Nettoyer les données orphelines si nécessaire
        $this->cleanup_orphaned_data($user_id);
        
        wp_send_json_success('Utilisateur supprimé avec succès de toutes les bases de données');
    }
    
    // Fonction pour nettoyer les données orphelines
    private function cleanup_orphaned_data($user_id) {
        global $wpdb;
        
        // Supprimer les métadonnées utilisateur orphelines
        $wpdb->delete(
            $wpdb->usermeta,
            array('user_id' => $user_id),
            array('%d')
        );
        
        // Supprimer les posts orphelins de cet utilisateur (optionnel)
        // $wpdb->delete(
        //     $wpdb->posts,
        //     array('post_author' => $user_id),
        //     array('%d')
        // );
        
        error_log('Gaisio: Nettoyage des données orphelines terminé pour l\'utilisateur ID: ' . $user_id);
    }
    
    // Fonction AJAX pour créer un utilisateur
    public function admin_create_user() {
        // Log pour débogage
        error_log('Gaisio: admin_create_user appelé');
        
        check_ajax_referer('gaisio_admin_nonce', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_send_json_error('Permissions insuffisantes');
        }
        
        $email = sanitize_email($_POST['email']);
        $display_name = sanitize_text_field($_POST['display_name']);
        
        // Log des données reçues
        error_log('Gaisio: Email reçu: ' . $email);
        error_log('Gaisio: Nom d\'affichage reçu: ' . $display_name);
        
        // Validation des données
        if (empty($email) || empty($display_name)) {
            wp_send_json_error('L\'email et le nom d\'affichage sont obligatoires');
        }
        
        if (email_exists($email)) {
            wp_send_json_error('Cette adresse email existe déjà');
        }
        
        // Générer automatiquement un identifiant de connexion basé sur le nom d'affichage
        $base_username = sanitize_user($display_name);
        $username = $this->generate_unique_username($base_username);
        
        // Générer automatiquement un code d'accès (mot de passe)
        $access_code = $this->generate_access_code();
        
        // Créer l'utilisateur WordPress
        $user_id = wp_create_user($username, $access_code, $email);
        
        if (is_wp_error($user_id)) {
            wp_send_json_error('Erreur lors de la création du compte: ' . $user_id->get_error_message());
        }
        
        // Mettre à jour les informations de l'utilisateur
        wp_update_user(array(
            'ID' => $user_id,
            'display_name' => $display_name,
            'first_name' => $display_name,
            'role' => 'subscriber' // Rôle par défaut
        ));
        
        // Ajouter l'utilisateur à la table Gaisio
        global $wpdb;
        $table = $wpdb->prefix . 'gaisio_users';
        
        $result = $wpdb->insert(
            $table,
            array(
                'user_id' => $user_id,
                'username' => $username,
                'email' => $email,
                'access_code' => $access_code
            ),
            array('%d', '%s', '%s', '%s')
        );
        
        if ($result) {
            // Ne plus envoyer d'email - fonctionnalité supprimée
            // $email_sent = $this->send_login_credentials_email($email, $display_name, $username, $access_code);
            
            $user_info = array(
                'user_id' => $user_id,
                'username' => $username,
                'email' => $email,
                'display_name' => $display_name,
                'access_code' => $access_code,
                'role' => 'subscriber'
            );
            
            $message = 'Utilisateur créé avec succès ! Les informations de connexion sont affichées ci-dessous.';
            
            wp_send_json_success(array(
                'message' => $message,
                'user_info' => $user_info,
                'email_sent' => false, // Plus d'envoi d'email
                'download_button' => '<button class="gaisio-btn-download" onclick="downloadUserCredentials(' . $user_id . ')">📄 Télécharger</button>'
            ));
        } else {
            wp_send_json_error('Erreur lors de l\'enregistrement dans la base de données');
        }
    }
    
    // Fonction pour générer un nom d'utilisateur unique
    private function generate_unique_username($base_username) {
        $username = $base_username;
        $counter = 1;
        
        // Vérifier si le nom d'utilisateur existe déjà
        while (username_exists($username)) {
            $username = $base_username . $counter;
            $counter++;
        }
        
        return $username;
    }
    
    // Fonction pour générer un code d'accès sécurisé
    private function generate_access_code() {
        // Générer un code d'accès de 8 caractères avec lettres et chiffres
        $characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        $access_code = '';
        
        for ($i = 0; $i < 8; $i++) {
            $access_code .= $characters[rand(0, strlen($characters) - 1)];
        }
        
        return $access_code;
    }
    
    // Fonction d'envoi d'email supprimée - plus d'envoi automatique d'emails
    
    // Fonction d'envoi d'email supprimée - plus d'envoi automatique d'emails
    
    // Fonction d'envoi d'email supprimée - plus d'envoi automatique d'emails
    
    // Fonction d'envoi d'email supprimée - plus d'envoi automatique d'emails
    
    // Fonction pour envoyer l'email de réinitialisation de mot de passe
    private function send_password_reset_email($email, $display_name, $username, $new_access_code) {
        $site_name = get_bloginfo('name');
        $site_url = get_site_url();
        $login_url = wp_login_url();
        
        $subject = sprintf('[%s] Votre nouveau code d\'accès', $site_name);
        
        // Message HTML pour un meilleur affichage
        $html_message = sprintf(
            '<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: #ff9800; color: white; padding: 20px; text-align: center; border-radius: 8px 8px 0 0; }
        .content { background: #f9f9f9; padding: 20px; border-radius: 0 0 8px 8px; }
        .credentials { background: #e3f2fd; border: 2px solid #2196f3; padding: 15px; margin: 20px 0; border-radius: 5px; }
        .warning { background: #fff3cd; border: 2px solid #ffc107; padding: 15px; margin: 20px 0; border-radius: 5px; }
        .button { display: inline-block; background: #2196f3; color: white; padding: 12px 24px; text-decoration: none; border-radius: 5px; margin: 10px 0; }
        .footer { text-align: center; margin-top: 20px; color: #666; font-size: 12px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🔑 Nouveau code d\'accès</h1>
        </div>
        <div class="content">
            <h2>Bonjour %s,</h2>
            <p>Votre code d\'accès a été réinitialisé sur <strong>%s</strong>.</p>
            
            <div class="credentials">
                <h3>🔐 Vos nouvelles informations de connexion :</h3>
                <p><strong>Identifiant de connexion :</strong> <code style="background: #fff; padding: 4px 8px; border-radius: 3px; font-weight: bold;">%s</code></p>
                <p><strong>Nouveau code d\'accès :</strong> <code style="background: #fff; padding: 4px 8px; border-radius: 3px; font-weight: bold; font-size: 16px;">%s</code></p>
                <p><strong>URL de connexion :</strong> <a href="%s">%s</a></p>
            </div>
            
            <div class="warning">
                <h3>⚠️ IMPORTANT :</h3>
                <ul>
                    <li>Ce nouveau code d\'accès remplace l\'ancien</li>
                    <li>Gardez ces informations en sécurité</li>
                    <li>Changez votre code d\'accès après votre prochaine connexion</li>
                    <li>Ne partagez jamais vos informations de connexion</li>
                </ul>
            </div>
            
            <p style="text-align: center;">
                <a href="%s" class="button">Se connecter maintenant</a>
            </p>
        </div>
        <div class="footer">
            <p>Cordialement,<br>L\'équipe %s</p>
        </div>
    </div>
</body>
</html>',
            $display_name,
            $site_name,
            $username,
            $new_access_code,
            $login_url,
            $login_url,
            $login_url,
            $site_name
        );
        
        // Message texte brut pour compatibilité
        $text_message = sprintf(
            'Bonjour %s,

Votre code d\'accès a été réinitialisé sur %s.

🔐 Vos nouvelles informations de connexion :
- Identifiant de connexion : %s
- Nouveau code d\'accès : %s
- URL de connexion : %s

⚠️ IMPORTANT :
- Ce nouveau code d\'accès remplace l\'ancien
- Gardez ces informations en sécurité
- Changez votre code d\'accès après votre prochaine connexion
- Ne partagez jamais vos informations de connexion

Pour vous connecter, rendez-vous sur : %s

Cordialement,
L\'équipe %s',
            $display_name,
            $site_name,
            $username,
            $new_access_code,
            $login_url,
            $login_url,
            $site_name
        );
        
        // Log des paramètres d'email
        error_log('Gaisio: Tentative d\'envoi d\'email de réinitialisation à ' . $email);
        error_log('Gaisio: Sujet: ' . $subject);
        error_log('Gaisio: Username: ' . $username);
        error_log('Gaisio: New access code: ' . $new_access_code);
        
        // Headers pour email HTML
        $headers = array(
            'Content-Type: text/html; charset=UTF-8',
            'From: ' . $site_name . ' <noreply@' . parse_url($site_url, PHP_URL_HOST) . '>',
            'Reply-To: noreply@' . parse_url($site_url, PHP_URL_HOST),
            'X-Mailer: WordPress/Gaisio Plugin',
            'MIME-Version: 1.0'
        );
        
        // Envoyer l'email HTML
        $sent = wp_mail($email, $subject, $html_message, $headers);
        
        // Log détaillé pour débogage
        if ($sent) {
            error_log('Gaisio: Email de réinitialisation HTML envoyé avec succès à ' . $email);
        } else {
            error_log('Gaisio: Erreur lors de l\'envoi de l\'email de réinitialisation HTML à ' . $email);
            
            // Essayer avec le message texte
            $text_headers = array(
                'Content-Type: text/plain; charset=UTF-8',
                'From: ' . $site_name . ' <noreply@' . parse_url($site_url, PHP_URL_HOST) . '>',
                'Reply-To: noreply@' . parse_url($site_url, PHP_URL_HOST),
                'X-Mailer: WordPress/Gaisio Plugin'
            );
            
            $text_sent = wp_mail($email, $subject, $text_message, $text_headers);
            error_log('Gaisio: Test avec message texte - ' . ($text_sent ? 'Succès' : 'Échec'));
            
            // Test alternatif avec mail() direct
            $alt_sent = mail($email, $subject, $text_message, implode("\r\n", $text_headers));
            error_log('Gaisio: Test alternatif mail() - ' . ($alt_sent ? 'Succès' : 'Échec'));
            
            $sent = $text_sent || $alt_sent;
        }
        
        return $sent;
    }
    

    
    // Fonction pour enregistrer le type de post personnalisé pour les signalements
    public function register_signalement_post_type() {
        $labels = array(
            'name'               => 'Signalements',
            'singular_name'      => 'Signalement',
            'menu_name'          => 'Signalements',
            'add_new'            => 'Ajouter un signalement',
            'add_new_item'       => 'Ajouter un nouveau signalement',
            'edit_item'          => 'Modifier le signalement',
            'new_item'           => 'Nouveau signalement',
            'view_item'          => 'Voir le signalement',
            'search_items'       => 'Rechercher des signalements',
            'not_found'          => 'Aucun signalement trouvé',
            'not_found_in_trash' => 'Aucun signalement trouvé dans la corbeille'
        );
        
        $args = array(
            'labels'              => $labels,
            'public'              => true,
            'publicly_queryable'  => true,
            'show_ui'             => true,
            'show_in_menu'        => true,
            'query_var'           => true,
            'rewrite'             => array( 'slug' => 'signalement' ),
            'capability_type'     => 'post',
            'has_archive'         => true,
            'hierarchical'        => false,
            'menu_position'       => 20,
            'menu_icon'           => 'dashicons-warning',
            'supports'            => array( 'title', 'editor', 'custom-fields' )
        );
        
        register_post_type( 'signalement', $args );
    }
    
    // Fonction AJAX pour traiter le formulaire de signalement
    public function submit_signalement_ajax() {
        // Log pour débogage
        error_log('Gaisio: submit_signalement_ajax appelé');
        error_log('Gaisio: POST data: ' . print_r($_POST, true));
        
        try {
            // Vérifier le nonce
            if ( ! isset( $_POST['signalement_nonce_field'] ) || ! wp_verify_nonce( $_POST['signalement_nonce_field'], 'signalement_nonce' ) ) {
                error_log('Gaisio: Erreur de nonce');
                wp_send_json_error(array('message' => 'Erreur de sécurité. Veuillez rafraîchir la page.'));
                return;
            }
            
            // Vérifier les champs obligatoires
            if ( ! isset( $_POST['signalement_date'] ) || ! isset( $_POST['signalement_intensite'] ) || ! isset( $_POST['signalement_localisation'] ) ) {
                error_log('Gaisio: Champs obligatoires manquants');
                wp_send_json_error(array('message' => 'Veuillez remplir tous les champs obligatoires.'));
                return;
            }
            
            // Récupération et validation des données du formulaire
            $date = sanitize_text_field( $_POST['signalement_date'] );
            $intensite = sanitize_text_field( $_POST['signalement_intensite'] );
            $duree = isset( $_POST['signalement_duree'] ) ? sanitize_text_field( $_POST['signalement_duree'] ) : '';
            $type = isset( $_POST['signalement_type'] ) ? sanitize_text_field( $_POST['signalement_type'] ) : '';
            $localisation = sanitize_text_field( $_POST['signalement_localisation'] );
            $description = isset( $_POST['signalement_description'] ) ? sanitize_textarea_field( $_POST['signalement_description'] ) : '';
            $nom = isset( $_POST['signalement_nom'] ) ? sanitize_text_field( $_POST['signalement_nom'] ) : '';
            $email = isset( $_POST['signalement_email'] ) ? sanitize_email( $_POST['signalement_email'] ) : '';
            
            // Log des données reçues
            error_log('Gaisio: Données reçues - Date: ' . $date . ', Intensité: ' . $intensite . ', Localisation: ' . $localisation);
            
            // Validation de la date
            if (empty($date)) {
                error_log('Gaisio: Date vide');
                wp_send_json_error(array('message' => 'La date et l\'heure sont obligatoires.'));
                return;
            }
            
            // Validation de l\'intensité
            if (empty($intensite) || !is_numeric($intensite) || $intensite < 1 || $intensite > 8) {
                error_log('Gaisio: Intensité invalide: ' . $intensite);
                wp_send_json_error(array('message' => 'L\'intensité doit être comprise entre 1 et 8.'));
                return;
            }
            
            // Validation de la localisation
            if (empty($localisation)) {
                error_log('Gaisio: Localisation vide');
                wp_send_json_error(array('message' => 'La localisation est obligatoire.'));
                return;
            }
            
            // Création du post de signalement
            $post_data = array(
                'post_title'    => 'Signalement - ' . $localisation . ' - ' . $date,
                'post_content'  => $description,
                'post_status'   => 'publish',
                'post_type'     => 'signalement',
                'post_author'   => 1,
            );
            
            error_log('Gaisio: Tentative de création du post avec les données: ' . print_r($post_data, true));
            
            $post_id = wp_insert_post( $post_data );
            
            if ( $post_id ) {
                error_log('Gaisio: Post créé avec succès, ID: ' . $post_id);
                
                // Ajout des meta données
                update_post_meta( $post_id, '_signalement_date', $date );
                update_post_meta( $post_id, '_signalement_intensite', $intensite );
                update_post_meta( $post_id, '_signalement_duree', $duree );
                update_post_meta( $post_id, '_signalement_type', $type );
                update_post_meta( $post_id, '_signalement_localisation', $localisation );
                update_post_meta( $post_id, '_signalement_nom', $nom );
                update_post_meta( $post_id, '_signalement_email', $email );
                
                error_log('Gaisio: Meta données mises à jour');
                
                // Enregistrer dans la base de données personnalisée si elle existe
                try {
                    $this->save_signalement_to_custom_table($post_id, $date, $intensite, $duree, $type, $localisation, $description, $nom, $email);
                    error_log('Gaisio: Données sauvegardées dans la table personnalisée');
                } catch (Exception $e) {
                    error_log('Gaisio: Erreur lors de la sauvegarde dans la table personnalisée: ' . $e->getMessage());
                    // Ne pas échouer si la table personnalisée pose problème
                }
        
        wp_send_json_success(array(
                    'message' => 'Votre signalement a été enregistré avec succès ! Merci pour votre contribution à la communauté scientifique.',
                    'post_id' => $post_id
                ));
            } else {
                error_log('Gaisio: Erreur lors de la création du post');
                wp_send_json_error(array('message' => 'Erreur lors de l\'enregistrement du signalement. Veuillez réessayer.'));
            }
            
        } catch (Exception $e) {
            error_log('Gaisio: Exception dans submit_signalement_ajax: ' . $e->getMessage());
            wp_send_json_error(array('message' => 'Une erreur inattendue s\'est produite. Veuillez réessayer.'));
        }
    }
    
    // Fonction pour sauvegarder dans une table personnalisée (optionnel)
    private function save_signalement_to_custom_table($post_id, $date, $intensite, $duree, $type, $localisation, $description, $nom, $email) {
        global $wpdb;
        
        error_log('Gaisio: Tentative de sauvegarde dans la table personnalisée');
        
        // Vérifier si la table existe
        $table_name = $wpdb->prefix . 'gaisio_signalements';
        $table_exists = $wpdb->get_var("SHOW TABLES LIKE '$table_name'") == $table_name;
        
        if (!$table_exists) {
            error_log('Gaisio: Table ' . $table_name . ' n\'existe pas, création en cours...');
            
            // Créer la table si elle n\'existe pas
            $charset_collate = $wpdb->get_charset_collate();
            $sql = "CREATE TABLE $table_name (
                id mediumint(9) NOT NULL AUTO_INCREMENT,
                post_id bigint(20) NOT NULL,
                date_signalement datetime NOT NULL,
                intensite tinyint(1) NOT NULL,
                duree varchar(50) DEFAULT NULL,
                type_mouvement varchar(50) DEFAULT NULL,
                localisation varchar(255) NOT NULL,
                description text,
                nom_contact varchar(100) DEFAULT NULL,
                email_contact varchar(100) DEFAULT NULL,
                created_at datetime DEFAULT CURRENT_TIMESTAMP,
                PRIMARY KEY (id),
                KEY post_id (post_id),
                KEY date_signalement (date_signalement),
                KEY localisation (localisation)
            ) $charset_collate;";
            
            require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
            $result = dbDelta($sql);
            
            error_log('Gaisio: Résultat de création de table: ' . print_r($result, true));
            
            // Vérifier à nouveau si la table existe maintenant
            $table_exists = $wpdb->get_var("SHOW TABLES LIKE '$table_name'") == $table_name;
            if (!$table_exists) {
                error_log('Gaisio: Échec de création de la table ' . $table_name);
                throw new Exception('Impossible de créer la table de signalements');
            }
        }
        
        // Préparer les données pour l'insertion
        $insert_data = array(
            'post_id' => $post_id,
            'date_signalement' => $date,
            'intensite' => $intensite,
            'duree' => $duree,
            'type_mouvement' => $type,
            'localisation' => $localisation,
            'description' => $description,
            'nom_contact' => $nom,
            'email_contact' => $email
        );
        
        error_log('Gaisio: Données à insérer: ' . print_r($insert_data, true));
        
        // Insérer les données
        $result = $wpdb->insert(
            $table_name,
            $insert_data,
            array('%d', '%s', '%d', '%s', '%s', '%s', '%s', '%s', '%s')
        );
        
        if ($result === false) {
            error_log('Gaisio: Erreur lors de l\'insertion dans la table personnalisée: ' . $wpdb->last_error);
            throw new Exception('Erreur lors de l\'insertion dans la base de données: ' . $wpdb->last_error);
        }
        
        error_log('Gaisio: Données insérées avec succès dans la table personnalisée, ID: ' . $wpdb->insert_id);
        return $wpdb->insert_id;
    }
    
    // Désactiver jQuery Migrate pour éviter les erreurs
    public function disable_jquery_migrate() {
        if (!is_admin()) {
            wp_dequeue_script('jquery-migrate');
        }
    }
    
    // Désactiver les avertissements jQuery Migrate
    public function disable_jquery_migrate_warnings() {
        if (!is_admin()) {
            ?>
            <script>
            // Désactiver les avertissements jQuery Migrate
            if (typeof jQuery !== 'undefined' && jQuery.migrateMute !== undefined) {
                jQuery.migrateMute = true;
            }
            
            // Supprimer les erreurs de console liées à jQuery Migrate
            if (typeof console !== 'undefined') {
                var originalError = console.error;
                console.error = function() {
                    var args = Array.prototype.slice.call(arguments);
                    var message = args.join(' ');
                    
                    // Ignorer les erreurs jQuery Migrate
                    if (message.indexOf('jquery-migrate') !== -1 || 
                        message.indexOf('Cannot read properties of undefined') !== -1) {
                        return;
                    }
                    
                    // Afficher les autres erreurs normalement
                    originalError.apply(console, args);
                };
            }
            </script>
            <?php
        }
    }
    
    // Fonction AJAX pour télécharger les informations de connexion en PDF
    public function download_user_credentials_pdf() {
        // Vérifier le nonce et les permissions AVANT tout output
        if (!wp_verify_nonce($_POST['nonce'], 'gaisio_admin_nonce')) {
            wp_die('Erreur de sécurité');
        }
        
        if (!current_user_can('manage_options')) {
            wp_die('Permissions insuffisantes');
        }
        
        $user_id = intval($_POST['user_id']);
        
        // Vérifier que l'utilisateur existe
        $user = get_user_by('ID', $user_id);
        if (!$user) {
            wp_die('Utilisateur non trouvé');
        }
        
        // Récupérer les informations de connexion depuis la base Gaisio
        global $wpdb;
        $table = $wpdb->prefix . 'gaisio_users';
        $gaisio_user = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table WHERE user_id = %d", $user_id));
        
        if (!$gaisio_user) {
            wp_die('Informations utilisateur non trouvées');
        }
        
        // Générer le contenu PDF
        $pdf_content = $this->generate_user_credentials_pdf($user, $gaisio_user);
        
        // Nettoyer tout output précédent
        if (ob_get_level()) {
            ob_end_clean();
        }
        
        // Envoyer le document en téléchargement (HTML pour une meilleure compatibilité)
        $filename = 'informations-connexion-' . sanitize_file_name($user->display_name) . '.html';
        
        // Headers pour le téléchargement HTML
        header('Content-Type: text/html; charset=UTF-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Content-Length: ' . strlen($pdf_content));
        header('Cache-Control: no-cache, must-revalidate');
        header('Pragma: no-cache');
        header('Expires: 0');
        
        // Envoyer le contenu
        echo $pdf_content;
        exit;
    }
    
    // Fonction pour générer le contenu PDF des informations de connexion
    private function generate_user_credentials_pdf($user, $gaisio_user) {
        $site_name = get_bloginfo('name');
        $site_url = get_site_url();
        $login_url = wp_login_url();
        $current_date = current_time('d/m/Y H:i');
        
        // Créer le contenu PDF en utilisant une bibliothèque simple
        $pdf_content = $this->create_simple_pdf($user, $gaisio_user, $site_name, $site_url, $current_date);
        
        return $pdf_content;
    }
    
    // Fonction pour créer un PDF simple sans bibliothèque externe
    private function create_simple_pdf($user, $gaisio_user, $site_name, $site_url, $current_date) {
        // Utiliser HTML par défaut - plus fiable et compatible
        return $this->create_html_pdf($user, $gaisio_user, $site_name, $site_url, $current_date);
    }
    
    // Fonction pour créer un PDF avec FPDF
    private function create_fpdf_pdf($user, $gaisio_user, $site_name, $site_url, $current_date) {
        $pdf = new FPDF();
        $pdf->AddPage();
        $pdf->SetFont('Arial', 'B', 16);
        
        // En-tête
        $pdf->Cell(0, 10, $site_name, 0, 1, 'C');
        $pdf->SetFont('Arial', '', 12);
        $pdf->Cell(0, 10, 'Informations de Connexion Utilisateur', 0, 1, 'C');
        $pdf->Ln(10);
        
        // Informations utilisateur
        $pdf->SetFont('Arial', 'B', 14);
        $pdf->Cell(0, 10, 'Utilisateur : ' . $user->display_name, 0, 1);
        $pdf->SetFont('Arial', '', 12);
        $pdf->Cell(0, 10, 'Email : ' . $user->user_email, 0, 1);
        $pdf->Ln(5);
        
        // Informations de connexion
        $pdf->SetFont('Arial', 'B', 14);
        $pdf->Cell(0, 10, 'Informations de Connexion :', 0, 1);
        $pdf->SetFont('Arial', '', 12);
        $pdf->Cell(0, 10, 'Nom d\'utilisateur : ' . $gaisio_user->username, 0, 1);
        $pdf->Cell(0, 10, 'Code d\'accès : ' . $gaisio_user->access_code, 0, 1);
        $pdf->Ln(10);
        
        // Avertissements
        $pdf->SetFont('Arial', 'B', 12);
        $pdf->Cell(0, 10, 'IMPORTANT :', 0, 1);
        $pdf->SetFont('Arial', '', 10);
        $pdf->Cell(0, 8, '- Gardez ces informations en sécurité', 0, 1);
        $pdf->Cell(0, 8, '- Changez votre code d\'accès après votre première connexion', 0, 1);
        $pdf->Cell(0, 8, '- Ne partagez jamais vos informations de connexion', 0, 1);
        $pdf->Ln(10);
        
        // Pied de page
        $pdf->SetFont('Arial', 'I', 10);
        $pdf->Cell(0, 10, 'Document généré le : ' . $current_date, 0, 1, 'C');
        $pdf->Cell(0, 10, 'Site : ' . $site_url, 0, 1, 'C');
        
        return $pdf->Output('', 'S');
    }
    
    // Fonction pour créer un PDF HTML stylisé (alternative si FPDF n'est pas disponible)
    private function create_html_pdf($user, $gaisio_user, $site_name, $site_url, $current_date) {
        $html = '
        <!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
            <title>Informations de Connexion - ' . $user->display_name . '</title>
    <style>
                body { 
                    font-family: Arial, sans-serif; 
                    margin: 40px; 
                    line-height: 1.6; 
                    color: #333; 
                }
                .header { 
                    text-align: center; 
                    border-bottom: 3px solid #2196f3; 
                    padding-bottom: 20px; 
                    margin-bottom: 30px; 
                }
                .site-name { 
                    font-size: 24px; 
                    font-weight: bold; 
                    color: #2196f3; 
                    margin-bottom: 10px; 
                }
                .title { 
                    font-size: 18px; 
                    color: #666; 
                }
                .section { 
                    margin-bottom: 25px; 
                }
                .section-title { 
                    font-size: 16px; 
                    font-weight: bold; 
                    color: #2196f3; 
                    margin-bottom: 15px; 
                    border-bottom: 1px solid #ddd; 
                    padding-bottom: 5px; 
                }
                .info-row { 
                    margin-bottom: 10px; 
                }
                .label { 
                    font-weight: bold; 
                    display: inline-block; 
                    width: 200px; 
                }
                .value { 
                    color: #666; 
                }
                .credentials { 
                    background: #f5f5f5; 
                    padding: 20px; 
                    border-radius: 8px; 
                    border-left: 4px solid #2196f3; 
                    margin: 20px 0; 
                }
                .warning { 
                    background: #fff3cd; 
                    border: 1px solid #ffc107; 
                    padding: 15px; 
                    border-radius: 5px; 
                    margin: 20px 0; 
                }
                .warning-title { 
                    font-weight: bold; 
                    color: #856404; 
                    margin-bottom: 10px; 
                }
                .footer { 
                    text-align: center; 
                    margin-top: 40px; 
                    padding-top: 20px; 
                    border-top: 1px solid #ddd; 
                    color: #666; 
                    font-size: 12px; 
                }
    </style>
</head>
<body>
        <div class="header">
                <div class="site-name">' . $site_name . '</div>
                <div class="title">Informations de Connexion Utilisateur</div>
        </div>
            
            <div class="section">
                <div class="section-title">Informations Utilisateur</div>
                <div class="info-row">
                    <span class="label">Nom complet :</span>
                    <span class="value">' . $user->display_name . '</span>
            </div>
                <div class="info-row">
                    <span class="label">Adresse email :</span>
                    <span class="value">' . $user->user_email . '</span>
        </div>
        </div>
            
            <div class="section">
                <div class="section-title">Informations de Connexion</div>
                <div class="credentials">
                    <div class="info-row">
                        <span class="label">Nom d\'utilisateur :</span>
                        <span class="value" style="font-weight: bold; color: #2196f3;">' . $gaisio_user->username . '</span>
    </div>
                    <div class="info-row">
                        <span class="label">Code d\'accès :</span>
                        <span class="value" style="font-weight: bold; color: #2196f3; font-size: 16px;">' . $gaisio_user->access_code . '</span>
                    </div>
                </div>
            </div>
            
            <div class="section">
                <div class="section-title">Avertissements Importants</div>
                <div class="warning">
                    <div class="warning-title">⚠️ SÉCURITÉ</div>
                    <ul style="margin: 0; padding-left: 20px;">
                        <li>Gardez ces informations en sécurité</li>
                        <li>Changez votre code d\'accès après votre première connexion</li>
                        <li>Ne partagez jamais vos informations de connexion</li>
                        <li>Utilisez un mot de passe fort et unique</li>
                    </ul>
                </div>
            </div>
            
            <div class="footer">
                <div>Document généré le : ' . $current_date . '</div>
                <div>Site : ' . $site_url . '</div>
                <div>© ' . date('Y') . ' ' . $site_name . ' - Tous droits réservés</div>
    </div>
</body>
        </html>';
        
        return $html;
    }
    
    // Fonction AJAX pour récupérer les statistiques ADMIN (séparée des statistiques publiques)
    public function admin_get_stats() {
        check_ajax_referer('gaisio_admin_nonce', 'nonce');
        
        // Vérifier les permissions d'administration
        if (!current_user_can('manage_options')) {
            wp_send_json_error('Permissions insuffisantes');
        }
        
        global $wpdb;
        $earthquakes_table = $wpdb->prefix . 'gaisio_earthquakes';
        $users_table = $wpdb->prefix . 'gaisio_users';
        $signalements_table = $wpdb->prefix . 'gaisio_signalements';
        
        // Nombre total de tremblements de terre
        $total_earthquakes = $wpdb->get_var("SELECT COUNT(*) FROM $earthquakes_table");
        
        // Nombre total d'utilisateurs
        $total_users = $wpdb->get_var("SELECT COUNT(*) FROM $users_table");
        
        // Nombre total de signalements
        $total_signalements = $wpdb->get_var("SELECT COUNT(*) FROM $signalements_table");
        
        // Magnitude la plus élevée
        $latest_magnitude = $wpdb->get_var("
            SELECT magnitude 
            FROM $earthquakes_table 
            WHERE magnitude IS NOT NULL 
            ORDER BY magnitude DESC 
            LIMIT 1
        ");
        
        // Tremblement de terre le plus récent
        $latest_earthquake = $wpdb->get_var("
            SELECT created_at 
            FROM $earthquakes_table 
            ORDER BY created_at DESC 
            LIMIT 1
        ");
        
        // Utilisateur le plus récent
        $latest_user = $wpdb->get_var("
            SELECT created_at 
            FROM $users_table 
            ORDER BY created_at DESC 
            LIMIT 1
        ");
        
        // Statistiques détaillées pour l'admin
        $admin_stats = array(
            'total_earthquakes' => intval($total_earthquakes),
            'total_users' => intval($total_users),
            'total_signalements' => intval($total_signalements),
            'latest_magnitude' => $latest_magnitude ? number_format($latest_magnitude, 1) : '0.0',
            'latest_earthquake_date' => $latest_earthquake ? date('d/m/Y H:i', strtotime($latest_earthquake)) : 'Aucun',
            'latest_user_date' => $latest_user ? date('d/m/Y H:i', strtotime($latest_user)) : 'Aucun',
            'platform_status' => 'Actif'
        );
        
        wp_send_json_success($admin_stats);
    }
    
    // Fonction AJAX pour la connexion utilisateur
    public function user_login_ajax() {
        // Log pour débogage
        error_log('Gaisio: user_login_ajax appelé');
        error_log('Gaisio: POST data: ' . print_r($_POST, true));
        
        // Vérifier le nonce de sécurité
        if (!wp_verify_nonce($_POST['nonce'], 'gaisio_user_nonce')) {
            error_log('Gaisio: Erreur de nonce - nonce reçu: ' . $_POST['nonce']);
            wp_send_json_error('Erreur de sécurité - nonce invalide');
        }
        
        $username = sanitize_text_field($_POST['username']);
        $access_code = sanitize_text_field($_POST['access_code']);
        $remember = isset($_POST['remember']) && $_POST['remember'] === 'true';
        
        error_log('Gaisio: Username: ' . $username . ', Access Code: ' . $access_code);
        
        // Validation des données
        if (empty($username) || empty($access_code)) {
            error_log('Gaisio: Champs vides - username: ' . $username . ', access_code: ' . $access_code);
            wp_send_json_error('Le nom d\'utilisateur et le code d\'accès sont obligatoires');
        }
        
        // Vérifier les identifiants dans la table Gaisio
        global $wpdb;
        $table_users = $wpdb->prefix . 'gaisio_users';
        
        error_log('Gaisio: Table utilisée: ' . $table_users);
        
        $user = $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM $table_users WHERE username = %s AND access_code = %s",
            $username,
            $access_code
        ));
        
        if (!$user) {
            error_log('Gaisio: Utilisateur non trouvé dans gaisio_users - username: ' . $username . ', access_code: ' . $access_code);
            
            // Vérifier si l'utilisateur existe dans la table
            $user_exists = $wpdb->get_row($wpdb->prepare(
                "SELECT * FROM $table_users WHERE username = %s",
                $username
            ));
            
            if ($user_exists) {
                error_log('Gaisio: Utilisateur trouvé mais access_code incorrect - stored: ' . $user_exists->access_code);
                wp_send_json_error('Code d\'accès incorrect pour cet utilisateur');
            } else {
                error_log('Gaisio: Utilisateur complètement introuvable');
                wp_send_json_error('Nom d\'utilisateur introuvable');
            }
        }
        
        error_log('Gaisio: Utilisateur trouvé dans gaisio_users: ' . print_r($user, true));
        
        // Récupérer l'utilisateur WordPress
        $wp_user = get_user_by('ID', $user->user_id);
        if (!$wp_user) {
            error_log('Gaisio: Utilisateur WordPress introuvable pour ID: ' . $user->user_id);
            wp_send_json_error('Utilisateur WordPress introuvable');
        }
        
        error_log('Gaisio: Utilisateur WordPress trouvé: ' . print_r($wp_user, true));
        
        // Authentifier l'utilisateur WordPress avec l'access_code
        $authenticated_user = wp_authenticate($username, $access_code);
        
        if (is_wp_error($authenticated_user)) {
            error_log('Gaisio: Échec de l\'authentification WordPress: ' . $authenticated_user->get_error_message());
            wp_send_json_error('Échec de l\'authentification: ' . $authenticated_user->get_error_message());
        }
        
        error_log('Gaisio: Authentification WordPress réussie');
        
        // Connecter l'utilisateur
        wp_set_current_user($authenticated_user->ID);
        wp_set_auth_cookie($authenticated_user->ID, $remember);
        
        error_log('Gaisio: Utilisateur connecté avec succès - ID: ' . $authenticated_user->ID);
        
        // Informations de l'utilisateur connecté
        $user_info = array(
            'user_id' => $authenticated_user->ID,
            'username' => $authenticated_user->user_login,
            'display_name' => $authenticated_user->display_name,
            'email' => $authenticated_user->user_email,
            'role' => $authenticated_user->roles[0] ?? 'subscriber'
        );
        
            wp_send_json_success(array(
            'message' => 'Connexion réussie ! Bienvenue ' . $authenticated_user->display_name,
            'user_info' => $user_info,
            'redirect_url' => home_url('/gaisio/') // Redirection vers la page utilisateur demandée
        ));
    }
    
    // Fonction AJAX pour la déconnexion utilisateur
    public function user_logout_ajax() {
        // Vérifier le nonce de sécurité
        if (!wp_verify_nonce($_POST['nonce'], 'gaisio_user_nonce')) {
            wp_send_json_error('Erreur de sécurité');
        }
        
        // Déconnecter l'utilisateur
        wp_logout();
        
        wp_send_json_success(array(
            'message' => 'Déconnexion réussie. Vous avez été redirigé vers le formulaire de connexion.',
            'redirect_url' => home_url('/login/') // Redirection vers le formulaire de connexion
        ));
    }
    
    // Fonction AJAX pour la connexion administrateur
    public function admin_login_ajax() {
        // Vérifier le nonce
        if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'gaisio_admin_nonce')) {
            wp_send_json_error('Erreur de sécurité');
        }
        
        $username = sanitize_text_field($_POST['username'] ?? '');
        $password = sanitize_text_field($_POST['password'] ?? '');
        $remember = !empty($_POST['remember']);
        
        if ($username === '' || $password === '') {
            wp_send_json_error('Nom d\'utilisateur et mot de passe requis');
        }
        
        // Authentifier via WordPress
        $user = wp_authenticate($username, $password);
        if (is_wp_error($user)) {
            wp_send_json_error('Identifiants invalides');
        }
        
        // Vérifier capacité admin
        if (!user_can($user, 'manage_options')) {
            wp_send_json_error('Accès refusé: non administrateur');
        }
        
        // Connexion de l'utilisateur
        wp_set_current_user($user->ID);
        wp_set_auth_cookie($user->ID, $remember);
        
        wp_send_json_success(array(
            'message' => 'Connexion admin réussie',
            'redirect_url' => home_url('/admin/')
        ));
    }
    

    

}

// Inclure la page d'administration
require_once plugin_dir_path(__FILE__) . 'admin/admin-page.php';

// Initialiser le plugin
new GaisioEarthquakeManager(); 