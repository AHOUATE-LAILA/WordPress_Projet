<?php
/**
 * Template Name: Gaisio Earthquake Platform
 * 
 * Template pour la plateforme Gaisio Earthquake
 */

get_header(); ?>

<div class="gaisio-platform-container">
    <div class="gaisio-header">
        <h1>Plateforme Gaisio - Tremblements de Terre</h1>
        <p>Institut national de recherche scientifique et technique</p>
    </div>
    
    <div class="gaisio-content">
        <?php if (!is_user_logged_in()): ?>
            <!-- Section pour les utilisateurs non connectés -->
            <div class="gaisio-section">
                <h2>Bienvenue sur la plateforme Gaisio</h2>
                <p>Cette plateforme permet aux utilisateurs de saisir et consulter les données de tremblements de terre.</p>
                
                <div class="gaisio-actions">
                    <div class="gaisio-action-card">
                        <h3>Créer un compte</h3>
                        <p>Créez votre compte pour accéder à toutes les fonctionnalités</p>
                        <?php echo do_shortcode('[gaisio_user_form]'); ?>
                    </div>
                    
                    <div class="gaisio-action-card">
                        <h3>Se connecter</h3>
                        <p>Déjà un compte ? Connectez-vous</p>
                        <a href="<?php echo wp_login_url(get_permalink()); ?>" class="gaisio-btn">Se connecter</a>
                    </div>
                </div>
            </div>
        <?php else: ?>
            <!-- Section pour les utilisateurs connectés -->
            <div class="gaisio-section">
                <div class="gaisio-welcome">
                    <h2>Bienvenue, <?php echo wp_get_current_user()->display_name; ?> !</h2>
                    <p>Vous pouvez maintenant saisir des données de tremblements de terre et consulter les enregistrements.</p>
                    <a href="<?php echo wp_logout_url(get_permalink()); ?>" class="gaisio-btn-secondary">Se déconnecter</a>
                </div>
                
                <div class="gaisio-forms">
                    <div class="gaisio-form-section">
                        <h3>Saisir un tremblement de terre</h3>
                        <?php echo do_shortcode('[gaisio_earthquake_form]'); ?>
                    </div>
                    
                    <div class="gaisio-table-section">
                        <h3>Données enregistrées</h3>
                        <?php echo do_shortcode('[gaisio_earthquake_table]'); ?>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<style>
.gaisio-platform-container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 2rem;
}

.gaisio-header {
    text-align: center;
    margin-bottom: 3rem;
    padding: 2rem;
    background: linear-gradient(135deg, #2c3e50, #34495e);
    color: white;
    border-radius: 10px;
}

.gaisio-header h1 {
    font-size: 2.5rem;
    margin-bottom: 0.5rem;
}

.gaisio-header p {
    font-size: 1.2rem;
    opacity: 0.9;
}

.gaisio-section {
    margin-bottom: 3rem;
}

.gaisio-section h2 {
    color: #2c3e50;
    margin-bottom: 1rem;
    font-size: 2rem;
}

.gaisio-actions {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 2rem;
    margin-top: 2rem;
}

.gaisio-action-card {
    background: white;
    padding: 2rem;
    border-radius: 10px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    text-align: center;
}

.gaisio-action-card h3 {
    color: #2c3e50;
    margin-bottom: 1rem;
}

.gaisio-welcome {
    background: white;
    padding: 2rem;
    border-radius: 10px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    margin-bottom: 2rem;
    text-align: center;
}

.gaisio-btn-secondary {
    background: #95a5a6;
    color: white;
    padding: 0.75rem 2rem;
    border: none;
    border-radius: 6px;
    font-size: 1rem;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.3s ease;
    text-decoration: none;
    display: inline-block;
    margin-top: 1rem;
}

.gaisio-btn-secondary:hover {
    background: #7f8c8d;
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(149, 165, 166, 0.3);
}

.gaisio-forms {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 2rem;
}

.gaisio-form-section,
.gaisio-table-section {
    background: white;
    padding: 2rem;
    border-radius: 10px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
}

.gaisio-form-section h3,
.gaisio-table-section h3 {
    color: #2c3e50;
    margin-bottom: 1.5rem;
    text-align: center;
}

@media (max-width: 768px) {
    .gaisio-actions,
    .gaisio-forms {
        grid-template-columns: 1fr;
    }
    
    .gaisio-header h1 {
        font-size: 2rem;
    }
}
</style>

<?php get_footer(); ?> 