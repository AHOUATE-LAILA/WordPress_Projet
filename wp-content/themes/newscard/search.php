<?php
/**
 * The template for displaying search results pages
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#search-result
 *
 * @package NewsCard
 */

get_header(); ?>
<div id="content" class="site-content">
	<div class="container">
		<div class="row justify-content-center site-content-row">
			<div id="primary" class="content-area<?php echo esc_attr(newscard_layout_primary()); ?>">
				<main id="main" class="site-main">

				<?php if ( have_posts() ) : ?>

					<header class="page-header">
						<h1 class="page-title">
							<?php
							/* translators: %s: search query. */
							printf( esc_html__( 'Search Results for: %s', 'newscard' ), '<span>' . get_search_query() . '</span>' );
							?>
						</h1>
					</header><!-- .page-header -->

					<div class="row gutter-parent-14 post-wrap">
						<?php
						/* Start the Loop */
						while ( have_posts() ) :
							the_post();

							/**
							 * Run the loop for the search to output the results.
							 * If you want to overload this in a child theme then include a file
							 * called content-search.php and that will be used instead.
							 */
							get_template_part( 'template-parts/content', 'search' );

						endwhile; ?>
					</div><!-- .row .gutter-parent-14 .post-wrap-->

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