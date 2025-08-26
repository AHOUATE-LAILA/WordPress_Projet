<?php
/**
 * Template Name: Front Page Template
 *
 * Displays the Front Page Layout of the theme.
 *
 * @package Theme Horse
 * @subpackage NewsCard
 * @since NewsCard 1.1
 */
get_header(); ?>
<div id="content" class="site-content<?php echo esc_attr(should_output_pt0()); ?>">
	<div class="container">
		<div class="row justify-content-center site-content-row<?php echo (is_page_template('templates/front-page-template.php')) ? ' gutter-14' : ''; ?>">
			<div id="primary" class="content-area<?php echo esc_attr(newscard_layout_primary()); ?>">

				<?php if ( is_active_sidebar('newscard_front_page_content_section') ) : ?>

					<main id="main" class="site-main" role="main">
						<?php dynamic_sidebar( 'newscard_front_page_content_section' ); ?>
					</main><!-- #main .site-main -->

				<?php endif; ?>

			</div><!-- #primary -->

			<?php if ( is_active_sidebar('newscard_front_page_sidebar_section') ) :

				$newscard_settings = newscard_get_option_defaults();

				global $post;
				if ($post) {
					$newscard_meta_layout = get_post_meta($post->ID, 'newscard_sidebarlayout', true);
				}
				$newscard_custom_layout = $newscard_settings['newscard_content_layout'];

				if ( empty($newscard_meta_layout) ) {
					$newscard_meta_layout = 'default';
				}

				if ( ('default' == $newscard_meta_layout && 'right' == $newscard_custom_layout) || 'meta-right' == $newscard_meta_layout ) { ?>

					<aside id="secondary" class="col-lg-4 widget-area" role="complementary">
						<div class="sticky-sidebar">
							<?php dynamic_sidebar( 'newscard_front_page_sidebar_section' ); ?>
						</div><!-- .sticky-sidebar -->
					</aside><!-- #secondary -->

				<?php } elseif ( ('default' == $newscard_meta_layout && 'left' == $newscard_custom_layout) || 'meta-left' == $newscard_meta_layout ) { ?>

					<aside id="secondary" class="col-lg-4 widget-area order-lg-1" role="complementary">
						<div class="sticky-sidebar">
							<?php dynamic_sidebar( 'newscard_front_page_sidebar_section' ); ?>
						</div><!-- .sticky-sidebar -->
					</aside><!-- #secondary -->

				<?php }

			endif; ?>
		</div><!-- row -->
	</div><!-- .container -->
</div><!-- #content .site-content-->
<?php get_footer();







