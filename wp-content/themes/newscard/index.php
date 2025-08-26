<?php
/**
 * The main template file
 *
 * This is the most generic template file in a WordPress theme
 * and one of the two required files for a theme (the other being style.css).
 * It is used to display a page when nothing more specific matches a query.
 * E.g., it puts together the home page when no home.php file exists.
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package NewsCard
 */

get_header(); ?>
<div id="content" class="site-content<?php echo esc_attr(should_output_pt0()); ?>">
	<div class="container">
		<div class="row justify-content-center site-content-row<?php echo (is_page_template('templates/front-page-template.php')) ? ' gutter-14' : ''; ?>">
			<div id="primary" class="content-area<?php echo esc_attr(newscard_layout_primary()); ?>">
				<main id="main" class="site-main">

				<?php if ( is_home() && !is_front_page() ) {

					if ( ($newscard_settings['newscard_banner_display'] === 'front-blog' && ($newscard_settings['newscard_banner_slider_posts_hide'] === 0 || $newscard_settings['newscard_banner_featured_posts_1_hide'] === 0 || $newscard_settings['newscard_banner_featured_posts_2_hide'] === 0)) || $newscard_settings['newscard_header_featured_posts_hide'] === 0 ) { ?>

						<h2 class="stories-title"><?php echo get_the_title(get_option('page_for_posts')); ?> </h2>

					<?php } else { ?>

						<header class="page-header">
							<h2 class="page-title"><?php echo get_the_title(get_option('page_for_posts')); ?> </h2>
						</header><!-- .page-header -->

					<?php }

				}

				if ( have_posts() ) : ?>
					<div class="row gutter-parent-14 post-wrap">
						<?php /* Start the Loop */
						while ( have_posts() ) :
							the_post();

							/*
							* Include the Post-Type-specific template for the content.
							* If you want to override this in a child theme, then include a file
							* called content-___.php (where ___ is the Post Type name) and that will be used instead.
							*/
							get_template_part( 'template-parts/content', get_post_format() );

						endwhile; ?>
					</div><!-- .row .gutter-parent-14 .post-wrap -->

					<?php the_posts_pagination( array(
						'prev_text' => __( 'Previous', 'newscard' ),
						'next_text' => __( 'Next', 'newscard' ),
						)
					);

				else :

					get_template_part( 'template-parts/content', 'none' );

				endif;
				?>

				</main><!-- #main -->
			</div><!-- #primary -->
			<?php do_action('newscard_sidebar'); ?>
		</div><!-- row -->
	</div><!-- .container -->
</div><!-- #content .site-content-->
<?php get_footer();