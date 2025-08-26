<?php
/**
 * Define Theme Version
 */
define( 'WEBSY_THEME_VERSION', '1.0' );

function websy_css() {
	$parent_style = 'webique-parent-style';
	wp_enqueue_style( $parent_style, get_template_directory_uri() . '/style.css' );
	wp_enqueue_style( 'websy-style', get_stylesheet_uri(), array( $parent_style ));
	
	wp_enqueue_style('websy-color-default',get_stylesheet_directory_uri() .'/assets/css/color/default.css');
	wp_dequeue_style('webique-default');	
}
add_action( 'wp_enqueue_scripts', 'websy_css',999);

function websy_setup()	{	
	add_theme_support( 'woocommerce' );
	add_theme_support( "title-tag" );
	add_theme_support( 'automatic-feed-links' );
	
	// Enregistrer un emplacement de menu pour la page d'accueil
	register_nav_menus( array(
		'homepage_menu' => esc_html__( 'Menu Page d\'Accueil', 'websy' )
	) );
}
add_action( 'after_setup_theme', 'websy_setup' );

// Fonction pour afficher le menu de la page d'accueil
function websy_homepage_menu() {
	if ( is_front_page() || is_home() ) {
		wp_nav_menu( 
			array(  
				'theme_location' => 'homepage_menu',
				'container'  => 'div',
				'container_class' => 'homepage-menu-container',
				'menu_class' => 'homepage-menu',
				'fallback_cb' => 'websy_homepage_menu_fallback',
				'echo' => true
			) 
		);
	}
}
add_action( 'websy_homepage_menu', 'websy_homepage_menu' );

// Fonction de fallback pour le menu de la page d'accueil
function websy_homepage_menu_fallback() {
	echo '<div class="homepage-menu-container">';
	echo '<ul class="homepage-menu">';
	echo '<li><a href="' . esc_url( home_url( '/' ) ) . '">Accueil</a></li>';
	echo '<li><a href="' . esc_url( home_url( '/a-propos' ) ) . '">À propos</a></li>';
	echo '<li><a href="' . esc_url( home_url( '/services' ) ) . '">Services</a></li>';
	echo '<li><a href="' . esc_url( home_url( '/contact' ) ) . '">Contact</a></li>';
	echo '</ul>';
	echo '</div>';
}

// Shortcode pour le menu de la page d'accueil
function websy_homepage_menu_shortcode( $atts ) {
	// Attributs par défaut
	$atts = shortcode_atts( array(
		'style' => 'default', // default, compact, full-width
		'class' => '',
		'background' => 'gradient', // gradient, solid, transparent
	), $atts );
	
	// Classes CSS personnalisées
	$custom_class = !empty( $atts['class'] ) ? ' ' . esc_attr( $atts['class'] ) : '';
	$style_class = 'homepage-menu-section-' . esc_attr( $atts['style'] );
	$bg_class = 'homepage-menu-bg-' . esc_attr( $atts['background'] );
	
	ob_start();
	?>
	<section class="homepage-menu-section <?php echo $style_class . $bg_class . $custom_class; ?>">
		<div class="av-container">
			<div class="row">
				<div class="col-12">
					<div class="homepage-menu-wrapper">
						<?php do_action('websy_homepage_menu'); ?>
					</div>
				</div>
			</div>
		</div>
	</section>
	<?php
	return ob_get_clean();
}
add_shortcode( 'homepage_menu', 'websy_homepage_menu_shortcode' );

// Shortcode pour le menu seul (sans la section)
function websy_homepage_menu_only_shortcode( $atts ) {
	$atts = shortcode_atts( array(
		'class' => '',
	), $atts );
	
	$custom_class = !empty( $atts['class'] ) ? ' ' . esc_attr( $atts['class'] ) : '';
	
	ob_start();
	?>
	<div class="homepage-menu-wrapper<?php echo $custom_class; ?>">
		<?php do_action('websy_homepage_menu'); ?>
	</div>
	<?php
	return ob_get_clean();
}
add_shortcode( 'homepage_menu_only', 'websy_homepage_menu_only_shortcode' );

// Shortcode pour la section de signalement
function websy_signalement_section_shortcode( $atts ) {
	$atts = shortcode_atts( array(
		'title' => 'Avez-vous ressenti une secousse ?',
		'description' => 'En signalant une secousse, vous contribuez à collecter des informations précieuses qui aident à l\'analyse des événements sismiques.',
		'button_text' => 'Signaler maintenant',
		'class' => '',
	), $atts );
	
	$custom_class = !empty( $atts['class'] ) ? ' ' . esc_attr( $atts['class'] ) : '';
	
	ob_start();
	?>
	<section class="signalement-section<?php echo $custom_class; ?>">
		<div class="av-container">
			<div class="row">
				<div class="col-12">
					<div class="signalement-content">
						<div class="signalement-text">
							<h2><?php echo esc_html( $atts['title'] ); ?></h2>
							<p><?php echo esc_html( $atts['description'] ); ?></p>
						</div>
						<div class="signalement-button">
							<a href="#signalement-form" class="btn-signaler" data-toggle="modal" data-target="#signalementModal">
								<i class="fa fa-bell"></i>
								<?php echo esc_html( $atts['button_text'] ); ?>
							</a>
						</div>
					</div>
				</div>
			</div>
		</div>
	</section>
	
	<!-- Modal du formulaire de signalement -->
	<div class="modal fade" id="signalementModal" tabindex="-1" role="dialog" aria-labelledby="signalementModalLabel" aria-hidden="true">
		<div class="modal-dialog modal-lg" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title" id="signalementModalLabel">Signaler une Secousse</h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body">
					<?php echo do_shortcode('[signalement_form]'); ?>
				</div>
			</div>
		</div>
	</div>
	<?php
	return ob_get_clean();
}
add_shortcode( 'signalement_section', 'websy_signalement_section_shortcode' );

// Shortcode pour le formulaire de signalement
function websy_signalement_form_shortcode( $atts ) {
	$atts = shortcode_atts( array(
		'class' => '',
	), $atts );
	
	$custom_class = !empty( $atts['class'] ) ? ' ' . esc_attr( $atts['class'] ) : '';
	
	ob_start();
	?>
	<form class="signalement-form<?php echo $custom_class; ?>" method="post" action="">
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
			<button type="submit" class="btn btn-primary btn-signaler-submit">
				<i class="fa fa-paper-plane"></i>
				Envoyer le signalement
			</button>
		</div>
	</form>
	<?php
	return ob_get_clean();
}
add_shortcode( 'signalement_form', 'websy_signalement_form_shortcode' );

// Traitement du formulaire de signalement
function websy_process_signalement_form() {
	if ( ! isset( $_POST['signalement_nonce_field'] ) || ! wp_verify_nonce( $_POST['signalement_nonce_field'], 'signalement_nonce' ) ) {
		return;
	}
	
	if ( ! isset( $_POST['signalement_date'] ) || ! isset( $_POST['signalement_intensite'] ) || ! isset( $_POST['signalement_localisation'] ) ) {
		return;
	}
	
	// Récupération des données du formulaire
	$date = sanitize_text_field( $_POST['signalement_date'] );
	$intensite = sanitize_text_field( $_POST['signalement_intensite'] );
	$duree = isset( $_POST['signalement_duree'] ) ? sanitize_text_field( $_POST['signalement_duree'] ) : '';
	$type = isset( $_POST['signalement_type'] ) ? sanitize_text_field( $_POST['signalement_type'] ) : '';
	$localisation = sanitize_text_field( $_POST['signalement_localisation'] );
	$description = isset( $_POST['signalement_description'] ) ? sanitize_textarea_field( $_POST['signalement_description'] ) : '';
	$nom = isset( $_POST['signalement_nom'] ) ? sanitize_text_field( $_POST['signalement_nom'] ) : '';
	$email = isset( $_POST['signalement_email'] ) ? sanitize_email( $_POST['signalement_email'] ) : '';
	
	// Création du post de signalement
	$post_data = array(
		'post_title'    => 'Signalement - ' . $localisation . ' - ' . $date,
		'post_content'  => $description,
		'post_status'   => 'publish',
		'post_type'     => 'signalement',
		'post_author'   => 1,
	);
	
	$post_id = wp_insert_post( $post_data );
	
	if ( $post_id ) {
		// Ajout des meta données
		update_post_meta( $post_id, '_signalement_date', $date );
		update_post_meta( $post_id, '_signalement_intensite', $intensite );
		update_post_meta( $post_id, '_signalement_duree', $duree );
		update_post_meta( $post_id, '_signalement_type', $type );
		update_post_meta( $post_id, '_signalement_localisation', $localisation );
		update_post_meta( $post_id, '_signalement_nom', $nom );
		update_post_meta( $post_id, '_signalement_email', $email );
		
		// Redirection avec message de succès
		wp_redirect( add_query_arg( 'signalement', 'success', wp_get_referer() ) );
		exit;
	}
}
add_action( 'init', 'websy_process_signalement_form' );

// Enregistrement du type de post personnalisé pour les signalements
function websy_register_signalement_post_type() {
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
add_action( 'init', 'websy_register_signalement_post_type' );

/**
 * Dynamic Styles
 */
if( ! function_exists( 'websy_dynamic_style' ) ):
    function websy_dynamic_style() {

		$output_css = '';
		
			
		 /**
		 *  Breadcrumb Style
		 */
		$websy_hs_breadcrumb	= get_theme_mod('hs_breadcrumb','1');	
		
		if($websy_hs_breadcrumb == '') { 
				$output_css .=".webique-content {
					padding-top: 200px;
				}\n";
			}
		
		
		/**
		 *  Parallax
		 */
	
    }
endif;
add_action( 'wp_enqueue_scripts', 'websy_dynamic_style',999);

function websy_custom_header_setup() {
	add_theme_support( 'custom-header', apply_filters( 'webique_custom_header_args', array(
		'default-image'          => '',
		'default-text-color'     => '4DB8F1',
		'width'                  => 2000,
		'height'                 => 200,
		'flex-height'            => true,
		'wp-head-callback'       => 'webique_header_style',
	) ) );
}
add_action( 'after_setup_theme', 'websy_custom_header_setup' );


/**
 * Called all the Customize file.
 */
require( get_stylesheet_directory() . '/inc/customize/websy-premium.php');
require( get_stylesheet_directory() . '/inc/websy-customizer.php');
require( get_stylesheet_directory() . '/inc/extra.php');


/**
 * Import Options From Parent Theme
 *
 */
function websy_parent_theme_options() {
	$webique_mods = get_option( 'theme_mods_webique' );
	if ( ! empty( $webique_mods ) ) {
		foreach ( $webique_mods as $webique_mod_k => $webique_mod_v ) {
			set_theme_mod( $webique_mod_k, $webique_mod_v );
		}
	}
}
add_action( 'after_switch_theme', 'websy_parent_theme_options' );
