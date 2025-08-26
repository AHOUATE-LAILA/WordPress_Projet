<?php
/**
 * Page d'administration Gaisio Earthquake Manager
 * Gestion des actualités et des comptes utilisateurs
 */

// Sécurité
if (!defined('ABSPATH')) {
    exit;
}

class Gaisio_Admin_Page {
    
    public function __construct() {
        add_action('admin_menu', array($this, 'add_admin_menu'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_scripts'));
        add_action('wp_ajax_gaisio_save_news', array($this, 'save_news'));
        add_action('wp_ajax_gaisio_delete_news', array($this, 'delete_news'));
        add_action('wp_ajax_gaisio_get_news', array($this, 'get_news'));
        add_action('wp_ajax_gaisio_update_user_role', array($this, 'update_user_role'));
        add_action('wp_ajax_gaisio_delete_user', array($this, 'delete_user'));
        
        // Ajouter le shortcode pour le frontend
        add_shortcode('gaisio_admin', array($this, 'frontend_shortcode'));
        
        // Charger les scripts et styles sur le frontend quand le shortcode est utilisé
        add_action('wp_enqueue_scripts', array($this, 'enqueue_frontend_scripts'));
    }
    
    public function add_admin_menu() {
        add_menu_page(
            'Gaisio Earthquake Manager',
            'Gaisio Sismique',
            'manage_options',
            'gaisio-earthquake-admin',
            array($this, 'admin_page'),
            'dashicons-admin-site',
            30
        );
    }
    
    public function enqueue_admin_scripts($hook) {
        if ($hook !== 'toplevel_page_gaisio-earthquake-admin') {
            return;
        }
        
        wp_enqueue_style('gaisio-admin-style', plugin_dir_url(__FILE__) . 'css/admin-style.css', array(), '1.0.0');
        wp_enqueue_script('gaisio-admin-script', plugin_dir_url(__FILE__) . 'js/admin-script.js', array('jquery'), '1.0.0', true);
        
        wp_localize_script('gaisio-admin-script', 'gaisioAdmin', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('gaisio_admin_nonce'),
            'strings' => array(
                'confirm_delete' => 'Êtes-vous sûr de vouloir supprimer cet élément ?',
                'saving' => 'Enregistrement...',
                'saved' => 'Enregistré avec succès !',
                'error' => 'Erreur lors de l\'enregistrement',
                'deleting' => 'Suppression...',
                'deleted' => 'Supprimé avec succès !'
            )
        ));
    }
    
    public function admin_page() {
        if (!current_user_can('manage_options')) {
            wp_die(__('Vous n\'avez pas les permissions nécessaires pour accéder à cette page.'));
        }
        
        ?>
        <div class="wrap gaisio-admin-wrap">
            <h1 class="wp-heading-inline">
                <span class="dashicons dashicons-admin-site"></span>
                Gaisio Earthquake Manager - Administration
            </h1>
            
            <div class="gaisio-admin-tabs">
                <nav class="nav-tab-wrapper">
                    <a href="#actualites" class="nav-tab nav-tab-active">📰 Gestion des Actualités</a>
                    <a href="#utilisateurs" class="nav-tab">👥 Gestion des Utilisateurs</a>
                    <a href="#emails" class="nav-tab">📧 Envoi d'Emails</a>
                    <a href="#statistiques" class="nav-tab">📊 Statistiques</a>
                </nav>
                
                <!-- Onglet Actualités -->
                <div id="actualites" class="tab-content active">
                    <div class="gaisio-admin-section">
                        <h2>Ajouter/Modifier une Actualité</h2>
                        <form id="gaisio-news-form" class="gaisio-form">
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="news-title">Titre de l'actualité *</label>
                                    <input type="text" id="news-title" name="title" required>
                                </div>
                            </div>
                            
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="news-description">Description</label>
                                    <textarea id="news-description" name="description" rows="3"></textarea>
                                </div>
                            </div>
                            
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="news-image">URL de l'image *</label>
                                    <input type="url" id="news-image" name="image_url" required>
                                </div>
                                <div class="form-group">
                                    <label for="news-date">Date de publication</label>
                                    <input type="date" id="news-date" name="pub_date" value="<?php echo date('Y-m-d'); ?>">
                                </div>
                            </div>
                            
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="news-status">Statut</label>
                                    <select id="news-status" name="status">
                                        <option value="published">Publié</option>
                                        <option value="draft">Brouillon</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="news-order">Ordre d'affichage</label>
                                    <input type="number" id="news-order" name="display_order" value="0" min="0">
                                </div>
                            </div>
                            
                            <div class="form-actions">
                                <button type="submit" class="button button-primary">💾 Enregistrer l'Actualité</button>
                                <button type="button" class="button button-secondary" id="reset-form">🔄 Réinitialiser</button>
                            </div>
                        </form>
                    </div>
                    
                    <div class="gaisio-admin-section">
                        <h2>Actualités Existantes</h2>
                        <div class="gaisio-table-container">
                            <table class="wp-list-table widefat fixed striped" id="news-table">
                                <thead>
                                    <tr>
                                        <th>Image</th>
                                        <th>Titre</th>
                                        <th>Description</th>
                                        <th>Date</th>
                                        <th>Statut</th>
                                        <th>Ordre</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody id="news-table-body">
                                    <!-- Les actualités seront chargées ici via AJAX -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                
                <!-- Onglet Utilisateurs -->
                <div id="utilisateurs" class="tab-content">
                    <div class="gaisio-admin-section">
                        <h2>Gestion des Comptes Utilisateurs</h2>
                        <div class="gaisio-table-container">
                            <table class="wp-list-table widefat fixed striped" id="users-table">
                                <thead>
                                    <tr>
                                        <th>Avatar</th>
                                        <th>Nom d'utilisateur</th>
                                        <th>Email</th>
                                        <th>Rôle</th>
                                        <th>Date d'inscription</th>
                                        <th>Dernière connexion</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody id="users-table-body">
                                    <!-- Les utilisateurs seront chargés ici via AJAX -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                
                <!-- Onglet Emails -->
                <div id="emails" class="tab-content">
                    <div class="gaisio-admin-section">
                        <h2>📧 Envoi d'Emails aux Utilisateurs</h2>
                        
                        <div class="gaisio-admin-section">
                            <h3>Composer un Email</h3>
                            <form id="gaisio-email-form" class="gaisio-form">
                                <div class="form-row">
                                    <div class="form-group">
                                        <label for="email-user-select">Destinataire *</label>
                                        <select id="email-user-select" name="user_id" required>
                                            <option value="">Sélectionnez un utilisateur</option>
                                            <!-- Les utilisateurs seront chargés ici via AJAX -->
                                        </select>
                                    </div>
                                </div>
                                
                                <div class="form-row">
                                    <div class="form-group">
                                        <label for="email-subject">Sujet *</label>
                                        <input type="text" id="email-subject" name="subject" required placeholder="Sujet de l'email">
                                    </div>
                                </div>
                                
                                <div class="form-row">
                                    <div class="form-group">
                                        <label for="email-message">Message *</label>
                                        <textarea id="email-message" name="message" rows="8" required placeholder="Votre message..."></textarea>
                                    </div>
                                </div>
                                
                                <div class="form-actions">
                                    <button type="submit" class="button button-primary">📤 Envoyer l'Email</button>
                                    <button type="button" class="button button-secondary" id="reset-email-form">🔄 Réinitialiser</button>
                                </div>
                            </form>
                        </div>
                        
                        <div class="gaisio-admin-section">
                            <h3>📋 Historique des Emails</h3>
                            <div class="gaisio-table-container">
                                <table class="wp-list-table widefat fixed striped" id="emails-table">
                                    <thead>
                                        <tr>
                                            <th>Date</th>
                                            <th>Destinataire</th>
                                            <th>Sujet</th>
                                            <th>Statut</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody id="emails-table-body">
                                        <!-- L'historique des emails sera chargé ici -->
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Onglet Statistiques -->
                <div id="statistiques" class="tab-content">
                    <div class="gaisio-admin-section">
                        <h2>Statistiques de la Plateforme</h2>
                        <div class="gaisio-stats-grid">
                            <div class="stat-card">
                                <div class="stat-icon">🌍</div>
                                <div class="stat-content">
                                    <h3>Tremblements de terre</h3>
                                    <div class="stat-number" id="total-earthquakes">-</div>
                                    <div class="stat-label">Total enregistrés</div>
                                </div>
                            </div>
                            
                            <div class="stat-card">
                                <div class="stat-icon">👥</div>
                                <div class="stat-content">
                                    <h3>Utilisateurs</h3>
                                    <div class="stat-number" id="total-users">-</div>
                                    <div class="stat-label">Total inscrits</div>
                                </div>
                            </div>
                            
                            <div class="stat-card">
                                <div class="stat-icon">📰</div>
                                <div class="stat-content">
                                    <h3>Actualités</h3>
                                    <div class="stat-number" id="total-news">-</div>
                                    <div class="stat-label">Total publiées</div>
                                </div>
                            </div>
                            
                            <div class="stat-card">
                                <div class="stat-icon">📅</div>
                                <div class="stat-content">
                                    <h3>Ce mois</h3>
                                    <div class="stat-number" id="monthly-earthquakes">-</div>
                                    <div class="stat-label">Tremblements</div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="gaisio-admin-section">
                            <h3>Activité Récente</h3>
                            <div id="recent-activity">
                                <!-- L'activité récente sera chargée ici -->
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php
    }
    
    // AJAX Handlers
    public function save_news() {
        check_ajax_referer('gaisio_admin_nonce', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_send_json_error('Permissions insuffisantes');
        }
        
        $title = sanitize_text_field($_POST['title']);
        $description = sanitize_textarea_field($_POST['description']);
        $image_url = esc_url_raw($_POST['image_url']);
        $pub_date = sanitize_text_field($_POST['pub_date']);
        $status = sanitize_text_field($_POST['status']);
        $display_order = intval($_POST['display_order']);
        
        if (empty($title) || empty($image_url)) {
            wp_send_json_error('Titre et image requis');
        }
        
        // Sauvegarder dans la base de données
        $news_data = array(
            'title' => $title,
            'description' => $description,
            'image_url' => $image_url,
            'pub_date' => $pub_date,
            'status' => $status,
            'display_order' => $display_order,
            'created_at' => current_time('mysql')
        );
        
        // Ici vous pouvez sauvegarder dans une table personnalisée
        // Pour l'exemple, on utilise les options WordPress
        $existing_news = get_option('gaisio_news', array());
        $news_id = uniqid('news_');
        $existing_news[$news_id] = $news_data;
        update_option('gaisio_news', $existing_news);
        
        wp_send_json_success('Actualité sauvegardée avec succès');
    }
    
    public function delete_news() {
        check_ajax_referer('gaisio_admin_nonce', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_send_json_error('Permissions insuffisantes');
        }
        
        $news_id = sanitize_text_field($_POST['news_id']);
        
        $existing_news = get_option('gaisio_news', array());
        if (isset($existing_news[$news_id])) {
            unset($existing_news[$news_id]);
            update_option('gaisio_news', $existing_news);
            wp_send_json_success('Actualité supprimée avec succès');
        } else {
            wp_send_json_error('Actualité non trouvée');
        }
    }
    
    public function get_news() {
        check_ajax_referer('gaisio_admin_nonce', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_send_json_error('Permissions insuffisantes');
        }
        
        $news = get_option('gaisio_news', array());
        wp_send_json_success($news);
    }
    
    public function update_user_role() {
        check_ajax_referer('gaisio_admin_nonce', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_send_json_error('Permissions insuffisantes');
        }
        
        $user_id = intval($_POST['user_id']);
        $new_role = sanitize_text_field($_POST['new_role']);
        
        $user = get_user_by('id', $user_id);
        if ($user) {
            $user->set_role($new_role);
            wp_send_json_success('Rôle mis à jour avec succès');
        } else {
            wp_send_json_error('Utilisateur non trouvé');
        }
    }
    
    public function delete_user() {
        check_ajax_referer('gaisio_admin_nonce', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_send_json_error('Permissions insuffisantes');
        }
        
        $user_id = intval($_POST['user_id']);
        
        if (wp_delete_user($user_id)) {
            wp_send_json_success('Utilisateur supprimé avec succès');
        } else {
            wp_send_json_error('Erreur lors de la suppression');
        }
    }

    // Shortcode handler for frontend - Page d'administration complète
    public function frontend_shortcode($atts) {
        // Vérifier que l'utilisateur est connecté et a les permissions d'admin
        if (!is_user_logged_in() || !current_user_can('manage_options')) {
            return '<div class="gaisio-admin-access-denied">
                <h3>🔒 Accès Restreint</h3>
                <p>Vous devez être connecté en tant qu\'administrateur pour accéder à cette page.</p>
                <p><a href="' . wp_login_url() . '" class="button">Se connecter</a></p>
            </div>';
        }

        // Localize script for frontend
        wp_localize_script('gaisio-frontend-script', 'gaisioFrontend', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('gaisio_frontend_nonce'),
            'plugin_url' => plugin_dir_url(__FILE__) . '../',
            'strings' => array(
                'loading_news' => 'Chargement des actualités...',
                'no_news_found' => 'Aucune actualité trouvée.',
                'error_loading_news' => 'Erreur lors du chargement des actualités.',
                'confirm_delete' => 'Êtes-vous sûr de vouloir supprimer cet élément ?',
                'saving' => 'Enregistrement...',
                'saved' => 'Enregistré avec succès !',
                'error' => 'Erreur lors de l\'enregistrement',
                'deleting' => 'Suppression...',
                'deleted' => 'Supprimé avec succès !'
            )
        ));

        // Return the HTML for the complete admin interface
        ob_start();
        ?>
        <div class="gaisio-admin-frontend-wrap">
            <div class="gaisio-admin-header">
                <h1 class="gaisio-admin-title">
                    <span class="dashicons dashicons-admin-site"></span>
                    Gaisio Earthquake Manager - Administration
                </h1>
            </div>
            
            <div class="gaisio-admin-tabs">
                <nav class="gaisio-nav-tab-wrapper">
                    <a href="#actualites" class="gaisio-nav-tab gaisio-nav-tab-active">📰 Gestion des Actualités</a>
                    <a href="#utilisateurs" class="gaisio-nav-tab">👥 Gestion des Utilisateurs</a>
                    <a href="#statistiques" class="gaisio-nav-tab">📊 Statistiques</a>
                </nav>
                
                <!-- Onglet Actualités -->
                <div id="actualites" class="gaisio-tab-content active">
                    <div class="gaisio-admin-section">
                        <h2>Ajouter/Modifier une Actualité</h2>
                        <form id="gaisio-news-form" class="gaisio-form">
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="news-title">Titre de l'actualité *</label>
                                    <input type="text" id="news-title" name="title" required>
                                </div>
                            </div>
                            
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="news-description">Description</label>
                                    <textarea id="news-description" name="description" rows="3"></textarea>
                                </div>
                            </div>
                            
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="news-image">URL de l'image *</label>
                                    <input type="url" id="news-image" name="image_url" required>
                                </div>
                            </div>
                            
                            <div class="form-row">
                                <div class="form-group">
                                    <button type="submit" class="gaisio-btn gaisio-btn-primary">
                                        <span class="btn-text">Ajouter l'Actualité</span>
                                        <span class="btn-loading" style="display: none;">Enregistrement...</span>
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                    
                    <div class="gaisio-admin-section">
                        <h2>Actualités Existantes</h2>
                        <div id="gaisio-news-list" class="gaisio-news-list">
                            <div class="gaisio-loading">Chargement des actualités...</div>
                        </div>
                    </div>
                </div>
                
                <!-- Onglet Utilisateurs -->
                <div id="utilisateurs" class="gaisio-tab-content">
                    <div class="gaisio-admin-section">
                        <h2>Gestion des Utilisateurs</h2>
                        <div id="gaisio-users-list" class="gaisio-users-list">
                            <div class="gaisio-loading">Chargement des utilisateurs...</div>
                        </div>
                    </div>
                </div>
                
                <!-- Onglet Statistiques -->
                <div id="statistiques" class="gaisio-tab-content">
                    <div class="gaisio-admin-section">
                        <h2>Statistiques de la Plateforme</h2>
                        <div id="gaisio-stats-grid" class="gaisio-stats-grid">
                            <div class="gaisio-loading">Chargement des statistiques...</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php
        return ob_get_clean();
    }

    // Enqueue scripts and styles for the frontend
    public function enqueue_frontend_scripts() {
        // Enqueue scripts and styles only if the shortcode is used on a page
        // This prevents unnecessary loading on admin pages
        if (is_admin()) {
            return;
        }

        wp_enqueue_script('gaisio-frontend-script', plugin_dir_url(__FILE__) . 'js/frontend-script.js', array('jquery'), '1.0.0', true);
        wp_enqueue_style('gaisio-frontend-style', plugin_dir_url(__FILE__) . 'css/frontend-style.css', array(), '1.0.0');
    }
}

// Initialiser la classe
new Gaisio_Admin_Page(); 