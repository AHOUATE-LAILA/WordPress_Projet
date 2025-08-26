<?php
/**
 * Template Name: Full Width Template
 *
 * Full-Width Template for the page builder
 *
 * @package Theme Horse
 * @subpackage NewsCard
 * @since NewsCard 1.1
 */
get_header(); ?>

<main id="main" class="">
	<?php
	if ( have_posts() ) :
		/* Start the Loop */
		while ( have_posts() ) :
			the_post();
			the_content();
		endwhile; 
		/* End of the loop. */
	endif;
	?>
</main><!-- #main -->

<?php get_footer();