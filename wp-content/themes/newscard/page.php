<?php
/**
 * The template for displaying all pages
 *
 * This is the template that displays all pages by default.
 * Please note that this is the WordPress construct of pages
 * and that other 'pages' on your WordPress site may use a
 * different template.
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

					<?php
					while ( have_posts() ) :
						the_post();

						get_template_part( 'template-parts/content', 'page' );

						// If comments are open or we have at least one comment, load up the comment template.
						if ( comments_open() || get_comments_number() ) :
							comments_template();
						endif;

					endwhile; // End of the loop.
					?>

				</main><!-- #main -->
			</div><!-- #primary -->
			<?php do_action('newscard_sidebar'); ?>
		</div><!-- row -->
	</div><!-- .container -->
</div><!-- #content .site-content-->
<?php get_footer();