<?php

use ColibriWP\Theme\Translations;
use Kubio\Theme\Theme;

wp_localize_script(
	get_template() . '-page-info',
	'pixelize_admin',
	array(
		'getStartedData'    => array(
			'plugin_installed_and_active' => Translations::escHtml( 'plugin_installed_and_active' ),
			'activate'                    => Translations::escHtml( 'activate' ),
			'activating'                  => Translations::get( 'activating', 'Kubio Page Builder' ),
			'install_recommended'         => isset( $_GET['install_recommended'] ) ? $_GET['install_recommended'] : '',
			'theme_prefix'                => Theme::prefix( '', false ),
		),
		'builderStatusData' => array(
			'status'                          => pixelize_theme()->getPluginsManager()->getPluginState( pixelize_get_builder_plugin_slug() ),
			'install_url'                     => pixelize_theme()->getPluginsManager()->getInstallLink( pixelize_get_builder_plugin_slug() ),
			'activate_url'                    => pixelize_theme()->getPluginsManager()->getActivationLink( pixelize_get_builder_plugin_slug() ),
			'slug'                            => pixelize_get_builder_plugin_slug(),
			'kubio_front_set_predesign_nonce' => wp_create_nonce( 'kubio_front_set_predesign_nonce' ),
			'kubio_disable_big_notice_nonce'  => wp_create_nonce( 'kubio_disable_big_notice_nonce' ),
			'plugin_activate_nonce'           => wp_create_nonce( 'plugin_activate_nonce' ),
			'messages'                        => array(
				'installing' => Translations::get( 'installing', 'Kubio Page Builder' ),
				'activating' => Translations::get( 'activating', 'Kubio Page Builder' ),
				'preparing'  => Translations::get( 'preparing_front_page_installation' ),
			),
		),
	)
);

?>

<div class="pixelize-notice-dont-show-container">
	<button class="button-link pixelize-dont-show-notice">
		<?php Translations::escHtmlE( 'dont_show_anymore' ); ?>
	</button>
</div>

<div class="pixelize-admin-notice-spacer">
	<div class="pixelize-admin-big-notice--container">
		<div class="content-holder">
			<div class="messages-area">
				<div class="title-holder">
					<h1><?php Translations::escHtmlE( 'would_you_like_to_install_front_page', pixelize_theme()->getName() ); ?></h1>
					<p><?php Translations::escHtmlE( 'theme_description', pixelize_theme()->getName() ); ?></p>
				</div>

				<ul>
					<li>
						<img src="<?php echo pixelize_theme()->getListMarkerUrl(); ?>" alt="">
						<?php Translations::escHtmlE( 'benefit_1' ); ?>
					</li>
					<li>
						<img src="<?php echo pixelize_theme()->getListMarkerUrl(); ?>" alt="">
						<?php Translations::escHtmlE( 'benefit_2' ); ?>
					</li>
					<li>
						<img src="<?php echo pixelize_theme()->getListMarkerUrl(); ?>" alt="">
						<?php Translations::escHtmlE( 'benefit_3' ); ?>
					</li>
					<li>
						<img src="<?php echo pixelize_theme()->getListMarkerUrl(); ?>" alt="">
						<?php Translations::escHtmlE( 'benefit_4' ); ?>
					</li>
				</ul>

				<div class="action-buttons">
					<button class="button button-primary button-hero start-with-ai-page">
						<?php Translations::escHtmlE( 'install_homepage_with_ai' ); ?>
					</button>
					<button class="button-link start-with-predefined-design-button">
						<?php Translations::escHtmlE( 'install_default_homepage' ); ?>
					</button>
				</div>
				<div class="content-footer ">
					<div>
						<div class="plugin-notice">
							<span class="spinner"></span>
							<span class="message"></span>
						</div>
					</div>
					<div>
						<p class="description large-text">
							<?php Translations::escHtmlE( 'start_with_a_front_page_plugin_info', 'Kubio Page Builder' ); ?>
						</p>
					</div>
				</div>
			</div>

			<div class="front-page-preview">
				<img src="<?php echo esc_url( pixelize_theme()->getFrontPagePreview() ); ?>"/>
			</div>
		</div>
	</div>
</div>






